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

    public $bibtexItem;
    public $outputId;
    public $itemTypeOptions;
    public $itemTypes;
    public $fields;

    public $itemTypeId;

    public $displayState;
    public $status;
    public $errorReportExists;
    public $priorReportExists;
    public $correctionsEnabled;

    public function mount()
    {
        foreach ($this->bibtexItem['item'] as $name => $content) {
            if ($name != 'kind') {
                $this->form->{$name} = $content;
            }
        }

        $itemType = $this->itemTypes->where('name', $this->bibtexItem['item']->kind)->first();
        $this->itemTypeId = $itemType->id;
        $this->fields = $itemType->itemFields->sortBy('id');

        $this->displayState = 'none';

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
        $output = Output::where('id', $this->outputId)
            ->with('fields')
            ->first();

        if ($output->item_type_id != $this->itemTypeId) {
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
            // a RawOutput (and RawOutputFields) from Output and its fields.
            $rawOutput = RawOutput::where('output_id', $output->id)->first();
            if (!$rawOutput) {
                $rawOutput = RawOutput::create([
                        'output_id' => $output->id,
                        'item_type_id' => $output->item_type_id,
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
            // Updsate $bibtexItem['item']->kind
            $this->bibtexItem['item']->kind = $this->itemTypes->where('id', $this->itemTypeId)->first()->name;

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
                        'content' => $input,
                        'seq' => $i,
                    ]);
                }
            }

            // Update $bibtexItem['item'] fields
            // ?????????????????????????

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
