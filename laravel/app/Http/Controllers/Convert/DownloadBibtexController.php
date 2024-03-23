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

