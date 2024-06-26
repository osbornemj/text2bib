<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDictionaryNameRequest;
use App\Http\Requests\UpdateDictionaryNameRequest;

use App\Models\DictionaryName;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DictionaryNamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $dictionaryNames = DictionaryName::orderBy('word')
            ->get();

        return view('admin.dictionaryNames.index')
            ->with('dictionaryNames', $dictionaryNames);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dictionaryName = new DictionaryName;

        return view('admin.dictionaryNames.create')
                        ->with('dictionaryName', $dictionaryName);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDictionaryNameRequest $request)
    {
        $input = $request->except('_token');

        DictionaryName::firstOrCreate($input);

        return redirect()->route('dictionaryNames.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DictionaryName $dictionaryName)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DictionaryName $dictionaryName): View
    {
        return view('admin.dictionaryNames.edit')
                        ->with('dictionaryName', $dictionaryName);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDictionaryNameRequest $request, DictionaryName $dictionaryName)
    {
        $dictionaryName->word = $request->word;
        $dictionaryName->save();

        return redirect()->route('dictionaryNames.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DictionaryName $dictionaryName): RedirectResponse
    {
        $dictionaryName->delete();

        return redirect()->route('dictionaryNames.index');
    }
}
