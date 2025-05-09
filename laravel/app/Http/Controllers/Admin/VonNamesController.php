<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVonNameRequest;
use App\Http\Requests\UpdateVonNameRequest;

use Illuminate\Support\Facades\DB;

use App\Models\VonName;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VonNamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $vonNames = VonName::orderBy('name')
            ->get();

        return view('admin.vonNames.index')
            ->with('vonNames', $vonNames);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $vonName = new VonName;

        return view('admin.vonNames.create')
                        ->with('vonName', $vonName);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVonNameRequest $request)
    {
        $input = $request->except('_token');

        // Case sensitive
        $vonName = VonName::where(DB::raw('BINARY `name`'), $input['name'])->first();

        if ( ! $vonName ) {
            VonName::create($input);
        }

        // Case insensitive
        //VonName::firstOrCreate($input);

        return redirect()->route('vonNames.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VonName $vonName): View
    {
        return view('admin.vonNames.edit')
                        ->with('vonName', $vonName);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVonNameRequest $request, int $id)
    {
        $vonName = VonName::find($id);
        $vonName->name = $request->name;
        $vonName->save();

        return redirect()->route('vonNames.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VonName $vonName): RedirectResponse
    {
        $vonName->delete();

        return redirect()->route('vonNames.index');
    }
}
