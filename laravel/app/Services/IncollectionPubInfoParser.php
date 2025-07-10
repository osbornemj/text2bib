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

        $booktitleRegExp = '(?P<booktitle>\p{Lu}[\p{L}\-: ]{5,100})';
        $booktitleNoPuncRegExp = '(?P<booktitle>\p{Lu}[\p{L}\- ]{5,100})';
        $booktitleWithPeriodRegExp = '(?P<booktitle>\p{Lu}[\p{L}\-:. ]{5,100})';
        $booktitleWithCommaRegExp = '(?P<booktitle>\p{Lu}[\p{L}\-:, ]{5,100})';

        $editorRegExp = '(?P<editor>\p{Lu}[\p{L}\-.&,\/ ]+)';
        
        $addressRegExp = '(?<address>[\p{L},]+( [\p{L}]+)?)';
        
        $publisherWord = '[\p{L}\-&\/]+';
        $publisherRegExp = '(?P<publisher>( ?'.$publisherWord.'){1,2}( \(?'.$publisherWord.'\)?)?)';
        $publisherUpTo4WordsRegExp = '(?P<publisher>( '.$publisherWord.'){1,3}( \(?'.$publisherWord.'\)?)?)';
        $publisherAnyLengthRegExp = '(?P<publisher>( '.$publisherWord.')+)';
        $publisherDetailsRegExp = '(?P<publisher>[\p{L}\-\/ ]*('.$this->regExps->publisherRegExp.')[\p{L}\-\/() ]*)';

        $addressPublisher1 = '(?P<address>'.$addressRegExp.'): ?(?P<publisher>'.$publisherAnyLengthRegExp.')';
        $addressPublisher2 = '(?P<address>'.$addressRegExp.'), (?P<publisher>'.$publisherDetailsRegExp.')'; 
        $publisherAddress = '(?P<publisher>'.$publisherRegExp.'), (?P<address>'.$addressRegExp.')';

        $addressAndPublisherRegExp = '(?P<addressAndPublisher>('.$addressPublisher1.')|('.$addressPublisher2.')|('.$publisherAddress.'))';

        $this->patternsWithBooktitle = [
            // <booktitle>, <addressAndPublisher>
            0 => $booktitleRegExp.', '.$addressAndPublisherRegExp.'\.?',
            // <booktitle>. <addressAndPublisher>.
            1 => $booktitleWithCommaRegExp.'\. ?\.? ?'.$addressAndPublisherRegExp.'\.?',
            // <booktitle>. Ed. <editor> <addressAndPublisher>
            2 => $booktitleRegExp.'\. '.$this->regExps->edsNoParensRegExp.' '.$editorRegExp.'\. '.$addressAndPublisherRegExp,
            // <booktitle>. <editor> Ed. <addressAndPublisher>
            3 => $booktitleRegExp.'[.,] '.$editorRegExp.' '.$this->regExps->edsNoParensRegExp.',? '.$addressAndPublisherRegExp,
            // <booktitle>, eds. <editor>[.,] <addressAndPublisher>
            4 => $booktitleRegExp.', '.$this->regExps->edsNoParensRegExp.' '.$editorRegExp.'[.,] '.$addressAndPublisherRegExp.'\.?',
            // <booktitle>[,.] <editor> (ed.)[,.] <addressAndPublisher>
            5 => $booktitleWithPeriodRegExp.'[.,] '.$editorRegExp.'\.? '.$this->regExps->edsParensRegExp.' ?[.,] '.$addressAndPublisherRegExp.'',
            // <booktitle> (<editor> ed.).? <addressAndPublisher>
            6 => $booktitleRegExp.' \('.$editorRegExp.',? '.$this->regExps->edsNoParensRegExp.'\)\.? '.$addressAndPublisherRegExp,
            // <booktitle> [no punctuation], <editor> [with periods], ed.: <publisher> [up to 4 words]
            7 => $booktitleNoPuncRegExp.'[.,] '.$editorRegExp.', '.$this->regExps->edsNoParensRegExp.': ?'.$publisherUpTo4WordsRegExp.'\.?',
            // <editor> Ed.,? <booktitle>[no :,.]: <address>[commas allowed], <publisher>[only letters and spaces].
            8 => $editorRegExp.',? '.$this->regExps->edsNoParensRegExp.'[.,]? (?P<booktitle>[^:,.]*): (?P<address>[\p{L}, ]{3,40}), (?P<publisher>[\p{L} ]{5,40})',
            // <editor> Ed.[.,] <booktitle>, <addressAndPublisher>
            9 => $editorRegExp.',? '.$this->regExps->edsNoParensRegExp.'[.,]? (?P<booktitle>[^,.]*)[,.] '.$addressAndPublisherRegExp,
            // <editor>,? (Ed.)[,:]? <booktitle[no comma or period]>. \(?<addressAndPublisher>\)?
            10 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[^,.]*)[.,] \(?'.$addressAndPublisherRegExp.'\)?',
            // <editor>,? (Ed.)[,:]? <booktitle>[no period].? (<addressAndPublisher>)?
            11 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[^.]*)\.? \('.$addressAndPublisherRegExp.'\)?',
            // <editor>,? (eds.)[,:]? <booktitle>. (?<addressAndPublisher>)?
            12 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[^.]*)\. \(?'.$addressPublisher1.'\)?',
            // <editor>,? (eds.)[,:]? <booktitle>[no punc]
            13 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[\p{L} ]+)',
            // <booktitle> (eds. editor>), <addressAndPublisher>
            14 => '(?P<booktitle>.{5,80}) \('.$this->regExps->edsNoParensRegExp.' '.$editorRegExp.'\), '.$addressAndPublisherRegExp.'',
            // <booktitle> edited by <editor>[.,] \(?<addressAndPublisher>\)?
            15 => '(?P<booktitle>.*?)'.$this->regExps->editedByRegExp.' '.$editorRegExp.'[.,] \(?'.$addressAndPublisherRegExp.'\)?',
            // <editor>,? (Ed.)[,:]? <booktitle>[no punc]. <addressAndPublisher>
            16 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[\p{L} ]*)[,.] '.$addressAndPublisherRegExp,
            // <editor>,? (Ed.)[,:]? <booktitle>[no period]. <publisher[no punc]>
            17 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[^.]*)[,.] (?P<publisher>[\p{L} ]+)',
            // <editor>,? (Ed.)[,:]? <booktitle>[no period]. <series[no punc]> <seriesNumber>
            18 => $editorRegExp.',? '.$this->regExps->edsParensRegExp.'[,:]? (?P<booktitle>[^.]*)[,.:] (?P<series>[\p{L} ]+) (?P<seriesNumber>\d+)',
            // <booktitle> (<editor> ed.).? <publisher>
            19 => $booktitleRegExp.' \('.$editorRegExp.',? '.$this->regExps->edsNoParensRegExp.'\)\.? (?P<publisher>[\p{L}() ]+)',
        ];

        $this->patternsNoBooktitle = [
            // <address>: <publisher>
            100 => '(?P<address>'.$addressRegExp.'): ?(?P<publisher>'.$publisherRegExp.')\.?',
            // (<editors>, Eds.)
            101 => '\((?P<editor>[^()]+), '.$this->regExps->edsNoParensRegExp.'\)',
        ];
    }

    public function checkPatterns(string $remainder, string $language, bool $booktitleSet): array|false
    {
        $isEditor = true;
        $isTranslator = false;
        $returner = false;

        $patterns  = $booktitleSet ? $this->patternsNoBooktitle : $this->patternsWithBooktitle;

        foreach ($patterns as $i => $pattern) {
            $result = preg_match('/^'.$pattern.'$/Ju', $remainder, $matches);
            if ($result) {
                if (isset($matches['editor'])) {
                    $string = rtrim($matches['editor'], ',').' 1';
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
                $returner['addressAndPublisher'] = trim($matches['addressAndPublisher'] ?? '');
                $returner['series'] = $matches['series'] ?? '';
                $returner['seriesNumber'] = $matches['seriesNumber'] ?? '';
                $returner['incollectionCase'] = $i;
                break;
            }
        }

        return $returner;
    }
    
}