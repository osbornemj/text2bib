<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\City;
use App\Models\Journal;
use App\Models\Output;
use App\Models\Publisher;

class AdminConvertedItem extends Component
{
    public $output;
    public $convertedItem;
    public $originalItem;

    public function mount()
    {
        // Put fields in uniform order
        $convertedItem = [];
        $originalItem = [];
        $fields = $this->output->itemType->fields;
        foreach ($fields as $field) {
            if (isset($this->output->item[$field])) {
                $convertedItem[$field] = $this->output->item[$field];
            }
            if ($this->output->rawOutput && isset($this->output->rawOutput->item[$field])) {
                $originalItem[$field] = $this->output->rawOutput->item[$field];
            }
        }

        $this->convertedItem = $convertedItem;
        $this->originalItem = $originalItem;
    }

    public function setCorrectness($value)
    {
        $this->output->admin_correctness = $value;
        $this->output->save();

        if ($value == 1) {
            $this->insertPublisherJournalCity($this->output);
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
}
