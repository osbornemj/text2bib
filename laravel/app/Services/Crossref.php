<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Traits\Months;

class Crossref
{
    use Months;

    public function __construct()
    {
        //
    }

    public function getCrossrefItemFromDoi(string $encodedDoi): string|null
    {
        $response = Http::withHeaders([
                'User-Agent' => 'text2bib (https://text2bib.org); mailto:' . env('CROSSREF_EMAIL'),
            ])
            ->accept('application/x-bibtex')
            ->get('https://api.crossref.org/works/' . $encodedDoi . '/transform');

        if ($response->status() == '200') {
            return $response->body();
        } else {
            return null;
        }
    }

    public function getCrossrefItemFromAuthorTitleYear(string $author, string $title, string $year, string $journal, string $publisher): string|null
    {
        $response = Http::withHeaders([
                'User-Agent' => 'text2bib (https://text2bib.org); mailto:' . env('CROSSREF_EMAIL'),
            ])
            ->get('https://api.crossref.org/works?query.bibliographic="' . $title . ', ' . $author . ' ' . $year . ' ' . $journal . ' ' . $publisher . '"&select=DOI&rows=1');

            if ($response->status() == '200') {
                return $response->body();
            } else {
                return null;
            }
    }

    public function parseCrossrefBibtex($crossrefItem) {
        if (preg_match('/^@(?P<itemType>[A-Za-z]+){(?P<label>[^,]+), (?P<fields>.*)$/', $crossrefItem, $matches)) {
            $remainder = $matches['fields'];
            $crossref_fields = [];
            $j = 0;
            while (trim($remainder) != '}' && $j < 20) {
                $j++;
                $pos = strpos($remainder, '=');
                $name = strtolower(substr($remainder, 0, $pos));
                if ($name == 'month') {
                    $content = substr($remainder, $pos + 1, 3);
                    $crossref_fields['month'] = in_array($content, array_keys($this->bibtexMonths)) ? $this->bibtexMonths[$content] : $content;
                    $remainder = trim(substr($remainder, $pos + 5));
                } else {
                    preg_match('/^\{(?P<content>[^}]+)\},?(?P<remainder>.*)/', substr($remainder, $pos + 1), $contentMatches);
                    $content = $contentMatches['content'] ?? '';
                    if ($name == 'pages') {
                        $content = str_replace('–', '-', $content);
                    }
                    $crossref_fields[$name] = $content ?: substr($remainder, $pos + 1);
                    $remainder = $contentMatches['remainder'] ?? null;
                    $remainder = trim($remainder);
                }
            }

            return [
                'crossref_item_type' => $matches['itemType'],
                'crossref_item_label' => $matches['label'],
                'crossref_fields' => (object) $crossref_fields
            ];
        } else {
            return null;
        }
    }

    /*
    public function getCrossrefItemFromDoi(string $doi, string $use): object
    {
        $response = Http::withHeaders([
                'User-Agent' => 'text2bib (https://text2bib.org); mailto:' . env('CROSSREF_EMAIL'),
            ])
            ->acceptJson()
            ->get('https://api.crossref.org/works/' . $doi);

        $body = json_decode($response->body());

        if ($body) {
            $details = $body->message;
            $crossref_item = new \stdClass();
            $crossref_item->doi = $use == 'latex' ? str_replace('_', '\_', $details->DOI) : $details->DOI;
            $crossref_item->itemType = match ($details->type) {
                'journal-article' => 'article',
                'book-chapter' => 'incollection',
                'book' => 'book',
            };
            $crossref_item->title = $details->title[0];
            $crossref_item->author = '';
            foreach ($details->author as $j => $author) {
                $crossref_item->author .= ($j ? ' and ' : '') . $author->family . ', ' . $author->given;
            }
            $crossref_item->year = $details->{'published-print'}->{'date-parts'}[0][0];

            switch ($crossref_item->itemType) {
                case 'article':
                    $crossref_item->journal = $details->{'container-title'}[0];
                    $crossref_item->pages = $details->page;
                    $crossref_item->number = $details->{'journal-issue'}->issue;
                    $crossref_item->volume = $details->volume;
                    break;
                case 'incollection':
                    $crossref_item->booktitle = $details->{'container-title'}[1];
                    $crossref_item->address = $details->{'publisher-location'};
                    $crossref_item->publisher = $details->publisher;
                    $crossref_item->pages = $details->page;
                    $crossref_item->isbn = '';
                    foreach ($details->{'isbn-type'} as $j => $isbntype) {
                        $crossref_item->isbn .= ($j ? ', ' : '') . $isbntype->value . ' (' . $isbntype->type .')';
                    }
                    break;
            }
        }

        return $crossref_item ?? null;
    }
    */
}