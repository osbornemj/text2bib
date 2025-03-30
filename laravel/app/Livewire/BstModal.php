<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bst;

class BstModal extends Component
{
    public $showModal = false;
    public $bst;
    public $nonstandardFields;

    public function loadBst($id)
    {
        $this->nonstandardFields = config('constants.nonstandard_bst_fields');
        $this->bst = Bst::find($id);
        $this->showModal = true;
    }
}