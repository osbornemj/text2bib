<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\Example;

use App\Services\Converter;

class ExampleCheckController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function run()
    {
        $examples = Example::all();
    }

    public function runExampleCheck(bool $verbose = false, int $id = null, string $charEncoding = 'utf8'): View
    {
        $examples = $id ? [Example::find($id)] : Example::all();

        $conversion = new Conversion;
        $conversion->char_encoding = $charEncoding ? $charEncoding : 'utf8';

        $results = [];
        $allCorrect = true;

        foreach ($examples as $example) {
            $source = $example->source;

            $output = $this->converter->convertEntry($source, $conversion);
            
            $unidentified = '';
            if (isset($output['item']->unidentified)) {
                $unidentified = $output['item']->unidentified;
                unset($output['item']->unidentified);
            }

            $diff1 = array_diff((array) $output['item'], (array) $example->bibtexFields());
            $diff2 = array_diff((array) $example->bibtexFields(), (array) $output['item']);

            if (!empty($diff1) || !empty($diff2)) {
                $result = [];
                $allCorrect = false;
                $result['result'] = 'incorrect';
                $result['source'] = $source;
                $result['errors'] = [];
                foreach ($diff1 as $key => $content) {
                    $bibtexFields = $example->bibtexFields();
                    $result['errors'][$key] = 
                        [
                            'content' => $content,
                            'correct' => isset($bibtexFields->{$key}) ? $bibtexFields->{$key} : ''
                        ];
                }
                foreach ($diff2 as $key => $content) {
                    $outputFields = $output['item'];
                    $result['errors'][$key] = 
                        [
                            'content' => isset($outputFields->{$key}) ? $outputFields->{$key} : '',
                            'correct' => $content
                        ];
                }

                if ($verbose) {
                    $result['details'] = $output['details'];
                }
    
                $results[$example->id] = $result;
            }
        }

        return view('admin.examples.checkResult',
            compact('results', 'verbose', 'allCorrect'));
    }
}
