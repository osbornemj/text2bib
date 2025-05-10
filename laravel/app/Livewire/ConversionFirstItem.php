<?php

namespace App\Livewire;

use App\Models\Conversion;
use Livewire\Component;

class ConversionFirstItem extends Component
{
    public Conversion $conversion;

    public $currentPage;

    public $firstOutput;

    public $style;

    public function mount(Conversion $conversion)
    {
        $this->conversion = $conversion;
        $this->firstOutput = $conversion->firstOutput;
    }

    public function delete()
    {
        if ($this->firstOutput) {
            $this->firstOutput->delete();
            $this->conversion->refresh(); // in case Conversion has been changed by another user or background process
            $this->firstOutput = $this->conversion->firstOutput;
        }
    }
}
