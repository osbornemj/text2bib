<?php

namespace App\Livewire;

use App\Models\Output;
use Livewire\Component;

class ConvertedOrCrossrefField extends Component
{
    public $convertedItem;

    public $outputId;

    public $field;

    public $fieldSource = 'conversion';

    public function setFieldSource()
    {
        $output = Output::find($this->outputId);

        // Set field in $item
        $item = $output->item;
        if ($this->fieldSource == 'conversion') {
            $item[$this->field] = $this->convertedItem['orig_item']->{$this->field};
        } elseif ($this->fieldSource == 'crossref') {
            $item[$this->field] = $this->convertedItem['crossref_item'][$this->field];
        }

        // Update $this->convertedItem
        $this->convertedItem['item']->{$this->field} = $item[$this->field];

        // Update entry in database
        $output->update(['item' => $item]);
    }
}
