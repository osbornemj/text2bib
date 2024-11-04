<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBstRequest;
use App\Http\Requests\UpdateBstRequest;

use App\Models\Bst;

//use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BstsController extends Controller
{
    var $nonstandardFields;

    public function __construct()
    {
        $this->nonstandardFields = config('constants.nonstandard_bst_fields');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $bsts = Bst::where('checked', 1)
            ->orderBy('name')
            ->paginate(50);

        return view('admin.bsts.index', compact('bsts'));
    }

    public function unchecked(): View
    {
        $uncheckedBsts = Bst::where('checked', 0)
            ->orderBy('name')
            ->paginate(50);

        return view('admin.bsts.unchecked', compact('uncheckedBsts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $bst = new Bst;

        $nonstandardFields = $this->nonstandardFields;

        return view('admin.bsts.create', compact('bst', 'nonstandardFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBstRequest $request)
    {
        $input = $request->except('_token');
        $input['name'] = strtolower($input['name']);
        $input['checked'] = 1;

        Bst::firstOrCreate($input);

        return redirect()->route('bsts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bst $bst): View
    {
        $nonstandardFields = $this->nonstandardFields;

        return view('admin.bsts.edit', compact('bst', 'nonstandardFields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBstRequest $request, int $id)
    {
        $bst = Bst::find($id);
        $input = $request->except('_token');
        $input['name'] = strtolower($input['name']);
        $input['checked'] = 1;
        $bst->update($input);

        return redirect()->route('bsts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bst $bst): RedirectResponse
    {
        $bst->delete();

        return redirect()->route('bsts.index');
    }
}
