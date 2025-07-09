<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Bst;
use App\Models\City;
use App\Models\Conversion;
use App\Models\Journal;
use App\Models\Output;
use App\Models\Publisher;
use App\Models\JournalWordAbbreviation;
use App\Models\Version;

use Illuminate\View\View as View;

class AdminController extends Controller
{
    public function index(): View
    {
        $uncheckedBstCount = Bst::where('checked', 0)->count();
        $uncheckedJournalCount = Journal::where('checked', 0)->count();
        $uncheckedPublisherCount = Publisher::where('checked', 0)->count();
        $uncheckedCityCount = City::where('checked', 0)->count();
        $uncheckedJournalWordAbbreviationCount = JournalWordAbbreviation::where('checked', 0)->count();
        $adminSetting = AdminSetting::first();
        $trainingItemsConversionCount = $adminSetting->training_items_conversion_count;
        $trainingItemsConversionStartedAt = $adminSetting->training_items_conversion_started_at;
        $trainingItemsConversionEndedAt = $adminSetting->training_items_conversion_ended_at;

        $maxCheckedConversionId = AdminSetting::select('max_checked_conversion_id')->first()->max_checked_conversion_id;
        $uncheckedConversionCount = Conversion::where('id', '>', $maxCheckedConversionId)->count();
        $adminCorrectOutputCount = Output::where('admin_correctness', 1)->count();

        $seconds = Carbon::parse($trainingItemsConversionStartedAt)->diffInSeconds(Carbon::parse($trainingItemsConversionEndedAt));
        $itemsPerSecond = $seconds ? number_format($trainingItemsConversionCount / $seconds, 2) : null;

        $latestVersionRecord = Version::latest()->first();
        $latestVersion = $latestVersionRecord ? $latestVersionRecord->created_at : null;

        return view('admin.index', compact(
            'uncheckedBstCount', 
            'uncheckedJournalCount', 
            'uncheckedPublisherCount', 
            'uncheckedCityCount', 
            'uncheckedJournalWordAbbreviationCount', 
            'latestVersion',
            'trainingItemsConversionCount',
            'trainingItemsConversionStartedAt',
            'trainingItemsConversionEndedAt',
            'itemsPerSecond',
            'uncheckedConversionCount',
            'adminCorrectOutputCount',
        ));
    }

    public function addVersion()
    {
        $version = new Version;
        $version->save();

        return redirect('/admin/index');
    }

    public function addExistingStarts()
    {
        $journals = Journal::all();

        foreach ($journals as $journal) {
            if (preg_match('/^(?P<firstWord>[^ \.]*)\. /', $journal->name, $matches)) {
                if (isset($matches['firstWord'])) {
                    $input['word'] = $matches['firstWord'];
                    JournalWordAbbreviation::firstOrCreate($input);
                }
            }
        }

        return redirect()->route('journalWordAbbreviations.index');
    }

    public function phpinfo()
    {
        return view('admin.phpinfo');
    }
}    
