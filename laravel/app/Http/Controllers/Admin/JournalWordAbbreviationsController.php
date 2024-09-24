<?php

namespace App\Http\Controllers\Admin;

use DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalWordAbbreviationRequest;
use App\Http\Requests\UpdateJournalWordAbbreviationRequest;
use App\Models\Journal;
use App\Models\Output;
use App\Models\JournalWordAbbreviation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class JournalWordAbbreviationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $checkedJournalWordAbbreviations = JournalWordAbbreviation::where('checked', 1)
            ->with('output')
            ->orderBy('word')
            ->paginate(50);

        $uncheckedJournalWordAbbreviationCount = JournalWordAbbreviation::where('checked', 0)->count();

        return view('admin.journalWordAbbreviations.index', compact('checkedJournalWordAbbreviations', 'uncheckedJournalWordAbbreviationCount'));
    }

    public function unchecked(): View
    {
        $uncheckedJournalWordAbbreviations = JournalWordAbbreviation::where('checked', 0)
            ->with('output')
            ->orderBy('word')
            ->paginate(50);

        $checked = 0;

        return view('admin.journalWordAbbreviations.unchecked', compact('uncheckedJournalWordAbbreviations', 'checked'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $journalWordAbbreviation = new JournalWordAbbreviation;

        return view('admin.journalWordAbbreviations.create')
                        ->with('journalWordAbbreviation', $journalWordAbbreviation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJournalWordAbbreviationRequest $request)
    {
        $input = $request->except('_token');
        $input['checked'] = 1;
        $input['word'] = rtrim($input['word'], '.');

        JournalWordAbbreviation::firstOrCreate($input);

        return redirect()->route('journalWordAbbreviations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(JournalWordAbbreviation $journalWordAbbreviation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $journalWordAbbreviation = JournalWordAbbreviation::find($id);

        return view('admin.journalWordAbbreviations.edit')
                        ->with('journalWordAbbreviation', $journalWordAbbreviation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJournalWordAbbreviationRequest $request, int $id)
    {
        $journalWordAbbreviation = JournalWordAbbreviation::find($id);
        $journalWordAbbreviation->word = $request->word;
        $journalWordAbbreviation->save();

        return redirect()->route('journalWordAbbreviations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $journalWordAbbreviation = JournalWordAbbreviation::find($id);
        $journalWordAbbreviation->delete();

        return redirect()->route('journalWordAbbreviations.index');
    }

    public function populate(): RedirectResponse
    {
//        DB::table('outputs')->orderBy('id')->chunk(100, function (Collection $outputs) {

        $outputs = Output::where('id', '<', 100000)->where('id', '>=', 90000)->get();
        foreach ($outputs as $output) {
            $item = $output->item;
            if (isset($item['journal'])) {
                $journal = $item['journal'];
                if ($journal) {
                    $journalWords = explode(' ', $journal);
                    foreach ($journalWords as $word) {
                        if (preg_match('/^[A-Z][a-z]*\.$/', $word)) {
                            $abbrev = substr($word, 0, -1);
                            if ($abbrev) {
                                JournalWordAbbreviation::firstOrCreate(
                                    ['word' => $abbrev],
                                    ['output_id' => $output->id]
                                );
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('journalWordAbbreviations.index');

    }
}
