<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ShowConvertedItemForm extends Form
{
    public string $author;

    public string $title;

    public string $journal;

    public int $year;

    public int $month;

    public string $volume;

    public string $number;

    public string $pages;

    public string $note;

    public string $doi;

    public string $url;

    public string $editor;

    public string $edition;

    public string $series;

    public string $address;

    public string $publisher;

    public string $archiveprefix;

    public string $eprint;

    public string $isbn;

    public string $oclc;

    public string $institution;

    public string $type;

    public string $booktitle;

    public string $school;

    public string $urldate;

    public string $chapter;

    public string $organization;

    public bool $postReport = false;

    public string $comment;
}
