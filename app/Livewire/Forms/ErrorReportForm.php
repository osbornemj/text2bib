<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

use App\Models\ItemType;
use App\Models\Output;

class ErrorReportForm extends Form
{
    //public $outputId;
    //public $itemTypeOptions;
    //public $fields;

    //public $itemTypeId;
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
    
    public $comment;
}
