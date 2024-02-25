<?php

namespace App\Livewire;

use Livewire\Component;

use App\Livewire\Forms\ShowConvertedItemForm;

use Illuminate\Support\Facades\Auth;

use App\Models\City;
use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\ItemType;
use App\Models\Journal;
use App\Models\Output;
use App\Models\Publisher;
use App\Models\RawOutput;

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
    public $correctness = 0;
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

        $this->correctionsEnabled = true;
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
        if ($this->status != 'changes') {
            $this->correctness = 0;
        }
    }

    public function updatedItemTypeId()
    {
        $this->fields = ItemType::find($this->itemTypeId)->fields;
        $this->displayState = 'block';
    }

    public function setCorrectness($value)
    {
        $this->correctness = $value;
        $this->displayState = $this->correctness == -1 ? 'block' : 'none';

        $output = Output::with('itemType')->where('id', $this->outputId)->first();

        if (in_array($value, [0, 1])) {
            $output->correctness = $value;
            $output->save();
        }

        if ($value == 1) {
            $this->insertPublisherJournalCity($output);
        }
    }

    private function insertPublisherJournalCity($output)
    {
        if ($output->itemType->name == 'article' && isset(($output->item)['journal'])) {
            $journalName = ($output->item)['journal'];
            if (!Journal::where('name', $journalName)->exists()) {
                $journal = new Journal;
                $journal->name = $journalName;
                $journal->save();
            }
        } else {
            if (in_array($output->itemType->name, ['book', 'incollection'])) {
                $publisherName = ($output->item)['publisher'];
                if (isset(($output->item)['publisher'])) {
                    if (!Publisher::where('name', $publisherName)->exists()) {
                        $publisher = new Publisher();
                        $publisher->name = $publisherName;
                        $publisher->save();
                    }
                }
                if (isset(($output->item)['address'])) {
                    $cityName = ($output->item)['address'];
                    if (!City::where('name', $cityName)->exists()) {
                        $city = new City();
                        $city->name = $cityName;
                        $city->save();
                    }
                }
            }
        }
    }

    public function submit(): void
    {
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
                && (($errorReportComment && $errorReportComment->comment_text != $this->form->comment) 
                    || (!$errorReportComment && $this->form->comment))) {
            $changes = true;
        }

        if (!$changes) {
            $this->status = 'noChange';
            $this->displayState = 'block';
        } else {
            // If RawOutput exists for this Output, leave it alone.  Otherwise create
            // a RawOutput from Output, so that the RawOutput contains the original conversion.
            RawOutput::firstOrCreate(
                ['output_id' => $output->id],
                ['output_id' => $output->id, 'item_type_id' => $output->item_type_id, 'item' => $output->item]
            );

            // Change $output according to user's entries
            $output->update(['item_type_id' => $this->itemTypeId]);

            // Update $convertedItem['itemType']
            $itemType = $this->itemTypes->where('id', $this->itemTypeId)->first();
            $this->convertedItem['itemType'] = $itemType->name;

            $inputs = $this->form->except('comment');

            // Restrict to fields relevant to the item_type that are not empty
            $item = [];
            foreach ($inputs as $name => $content) {
                if (in_array($name, $itemType->fields) && $content) {
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
            $newErrorReport = ErrorReport::updateOrCreate(
                ['output_id' => $output->id],
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
                    'comment_text' => strip_tags($this->form->comment),
                ]);
            }

            $this->errorReport = $newErrorReport;

            $this->status = 'changes';
            $this->displayState = 'none';
            // correctness set to 0 because then 'correct' and 'incorrect' buttons are neutral,
            // and 'corrected' button appears because 'status' is 'changes'.
            $this->correctness = 0;
            $output->update(['correctness' => -1]);

            $this->insertPublisherJournalCity($output);
        }
    }
}
