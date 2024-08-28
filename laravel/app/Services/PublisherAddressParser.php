<?php
namespace App\Services;

use Str;

use App\Traits\Months;
use App\Traits\StringExtractors;
use App\Traits\Utilities;

class PublisherAddressParser
{
    use Months;
    use StringExtractors;
    use Utilities;

    /**
     * Assuming $string contains the publisher and address, isolate those two components.
     * @param $string string
     * @param $address string|null
     * @param $publisher string|null
     * @param $cityString string
     * @param $publisherString string
     * @param $cities array: from database
     * @param $publishers array: from database
     * @return $remainder string
     */
    public function extractPublisherAndAddress(string $string, string|null &$address, string|null &$publisher, string|null $cityString, string|null $publisherString, array $cities, array $publishers): string
    {
        // If, after removing $publisherString and $cityString, only punctuation remains, set those strings to be
        // publisher and address
        $newString = Str::remove([$publisherString, $cityString], $string);

        if (empty(trim($newString, ' ,.;:'))) {
            $publisher = $publisherString;
            $address = $cityString;
            return '';
        } 

        $containsPublisher = $containsCity = false;
        $string = trim($string, ' ().,');
        // If $string contains a single ':', take city to be preceding string and publisher to be
        // following string
        if (substr_count($string, ':') == 1) {
            // Work back from ':' looking for '(' not followed by ')'.  If found, take the following char to
            // be the start of the address (covers case like "extra stuff (New York: Addison-Wesley).
            for ($j = strpos($string, ':'); $j > 0 and $string[$j] != ')' && $string[$j] != '('; $j--) {

            }
            if ($string[$j] == '(') {
                $remainder = substr($string, 0, $j);
                $string = substr($string, $j + 1);
            } else {
                $remainder = '';
            }
            $colonPos = strpos($string, ':');
            $address = rtrim(ltrim(substr($string, 0, $colonPos), ',. '), ': ');
            $remainder = trim(substr($string, $colonPos + 1), ',.: ');

            // If year is repeated at end of $remainder, remove it and put it in $remainder
            $result = $this->findRemoveAndReturn($remainder, '(' . $this->yearRegExp . ')');
            $dupYear = $result ? $result[0] : null;

            $periodPos = strpos($remainder, '.');

            // If period follows 'St.' at start of string or ' St.' later in string, ignore it and find next period
            if (
                $periodPos !== false 
                && 
                (
                    ($periodPos == 2 && Str::startsWith($remainder, ['St.', 'Univ.']))
                    ||
                    ($periodPos > 2 && substr($remainder, $periodPos - 3, 4) == ' St.')
                    ||
                    ($periodPos > 4 && substr($remainder, $periodPos - 5, 6) == ' Univ.')
                )
               ) {
                $pos = strpos(substr($remainder, $periodPos + 1), '.');
                $periodPos = ($pos === false) ? false : $periodPos + 1 + $pos;
            }

            if ($periodPos !== false && preg_match('/[^A-Z]/', $remainder[$periodPos-1])) {
                $publisher = substr($remainder, 0, $periodPos);
                $remainder = substr($remainder, $periodPos);
            } else {
                $publisher = trim($remainder, '., ');
                $remainder = '';
            }

            if ($dupYear) {
                $remainder .= ' ' . $dupYear;
            }

            // If publisher ends in " [A-Z][A-Z]" (US 2-letter state abbreviation) then in fact it must be the address, so swith the publisher and address
            if (preg_match('/ [A-Z]{2}$/', $publisher)) {
                $oldPublisher = $publisher;
                $publisher = $address;
                $address = $oldPublisher;
            }
        // else if string contains no colon and at least one ',', take publisher to be string
        // preceding first comma and city to be rest UNLESS string before comma has "City,? AB" format, in which case
        // it is a US city-state, and therefore is the address OR string before comma is a city string, in which case
        // the whole string is the address (why?)
        } elseif (! substr_count($string, ':') && substr_count($string, ',')) {
            $stringBeforeComma = trim(substr($string, 0, strpos($string, ',')), ',. ');
            $stringAfterComma = trim(substr($string, strpos($string, ',') + 1), ',.: ');
            if (preg_match('/^\p{Lu}\p{Ll}+,? \p{Lu}{2}$/', $stringBeforeComma)) {
                $address = $stringBeforeComma;
                $publisher = $stringAfterComma;
            } elseif ($stringBeforeComma == $cityString) {
                $address = $stringBeforeComma . ', ' . $stringAfterComma;
                $publisher = '';
            } else {
                $publisher = $stringBeforeComma;
                $address = $stringAfterComma;
            }
            $remainder = '';
        // else take publisher/city to be strings that match list above and report rest to be
        // city/publisher
        } else {
            $stringMinusPubInfo = $string;
            foreach ($publishers as $publisherFromList) {
                $publisherPos = strpos($string, $publisherFromList);
                if ($publisherPos !== false) {
                    $containsPublisher = true;
                    $publisher = $publisherFromList;
                    $stringMinusPubInfo = substr($string, 0, $publisherPos) . substr($string, $publisherPos + strlen($publisherFromList));
                    break;
                }
            }
            foreach ($cities as $cityFromList) {
                $cityPos = strpos($stringMinusPubInfo, $cityFromList);
                if ($cityPos !== false) {
                    $containsCity = true;
                    $address = $cityFromList;
                    $stringMinusPubInfo = substr($stringMinusPubInfo, 0, $cityPos) . substr($stringMinusPubInfo, $cityPos + strlen($cityFromList));
                    break;
                }
            }

            // These two lines seem necessary---why??
            if (! $containsPublisher) {
                $publisher = '';
            }
            if (! $containsCity) {
                $address = '';
            }

            $remainder = $stringMinusPubInfo;
            // If only publisher has been identified, take rest to be city
            if ($containsPublisher and ! $containsCity) {
                $address = trim($remainder, ',.: }{ ');
                $remainder = '';
                // elseif publisher has not been identified, take rest to be publisher (whether or not city has been identified)
            } elseif (! $containsPublisher) {
                $publisher = trim($remainder, ',.: }{ ');
                $remainder = '';
            }
        }
        $publisher = Str::of($publisher)->replaceStart('by', '')->trim();
        $address = ltrim($address, '} ');

        return $remainder;
    }
    
}