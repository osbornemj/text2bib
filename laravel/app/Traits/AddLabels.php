<?php
namespace App\Traits;

use Str;

use App\Models\Conversion;

trait AddLabels
{
    public function addLabels(array $convertedItems, Conversion $conversion): array
    {
        $baseLabels = [];
        foreach ($convertedItems as $key => $convertedItem) {
            if (isset($convertedItem['item'])) {
                if (isset($convertedItem['label']) && $convertedItem['label'] && !$conversion->override_labels) {
                    $baseLabel = $convertedItem['label'];
                } else {
                    $baseLabel = $this->makeLabel($convertedItem['item'], $conversion);
                }

                $label = $baseLabel;
                // if $baseLabel already used, add a suffix to it
                if (in_array($baseLabel, $baseLabels)) {
                    $values = array_count_values($baseLabels);
                    $label .= chr(96 + $values[$baseLabel]);
                }

                $baseLabels[] = $baseLabel;
                $convertedItems[$key]['label'] = $label;
            } else {
                $convertedItems[$key]['label'] = '';
            }
        }

        return $convertedItems;
    }

    public function makeLabel(object $item, Conversion $conversion): string
    {
        $label = '';
        if (isset($item->author) && $item->author) {
            $authors = explode(" and ", $item->author);
        } elseif (isset($item->editor)) {
            $authors = explode(" and ", $item->editor);
        } else {
            $authors = [];
        }
        if (isset($authors) && $authors) {
            foreach ($authors as $author) {
                $authorLetters = $this->onlyLetters(Str::ascii($author));
                if ($pos = strpos($author, ',')) {
                    if ($conversion->label_style == 'short') {
                        $label .= $authorLetters[0] ?? '';
                    } else {
                        $label .= substr($authorLetters, 0, $pos);
                    }
                } else {
                    if ($conversion->label_style == 'short') {
                        $label .= substr(trim(strrchr($authorLetters, ' '), ' '), 0, 1);
                    } else {
                        $label .= trim(strrchr($authorLetters, ' '), ' ');
                    }
                }
            }
        }

        if ($conversion->label_style == 'short') {
            $label = mb_strtolower($label) . (isset($item->year) ? substr($item->year, 2) : '');
        } elseif ($conversion->label_style == 'gs') {
            $firstAuthor = count($authors) ? $authors[0] : 'none';

            if (strpos($firstAuthor, ',') === false) {
                // assume last name is last segment
                if (strpos($firstAuthor, ' ') === false) {
                    $label = mb_strtolower($firstAuthor);
                } else {
                    $r = strrpos($firstAuthor, ' ');
                    $label = mb_strtolower(substr($firstAuthor, $r+1));
                }
            } else {
                // last name is segment up to comma
                $label = mb_strtolower(substr($firstAuthor, 0, strpos($firstAuthor, ',')));
            }
            $label .= isset($item->year) ? $item->year : '';
            $title = $item->title;
            if (Str::startsWith($title, ['A ', 'The ', 'On ', 'An '])) {
                $title = Str::after($title, ' ');   
            }
            
            $firstTitleWord = Str::before($title, ' ');

            $label .= mb_strtolower($this->onlyLetters($firstTitleWord));
        } else {
            $label .= isset($item->year) ? $item->year : '';
        }

        $label = trim($label);

        return $label;
    }

    // Returns string consisting only of letters and spaces in $string
    public function onlyLetters(string $string): string
    {
        return preg_replace("/[^a-z\s]+/i", "", $string);
    }
}