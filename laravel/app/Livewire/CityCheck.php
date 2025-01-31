<?php

namespace App\Livewire;

use Livewire\Component;

class CityCheck extends Component
{
    public $city;

    public $currentPage;

    public $type;

    public function check($value, $currentPage)
    {
        $this->city->checked = $value;
        $this->city->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/cities?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedCities?page='.$currentPage);
        }
    }

    public function distinctive($value)
    {
        $this->city->distinctive = $value;
        $this->city->save();
    }

    public function delete($currentPage)
    {
        $this->city->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/cities?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedCities?page='.$currentPage);
        }
    }
}
