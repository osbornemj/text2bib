<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Journal;
use App\Models\Publisher;

class AdminController extends Controller
{
    public function index()
    {
        $uncheckedJournalCount = Journal::where('checked', 0)->count();
        $uncheckedPublisherCount = Publisher::where('checked', 0)->count();
        $uncheckedCityCount = City::where('checked', 0)->count();

        return view('admin.index', compact('uncheckedJournalCount', 'uncheckedPublisherCount', 'uncheckedCityCount'));
    }
}
