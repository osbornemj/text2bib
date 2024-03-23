<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\City;
use App\Models\Journal;
use App\Models\Publisher;
use App\Models\Version;

use Illuminate\View\View as View;

class AdminController extends Controller
{
    public function index(): View
    {
        $uncheckedJournalCount = Journal::where('checked', 0)->count();
        $uncheckedPublisherCount = Publisher::where('checked', 0)->count();
        $uncheckedCityCount = City::where('checked', 0)->count();

        $latestVersion = Version::latest()->first()->created_at;

        return view('admin.index', compact('uncheckedJournalCount', 'uncheckedPublisherCount', 'uncheckedCityCount', 'latestVersion'));
    }

    public function addVersion()
    {
        $version = new Version;
        $version->save();

        return redirect('/admin/index');
    }
}
