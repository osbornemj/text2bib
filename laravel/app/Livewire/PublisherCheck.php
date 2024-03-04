<?php

namespace App\Livewire;

use Livewire\Component;

class PublisherCheck extends Component
{
    public $publisher;

    public function check($value)
    {
        $this->publisher->checked = $value;
        $this->publisher->save();

        return redirect()->to('/admin/publishers');
    }

    public function distinctive($value)
    {
        $this->publisher->distinctive = $value;
        $this->publisher->save();
    }

    public function delete()
    {
        $this->publisher->delete();
        return redirect()->to('/admin/publishers');
    }

}
