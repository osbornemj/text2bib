<?php

namespace App\Livewire;

use Livewire\Component;

use App\Livewire\Forms\ErrorReportForm;

use App\Models\ItemField;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\OutputField;
use App\Models\RawOutput;
use App\Models\RawOutputField;

class ErrorReport extends Component
{
    public ErrorReportForm $form;

    public $output;
    public $outputId;
    public $itemTypeOptions;
    public $fields;

    public $itemTypeId;

    public $displayState;
    public $status;

    public function mount()
    {
        $output = Output::where('id', $this->outputId)
            ->with('fields.itemField')
            ->first();

        foreach ($output->fields as $field) {
            $itemField = $field->itemField;
            $this->form->{$itemField->name} = $field->content;
        }

        $this->itemTypeId = $output->item_type_id;
        $itemType = ItemType::find($this->itemTypeId);
        $this->fields = $itemType->itemFields->sortBy('id');

        $this->displayState = 'none';
    }

    public function updatedItemTypeId()
    {
        $itemType = ItemType::find($this->itemTypeId);
        $this->fields = $itemType->itemFields->sortBy('id');
        $this->displayState = 'block';
    }

    public function submit($outputId): void
    {
        // If no changes have been made, abort
        $changes = false;
        $output = Output::where('id', $this->outputId)
            ->with('fields')
            ->first();
        if ($output->item_type_id != $this->itemTypeId) {
            $changes = true;
        } elseif ($this->form->comment) {
            $changes = true;
        } else {
            foreach ($output->fields as $field) {
                $name = $field->itemField->name;
                if ($field->content != $this->form->$name) {
                    $changes = true;
                    break;
                }
            }
        }

        if (!$changes) {
            $this->status = 'noChange';
            $this->displayState = 'block';
        } else {
            // If RawOutput exists for this Output, leave it alone.  Otherwise create
            // a RawOutput (and RawOutputFields) from Output and its fields.
            $rawOutput = RawOutput::where('output_id', $output->id)->first();
            if (!$rawOutput) {
                $rawOutput = RawOutput::create([
                        'output_id' => $output->id,
                        'item_type_id' => $output->item_type_id,
                        'comment' => $this->form->comment,
                ]);

                foreach ($output->fields as $field) {
                    RawOutputField::create([
                            'raw_output_id' => $rawOutput->id,
                            'item_field_id' => $field->item_field_id,
                            'content' => $field->content,
                            'seq' => $field->seq,
                    ]);
                }
            }

            // Change $output according to user's entries
            // $output->item_type_id = $this->itemTypeId;
            // $output->save();
            $output->update(['item_type_id' => $this->itemTypeId]);

            foreach ($output->fields as $field) {
                $field->delete();
            }

            $inputs = $this->form->except('comment');
            
            // Restrict to fields relevant to the item_type
            $itemType = ItemType::find($this->itemTypeId);
            $i = 0;
            foreach ($inputs as $key => $input) {
                $i++;
                $itemField = ItemField::where('name', $key)->first();
                if ($itemType->itemFields->contains($itemField)) {
                    OutputField::create([
                        'output_id' => $output->id,
                        'item_field_id' => $itemField->id,
                        'content' => strip_tags($input),
                        'seq' => $i,
                    ]);
                }
            }

            // outputFields have changed, so need to get them again
            $output = Output::where('id', $this->outputId)
                ->with('fields')
                ->first();
            $this->output = $output;
            foreach ($output->fields as $field) {
                $itemField = $field->itemField;
                $this->form->{$itemField->name} = $field->content;
            }

            $this->itemTypeId = $output->item_type_id;
            $itemType = ItemType::find($this->itemTypeId);
            $this->fields = $itemType->itemFields->sortBy('id');

            $this->status = 'changes';
            $this->displayState = 'block';
        }
    }
}
