<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExcludedWordRequest;
use App\Http\Requests\UpdateExcludedWordRequest;

use App\Models\ExcludedWord;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExcludedWordsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $excludedWords = ExcludedWord::orderBy('word')
            ->get();

        return view('admin.excludedWords.index')
            ->with('excludedWords', $excludedWords);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $excludedWord = new ExcludedWord;

        return view('admin.excludedWords.create')
                        ->with('excludedWord', $excludedWord);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExcludedWordRequest $request)
    {
        $input = $request->except('_token');

        ExcludedWord::firstOrCreate($input);

        return redirect()->route('excludedWords.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExcludedWord $excludedWord): View
    {
        return view('admin.excludedWords.edit')
                        ->with('excludedWord', $excludedWord);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExcludedWordRequest $request, int $id)
    {
        $excludedWord = ExcludedWord::find($id);
        $excludedWord->word = $request->word;
        $excludedWord->save();

        return redirect()->route('excludedWords.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExcludedWord $excludedWord): RedirectResponse
    {
        $excludedWord->delete();

        return redirect()->route('excludedWords.index');
    }
}
