<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVersionRequest;
use App\Http\Requests\UpdateVersionRequest;

use App\Models\Version;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VersionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $versions = Version::orderBy('version_number')
            ->get();

        return view('admin.versions.index')
            ->with('versions', $versions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $version = new Version;

        return view('admin.versions.create')
                        ->with('version', $version);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVersionRequest $request)
    {
        $input = $request->all();

        Version::create($input);

        return redirect()->route('versions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Version $version)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $version = Version::find($id);

        return view('admin.versions.edit')
                        ->with('version', $version);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVersionRequest $request, int $id)
    {
        $version = Version::find($id);
        $version->version = $request->version;
        $version->save();

        return redirect()->route('versions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $version = Version::find($id);
        $version->delete();

        return redirect()->route('versions.index');
    }
}
