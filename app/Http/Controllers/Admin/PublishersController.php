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
            ->get();

        $uncheckedPublishers = Publisher::where('checked', 0)
            ->orderBy('name')
            ->get();

        return view('admin.publishers.index', compact('checkedPublishers', 'uncheckedPublishers'));
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
        $input = $request->all();
        $input['checked'] = 1;

        Publisher::create($input);

        return redirect()->route('publishers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $publisher = Publisher::find($id);

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
    public function destroy(int $id): RedirectResponse
    {
        $publisher = Publisher::find($id);
        $publisher->delete();

        return redirect()->route('publishers.index');
    }
}
