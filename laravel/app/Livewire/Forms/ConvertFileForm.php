<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ConvertFileForm extends Form
{
    #[Rule('required', message: 'Please select a file to upload')]
    #[Rule('max:100', message: 'The size of your file exceeds the maximum allowed, 100K')]
    #[Rule('mimetypes:text/plain,text/x-tex', message: 'Your file is not plain text')]
    public ?TemporaryUploadedFile $file =  null;

    #[Rule('required', message: 'Please choose one of the options')]
    #[Rule('in:latex,biblatex,zotero-word,mendeley,refworks,endnote,other', message: 'The value of this field must be "latex", "biblatex", "zotero-word", "mendeley", "refworks", "endnote", or "other"')]
    public string $use;

    #[Rule('required_if:use,latex', message: 'Please select the BibTeX style file you will use')]
    public ?int $bst_id;

    #[Rule('regex:/^[a-z0-9\-]+$/i', message: 'The name you have entered is not a valid name for a style file.  Enter the argument of the \bibliographystyle command in your document.')]
    #[Rule('nullable', message: '')]
    #[Rule('required_with:bst_url', message: 'Please enter the name of your BibTeX style file')]
    public string $bst_name = '';

    #[Rule('url', message: 'Please enter a valid url for your BibTeX style file')]
    #[Rule('nullable', message: '')]
    #[Rule('required_with:bst_name', message: 'Please enter a url at which your BibTeX style file is available')]
    public string $bst_url;

    #[Rule('required_if:use,other', message: 'Please describe how you will use the BibTeX file')]
    public string $other_use;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:line,cr', message: 'The value of this field must be "line" or "cr"')]
    public string $item_separator;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:en,cz,es,fr,my,nl,pt', message: 'The value of this field must be "English", "Burmese", "Czech", "Dutch", "French", "Portuguese", or "Spanish"')]
    public string $language;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:short,long,long-kebab,gs', message: 'The value of this field must be "short", "long", "long-kebab", or "gs"')]
    public string $label_style;

    #[Rule('required', message: 'Please choose an option')]
    public bool $override_labels;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:w,l', message: 'The value of this field must be "w" or "l"')]
    public string $line_endings;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:utf8,utf8leave', message: 'The value of this field must be "utf8" or "utf8leave"')]
    public string $char_encoding;

    #[Rule('required', message: 'Please choose an option')]
    public bool $percent_comment;

    #[Rule('required', message: 'Please choose an option')]
    public bool $include_source;

    #[Rule('required', message: 'Please choose an option')]
    public bool $use_crossref;

    #[Rule('required', message: 'Please choose an option')]
    public bool $save_settings;

    #[Rule('required', message: 'Please choose an option')]
    #[Rule('string', message: 'The value of this field must be a string')]
    #[Rule('in:standard,detailed', message: 'The value of this field must be "standard" or "detailed"')]
    public string $report_type;
}
