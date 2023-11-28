<?php

namespace App\Livewire;

use Livewire\Component;

use App\Livewire\Forms\ReportErrorForm;

use Illuminate\Support\Facades\Auth;

use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\ItemField;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\OutputField;
use App\Models\RawOutput;
use App\Models\RawOutputField;

class ReportError extends Component
{
    public ReportErrorForm $form;

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
        $this->fields = $itemType->itemFields;

        $this->displayState = 'none';

        /*
        $errorReport = ErrorReport::where('output_id', $this->outputId)->first();
        $this->correctionsEnabled = true;
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
        $itemType = ItemType::find($this->itemTypeId);
        $this->fields = $itemType->itemFields->sortBy('id');
        $this->displayState = 'block';
    }

    public function submit(): void
    {
        if (isset($this->form->postReport)) {
            $this->validate();
        }

        // If no changes have been made, abort
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
            $rawOutput = RawOutput::where('output_id', $output->id)->first();
            if (!$rawOutput) {
                $rawOutput = RawOutput::create([
                        'output_id' => $output->id,
                        'item_type_id' => $output->item_type_id,
                        'item' => $output->item,
                ]);
            }

            // Change $output according to user's entries
            $output->update(['item_type_id' => $this->itemTypeId]);
            // Update $convertedItem['itemType']
            $this->convertedItem['itemType'] = $this->itemTypes->where('id', $this->itemTypeId)->first()->name;

            $inputs = $this->form->except('comment');

            // Restrict to fields relevant to the item_type
            $itemType = ItemType::find($this->itemTypeId);
            $i = 0;
            $item = [];
            foreach ($inputs as $name => $content) {
                $i++;
                $itemField = ItemField::where('name', $name)->first();
                if ($itemType->itemFields->contains($itemField)) {
                    $this->form->{$name} = $content;
                    $item[$name] = $content;
                }
            }

            // Update entry in database
            $output->update(['item' => $item]);

            // Update $this->convertedItem['item'] fields
            $this->convertedItem['item'] = $item;

            $this->itemTypeId = $output->item_type_id;
            $this->fields = $itemType->itemFields->sortBy('id');

            // File report
            if ($this->form->postReport) {
                $newErrorReport = ErrorReport::updateOrCreate(
                    ['output_id' => $output->id],
                    ['title' => $this->form->reportTitle],
                );
                $this->errorReportExists = true;

                if ($this->priorReportExists) {
                    $errorReportComment->update([
                            'comment_text' => $this->form->comment
                        ]);
                } else {
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
        return view('livewire.report-error');
    }
}
