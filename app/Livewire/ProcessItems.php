<?php

namespace App\Livewire;

use Livewire\Component;

class ProcessItems extends Component
{
    public $numberProcessed = 0;

    public function process()
    {
        for($i = 0; $i < 3; $i++) {
            sleep(1);
            $this->numberProcessed++;
//            $this->dispatch('update-number-processed', numberProcessed: $this->numberProcessed);
            $this->dispatch('update-number-processed', numberProcessed: $this->numberProcessed);
        }
        dd('done');
    }

    public function render()
    {
        return view('livewire.process-items');
    }
}
