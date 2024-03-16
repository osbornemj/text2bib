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

use App\Services\Converter;

class ConvertFile extends Component
{
    use WithFileUploads;

    use AddLabels;

    public ConvertFileForm $uploadForm;

    private Converter $converter;

    public $conversionExists = false;

    public $convertedItems;
    public $conversionId;
    public $outputId;
    public $includeSource;
    public $reportType;

    public $itemTypeOptions;
    public $itemTypes;

    public $entry = null;
    public $itemSeparatorError = false;
    public $nonUtf8Entries = [];

    public function boot()
    {
        $this->converter = new Converter();
    }

    public function mount()
    {
        $userSettings = UserSetting::where('user_id', Auth::id())->first();

        $defaults = [
            'item_separator' => 'line',
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

    public function submit(int $reConversionId = null): void
    {
        // $conversionId is set if user is re-doing a conversion
        // that had 'line' as item_separator but should have 'cr'.
        if ($reConversionId) {
            $conversion = Conversion::find($reConversionId);
            $conversion->update(['item_separator' => 'cr']);
        } else {
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
            $conversion->save();
        }

        $this->conversionId = $conversion->id;

        // Get content of the file that the user uploaded
        $filestring = Storage::disk('public')->get('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);
        // If line consists only of tab and/or space followed by a linefeed, remove the tab and space.
        $filestring = preg_replace('/\n\t? ?\n/', "\n\n", $filestring);

        $entrySeparator = Str::startsWith($filestring, '<li>') ? '<li>' : ($conversion->item_separator == 'line' ? "\n\n" : "\n");

        // Create array of entries
        $entries = explode($entrySeparator, $filestring);

        $this->itemSeparatorError = false;
        $this->nonUtf8Entries = [];

        // Check for utf-8
        foreach ($entries as $entry) {
            if (!mb_check_encoding($entry)) {
                // Need to convert to UTF-8 because Livewire uses json encoding
                // (and will crash if non-utf-8 string is passed to it)
                $this->nonUtf8Entries[] = mb_convert_encoding($entry, "UTF-8");
            }
        }

        // If encoding is correct, check for possible item_separator error
        if (count($this->nonUtf8Entries) == 0 && count($entries) <= 2 && strlen($entries[0]) > 500) {
            $this->entry = $entries[0];
            $this->itemSeparatorError = true;
        }
       
        // If item_separator and encoding seem correct, perform the conversion
        if ($this->itemSeparatorError == false && count($this->nonUtf8Entries) == 0) {
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
                    'conversion_id' => $conversion->id,
                    'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                    'label' => $convItem['label'],
                    'item' => $convItem['item'],
                    'seq' => $i,
                ]);
                $convertedItems[$output->id] = $convItem;
            }

            $this->conversionExists = true;

            $this->convertedItems = $convertedItems;
            $this->includeSource = $conversion->include_source;
            $this->reportType = $conversion->report_type;
            $this->itemTypes = $itemTypes;
            $this->itemTypeOptions = $itemTypes->pluck('name', 'id')->all();
        }
    }
}
