<?php
namespace App\Traits;

trait MakeScholarTitle
{
    public function makeScholarTitle(string $title): string
    {
        $scholarTitle = str_replace(' ', '+', $title);
        $scholarTitle = str_replace(["'", '"', "{", "}", "\\"], "", $scholarTitle);
        return $scholarTitle;
    }
}