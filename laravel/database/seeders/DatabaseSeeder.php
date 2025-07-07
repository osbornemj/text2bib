<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ItemField;
use App\Models\ItemType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $itemFields = ['address', 'annote', 'archiveprefix', 'author', 'booksubtitle', 'booktitle', 'chapter', 'date', 'doi', 'edition', 'editor', 'eprint',
            'howpublished', 'institution', 'isbn', 'issn', 'journal', 'key', 'month', 'note', 'number', 'oclc',
            'organization', 'pages', 'pagetotal', 'publisher', 'school', 'series', 'subtitle', 'title', 'translator', 'type', 'url', 'urldate', 'volume', 'year'];

        foreach ($itemFields as $name) {
            ItemField::create([
                'name' => $name
            ]);
        }

        $itemTypeItemFields = [
            'article' => ['author', 'year', 'title', 'journal', 'volume', 'number', 'month', 'pages', 'note', 'doi',
                 'url', 'archiveprefix', 'eprint', 'date', 'issn', 'urldate'],
            'book' => ['author', 'year', 'editor', 'title', 'subtitle', 'volume', 'edition', 'series', 'address', 'publisher',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint', 'isbn', 'oclc', 'translator', 'pagetotal', 'number', 'urldate'],
            'incollection' => ['author', 'year', 'title', 'booktitle', 'booksubtitle', 'volume', 'edition', 'series', 'editor', 'address', 'publisher', 'pages',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint', 'isbn', 'chapter', 'translator', 'number'],
            'inproceedings' => ['author', 'year', 'title', 'booktitle', 'booksubtitle', 'editor', 'address', 'publisher', 'pages', 'month', 'note', 'archiveprefix', 'eprint', 'doi', 'translator'],
            'mastersthesis' => ['author', 'year', 'title', 'subtitle', 'school', 'month', 'note', 'url', 'doi', 'pagetotal'],
            'online' => ['author', 'year', 'title', 'url', 'urldate', 'month', 'note', 'date'],
            'phdthesis' => ['author', 'year', 'title', 'subtitle', 'school', 'month', 'note', 'url', 'doi', 'isbn', 'pagetotal'],
            'techreport' => ['author', 'year', 'title', 'type', 'number', 'institution', 'month', 'note', 'url',
                 'archiveprefix', 'eprint', 'doi'],
            'unpublished' => ['author', 'month', 'title', 'year', 'note', 'url', 'urldate', 'archiveprefix', 'eprint', 'doi'],
        ];

        foreach($itemTypeItemFields as $name => $fields) {
            ItemType::create([
                'name' => $name,
                'fields' => $fields
            ]);
        }
    }
}
