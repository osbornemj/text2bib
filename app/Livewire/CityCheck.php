<?php

namespace App\Livewire;

use Livewire\Component;

class CityCheck extends Component
{
    public $city;

    public function check($value)
    {
        $this->city->checked = $value;
        $this->city->save();

        return redirect()->to('/admin/cities');
    }

    public function distinctive($value)
    {
        $this->city->distinctive = $value;
        $this->city->save();
    }

    public function delete()
    {
        $this->city->delete();
        return redirect()->to('/admin/cities');
    }

}
