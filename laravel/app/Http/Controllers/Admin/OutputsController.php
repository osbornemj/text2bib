<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Services\Converter;

class OutputsController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function edit(Output $output): View
    {
        return view('admin.conversions.editSource')
                        ->with('output', $output);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $output = Output::find($id);

        $output->source = $request->source;

        $conversion = new Conversion;
        $result = $this->converter->convertEntry($output->source, $conversion, $output->conversion->language, 'utf8leave', 'biblatex');

        $output->item = $result['item'];

        $itemType = ItemType::where('name', $result['itemType'])->first();
        $output->item_type_id = $itemType->id;

        $output->save();

        return redirect()->route('admin.trainingItems.index');
    }

}
