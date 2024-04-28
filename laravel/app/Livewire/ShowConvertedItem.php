<?php

namespace App\Livewire;

use App\Http\Requests\StoreStartJournalAbbreviationRequest;
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
use App\Models\StartJournalAbbreviation;
use App\Models\User;

use App\Notifications\ErrorReportPosted;

class ShowConvertedItem extends Component
{
    public ShowConvertedItemForm $form;

    public $convertedItem;
    public $outputId;
    public $itemTypeOptions;
    public $itemTypes;
    public $fields;
    public $errorReport;
    public $language = 'my';

    public $itemTypeId;

    public $displayState;
    public $status;
    public $correctness;
    public $correctionExists;
    public $priorReportExists;
    public $correctionsEnabled;

    public function mount()
    {
        foreach ($this->convertedItem['item'] as $name => $content) {
            $this->form->{$name} = $content;
        }

        $itemType = $this->itemTypes->where('name', $this->convertedItem['itemType'])->first();
        $this->itemTypeId = $itemType->id;

        // For Burmese items, just show the fields in the item
        if ($this->language == 'my') {
            $this->fields = [];
            foreach ($this->convertedItem['item'] as $f => $c) {
                $this->fields[] = $f;
            }
        } else {
            $this->fields = $itemType->fields;
        }

        $this->displayState = 'none';

        $output = Output::find($this->outputId);
        $this->correctness = $output->correctness;
        $this->correctionsEnabled = true;
        $this->correctionExists = false;
        $this->priorReportExists = false;

        /*
        if ($errorReport) {
            $this->form->reportTitle = $errorReport->title;
            $this->correctionExists = true;    
            $this->priorReportExists = true;
            $errorReportCommentByOtherExists = ErrorReportComment::where('error_report_id', $errorReport->id)
                ->where('user_id', '!=', Auth::user()->id)
                ->exists();
            if ($errorReportCommentByOtherExists) {
                $this->correctionsEnabled = false;
            }
        } else {
            $this->form->reportTitle = '';
            $this->correctionExists = false;
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
            if (! Journal::where('name', $journalName)->exists()) {
                $journal = new Journal;
                $journal->name = $journalName;
                $journal->save();
            }
            if (preg_match('/^(?P<firstWord>[A-Z][a-z]+)\. /', $journalName, $matches)) {
                if (isset($matches['firstWord'])) {
                    StartJournalAbbreviation::firstOrCreate(
                        ['word' => $matches['firstWord']],
                        ['output_id' => $output->id]
                    );
                }
            }
        } else {
            if (in_array($output->itemType->name, ['book', 'incollection'])) {
                if (isset(($output->item)['publisher'])) {
                    $publisherName = ($output->item)['publisher'];
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
            foreach ($this->fields as $field) {
                if ((isset($output->item[$field]) && $output->item[$field] != $this->form->$field)
                        ||
                        (! isset($output->item[$field]) && !empty($this->form->$field))
                    ) {
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

            // Restrict to fields relevant to the item_type
            $item = [];
            foreach ($inputs as $name => $content) {
                if (in_array($name, $itemType->fields)) {
                    $this->form->{$name} = $content;
                    if (!empty($content)) {
                        $item[$name] = $content;
                        $this->convertedItem['item']->$name = $content;                    
                    } else {
                        unset($this->convertedItem['item']->$name);
                    }                    
                }
            }

            // Update entry in database
            $output->update(['item' => $item]);

            $this->itemTypeId = $output->item_type_id;
            $this->fields = $itemType->fields;

            $this->correctionExists = true;

            // File report
            if ($this->form->postReport) {
                $newErrorReport = ErrorReport::updateOrCreate(
                    ['output_id' => $output->id],
                );

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

                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new ErrorReportPosted($newErrorReport->id));
                }
            }

            $this->status = 'changes';
            $this->displayState = 'none';
            // correctness = 2 for item that has been corrected
            $this->correctness = 2;
            $output->update(['correctness' => 2]);

            $this->insertPublisherJournalCity($output);
        }
    }
}
