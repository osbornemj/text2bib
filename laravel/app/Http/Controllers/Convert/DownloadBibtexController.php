<?php

namespace App\Http\Controllers\Convert;

use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Conversion;
use App\Models\Output;

class DownloadBibtexController extends Controller
{
    public function __invoke(int $conversionId): StreamedResponse
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
            function () use ($outputs, $includeSource, $lineEndings, $conversion) {
                if ($lineEndings == 'w') {
                    $cr = "\r\n";
                } elseif ($lineEndings == 'l') {
                    $cr = "\n";
                }

                $handle = fopen('php://output', 'w');
                $prologue = '% Created ' . $conversion->created_at . ' by https://text2bib.org.';
                if ($conversion->version) {
                    $prologue .= ' Algorithm version ' . $conversion->version . '.';
                }
                $prologue .= $cr . $cr;
                fwrite($handle, $prologue);
                foreach ($outputs as $output) {
                    $itemType = $output->itemType;
                    // If $itemType is not set, then item_type_id is null because user has chosen
                    // a Crossref-reported item, with a type not among the ones detected by text2bib
                    $itemTypeName = $itemType ? $itemType->name : $output->crossref_item_type;
                    $item = '';
                    if ($includeSource) {
                        $item .= '% ' . $output->source . $cr;
                    }
                    $item .= '@' . $itemTypeName . '{' . $output->label . ',' . $cr;

                    // Include fields in order they are given in itemType, if itemType is defined
                    if ($itemType) {
                        $fields = $itemType->fields;
                        foreach ($fields as $field) {
                            if (isset($output->item[$field])) {
                            $item .= '  ' . $field . ' = {' . $output->item[$field] . '},' . $cr;
                            }
                        }
                    } else {
                        foreach ($output->item as $name => $content) {
                            $item .= '  ' . $name . ' = {' . $content . '},' . $cr;
                        }
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

