<?php
namespace App\Traits;

trait Months
{
    // Month abbreviations in many languages: https://web.library.yale.edu/cataloging/months
    var $months = [
        'en' => [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ],
        'cz' => [
            1 => 'leden', 
            2 => 'únor', 
            3 => 'březen', 
            4 => 'duben',
            5 => 'květen', 
            6 => 'červen', 
            7 => 'červenec', 
            8 => 'srpen',
            9 => 'září', 
            10 => 'říjen', 
            11 => 'listopad|listopadu', 
            12 => 'prosinec',
        ],
        'fr' => [
            1 => 'janvier', 
            2 => 'février', 
            3 => 'mars',
            4 => 'avril', 
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet', 
            8 => 'aout|août',
            9 => 'septembre', 
            10 => 'octobre', 
            11 => 'novembre', 
            12 => 'décembre',
        ],
        'es' => [
            1 => 'enero',
            2 => 'febrero', 
            3 => 'marzo', 
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio', 
            7 => 'julio', 
            8 => 'agosto',
            9 => 'septiembre', 
            10 => 'octubre', 
            11 => 'noviembre', 
            12 => 'deciembre',
        ],
        'pt' => [
            1 => 'janeiro', 
            2 => 'fevereiro', 
            3 => 'março',
            4 => 'abril',
            5 => 'maio', 
            6 => 'junho', 
            7 => 'julho', 
            8 => 'agosto',
            9 => 'setembro', 
            10 => 'outubro', 
            11 => 'novembro', 
            12 => 'dezembro',
        ],
        'my' => [
            1 => 'ဇန်နဝါရီလ',
            2 => 'ဖေဖော်ဝါရီ',
            3 => 'မတ်လ',
            4 => 'ဧပြီလ',
            5 => 'မေ',
            6 => 'ဇွန်လ',
            7 => 'ဇူလိုင်လ',
            8 => 'ဩဂုတ်လ',
            9 => 'စက်တင်ဘာ',
            10 => 'အောက်တိုဘာလ',
            11 => 'နိုဝင်ဘာလ',
            12 => 'ဒီဇင်ဘာ',
        ],
        'nl' => [
            1 => 'januari', 
            2 => 'februari', 
            3 => 'maart', 
            4 => 'april',
            5 => 'mei',
            6 => 'juni',
            7 => 'juli',
            8 => 'augustus',
            9 => 'september', 
            10 => 'oktober', 
            11 => 'november', 
            12 => 'december',
        ],
    ];

    var $monthsAbbreviations = [
        'en' => [
            1 => 'Jan',
            2 => 'Feb', 
            3 => 'Mar', 
            4 => 'Apr', 
            6 => 'Jun', 
            7 => 'Jul', 
            8 => 'Aug', 
            9 => 'Sep|Sept', 
            10 => 'Oct', 
            11 => 'Nov', 
            12 => 'Dec'
        ],
        'cz' => [
            1 => 'led', 
            2 => 'ún', 
            3 => 'břez', 
            4 => 'dub', 
            5 => 'květ', 
            6 => 'červ', 
            7 => 'červen', 
            8 => 'srp', 
            9 => 'zář', 
            10 => 'říj', 
            11 => 'list', 
            12 => 'pros'
        ],
        'fr' => [
            1 => 'janv', 
            2 => 'févr', 
            4 => 'avr', 
            7 => 'juil|juill',
            9 => 'sept', 
            10 => 'oct', 
            11 => 'nov', 
            12 => 'déc'
        ],
        'es' => [
            2 => 'feb', 
            3 => 'mar', 
            4 => 'abr', 
            6 => 'jun', 
            7 => 'jul', 
            9 => 'set|sept',
            10 => 'oct', 
            11 => 'nov', 
            12 => 'dec'
        ],
        'pt' => [
            1 => 'jan', 
            2 => 'fev', 
            3 => 'mar', 
            4 => 'abr', 
            5 => 'mai', 
            6 => 'jun', 
            7 => 'jul', 
            8 => 'ago', 
            9 => 'set', 
            10 => 'oct', 
            11 => 'nov', 
            12 => 'dez'
        ],
        'my' => [
            1 => 'Jan',
            2 => 'Feb', 
            3 => 'Mar', 
            4 => 'Apr', 
            6 => 'Jun', 
            7 => 'Jul', 
            8 => 'Aug', 
            9 => 'Sep|Sept', 
            10 => 'Oct', 
            11 => 'Nov', 
            12 => 'Dec',
        ],
        'nl' => [
            1 => 'jan', 
            2 => 'febr', 
            3 => 'mrt', 
            4 => 'apr', 
            8 => 'aug', 
            9 => 'sep', 
            10 => 'okt', 
            11 => 'nov', 
            12 => 'dec'
        ],
    ];

    // 'id' => '(?P<m1>Januari|Jan' . $p . '|Djan' . $p . ')|(?P<m2>Februari|Peb' . $p . ')|(?P<m3>Maret|Mrt' . $p . ')|(?P<m4>April|Apr' . $p . ')|'
    //     . '(?P<m5>Mei)|(?P<m6>Juni|Djuni)|(?P<m7>Juli|Djuli)|(?P<m8>Augustus|Ag' . $p . ')|'
    //     . '(?P<m9>September|Sept' . $p . ')|(?P<m10>Oktober|Okt' . $p . ')|(?P<m11>November|Nop' . $p . ')|(?P<m12>Desember|des' . $p . ')',

    // Do not add ';' to list of punctuation marks following abbreviation
    private function makeMonthsRegExp(): array
    {
        $regExp = [];
        foreach ($this->months as $language => $monthList) {
            $regExp[$language] = '';
            foreach ($monthList as $i => $month) {
                $regExp[$language] .= $i > 1 ? '|' : '';
                $regExp[$language] .= '(?P<m' . $i  . '>' . $month;
                if (isset($this->monthsAbbreviations[$language][$i])) {
                    $regExp[$language] .= '|' . $this->monthsAbbreviations[$language][$i] . '[.,]?';
                }
                $regExp[$language] .= ')';
            }
        }

        return $regExp;
    }

    private function makeMonthsAbbreviationsRegExp(): array
    {
        $regExp = [];
        foreach ($this->monthsAbbreviations as $language => $monthsAbbreviationList) {
            $regExp[$language] = '';
            foreach ($monthsAbbreviationList as $i => $monthAbbrev) {
                $regExp[$language] .= $i > 1 ? '|' : '';
                $regExp[$language] .= '(?P<m' . $i  . '>' . $monthAbbrev . ')';
            }
        }

        return $regExp;
    }

}
