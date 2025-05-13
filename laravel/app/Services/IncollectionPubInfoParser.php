<?php
namespace App\Services;

class IncollectionPubInfoParser
{
    public AuthorParser $authorParser;
    private RegularExpressions $regExps;

    var $pubInfoDetails;
    var $patternsNoBooktitle;
    var $patternsWithBooktitle;

    public function __construct()
    {
        $this->regExps = new RegularExpressions;
        $this->authorParser = new AuthorParser();

        $booktitleRegExp = '\p{Lu}[\p{L}\-: ]{5,100}';
        $booktitleNoPuncRegExp = '\p{Lu}[\p{L}\- ]{5,100}';
        $booktitleWithPeriodRegExp = '\p{Lu}[\p{L}\-:. ]{5,100}';
        $booktitleWithCommaRegExp = '\p{Lu}[\p{L}\-:, ]{5,100}';
        $editorRegExp = '\p{Lu}[\p{L}\-.&, ]+';
        $addressRegExp = '[\p{L},]+( [\p{L}]+)?';
        $publisherRegExp = '[\p{L}\-]+( [\p{L}\-]+)?( \(?[\p{L}\-()]+\)?)?';
        $publisherUpTo4WordsRegExp = '[\p{L}\-\/]+( [\p{L}\-\/]+){0,2}( \(?[\p{L}\-()]+\)?)?';
        $publisherAnyLengthRegExp = '[\p{L}\-\/]+( [\p{L}\-\/]+)*';
        $publisherDetailsRegExp = '[\p{L}\-\/ ]*(University|Press) [\p{L}\-\/ ]*';
        $addressAndPublisherRegExp = '((?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherAnyLengthRegExp . '))|((?P<address>' . $addressRegExp . '), (?P<publisher>' . $publisherDetailsRegExp . '))|((?P<publisher>' . $publisherRegExp . '), (?P<address>' . $addressRegExp . '))';

        $this->patternsWithBooktitle = [
            // <booktitle>, <addressAndPublisher>
            0 => '/^(?P<booktitle>' . $booktitleRegExp . '), (' . $addressAndPublisherRegExp . ')\.?$/Ju',
            // <booktitle>. <addressAndPublisher>.
            1 => '/^(?P<booktitle>' . $booktitleWithCommaRegExp . ')\. ?\.? ?(' . $addressAndPublisherRegExp . ')\.?$/Ju',
            // <booktitle>. Ed. <editor> <addressAndPublisher>
            2 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. ' . $this->regExps->edsNoParensRegExp . ' (?P<editor>' . $editorRegExp . ')\. (' . $addressAndPublisherRegExp . ')$/Ju',
            // <booktitle>. <editor> Ed. <address>, <publisher>
            3 => '/^(?P<booktitle>' . $booktitleRegExp . ')\. (?P<editor>' . $editorRegExp . ') ' . $this->regExps->edsNoParensRegExp . ' (?P<address>' . $addressRegExp . '), (?<publisher>' . $publisherUpTo4WordsRegExp . ')$/u',
            // <booktitle>, eds. <editor>[.,] <addressAndPublisher>
            4 => '/^(?P<booktitle>' . $booktitleRegExp . '), ' . $this->regExps->edsNoParensRegExp . ' (?P<editor>' . $editorRegExp . ')[.,] (' . $addressAndPublisherRegExp . ')\.?$/Ju',
            // <booktitle>[,.] <editor> (ed.)[,.] <addressAndPublisher>
            5 => '/^(?P<booktitle>' . $booktitleWithPeriodRegExp . ')[.,] (?P<editor>' . $editorRegExp . ')\.? ' . $this->regExps->edsParensRegExp . ' ?[.,] (' . $addressAndPublisherRegExp . ')$/Ju',
            // <booktitle> (<editor> ed.).? <addressAndPublisher>
            6 => '/^(?P<booktitle>' . $booktitleRegExp . ') \((?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsNoParensRegExp . '\)\.? (' . $addressAndPublisherRegExp . ')$/Ju',
            // <booktitle> [no punctuation], <editor> [with periods], ed.: <publisher> [up to 4 words]
            7 => '/^(?P<booktitle>' . $booktitleNoPuncRegExp . ')[.,] (?P<editor>' . $editorRegExp . '), ' . $this->regExps->edsNoParensRegExp . ': ?(?P<publisher>' . $publisherUpTo4WordsRegExp . ')\.?$/u',
            // <editor> Ed., <booktitle>[no :,.]: <address>[commas allowed], <publisher>[only letters and spaces].
            8 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsNoParensRegExp . '[.,]? (?P<booktitle>[^:,.]*): (?P<address>[\p{L}, ]{3,40}), (?P<publisher>[\p{L} ]{5,40})$/u',
            // <editor> Ed., <booktitle>, <addressAndPublisher>
            9 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsNoParensRegExp . ', (?P<booktitle>[^,.]*), (' . $addressAndPublisherRegExp . ')$/Ju',
            // <editor>,? (Ed.)[,:]? <booktitle[no comma or period]>. \(?<addressAndPublisher>\)?
            10 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsParensRegExp . '[,:]? (?P<booktitle>[^,.]*)[\.,] \(?(' . $addressAndPublisherRegExp . ')\)?$/Ju',            
            // <editor>,? (Ed.)[,:]? <booktitle>[no period]. \(<addressAndPublisher>\)?
            11 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsParensRegExp . '[,:]? (?P<booktitle>[^.]*)\.? \((' . $addressAndPublisherRegExp . ')\)?$/Ju',                        
            // <booktitle> (eds. editor>), <addressAndPublisher>
            12 => '/^(?P<booktitle>.{5,80}) \(' . $this->regExps->edsNoParensRegExp . ' (?P<editor>' . $editorRegExp . ')\), (' . $addressAndPublisherRegExp . ')$/Ju',
            // <booktitle> edited by <editor>[.,] \(?<addressAndPublisher>\)?
            13 => '/^(?P<booktitle>.*?)' . $this->regExps->editedByRegExp . ' (?P<editor>' . $editorRegExp . ')[.,] \(?(' . $addressAndPublisherRegExp . ')\)?$/Ju',
            // <editor>,? (Ed.)[,:]? <booktitle>[no punc]. <addressAndPublisher>
            14 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsParensRegExp . '[,:]? (?P<booktitle>[\p{L} ]*)[,.] (' . $addressAndPublisherRegExp . ')$/Ju',                                    
            // <editor>,? (Ed.)[,:]? <booktitle>[no period]. <publisher[no punc]>
            15 => '/^(?P<editor>' . $editorRegExp . '),? ' . $this->regExps->edsParensRegExp . '[,:]? (?P<booktitle>[^.]*)[,.] (?P<publisher>[\p{L} ]+)$/Ju',                                    
        ];

        $this->patternsNoBooktitle = [
            // <address>: <publisher>
            100 => '/^(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u',
            // (<editors>, Eds.)
            101 => '/^\((?P<editor>[^()]+), ' . $this->regExps->edsNoParensRegExp . '\)$/u',
        ];
    }

    public function checkPatterns(string $remainder, string $language, bool $booktitleSet): array|false
    {
        $isEditor = true;
        $isTranslator = false;
        $returner = false;

        $patterns  = $booktitleSet ? $this->patternsNoBooktitle : $this->patternsWithBooktitle;

        foreach ($patterns as $i => $pattern) {
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
                $returner['booktitle'] = trim($matches['booktitle'] ?? '', '.,: ');
                $returner['address'] = trim($matches['address'] ?? '');
                $returner['publisher'] = trim($matches['publisher'] ?? '');
                $returner['incollectionCase'] = $i;
                break;
            }
        }

        return $returner;
    }
    
}