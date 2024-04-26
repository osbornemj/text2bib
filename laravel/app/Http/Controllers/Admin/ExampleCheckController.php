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

    public function runExampleCheck(Request $request, string $reportType = 'brief', string $language = 'en', string $detailsIfCorrect = 'hide', int $id = null, string $charEncoding = 'utf8'): View
    {
        $conversion = new Conversion;
        $conversion->char_encoding = $request->char_encoding ?: $charEncoding;
        $conversion->report_type = $request->report_type ?: $reportType;
        $conversion->language = $request->language ?: $language;
        $detailsIfCorrect = $request->detailsIfCorrect ?: $detailsIfCorrect;

        $typeOptions = ['detailed' => 'details', 'brief' => 'brief'];
        $utf8Options = ['utf8leave' => 'do not convert accents to TeX', 'utf8' => 'convert accents to TeX'];
        $languageOptions = ['en' => 'en', 'cz' => 'cz', 'es' => 'es', 'fr' => 'fr', 'my' => 'my', 'nl' => 'nl', 'pt' => 'pt'];
        $detailOptions = ['show' => 'show', 'hide' => 'hide'];

        $id = $request->exampleId ?: $id;
        // If single example is being converted, save language of conversion to the example
        // (so that when all examples are converted that language is used for this example)
        if ($id) {
            $example = Example::find($id);
            $example->update(['language' => $conversion->language, 'char_encoding' => $conversion->char_encoding]);
            $examples = [$example];
        } else {
            $examples = Example::all();
        }

        $results = [];
        $allCorrect = true;

        $previousAuthor = null;

        foreach ($examples as $example) {

            $correctType = $correctContent = true;
            $result = null;

            $reportType = $conversion->report_type;

            if ($example->language != 'en') {
                $conversion->char_encoding = 'utf8leave';
            }

            $output = $this->converter->convertEntry($example->source, $conversion, $example->language, $example->char_encoding, $previousAuthor);
            $previousAuthor = $output['item']->author ?? null;
            
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

            if (($conversion->report_type == 'detailed' && $result['result'] == 'incorrect') || $detailsIfCorrect == 'show') {
                $result['details'] = $output['details'];
            }

            $result['language'] = $example->language;
            $result['charEncoding'] = $example->char_encoding;

            if (isset($result) && ($result['result'] == 'incorrect' || $detailsIfCorrect == 'show')) {
                $results[$example->id] = $result;
            }
        }

        $exampleCount = count($examples);

        return view('admin.examples.checkResult',
            compact('results', 'reportType', 'detailsIfCorrect', 'allCorrect', 'exampleCount', 'typeOptions', 'utf8Options', 'languageOptions', 'detailOptions'));
    }
}
