<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\ItemType;

class SubmitErrorReport extends Component
{
    //public $outputId;
    public $output;
    public $itemTypeOptions;
    public $fields;

    public $itemTypeId;

    public $author;
    public $title;         
    public $journal;       
    public $year;          
    public $month;         
    public $volume;        
    public $number;        
    public $pages;         
    public $note;          
    public $doi;           
    public $url;           
    public $editor;        
    public $edition;       
    public $series;        
    public $address;       
    public $publisher;     
    public $archiveprefix; 
    public $eprint;        
    public $isbn;          
    public $oclc;         
    public $institution;
    public $type;          
    public $booktitle;     
    public $school;        
    public $urldate;       
    public $chapter;       
    public $organization;     

    public function mount()
    {
        /*
        $output = Output::where('id', $this->outputId)
            ->with('fields.itemField')
            ->first();
        */

        $this->fields = $this->output->fields;
        $this->itemTypeId = $this->output->item_type_id;

        foreach ($this->output->fields as $field) {
            $itemField = $field->itemField;
            $this->{$itemField->name} = $field->content;
        }
    }

    public function updatedItemTypeId($value)
    {
        //dd('qqq');
        $itemType = ItemType::find($value);
        $this->fields = $itemType->itemFields->orderBy('id');
        //$this->fields = [];
    }

    public function updated($property)
    {
        if ($property === 'author') {
            dd('www');
            $this->author = 'ABD';
        }
    }

    public function submitErrorReport($outputId)
    {
        dd($this->only(['author']));
    }

    public function render()
    {
        return view('livewire.submit-error-report');
    }
}
