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
        $itemFields = ['address', 'annote', 'archiveprefix', 'author', 'booktitle', 'chapter', 'date', 'doi', 'edition', 'editor',
            'howpublished', 'eprint', 'institution', 'isbn', 'journal', 'key', 'month', 'note', 'number', 'oclc',
            'organization', 'pages', 'publisher', 'school', 'series', 'title', 'type', 'url', 'urldate', 'volume', 'year'];

        foreach ($itemFields as $name) {
            ItemField::create([
                'name' => $name
            ]);
        }

        $itemTypeItemFields = [
            'article' => ['author', 'title', 'journal', 'year', 'month', 'volume', 'number', 'pages', 'note', 'doi',
                 'url', 'archiveprefix', 'eprint', 'date'],
            'book' => ['author', 'editor', 'title', 'edition', 'volume', 'series', 'year', 'address', 'publisher',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint', 'isbn', 'oclc'],
            'techreport' => ['author', 'title', 'year', 'month', 'number', 'institution', 'note', 'type', 'url',
                 'archiveprefix', 'eprint'],
            'incollection' => ['author', 'title', 'booktitle', 'volume', 'series', 'year', 'publisher', 'address', 'editor', 'pages',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint', 'volume', 'series', 'isbn'],
            'inproceedings' => ['author', 'title', 'booktitle', 'year', 'month', 'publisher', 'address', 'editor',
                 'pages', 'note', 'archiveprefix', 'eprint', 'doi'],
            'unpublished' => ['author', 'title', 'year', 'month', 'note', 'url', 'archiveprefix', 'eprint', 'doi'],
            'mastersthesis' => ['author', 'title', 'school', 'year', 'month', 'note', 'url', 'doi'],
            'phdthesis' => ['author', 'title', 'school', 'year', 'month', 'note', 'url', 'doi'],
            'online' => ['author', 'title', 'year', 'url', 'urldate', 'month'],
        ];

        foreach($itemTypeItemFields as $name => $fields) {
            ItemType::create([
                'name' => $name,
                'fields' => $fields
            ]);
        }
    }
}
