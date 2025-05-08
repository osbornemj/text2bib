<?php

namespace App\Livewire;

use Livewire\Component;

class ConversionFirstItem extends Component
{
    public $conversion;

    public $firstOutput;

    public $style;

    public function mount()
    {
        $this->firstOutput = $this->conversion->firstOutput;
    }

    public function delete()
    {
        $this->firstOutput->delete();
        $this->firstOutput = $this->conversion->firstOutput;
    }
}
