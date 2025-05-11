<?php
namespace App\Services;

use Str;

use App\Traits\StringCleaners;
use App\Traits\StringExtractors;
use App\Traits\Utilities;

use App\Services\RegularExpressions;

class IncollectionPubInfoParser
{
    use StringCleaners;
    use StringExtractors;
    use Utilities;

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
        $publisherRegExp = '[\p{L}\-\/]+( [\p{L}\-\/]+){0,2}';
        $publisherUpTo4WordsRegExp = '[\p{L}\-\/]+( [\p{L}\-\/]+){0,3}';

        $this->patterns = [
            // <booktitle>, <address>: <publisher>.
            0 => '/^(?P<booktitle>' . $booktitleRegExp . '), (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u',
            // <booktitle>. <address>: <publisher>.
            1 => '/^(?P<booktitle>' . $booktitleWithCommaRegExp . ')\. ?\.? ?(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <booktitle>. Ed. <editors> <address>: <publisher>.
            2 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. ' . $this->regExps->edsNoParensRegExp . ' (?P<remains>.*)$/u',
            // <booktitle>. <editors> Ed. <address>: <publisher>.
            3 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. (?P<editors>' . $editorsWithPeriodRegExp . ') ' . $this->regExps->edsNoParensRegExp . ' (?P<address>' . $addressRegExp . '), (?<publisher>' . $publisherUpTo4WordsRegExp . ')$/u',
            // <booktitle>, eds. <editors>[.,] <address>: <publisher>.
            4 => '/^(?P<booktitle>' . $booktitleRegExp . '), ' . $this->regExps->edsNoParensRegExp . ' (?P<editors>' . $editorsWithPeriodRegExp . ')[.,] (?P<address>' . $addressRegExp . '): (?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <booktitle>[,.] <editors> (ed.). <address>: <publisher>.
            5 => '/^(?P<booktitle>' . $booktitleWithPeriodRegExp . ')[.,] (?P<editors>' . $editorsRegExp . ')\.? ' . $this->regExps->edsParensRegExp . ' ?[.,] (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u',
            // <booktitle>[,.] <editors> (ed.)[,.] <publisher>, <address>
            6 => '/^(?P<booktitle>' . $booktitleWithPeriodRegExp . ')[.,] (?P<editors>' . $editorsWithPeriodRegExp . ')\.? ' . $this->regExps->edsParensRegExp . ' ?[.,] (?P<publisher>' . $publisherRegExp . '), (?P<address>' . $addressRegExp . ')$/u',
            // <booktitle> [no punctuation], <editors> [with periods], ed.: <publisher> [up to 4 words]
            7 => '/^(?P<booktitle>' . $booktitleNoPuncRegExp . ')[.,] (?P<editors>' . $editorsWithPeriodRegExp . '), ' . $this->regExps->edsNoParensRegExp . ': ?(?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <editor> Ed., <booktitle>[no :,.]: <address>[commas allowed], <publisher>[only letters and spaces].
            8 => '/^(?P<editor>.{5,80}),? ' . $this->regExps->edsNoParensRegExp . ',? (?P<booktitle>[^:,.]*): (?P<address>[\p{L}, ]{3,40}), (?P<publisher>[\p{L} ]{5,40})$/u',
            // <editor> Ed., <booktitle>, <publisher>, <address>.
            9 => '/^(?P<editor>.{5,80}) ' . $this->regExps->edsNoParensRegExp . ', (?P<booktitle>[^,.]*), (?P<publisher>[\p{L} ]{3,40}), (?P<address>[\p{L}, ]{5,40})$/u',
            // <editor> (Ed.) <booktitle>. <address>: <publisher>
            10 => '/^(?P<editor>.{5,80}) ' . $this->regExps->edsParensRegExp . ',? (?P<booktitle>[^,.]*). (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')$/u',            // <booktitle> (eds. editor>), <address>: <publisher>
            11 => '/^(?P<booktitle>.{5,80}) \(' . $this->regExps->edsNoParensRegExp . ' (?P<editor>[\p{L}\- .]+)\), (?P<address>[\p{L}]+): (?P<publisher>[\p{L} ]{3,40})$/u',
            // <booktitle>, trans. <translator> <address>: <publisher>.
            12 => '/^(?P<booktitle>' . $booktitleRegExp . ')[.,] (?P<trans>' . $this->regExps->translatorRegExp . ')(?P<remains>.*)$/u',
        ];
    }

    // Overrides method in Utilities trait
    private function verbose(string|array $arg): void
    {
        $this->pubInfoDetails[] = $arg;
    }

    public function checkPatterns(string $remainder, $cities, $dictionaryNames, $language)
    {
        $trash1 = $trash2 = $trash3 = $trash4 = $trash5 = null;
        $isEditor = true;
        $isTranslator = false;

        foreach ($this->patterns as $i => $pattern) {
            $result = preg_match($pattern, $remainder, $matches);
            if ($result) {
                if (isset($matches['editors'])) {
                    $string = $matches['editors'];
                    if (is_array(
                        $this->authorParser->checkAuthorPatterns(
                            $string,
                            $year,
                            $month,
                            $day,
                            $date,
                            $isEditor,
                            $isTranslator,
                            $language
                        )
                    )) {
                        $editorResult = $this->authorParser->convertToAuthors(
                            explode(' ', $matches['editors']), 
                            $trash1, 
                            $trash2, 
                            $trash3, 
                            $trash4, 
                            $trash5, 
                            $isEditor, 
                            $isTranslator, 
                            $cities, 
                            $dictionaryNames, 
                            false, 
                            'editors', 
                            $language
                        );
                        if ($editorResult) {
                            $editor = $editorResult['authorstring'];
                            break;
                        }
                    }
                }
            }
        }

        dump($i, $remainder, $editor ?? '', $matches);
    }
    
}