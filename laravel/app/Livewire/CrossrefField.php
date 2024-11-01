<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Output;

class CrossrefField extends Component
{
    #[Reactive]
    public $convertedItem;
    public $outputId;
    #[Reactive]
    public $field;
    public $checked = false;

    public function addCrossrefField()
    {
        $output = Output::find($this->outputId);

        // Add field to $output and to $this->convertedItem.
        $item = $output->item;
        if ($this->checked) {
            $item[$this->field] = $this->convertedItem['crossref_item'][$this->field];
            $this->convertedItem['item']->{$this->field} = $item[$this->field];
        } else {
            unset($item[$this->field]);
            unset($this->convertedItem['item']->{$this->field});
        }

        // Update entry in database
        $output->update(['item' => $item]);
    }

    public function render()
    {
        return view('livewire.crossref-field',
        [
            'convertedItem' => $this->convertedItem,
            'field' => $this->field, 
        ]);
    }
}
