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

use App\Traits\AddLabels;

use App\Services\Converter;

class ConversionController extends Controller
{
    use AddLabels;

    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    /*
    public function bibtex()
    {
        return view('index.bibtex');
    }
    */

    // Converts entries in exising file according to parameters of Conversion with id = $conversionId.
    // Duplicate of code in Livewire component ConvertFile (with minor changes: return ... -> redirect ...).
    // Used by Admin.
    // Can duplication be avoided?
    /*
    public function convert(int $conversionId): View|bool
    {
        $conversion = Conversion::find($conversionId);

        // Get file that user uploaded
        $filestring = Storage::disk('public')->get('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);

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

        $convertedEntries = [];
        foreach ($entries as $entry) {
            // $convertedEntries is array with components 'source', 'item', 'itemType', 'label', 'warnings',
            // 'notices', 'details'.
            // 'label' (which depends on whole set of converted items) is updated later
            $convertedEntry = $this->converter->convertEntry($entry, $conversion);
            if ($convertedEntry) {
                $convertedEntries[] = $convertedEntry;
            }
        }

        // $this->addLabels from AddLabbels trait
        $convertedEntries = $this->addLabels($convertedEntries, $conversion);

        $itemTypes = ItemType::all();

        // Write converted items to database and key array to output ids
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

        $itemTypeOptions = $itemTypes->pluck('name', 'id')->all();
        $conversionId = $conversion->id;
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
    */

    public function downloadBibtex(int $conversionId): StreamedResponse
    {
        $user = Auth::user();

        $conversion = Conversion::find($conversionId);
        $includeSource = $conversion->include_source;
        $lineEndings = $conversion->line_endings;

        if ($conversion->user_id != $user->id && ! $user->is_admin)  {
            abort(403);
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
}

