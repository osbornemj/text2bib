<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bst;

class BstModal extends Component
{
    public $showModal = false;
    public ?Bst $bst = null;
    public array $nonstandardFields;

    public function loadBst(int $id)
    {
        $this->nonstandardFields = config('constants.nonstandard_bst_fields');
        $this->bst = Bst::find($id);
        $this->showModal = true;
    }
}