<?php

namespace App\Livewire;

use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

use App\Models\Bst;
use App\Models\Conversion;
use App\Models\CrossrefBibtex;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;
use App\Models\UserSetting;
use App\Models\Version;

use App\Traits\AddLabels;

use Livewire\Component;

use App\Livewire\Forms\ConvertFileForm;

use App\Services\Converter;
use App\Services\Crossref;

class ConvertFile extends Component
{
    use WithFileUploads;

    use AddLabels;

    public ConvertFileForm $uploadForm;

    private Converter $converter;
    private Crossref $crossref;

    public $conversionExists = false;
    public $conversionCount;
    public $version;
    public $crossrefQuota;
    public $crossrefQuotaRemaining;
    public $crossrefQueryCount;
    public $retrievedFromCrossrefCount;
    public $retrievedFromCacheCount;

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
    public $bstName;
    public $bstFields;
    public $languages;

    public function boot()
    {
        $this->converter = new Converter();
        $this->crossref = new Crossref();
    }

    public function mount()
    {
        $userSettings = UserSetting::where('user_id', Auth::id())->first();

        if ($userSettings && $userSettings->bst_id) {
            $bst = Bst::find($userSettings->bst_id);
            $bstId = $bst->id;
            $bstName = $bst ? $bst->name : '';
        } else {
            $bstId = null;
            $bstName = '';
        }

        $this->bstFields = ['doi', 'eid', 'isbn', 'issn', 'translator', 'url', 'urldate'];

        $this->languages = config('constants.languages');
 
        $defaults = [
            'use' => '',
            'other_use' => '',
            'bst_id' => $bstId,
            'item_separator' => 'line',
            'language' => 'en',
            'label_style' => 'short',
            'override_labels' => '0',
            'line_endings' => 'w',
            'char_encoding' => 'utf8leave',
            'percent_comment' => '1',
            'include_source' => '1',
            'use_crossref' => '0',
            'report_type' => 'standard',
            'save_settings' => '1',
        ];

        foreach ($defaults as $setting => $default) {
            $this->uploadForm->$setting = $userSettings ? $userSettings->$setting : $default;
        }
        $this->uploadForm->bstName = $bstName;

        $this->crossrefQuota = config('constants.crossref_quota');

        $this->useOptions = [
            'latex' => 'In a LaTeX document, using a traditional BibTeX style file (your document specifies a \bibliographystyle)',
            'biblatex' => 'In a LaTeX document, using biblatex (your document says \usepackage{biblatex} in the preamble)',
            'zotero-word' => 'To import references into Zotero',
            'mendeley' => 'To import references into Mendeley',
            'refworks' => 'To import references into RefWorks',
            'endnote' => 'To import references into EndNote',
            'other' => 'Other (enter in text box)',
        ];

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $this->conversionCount = $user->conversions->count();
        $this->version = Version::latest()->first()->created_at;

        if ($user->crossref_date == null || ! $user->crossref_date->isToday()) {
            $user->crossref_date = now();
            $user->crossref_number = 0;
            $user->save();
        }

        $this->crossrefQuotaRemaining = max(0, $this->crossrefQuota - $user->crossref_number);
        $this->crossrefQueryCount = 0;
        $this->retrievedFromCrossrefCount = 0;
        $this->retrievedFromCacheCount = 0;
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

        $this->uploadForm->validate();

        $file = $this->uploadForm->file;

        /** @var \App\Models\User $user **/
        $user = Auth::user();

        // Write file to user_files table
        $sourceFile = new UserFile;
        $sourceFile->user_id = $user->id;
        $sourceFile->file_type = $file->getClientMimeType();
        $sourceFile->file_size = $file->getSize();
        $sourceFile->original_filename = $file->getClientOriginalName();
        $sourceFile->type = 'SRC';
        $sourceFile->save();

        // Store file
        $file->storeAs(
            'files',
            $user->id . '-' . $sourceFile->id . '-source.txt',
            'public',
        );

        // Get settings and save them if requested
        $settingValues = $this->uploadForm->except('file');

        if ($settingValues['bstName']) {
            $bst = Bst::firstOrCreate([
                'name' => $settingValues['bstName']
            ]);

            $settingValues['bst_id'] = $bst->id;
        }

        unset($settingValues['bstName']);

        if ($settingValues['use'] != 'other') {
            $settingValues['other_use'] = '';
        }
        if ($settingValues['use'] != 'latex') {
            $settingValues['bst_id'] = null;
        }

        if ($this->uploadForm->save_settings) {
            $userSetting = UserSetting::firstOrNew( 
                ['user_id' => $user->id]
            );
            $userSetting->fill($settingValues);
            $userSetting->save();
        }

        $settingValues['user_file_id'] = $sourceFile->id;
        unset($settingValues['save_settings']);

        // Create Conversion
        $conversion = new Conversion;
        $conversion->fill($settingValues);
        $conversion->user_id = $user->id;
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
        $filestring = Storage::disk('public')->get('files/' . $user->id . '-' . $conversion->user_file_id . '-source.txt');

        $sourceFile->update(['sha1_hash' => sha1($filestring)]);

        $previousUserFile = UserFile::where('user_id', $user->id)
                ->where('sha1_hash', $sourceFile->sha1_hash)
                ->where('created_at', '<', $sourceFile->created_at)
                ->latest()
                ->first();

        if ($previousUserFile && ! $user->is_admin) {
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


            if (count($entries) > 5) {
                $conversion->update(['report_type' => 'standard']);
            }

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
                    $encodings[$i] = $this->mb_detect_encoding_in_order($entry, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
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
        if ($this->fileError || $this->itemSeparatorError || count($this->unknownEncodingEntries) > 0) {
            return;
        }

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

                $doi = $convItem['item']->doi ?? null;

                if ($conversion->use_crossref && $doi) {
                    $doi = str_replace('\_', '_', $doi);

                    // Look for item in table crossref_bibtexs. If not there, go to Crossref.
                    $crossrefBibtex = CrossrefBibtex::where('doi', $doi)->first();
                    if ($crossrefBibtex) {
                        $this->retrievedFromCacheCount++;
                        $crossrefSource = 'database';
                        $convItem['crossref_item_type'] = $crossrefBibtex->item_type;
                        $convItem['crossref_item_label'] = null;
                        $convItem['crossref_item'] = $crossrefBibtex->item;
                        $convItem['notices'][] = "Item retrieved from Crossref cache";
                    } elseif ($this->crossrefQuotaRemaining > 0 && isset($convItem['item']->title)) {
                        $crossrefSource = 'crossref';
                        $encodedDoi = urlencode($doi);
                        $crossrefResult = $this->crossref->getCrossrefItemFromDoi($encodedDoi);
                        $user->crossref_number++;
                        $this->crossrefQuotaRemaining--;
                        $this->crossrefQueryCount++;

                        if ($crossrefResult !== null) {
                            $crossref_item = trim($crossrefResult);
                            //$crossref_item = trim($this->crossref->getCrossrefItemFromAuthorTitleYear($convItem['item']->author ?? '', $convItem['item']->title ?? '', $convItem['item']->year ?? ''));
                            //dd($crossref_item);
                            $result = $this->crossref->parseCrossrefBibtex($crossref_item);
                            if ($result) {
                                CrossrefBibtex::create([
                                    'doi' => $doi,
                                    'bibtex' => $crossref_item,
                                    'item_type' => $result['crossref_item_type'],
                                    'item' => $result['crossref_fields'],
                                ]);
    
                                $this->retrievedFromCrossrefCount++;
    
                                $convItem['crossref_item_type'] = $result['crossref_item_type'];
                                $convItem['crossref_item_label'] = $result['crossref_item_label'];
                                $convItem['crossref_item'] = $result['crossref_fields'];
                                $convItem['notices'][] = "Doi found in Crossref database";
                            }
                        } else {
                            $convItem['notices'][] = "Doi not found in Crossref database";
                        }

                        if ($this->crossrefQuotaRemaining == 0) {
                            $convItem['warnings'][] = 'You have now used all your quota of queries to Crossref for today.';
                        }
                    }
                }

                $user->save();

                $conversion->update([
                    'crossref_count' => $this->crossrefQueryCount,
                    'crossref_cache_count' => $this->retrievedFromCacheCount,
                    'crossref_quota_remaining' => $this->crossrefQuotaRemaining
                ]);

                $output = Output::create([
                    'source' => $convItem['source'],
                    'detected_encoding' => $encodings[$i],
                    'conversion_id' => $conversion->id,
                    'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                    'orig_item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                    'label' => $convItem['label'],
                    'item' => $convItem['item'],
                    'orig_item' => $convItem['item'],
                    'crossref_item_type' => $convItem['crossref_item_type'] ?? null,
                    'crossref_item_label' => $convItem['crossref_item_label'] ?? null,
                    'crossref_item' => $convItem['crossref_item'] ?? null,
                    'crossref_source' => $crossrefSource ?? null,
                    'author_pattern' => $convItem['author_pattern'],
                    'seq' => $i,
                ]);

                $convItem['orig_item'] = $convItem['item'];

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

    /**
     * See comment by mta59066 at gmail dot com on https://www.php.net/manual/en/function.mb-detect-encoding.php
     */
    public function mb_detect_encoding_in_order(string $string, array $encodings): string|false
    {
        foreach($encodings as $enc) {
            if (mb_check_encoding($string, $enc)) {
                return $enc;
            }
        }
        return false;
    }

}
