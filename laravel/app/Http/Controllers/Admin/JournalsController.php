<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;

use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JournalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $checkedJournals = Journal::where('checked', 1)
            ->orderBy('name')
            ->get();

        $uncheckedJournals = Journal::where('checked', 0)
            ->orderBy('name')
            ->get();

        return view('admin.journals.index', compact('checkedJournals', 'uncheckedJournals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $journal = new Journal;

        return view('admin.journals.create')
                        ->with('journal', $journal);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJournalRequest $request)
    {
        $input = $request->all();
        $input['checked'] = 1;

        Journal::create($input);

        return redirect()->route('journals.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $journal = Journal::find($id);

        return view('admin.journals.edit')
                        ->with('journal', $journal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJournalRequest $request, int $id)
    {
        $journal = Journal::find($id);
        $journal->name = $request->name;
        $journal->save();

        return redirect()->route('journals.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $journal = Journal::find($id);
        $journal->delete();

        return redirect()->route('journals.index');
    }
}
