<?php

namespace App\Livewire;

use Livewire\Component;

class ProcessItems extends Component
{
    public function process()
    {
        for($i = 0; $i < 4; $i++) {
            sleep(1);
        }
    }

    public function render()
    {
        return view('livewire.process-items');
    }
}
