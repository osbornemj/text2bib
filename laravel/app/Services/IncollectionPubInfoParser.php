<?php
namespace App\Services;

class IncollectionPubInfoParser
{
    public AuthorParser $authorParser;
    private RegularExpressions $regExps;

    var $pubInfoDetails;
    var $patterns;

    public function __construct()
    {
        $this->regExps = new RegularExpressions;
        $this->authorParser = new AuthorParser();

        $booktitleRegExp = '\p{Lu}[\p{L}\-: ]{5,100}';
        $booktitleNoPuncRegExp = '\p{Lu}[\p{L}\- ]{5,100}';
        $booktitleWithPeriodRegExp = '\p{Lu}[\p{L}\-:. ]{5,100}';
        $booktitleWithCommaRegExp = '\p{Lu}[\p{L}\-:, ]{5,100}';
        $editorsRegExp = '\p{Lu}[\p{L}\-& ]+';
        $editorsWithPeriodRegExp = '\p{Lu}[\p{L}\-.&, ]+';
        $addressRegExp = '[\p{L},]+( [\p{L}]+)?';
        $publisherRegExp = '[\p{L}\-]+( [\p{L}\-]+)?( \(?[\p{L}\-()]+\)?)?';
        $publisherUpTo4WordsRegExp = '[\p{L}\-\/]+( [\p{L}\-\/]+){0,3}';

        $this->patterns = [
            // <booktitle>, <address>: <publisher>.
            0 => '/^(?P<booktitle>' . $booktitleRegExp . '), (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u',
            // <booktitle>. <address>: <publisher>.
            1 => '/^(?P<booktitle>' . $booktitleWithCommaRegExp . ')\. ?\.? ?(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <booktitle>. Ed. <editor> <address>: <publisher>.
            2 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. ' . $this->regExps->edsNoParensRegExp . ' (?P<editor>' . $editorsRegExp . ')\. (?P<address>' . $addressRegExp . '): (?P<publisher>' . $publisherRegExp . ')$/u',
            // <booktitle>. <editor> Ed. <address>: <publisher>.
            3 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. (?P<editor>' . $editorsWithPeriodRegExp . ') ' . $this->regExps->edsNoParensRegExp . ' (?P<address>' . $addressRegExp . '), (?<publisher>' . $publisherUpTo4WordsRegExp . ')$/u',
            // <booktitle>, eds. <editor>[.,] <address>: <publisher>.
            4 => '/^(?P<booktitle>' . $booktitleRegExp . '), ' . $this->regExps->edsNoParensRegExp . ' (?P<editor>' . $editorsWithPeriodRegExp . ')[.,] (?P<address>' . $addressRegExp . '): (?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <booktitle>[,.] <editor> (ed.). <address>: <publisher>.
            5 => '/^(?P<booktitle>' . $booktitleWithPeriodRegExp . ')[.,] (?P<editor>' . $editorsRegExp . ')\.? ' . $this->regExps->edsParensRegExp . ' ?[.,] (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u',
            // <booktitle>[,.] <editor> (ed.)[,.] <publisher>, <address>
            6 => '/^(?P<booktitle>' . $booktitleWithPeriodRegExp . ')[.,] (?P<editor>' . $editorsWithPeriodRegExp . ')\.? ' . $this->regExps->edsParensRegExp . ' ?[.,] (?P<publisher>' . $publisherRegExp . '), (?P<address>' . $addressRegExp . ')$/u',
            // <booktitle> (<editor> ed.).? <publisher>, <address>
            7 => '/^(?P<booktitle>' . $booktitleRegExp . ') \((?P<editor>' . $editorsWithPeriodRegExp . ') ' . $this->regExps->edsNoParensRegExp . '\)\.? (?P<publisher>' . $publisherRegExp . '), (?P<address>' . $addressRegExp . ')$/u',
            // <booktitle> [no punctuation], <editor> [with periods], ed.: <publisher> [up to 4 words]
            8 => '/^(?P<booktitle>' . $booktitleNoPuncRegExp . ')[.,] (?P<editor>' . $editorsWithPeriodRegExp . '), ' . $this->regExps->edsNoParensRegExp . ': ?(?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <editor> Ed., <booktitle>[no :,.]: <address>[commas allowed], <publisher>[only letters and spaces].
            9 => '/^(?P<editor>.{5,80}),? ' . $this->regExps->edsNoParensRegExp . ',? (?P<booktitle>[^:,.]*): (?P<address>[\p{L}, ]{3,40}), (?P<publisher>[\p{L} ]{5,40})$/u',
            // <editor> Ed., <booktitle>, <publisher>, <address>.
            10 => '/^(?P<editor>.{5,80}) ' . $this->regExps->edsNoParensRegExp . ', (?P<booktitle>[^,.]*), (?P<publisher>[\p{L} ]{3,40}), (?P<address>[\p{L}, ]{5,40})$/u',
            // <editor> (Ed.) <booktitle>. <address>: <publisher>
            11 => '/^(?P<editor>.{5,80}) ' . $this->regExps->edsParensRegExp . '[,:]? (?P<booktitle>[^,.]*). (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')$/u',            // <booktitle> (eds. editor>), <address>: <publisher>
            12 => '/^(?P<booktitle>.{5,80}) \(' . $this->regExps->edsNoParensRegExp . ' (?P<editor>[\p{L}\- .]+)\), (?P<address>[\p{L}]+): (?P<publisher>[\p{L} ]{3,40})$/u',
        ];
    }

    public function checkPatterns(string $remainder, $language): array|false
    {
        $isEditor = true;
        $isTranslator = false;
        $returner = false;

        foreach ($this->patterns as $i => $pattern) {
            $result = preg_match($pattern, $remainder, $matches);
            if ($result) {
                if (isset($matches['editor'])) {
                    $string = rtrim($matches['editor'], ',') . ' 1';
                    $authorResult = $this->authorParser->checkAuthorPatterns(
                        $string,
                        $year,
                        $month,
                        $day,
                        $date,
                        $isEditor,
                        $isTranslator,
                        $language
                    );
                    if (is_array($authorResult)) {
                        $returner['editor'] = $authorResult['authorstring'];
                    } else {
                        break;
                    }
                }
                $returner['booktitle'] = $matches['booktitle'] ?? '';
                $returner['address'] = $matches['address'] ?? '';
                $returner['publisher'] = $matches['publisher'] ?? '';
                $returner['incollectionCase'] = $i;
                break;
            }
        }

        return $returner;
    }
    
}