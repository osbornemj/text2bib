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

    #[Rule('required', message: 'Please choose one of the options')]
    #[Rule('in:latex,biblatex,zotero-word,mendeley,refworks,endnote,other', message: 'The value of this field must be "latex", "biblatex", "zotero-word", "mendeley", "refworks", "endnote", or "other"')]
    public $use;

    #[Rule('required_if:use,latex', message: 'Please enter the name of the BibTeX style file you will use')]
    #[Rule('regex:/^[a-z0-9\-]+$/i', message: 'The name you have entered is not a valid name for a style file.  Enter the argument of the \bibliographystyle command in your document.')]
    public $bstName;

    #[Rule('required_if:use,other', message: 'Please describe how you will use the BibTeX file')]
    public $other_use;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:line,cr', message: 'The value of this field must be "line" or "cr"')]
    public $item_separator;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:en,cz,es,fr,my,nl,pt', message: 'The value of this field must be "English", "Burmese", "Czech", "Dutch", "French", "Portuguese", or "Spanish"')]
    public $language;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:short,long,long-kebab,gs', message: 'The value of this field must be "short", "long", "long-kebab", or "gs"')]
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
    public $use_crossref;

    #[Rule('required', message: 'Please choose an option')]
    public $save_settings;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:standard,detailed', message: 'The value of this field must be "standard" or "detailed"')]
    public $report_type;
}
