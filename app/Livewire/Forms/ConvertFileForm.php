<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

class ConvertFileForm extends Form
{
    #[Rule('required', message: 'Please select a file to upload')]    
    #[Rule('max:100', message: 'The size of your file exceeds the maximum allowed, 100K')]    
    #[Rule('mimes:txt', message: 'Your file is not plain text')]    
    public $file;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:line,cr', message: 'The value of this field must be "line" or "cr"')]    
    public $item_separator;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:authors,year', message: 'The value of this field must be "authors" or "year"')]    
    public $first_component;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:short,long,gs', message: 'The value of this field must be "short", "long", or "gs"')]    
    public $label_style;

    #[Rule('required', message: 'Please choose an option')]    
    public $override_labels;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:w,l', message: 'The value of this field must be "w" or "l"')]    
    public $line_endings;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:utf8,utf8leave', message: 'The value of this field must be "utf8" or "utf8leave"')]    
    public $char_encoding;

    #[Rule('required', message: 'Please choose an option')]    
    public $percent_comment;

    #[Rule('required', message: 'Please choose an option')]    
    public $include_source;

    #[Rule('required', message: 'Please choose an option')]    
    public $save_settings;

    #[Rule('required', message: 'Please choose an option')]    
    #[Rule('string', message: 'The value of this field must be a string')]    
    #[Rule('in:standard,detailed', message: 'The value of this field must be "standard" or "detailed"')]    
    public $report_type;
}
