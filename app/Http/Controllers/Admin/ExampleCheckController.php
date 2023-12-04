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

        $report = '';
        foreach ($examples as $example) {
            // line endings should be reglarized when example is saved
            //$source = $this->regularizeLineEndings($example->source);
            $source = $example->source;

            $output = $this->converter->convertEntry($source, $conversion);
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
}
