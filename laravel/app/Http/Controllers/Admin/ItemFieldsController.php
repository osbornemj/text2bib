<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\ItemField;

class ItemFieldsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $itemField = new ItemField;

        return view('admin.itemFields.create')
            ->with('itemField', $itemField);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->all();
        ItemField::create($input);

        return redirect()->route('itemTypes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $itemField = ItemField::find($id);

        return view('admin.ItemFields.edit')
                        ->with('itemField', $itemField);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $ItemField = ItemField::find($id);

        if (!isset($request['default_tier'])) {
            $request['default_tier'] = 0;
        }

        $ItemField->update($request->all());

        return redirect()->action('Admin\ItemFieldsController@index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $ItemField = ItemField::find($id);

        $ItemField->delete();

        return redirect()->action('Admin\ItemFieldsController@index');
    }
}
