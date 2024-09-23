<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStartJournalAbbreviationRequest;
use App\Http\Requests\UpdateStartJournalAbbreviationRequest;
use App\Models\Journal;
use App\Models\Output;
use App\Models\StartJournalAbbreviation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StartJournalAbbreviationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $checkedStartJournalAbbreviations = StartJournalAbbreviation::where('checked', 1)
            ->with('output')
            ->orderBy('word')
            ->get();

        $uncheckedStartJournalAbbreviations = StartJournalAbbreviation::where('checked', 0)
            ->with('output')
            ->orderBy('word')
            ->get();

        return view('admin.startJournalAbbreviations.index', compact('checkedStartJournalAbbreviations', 'uncheckedStartJournalAbbreviations'));
    }

    public function unchecked(): View
    {
        $uncheckedStartJournalAbbreviations = StartJournalAbbreviation::where('checked', 0)
            ->with('output')
            ->orderBy('word')
            ->paginate(50);

        $checked = 0;

        return view('admin.startJournalAbbreviations.unchecked', compact('uncheckedStartJournalAbbreviations', 'checked'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $startJournalAbbreviation = new StartJournalAbbreviation;

        return view('admin.startJournalAbbreviations.create')
                        ->with('startJournalAbbreviation', $startJournalAbbreviation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStartJournalAbbreviationRequest $request)
    {
        $input = $request->except('_token');
        $input['checked'] = 1;
        $input['word'] = rtrim($input['word'], '.');

        StartJournalAbbreviation::firstOrCreate($input);

        return redirect()->route('startJournalAbbreviations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(StartJournalAbbreviation $startJournalAbbreviation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $startJournalAbbreviation = StartJournalAbbreviation::find($id);

        return view('admin.startJournalAbbreviations.edit')
                        ->with('startJournalAbbreviation', $startJournalAbbreviation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStartJournalAbbreviationRequest $request, int $id)
    {
        $startJournalAbbreviation = StartJournalAbbreviation::find($id);
        $startJournalAbbreviation->word = $request->word;
        $startJournalAbbreviation->save();

        return redirect()->route('startJournalAbbreviations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $startJournalAbbreviation = StartJournalAbbreviation::find($id);
        $startJournalAbbreviation->delete();

        return redirect()->route('startJournalAbbreviations.index');
    }

    public function populate()
    {
        $outputs = Output::all();

        foreach ($outputs as $output) {
            $item = $output->item;
            if (isset($item['journal'])) {
                $journal = $item['journal'];
                if ($journal) {
                    $journalWords = explode(' ', $journal);
                    foreach ($journalWords as $word) {
                        if (strlen($word) > 1 && substr($word, -1) == '.') {
                            $abbrev = substr($word, 0, -1);
                            if ($abbrev) {
                                StartJournalAbbreviation::firstOrCreate(
                                    ['word' => $abbrev],
                                    ['output_id' => $output->id]
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
