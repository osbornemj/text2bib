<?php

namespace App\Livewire;

use Livewire\Component;

class ItemTypeFields extends Component
{
    public $itemType;

    public function reorder($oldPosition, $newPosition)
    {
        $fields = $this->itemType->fields;
        $out = array_splice($fields, $oldPosition, 1);
        array_splice($fields, $newPosition, 0, $out);

        $this->itemType->fields = $fields;
        $this->itemType->save();
    }
}
