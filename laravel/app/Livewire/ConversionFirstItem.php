<?php

namespace App\Livewire;

use Livewire\Component;

class ConversionFirstItem extends Component
{
    public $conversion;

    public function delete()
    {
        $this->conversion->firstOutput()?->delete();
    }
}
