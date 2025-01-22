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
                if (isset($convertedItem['label']) && $convertedItem['label'] && ! $conversion->override_labels) {
                    $baseLabel = $convertedItem['label'];
                } else {
                    $baseLabel = $this->makeLabel($convertedItem['item'], $conversion);
                }

                $label = $baseLabel;
                // if $baseLabel already used, add a suffix to it
                if (in_array($baseLabel, $baseLabels)) {
                    $values = array_count_values($baseLabels);
                    $label .= '-' . $values[$baseLabel];
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
                if ($conversion->language == 'my') {
                    $authorLetters = $author;
                } else {
                    //$authorLetters = $this->onlyLetters(Str::ascii($author));
                    $authorLetters = $this->onlyLetters($author);
                }
                // Position of (first) comma
                $commaPos = mb_strpos($authorLetters, ',');
                // Position of last space
                $spacePos = mb_strrpos($authorLetters, ' ');
                if ($commaPos !== false || $spacePos === false) {
                    if ($conversion->label_style == 'short') {
                        $label .= mb_substr($authorLetters, 0, 1) ?? '';
                    } elseif ($conversion->label_style == 'long-kebab') {
                        $label .= mb_strtolower(mb_substr($authorLetters, 0, $commaPos)) . '-';
                    } else {
                        $label .= mb_substr($authorLetters, 0, $commaPos);
                    }
                } else {
                    if ($conversion->label_style == 'short') {
                        // Take first letter after first space ('John Smith' => 'S')
                        $label .= mb_substr($authorLetters, $spacePos + 1, 1);
                    } elseif ($conversion->label_style == 'long-kebab') {
                        $label .= mb_strtolower(trim(mb_substr($authorLetters, $spacePos + 1), ' ')) . '-';
                    } else {
                        // Take letters after first space ('John Smith' => 'Smith')
                        $label .= trim(mb_substr($authorLetters, $spacePos + 1), ' ');
                    }
                }
            }
        }

        if ($conversion->language == 'my') {
            $year = $this->translateFrom($item->year, 'my');
        } else {
            $year = $item->year ?? '??';
        }

        if ($conversion->label_style == 'short') {
            $label = mb_strtolower($label) . mb_substr($year, 2, 2);
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
            $label .= $year;
            $title = $item->title ?? '';
            if (Str::startsWith($title, ['A ', 'The ', 'On ', 'An '])) {
                $title = Str::after($title, ' ');   
            }
            
            $firstTitleWord = Str::before($title, ' ');

            $label .= mb_strtolower($this->onlyLetters($firstTitleWord));
        } else {
            $label .= $year;
        }

        $label = str_replace(' ', '', $label);
        $label = substr(trim($label), 0, 255);

        return $label;
    }

    // Returns string consisting only of letters and spaces and commas in $string
    public function onlyLetters(string $string): string
    {
        if ($string == null) {
            $string = '';
        }
        return preg_replace("/[^\p{L},\s]+/u", "", $string);
    }

    public function translateFrom($string, $language)
    {
        if ($language == 'my') {
            // Burmese numerals
            $string = str_replace("\xE1\x81\x80", "0", $string);
            $string = str_replace("\xE1\x81\x81", "1", $string);
            $string = str_replace("\xE1\x81\x82", "2", $string);
            $string = str_replace("\xE1\x81\x83", "3", $string);
            $string = str_replace("\xE1\x81\x84", "4", $string);
            $string = str_replace("\xE1\x81\x85", "5", $string);
            $string = str_replace("\xE1\x81\x86", "6", $string);
            $string = str_replace("\xE1\x81\x87", "7", $string);
            $string = str_replace("\xE1\x81\x88", "8", $string);
            $string = str_replace("\xE1\x81\x89", "9", $string);
        }

        return $string;
    }
    
}