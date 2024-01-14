<?php

namespace App\Livewire;

use Livewire\Component;

use App\Livewire\Forms\ShowConvertedItemForm;

use Illuminate\Support\Facades\Auth;

use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\ItemField;
use App\Models\ItemType;
use App\Models\Output;
//use App\Models\OutputField;
use App\Models\RawOutput;
//use App\Models\RawOutputField;

class ShowConvertedItem extends Component
{
    public ShowConvertedItemForm $form;

    public $convertedItem;
    public $outputId;
    public $itemTypeOptions;
    public $itemTypes;
    public $fields;
    public $errorReport;

    public $itemTypeId;

    public $displayState;
    public $status;
    public $errorReportExists;
    public $priorReportExists;
    public $correctionsEnabled;

    public function mount()
    {
        foreach ($this->convertedItem['item'] as $name => $content) {
            $this->form->{$name} = $content;
        }

        $itemType = $this->itemTypes->where('name', $this->convertedItem['itemType'])->first();
        $this->itemTypeId = $itemType->id;
        $this->fields = $itemType->fields;
        
        $this->displayState = 'none';

        //$errorReport = ErrorReport::where('output_id', $this->outputId)->first();
        $this->correctionsEnabled = true;
        $this->form->reportTitle = '';
        $this->errorReportExists = false;
        $this->priorReportExists = false;
        /*
        if ($errorReport) {
            $this->form->reportTitle = $errorReport->title;
            $this->errorReportExists = true;    
            $this->priorReportExists = true;
            $errorReportCommentByOtherExists = ErrorReportComment::where('error_report_id', $errorReport->id)
                ->where('user_id', '!=', Auth::user()->id)
                ->exists();
            if ($errorReportCommentByOtherExists) {
                $this->correctionsEnabled = false;
            }
        } else {
            $this->form->reportTitle = '';
            $this->errorReportExists = false;
            $this->priorReportExists = false;
        }
        */
    }

    public function showForm()
    {
        $this->displayState = 'block';
    }

    public function hideForm()
    {
        $this->displayState = 'none';
    }

    public function updatedItemTypeId()
    {
        $this->fields = ItemType::find($this->itemTypeId)->fields;
        $this->displayState = 'block';
    }

    public function submit(): void
    {
        if (isset($this->form->postReport)) {
            $this->validate();
        }

        // Determine whether user has made any changes
        $changes = false;
        $output = Output::find($this->outputId);

        if ($output->item_type_id != $this->itemTypeId) {
            $changes = true;
        } else {
            foreach ($output->item as $name => $content) {
                if ($content != $this->form->$name) {
                    $changes = true;
                    break;
                }
            }
        }
        
        $errorReport = ErrorReport::where('output_id', $output->id)->orderBy('created_at', 'asc')->first();
        $errorReportComment = $errorReport ? ErrorReportComment::where('error_report_id', $errorReport->id)->first() : null;
        $this->priorReportExists = $errorReport ? true : false;
        if (!$changes 
                && $errorReport 
                && (($errorReport->title != $this->form->reportTitle) 
                    || ($errorReportComment && $errorReportComment->comment_text != $this->form->comment) 
                    || (!$errorReportComment && $this->form->comment))) {
            $changes = true;
        }

        if (!$changes) {
            $this->status = 'noChange';
            $this->displayState = 'block';
        } else {
            // If RawOutput exists for this Output, leave it alone.  Otherwise create
            // a RawOutput from Output.
            $rawOutput = RawOutput::firstOrCreate(
                ['output_id' => $output->id],
                ['output_id' => $output->id, 'item_type_id' => $output->item_type_id, 'item' => $output->item]
            );

            // Change $output according to user's entries
            $output->update(['item_type_id' => $this->itemTypeId]);

            // Update $convertedItem['itemType']
            $itemType = $this->itemTypes->where('id', $this->itemTypeId)->first();
            $this->convertedItem['itemType'] = $itemType->name;

            $inputs = $this->form->except('comment');

            // Restrict to fields relevant to the item_type
            $item = [];
            foreach ($inputs as $name => $content) {
                $itemField = ItemField::where('name', $name)->first();
                if (in_array($name, $itemType->fields)) {
                    $this->form->{$name} = $content;
                    $item[$name] = $content;
                }
            }

            // Update entry in database
            $output->update(['item' => $item]);

            // Update $this->convertedItem['item'] fields
            foreach ($item as $name => $content) {
                $this->convertedItem['item']->$name = $content;
            }

            $this->itemTypeId = $output->item_type_id;
            $this->fields = $itemType->fields;

            // File report
            if ($this->form->postReport) {
                $newErrorReport = ErrorReport::updateOrCreate(
                    ['output_id' => $output->id],
                    ['title' => $this->form->reportTitle],
                );
                $this->errorReportExists = true;

                if ($this->priorReportExists) {
                    if ($this->form->comment) {
                        $errorReportComment->update([
                            'comment_text' => $this->form->comment
                        ]);
                    } elseif ($errorReportComment) {
                        $errorReportComment->delete();
                    }
                } elseif ($this->form->comment) {
                    ErrorReportComment::create([
                        'error_report_id' => $newErrorReport->id,
                        'user_id' => Auth::user()->id,
                        'comment_text' => $this->form->comment,
                    ]);
                }

                $this->errorReport = $newErrorReport;
            }

            // Notify admin?

            $this->status = 'changes';
            $this->displayState = 'none';
        }
    }

    public function render()
    {
        return view('livewire.show-converted-item');
    }
}
