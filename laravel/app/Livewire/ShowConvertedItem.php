<?php

namespace App\Livewire;

use Livewire\Component;

//use App\Livewire\Forms\ShowConvertedItemForm;

use Illuminate\Support\Facades\Auth;

use App\Models\City;
use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\ItemType;
use App\Models\Journal;
use App\Models\Output;
use App\Models\Publisher;
use App\Models\JournalWordAbbreviation;
use App\Models\User;

use App\Notifications\ErrorReportPosted;

class ShowConvertedItem extends Component
{
//    public ShowConvertedItemForm $form;

    public $address;       
    public $annote;       
    public $archiveprefix; 
    public $author;
    public $booktitle;     
    public $chapter;       
    public $date;           
    public $doi;           
    public $edition;       
    public $editor;        
    public $eprint;  
    public $howpublished;      
    public $institution;
    public $isbn;          
    public $issn;          
    public $journal;
    public $key;       
    public $month;         
    public $note;          
    public $number;        
    public $oclc;         
    public $organization;  
    public $pages;         
    public $pagetotal;         
    public $publisher;     
    public $school;        
    public $series;        
    public $title;         
    public $translator;        
    public $type;          
    public $url;           
    public $urldate;       
    public $volume;        
    public $year;          

    public $postReport = false;

    public $comment;

    public $convertedItem;
    public $outputId;
    public $itemTypeOptions;
    public $itemTypes;
    public $fields;
    public $errorReport;
    public $language;

    public $itemTypeId;

    public $displayState;
    public $status;
    public $correctness;
    public $correctionExists;
    public $priorReportExists;
    public $correctionsEnabled;

    public function mount()
    {
        foreach ($this->convertedItem['item'] as $field => $content) {
            $this->$field = $content;
        }

        $itemType = $this->itemTypes->where('name', $this->convertedItem['itemType'])->first();
        $this->itemTypeId = $itemType->id;

        // For Burmese, show only the fields in the item
        if ($this->language == 'my') {
            $this->fields = [];
            foreach ($this->convertedItem['item'] as $f => $c) {
                $this->fields[] = $f;
            }
        } else {
            $this->fields = $itemType->fields;
        }

        foreach ($this->fields as $field) {
            $this->$field = $this->convertedItem['item']->$field ?? '';
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

    /**
     * Add field from Crossref data
     */
    public function addCrossrefField($field)
    {
        $output = Output::find($this->outputId);

        // Add field to $output
        $item = $output->item;
        if (isset($item[$field])) {
            unset($item[$field]);
            unset($this->convertedItem['item']->$field);
            $this->$field = '';
        } else {
           $item[$field] = $this->convertedItem['crossref_item'][$field];
           $this->$field = $this->convertedItem['crossref_item'][$field];
           $this->convertedItem['item']->$field = $item[$field];
        }

        // Update entry in database
        $output->update(['item' => $item]);
    }

    public function setFieldSource($field, $fieldSource)
    {
        $output = Output::find($this->outputId);

        // Set field in $item
        $item = $output->item;
        if ($fieldSource == 'conversion') {
            $this->$field = $this->convertedItem['orig_item']->{$field};
            $item[$field] = $this->convertedItem['orig_item']->{$field};
        } elseif ($fieldSource == 'crossref') {
            $this->$field = $this->convertedItem['crossref_item'][$field];
            $item[$field] = $this->convertedItem['crossref_item'][$field];
        }

        // Update $this->convertedItem
        $this->convertedItem['item']->{$field} = $item[$field];

        // Update entry in database
        $output->update(['item' => $item]);
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
                $journal->name = substr($journalName, 0, 255);
                $journal->save();
            }
            if (preg_match_all('/(^| )(?P<word>[A-Z][a-z]+)\./', $journalName, $matches)) {
                if (isset($matches['word'])) {
                    foreach ($matches['word'] as $word)
                    JournalWordAbbreviation::firstOrCreate(
                        ['word' => $word],
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
                        $publisher->name = substr($publisherName, 0, 255);
                        $publisher->save();
                    }
                }
                if (isset(($output->item)['address'])) {
                    $cityName = ($output->item)['address'];
                    if (!City::where('name', $cityName)->exists()) {
                        $city = new City();
                        $city->name = substr($cityName, 0, 255);
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
                if ((isset($output->item[$field]) && isset($this->$field) && $output->item[$field] != $this->$field)
                        ||
                        (! isset($output->item[$field]) && ! empty($this->$field))
                    ) {
                    $changes = true;
                    break;
                }
            }
        }
        
        $errorReport = ErrorReport::where('output_id', $output->id)->orderBy('created_at', 'asc')->first();
        $errorReportComment = $errorReport ? ErrorReportComment::where('error_report_id', $errorReport->id)->first() : null;
        $this->priorReportExists = $errorReport ? true : false;
        if (! $changes 
                && $errorReport 
                && (($errorReportComment && $errorReportComment->comment_text != $this->comment) 
                    || (!$errorReportComment && $this->comment))) {
            $changes = true;
        }

        if (! $changes) {
            $this->status = 'noChange';
            $this->displayState = 'block';
        } else {
            // Change $output according to user's entries
            $output->update(['item_type_id' => $this->itemTypeId]);

            // Update $convertedItem['itemType']
            $itemType = $this->itemTypes->where('id', $this->itemTypeId)->first();
            $this->convertedItem['itemType'] = $itemType->name;

            $inputs = $this->except('comment');

            // Restrict to fields relevant to the item_type
            $item = [];
            foreach ($inputs as $field => $content) {
                if (in_array($field, $itemType->fields)) {
                    $this->$field = $content;
                    if (! empty($content)) {
                        $item[$field] = $content;
                        $this->convertedItem['item']->$field = $content;                    
                    } else {
                        unset($this->convertedItem['item']->$field);
                    }                    
                }
            }

            // Update entry in database
            $output->update(['item' => $item]);

            $this->itemTypeId = $output->item_type_id;
            $this->fields = $itemType->fields;

            $this->correctionExists = true;

            // File report
            if ($this->postReport) {
                $newErrorReport = ErrorReport::updateOrCreate(
                    ['output_id' => $output->id],
                );

                if ($this->priorReportExists) {
                    if ($this->comment) {
                        $errorReportComment->update([
                            'comment_text' => $this->comment
                        ]);
                    } elseif ($errorReportComment) {
                        $errorReportComment->delete();
                    }
                } elseif ($this->comment) {
                    ErrorReportComment::create([
                        'error_report_id' => $newErrorReport->id,
                        'user_id' => Auth::user()->id,
                        'comment_text' => strip_tags($this->comment),
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
