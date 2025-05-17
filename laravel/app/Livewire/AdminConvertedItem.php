<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Journal;
use App\Models\Output;
use App\Models\Publisher;
use Livewire\Component;

class AdminConvertedItem extends Component
{
    public $output;

    public $convertedItem;

    public $originalItem;

    public $originalItemSet;

    public $crossrefItem;

    public function mount()
    {
        // Put fields in uniform order
        $convertedItem = [];
        $originalItem = [];
        $crossrefItem = [];

        // For Burmese items, just show the fields in the item
        if ($this->output->conversion->language == 'my') {
            $fields = [];
            foreach ($this->output->item as $f => $c) {
                $fields[] = $f;
            }
        } else {
            $fields = $this->output->itemType ? $this->output->itemType->fields : [];
        }

        $this->originalItemSet = $this->output->orig_item ? true : false;

        foreach ($fields as $field) {
            if (isset($this->output->item[$field])) {
                $convertedItem[$field] = $this->output->item[$field];
            }
            if (isset($this->output->orig_item[$field])) {
                $originalItem[$field] = $this->output->orig_item[$field];
            }
            if (isset($this->output->crossref_item[$field])) {
                $crossrefItem[$field] = $this->output->crossref_item[$field];
            }
        }

        $this->convertedItem = $convertedItem;
        $this->originalItem = $originalItem;
        $this->crossrefItem = $crossrefItem;
    }

    public function setCorrectness($value)
    {
        $this->output->admin_correctness = $value;
        $this->output->save();

        if ($value == 1) {
            $this->insertPublisherJournalCity($this->output);
        }
    }

    public function delete()
    {
        Output::find($this->output->id)->delete();
        $this->output = null;
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
        } else {
            if (in_array($output->itemType->name, ['book', 'incollection'])) {
                if (isset(($output->item)['publisher'])) {
                    $publisherName = ($output->item)['publisher'];
                    if (! Publisher::where('name', $publisherName)->exists()) {
                        $publisher = new Publisher;
                        $publisher->name = $publisherName;
                        $publisher->save();
                    }
                }
                if (isset(($output->item)['address'])) {
                    $cityName = ($output->item)['address'];
                    if (! City::where('name', $cityName)->exists()) {
                        $city = new City;
                        $city->name = $cityName;
                        $city->save();
                    }
                }
            }
        }
    }
}
