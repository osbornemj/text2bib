<?php

namespace App\Livewire;

use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
use stdClass;

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
    public $fileError = null;
    public $notUtf8;
    public $convertedEncodingCount;
    public $useOptions;

    public function boot()
    {
        $this->converter = new Converter();
    }

    public function mount()
    {
        $userSettings = UserSetting::where('user_id', Auth::id())->first();

        $defaults = [
            'use' => '',
            'other_use' => '',
            'item_separator' => 'line',
            'language' => 'en',
            'label_style' => 'short',
            'override_labels' => '0',
            'line_endings' => 'w',
            'char_encoding' => 'utf8leave',
            'percent_comment' => '1',
            'include_source' => '1',
            'report_type' => 'standard',
            'save_settings' => '1',
        ];

        foreach ($defaults as $setting => $default) {
            $this->uploadForm->$setting = $userSettings ? $userSettings->$setting : $default;
        }

        $this->useOptions = [
            'latex' => 'In a LaTeX document, using a traditional BibTeX style file (your document specifies a \bibliographystyle)',
            'biblatex' => 'In a LaTeX document, using biblatex (your document says \usepackage{biblatex} in the preamble)',
            'zotero-word' => 'To import references into Zotero, to use in Microsoft Word or Libre Office',
            'mendeley' => 'To import references into Mendeley',
            'refworks' => 'To import references into RefWorks',
            'endnote' => 'To import references into EndNote',
            'other' => 'Other (enter in text box)',
        ];

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

    public function submit(bool $redo = false)//: void
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

        // Next block added after conversion 10715 was submitted with user id null.
        // However, 10841 was submitted with user id null after this check was added.
        if (! Auth::check()) {
            die;
        }

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
        if ($conversion->language != 'en' && $conversion->char_encoding == 'utf8') {
            $conversion->char_encoding = 'utf8leave';
        }
        $conversion->version = $this->version;
        $conversion->save();

        $this->conversionId = $conversion->id;

        // Get content of the file that the user uploaded
        $filestring = Storage::disk('public')->get('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        $sourceFile->update(['sha1_hash' => sha1($filestring)]);

        $previousUserFile = UserFile::where('user_id', Auth::id())
                ->where('sha1_hash', $sourceFile->sha1_hash)
                ->where('created_at', '<', $sourceFile->created_at)
                ->latest()
                ->first();

        if ($previousUserFile && ! Auth::user()->is_admin) {
            $previousConversion = Conversion::where('user_file_id', $previousUserFile->id)->first();
            if (
                $previousConversion &&
                $previousConversion->use == $conversion->use &&
                $previousConversion->other_use == $conversion->other_use &&
                $previousConversion->item_separator == $conversion->item_separator &&
                $previousConversion->language == $conversion->language &&
                $previousConversion->label_style == $conversion->label_style &&
                $previousConversion->override_labels == $conversion->override_labels &&
                $previousConversion->line_endings == $conversion->line_endings &&
                $previousConversion->char_encoding == $conversion->char_encoding &&
                $previousConversion->percent_comment == $conversion->percent_comment &&
                $previousConversion->include_source == $conversion->include_source &&
                $previousConversion->report_type == $conversion->report_type &&
                $previousConversion->version == $conversion->version->toDateTimeString()
            ) {
                $conversion->delete();
                return redirect('showConversion/' . $previousConversion->id . '/1');
            }
        }

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);
        // If line consists only of tab and/or space followed by a linefeed, remove the tab and space.
        $filestring = preg_replace('/\n\t? ?\n/', "\n\n", $filestring);
        $filestring = str_replace('\end{bibliography}', '', $filestring);

        // Remove this string from file --- BOM (byte order mark) if at start of file, otherwise zero width no-break space
        $filestring = str_replace("\xEF\xBB\xBF", " ", $filestring);

        if (Str::contains($filestring, ['@article', '@book', '@incollection', '@inproceedings', '@unpublished', '@online', '@techreport', '@phdthesis', '@mastersthesis', '@misc'])) {
            $this->fileError = 'bibtex';
        } elseif (substr_count($filestring, '\bibinfo{') > 2 || substr_count($filestring, '\bibinfo {') > 2) {
            $this->fileError = 'bbl-natbib';
        } elseif (substr_count($filestring, "\nAU ") > 3 && substr_count($filestring, "\nTI ") > 3 && substr_count($filestring, "\nSO ") > 3) {
            // for an example, see 2740-7657-source.txt
            $this->fileError = 'bibliographic-export';
        }

        if ($this->fileError) {
            $conversion->update(['file_error' => $this->fileError]);
        } else {
            $entrySeparator = Str::startsWith($filestring, '<li>') ? '<li>' : ($conversion->item_separator == 'line' ? "\n\n" : "\n");

            // Create array of entries
            $entries = explode($entrySeparator, $filestring);

            // Remove empty entries and entries that are "\n"; last condition eliminates stray lines with short text
            // (has to exclude ones with strlen == 1 to clean up BOM)
            $entries = array_filter($entries, fn($value) => ! empty($value) && $value != "\n" && strlen($value) > 10);

            $this->itemSeparatorError = false;
            $this->unknownEncodingEntries = [];

            // Check for utf-8
            $encodings = [];
            $this->notUtf8 = false;
            $this->convertedEncodingCount = 0;
            foreach ($entries as $i => $entry) {
                if ($conversion->language == 'my') {
                    $encodings[$i] = 'UTF-8';
                } else {
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
            }

            if ($this->notUtf8) {
                $conversion->update(['non_utf8_detected' => true]);
            }

            // If encoding is correct, check for possible item_separator error
            if (count($this->unknownEncodingEntries) == 0 && count($entries) <= 2 && count($entries) >= 1 && strlen($entries[array_key_first($entries)]) > 500) {
                $this->entry = $entries[array_key_first($entries)];
                $this->itemSeparatorError = true;
                $conversion->delete();
            }
        }

        // If file is not already a BibTeX file and item_separator and encoding seem correct, perform the conversion
        if (! $this->fileError && $this->itemSeparatorError == false && count($this->unknownEncodingEntries) == 0) {
            $convertedEntries = [];
            $previousAuthor = null;
            foreach ($entries as $j => $entry) {
                // Some files start with \u{FEFF}, but this character is now converted to space earlier in this method
                if ($entry) {
                    // $convertedEntries is array with components 
                    // 'source', 'item', 'itemType', 'label', 'warnings', 'notices', 'details', 'scholarTitle'.
                    // 'label' (which depends on whole set of converted items) is updated later
                    $convertedEntry = $this->converter->convertEntry($entry, $conversion, null, null, null, $previousAuthor);
                    $previousAuthor = $convertedEntry['item']->author ?? null;
                    $convertedEntry['detected_encoding'] = $encodings[$j];
                    if ($convertedEntry) {
                        $convertedEntries[$j] = $convertedEntry;
                    }
                }
            }

            // Add labels to entries
            $convertedEntries = $this->addLabels($convertedEntries, $conversion);

            $itemTypes = ItemType::all();

            // Write each converted item to an Output **and key array to output ids**.
            // Note that source is written to conversions table, so original file
            // is not needed except to check how entries were created from it.
            $convertedItems = [];
            foreach ($convertedEntries as $i => $convItem) {
                if (isset($convItem['source'])) {
                    /*
                    $doi = $convItem['item']->doi ?? null;
                    $doi = str_replace('\_', '_', $doi);

                    $crossref_item = null;
                    if ($doi) {
                        $crossref_item = $this->getCrossrefItemFromDoi($doi, $conversion->use);
                        $convItem['crossref_item'] = $crossref_item;
                    }
                    */

                    $output = Output::create([
                        'source' => $convItem['source'],
                        'detected_encoding' => $encodings[$i],
                        'conversion_id' => $conversion->id,
                        'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                        'label' => $convItem['label'],
                        'item' => $convItem['item'],
                    //    'crossref_item' => $crossref_item,
                        'author_pattern' => $convItem['author_pattern'],
                        'seq' => $i,
                    ]);
                    $convertedItems[$output->id] = $convItem;
                }
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

    public function getCrossrefItemFromDoi(string $doi, string $use): object
    {
        $response = Http::withHeaders([
                'User-Agent' => 'text2bib (https://text2bib.org); mailto:' . env('CROSSREF_EMAIL'),
            ])
            ->acceptJson()
            ->get('https://api.crossref.org/works/' . $doi);
            //->accept('application/x-bibtex')
            //->get('https://api.crossref.org/works/' . $doi . '/transform');

        $body = json_decode($response->body());

        if ($body) {
            $details = $body->message;
            $crossref_item = new \stdClass();
            $crossref_item->doi = $use == 'latex' ? str_replace('_', '\_', $details->DOI) : $details->DOI;
            $crossref_item->itemType = match ($details->type) {
                'journal-article' => 'article',
                'book-chapter' => 'incollection',
                'book' => 'book',
            };
            $crossref_item->title = $details->title[0];
            $crossref_item->author = '';
            foreach ($details->author as $j => $author) {
                $crossref_item->author .= ($j ? ' and ' : '') . $author->family . ', ' . $author->given;
            }
            $crossref_item->year = $details->{'published-print'}->{'date-parts'}[0][0];

            switch ($crossref_item->itemType) {
                case 'article':
                    $crossref_item->journal = $details->{'container-title'}[0];
                    $crossref_item->pages = $details->page;
                    $crossref_item->number = $details->{'journal-issue'}->issue;
                    $crossref_item->volume = $details->volume;
                    break;
                case 'incollection':
                    $crossref_item->booktitle = $details->{'container-title'}[1];
                    $crossref_item->address = $details->{'publisher-location'};
                    $crossref_item->publisher = $details->publisher;
                    $crossref_item->pages = $details->page;
                    $crossref_item->isbn = '';
                    foreach ($details->{'isbn-type'} as $j => $isbntype) {
                        $crossref_item->isbn .= ($j ? ', ' : '') . $isbntype->value . ' (' . $isbntype->type .')';
                    }
                    break;
            }
        }

        return $crossref_item ?? null;
    }
}
