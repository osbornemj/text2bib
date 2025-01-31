<?php

namespace App\Livewire;

use App\Models\City;
// use App\Livewire\Forms\ShowConvertedItemForm;

use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\ItemType;
use App\Models\Journal;
use App\Models\JournalWordAbbreviation;
use App\Models\Output;
use App\Models\Publisher;
use App\Models\User;
use App\Notifications\ErrorReportPosted;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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

    public $origFields;

    public $crossrefFields;

    public $errorReport;

    public $language;

    public $itemTypeId;

    public $displayState;

    public $status;

    public $correctness;

    public $correctionExists;

    public $priorReportExists;

    public $correctionsEnabled;

    public $source = 'conversion';

    public function mount()
    {
        foreach ($this->convertedItem['item'] as $field => $content) {
            $this->$field = $content;
        }

        // There is no itemType if the user is viewing an item for which she chose
        // the result reported by Crossref and the item type that Crossref reported
        // is not one of the item types that text2bib detects (for example, it is @inbook).
        $itemType = $this->itemTypes->where('id', $this->convertedItem['item_type_id'])->first();
        $this->itemTypeId = $itemType?->id;

        // For Burmese, show only the fields in the item
        if (! $itemType || $this->language == 'my') {
            $this->fields = [];
            foreach ($this->convertedItem['item'] as $f => $c) {
                $this->fields[] = $f;
            }
        } else {
            $this->fields = $itemType->fields;
            $this->origFields = $itemType->fields;
        }

        foreach ($this->fields as $field) {
            $this->$field = $this->convertedItem['item']->$field ?? '';
        }

        if (isset($this->convertedItem['crossref_item_type'])) {
            $crossrefItemType = $this->itemTypes->where('name', $this->convertedItem['crossref_item_type'])->first();
            if ($crossrefItemType) {
                $this->crossrefFields = $crossrefItemType->fields;
            } else {
                // If crossref type is not one of the types detected by Converter, assign the crossrefFields
                // to be the ones in the record retrieved from Crossref
                $this->crossrefFields = [];
                foreach ($this->convertedItem['crossref_item'] as $f => $c) {
                    $this->crossrefFields[] = $f;
                }
            }
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
            $item[$field] = $this->convertedItem['crossref_item']->$field;
            $this->$field = $this->convertedItem['crossref_item']->$field;
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
            $this->$field = $this->convertedItem['crossref_item']->$field;
            $item[$field] = $this->convertedItem['crossref_item']->$field;
        }

        // Update $this->convertedItem
        $this->convertedItem['item']->$field = $item[$field];

        // Update entry in database
        $output->update(['item' => $item]);
    }

    public function setItemSource($itemSource)
    {
        $output = Output::find($this->outputId);

        if ($itemSource == 'crossref') {
            $item = (object) $output->crossref_item;
            $item_type_id = $output->crossref_item_type_id;
            $item_type_name = $item_type_id ? ItemType::find($item_type_id)->name : $output->crossref_item_type;
            $this->fields = $this->crossrefFields;
            $this->source = 'crossref';
        } else {
            $item = (object) $output->orig_item;
            $item_type_id = $output->orig_item_type_id;
            $item_type_name = ItemType::find($item_type_id)->name;
            $this->fields = $this->origFields;
            $this->source = 'conversion';
        }

        $this->itemTypeId = $item_type_id;
        $output->update(['item' => $item, 'item_type_id' => $item_type_id]);
        $this->convertedItem['item'] = $item;
        $this->convertedItem['itemType'] = $item_type_name;
        $this->convertedItem['item_type_id'] = $item_type_id;

        $this->itemTypeOptions = $this->itemTypes->pluck('name', 'id')->all();
        if (! in_array($item_type_name, $this->itemTypeOptions)) {
            $this->itemTypeOptions[99] = $item_type_name;
            $this->itemTypeId = 99;
        }
        // dd($this->itemTypeOptions);

        foreach ($this->fields as $field) {
            $this->$field = $this->convertedItem['item']->$field ?? '';
        }
    }

    public function updatedItemTypeId()
    {
        if ($this->itemTypeId == 99) {
            $inputs = $this->except('comment');
            $this->fields = $inputs['crossrefFields'];
        } else {
            $this->fields = ItemType::find($this->itemTypeId)->fields;
        }

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
        if ($output->itemType && $output->itemType->name == 'article' && isset(($output->item)['journal'])) {
            $journalName = ($output->item)['journal'];
            if (! Journal::where('name', $journalName)->exists()) {
                $journal = new Journal;
                $journal->name = substr($journalName, 0, 255);
                $journal->save();
            }
            if (preg_match_all('/(^| )(?P<word>[A-Z][a-z]+)\./', $journalName, $matches)) {
                if (isset($matches['word'])) {
                    foreach ($matches['word'] as $word) {
                        JournalWordAbbreviation::firstOrCreate(
                            ['word' => $word],
                            ['output_id' => $output->id]
                        );
                    }
                }
            }
        } else {
            if ($output->itemType && in_array($output->itemType->name, ['book', 'incollection'])) {
                if (isset(($output->item)['publisher'])) {
                    $publisherName = ($output->item)['publisher'];
                    if (! Publisher::where('name', $publisherName)->exists()) {
                        $publisher = new Publisher;
                        $publisher->name = substr($publisherName, 0, 255);
                        $publisher->save();
                    }
                }
                if (isset(($output->item)['address'])) {
                    $cityName = ($output->item)['address'];
                    if (! City::where('name', $cityName)->exists()) {
                        $city = new City;
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
                    || (! $errorReportComment && $this->comment))) {
            $changes = true;
        }

        if (! $changes) {
            $this->status = 'noChange';
            $this->displayState = 'block';
        } else {
            // Change $output according to user's entries
            if ($this->itemTypeId == 99) {
                $output->update(['item_type_id' => null]);
            } else {
                $output->update(['item_type_id' => $this->itemTypeId]);
            }

            // Update $convertedItem['itemType']
            if ($this->itemTypeId == 99) {
                $this->convertedItem['itemType'] = $output->crossref_item_type;
            } else {
                $itemType = $this->itemTypes->where('id', $this->itemTypeId)->first();
                $this->convertedItem['itemType'] = $itemType->name;
            }

            $inputs = $this->except('comment');

            $item = [];
            if ($this->itemTypeId == 99) {
                $this->fields = $inputs['crossrefFields'];
                foreach ($this->fields as $field) {
                    $this->$field = $inputs[$field];
                    $item[$field] = $inputs[$field];
                    $this->convertedItem['item']->$field = $inputs[$field];
                }
            } else {
                // Restrict to fields relevant to the item_type
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
            }

            // Update entry in database
            $output->update(['item' => $item]);

            if ($this->itemTypeId != 99) {
                $this->itemTypeId = $output->item_type_id;
                $this->fields = $itemType->fields;
            }

            $this->correctionExists = true;

            // File report
            if ($this->postReport) {
                $newErrorReport = ErrorReport::updateOrCreate(
                    ['output_id' => $output->id],
                );

                if ($this->priorReportExists) {
                    if ($this->comment) {
                        $errorReportComment->update([
                            'comment_text' => $this->comment,
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
            $this->source = '';
            $this->displayState = 'none';
            // correctness = 2 for item that has been corrected
            $this->correctness = 2;
            $output->update(['correctness' => 2]);

            $this->insertPublisherJournalCity($output);
        }
    }
}
