<?php

namespace App\Livewire;

use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;
use App\Models\UserSetting;

use App\Traits\AddLabels;

use Livewire\Component;

use App\Livewire\Forms\ConvertFileForm;
use App\Models\Version;
use App\Services\Converter;

class ConvertFile extends Component
{
    use WithFileUploads;

    use AddLabels;

    public ConvertFileForm $uploadForm;

    private Converter $converter;

    public $conversionExists = false;
    public $conversionCount;
    public $version;

    public $convertedItems;
    public $conversionId;
    public $outputId;
    public $includeSource;
    public $reportType;
    public $conversion;

    public $itemTypeOptions;
    public $itemTypes;

    public $entry = null;
    public $itemSeparatorError = false;
    public $unknownEncodingEntries = [];
    public $isBibtex;
    public $notUtf8;
    public $convertedEncodingCount;

    public function boot()
    {
        $this->converter = new Converter();
    }

    public function mount()
    {
        $userSettings = UserSetting::where('user_id', Auth::id())->first();

        $defaults = [
            'item_separator' => 'line',
            'language' => 'en',
            'label_style' => 'short',
            'override_labels' => '1',
            'line_endings' => 'w',
            'char_encoding' => 'utf8',
            'percent_comment' => '1',
            'include_source' => '1',
            'report_type' => 'standard',
            'save_settings' => '1',
        ];

        foreach ($defaults as $setting => $default) {
            $this->uploadForm->$setting = $userSettings ? $userSettings->$setting : $default;
        }

        $user = Auth::user();
        $this->conversionCount = $user->conversions->count();
        $this->version = Version::latest()->first()->created_at;
    }

    /*
    protected function queryString()
    {
        return [
            'conversionId' => [
                'as' => 'id'
            ]
        ];
    }
    */

    public function submit(bool $redo = false): void
    {
        // When a user clicks the link to resubmit a file on the page item-separator-error.blade.php,
        // *first* the form on the page file-upload-form.blade.php is submitted, *then* the form on the 
        // page item-separator-error.blade.php, with $redo = true, is submitted.  (Why does that happen?)
        // Thus in the case that the item-separator-error page is reached, the existing Conversion
        // (and with it the associated file) is deleted.  When the button on the item-separator-error
        // form is pressed, the file is again uploaded and deleted, then the form on the item-separator-error
        // page executes submit(true), which uploads the file once again, creates a new Conversion and
        // sets line_endings to 'cr', and does the conversion.
        // There must be a better way to handle this case --- perhaps by showing and hiding the divs on
        // convert-file.blade.php rather than using @includes?

        $this->uploadForm->validate();

        $file = $this->uploadForm->file;

        // Write file to user_files table
        $sourceFile = new UserFile;
        $sourceFile->user_id = Auth::id();
        $sourceFile->file_type = $file->getClientMimeType();
        $sourceFile->file_size = $file->getSize();
        $sourceFile->original_filename = $file->getClientOriginalName();
        $sourceFile->type = 'SRC';
        $sourceFile->save();

        // Store file
        $file->storeAs(
            'files',
            Auth::id() . '-' . $sourceFile->id . '-source.txt',
            'public',
        );

        // Get settings and save them if requested
        $settingValues = $this->uploadForm->except('file');

        if ($this->uploadForm->save_settings) {
            $userSetting = UserSetting::firstOrNew( 
                ['user_id' => Auth::id()]
            );
            $userSetting->fill($settingValues);
            $userSetting->save();
        }

        $settingValues['user_file_id'] = $sourceFile->id;
        unset($settingValues['save_settings']);

        // Create Conversion
        $conversion = new Conversion;
        $conversion->fill($settingValues);
        $conversion->user_id = Auth::id();
        if ($redo) {
            $conversion->item_separator = 'cr';
        }
        if ($conversion->language != 'en') {
            $conversion->char_encoding = 'utf8leave';
        }
        $conversion->version = $this->version;
        $conversion->save();

        $this->conversionId = $conversion->id;

        // Get content of the file that the user uploaded
        $filestring = Storage::disk('public')->get('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);
        // If line consists only of tab and/or space followed by a linefeed, remove the tab and space.
        $filestring = preg_replace('/\n\t? ?\n/', "\n\n", $filestring);
        $filestring = str_replace('\end{bibliography}', '', $filestring);

        $this->isBibtex = Str::contains($filestring, ['@article', '@book', '@incollection', '@inproceedings', '@unpublished', '@online', '@techreport', '@phdthesis', '@mastersthesis']);

        if ($this->isBibtex) {
            $conversion->update(['is_bibtex' => true]);
        } else {
            $entrySeparator = Str::startsWith($filestring, '<li>') ? '<li>' : ($conversion->item_separator == 'line' ? "\n\n" : "\n");

            // Create array of entries
            $entries = explode($entrySeparator, $filestring);
            // Remove empty entries and entries that are "\n"
            $entries = array_filter($entries, fn($value) => ! empty($value) && $value != "\n");

            $this->itemSeparatorError = false;
            $this->unknownEncodingEntries = [];

            // Check for utf-8
            $encodings = [];
            $this->notUtf8 = false;
            $this->convertedEncodingCount = 0;
            foreach ($entries as $i => $entry) {
                $encodings[$i] = mb_detect_encoding($entry, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                if (in_array($encodings[$i], ['ISO-8859-1', 'Windows-1252'])) {
                    $entries[$i] = mb_convert_encoding($entry, 'UTF-8', $encodings[$i]);
                    $this->convertedEncodingCount++;
                    $this->notUtf8 = true;
                } elseif ($encodings[$i] != 'UTF-8') {
                    // Need to convert to UTF-8 because Livewire uses json encoding
                    // (and will crash if non-utf-8 string is passed to it)
                    $this->unknownEncodingEntries[] = mb_convert_encoding($entry, "UTF-8");
                }
            }

            if ($this->notUtf8) {
                $conversion->update(['non_utf8_detected' => true]);
            }

            // If encoding is correct, check for possible item_separator error
            if (count($this->unknownEncodingEntries) == 0 && count($entries) <= 2 && strlen($entries[array_key_first($entries)]) > 500) {
                $this->entry = $entries[array_key_first($entries)];
                $this->itemSeparatorError = true;
                $conversion->delete();
            }
        }
       
        // If file is not already a BibTeX file and item_separator and encoding seem correct, perform the conversion
        if (! $this->isBibtex && $this->itemSeparatorError == false && count($this->unknownEncodingEntries) == 0) {
            $convertedEntries = [];
            $i = 0;
            foreach ($entries as $entry) {
                $i++;
                // Some files start with \u{FEFF}, but this character is now converted to space by cleanText
                if ($entry) {
                    // $convertedEntries is array with components 
                    // 'source', 'item', 'itemType', 'label', 'warnings', 'notices', 'details', 'scholarTitle'.
                    // 'label' (which depends on whole set of converted items) is updated later
                    $convertedEntry = $this->converter->convertEntry($entry, $conversion);
                    $convertedEntry['detected_encoding'] = $encodings[$i-1];
                    if ($convertedEntry) {
                        $convertedEntries[] = $convertedEntry;
                    }
                }
            }

            // Add labels to entries
            $convertedEntries = $this->addLabels($convertedEntries, $conversion);

            $itemTypes = ItemType::all();

            // Write each converted item to an Output **and key array to output ids**
            // Note that source is written to conversions table, so original file
            // is not needed except to check how entries were created from it.
            $convertedItems = [];
            foreach ($convertedEntries as $i => $convItem) {
                $output = Output::create([
                    'source' => $convItem['source'],
                    'detected_encoding' => $encodings[$i],
                    'conversion_id' => $conversion->id,
                    'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                    'label' => $convItem['label'],
                    'item' => $convItem['item'],
                    'seq' => $i,
                ]);
                $convertedItems[$output->id] = $convItem;
            }

            $this->conversionExists = true;
            $this->conversion = $conversion;
            
            $this->convertedItems = $convertedItems;
            $this->includeSource = $conversion->include_source;
            $this->reportType = $conversion->report_type;
            $this->itemTypes = $itemTypes;
            $this->itemTypeOptions = $itemTypes->pluck('name', 'id')->all();
        }
    }
}
