<?php
namespace App\Services;

use Str;

use PhpSpellcheck\Spellchecker\Aspell;

use App\Traits\AuthorPatterns;
use App\Traits\Stopwords;
use App\Traits\Utilities;

use App\Models\VonName;

class AuthorParser
{
    var $andWords;
    var $authorDetails;
    var $nameSuffixes;
    var $vonNames;

    private Dates $dates;

    use AuthorPatterns;
    use Stopwords;
    use Utilities;

    public function __construct()
    {
        $this->dates = new Dates();
        $this->vonNames = VonName::all()->pluck('name')->toArray();

        $this->nameSuffixes = ['Jr', 'Sr', 'III'];

        $this->andWords = [
            'and', 
            '/', 
            '\&', 
            '&', 
            '$\&$', 
            'dan', // Indonesian
            'e',   // Portuguese, Italian
            'en',  // Dutch
            'et',  // French
            'i',   // Polish
            'şi',  // Romanian
            'und', // German
            've',  // Turkish
            'y',   // Spanish
            'и',   // Russian
        ];
    }

    // Overrides method in Utilities trait
    private function verbose(string|array $arg): void
    {
        $this->authorDetails[] = $arg;
    }

    public function addToAuthorString(int $i, string &$string, string $addition): void
    {
        $string .= $addition;
        $this->verbose(['addition' => "(" . $i . ") Added \"" . $addition . "\" to list of authors"]);
    }

    /**
     * convertToAuthors: determine the authors in the initial elements of an array of words
     * After making a change, check that all examples are still converted correctly.
     * @param array $words array of words
     * @param string|null $remainder: string remaining after authors removed
     * @param string|null $year
     * @param string|null $month
     * @param string|null $day
     * @param string|null $date
     * @param boolean $determineEnd: if true, figure out where authors end; otherwise take whole string to be authors
     * @param string $type: 'authors' or 'editors'
     * @return array, with author string and warnings
     */
    public function convertToAuthors(array $words, string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, array $cities, array $dictionaryNames, bool $determineEnd = true, string $type = 'authors', string $language = 'en'): array
    {
        // if author list is in \textsc, remove the \textsc
        if (isset($words[0]) && Str::startsWith($words[0], '\textsc{')) {
            $words[0] = substr($words[0], 8);
        }
        $remainingWords = $words;
        if ($type == 'authors') {
            $remainder = implode(' ', $remainingWords);
        }

        ////////////////////////////////////
        // Check for some common patterns //
        ////////////////////////////////////

        $result = $this->checkAuthorPatterns($remainder, $year, $month, $day, $date, $isEditor, $language);

        if ($result) {
            return [
                'authorstring' => $result['authorstring'],
                'warnings' => [],
                'organization' => false,
                'author_pattern' => $result['author_pattern'],
                'author_details' => $this->authorDetails,
            ];
        }

        $this->verbose('Authors do not match any pattern');

        /////////////////////////////////
        // Check for organization name //
        /////////////////////////////////

        $result = $this->checkAuthorOrganization($words, $remainder, $year, $month, $day, $date, $isEditor, $dictionaryNames, $language);

        if ($result) {
            return [
                'authorstring' => $result['authorstring'],
                'warnings' => [],
                'organization' => true,
                'author_pattern' => $result['author_pattern'],
                'author_details' => $this->authorDetails,
            ];
        }

        $this->verbose('Author is not an organization');

        /////////////////////////////////////////
        // Otherwise check words incrementally //
        /////////////////////////////////////////

        $result = $this->checkAuthorsIncrementally($words, $remainder, $year, $month, $day, $date, $isEditor, $cities, $dictionaryNames, $determineEnd, $type, $language);

        return [
            'authorstring' => $result['authorstring'],
            'warnings' => $result['warnings'],
            'organization' => false,
            'author_pattern' => null,
            'author_details' => $this->authorDetails,
        ];
    }
 
    /**
     * Determine whether the author string matches any of the patterns in the AuthorPatterns trait.
     */
    public function checkAuthorPatterns(string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, string $language): array|null
    {
        $authorRegExps = $this->authorPatterns();
        $authorstring = '';

        foreach ($authorRegExps as $i => $r) {
            $name1 = $r['name1'];
            $name2 = $r['name2'] ?? $name1;
            $initials = $r['initials'] ?? false;

            $regExp = '%^(?P<firstAuthor>' . $name1 . ')' . $r['end1']; 
            if ($r['end2']) {
                $regExp .= '(?P<middleAuthors>(' . $name2 . $r['end1'] . ')*)';
                $regExp .= '(?P<penultimateAuthor>' . $name2 . ')' . $r['end2'];
            }
            if ($r['end3']) {
                $regExp .= '(?P<lastAuthor>' . $name2 . ')' . $r['end3'];
            }
            $regExp .= '(?P<remainder>.*)%u';

            if (preg_match($regExp, $remainder, $matches)) {
                $authorstring .= $this->formatAuthor($matches['firstAuthor'], $initials);

                if (isset($matches['middleAuthors'])) {
                    // process middle authors
                    $subremainder = $matches['middleAuthors'];
                    $result = 1;
                    while ($result == 1) {
                        $result = preg_match('%^(?P<author>'. $name2 . ')' . $r['end1'] . '(?P<remainder>.*)$%u', $subremainder, $submatches);
                        if (isset($submatches['author'])) {
                            $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($submatches['author'], $initials);
                            $subremainder = $submatches['remainder'];
                        }
                    }
                }

                if (isset($matches['penultimateAuthor'])) {
                    $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($matches['penultimateAuthor'], $initials);
                }
                if (isset($matches['lastAuthor'])) {
                    $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($matches['lastAuthor'], $initials);
                }

                if (preg_match('%^et.? al.?(?P<remainder>.*)$%', $matches['remainder'], $endmatches)) {
                    $authorstring .= ' and others';
                    $remainder = $endmatches['remainder'];
                } else {
                    if (! empty($r['etal'])) {
                        $authorstring .= ' and others';
                    }
                    $remainder = $matches['remainder'];
                }

                $this->verbose('Authors match pattern ' . $i);

                break;
            }
        }
        
        if ($authorstring) {
            $year = $this->dates->getDate(trim($remainder), $remainder, $month, $day, $date, true, true, true, $language);
            if (preg_match('%^(?P<firstWord>[^ ]+) (?P<remains>.*)$%', $remainder, $matches)) {
                if ($this->isEd($matches['firstWord'])) {
                    $isEditor = true;
                    $remainder = $matches['remains'];
                    if ($year = $this->dates->getDate($remainder, $remains, $month, $day, $date, true, true, true, $language)) {
                        $remainder = $remains;
                    }
                }
            }
            return [
                'authorstring' => $authorstring,
                'author_pattern' => $i,
            ];
        } else {
            return null;
        }
    }

    /**
     * Determine whether the authorstring is the name of an organization.
     */
    public function checkAuthorOrganization(array $words, string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, array $dictionaryNames, string $language): array|null
    {
        /*
         * Between 3 and 80 letters and spaces (no punctuation) followed by year in parens or brackets
         * Author strings without spaces, like 'John Doe and Jane Doe' or 'Doe J and Doe K' should have been
         * taken care of by author patterns (above).
         */
        preg_match('/^(?P<name>[\p{L} ]{3,80})(?P<remains>[\(\[]?[1-9][0-9]{3}.*$)/u', $remainder, $matches);
        if (! empty($matches)) {
            $remainder = $matches['remains'];
            $year = $this->dates->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
            $this->verbose('Authors match pattern 128');
            return [
                'authorstring' => trim($matches['name']),
                'author_pattern' => 128,
            ];
        }

        /*
         * If organization name does not match previous pattern:
         * If first 3-6 words are all letters and in the dictionary except possibly last one, which is letters with a period at the end
         * then they make up the name of an organization
         * Coded before author-pattern check moved first, so may be unnecessary now:
         * Dictionary check is to exclude strings like 'John Doe and Jane Doe' or 'Doe J and Doe K', which needs processing
         * as names, to insert commas after the last names.  A word that is not in the dictionary and could not be part
         * of a name, like 'American', is also possible.
         */
        $name = '';
        foreach ($words as $i => $word) {
            if ($this->isInitials($word)) {
                break;
            } elseif ($i == 0 && strlen($word) > 3 && substr($word, -1) == '.') {
                $remainder = implode(' ', array_slice($words, 1));
                $year = $this->dates->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                return [
                    'authorstring' => substr($word, 0, -1),
                    'author_pattern' => null,
                ];
            } elseif (ctype_alpha((string) $word) && ($this->inDict($word, $dictionaryNames) || in_array($word, ['American']))) {
                $name .= ($i ? ' ' : '') . $word;
            } else {
                $xword = substr($word, 0, -1);
                // possibly last character could be other punctuation?
                if (ctype_alpha((string) $xword) && $this->inDict($xword, $dictionaryNames) && in_array(substr($word, -1), ['.'])) {
                    if ($i >= 2 && $i <= 5) {
                        $remainder = implode(' ', array_slice($words, $i+1));
                        $year = $this->dates->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                        return [
                            'authorstring' => $name . ' ' . $xword,
                            'author_pattern' => null,
                        ];
                    } else {
                        break;
                    }
                } elseif ($i >= 3 && $i <= 6) {
                    $remainder = implode(' ', array_slice($words, $i));
                    $year = $this->dates->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                    return [
                        'authorstring' => $name,
                        'author_pattern' => null,
                    ];
                } else {
                    break;
                }        
            }
        }

        return null;
    }

    /**
     * Go through the author string word by word, separating it into author names.
     */
    public function checkAuthorsIncrementally(array $words, string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, array $cities, array $dictionaryNames, bool $determineEnd = true, string $type = 'authors', string $language = 'en')
    {
        $namePart = $authorIndex = $case = 0;
        $prevWordAnd = $prevWordVon = $done = $isEditor = $hasAnd = $multipleAuthors = false;
        $wordHasComma = $prevWordHasComma = false;
        $fullName = '';
        $warnings = [];
        $skip = false;

        $authorstring = '';

        $remainingWords = $words;

        foreach ($words as $i => $word) {
            if ($skip) {
                $skip = false;
                continue;
            }

            $wordEndsName = false;
            if (substr($word, -1) == ';') {
                $word = substr($word, 0, -1) . ',';
                $wordEndsName = true;
            }

            // Word is in vonNames or it is all uppercase and lowercased version of it is a lowercased vonName
            $wordIsVon = in_array($word, $this->vonNames)
                 || (preg_match('/^[A-Z]*$/', $word) && in_array(strtolower($word), array_map('strtolower', $this->vonNames)));

            // If word is all uppercase, with no trailing punctuation, and next word is not all uppercase,
            // and word is not a von name and is not "and"
            // then add a comma at the end
            // The idea is to interpret SMITH John to be SMITH, John.
            if (
                strlen($word) > 2 &&
                ! $wordIsVon &&
                preg_match('/^[A-Z]*$/', $word) &&
                isset($words[$i+1]) &&
                mb_strtoupper($words[$i+1]) != $words[$i+1] &&
                ! $this->isAnd(strtolower($word), $language)
               ) {
                $word = $word . ',';
            }

            $nameComplete = true;
            $prevWordHasComma = $wordHasComma;
            $wordHasComma = substr($word, -1) == ',';
            // Get letters in word, eliminating accents & other non-letters, to get accurate length
            $lettersOnlyWord = preg_replace("/[^A-Za-z]/", '', $word);

            // Deal with specific cases of no space at end of authors.  Note that case Smith~(1980) is
            // already handled.
            // These cases are very specific --- how to generalize?  
            // Smith},``Title... 
            if (Str::contains($word, ['},`'])) {
                $firstWord = strtok($word, '}');
                $remain = $remainingWords;
                array_shift($remain);
                $remainingWords = array_merge([$firstWord, ltrim(Str::after($word, '}'), ',.')], $remain);
                $word = $firstWord;
            // Smith(2001)
            } elseif (preg_match('/[a-zA-Z]\([19|20]/', $word)) {
                $firstWord = strtok($word, '(');
                $remain = $remainingWords;
                array_shift($remain);
                $remainingWords = array_merge([$firstWord, '(' . Str::after($word, '(')], $remain);
                $word = $firstWord;
            }

            if ($case == 12 
                    && ! in_array(rtrim($word, ',.'), $this->nameSuffixes) 
                    // Deal with names like Bruine de Bruin, W.
                    && isset($words[$i+1]) // must be at least 2 words left
                    && ! $this->isAnd($words[$i+1], $language) // next word is not 'and'
                )  {
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
            }

            if (isset($bareWords)) {
                $this->verbose("bareWords: " . implode(' ', $bareWords));
            }
            unset($bareWords);

            if (isset($reason)) {
                $this->verbose('Reason: ' . $reason);
                unset($reason);
            }

            if (in_array($case, [11, 12, 14]) && $done) {  // 14: et al.
                break;
            }

            if (! $done) {
                $this->verbose(['text' => 'Word ' . $i . ": ", 'words' => [$word], 'content' => " - authorIndex: " . $authorIndex . ", namePart: " . $namePart]);
                $this->verbose("fullName: " . $fullName);
            }

            if (isset($itemYear)) {
                $year = $itemYear;
            }
            if ($year) {
                $this->verbose("Year: " . $year);
            }

            // Remove first word from $remainingWords
            array_shift($remainingWords);

            if ($authorIndex > 0) {
                $multipleAuthors = true;
            }

            $edResult = $this->isEd($word);

            $nextWord = isset($words[$i+1]) ? rtrim($words[$i+1], ',;') : null;

            // Case of initials A. -B. (with space).
            if ($nextWord && preg_match('/^[A-Z]\./', $word) && preg_match('/^-[A-Z]\./', $nextWord)) {
                $word = $word . $nextWord;
                array_shift($remainingWords);
                $skip = true;
            }

            if (in_array($word, [" ", "{\\sc", "\\sc"])) {
                //
            } elseif (in_array($word, ['...', '…'])) {
                $this->verbose('[convertToAuthors 1]');
                $formattedAuthor = $this->formatAuthor(($fullName));
                if (Str::endsWith($authorstring, $formattedAuthor)) {
                    $this->addToAuthorString(3, $authorstring, ' and others');
                } else {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                }
                $fullName = '';
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
                $remainder = implode(" ", $remainingWords);
                $done = false;
            } elseif ($edResult) {
                $this->verbose('[convertToAuthors 2]');
                if ($edResult == 1 && $multipleAuthors) {
                   $warnings[] = "More than one editor has been identified, but string denoting editors (\"" . $word . "\") is singular";
                }
                $this->verbose("String for editors, so ending name string (word: " . $word .")");
                // Word is 'ed' or 'ed.' if $hasAnd is false, or 'eds' or 'eds.' if $hasAnd is true
                $isEditor = true;
                $remainder = implode(" ", $remainingWords);
                if ($namePart == 0) {
                    $warnings[] = "String for editors detected after only one part of name.";
                }
                // Check for year following editors
                if ($year = $this->dates->getDate($remainder, $remains, $trash1, $trash2, $trash3, true, false, true, $language)) {
                    $remainder = $remains;
                    $this->verbose("Year detected, so ending name string (word: " . $word .")");
                } else {
                    $this->verbose("String indicating editors (e.g. 'eds') detected, so ending name string");
                }
                // Under some conditions (von name?), $fullName has already been added to $authorstring.
                if (!Str::endsWith($authorstring, $fullName)) {
                    $this->addToAuthorString(1, $authorstring, $this->formatAuthor($fullName));
                }
                break;  // exit from foreach
            } elseif ($determineEnd && $done) {
                break;  // exit from foreach
            } elseif ($this->isAnd($word, $language) && ($word != 'et' || ! in_array($nextWord, ['al..', 'al.', 'al', 'al.:']))) {
                $this->verbose('[convertToAuthors 3]');
                // Word is 'and' or equivalent, and if it is "et" it is not followed by "al".
                $hasAnd = $prevWordAnd = true;
                $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName) . ' and');
                $fullName = '';
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
                $reason = 'Word is "and" or equivalent';
            } elseif (rtrim($word, '.') == 'others') {
                $this->verbose('[convertToAuthors 3a]');
                $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName) . ' others');
                $remainder = implode(" ", $remainingWords);
                $done = true;
            } elseif (in_array($word, ['et', 'et.'])) {
                // Word is 'et'
                $this->verbose('nextWord: ' . $nextWord);
                if (in_array($nextWord, ['al..', 'al.', 'al', 'al.:'])) {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                    array_shift($remainingWords);
                    if (count($remainingWords)) {
                        $remainder = implode(" ", $remainingWords);
                        $this->verbose('[c2a getDate 2]');
                        if (preg_match('/^(18|19|20)[0-9]{2}$/', $remainingWords[0])) {
                            // If first remaining word is a year **with no punctuation**, assume it starts title
                        } else {
                            $year = $this->dates->getDate($remainder, $remains, $trash1, $trash2, $trash3, true, true, true, $language);
                            $remainder = trim($remains);
                        }
                    }
                    $done = true;
                    $case = 14;
                    $reason = 'Word is "et" and next word is "al." or "al"';
                }
            } elseif ($type == 'editors' && isset($word[0]) && $word[0] == '(' && $remainder && $this->isAddressPublisher($remainder, finish: false)) {
                $this->addToAuthorString(17, $authorstring, $this->formatAuthor($fullName));
                $done = true;
            } elseif ($determineEnd && substr($word, -1) == ':') {
                if (
                        ! preg_match('/^[A-Z]\.:$/', $word)
                        && $namePart >= 2 
                        && isset($words[$i-1]) 
                        && in_array(substr($words[$i-1], -1), ['.', ',', ';']) 
                        && (! $this->isInitials($words[$i-1]) || (isset($words[$i-2]) && in_array(substr($words[$i-2], -1), [',', ';'])))
                    ) {
                    $this->verbose('Word ends in colon and is not initial with period followed by colon, $namePart is at least 2, and previous word ends in comma or period, and either previous word is not initials or word before it ends in comma, so assuming word is first word of title.');
                    $this->addToAuthorString(16, $authorstring, $this->formatAuthor($fullName));
                    $remainder = $word . ' ' . implode(" ", $remainingWords);
                    $done = true;
                } elseif (
                        $type == 'editors'
                        && isset($words[$i-1])
                        && in_array(substr($words[$i-1], -1), ['.', ',', ';'])
                    ) { 
                    $this->verbose('Looking for editor, word ends in colon, and previous word ends in comma or period, so assuming word is start of publication info (address of publisher).');
                    $this->addToAuthorString(16, $authorstring, $this->formatAuthor($fullName));
                    $remainder = $word . ' ' . implode(" ", $remainingWords);
                    $done = true;
                } else {
                    $this->verbose('[convertToAuthors 5]');
                    $nameComponent = $word;
                    $fullName .= ' '. trim($nameComponent, '.');
                    $this->addToAuthorString(17, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $reason = 'Word ends in colon';
                    $done = true;
                }
            } elseif ($determineEnd && substr($word, -1) == '.' && strlen($lettersOnlyWord) > 3
                    && mb_strtolower(substr($word, -2, 1)) == substr($word, -2, 1)) {
                // If $determineEnd and word ends in period and word has > 3 chars (hence not "St.") and previous letter
                // is lowercase (hence not string of initials without spaces):
                if ($namePart == 0) {
                    // If $namePart == 0, something is wrong (need to choose an earlier end for name string) UNLESS author has
                    // a single name.  If string is followed by year or quotedOrItalic, that seems right,
                    // in which case we may have an entry like "Economist. 2005. ..."
                    $remainder = implode(" ", $remainingWords);
                    $this->verbose('[c2a getDate 3]');
                    if ($year = $this->dates->getDate($remainder, $remainder, $trash1, $trash2, $trash3, true, false, true, $language)) {
                        $this->verbose('[convertToAuthors 6]');
                        // Don't use spaceOutInitials in this case, because string is not initials.  Could be
                        // something like autdiogames.net, in which case don't want to put space before period.
                        $nameComponent = $word;
                        $fullName .= trim($nameComponent, '.');
                        if (isset($fullName[0]) && $fullName[0] != ' ') {
                            $fullName = ' ' . $fullName;
                        }
                        $this->addToAuthorString(4, $authorstring, $fullName);
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string starts with year';
                        $itemYear = $year; // because $year is recalculated below
                        $done = true;
                    } elseif ($this->getQuotedOrItalic($remainder, true, false, $before, $after, $style)) {
                        $this->verbose('[convertToAuthors 7]');
                        $nameComponent = $word;
                        $fullName .= ($fullName ? ' and ' : '') . trim($nameComponent, '.');
                        $this->addToAuthorString(18, $authorstring, $fullName);
                        $reason = 'Word ends in period, namePart is 0, and remaining string starts with quoted or italic';
                        $done = true;
                    } else {
                        $this->verbose('[convertToAuthors 8]');
                        // Case like: "Arrow, K. J., Hurwicz. L., and ..." [note period at end of Hurwicz]
                        // "Hurwicz" is current word, which is added back to the remainder.
                        // (Ideally the script should realize the typo and still get the rest of the authors.)
                        // If remainder starts with quotes or italic, assume author has just one name
                        $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo?  Processed with comma instead of period.";
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string does not start with year';
                        $word = substr($word, 0, -1) . ',';
                        goto namePart0;
                    }
                } elseif (! isset($remainingWords[0]) || ! $this->isInitials(trim($remainingWords[0], ', '))) {
                    // If $namePart > 0
                    $this->verbose('[convertToAuthors 9]');
                    $nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, '.')));
                    $fullName .= " " . $nameComponent;
                    $this->addToAuthorString(5, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, and namePart is > 0';
                } elseif (
                        isset($remainingWords[0])
                        && isset($remainingWords[1])
                        && (
                            (substr(trim($remainingWords[0], ', '), -1) == '.' && $this->isInitials(trim($remainingWords[0], ', ')))
                            ||
                             $this->isAnd($remainingWords[0])
                           )
                        ) {
                    // case like "Richerson, Peter. J., and ..."
                    $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo?  Period was ignored.";
                    $word = substr($word, 0, -1);
                    goto namePart0;
                } else {
                    // period at end of list of authors
                    $this->verbose('[convertToAuthors 7a]');
                    $nameComponent = $word;
                    $fullName .= " " . trim($nameComponent, '.');
                    $this->addToAuthorString(18, $authorstring, $fullName);
                    $reason = 'Word ends in period, namePart is 1, and next word is not initials or "and", so ending author list.';
                    $done = true;
                    $replaced = false;
                    foreach ($this->andWords as $andWord) {
                        if (Str::startsWith($remainder, $andWord . ' ')) {
                            // can't make replacement based on $fullName, because case might differ from case in $remainder
                            // (e.g. last name all-uppercase vs. formatted with uppercase first letter and rest lowercase)
                            $remainder = substr($remainder, strlen($andWord) + 1 + strlen(trim($fullName)));
                            $replaced = true;
                            break;
                        }
                    }
                    if (! $replaced) {
                        //$remainder = Str::replaceStart(trim($fullName), '', $remainder);
                        $remainder = substr($remainder, strlen(trim($fullName)));
                    }
                }
                $this->verbose("Remainder: " . $remainder);
                $this->verbose('[c2a getDate 4]');
                $this->verbose('[convertToAuthors 10]');
                if (! isset($year) || ! $year) {
                    if ($year = $this->dates->getDate($remainder, $remains, $month, $day, $trash3, true, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Remains: " . $remains);
                    }
                }
                $done = true;
            } elseif (
                // next word starts with uppercase letter and next word but one starts with lowercase letter, so cannot start title.
                // So $words[$i+1] is first word of title.  So terminate name string now.
                $type == 'authors'
                && $this->isInitials($word)
                && isset($words[$i+1], $language)
                && ! in_array($words[$i+1], ['et', 'et.', 'al', 'al.'])
                && (! isset($words[$i+2]) || ! $this->isAnd($words[$i+2]))
                && isset($words[$i+2])
                && isset($words[$i+2][0])
                && ! preg_match('/^eds?/', $words[$i+2])
                && preg_match('/[A-Z]/', $words[$i+1][0])
                && preg_match('/[a-z]/', $words[$i+2][0])
                && (
                    ! $this->isAnd($words[$i+2], $language)
                    ||
                    (isset($words[$i+3][0]) && preg_match('/[a-z]/', $words[$i+3][0]) && ! in_array($words[$i+3], $this->vonNames))
                   )
                && (
                    ! in_array($words[$i+2], $this->vonNames)
                    ||
                    (isset($words[$i+3][0]) && preg_match('/[a-z]/', $words[$i+3][0]) && ! in_array($words[$i+3], $this->vonNames))
                   )
                && ! in_array($words[$i+2], ['et', 'et.', 'al', 'al.'])
                && (! isset($words[$i+3]) || ! preg_match('/^[\(\[]?(19|20)[0-9]{2}[\)\]]?$/', trim($words[$i+3], '.')))
                ) {
                $this->verbose('[convertToAuthors 14a]');
                $fullName .= ' ' . $word;
                $remainder = implode(" ", $remainingWords);
                $done = true;
                $this->addToAuthorString(29, $authorstring, $this->formatAuthor($fullName));
            } elseif ($namePart == 0) {
                namePart0:
                if (isset($words[$i+1]) && $this->isAnd($words[$i+1], $language)) {
                    $this->verbose('[convertToAuthors 11]');
                    // Next word is 'and' or equivalent.  Happens if $namePart is 0 because of von name.  Seems impossible to tell
                    // whether in "Werner de la ..." the "Werner" is the first name or part of the last name, to be followed by
                    // a first name later (without looking ahead --- attempted in convertToAuthors 6a below).
                    // On encounting a von name, namepart is not incremented, which is
                    // wrong if Werner is a first name.
                    $hasAnd = $prevWordAnd = true;
                    $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName . ' ' . $word));
                    $fullName = '';
                    $namePart = 0;
                    $this->verbose("\$namePart set to 0");
                    $authorIndex++;
                    $reason = 'Word is "and" or equivalent';
                // Check if $word and first word of $remainingWords are plausibly a name.  If not, end search if $determineEnd.
                } elseif ($determineEnd && isset($remainingWords[0]) && $this->isNotName($word, $remainingWords[0])) {
                    $this->verbose('[convertToAuthors 12]');
                    $fullName .= ' ' . $word;
                    $remainder = implode(" ", $remainingWords);
                    $this->addToAuthorString(6, $authorstring, ' ' . ltrim($this->formatAuthor($fullName)));
                    $origRemainder = $remainder;
                    $origRemainingWords = $remainingWords;
                    if ($type == 'authors' && $this->isEd($remainingWords[0])) {
                        $isEditor = true;
                        array_shift($remainingWords);
                        $remainder = implode(" ", $remainingWords);
                        $this->verbose("Editors detected");
                    }
                    if ($year = $this->dates->getDate($remainder, $remains, $month, $day, $date, true, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Year detected");
                    }
                    if (! $year) {
                        // eds will be detected on next round
                        $remainder = $origRemainder;
                        $remainingWords = $origRemainingWords;
                    }
                    $done = true;
                } elseif ($this->isInitials($word) && isset($words[$i+1]) && 
                        (
                            ($this->isInitials($words[$i+1]) && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language))
                            ||
                            (in_array(substr($words[$i+1],-1), [',', ';']) && $this->isInitials(substr($words[$i+1],0,-1)))
                        )
                    ) {
                    $this->verbose('[convertToAuthors 13]');
                    $fullName .= ' ' . $word;
                // $nextWord not initials, to rule out case like GUO, J., where GUO gets classified as initials because it is all
                // uppercase and has only 3 letters.  Logically could make condition $namePart > 0, but $namePart is sometimes set to
                // 0 in the middle of a name.
                } elseif (substr($word,-1) == ',' && $this->isInitials(substr($word,0,-1)) && ! $this->isInitials(substr($nextWord, 0, -1))) {
                    $this->verbose('[convertToAuthors 14]');
                    $fullName .= ' ' . substr($word, 0, -1);
                    $namePart = 0;
                    $this->verbose("\$namePart set to 0");
                    $authorIndex++;
                } elseif (
                    // $word is publication city and either it ends in a colon or next word is two-letter state abbreviation followed by colon
                    $type == 'editors'
                    && in_array(trim($word, ',: '), $cities)
                    && 
                        (
                            substr($word, -1) == ':'
                            ||
                            (
                                isset($words[$i+1])
                                && preg_match('/^[A-Z]{2}:$/', $words[$i+1])
                            )
                        )
                ) {
                    $done = true;
                    $this->addToAuthorString(28, $authorstring, $this->formatAuthor($fullName));
                } else {
                    $this->verbose('[convertToAuthors 15]');
                    if (! $prevWordAnd && $authorIndex) {
                        $this->addToAuthorString(7, $authorstring, $this->formatAuthor($fullName) . ' and');
                        $fullName = '';
                        $prevWordAnd = true;
                    }
                    $name = $this->spaceOutInitials($word);
                    // If part of name is all uppercase and 3 or more letters long, convert it to ucfirst(mb_strtolower())
                    // For component with 1 or 2 letters, assume it's initials and leave it uc (to be processed by formatAuthor)
                    if (strlen($name) > 2 && strtoupper($name) == $name && strpos($name, '.') === false) {
                        $nameComponent = ucfirst(mb_strtolower($name));
                        // Simpler version of following code, without check for hyphen, produces strange result ---
                        // the *next* word has a period replaced by a comma
                        if (str_contains($nameComponent, '-')) {
                            $nameChars = mb_str_split($nameComponent);
                            $nameComponent = '';
                            foreach ($nameChars as $i => $char) {
                                $nameComponent .= ($i > 0 && $nameChars[$i-1] == '-') ? mb_strtoupper($char) : $char;
                            }
                        }
                    } else {
                        $nameComponent = $name;
                    }
                    $oldFullName = $fullName;
                    $fullName .= ' ' . $nameComponent;
                    if ($wordIsVon) {
                        $this->verbose('[convertToAuthors 16]');
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name");
                        if ($oldFullName && ! $prevWordVon) {
                            $this->verbose("convertToAuthors: incrementing \$namePart");
                            $namePart++;
                        }
                        $prevWordVon = true;
                    } else {
                        $prevWordVon = false;
                        if (isset($words[$i]) && ! Str::endsWith($words[$i], ',') 
                                && isset($words[$i+1]) 
                                && Str::endsWith($words[$i+1], [',', ';']) 
                                && ! $this->isInitials(substr($words[$i+1], 0, -1)) 
                                && isset($words[$i+2]) 
                                && Str::endsWith($words[$i+2], [',', ';']) 
                                && ! $this->dates->isYear(trim($words[$i+2], ',()[]'))
                            ) {
                            // $words[$i] does not end in a comma AND $words[$i+1] is set and ends in a comma and is not initials AND $words[$i+2]
                            // is set and ends in a comma AND $words[$i+2] is not a year.
                            // E.g. Ait Messaoudene, N., ...
                            $this->verbose('[convertToAuthors 17]');
                            $this->verbose("convertToAuthors: '" . $words[$i] . "' identified as first segment of last name, with '" . $words[$i+1] . "' as next segment");
                        } elseif (isset($words[$i+1]) && ! in_array($words[$i+1], $this->vonNames)) {
                            $this->verbose('[convertToAuthors 18]');
                            $this->verbose("convertToAuthors: incrementing \$namePart");
                            $namePart++;
                        }
                        // Following occurs in case of name that is a single string, like "IMF"
                        if ($year = $this->dates->getDate(implode(" ", $remainingWords), $remains, $trash1, $trash2, $trash3, true, true, true, $language)) {
                            $this->verbose('[c2a getDate 6]');
                            $this->verbose('[convertToAuthors 19]');
                            $remainder = $remains;
                            $done = true;
                        }
                        $case = 6;
                    }
                }
            } else {
                // namePart > 0 and word doesn't end in some character, then lowercase letter, then period
                $prevWordAnd = false;

                // 2023.8.2: trimRightBrace removed to deal with conversion of example containing name Oblo{\v z}insk{\' y}
                // However, it must have been included here for a reason, so probably it should be included under
                // some conditions.
                if (in_array(rtrim($word, '.,'), $this->nameSuffixes)) {
                    $this->verbose('[convertToAuthors 20]');
                    $fullName = $this->formatAuthor($fullName);
                    $nWords = explode(' ', trim($fullName, ' '));
                    $nameWords = [];
                    foreach ($nWords as $nWord) {
                        $nameWords[] = Str::endsWith($nWord, [',,']) ? substr($nWord, 0, -1): $nWord;
                    }
                    if (count($nameWords) == 1) {
                        $this->verbose('[convertToAuthors 21]');
                        $fullName = $nameWords[0] . ' ' . $word;
                        $nameComplete = false;
                    } else {
                        // Put Jr. or Sr. in right place for BibTeX: format is lastName, Jr., firstName OR lastName Jr., firstName.
                        // Assume last name is single word that is followed by a comma (which covers both
                        // firstName lastName, Jr. and lastName, firstName, Jr.
                        $this->verbose('[convertToAuthors 22]');
                        $fullName = ' ';
                        // Put Jr. after the last name
                        $k = -1;
                        foreach ($nameWords as $j => $nameWord) {
                            if (substr($nameWord, -1) == ',') {
                                $fullName .= $nameWord . ' ' . rtrim($word, ',') . ',';
                                $k = $j;
                                break;
                            }
                        }
                        if ($k >= 0) {
                            // Put the rest of the names after Jr.
                            foreach ($nameWords as $m => $nameWord) {
                                if ($m != $k) {
                                    $fullName .= ' ' . $nameWord;
                                }
                            }
                        }

                        // No comma at end of any of $nameWords
                        if ($k == -1) {
                            $n = count($nameWords);
                            $fullName .= $nameWords[$n-1] . ' '. rtrim($word, ',') . ',';
                            foreach ($nameWords as $i => $nameWord) {
                                if ($i < $n - 1) {
                                    $fullName .= ' ' . $nameWord;
                                }
                            }
                        }

                        $namePart = 0;
                        $this->verbose("\$namePart set to 0");
                        $authorIndex++;
                    }

                    if (! Str::endsWith($word, ',') && ! $this->isAnd($nextWord, $language)) {
                        $done = true;
                        $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                        $year = $this->dates->getDate(implode(" ", $remainingWords), $remainder, $month, $day, $date, true, true, true, $language);
                        break;
                    }

                    $this->verbose('Name with Jr., Sr., or III; fullName: ' . $fullName);
                } else {
                    $this->verbose('[convertToAuthors 23]');
                    // Don't rtrim '}' because it could be part of the name: e.g. Oblo{\v z}insk{\' y}.
                    // Don't trim comma from word before Jr. etc, because that is valuable info
                    $trimmedWord = (isset($words[$i+1]) && in_array(rtrim($words[$i+1], '.,)'), $this->nameSuffixes)) ? $word : rtrim($word, ',;');
                    $nameComponent = $this->spaceOutInitials($trimmedWord);
                    $nameComponent = preg_replace('/([A-Za-z][a-z])\.$/', '$1', $nameComponent);
                    $fullName .= " " . $nameComponent;
                }

                // $bareWords is array of words at start of $remainingWords that don't end end in ','
                // or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
                $bareWordsResult = $this->bareWords($remainingWords, false, $language);
                $bareWords = $bareWordsResult['barewords'];
                $wordAfterBareWords = $bareWordsResult['nextword'];
                // If 'and' has not already occurred ($hasAnd is false), its occurrence in $barewords is compatible
                // with $barewords being part of the authors' names OR being part of the title, so should be ignored.
                $nameScore = $this->nameScore($bareWords, ! $hasAnd, $dictionaryNames);
                $this->verbose("bareWords (no trailing punct, not year in parens): " . implode(' ', $bareWords));
                $this->verbose("nameScore: " . $nameScore['score'] . ". Count: " . $nameScore['count']);
                if ($nameScore['count']) {
                    $this->verbose('[convertToAuthors 24]');
                    $this->verbose('nameScore per word: ' . number_format($nameScore['score'] / $nameScore['count'], 2));
                }

                // If one of the bareWords starts with a lc letter and is not a vonName or and, set $bareWordStartsLc true.
                $bareWordStartsLc = false;
                foreach ($bareWords as $bareWord) {
                    if (
                        isset($bareWord[0])
                        && preg_match('/^\p{L}$/u', $bareWord[0])
                        && mb_strtolower($bareWord[0]) == $bareWord[0] 
                        && ! in_array($bareWord, $this->vonNames) 
                        && ! $this->isAnd($bareWord, $language)
                       ) {
                        $bareWordStartsLc = true;
                        break;
                    }
                }

                $wordsRemainingAfterNext = $remainingWords;
                array_shift($wordsRemainingAfterNext);
                $upcomingQuotedText = $this->getQuotedOrItalic(implode(" ", $wordsRemainingAfterNext), true, false, $before, $after, $style);

                if ($determineEnd && $text = $this->getQuotedOrItalic(implode(" ", $remainingWords), true, false, $before, $after, $style)) {
                    if (in_array($text, ['et al', 'et al.', 'et. al.', 'et al.:'])) {
                        $this->verbose('[convertToAuthors 25]');
                        $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                        if (isset($remainingWords[0]) && $remainingWords[0] == '{\em') {
                            array_shift($remainingWords);
                        }
                        array_shift($remainingWords);
                        array_shift($remainingWords);
                        $remainder = implode(" ", $remainingWords);
                        $done = true;
                        $case = 19;
                    } else {
                        $this->verbose('[convertToAuthors 26a]');
                        $remainder = implode(" ", $remainingWords);
                        $done = true;
                        $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                        $case = 7;
                    }
                } elseif ($determineEnd && $year = $this->dates->getDate(implode(" ", $remainingWords), $remainder, $month, $day, $date, true, true, true, $language)) {
                    $this->verbose('[convertToAuthors 14b] Ending author string: word is "'. $word . '", year is next.');
                    $done = true;
                    $fullName = ($fullName[0] != ' ' ? ' ' : '') . $fullName;
                    $this->addToAuthorString(10, $authorstring, $this->formatAuthor($fullName));
                } elseif ($determineEnd && $upcomingQuotedText && $upcomingQuotedText != 'et al.') {
                    $this->verbose('[convertToAuthors 26b]');
                    $remainder = implode(" ", $wordsRemainingAfterNext);
                    $done = true;
                    if (Str::endsWith($word, ',') && ! $prevWordHasComma) {
                        $fullName .= ',';
                    }
                    $fullName = $fullName . ' ' . rtrim($nextWord, ',. ');
                    $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                    $case = 7;
                } elseif (
                    // stop if ...
                        $determineEnd
                        &&
                        isset($remainingWords[0])
                        &&
                        ! $this->isAnd($remainingWords[0], $language)
                        && 
                        // don't stop if next word ends in a colon and is not in the dictionary and the following word
                        // starts with an uppercase letter
                        // (to catch case in which author list is terminated by a colon, which is ignored when computing
                        // bareWords (because colons often occur after the first or second word of a title))
                        (
                            substr($remainingWords[0], -1) != ':'
                            ||
                            $this->inDict($remainingWords[0], $dictionaryNames)
                            ||
                            (
                                isset($remainingWords[1])
                                &&
                                mb_strtolower($remainingWords[1]) == $remainingWords[1]
                            )
                        )
                        &&
                        (
                            count($bareWords) > 3
                            ||
                            (
                                // next word ends in '.' or ',', then there is another word, and then numbers
                                // Two words seems to be the minimum (e.g. one-word article title and one-word journal name),
                                // so terminate authors.
                                in_array(substr($nextWord, -1), ['.', ',']) && isset($remainingWords[2]) && preg_match('/^[0-9.,;:\-()]*$/', $remainingWords[2])
                            )
                            ||
                            (
                                $wordAfterBareWords
                                && ctype_alpha($wordAfterBareWords)
                                && $wordAfterBareWords == mb_strtolower($wordAfterBareWords)
                                && ! $this->isAnd($wordAfterBareWords, $language) 
                                && ! in_array($wordAfterBareWords, $this->vonNames)
                                && ! in_array($wordAfterBareWords, ['al.'])
                            )
                            ||
                            (
                                $this->inDict(trim($remainingWords[0], ',;'), $dictionaryNames) 
                                && ! $this->isInitials(trim($remainingWords[0], ',;'))
                                && ! in_array(trim($remainingWords[0], '.,'), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[0])
                                && ! empty($remainingWords[1]) 
                                && (
                                    $this->inDict(trim($remainingWords[1], ': '), $dictionaryNames) 
                                    ||
                                    mb_strtolower($remainingWords[1][0]) == $remainingWords[1][0]
                                   )
                                && ! $this->isInitials(trim($remainingWords[1], ',;'))
                                && ! in_array(trim($remainingWords[1], '.,'), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[1])
//                                && strtolower($remainingWords[1][0]) == $remainingWords[1][0] 
                                && $remainingWords[1] != '...' 
                                && ! in_array($remainingWords[1][0], ["'", "`"])
                                && (! isset($remainingWords[2]) 
                                    ||
                                    ($this->inDict($remainingWords[2], $dictionaryNames)
                                    && ! $this->isInitials(trim($remainingWords[2], ',;'))
                                    && ! in_array(trim($remainingWords[2], '.,'), $this->nameSuffixes)
                                    && ! preg_match('/[0-9]/', $remainingWords[2])
                                    )
                                )
                            )
                        )
                        &&
                        (
                            $nameScore['count'] == 0
                            || $nameScore['score'] / $nameScore['count'] < 0.26
                            || (
                                $bareWordStartsLc
                               )
                            || (
                                isset($bareWords[1])
                                && mb_strtolower($bareWords[1]) == $bareWords[1]
                                && ! $this->isAnd($bareWords[1], $language)
                                && (
                                    ! in_array($bareWords[1], $this->vonNames)
                                    ||
                                        (
                                        isset($bareWords[2])
                                        && mb_strtolower($bareWords[2]) == $bareWords[2]
                                        && ! in_array($bareWords[2], $this->vonNames)
                                        && ! $this->isAnd($bareWords[2], $language)
                                        )
                                   )
                                && ! in_array($remainingWords[1], ['et', 'et.', 'al', 'al.'])
                               )
                            || (
                                // title cannot start with lowercase letter (assumes that only words in author string that
                                // can begin with lowercase letters are 'and', a von name, and 'et' or 'al').
                                isset($remainingWords[1])
                                && preg_match('/^[a-z]/', $remainingWords[1])
                                && ! $this->isAnd($remainingWords[1])
                                && ! in_array($remainingWords[1], $this->vonNames)
                                && ! in_array($remainingWords[1], ['et', 'et.', 'al', 'al.'])
                               )
                        )
                        &&
                        (
                            ! $this->isInitials(rtrim($remainingWords[0], ',: '))
                            ||
                            ($remainingWords[0] == 'A' && isset($remainingWords[1]) && $remainingWords[1][0] == strtolower($remainingWords[1][0]))
                        )
                    ) {
                    // Low nameScore relative to number of bareWords (e.g. less than 26% of words not in dictionary)
                    // Note that this check occurs only when $namePart > 0---so it rules out double-barelled
                    // family names that are not followed by commas.  ('Paulo Klinger Monteiro, ...' is OK.)
                    // Cannot set limit to be > 1 bareWord, because then '... Smith, Nancy Lutz and' gets truncated
                    // at comma.

                    $this->verbose('[convertToAuthors 28]');
                    $done = true;
                    $this->addToAuthorString(11, $authorstring, $this->formatAuthor($fullName));
                } elseif ($nameComplete && Str::endsWith($word, [',', ';']) && isset($words[$i + 1]) && ! $this->isEd($words[$i + 1])) {
                    // $word ends in comma or semicolon and next word is not string for editors
                    if (
                        $type == 'editors'
                        && in_array(trim($words[$i+1], ', '), $cities)
                        &&
                            (
                                substr($words[$i+1], -1) == ':'
                                ||
                                (
                                    isset($words[$i+2])
                                    &&
                                    (
                                        strlen($words[$i+2]) == 2
                                        &&
                                        substr($words[$i+2], -1) == ':'
                                    )
                                )
                            )
                    ) {
                        $done = true;
                        $this->addToAuthorString(27, $authorstring, $this->formatAuthor($fullName));
                    } elseif ($hasAnd) {
                        $this->verbose('[convertToAuthors 29]');
                        // $word ends in comma or semicolon and 'and' has already occurred
                        // To cover the case of a last name containing a space, look ahead to see if next words
                        // are initials or year.  If so, add back comma taken off above and continue.  Else done.
                        if ($i + 3 < count($words)
                            &&
                            (
                                $this->isInitials($words[$i + 1])
                                || $this->dates->getDate($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, true, $language)
                                || ($this->isInitials($words[$i + 2]) && $this->dates->getDate($words[$i + 3], $trash, $trash1, $trash2, $trash3, true, true, true, $language))
                            )
                        ) {
                            $fullName .= ',';
                        } else {
                            $done = true;
                            $this->addToAuthorString(12, $authorstring, $this->formatAuthor($fullName));
                            $case = 11;
                        }
                    } elseif (! in_array(substr($words[$i+1],-1), [',', ';']) && ! $this->isInitials($words[$i+1]) && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language)) {
                        // $nameComplete and next word does not end in a comma and following word is 'and'
                        $this->verbose('[convertToAuthors 30]');
                        $this->addToAuthorString(13, $authorstring, $this->formatAuthor($fullName));
                        $done = true;
                    } else {
                        // If word ends in comma or semicolon and 'and' has not occurred.
                        // To cover case of last name containing a space, look ahead to see if next word
                        // is a year or starts quoted or italic. 
                        // If so, add back comma and continue.
                        // (Of course this routine won't do the trick if there are more authors after this one.  In
                        // that case, you need to look further ahead.)
                        $this->verbose('[c2a getDate 9]');
                        if (! $prevWordHasComma && $i + 2 < count($words)
                                && (
                                    $this->dates->getDate($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, true, $language)
                                    ||
                                    $this->getQuotedOrItalic($words[$i + 2], true, false, $before, $after, $style)
                                )) {
                            $this->verbose('[convertToAuthors 31]');
                            $fullName .= ',';
                        } else {
                            // Low name score relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                            if ($nameScore['count'] > 2 && $nameScore['score'] / $nameScore['count'] < 0.25) {
                                $this->verbose('[convertToAuthors 32]');
                                $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            // publication info must take at least 7 words [although it may already have been removed],
                            // so with author name there must be at least 9 words left for author to be added.
                            // (Applies mostly to books with short titles.)  However, if next word is "and", that definitely
                            // is not the start of the title and if nameScore per word is 1 or more, upcoming words really must be names
                            } elseif ($type == 'authors' && count($remainingWords) < 9 && isset($remainingWords[0]) && ! $this->isAnd($remainingWords[0]) && $determineEnd && $nameScore['count'] > 0 && $nameScore['score'] / $nameScore['count'] < 1) {
                                $this->verbose('[convertToAuthors 33]');
                                $this->addToAuthorString(15, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            }
                            $case = 12;
                            $this->verbose('[convertToAuthors 34]');
                        }
                    }
                } else {
                    $this->verbose('[convertToAuthors 35]');
                    if ($wordIsVon) {
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name, so 'namePart' not incremented");
                    } else {
                        $namePart++;
                        $this->verbose("\$namePart set to 0");
                    }
                    if ($i + 1 == count($words)) {
                        $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                    }
                }
            }
        }

        return [
            'authorstring' => $authorstring,
            'warnings' => $warnings,
        ];

    }
    /**
     * Normalize format to Smith, A. B. or A. B. Smith or Smith, Alan B. or Alan B. Smith.
     * In particular, change Smith AB to Smith, A. B. and A.B. SMITH to A. B. Smith and SMITH Ann to SMITH, Ann
     * $nameString is a FULL name (e.g. first and last or first middle last)
     */
    public function formatAuthor(string $nameString, bool $initials = false): string
    {
        $this->verbose(['text' => 'formatAuthor: argument ', 'words' => [$nameString]]);

        // If $nameString contains no space, just return it.  (Probably name for website?)
        if (! str_contains(trim($nameString, ' .'), ' ')) {
            return trim($nameString, ' .');
        }

        $nameString = str_replace('..', '.', $nameString);
        if (! str_contains($nameString, '{')) {
            $nameString = rtrim($nameString, '}');
        }

        $namesRaw = explode(' ', $nameString);

        // $initialsStart is index of component (a) that is initials and (b) after which all components are initials
        // initials are any string for which all letters are u.c. and at most two characters that are
        // letter or period
        $initialsStart = count($namesRaw);
        $allUppercase = true;
        $names = [];
        $initialsMaxStringLength = $initials ? 4 : 2; // initials could be 'A' or 'AB' or 'A.'

        foreach ($namesRaw as $k => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsStart = (strtoupper($lettersOnlyName) == $lettersOnlyName 
                    && strlen($lettersOnlyName) <= $initialsMaxStringLength) ? min([$k, $initialsStart]) : count($namesRaw);
            // Ignore $name that is '.' or ',' (a typo)
            if (! in_array($name, ['.', ','])) {
                $names[] = $name;
            }
        }

        // If word does not end in comma and names do not begin with initials and ??
        // add comma to end of preceding word if there isn't one there already
        if (strpos($nameString, ',') === false && $initialsStart > 0 && $initialsStart < count($names)) {
            if (substr($names[$initialsStart - 1], -1) != ',') {
                $names[$initialsStart - 1] .= ',';
            }
        }

        $fName = '';
        $commaPassed = false;
        //$initialPassed = false;

        $lettersOnlyNameString = preg_replace("/[^A-Za-z]/", '', $nameString);
        if (strtoupper($lettersOnlyNameString) != $lettersOnlyNameString) {
            $allUppercase = false;
        }

        foreach ($names as $i => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            if ($i) {
                $fName .= ' ';
            }
            // if (strpos($name, '.') !== false) {
            //     $initialPassed = true;
            // }

            // If name (all components) is not ALL uppercase, there are fewer than 3 letters
            // in $name or a comma has occurred and there are fewer than 4 letters, and all letters in the name are uppercase, assume $name
            // is initials.  Put periods and spaces as appropriate.
            if (
                ! $allUppercase
                &&
                (strlen($lettersOnlyName) < 3 || ($commaPassed && ($initials || strlen($lettersOnlyName) < 4)))
                &&
                mb_strtoupper($lettersOnlyName) == $lettersOnlyName
                &&
                $lettersOnlyName != 'III'
               ) {
                // First deal with single accented initial
                // Case of multiple accented initials not currently covered
                if (preg_match('/^\\\\\S\{[a-zA-Z]\}\.$/', $name)) {  // e.g. \'{A}.
                    $fName .= $name; 
                } elseif (preg_match('/^\{\\\\\S[a-zA-Z]\}\.$/', $name)) {  // e.g. {\'A}.
                    $fName .= $name;
                } elseif (preg_match('/^\{\\\\\S[a-zA-Z]\}$/', $name)) {  // e.g. {\'A}
                    $fName .= $name . '.';
                } elseif (preg_match('/^\\\\\S\{[a-zA-Z]\}$/', $name)) {  // e.g. \'{A}
                    $fName .= $name . '.';
                } elseif (preg_match('/^\\\\\S[a-zA-Z]$/', $name)) {  // e.g. \'A
                    $fName .= $name . '.';
                } else {
                    $chars = mb_str_split($name);
                    foreach ($chars as $j => $char) {
                        if (ctype_alpha($char)) {
                            if ($j >= count($chars) - 1 || $chars[$j + 1] != '.') {
                                $fName .= $char . '.';
                                if (count($chars) > $j + 1 && $chars[$j+1] != '-') {
                                    $fName .= ' ';
                                }
                            } else {
                                $fName .= $char;
                            }
                        } else {
                            $fName .= $char;
                        }
                    }
                }

            // If name is ALL uppercase and contains no period, translate uppercase component to an u.c. first letter and the rest l.c.
            // (Contains no period to deal with name H.-J., which should not be convered to H.-j.)
            } elseif (
                mb_strtoupper($lettersOnlyName) == $lettersOnlyName
                &&
                (strpos($name, '.') === false || strpos($name, '.') < strlen($name)) 
                &&
                $lettersOnlyName != 'III'
               ) {
                if (strlen($name) == 1) {
                    $fName .= $name . '.';
                } else {
                    $chars = mb_str_split($name);
                    $lcName = '';
                    foreach ($chars as $j => $char) {
                        $lcName .= ($j && $chars[$j-1] != '-') ? mb_strtolower($char) : $char;
                    }
                    $fName .= strlen($lcName) > 2 ? rtrim($lcName, '.') : $lcName;
                }

                if (
                    $i == 0 
                    && mb_strtoupper($name) == $name 
                    && strlen($name) > 1 
                    && ! in_array(substr($name, -1), ['.', ','])
                    && (
                        ! isset($names[$i+1]) || substr($names[$i+1], -1) != ',' || ! mb_strtoupper($names[$i+1]) == $names[$i+1]
                       )
                   ) {
                    $fName .= ',';
                }
            } else {
                $fName .= $name;
            }

            if (strpos($name, ',') !== false) {
                $commaPassed = true;
            }
        }

        $this->verbose(['text' => 'formatAuthor: result ', 'words' => [$fName]]);

        return $fName;
    }

    /*
     * Determine whether $word is component of a name: all letters and either all u.c. or first letter u.c. and rest l.c.
     * (and may be TeX accents)
     * If $finalPunc != '', then allow word to end in any character in $finalPunc.
     */
    public function isName(string $word, string $finalPunc = ''): bool
    {
        if (in_array(substr($word, -1), str_split($finalPunc))) {
            $word = substr($word, 0, -1);
        }
        if (preg_match('/^[a-z{}\\\"\'\-]+$/i', $word) && (ucfirst($word) == $word || strtoupper($word) == $word)) {
            return true;
        }

        return false;
    }

    // The following two methods use similar logic to attempt to determine whether a string
    // starts with names.  They should be consolidated.

    /*
     * Determine whether $string plausibly starts with a list of names
     * The method checks only the first 2 or 3 words in the string, not the whole string
     */
    public function isNameString(string $string): bool
    {
        $phrases = $this->phrases;
        $this->verbose("isNameString is examining string \"" . $string . "\"");
        $result = false;
        $words = explode(' ', $string);
        $word1 = count($words) > 1 ? rtrim($words[1], ',.;') : null;
        if ($this->isInitials($words[0]) && count($words) >= 2) {
            $this->verbose('First word is initials and there are at least 2 words in string');
            if ($this->isName(rtrim($word1, '.,')) && (ctype_alpha($word1) || count($words) == 2)) {
                $this->verbose("isNameString: string is name (case 1): <initial> <name>");
                $result = true;
            } elseif (
                    $this->isInitials($word1)
                    && count($words) >= 3
                    && $this->isName(rtrim($words[2], '.,'))
                    && ctype_alpha(rtrim($words[2], '.,'))
            ) {
                $this->verbose("isNameString: string is name (case 2): <initial> <initial> <name>");
                $result = true;
            } elseif (
                in_array($word1, $this->vonNames)
                && count($words) >= 3
                && $this->isName(rtrim($words[2], '.,'))
                && ctype_alpha(rtrim($words[2], '.,'))
            ){
                $this->verbose("isNameString: string is name (case 3): <initial> <vonName> <name>");
                $result = true;
            } else {
                $this->verbose("isNameString: string is not name (1)");
            }
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isInitials($word1)) {
            $this->verbose("isNameString: string is name (case 4): <name> <initial>");
            $result = true;
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isName($word1, '.')) {
            $this->verbose("isNameString: string is name (case 5): <name> <name>");
            $result = true;
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && in_array($word1, $this->vonNames) && $this->isName($words[2], ',') ) {
            $this->verbose("isNameString: string is name (case 6): <name> <vonName> <name>");
            $result = true;
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isName($word1) && $words[2] == $phrases['and']) {
            $this->verbose("isNameString: string is name (case 7): <name> <name> and");
            $result = true;
        } else {
            $this->verbose("isNameString: string is not name (2)");
        }

        return $result;
    }

    /*
     * Determine whether string plausibly starts with a name.
     * The method checks only the first few words in the string, not the whole string.
     */
    public function initialNameString(string $string): bool
    {
        $phrases = $this->phrases;
        $result = false;
        $words = explode(' ', $string);
        if ($this->isInitials($words[0])) {
            if (isset($words[1]) && $this->isName($words[1], '.')) {
                $result = true;
            } elseif (isset($words[1]) && $this->isInitials($words[1])
                    && isset($words[2]) && $this->isName($words[2], '.')) {
                $result = true;
            }
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isInitials($words[1])) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isName($words[1], '.')) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isName($words[1]) && isset($words[2]) && $words[2] == $phrases['and']) {
            $result = true;
        }

        return $result;
    }

    /**
     * bareWords: in array $words of strings, report the elements at the start up until one ends
     * in ',' or '.' or ')' or ';' or is a year in parens or brackets or starts with quotation mark
     * or, if $stopAtAnd is true, is 'and'.
     */
    public function bareWords(array $words, bool $stopAtAnd, string $language = 'en'): array
    {
        $barewords = [];
        $j = 0;
        foreach ($words as $j => $word) {
            $stop = false;
            $endsWithPunc = false;
            $include = true;

            // : is now included in this list (which may require special handling for titles that have : after first or second word)
            if (Str::endsWith($word, ['.', ',', ')', ';', '}', ':'])) {
                $stop = true;
                $endsWithPunc = true;
            }

            if (preg_match('/[A-Z]\.:/', $word)) {
                $stop = true;
                $endsWithPunc = true;
            }

            if (preg_match('/(\(|\[)?(18|19|20)([0-9][0-9])(\)|\])?/', $word) || Str::startsWith($word, '(')) {
                $stop = true;
                $include = false;
            }

            if (Str::startsWith($word, ['`', '"', "'", '\emph{', '{\em'])) {
                $stop = true;
            }

            // 'et' deals with the case 'et al.'
            if ($word == 'et') {
                $stop = true;
            }

            if ($stopAtAnd && $this->isAnd($word, $language)) {
                $stop = true;
            }

            if ($stop) {
                if ($include) {
                    $barewords[] = $endsWithPunc ? substr($word, 0, -1) : $word;
                }
                break;
            } else {
                $barewords[] = $word;
            }
        }

        return ['barewords' => $barewords, 'nextword' => $words[$j+1] ?? null];
    }

    /*
     * Assign a score to an array of words, with higher numbers meaning it is more likely to consist of names than
     * the title of an item.  If $ignoreAnd is true, ignore occurrences of 'and'.
     * +1 for each word in the string that is not in the dictionary
     * +2 for each word in the string that is a von name
     * -2 for each word in the string that is a stopword
     */
    public function nameScore(array $words, bool $ignoreAnd, array $dictionaryNames): array
    {
        $aspell = Aspell::create();
        $wordsToCheck = [];
        $score = 0;

        foreach ($words as $word) {
            // Names are in dictionary with initial u.c. letter, so convert word to l.c. to exclude them as regular words
            $lcword = mb_strtolower($word);

            if (($this->isAnd($word) && ! $ignoreAnd) || $this->isInitials(($word))) {
                $score++;
            } elseif (
                // not using isAnd here, because that allows "with"
                    ($word != 'and' || ! $ignoreAnd) &&
//                    ((isset($word[0]) && mb_strtoupper($word[0]) == $word[0]) || in_array($word, $this->vonNames)) &&
                    ! $this->isInitials($word) &&
                    ! in_array($word, $dictionaryNames)
                ) {
                $wordsToCheck[] = $lcword;
                if (in_array($lcword, $this->stopwords) && ! in_array($word, $this->vonNames)) {
                    $score -= 2;
                }
                if (in_array($word, $this->vonNames)) { // && ! $this->inDict($lcword)
                    $score += 2;
                }
            }
        }

        $string = implode(' ', $wordsToCheck);
        // Number of words in $wordsToCheck not in dictionary
        $score += iterator_count($aspell->check($string, ['en_US']));
        
        $returner = ['count' => count($wordsToCheck), 'score' => $score];

        return $returner;
    }

    /**
     * Regularize A.B. or A. B. to A. B. (but keep A.-B. as it is)
     * @param $string string
     */
    public function spaceOutInitials(string $string): string
    {
        return preg_replace('/(?<!\\\)\.([^ -])/', '. $1', $string);
    }

    /**
     * isNotName: determine if $word1 and $word2 might be names: start with u.c. letter or is a von name
     * or "d'" and is not an initial
     * @param $words array
     */
    public function isNotName(string $word1, string $word2): bool
    {
        $words = [$word1, $word2];

        foreach ($words as $i => $word) {
            // in case word is like {J}.-{P}.
            $word = preg_replace('/\{([A-Z])\}/', '$1', $word);
            // in case word is {Smith} or {Smith},
            $endsWithComma = false;
            if (Str::endsWith($word, ',')) {
                $word = rtrim($word, ',');
                $endsWithComma = true;
            }
            if (Str::startsWith($word, '{') && Str::endsWith($word, '}')) {
                $words[$i] = trim($word, '{}') . ($endsWithComma ? ',' : '');
            } else {
                $words[$i] = $word;
            }
        }

        $this->verbose(['text' => 'Arguments of isNotName: ', 'words' => [$words[0], $words[1]]]);
        $result = false;
        // Following reg exp allows {\'A} and \'{A} and \'A (but also allows {\'{A}, which it shouldn't)
        $accentRegExp = '/(^\{?(\\\"|\\\\\'|\\\`|\\\\\^|\\\H|\\\v|\\\A|\\\~|\\\k|\\\c|\\\\\.)\{?[A-Z]\}?|^\{\\\O\})/';
        
        for ($i = 0; $i < 2; $i++) {
            if (preg_match($accentRegExp, $words[$i])) {
                $this->verbose(['text' => 'Name component ', 'words' => [$words[$i]], 'content' => ' starts with accented uppercase character']);
            } elseif (
                // Not a name if doesn't start with an accented uppercase letter and it starts with l.c. and is not
                // "d'" and is not a von name and is not a single (possibly lowercase) letter followed by a period
                // [e.g. as in E. v. d. Boom]
                isset($words[$i][0]) 
                    && mb_strtolower($words[$i][0]) == $words[$i][0]
                    && ! (strlen($words[$i]) == 2 && in_array($words[$i][0], range('a', 'z')) && $words[$i][1] == '.')
                    && substr($words[$i], 0, 2) != "d'" 
                    && ! in_array($words[$i], ['...', '…']) 
                    && ! in_array($words[$i], $this->vonNames)
                    && $words[$i] != 'of'  // To deal with "Nicholas of Breslov" as author; don't add to vonNames, because they are used in other places, and adding them will mean that parts of titles are classified as names
                ) {
                $this->verbose(['text' => 'isNotName: ', 'words' => [$words[$i]], 'content' => ' appears not to be a name']);
                return true;
            }
        }

        $this->verbose(['text' => 'isNotName: ', 'words' => [$word1, $word2], 'content' => ' could be names']);
        return $result;
    }

    
}