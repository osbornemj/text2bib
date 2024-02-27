<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNameRequest;
use App\Http\Requests\UpdateNameRequest;

use App\Models\Name;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $names = Name::orderBy('name')
            ->get();

        return view('admin.names.index')
            ->with('names', $names);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $name = new Name;

        return view('admin.names.create')
                        ->with('name', $name);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNameRequest $request)
    {
        $input = $request->all();

        Name::create($input);

        return redirect()->route('names.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $name = Name::find($id);

        return view('admin.names.edit')
                        ->with('name', $name);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNameRequest $request, int $id)
    {
        $name = Name::find($id);
        $name->name = $request->name;
        $name->save();

        return redirect()->route('names.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $name = Name::find($id);
        $name->delete();

        return redirect()->route('names.index');
    }
}
