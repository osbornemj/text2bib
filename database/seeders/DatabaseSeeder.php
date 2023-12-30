<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\City;
use App\Models\ExcludedWord;
use App\Models\ItemField;
use App\Models\ItemType;
use App\Models\Name;
use App\Models\Publisher;
use App\Models\VonName;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Words that are in dictionary but are abbreviations in journal names
        $excludedWords = ['Trans', 'Ind', 'Int', 'Soc', 'Proc', 'Phys', 'Rev', 'Amer', 'Math', 'Meth', 'Geom', 'Univ',
             'Nat', 'Sci', 'Austral'];

        foreach ($excludedWords as $word) {
            ExcludedWord::create([
                'word' => $word
            ]);
        }

        // von names
        // Van: to deal with case like Van de Stahl, H.
        // la: to deal with de la Monte, for example
        $vonNames = ["de", "De", "der", "da", "das", "della", "la", "van", "Van", "von"];
        
        foreach ($vonNames as $name) {
            VonName::create([
                'name' => $name
            ]);
        }

        // The script will identify strings as cities and publishers even if they are not in these arrays---but the
        // presence of a string in one of the arrays helps when the elements of the reference are not styled in any way.
        $cities = ['Berlin', 'Boston', 'Cambridge', 'Chicago', 'Greenwich', 'Heidelberg', 'London', 'New York', 'Northampton',
            'Oxford', 'Philadelphia', 'Princeton', 'San Diego', 'Upper Saddle River', 'Washington'];
        
        foreach ($cities as $name) {
            City::create([
                'name' => $name
            ]);
        }
    
        // Springer-Verlag should come before Springer, so that if string contains Springer-Verlag, that is found
        $publishers = ['Academic Press', 'Cambridge University Press', 'Chapman & Hall', 'Edward Elgar', 'Elsevier',
            'Harvard University Press', 'JAI Press', 'McGraw-Hill', 'MIT Press', 'Norton', 'Oxford University Press',
            'Prentice Hall', 'Princeton University Press', 'Princeton Univ. Press', 'Routledge', 'Springer-Verlag',
            'Springer', 'University of Pennsylvania Press', 'University of Pittsburgh Press',
            'Van Nostrand Reinhold', 'Wiley', 'Yale University Press'];
        
        foreach ($publishers as $name) {
            Publisher::create([
                'name' => $name
            ]);
        }

        // Proper nouns, which need an initial capital letter (to be inluded in braces in BibTeX file)
        $names = ['American', 'Arrovian', 'Aumann', 'Bayes', 'Bayesian', 'Cournot', 'Gauss', 'Gaussian', 'German',
            'Groves', 'Indian', 'Ledyard', 'Lindahl', 'Markov', 'Markovian', 'Nash', 'Savage', 'U.S.', 'Walras', 'Walrasian'];

        foreach ($names as $name) {
            Name::create([
                'name' => $name
            ]);
        }

        // $itemTypes = ['article', 'book', 'incollection', 'inproceedings', 'mastersthesis', 'online', 'phdthesis',
        //     'techreport', 'unpublished'];

        // foreach ($itemTypes as $name) {
        //     ItemType::create([
        //         'name' => $name
        //     ]);
        // }

        $itemFields = ['address', 'annote', 'archiveprefix', 'author', 'booktitle', 'chapter', 'doi', 'edition', 'editor',
            'howpublished', 'eprint', 'institution', 'isbn', 'journal', 'key', 'month', 'note', 'number', 'oclc',
            'organization', 'pages', 'publisher', 'school', 'series', 'title', 'type', 'url', 'urldate', 'volume', 'year'];

        foreach ($itemFields as $name) {
            ItemField::create([
                'name' => $name
            ]);
        }

        $itemTypeItemFields = [
            'article' => ['author', 'title', 'journal', 'year', 'month', 'volume', 'number', 'pages', 'note', 'doi',
                 'url', 'archiveprefix', 'eprint'],
            'book' => ['author', 'editor', 'title', 'edition', 'volume', 'series', 'year', 'address', 'publisher',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint', 'isbn', 'oclc'],
            'techreport' => ['author', 'title', 'year', 'month', 'number', 'institution', 'note', 'type', 'url',
                 'archiveprefix', 'eprint'],
            'incollection' => ['author', 'title', 'booktitle', 'volume', 'series', 'year', 'publisher', 'address', 'editor', 'pages',
                 'note', 'doi', 'url', 'archiveprefix', 'eprint'],
            'inproceedings' => ['author', 'title', 'booktitle', 'year', 'month', 'publisher', 'address', 'editor',
                 'pages', 'note', 'archiveprefix', 'eprint'],
            'unpublished' => ['author', 'title', 'year', 'month', 'note', 'url', 'archiveprefix', 'eprint'],
            'mastersthesis' => ['author', 'title', 'school', 'year', 'month', 'note'],
            'phdthesis' => ['author', 'title', 'school', 'year', 'month', 'note'],
            'online' => ['author', 'title', 'year', 'url', 'urldate'],
        ];

        foreach($itemTypeItemFields as $name => $fields) {
            ItemType::create([
                'name' => $name,
                'fields' => $fields
            ]);
        }

        // foreach ($itemTypes as $name) {
        //     $itemType = ItemType::where('name', $name)->first();
        //     foreach ($itemTypeItemFields[$name] as $fieldName) {
        //         $itemField = ItemField::where('name', $fieldName)->first();
        //         $itemType->itemFields()->attach($itemField->id);
        //     }
        // }
    }
}
