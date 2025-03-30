<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bst;

class BstButton extends Component
{
    public $showModal = false;
    public $bst;

    public function loadBst($id)
    {
        $this->bst = Bst::find($id);
        $this->showModal = true;
    }
}