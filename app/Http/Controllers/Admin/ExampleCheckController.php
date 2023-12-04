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

    public function runExampleCheck(bool $verbose = false, int $id = null): View
    {
        $examples = $id ? [Example::find($id)] : Example::all();

        $conversion = new Conversion;

        $results = [];
        foreach ($examples as $example) {
            $source = $example->source;

            $output = $this->converter->convertEntry($source, $conversion);
            $unidentified = '';
            if (isset($output['item']->unidentified)) {
                $unidentified = $output['item']->unidentified;
                unset($output['item']->unidentified);
            }

            $diff = array_diff((array) $output['item'], (array) $example->bibtexFields());

            $result = [];
            if (empty($diff)) {
                $result['result'] = 'correct';
                if ($unidentified) {
                    $result['unidentified'] = $unidentified;
                }
            } else {
                $result['result'] = 'incorrect';
                $result['source'] = $source;
                $result['errors'] = [];
                foreach ($diff as $key => $content) {
                    $bibtexFields = $example->bibtexFields();
                    $result['errors'][$key] = 
                        [
                            'content' => $content,
                            'correct' => isset($bibtexFields->{$key}) ? $bibtexFields->{$key} : ''
                        ];
                }
            }

            if ($verbose) {
                $result['details'] = $output['details'];
            }

            $results[$example->id] = $result;
        }

        return view('admin.examples.checkResult',
            compact('results', 'verbose'));
    }
}
