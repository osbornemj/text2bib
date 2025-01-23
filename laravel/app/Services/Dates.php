<?php
namespace App\Services;

use Str;

use Carbon\Carbon;
//use Carbon\Exceptions\InvalidFormatException;

use App\Traits\Months;
use App\Traits\Utilities;

class Dates
{
    private Dates $dates;

    var $monthsRegExp;
    var $monthsAbbreviationsRegExp;
    
    use Months;
    use Utilities;

    public function __construct()
    {
        $this->monthsRegExp = $this->makeMonthsRegExp();
        $this->monthsAbbreviationsRegExp = $this->makeMonthsAbbreviationsRegExp();
    }

    /**
     * Report whether $string is a year between 1800 and 2100
     * @param string $string
     * @return bool
     */
    public function isYear(string $string): bool
    {
        $number = (int) $string;
        return $number > 1800 && $number < 2100;
    }

    /**
     * Get last best (a non-range, if there is one) substring in $string that is a date,
     * unless $start is true, in which case restrict to start of string and take only first match.
     * @param string $string 
     * @param string|null &$remains what is left of the string after the substring is removed
     * @param string|null &$month
     * @param string|null &$day
     * @param string|null &$date
     * @param boolean $start = true (if true, check only for substring at start of string)
     * @param boolean $allowMonth = false (allow string like "(April 1998)" or "(April-May 1998)" or "April 1998:"
     * @param boolean $allowEarlyYears = false (allow centuries starting with 13 rather than 18)
     * @param string $language = 'en'
     * @return string year
     */
    public function getDate(string $string, string|null &$remains, string|null &$month, string|null &$day, string|null &$date, bool $start = true, bool $allowMonth = false, bool $allowEarlyYears = false, string $language = 'en'): string
    {
        $year = '';
        $remains = $string;
        $months = ($this->makeMonthsRegExp())[$language];
        $monthNames = ($this->months)[$language];

        $centuries = $allowEarlyYears ? '13|14|15|16|17|18|19|20' : '18|19|20';

        // en => n.d., es => 's.f.', pt => 's.d.'?
        if (preg_match('/^(?P<year>[\(\[]?(n\. ?d\.|s\. ?f\.|s\. ?d\.)[\)\]]?|[\(\[]?[Ff]orthcoming[\)\]]?|[\(\[]?[Ii]n [Pp]ress[\)\]]?)(?P<remains>.*)$/', $remains, $matches0)) {
            $remains = $matches0['remains'];
            $year = trim($matches0['year'], '[]()');
            return $year;
        // (2020) [1830] OR 2020 [1830]
        } elseif (preg_match('/^(?P<year>\(?[\[(]?(' . $centuries . ')[0-9]{2}\)?[\])]? [\[(]?(' . $centuries . ')[0-9]{2}[\])]?)(?P<remains>.*)$/i', $remains, $matches0)) {
            $remains = trim($matches0['remains'], ') ');
            if ($matches0['year'][0] == '(') {
                $year = substr($matches0['year'], 1);
            } else {
                $year = $matches0['year'];
            }
            return $year;
        }

        if ($allowMonth) {
            if (
                // (year,? month) or (year,? month day) (or without parens or with brackets)
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2})[a-z]?,? (?P<month>' . $months . ') ?(?P<day>[0-9]{1,2})?)[\)\]]?/i', $string, $matches1)
                ||
                // (year,? day month) 
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2})[a-z]?,? (?P<day>[0-9]{1,2}) (?P<month>' . $months . ') ?)[\)\]]?/i', $string, $matches1)
                ||                
                // (day month year) or (month year) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Portuguese
                preg_match('/^ ?[\(\[]?(?P<date>(?P<day>[0-9]{1,2}\.?)? ?(de )?(?P<month>' . $months . ')(,| de)? ?(?P<year>(' . $centuries . ')[0-9]{2}))[\)\]]?/i', $string, $matches1)
                ||
                // (day monthNumber year) or (monthNumber year) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Portuguese
                preg_match('/^ ?[\(\[]?(?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>[0-9]{1,2}) ?(de )?(?P<year>(' . $centuries . ')[0-9]{2}))[\)\]]?/i', $string, $matches1)
                ||
                // (year-monthNumber-day) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Portuguese
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2})-(?P<month>[0-9]{1,2})-(?P<day>[0-9]{1,2}))[\)\]]?/i', $string, $matches1)
                ||
                // (year,? monthNumber day) (or without parens or with brackets)                            
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2})[a-z]?,?[ \/](?P<month>[0-9]{1,2})[ \/](?P<day>[0-9]{1,2}))[\)\]]?/i', $string, $matches1)
                ||
                // (month day, year) (or without parens or with brackets)
                preg_match('/^ ?[\(\[]?(?P<date>(?P<month>' . $months . ') (?P<day>[0-9]{1,2}), (?P<year>(' . $centuries . ')[0-9]{2}))[\)\]]?/i', $string, $matches1)
                ||
                // <month>-<month> <year>
                preg_match('/[ \(](?P<date>(?P<month>(' . $months . ')-(' . $months . '))(?P<year>(' . $centuries . ')[0-9]{2}))/iJ', $string, $matches2, PREG_OFFSET_CAPTURE)
                ) {
                $year = $matches1['year'] ?? null;
                $month = $matches1['month'] ?? null;
                $day = $matches1['day'] ?? null;
                $date = $matches1['date'] ?? null;
                $remains = substr($remains, strlen($matches1[0]));

                return $year;
            }

            if (! $start) {
                if (
                    // <year> <month> <day>? [month cannot be followed by '-': in that case it's a month range, picked up in next preg_match]
                    // space after $months is not optional, otherwise will pick up '9' as day in '2020 Aug;9(8):473-480'
                    preg_match('/[ \(](?P<date>(?P<year>(' . $centuries . ')[0-9]{2}) (?P<month>(' . $months . ')(?!-))( (?P<day>[0-9]{1,2}))?)/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ||
                    // <year> <month>-<month> <day>?
                    // space after $months is not optional, otherwise will pick up '9' as day in '2020 Aug-Sep;9(8):473-480'
                    preg_match('/[ \(](?P<date>(?P<year>(' . $centuries . ')[0-9]{2}) (?P<month>(' . $months . ')-(' . $months . '))( (?P<day>[0-9]{1,2}))?)/iJ', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ||
                    // <month> <day> <year>
                    preg_match('/[ \(](?P<date>(?P<month>' . $months . ') (?P<day>[0-9]{1,2}) (?P<year>(' . $centuries . ')[0-9]{2}))/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ||
                    // <day>? <month> <year>
                    // The optional "de" between day and month and between month and year is for Spanish
                    preg_match('/[ \(](?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>' . $months . ') ?(de )?(?P<year>(' . $centuries . ')[0-9]{2}))/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ||
                    // <day>/<monthNumber>/<year>
                    preg_match('%[ \(](?P<date>(?P<day>[0-9]{1,2})/(?P<monthNumber>[0-9]{1,2})/(?P<year>(' . $centuries . ')[0-9]{2}))%i', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ||
                    // <year> followed by volume, number, pages
                    // volume, number, pages pattern may need to be relaxed
                    // (pages might look like years, so this case should not be left to the routine below, which takes the last year-like string)
                    preg_match('/(?P<date>(?P<year>(' . $centuries . ')[0-9]{2}))[.,;] ?[0-9]{1,4} ?\([0-9]{1,4}\)[:,.] ?[0-9]{1,5} ?- ?[0-9]{1,5}/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                    ) {
                    $year = $matches2['year'][0] ?? null;

                    if (isset($matches2['monthNumber'])) {
                        $monthNumber = $matches2['monthNumber'][0];
                        if (isset($monthNumber[0]) && $monthNumber[0] == '0') {
                            $monthNumber = substr($monthNumber, 1);
                        }
                        $month = $monthNames[$monthNumber] ?? '??';
                    } else {
                        $month = $matches2['month'][0] ?? null;
                        if ($month) {
                            $month = rtrim($month, '.,; ');
                        }
                    }

                    $day = $matches2['day'][0] ?? null;
                    $date = $matches2['date'][0] ?? null;
                    $remains = rtrim(substr($string, 0, $matches2['date'][1]), '(') . ltrim(substr($string, $matches2['date'][1] + strlen($matches2['date'][0])), ')');

                    return $year;
                }
            }
        }

        // Remove labels from months (to avoid duplicate names in regexp)
        $months = preg_replace('/\(\?P<m[1-9][0-2]?>/', '', $months);
        $months = preg_replace('/\)|/', '', $months);
        
        // Year can be (1980), [1980], '1980 ', '1980,', '1980.', '1980)', '1980:' or end with '1980' if not at start and
        // (1980), [1980], ' 1980 ', '1980,', '1980.', or '1980)' if at start; instead of 1980, can be of form
        // 1980/1 or 1980/81 or 1980/1981 or 1980-1 or 1980-81 or 1980-1981
        // NOTE: '1980:' could be a volume number---might need to check for that

        $monthRegExp = '(?P<month>(' . $months . ')([-\/](' . $months . '))?)?';

        // Year should not be preceded by 'pp. ', which would mean it is in fact a page number/page range.
        $yearOrRangeRegExp = '(?P<year>(?<!pp\. )(' . $centuries . ')([0-9]{2})(?P<yearRange>(--?(' . $this->yearRegExp . '|[0-9]{1,2})|\/[0-9]{1,4}))?)[a-z]?';

        $regExp0 = $allowMonth ? $monthRegExp . '\.?,? *?' . $yearOrRangeRegExp : $yearOrRangeRegExp;

        // Require space or ',' in front of year if search is not restricted to start or in parens or brackets,
        // to avoid picking up second part of page range (e.g. 1913-1920).  (Comma allowed in case of no space: e.g. 'vol. 17,1983'.)
        $regExp1 = ($start ? '' : '[ ,]') . $regExp0 . '[ .,):;]';
        $regExp2 = '[ ,]' . $regExp0 . '$';
        $regExp3 = '\[' . $regExp0 . '\??\]';
        $regExp4 = '\(' . $regExp0 . '\??\)';

        //dd($start, preg_match('/^(?P<remains1>.*)(' . $regExp3 . ')[.,]?(?P<remains2>.*)$/', $string, $matches2), $matches2);

        if ($start) {
            $regExps = [$regExp1, $regExp3, $regExp4];
            foreach ($regExps as $regExp) {
                preg_match('/^(' . $regExp . ')[.,]?(?P<remains>.*)$/', $string, $matches);
                if (! empty($matches)) {
                    $year = $matches['year'];
                    if ($allowMonth && $matches['month']) {
                        $month = $matches['month'];
                    }
                    $remains = $matches['remains'];
                    break;
                }
            }
        } else {
            $regExps = [$regExp1, $regExp2, $regExp3, $regExp4];
            $bestMatch = null;
            $bestMatchScore = 0;
            foreach ($regExps as $i => $regExp) {
                preg_match('/^(?P<remains1>.*)(' . $regExp . ')[.,]?(?P<remains2>.*)$/', $string, $matches2);
                if (! empty($matches2)) {
                    if (empty($matches2['yearRange'])) {
                        $bestMatchScore = 1;
                    }
                    // Best match is one that has no year range and comes later (regExp3 or regExp4, which have parens/brackets)
                    if (! $bestMatch || empty($matches2['yearRange']) || ($matches2['yearRange'] && $bestMatchScore == 0)) {
                        $bestMatch = $matches2;
                    }
                }
            }
            if ($bestMatch) {
                $year = $bestMatch['year'];
                if ($allowMonth && ! empty($bestMatch['month'])) {
                    $month = $bestMatch['month'];
                }
                $remains = $bestMatch['remains1'] . ' ' . $bestMatch['remains2'];
            }
        }

        return $year;
    }

    /**
     * Report whether string is a date OR, if $type is 'contains', report the date it contains,
     * in a range of formats, including 2 June 2018, 2 Jun 2018, 2 Jun. 2018, June 2, 2018,
     * 6-2-2018, 6/2/2018, 2-6-2018, 2/6/2018.
     * If $allowRange is true, dates like 6-8 June, 2024 are allowed AND year is optional
     * @param string $string
     * @param string $language = 'en'
     * @param string $type = 'is'
     * @param bool $allowRange = false
     * @return bool|array
     */
    public function isDate(string $string, string $language = 'en', string $type = 'is', bool $allowRange = false): bool|array
    {
        $ofs = ['en' => '', 'cz' => '', 'fr' => '', 'es' => 'de', 'my' => '', 'nl' => '', 'pt' => 'de '];

        $year = '(?P<year>' . $this->yearRegExp . ')';
        $monthName = '(?P<monthName>' . ($this->makeMonthsRegExp())[$language] . ')';

        $of = $ofs[$language];
        $day = '(?P<day>[0-3]?[0-9])((st|nd|rd|th)\.?)?';
        $dayRange = '(?P<day>[0-3]?[0-9](--?[0-3]?[0-9])?)';
        $monthNumber = '(?P<monthNumber>[01]?[0-9])';

        $starts = match ($type) {
            'is' => '^',
            'contains' => '',
            'starts' => '^',
        };

        $ends = match ($type) {
            'is' => '$',
            'contains' => '(,|\.|]|\)| |$)',
            'starts' => '',
        };

        $matches = [];
        $isDates = [];
        if ($allowRange) {
            $isDates[1] = preg_match('/(' . $starts . $dayRange . '( ' . $of . ')?' . ' ' . $monthName . ',? ?' . '(' . $of . ' )?' . $year . '?' . $ends . ')/i' , $string, $matches[1]);
            $isDates[2] = preg_match('/(' . $starts . $monthName . ' ?' . $dayRange . ',? '. $year . '?' . $ends . ')/i', $string, $matches[2]);
            $isDates[3] = preg_match('/(' . $starts . $monthName . ' ?' . $dayRange . $ends . ')/i', $string, $matches[3]);
        } else {
            $isDates[1] = preg_match('/(' . $starts . $day . '( ' . $of . ')?' . ' ' . $monthName . ',? ?' . '(' . $of . ' )?' . $year . $ends . ')/i' , $string, $matches[1]);
            $isDates[2] = preg_match('/(' . $starts . $monthName . ' ?' . $day . '(,? ' . $year . ')?' . $ends . ')/i', $string, $matches[2]);
            $isDates[3] = preg_match('/(' . $starts . $day . '[\-\/. ]' . '(' . $of . ')?' . $monthNumber . ',?[\-\/. ]'. '(' . $of .')?' . $year . $ends . ')/i', $string, $matches[3]);
            $isDates[4] = preg_match('/(' . $starts . $monthNumber . '[\-\/ ]' . $day . ',?[\-\/ ]'. $year . $ends . ')/i', $string, $matches[4]);
            $isDates[5] = preg_match('/(' . $starts . $year . '[\-\/, ]' . $monthNumber . '[\-\/ ]' . $day . $ends . ')/i', $string, $matches[5]);
            $isDates[6] = preg_match('/(' . $starts . $year . '[, ]' . $monthName . ' ' . $day . $ends . ')/i', $string, $matches[6]);
            $isDates[7] = preg_match('/(' . $starts . $year . '[, ]' . $day . ' ' . $monthName . $ends . ')/i', $string, $matches[7]);
        }

        // Take last pattern that matches if type is 'is' and first one if type is 'contains' (why?)
        if ($type == 'is') {
            return max($isDates);
        } elseif (in_array($type, ['contains', 'starts'])) {
            $monthNumber = '';
            foreach ($isDates as $i => $isDate) {
                if (isset($matches[$i][0]) && $matches[$i][0]) {
                    if (! isset($matches[$i]['monthNumber'])) {
                        for ($j = 1; $j <= 12; $j++) {
                            if ($matches[$i]['m' . $j]) {
                                $monthNumber = $j;
                                break;
                            }
                        }
                    }

                    $monthNumber = $matches[$i]['monthNumber'] ?? $monthNumber;
                   
                    if (isset($matches[$i]['year']) && $monthNumber && isset($matches[$i]['day'])) {
                        $monthNumberLeadingZero = (strlen($monthNumber) == 1 ? '0' : '') . $monthNumber;
                        $dayLeadingZero = (strlen($matches[$i]['day']) == 1 ? '0' : '') . $matches[$i]['day'];
                        $isoDate = ($matches[$i]['year'] ?: '????') . '-' . $monthNumberLeadingZero . '-' . $dayLeadingZero;
                    } else {
                        $isoDate = null;
                    }

                    return [
                        'date' => $matches[$i][0],
                        'isoDate' => $isoDate,
                        'year' => $matches[$i]['year'] ?? '',
                        'day' => $matches[$i]['day'] ?? '',
                        'monthNumber' => $monthNumber,
                        'monthName' => $matches[$i]['monthName'] ?? '',
                    ];
                }
            }
            return false;
        }
    }

    /**
     * If month (or month range) is parsable, parse it: 
     * translate 'Jan' or 'Jan.' or 'January' or 'JANUARY', for example, to 'January'.
     * @param string $month
     * @param string $language = 'en'
     * @return array ['months' => ..., 'month1Number' => ..., 'month2number' => ...]
    */
    public function fixMonth(string $month, string $language = 'en'): array
    {
        if (is_numeric($month)) {
            return [
                'months' => $month,
                'month1number' => $month,
                'month1numberNoLeadingZero' => substr($month, 0, 1) == '0' ? substr($month, 1) : $month,
                'month2number' => null,
                'month2numberNoLeadingZero' => null,
            ];
        }

        Carbon::setLocale($language);

        $month1number = $month2number = null;

        $month1 = trim(Str::before($month, '-'), ', ');
        if (preg_match('/^[\p{L}.]*$/u', $month1)) {
            $fullMonth1 = Carbon::parseFromLocale('1 ' . $month1, $language)->monthName;
            $month1number = Carbon::parseFromLocale('1 ' . $month1, $language)->format('m');
        } else {
            $fullMonth1 = $month1;
        }

        $month2 = Str::contains($month, '-') ? Str::after($month, '-') : null;
        if ($month2 && preg_match('/^[a-zA-Z.]*$/', $month2)) {
            $fullMonth2 = Carbon::parseFromLocale('1 ' . $month2, $language)->monthName;
            $month2number = Carbon::parseFromLocale('1 ' . $month2, $language)->format('m');
        } elseif ($month2) {
            $fullMonth2 = $month2;
        } else {
            $fullMonth2 = null;
        }

        $months = $fullMonth1 . ($fullMonth2 ? '--' . $fullMonth2 : '');

        $month1numberNoLeadingZero = substr($month1number, 0, 1) == '0' ? substr($month1number, 1) : $month1number;
        $month2numberNoLeadingZero = substr($month2number, 0, 1) == '0' ? substr($month2number, 1) : $month2number;

        return [
            'months' => $months,
            'month1number' => $month1number,
            'month1numberNoLeadingZero' => $month1numberNoLeadingZero,
            'month2number' => $month2number,
            'month2numberNoLeadingZero' => $month2numberNoLeadingZero,
        ];
    }
    
}