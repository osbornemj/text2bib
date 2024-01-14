<?php

namespace App\Http\Controllers\Convert;

use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Models\Conversion;
use App\Models\Example;
use App\Models\ItemType;
use App\Models\ItemField;
use App\Models\Output;
use App\Models\OutputField;

use App\Services\Converter;

class ConversionController extends Controller
{

    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function convert(int $conversionId): View|bool
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);

        // Get file that user uploaded
        $filestring = Storage::disk('public')->get('files/' . $user->id . '-' . $conversion->user_file_id . '-source.txt');

        // if (mb_detect_encoding($filestring, 'UTF-8, ISO-8859-1', true) === 'UTF-8'){
        //      dd('utf-8');
        // } else {
        //      dd('not utf-8');
        // }

        $filestring = $this->regularizeLineEndings($filestring);
        $entries = explode($conversion->item_separator == 'line' ? "\n\n" : "\n", $filestring);

        if (count($entries) == 1 && strlen($entries[0]) > 500) {
            $entry = $entries[0];
            return view('index.itemSeparatorError', compact('entry', 'conversionId'));
        }

        // Check for utf-8
        $nonUtf8Entries = [];
        foreach ($entries as $entry) {
            if (!mb_check_encoding($entry)) {
                $nonUtf8Entries[] = $entry;
            }
        }

        if (count($nonUtf8Entries)) {
            return view('index.encodingError', compact('nonUtf8Entries'));
        }

        $convItems = [];
        foreach ($entries as $entry) {
            // $convItems is array with components 'source', 'item', 'itemType', 'label', 'warnings',
            // 'notices', 'details'.
            // 'label' (which depends on whole set of converted items) is updated later
            $convertedEntry = $this->converter->convertEntry($entry, $conversion);
            if ($convertedEntry) {
                $convItems[] = $convertedEntry;
            }
        }

        $convItems = $this->addLabels($convItems, $conversion);

        $itemTypes = ItemType::all();

        // Write converted items to database and key array to output ids
        $convertedItems = [];
        foreach ($convItems as $i => $convItem) {
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

        $itemTypeOptions = $itemTypes->pluck('name', 'id')->all();
        $includeSource = $conversion->include_source;
        $reportType = $conversion->report_type;

        return view('index.bibtex',
            compact(
                'convertedItems',
                'itemTypes',
                'itemTypeOptions',
                'conversionId',
                'includeSource',
                'reportType'
            )
        );
    }

    public function redo(int $conversionId): RedirectResponse
    {
        $conversion = Conversion::find($conversionId);
        $conversion->update(['item_separator' => 'cr']);

        return redirect('convert/' . $conversion->id);
    }

    // private function isUtf8(string $string): bool
    // {
    //     return mb_check_encoding($string, 'UTF-8');
        // Following is from W3C site.
        // return preg_match('%^(?:
        //     [\x09\x0A\x0D\x20-\x7E]              # ASCII
        //     | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        //     |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        //     | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        //     |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        //     |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        //     | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        //     |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        //     )*$%xs', $string);
    // }

    public function addLabels(array $convertedItems, Conversion $conversion): array
    {
        $baseLabels = [];
        foreach ($convertedItems as $key => $convertedItem) {
            if ($convertedItem['label'] && !$conversion->override_labels) {
                $baseLabel = $convertedItem['label'];
            } else {
                $baseLabel = $this->makeLabel($convertedItem['item'], $conversion);
            }

            $label = $baseLabel;
            // if $baseLabel already used, add a suffix to it
            if (in_array($baseLabel, $baseLabels)) {
                $values = array_count_values($baseLabels);
                $label .= chr(96 + $values[$baseLabel]);
            }

            $baseLabels[] = $baseLabel;
            $convertedItems[$key]['label'] = $label;
        }

        return $convertedItems;
    }

    public function makeLabel(object $item, Conversion $conversion): string
    {
        $label = '';
        if (isset($item->author) && $item->author) {
            $authors = explode(" and ", $item->author);
        } elseif (isset($item->editor)) {
            $authors = explode(" and ", $item->editor);
        } else {
            $authors = [];
        }
        if (isset($authors) && $authors) {
            foreach ($authors as $author) {
                $authorLetters = $this->onlyLetters(Str::ascii($author));
                if ($pos = strpos($author, ',')) {
                    if ($conversion->label_style == 'short') {
                        $label .= $authorLetters[0];
                    } else {
                        $label .= substr($authorLetters, 0, $pos);
                    }
                } else {
                    if ($conversion->label_style == 'short') {
                        $label .= substr(trim(strrchr($authorLetters, ' '), ' '), 0, 1);
                    } else {
                        $label .= trim(strrchr($authorLetters, ' '), ' ');
                    }
                }
            }
        }

        if ($conversion->label_style == 'short') {
            $label = mb_strtolower($label) . substr($item->year, 2);
        } elseif ($conversion->label_style == 'gs') {
            $firstAuthor = count($authors) ? $authors[0] : 'none';

            if (strpos($firstAuthor, ',') === false) {
                // assume last name is last segment
                if (strpos($firstAuthor, ' ') === false) {
                    $label = mb_strtolower($firstAuthor);
                } else {
                    $r = strrpos($firstAuthor, ' ');
                    $label = mb_strtolower(substr($firstAuthor, $r+1));
                }
            } else {
                // last name is segment up to comma
                $label = mb_strtolower(substr($firstAuthor, 0, strpos($firstAuthor, ',')));
            }
            $label .= $item->year;
            $title = $item->title;
            if (Str::startsWith($title, ['A ', 'The ', 'On ', 'An '])) {
                $title = Str::after($title, ' ');   
            }
            
            $firstTitleWord = Str::before($title, ' ');

            $label .= mb_strtolower($this->onlyLetters($firstTitleWord));
        } else {
            $label .= $item->year;
        }

        return $label;
    }

    public function downloadBibtex(int $conversionId): StreamedResponse
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);
        $includeSource = $conversion->include_source;
        $lineEndings = $conversion->line_endings;

        if ($conversion->user_id != $user->id)  {
            die('Invalid');
        }                   

        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        return new StreamedResponse(
            function () use ($outputs, $includeSource, $lineEndings) {
                if ($lineEndings == 'w') {
                    $cr = "\r\n";
                } elseif ($lineEndings == 'l') {
                    $cr = "\n";
                }

                $handle = fopen('php://output', 'w');
                foreach ($outputs as $output) {
                    $item = '';
                    if ($includeSource) {
                        $item .= '% ' . $output->source . $cr;
                    }
                    $item .= '@' . $output->itemType->name . '{' . $output->label . ',' . $cr;
                    foreach ($output->item as $name => $content) {
                        $item .= '  ' . $name . ' = {' . $content . '},' . $cr;
                    }
                    $item .= '}' . $cr . $cr;
                
                    fwrite($handle, $item);
                }
                fclose($handle);
            },
            200,
            [
                'Content-type'        => 'text/plain; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename=bibtex.bib'
            ]
        );
    }

    // Replace \r\n and \r with \n
    public function regularizeLineEndings(string $string): string
    {
        return str_replace(["\r\n", "\r"], "\n", $string);
    }

    // Returns string consisting only of letters and spaces in $string
    public function onlyLetters(string $string): string
    {
        return preg_replace("/[^a-z\s]+/i", "", $string);
    }

    ///////////////// REMAINING METHODS PROBABLY NOT USED ///////////////////////
    /*
    public function convertIncremental(Request $request, int $conversionId, int $index = 0): View
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);

        // Need to get all entries even if $request->source is set, to calculate count($entries)
        $filestring = Storage::disk('public')->get('files/' . $user->id . '-' . $conversion->user_file_id . '-source.txt');
        $filestring = $this->regularizeLineEndings($filestring);
        $entries = explode($conversion->item_separator == 'line' ? "\n\n" : "\n", $filestring);

        if ($request->source) {
            $source = $request->source;
            // itemTypeId = 0 is auto-detect
            if ($request->itemTypeId) {
                $itemType = ItemType::find($request->itemTypeId);
                $this->itemType = $itemType->name;
            }
        } else {
            $source = $entries[$index];
        }

        $bibtexItem = $this->convertEntry($source, $conversion);
        $bibtex = $this->makeBibtex([$bibtexItem], $conversion);

        $itemType = ItemType::where('name', $bibtexItem['item']->kind)->first();

        $itemTypeOptions = ItemType::orderBy('name')->select('id', 'name')->get()->toArray();
        $selected = [];
        $options[0] = 'auto-detect';
        foreach ($itemTypeOptions as $i) {
            $options[$i['id']] = $i['name'];
            if ($i['name'] == $bibtexItem['item']->kind) {
                $selected[$i['id']] = true;
            }
        }

        $scholarTitle = '';
        if (isset($bibtexItem['item']->title)) {
            $scholarTitle = str_replace(' ', '+', $bibtexItem['item']->title);
            $scholarTitle = Str::remove(["'", '"', "{", "}", "\\"], $scholarTitle);
        }

        $googleScholarUrl = "https://scholar.google.com/scholar?as_q=" . $scholarTitle . "&num=100&btnG=Search+Scholar&as_sdt=1.&as_sdtp=on&as_sdtf=&as_sdts=5&hl=en";
        $jstorUrl = "https://www.jstor.org/action/doAdvancedSearch?q0=" . $scholarTitle . "&f0=ti&c1=AND&q1=&f1=ti&wc=on&Search=Search&sd=&ed=&la=&jo=";

        return view('index.outputIncremental')
            ->with('conversion', $conversion)
            ->with('bibtex', $bibtex)
            ->with('entryCount', count($entries))
            ->with('index', $index)
            ->with('options', $options)
            ->with('selected', $selected)
            ->with('googleScholarUrl', $googleScholarUrl)
            ->with('jstorUrl', $jstorUrl)
            ->with('itemFields', $itemType->itemFields)
            ->with('scholarTitle', $scholarTitle)
            ->with('bibtexItem', $bibtexItem);
    }

    public function addOutput(Request $request, int $conversionId): RedirectResponse
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);

        // No need to pass warnings and notices, which are not used in BibTeX file
        $bibtexItem = [
            'source' => $request->source,
            'item' => (object) $request->except('_token', 'index', 'entryCount', 'source'),
            'warnings' => [],
            'notices' => []
        ];

        $itemType = ItemType::where('name', $bibtexItem['item']->kind)->first();

        $output = new Output;
        $output->source = $bibtexItem['source'];    
        $output->conversion_id = $conversion->id;
        $output->item_type_id = $itemType->id;
        $output->seq = $request->index + 1;
        $output->save();

        $i = 0;
        foreach ($bibtexItem['item'] as $key => $content) {
            if ($key != 'kind') {
                $itemField = ItemField::where('name', $key)->first();

                $i++;
                $outputField = new OutputField;
                $outputField->output_id = $output->id;
                $outputField->item_field_id = $itemField->id;
                $outputField->content = $content;
                $outputField->seq = $i;
                $outputField->save();
            }
        }

        if ($request->index + 1 < $request->entryCount) {
            return redirect()->route('file.convertIncremental', ['conversionId' => $conversionId, 'index' => $request->index + 1]);
        } else {
            return redirect()->route('conversion.showBibtex', ['fileId' => $conversion->bib_file_id]);
        }
    }
    */
    /*
    public function addToBibtex(Request $request, int $conversionId)
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);

        // No need to pass warnings and notices, which are not used in BibTeX file
        $bibtexItem = ['source' => $request->source, 'item' => (object) $request->except('_token', 'index', 'entryCount', 'source'), 'warnings' => [], 'notices' => []];
        $bibtex = $this->makeBibtex([$bibtexItem], $conversion);
        Storage::disk('public')->append('files/' . $user->id . '-' . $conversion->bib_file_id . '-bib.bib', $bibtex);

        if ($request->index + 1 < $request->entryCount) {
            return redirect()->route('file.convertIncremental', ['conversionId' => $conversionId, 'index' => $request->index + 1]);
        } else {
            return redirect()->route('file.showBibtex', ['fileId' => $conversion->bib_file_id]);
        }
    }
    */


    public function showBibtex(int $conversionId): View
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);
        $includeSource = $conversion->include_source;

        if ($conversion->user_id != $user->id)  {
            die('Invalid');
        }                   

        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('fields.itemField')
                    ->with('itemType')
                    ->get();

        $itemTypes = ItemType::all();
        $itemTypeOptions = [];
        foreach ($itemTypes as $itemType) {
            $itemTypeOptions[$itemType->id] = $itemType->name;
        }

        $fields = [];
        $itemTypeId = 0;

        return view('index.bibtex', compact('outputs', 'fields', 'itemTypeId', 'itemTypeOptions', 'conversionId', 'includeSource'));
    }

    /*
    public function showBibtex(int $bibFileId): View
    {
        $user = Auth::user();
        $filestring = Storage::disk('public')->get('files/' . $user->id . '-' . $bibFileId . '-bib.bib');
        $bibtexFormatted = str_replace("\n", "<br/>", $filestring);
        $bibtexFormattedArray = explode("\n\n", $filestring);
dd($filestring, $bibtexFormatted);
        return view('index.bibtex', compact('bibFileId', 'bibtexFormatted'));
    }
    */

    /*
    public function downloadBibtex(int $fileId)
    {
        $user = Auth::user();
        return Storage::disk('public')->download('files/' . $user->id . '-' . $fileId . '-bib.bib');
    }
    */

}

