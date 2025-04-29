<?php

namespace App\Livewire;

use App\Models\Conversion;
use Livewire\Component;

class ConversionUsability extends Component
{
    public $conversion;

    public function toggleUsable()
    {
        $this->conversion->usable = 1 - $this->conversion->usable;

        Conversion::find($this->conversion->id)->update(['usable' => $this->conversion->usable]);
    }

}
