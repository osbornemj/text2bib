<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;

use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $checkedPublishers = Publisher::where('checked', 1)
            ->orderBy('name')
            ->paginate(50);

        return view('admin.publishers.index', compact('checkedPublishers'));
    }

    public function unchecked(): View
    {
        $uncheckedPublishers = Publisher::where('checked', 0)
            ->orderBy('name')
            ->paginate(50);

        return view('admin.publishers.unchecked', compact('uncheckedPublishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $publisher = new Publisher;

        return view('admin.publishers.create')
                        ->with('publisher', $publisher);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublisherRequest $request)
    {
        $input = $request->except('_token');
        $input['checked'] = 1;

        Publisher::firstOrCreate($input);

        return redirect()->route('publishers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher): View
    {
        return view('admin.publishers.edit')
                        ->with('publisher', $publisher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePublisherRequest $request, int $id)
    {
        $publisher = Publisher::find($id);
        $publisher->name = $request->name;
        $publisher->save();

        return redirect()->route('publishers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher): RedirectResponse
    {
        $publisher->delete();

        return redirect()->route('publishers.index');
    }
}
