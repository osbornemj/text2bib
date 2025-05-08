<?php

namespace App\Livewire;

use Livewire\Component;

class ConversionFirstItem extends Component
{
    public $conversion;

    public $style;

    public function delete()
    {
        $this->conversion->firstOutput?->delete();
    }
}
