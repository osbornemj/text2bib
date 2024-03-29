<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\Example;

use App\Services\Converter;
use Illuminate\Http\Request;

class ExampleCheckController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function runExampleCheck(Request $request, string $reportType = 'details', string $language = 'en', string $detailsIfCorrect = 'show', int $id = null, string $charEncoding = 'utf8'): View
    {
        $id = $request->exampleId ?: $id;
        $examples = $id ? [Example::find($id)] : Example::all();

        $conversion = new Conversion;
        $conversion->char_encoding = $request->char_encoding ?: $charEncoding;
        $conversion->report_type = $request->report_type ?: $reportType;
        $conversion->language = $request->language ?: $language;
        $detailsIfCorrect = $request->detailsIfCorret ?: $detailsIfCorrect;

        $results = [];
        $allCorrect = true;

        foreach ($examples as $example) {
            $correctType = $correctContent = true;
            $result = null;

            $output = $this->converter->convertEntry($example->source, $conversion);
            
            $unidentified = '';
            if (isset($output['item']->unidentified)) {
                $unidentified = $output['item']->unidentified;
                unset($output['item']->unidentified);
            }

            if ($output['itemType'] != $example->type) {
                $correctType = false;
            }

            if ((array) $output['item'] != (array) $example->bibtexFields()) {
                $correctContent = false;
            }

            $diff1 = array_diff_assoc((array) $output['item'], (array) $example->bibtexFields());
            $diff2 = array_diff_assoc((array) $example->bibtexFields(), (array) $output['item']);

            $result = [];
            $result['source'] = $example->source;
            $result['errors'] = [];
    
            if ($correctType && $correctContent) {
                $result['result'] = 'correct';
            } else {
                $allCorrect = false;
                $result['result'] = 'incorrect';
            }

            if (!$correctType) {
                $result['typeError']['content'] = $output['itemType'];
                $result['typeError']['correct'] = $example->type;
            }

            if (!$correctContent) {
                foreach ($diff1 as $key => $content) {
                    $bibtexFields = $example->bibtexFields();
                    $result['errors'][$key] = 
                        [
                            'content' => $content,
                            'correct' => isset($bibtexFields->$key) ? $bibtexFields->$key : ''
                        ];
                }
                foreach ($diff2 as $key => $content) {
                    $outputFields = $output['item'];
                    $result['errors'][$key] = 
                        [
                            'content' => isset($outputFields->$key) ? $outputFields->$key : '',
                            'correct' => $content
                        ];
                }
            }

            if (($conversion->report_type == 'details' && $result['result'] == 'incorrect') || $detailsIfCorrect == 'show') {
                $result['details'] = $output['details'];
            }

            if (isset($result) && ($result['result'] == 'incorrect' || $detailsIfCorrect == 'show')) {
                $results[$example->id] = $result;
            }
        }

        $exampleCount = count($examples);

        return view('admin.examples.checkResult',
            compact('results', 'reportType', 'detailsIfCorrect', 'allCorrect', 'exampleCount'));
    }
}
