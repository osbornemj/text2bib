<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Example;
use App\Models\ExampleField;

class ExamplesController extends Controller
{
    public function index(): View
    {
        $examples = Example::orderByDesc('id')
            ->with('fields')
            ->paginate(100);

        $typeOptions = ['detailed' => 'details', 'brief' => 'brief'];
        $utf8Options = ['utf8leave' => 'do not convert accents to TeX', 'utf8' => 'convert accents to TeX'];
        $languageOptions = ['en' => 'en', 'cz' => 'cz', 'es' => 'es', 'fr' => 'fr', 'my' => 'my', 'nl' => 'nl', 'pt' => 'pt'];
        $detailOptions = ['show' => 'show', 'hide' => 'hide'];

        return view('admin.examples.index',
            compact('examples', 'typeOptions', 'utf8Options', 'languageOptions', 'detailOptions'));
    }

    public function create(): View
    {
        $example = new Example;

        return view('admin.examples.create')
                        ->with('example', $example);
    }

    public function store(Request $request): RedirectResponse
    {
        $sources = explode("\r\n\r\n", $request->source);
        $bibtexs = explode("\r\n\r\n", $request->bibtex);

        foreach ($sources as $key => $source) {
            $bibtexLines = explode("\r\n", $bibtexs[$key]);

            preg_match('/@([a-z]+)\{([a-zA-Z0-9]*),?/', $bibtexLines[0], $matches);
            $example = Example::create(['source' => $source, 'type' => $matches[1]]);

            // remove type and label
            array_shift($bibtexLines);
            // remove closing }
            array_pop($bibtexLines);

            foreach($bibtexLines as $key => $bibtexLine) {
                $bibtexLineComponents = explode('=', $bibtexLine);
                ExampleField::create([
                    'example_id' => $example->id,
                    'name' => $bibtexLineComponents[0],
                    'content' => trim($bibtexLineComponents[1], '{}, '),
                ]);
            }
        }

        return redirect()->route('examples.index');
    }

    public function edit(int $id): View
    {
        $example = Example::find($id);

        return view('admin.examples.edit')
                        ->with('example', $example);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $example = Example::find($id);

        $example->source = $request->source;
        $example->save();

        return redirect()->route('examples.index');
    }
}
