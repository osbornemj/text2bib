<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\City;

class CityCheck extends Component
{
    public City $city;

    public string $currentPage;

    public string $type;

    public function check(string $value, string $currentPage)
    {
        $this->city->checked = $value;
        $this->city->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/cities?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedCities?page='.$currentPage);
        }
    }

    public function distinctive(string $value)
    {
        $this->city->distinctive = $value;
        $this->city->save();
    }

    public function delete(string $currentPage)
    {
        $this->city->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/cities?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedCities?page='.$currentPage);
        }
    }
}
