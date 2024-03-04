<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\ItemField;
use App\Models\ItemType;

class ItemTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $itemTypes = ItemType::orderBy('name')
            ->get();

        $itemFields = ItemField::orderBy('name')
            ->get();

        $itemTypeNames = [];
        foreach ($itemTypes as $itemType) {
            $itemTypeNames[$itemType->id] = $itemType->name;
        }

        $itemFieldNames = [];
        foreach ($itemFields as $itemField) {
            $itemFieldNames[$itemField->id] = $itemField->name;
        }

        return view('admin.itemTypes.index')
            ->with('itemFields', $itemFields)
            ->with('itemFieldNames', $itemFieldNames)
            ->with('itemTypes', $itemTypes)
            ->with('itemTypeNames', $itemTypeNames);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $itemType = new ItemType;

        return view('admin.itemTypes.create')
                        ->with('itemType', $itemType);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->all();

        ItemType::create($input);

        return redirect()->route('itemTypes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $itemType = ItemType::find($id);

        return view('admin.ItemTypes.edit')
                        ->with('itemType', $itemType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $itemType = ItemType::find($id);

        if (!isset($request['default_tier'])) {
            $request['default_tier'] = 0;
        }

        $itemType->update($request->all());

        return redirect()->route('itemTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $itemType = ItemType::find($id);

        $itemType->delete();

        return redirect()->route('itemTypes.index');
    }

    /**
     * Add the specified field to the specified type.
     */
    public function add(Request $request): RedirectResponse
    {
        $itemType = ItemType::find($request->itemType_id);
        $itemField = ItemField::find($request->itemField_id);

        if (!in_array($itemField->name, $itemType->fields)) {
            $fields = $itemType->fields;
            array_push($fields, $itemField->name);
            $itemType->fields = $fields;
            $itemType->save();
        }

        return redirect()->route('itemTypes.index');
    }

    /**
     * Remove the specified field from the specified type.
     */
    public function remove(string $itemField, int $itemTypeId): RedirectResponse
    {
        $itemType = ItemType::find($itemTypeId);
        $itemType->fields = array_diff($itemType->fields, [$itemField]);
        $itemType->save();        

        return redirect()->route('itemTypes.index');
    }
}
