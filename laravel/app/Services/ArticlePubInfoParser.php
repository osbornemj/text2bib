<?php
namespace App\Services;

use Str;

use App\Traits\StringCleaners;
use App\Traits\StringExtractors;
use App\Traits\Months;
use App\Traits\Utilities;

use App\Services\RegularExpressions;

class ArticlePubInfoParser
{
    use Months;
    use StringCleaners;
    use StringExtractors;
    use Utilities;

    private RegularExpressions $regExps;

    var $monthsRegExp;
    var $pubInfoDetails;

    public function __construct()
    {
        $this->monthsRegExp = $this->makeMonthsRegExp();
        $this->regExps = new RegularExpressions;
    }

    // Overrides method in Utilities trait
    private function verbose(string|array $arg): void
    {
        $this->pubInfoDetails[] = $arg;
    }

    // Get journal name from $remainder, which includes also publication info
    public function getJournal(string &$remainder, object &$item, bool $italicStart, bool $pubInfoStartsWithForthcoming, bool $pubInfoEndsWithForthcoming, string $language, string $startPagesRegExp, string $volumeRegExp): array
    {
        $this->pubInfoDetails = [];
        $retainFinalPeriod = false;

        if ($italicStart) {
            // (string) on next line to stop VSCode complaining
            $italicText = (string) $this->getQuotedOrItalic($remainder, true, false, $before, $after, $style);
            if (preg_match('/ [0-9]/', $italicText)) {
                // Seems that more than just the journal name is included in the italics/quotes, so forget the quotes/italics
                // and continue
                $remainder = $before . $italicText . $after;
            } else {
                $remainder = $before . $after;
                return [
                    'journal' => $italicText,
                    'retainFinalPeriod' => $retainFinalPeriod,
                    'pub_info_details' => [],
                ];
            }
        }

        $containsDigit = preg_match('/[0-9]/', $remainder);

        if ($pubInfoStartsWithForthcoming && ! $containsDigit) {
            // forthcoming at start
            $result = $this->extractLabeledContent($remainder, $this->startForthcomingRegExp, '.*', true);
            $journal = $result ? $this->getQuotedOrItalic($result['content'], true, false, $before, $after, $style) : null;
            if (! $journal) {
                $journal = $result ? $result['content'] : '';
            }
            $label = $result ? $result['label'] : '';
            if (Str::startsWith($label, ['Forthcoming', 'forthcoming', 'Accepted', 'accepted', 'To appear', 'to appear'])) {
                $label = Str::replaceEnd(' in', '', $label);
                $label = Str::replaceEnd(' at', '', $label);
            }
            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . $label, 'getJournal 1');
        } elseif ($pubInfoEndsWithForthcoming && ! $containsDigit) {
            // forthcoming at end
            $result = $this->extractLabeledContent($remainder, '.*', $this->endForthcomingRegExp, true);
            $journal = $result['label'];
            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . trim($result['content'], '()'), 'getJournal 2');
        } else {
            $words = $remainingWords = explode(' ', $remainder);
            $initialWords = [];
            foreach ($words as $key => $word) {
                $initialWords[] = $word;
                array_shift($remainingWords);
                $remainder = implode(' ', $remainingWords);
                if ($key === count($words) - 1 // last word in remainder
                    || (isset($words[$key+1]) && Str::contains($words[$key+1], range('1', '9'))) // next word contains a digit
                    || (isset($words[$key+1]) && preg_match('/^[IVXLCD]{2,}:?$/', $words[$key+1])) // next word is Roman number.  2 or more characters required because some journal names end in "A", "B", "C", "D", ....  That means I or C won't be detected as a volume number.
                    || preg_match('/^(' . $this->monthsRegExp[$language] . ')( [0-9]{1,2})?[\-.,;]/', $remainder) // <month> or <month day> next
                    || preg_match('/^(' . $this->regExps->numberRegExp . ')/u', $remainder) // followed by number info
                    || preg_match('/^(\(?(' . $this->regExps->volumeRegExp . ')) /', $remainder) // followed by volume info
                    || preg_match($this->regExps->startPagesRegExp, ltrim($remainder, '( ')) // followed by pages info
                    || preg_match('/^' . $this->articleRegExp . '/i', $remainder) // followed by article info
                    || $this->containsFontStyle($remainder, true, 'bold', $posBold, $lenBold) // followed by bold
                    || $this->containsFontStyle($remainder, true, 'italics', $posItalic, $lenItalic) // followed by italics
                    // (Str::endsWith($word, '.') && strlen($word) > 2 && $this->inDict($word) && !in_array($word, $this->excludedWords))
                   )
                {
                    $this->verbose('Ending journal name.  Next word: ' . $word);
                    $this->verbose('[getJournal] Remainder: ' . $remainder);
                    $journal = implode(' ', $initialWords);
                    
                    // If journal name ends with '.,', final period should definitely be retained
                    $retainFinalPeriod = substr($journal, -2) == '.,';

                    $journal = rtrim($journal, ', ');
                    $remainder = ltrim($remainder, ',.');
                    break;
                }
            }
        }

        // To deal with (erroneous) extra quotes at start
        $journal = ltrim($journal, "' ");

        return [
            'journal' => $journal,
            'retainFinalPeriod' => $retainFinalPeriod,
            'pub_info_details' => $this->pubInfoDetails,
        ];
    }

    // Allows page number to be preceded by uppercase letter.  Second number in range should really be allowed
    // to start with uppercase letter only if first number in range does so---and if pp. is present, almost
    // anything following should be allowed as page numbers?
    // '---' shouldn't be used in page range, but might be used by mistake
    public function getVolumeNumberPagesForArticle(string &$remainder, object &$item, string $language, string $pagesRegExp, string $pageWordsRegExp, string $volumeAndCodesRegExp, bool $start = false): array
    {
        $issuePrefixes = 'Suppl ';

        $this->pubInfoDetails = [];

        $remainder = trim($this->regularizeSpaces($remainder), ' ;.,\'');
        $result = $containsNumberDesignation = false;

        $months = $this->monthsRegExp[$language];

        // −, third character that is replaced, is minus sign (E2 88 92)
        $remainder = str_replace(['Ð', '{\DH}', '−', ''], '-', $remainder);

        // First check for some common patterns
        // p omitted from permitted starting letters, to all p100 to be interpreted as page 100.
        $number = '[A-Za-oq-z]?([Ss]upp )?[0-9]{1,13}[A-Za-z]?';
        $numberWithRoman = '([0-9]{1,4}|[IVXLCD]{1,6})';
        $letterNumber = '([A-Z]{1,3})?-?' . $number;
        $numberRange = $number . '(( ?--?-? ?|_|\?)' . $number . ')?';
        // Some Elsevier journals number supplementary material by adding a suffix to the last page number of the paper
        // and page numbers are presented in the format '62-83.e10'.
        $pageNumberESuffix = '(\.e[0-9]{1,3})';
        // slash is permitted in range of issues (e.g. '1/2'), but not for volume, because '12/3' is interepreted to mean
        // volume 12 number 3
        $numberRangeWithSlash = '(' . $issuePrefixes . ')?' . $number . '(( ?--?-? ?|_|\/)' . $number . $pageNumberESuffix . '?)?( ?\(?(' . $months . ')([-\/](' . $months . '))?' . '( ' . $this->yearRegExp . ')?' . '\)?)?';
        //$monthRange = '\(?(?P<month1>' . $months . ')(-(?P<month2>' . $months . '))?\)?';
        // Ð is for non-utf8 encoding of en-dash(?)
        $letterNumberRange = $letterNumber . '(( ?--?-? ?|_|\?)' . $letterNumber . $pageNumberESuffix . '?)?';
        $numberRangeWithRoman = $numberWithRoman . '((--?-?|_)' . $numberWithRoman . ')?';
        // }? at end is because $volumeAndCodesRegExp includes \textbf{
        $volumeRx = '('. $this->regExps->volumeAndCodesRegExp . ')?(?P<vol>' . $numberRange . ')}?';
        $volumeWithRomanRx = '('. $this->regExps->volumeAndCodesRegExp . ')?(?P<vol>' . $numberRangeWithRoman . ')[}_]?';
        // Number, like volume, may have \textbf or \textit etc. (less likely, but possible)
        $numberRx = '('. $this->regExps->numberAndCodesRegExp . ')?(?P<num>' . $numberRangeWithSlash . ')}?';
        //$volumeWordRx = '('. $volumeAndCodesRegExp . ')(?P<vol>' . $numberRange . ')';
        // Letter in front of volume is allowed only if preceded by "vol(ume)" and is single number
        $volumeWordLetterRx = '('. $this->regExps->volumeAndCodesRegExp . ')(?P<vol>' . $letterNumber . ')';
        $numberWordRx = '('. $this->regExps->numberRegExp . ')(?P<num>' . $numberRangeWithSlash . ')';
        $pagesRx = '(?P<pageWord>'. $pageWordsRegExp . ')?(?P<pp>' . $letterNumberRange . ')';
        $punc1 = '(}?[ ,] ?|, ?| ?: ?|,? ?[(\[]\(?|\* ?\()';
        $punc2 = '([)\]]?[ :] ?|[)\]]?\)?, ?| ?: ?)';

        $dashEquivalents = ['---', '--', ' - ', '- ', ' -', '_', '?'];

        // e.g. Volume 6, No. 3, pp. 41-75 OR 6(3) 41-75
        if (preg_match('/^' . $volumeWithRomanRx . $punc1 . $numberRx . $punc2 . $pagesRx . '/J', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--', ' - '], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 1');
            $this->setField($item, 'number', str_replace(['---', '--', ' - '], '-', $matches['num']), 'getVolumeNumberPagesForArticle 2');
            $this->setField($item, 'pages', str_replace($dashEquivalents, '-', $matches['pp']), 'getVolumeNumberPagesForArticle 3');
            $remainder = trim(substr($remainder, strlen($matches[0])));
            $result = true;
        // e.g. Volume 6, 41-75$ OR 6 41-75$
       } elseif (preg_match('/^' . $volumeRx . $punc1 . $pagesRx . '$/J', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 4');
            if (Str::contains($matches['pp'], ['-', '_', '?']) || strlen($matches['pp']) < 7 || (isset($matches['pageWord']) && $matches['pageWord'])) {
                $this->setField($item, 'pages', str_replace($dashEquivalents, '-', $matches['pp']), 'getVolumeNumberPagesForArticle 5a');
            } else {
                $this->addToField($item, 'note', 'Article ' . $matches['pp'], 'getVolumeNumberPagesForArticle 5b');
            }
            $remainder = '';
            $result = true;
        // e.g. Volume 6 or IV, No. 3 
        } elseif (preg_match('/^' . $volumeWithRomanRx . $punc1 . $numberWordRx . '$/J', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 6');
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 7');
            $remainder = '';
            $result = true;
        // e.g. Volume A6, No. 3 
        } elseif (preg_match('/^' . $volumeWordLetterRx . $punc1 . $numberWordRx . '$/J', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 6a');
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 7a');
            $remainder = '';
            $result = true;
        // e.g. Volume A6, 41-75$
        } elseif (preg_match('/^' . $volumeWordLetterRx . $punc1 . $pagesRx . '$/J', $remainder, $matches)) {
               $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 8');
               $this->setField($item, 'pages', str_replace($dashEquivalents, '-', $matches['pp']), 'getVolumeNumberPagesForArticle 9');
               $remainder = '';
               $result = true;
        // e.g. Number 6, 41-75$
        } elseif (preg_match('/^' . $numberWordRx . $punc1 . $pagesRx . '$/J', $remainder, $matches)) {
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 10');
            $this->setField($item, 'pages', str_replace($dashEquivalents, '-', $matches['pp']), 'getVolumeNumberPagesForArticle 11');
            $remainder = '';
            $result = true;
        } elseif (! $start) {
            // If none of the common patterns fits, fall back on approach that first looks for a page range then
            // uses the method getVolumeAndNumberForArticle to figure out the volume and number, if any
            $numberOfMatches = preg_match_all('/^(?P<before>.*?)\(?' . $this->regExps->pagesRegExp . '\)?(?P<after>.*?)$/J', $remainder, $matches);
            if ($numberOfMatches) {
                // Take last match for a page range
                $matchIndex = $numberOfMatches - 1;
                $this->verbose("Number of matches for a potential page range: " . $numberOfMatches);
                $this->verbose("Match index: " . $matchIndex);
                $this->setField($item, 'pages', str_replace(['---', '--', ' '], ['-', '-', ''], $matches['pages'][$matchIndex]), 'getVolumeNumberPagesForArticle 10');

                // If pages surrounded by parens, don't include parens in remainder
                $remainder = $matches['before'][$matchIndex] . ' ' . $matches['after'][$matchIndex];
                $remainder = trim($remainder, '- ');
                $result = true;
            // single page
            } elseif (preg_match('/^(?P<before>.*?)p\. (?P<pp>[1-9][0-9]{0,5})(?P<after>.*?)$/', $remainder, $matches)) {
                if (isset($matches['pp'])) {
                    $this->setField($item, 'pages', $matches['pp'], 'getVolumeNumberPagesForArticle 10a');
                    $result = true;
                    $remainder = $matches['before'] . ' ' . $matches['after'];
                    $remainder = trim($remainder, ',. ');
                }
            } else {
                $item->pages = '';
                $take = 0;
                $drop = 0;
            }
        }

        return [
            'result' => $result,
            'pub_info_details' => $this->pubInfoDetails,
            'containsNumberDesignation' => $containsNumberDesignation,
        ];
    }

    public function getVolumeAndNumberForArticle(string &$remainder, object &$item, bool &$containsNumberDesignation, bool &$numberInParens, string $volumeAndCodesRegExp): array
    {
        $this->pubInfoDetails = [];

        $numberInParens = false;
        if (ctype_digit($remainder)) {
            $this->verbose('Remainder is entirely numeric, so assume it is the volume');
            $this->setField($item, 'volume', $remainder, 'getVolumeAndNumberForArticle 1');
            $remainder = '';
        } elseif ($remainder && preg_match('/^[IVXLCDM]{0,8}$/', $remainder)) {
            $this->verbose('Remainder is Roman number, so assume it is the volume');
            $this->setField($item, 'volume', $remainder, 'getVolumeAndNumberForArticle 2');
            $remainder = '';
        } elseif ($this->containsFontStyle($remainder, false, 'bold', $startPos, $length)) {
            $this->verbose('[v2] bold (startPos: ' . $startPos . ')');
            $this->setField($item, 'volume', $this->getStyledText($remainder, false, 'bold', $before, $after, $remainder), 'getVolumeAndNumberForArticle 3');
            $this->verbose('remainder: ' . ($remainder ? $remainder : '[empty]'));
            $remainder = ltrim($remainder, ':');
            $number = '[a-z]?[1-9][0-9]{0,5}[A-Za-z]?';
            $numberRange = $number . '((--?-?|_)' . $number . ')';
            if (preg_match('/^' . $numberRange . '$/', $remainder, $matches)) {
                $this->setField($item, 'pages', str_replace(['---', '--', '_'], '-', $remainder), 'getVolumeAndNumberForArticle 3a');
                $this->verbose('[p3a] pages: ' . $item->pages);
            } elseif ($remainder && ctype_digit($remainder)) {
                if (strlen($remainder) < 7) {
                    if (isset($item->pages)) {
                        $this->setField($item, 'number', $remainder, 'getVolumeAndNumberForArticle 3d');
                        $this->verbose('[p3d] number: ' . $item->number);
                    } else {
                        $this->setField($item, 'pages', $remainder, 'getVolumeAndNumberForArticle 3b');  // could be a single page
                        $this->verbose('[p3b] pages: ' . $item->pages);
                    }
                } else {
                    $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . 'Article ' . $remainder, 'getVolumeAndNumberForArticle 3c');  // could be a single page
                    $this->verbose('[p3c] note: ' . $item->note);
                }
                $remainder = '';
            } elseif (preg_match('/^\((\\\(textbf|textit){)?(?P<number>\d{1,4})}?\)$/', $remainder, $matches)) {
                // Something like (\textbf{23}) or (23).
                $this->setField($item, 'number', $matches['number'], 'getVolumeAndNumberForArticle 3c');
                $numberInParens = true;
                $remainder = '';
            }
        } else {
            // $item->number can be a range (e.g. '6-7')
            // Look for something like 123:6-19
            // First consider case in which there is only a volume
            $this->verbose('[v3] Remainder: ' . $remainder);
            // 'Volume? 123$'
            $numberOfMatches1 = preg_match('/^(' . $this->regExps->volumeAndCodesRegExp . ')?([1-9][0-9]{0,3})$/', $remainder, $matches1, PREG_OFFSET_CAPTURE);
            // $volumeAndCodesRegExp has space at end of it, but no further space is allowed.
            // So 'Vol. A2' is matched but not 'Vol. 2, no. 3'
            $numberOfMatches2 = preg_match('/^(' . $this->regExps->volumeAndCodesRegExp . ')([^ 0-9]*[1-9][0-9]{0,3})$/', $remainder, $matches2, PREG_OFFSET_CAPTURE);

            if ($numberOfMatches1) {
                $matches = $matches1;
            } elseif ($numberOfMatches2) {
                $matches = $matches2;
            } else {
                $matches = null;
            }

            if ($matches) {
                $this->verbose('[p2a] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0]);
                $this->setField($item, 'volume', $matches[2][0], 'getVolumeAndNumberForArticle 4');
                unset($item->number);
                // if a match is empty, [][1] component is -1
                $take = $matches[1][1] >= 0 ? $matches[1][1] : $matches[2][1];
                $drop = $matches[2][1] + strlen($matches[2][0]);
                $this->verbose('take: ' . $take . ', drop: ' . $drop);
                $this->verbose('No number assigned');
            } else {
                // Starts with volume
                preg_match('/^(' . $this->regExps->volumeAndCodesRegExp . ')(?P<volume>[1-9][0-9]{0,3})[,\.\/ ](--? ?)?/', $remainder, $matches);
                if (isset($matches['volume'])) {
                    $volume = $matches['volume'];
                    $this->setField($item, 'volume', $volume, 'getVolumeAndNumberForArticle 17');
                    $remainder = trim(str_replace($matches[0], '', $remainder));
                    // Does a number follow the volume?
                    // The /? allows a format 125/6 for volume/number
                    preg_match('%^\(?(?P<numberDesignation>' . $this->regExps->numberRegExp . ')?[ /]?(?P<number>([0-9]{1,20}[a-zA-Z]*)([-\/][1-9][0-9]{0,6})?)\)?%', $remainder, $matches);
                    if (isset($matches['number'])) {
                        $number = $matches['number'];
                        $this->setField($item, 'number', $number, 'getVolumeAndNumberForArticle 18');
                        $remainder = trim(str_replace($matches[0], '', $remainder));
                        if (isset($matches['numberDesignation'][0])) {
                            $containsNumberDesignation = true;
                        }
                    }
                    $take = $drop = 0;
                } else {
                    // A letter or sequence of letters is permitted after an issue number
                    $numberOfMatches = preg_match('%(' . $this->regExps->volumeAndCodesRegExp . '|[^0-9]|^)(?P<volume>([1-9][0-9]{0,3}|[IVXL]{1,3}))(?P<punc1> ?, |\(| | \(|\.|:|;|/)(?P<numberDesignation>' . $this->regExps->numberRegExp . ')? ?(?P<number>([0-9]{1,20}[a-zA-Z]*)([/-][1-9][0-9]{0,6})?)\)?%', $remainder, $matches, PREG_OFFSET_CAPTURE);
                    $numberInParens = isset($matches['punc1']) && in_array($matches['punc1'][0], ['(', ' (']);

                    if ($numberOfMatches) {
                        $this->verbose('[p2b] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0] . ', 3: ' . $matches[3][0] . ', 4: ' . $matches[4][0] . ', 5: ' . $matches[5][0] . (isset($matches[6][0]) ? ', 6: ' . $matches[6][0] : '') . (isset($matches[7][0]) ? ', 7: ' . $matches[7][0] : '') . (isset($matches[8][0]) ? ', 8: ' . $matches[8][0] : ''));
                        $this->setField($item, 'volume', $matches['volume'][0], 'getVolumeAndNumberForArticle 14');
                        if (strlen($matches['number'][0]) < 7) {
                            $this->setField($item, 'number', $matches['number'][0], 'getVolumeAndNumberForArticle 5');
                            if ($matches['numberDesignation'][0]) {
                                $containsNumberDesignation = true;
                            }
                        } else {
                            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . 'Article ' . $matches['number'][0], 'getVolumeAndNumberForArticle 6');
                        }
                        // if a match is empty, [][1] component is -1
                        $take = $matches[1][1] >= 0 ? $matches[1][1] : $matches[2][1];
                        $drop = $matches['number'][1] + strlen($matches['number'][0]);
                        $this->verbose('take: ' . $take . ', drop: ' . $drop);
                    } else {
                        // Look for "vol" etc. followed possibly by volume number and then something other than an issue number
                        // (e.g. some extraneous text after the entry)
                        $volume = $this->extractLabeledContent($remainder, $this->regExps->volumeAndCodesRegExp, '[1-9][0-9]{0,3}');
                        if ($volume) {
                            $this->verbose('[p2c]');
                            $remainder = trim($remainder, '*');
                            $this->setField($item, 'volume', $volume, 'getVolumeAndNumberForArticle 7');
                            $take = $drop = 0;
                        } elseif (preg_match('/^article (id )?.*$/i', $remainder)) {
                            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . $remainder, 'getVolumeAndNumberForArticle 8');
                            $take = 0;
                            $drop = strlen($remainder);
                        } else {
                            // Look for something like 123:xxx (where xxx is not a page range)
                            $numberOfMatches = preg_match('/([1-9][0-9]{0,3})( ?, |\(| | \(|\.|:)*(.*)/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                            if ($numberOfMatches) {
                                $this->verbose('[p2d]');
                                if (Str::startsWith($matches[3][0], ['Article', 'article', 'Paper', 'paper'])) {
                                    $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . $matches[3][0], 'getVolumeAndNumberForArticle 9');
                                    $this->setField($item, 'volume', $matches[1][0], 'getVolumeAndNumberForArticle 10');
                                } elseif (preg_match('/^([0-9]*) *([0-9]*)[ ]*$/', $remainder, $matches)) {
                                    if (empty($item->pages)) {
                                        $this->setField($item, 'pages', $matches[2], 'getVolumeAndNumberForArticle 11');
                                    }
                                    if (empty($item->volume)) {
                                        $this->setField($item, 'volume', $matches[1], 'getVolumeAndNumberForArticle 12');
                                    }
                                } elseif (preg_match('/^(' . $this->regExps->numberRegExp . ')(?P<number>[0-9]{1,4})(?P<remains>.*)$/', $remainder, $matches)) {
                                    if ($matches['number']) {
                                        $this->setField($item, 'number', $matches['number'], 'getVolumeAndNumberForArticle 12b');
                                        $this->addToField($item, 'note', trim($matches['remains'], '., '), 'getVolumeAndNumberForArticle 12c');
                                        $containsNumberDesignation = true;
                                    }
                                } else {
                                    // Assume all of $remainder is volume (might be something like '123 (Suppl. 19)')
                                    if (! Str::contains($remainder, ['('])) {
                                        $remainder = rtrim($remainder, ')');
                                    }
                                    // If volume is in parens, remove them.
                                    $remainder = trim($remainder, ' ,');
                                    if (preg_match('/^\((?P<volume>.*?)\)$/', $remainder, $matches)) {
                                        $remainder = $matches['volume'];
                                    }
                                    $volume = trim($remainder, ' ,;:.{}');
                                    if (substr($volume, 0, 4) == '\em ') {
                                        $volume = substr($volume, 4);
                                    }
                                    $this->setField($item, 'volume', $volume, 'getVolumeAndNumberForArticle 13');
                                }
                                $take = 0;
                                $drop = strlen($remainder);
                            } else {
                                $this->verbose('[p2e]');
                                unset($item->volume);
                                unset($item->number);
                                $take = $drop = 0;
                            }
                        }
                    }
                }
            }
            $remainder = substr($remainder, 0, $take) . substr($remainder, $drop);
            $remainder = trim($remainder, ',. )(');

            $this->verbose('remainder: ' . ($remainder ? $remainder : '[empty]'));
            if ($remainder && ctype_digit($remainder)) {
                $this->setField($item, 'pages', $remainder, 'getVolumeAndNumberForArticle 24'); // could be a single page
                $remainder = '';
                $this->verbose('[p4] pages: ' . $item->pages);
            }
        }

        return [
            'pub_info_details' => $this->pubInfoDetails,
        ];
    }
     
}