<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

use App\Models\ExampleField;
use Illuminate\Http\RedirectResponse;

class ExampleFieldsController extends Controller
{
    public function editContent(ExampleField $exampleField): View
    {
        return view('admin.exampleFields.editContent')
                        ->with('exampleField', $exampleField);
    }

    public function updateContent(Request $request, int $id): RedirectResponse
    {
        $exampleField = ExampleField::find($id);

        $exampleField->content = $request->content;
        $exampleField->save();

        return redirect()->route('examples.index');
    }
}
