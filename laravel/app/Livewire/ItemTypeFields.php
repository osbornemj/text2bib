<?php

namespace App\Livewire;

use App\Models\ItemType;
use Livewire\Component;

class ItemTypeFields extends Component
{
    public ItemType $itemType;

    public function reorder(int $oldPosition, int $newPosition)
    {
        $fields = $this->itemType->fields;
        $out = array_splice($fields, $oldPosition, 1);
        array_splice($fields, $newPosition, 0, $out);

        $this->itemType->fields = $fields;
        $this->itemType->save();
    }
}
