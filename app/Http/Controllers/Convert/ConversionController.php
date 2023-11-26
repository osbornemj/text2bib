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

    public function convert(int $conversionId): View
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);

        // Get file that user uploaded
        $filestring = Storage::disk('public')->get('files/' . $user->id . '-' . $conversion->user_file_id . '-source.txt');

        // Convert items in file to BibTeX and write them to the database
        $bibtexItems = $this->convertText($filestring, $conversion);
        $includeSource = $conversion->include_source;

        // After writing the outputs to the database, we now read them back.
        // That seems inefficient, but it's hard to see how to avoid it.
        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('fields.itemField')
                    ->with('itemType')
                    ->get();

        $unidentifieds = $warnings = $details = [];
        foreach ($bibtexItems as $outputId => $bibtexItem) {
            $unidentifieds[$outputId] = $bibtexItem['item']->unidentified ?? '';
            $warnings[$outputId] = $bibtexItem['warnings'];
            $details[$outputId] = $bibtexItem['details'];
        }

        $itemTypes = ItemType::all();
        $itemTypeOptions = [];
        foreach ($itemTypes as $itemType) {
            $itemTypeOptions[$itemType->id] = $itemType->name;
        }

        $fields = [];
        $itemTypeId = 0;

        return view('index.bibtex',
            compact(
                'outputs',
                'fields',
                'itemTypeId',
                'itemTypeOptions',
                'conversionId',
                'includeSource',
                'unidentifieds',
                'warnings',
                'details'
            )
        );
    }

    public function convertText(string $filestring, Conversion $conversion): array
    {
        $filestring = $this->regularizeLineEndings($filestring);
        $entries = explode($conversion->item_separator == 'line' ? "\n\n" : "\n", $filestring);

        $bibtexItems = [];
        foreach ($entries as $i => $entry) {
            $bibtexItem = $this->converter->convertEntry($entry, $conversion);
            $itemType = ItemType::where('name', $bibtexItem['item']->kind)->first();

            $output = Output::create([
                'source' => $bibtexItem['source'],
                'conversion_id' => $conversion->id,
                'item_type_id' => $itemType->id,
                'seq' => $i,
            ]);
    
            $j = 0;
            foreach ($bibtexItem['item'] as $key => $content) {
                if (!in_array($key, ['kind', 'label', 'unidentified'])) {
                    $itemField = ItemField::where('name', $key)->first();
if (!$itemField) {
    dd('key: ' . $key);
}    
                    $j++;
                    OutputField::create([
                        'output_id' => $output->id,
                        'item_field_id' => $itemField->id,
                        'content' => $content,
                        'seq' => $j,
                    ]);
                }
            }
            $bibtexItems[$output->id] = $bibtexItem;
        }

        return $bibtexItems;
    }

    // Replace \r\n and \r with \n
    public function regularizeLineEndings(string $string): string
    {
        return str_replace(["\r\n", "\r"], "\n", $string);
    }

    public function makeBibtex(array $bibtexItems, Conversion $conversion): string
    {
        $cr = $conversion->line_endings == 'w' ? "\r\n" : "\n";
        $output = '';
        $baseLabels = [];
        foreach ($bibtexItems as $bibtexItem) {
            if ($conversion->include_source) {
                $output .= '% ' . $bibtexItem['source'] . $cr;
            }

            $fields = $bibtexItem['item'];

            $output .= '@' . $fields->kind . '{';

            if (isset($fields->label)) {
                $output .= $fields->label;
            } else {
                $baseLabel = $this->makeLabel($fields, $conversion);
            
                $label = $baseLabel;
                if (in_array($baseLabel, $baseLabels)) {
                    $values = array_count_values($baseLabels);
                    $label .= chr(96 + $values[$baseLabel]);
                }

                $baseLabels[] = $baseLabel;
                $output .= $label;
                $fields->label = $label;
            }

            $output .= ',' . $cr;
            foreach($fields as $key => $value) {
                if ($value && !in_array($key, ['kind', 'label'])) {
                    $output .= $key . ' = {' . $value . '},' . $cr;
                }
            }
            // If many items are being added, put blank line between them.
            // (If only one item is added at a time, an additional CR will
            // be added by the append operation.)
            $output .= '}' . $cr . (count($bibtexItems) == 1 ? '' : $cr);
        }

        return $output;
    }

    public function makeLabel($item, Conversion $conversion): string
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
            $label = strtolower($label) . substr($item->year, 2);
        } elseif ($conversion->label_style == 'gs') {
            $firstAuthor = count($authors) ? $authors[0] : 'none';

            if (strpos($firstAuthor, ',') === false) {
                // assume last name is last segment
                if (strpos($firstAuthor, ' ') === false) {
                    $label = strtolower($firstAuthor);
                } else {
                    $r = strrpos($firstAuthor, ' ');
                    $label = strtolower(substr($firstAuthor, $r+1));
                }
            } else {
                // last name is segment up to comma
                $label = strtolower(substr($firstAuthor, 0, strpos($firstAuthor, ',')));
            }
            $label .= $item->year;
            if (Str::startsWith($item->title, ['A ', 'The ', 'On ', 'An '])) {
                $item->title = Str::after($item->title, ' ');   
            }
            
            $firstTitleWord = Str::before($item->title, ' ');

            $label .= strtolower($firstTitleWord);
        } else {
            $label .= $item->year;
        }

        return $label;
    }

    public function downloadBibtex(int $conversionId)
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

        return new StreamedResponse(
            function () use ($outputs, $includeSource) {
                $cr = "\r\n";
                $handle = fopen('php://output', 'w');
                foreach ($outputs as $output) {
                    $item = '';
                    if ($includeSource) {
                        $item .= '% ' . $output->source . $cr;
                    }
                    $item .= '@' . $output->itemType->name . '{' . $cr;
                    foreach ($output->fields as $field) {
                        $item .= '  ' . $field->itemField->name . ' = {' . $field->content . '},' . $cr;
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

    /////////////// SHOULD BE MOVED TO A DIFFERENT FILE ///////////////////
    public function runExampleCheck(bool $verbose = false, int $id = null): View
    {
        $examples = $id ? [Example::find($id)] : Example::all();

        $conversion = new Conversion;

        $report = '';
        foreach ($examples as $example) {
            $source = $this->regularizeLineEndings($example->source);

            $output = $this->convertEntry($source, $conversion);
            $unidentified = '';
            if (isset($output['item']->unidentified)) {
                $unidentified = $output['item']->unidentified;
                unset($output['item']->unidentified);
            }

            $diff = array_diff((array) $output['item'], (array) $example->bibtexFields());

            $report .= "<p>Example " . $example->id . ' converted ';
            if (empty($diff)) {
                $report .= '<span style="background-color: rgb(134 239 172);">correctly</span>';
                if ($unidentified) {
                    $report .= ' (but string "' . $unidentified . '" not assigned to field)';
                }
            } else {
                $report .= '<span style="background-color: rgb(253 164 175);">incorrectly</span>';
                $report .= ' &nbsp; &bull; &nbsp; <a href="' . url('/admin/runExampleCheck/1/' . $example->id) . '">verbose conversion</a>';
                $report .= '<p><span style="background-color: rgb(203 213 225);">Source:</span> ' . $source . '<p>';
                foreach ($diff as $key => $content) {
                    $bibtexFields = $example->bibtexFields();
                    $report .= "<p>" . $key . ':<p>|' . $content . '|';
                    $report .= '<p><i>instead of</i><p>|' . (isset($bibtexFields->{$key}) ? $bibtexFields->{$key} : '') . '|';
                }
            }

            if ($verbose) {
                $report .= '<p>';
                foreach ($this->displayLines as $line) {
                    $report .= $line;
                };
            }
        }

        return view('admin.examples.checkResult')
            ->with('report', $report);
    }

    ///////////////// REMAINING METHODS PROBABLY NOT USED ///////////////////////
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

