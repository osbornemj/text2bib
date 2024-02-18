<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;

use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $checkedCities = City::where('checked', 1)
            ->orderBy('name')
            ->get();

        $uncheckedCities = City::where('checked', 0)
            ->orderBy('name')
            ->get();

        return view('admin.cities.index', compact('checkedCities', 'uncheckedCities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $city = new City;

        return view('admin.cities.create')
                        ->with('city', $city);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        $input = $request->all();
        $input['checked'] = 1;

        City::create($input);

        return redirect()->route('cities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $city = City::find($id);

        return view('admin.cities.edit')
                        ->with('city', $city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, int $id)
    {
        $city = City::find($id);
        $city->name = $request->name;
        $city->save();

        return redirect()->route('cities.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $city = City::find($id);
        $city->delete();

        return redirect()->route('cities.index');
    }
}
