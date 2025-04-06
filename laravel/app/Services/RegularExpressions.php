<?php
namespace App\Services;

use App\Traits\Utilities;

class RegularExpressions
{
    var $accessedRegExp1;
    var $retrievedFromRegExp1;
    var $retrievedFromRegExp2;

    var $editedByRegExp;
    var $editorStartRegExp;
    var $edsNoParensRegExp;
    var $edsParensRegExp;
    var $edsOptionalParensRegExp;
    var $edsRegExp;

    var $editionRegExp;
//    var $editionWordsRegExp;

    var $firstPublishedRegExp;

    var $inRegExp;

    var $inReviewRegExp;

    var $isbnLabelRegExp;
    var $isbnNumberRegExp;
    var $issnRegExps;

    var $numberRegExp;
    var $numberAndCodesRegExp;

    var $oclcLabelRegExp;
    var $oclcNumberRegExp;

    var $page;
    var $pageRange;
    var $pagesRegExp;
    var $pageRegExp;
    var $pageRegExpWithPp;
    var $pagesRegExpWithPp;
    var $pageWordsRegExp;
    var $startPagesRegExp;

    var $seriesRegExp;

    var $fullThesisRegExp;
    var $masterRegExp;
    var $phdRegExp;
    var $thesisRegExp;

    var $translatedByRegExp;
    var $translatorRegExp;

    var $twoPartTitleAbbreviationsRegExp;

    var $unpublishedRegExp;

    var $volumeRegExp;
    var $volumeAndCodesRegExp;
    var $volumeNumberPagesRegExp;
    var $volumeNumberYearRegExp;
    var $volumeWithNumberRegExp;

    use Utilities;

    public function __construct()
    {
        ////////////////////
        // in [booktitle] //
        ////////////////////

        // en for Spanish (and French?), em for Portuguese, dans for French, dalam for Indonesian
        $inWords = [
            '[IiEe][nm]', // English, Spanish, French(?), Portuguese
            '[Dd]ans',    // French
            '[Dd]alam',   // Indonesian
            'W',          // Polish
        ];

        foreach ($inWords as $i => $inWord) {
            $this->inRegExp .= ($i ? '|' : '(') . $inWord;
        }
        $this->inRegExp .= ')';

        /////////////
        // Editors //
        /////////////

        $editorWords = [
            '[Ee]ditors?',
            '[Ee]ds?\.?',   // English, Czech
            'Hrsgg\.',      // German
            'Hg\.',         // German
            '[Dd]ir\.',     // French
            '[Éé]ds?\.',    // French
            'რედ\.?',      // Georgian
            '[Aa] cura di', // Italian
            '[Rr]ed\.',     // Polish
            '[Dd]ü\.',      // Turkish
            '[Cc]oords?\.',   // Portuguese(?)
        ];

        $editedByWords = [
            '[Ee]dited by',
            '[Ee]d\. by',
            '[Ee]d(itado|\.) por',  // Spanish, Portuguese
            '[Éé]d(ité|\.) par',    // French
            '[Bb]earbeitet von',    // German
            '[Hh]rsg\. von',        // German
            '[Bb]ewerkt door',      // Dutch
            '[Dd]iedit oleh',       // Indonesian
        ];

        $edsRx = '';
        foreach ($editorWords as $i => $editorWord) {
            $edsRx .= ($i ? '|' : '') . $editorWord;
        }

        // "[Ee]ditors?" or ...
        $this->edsNoParensRegExp = '(' . $edsRx . ')';
        // "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsParensRegExp = '[(\[](' . $edsRx . ')[)\]]';
        // "[Ee]ditors?" or "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsOptionalParensRegExp = '[(\[]?(' . $edsRx . ')[)\]]?';
        // " [Ee]ditors? " or "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsRegExp = '/[(\[ ](' . $edsRx . ')[)\],. ]/u';

        $editedByRx = '';
        foreach ($editedByWords as $i => $editedByWord) {
            $editedByRx .= ($i ? '|' : '') . $editedByWord;
        }

        $this->editedByRegExp = '(' . $editedByRx . ')';

        $this->editorStartRegExp = '/^[(\[]?(' . $editedByRx . '|' . $edsRx . ')/u';

        //////////////
        // Editions //
        //////////////

        // Words meaning edition; for each language, full word first, then abbreviations
        $editionWordsLocalized = [
            'en' => [
                'edition', 'ed\.?', 'edn\.?', 
            ],
            'cz' => [
                'vydání', 
            ],
            'fr' => [
                'édition', 'ed\.?', 'edn\.?', 
            ],
            'es' => [
                'edición', 'ed\.?', 'edn\.?', 
            ],
            'pt' => [
                'edição', 'ed\.?', 
            ],
            'my' => [
                'edition', 'ed\.?', 'edn\.?', 
            ],
            'nl' => [
                'editie', 'ed\.?',
            ],
        ];

        // $this->editionWordsRegExp = '(';
        // $editionWords = [];
        // $k = 0;
        // foreach ($editionWordsLocalized as $words) {
        //     foreach ($words as $word) {
        //         if (! in_array($word, $editionWords)) {
        //             $this->editionWordsRegExp .= ($k ? '|' : '') . $word;
        //         }
        //         $editionWords[] = $word;
        //         $k = 1;
        //     }
        // }
        // $this->editionWordsRegExp .= ')';

        $editionNumbers = [
            'en' => [
                1 => ['1st', 'first', '1'],
                2 => ['2nd', 'second', '2'],
                3 => ['3rd', 'third', '3'],
                4 => ['4th', 'fourth', '4'],
                5 => ['5th', 'fifth', '5'],
                6 => ['6th', 'sixth', '6'],
                7 => ['7th', 'seventh', '7'],
                8 => ['8th', 'eighth', 'eight', '8'],
                9 => ['9th', 'ninth', '9'],
                10 => ['10th', 'tenth', '10'],
                11 => [
                    '[1-9][1-9]th', 
                    '(15|16|17|18|19|20)[0-9]{2}', 
                    'rev\.', 
                    '(?<!(1st|2nd|3rd|[4-9]th) )revised', 
                    '(1st|2nd|3rd|[4-9]th)? revised',
                ]
            ],
            'cz' => [
                1 => ['1\.'],
                2 => ['2\.'],
                3 => ['3\.'],
                4 => ['4\.'],
                5 => ['5\.'],
                6 => ['6\.'],
                7 => ['7\.'],
                8 => ['8\.'],
                9 => ['9\.'],
                10 => ['10\.'],
            ],
            'fr' => [
                1 => ['1er', '1ère'],
                2 => ['2e',],
                3 => ['3e'],
                4 => ['4e'],
                5 => ['5e'],
                6 => ['6e'],
                7 => ['7e'],
                8 => ['8e'],
                9 => ['9e'],
                10 => ['10e'],
            ],
            'es' => [
                1 => ['1ra', '1º'],
                2 => ['2da', '2º'],
                3 => ['3ra', '3º'],
                4 => ['4ra', '4º'],
                5 => ['5ra', '5º'],
                6 => ['6ra', '6º'],
                7 => ['7ra', '7º'],
                8 => ['8ra', '8º'],
                9 => ['9ra', '9º'],
                10 => ['10ra', '10º'],
            ],
            'pt' => [
                1 => ['1º\.?'],
                2 => ['2ª\.?'],
                3 => ['3ª\.?'],
                4 => ['4ª\.?'],
                5 => ['5ª\.?'],
                6 => ['6ª\.?'],
                7 => ['7ª\.?'],
                8 => ['8ª\.?'],
                9 => ['9ª\.?'],
                10 => ['10ª\.?'],
            ],
            'my' => [
                1 => ['1st'],
                2 => ['2nd'],
                3 => ['3rd'],
                4 => ['4th'],
                5 => ['5th'],
                6 => ['6th'],
                7 => ['7th'],
                8 => ['8th'],
                9 => ['9th'],
                10 => ['10th'],
            ],
            'nl' => [
                1 => ['1e'],
                2 => ['2e'],
                3 => ['3e'],
                4 => ['4e'],
                5 => ['5e'],
                6 => ['6e'],
                7 => ['7e'],
                8 => ['8e'],
                9 => ['9e'],
                10 => ['10e'],
            ],
        ];

        // Strings are keyed to languages, but the language is ignored currently: a single regular expression
        // is constructed from all the strings, with the following format:
        // '(?P<fullEdition>(?P<edition>(?P<n-1>1st|first)|(?P<n-2>2nd|second)|...) (edition|ed|edn))' . 
        // '|' . 
        // '(?P<fullEdition>(?P<edition>(?P<n-1>1\.)|(?P<n-2>2\.)|...) (vydání))' . 
        // '|'
        // ...
        $this->editionRegExp = '(?P<fullEdition>(?P<edition>';
        foreach ($editionNumbers as $lang => $numbers) {
            $this->editionRegExp .= ($lang != 'en' ? '|' : '');
            foreach ($numbers as $number => $values) {
                $this->editionRegExp .= ($number > 1 ? '|' : '(') . '(?P<n' . $number . '>';
                foreach ($values as $i => $value) {
                    $this->editionRegExp .= ($i ? '|' : '') . $value;
                }
                $this->editionRegExp .= ')';
            }
            $this->editionRegExp .= ') ?(';
            $words = $editionWordsLocalized[$lang];
            foreach ($words as $j => $word) {
                $this->editionRegExp .= ($j ? '|' : '') . $word;
            }
            $this->editionRegExp .= ')';
        }
        $this->editionRegExp .= '))';

        /////////////////
        // Translators //
        /////////////////

        $translatorWords = [
            '[Tt]ranslators?',
            '[Tt]rans\.(?! by)',
            '[Tt]rad\.(?! by)',
            '[Tt]r\.(?! by)',
            'Tradução',   // Portuguese
            'Çev\.',      // Turkish
        ];

        $translatorRx = '';
        foreach ($translatorWords as $i => $translatorWord) {
            $translatorRx .= ($i ? '|' : '') . $translatorWord;
        }

        $this->translatorRegExp = '(' . $translatorRx . ')';

        $translatedByWords = [
            '[Tt]ranslat(ed|ion) by',
            '[Tt]ransl?\. by',
            '[Tt]r\.(?! by)',
            '[Tt]r\.? by',
            '[Tt]rad(\.|ucción) de',  // Spanish
            '[Tt]raducido por',       // Spanish
            'Übersetzt von',          // German
            'Übersetzung von',        // German
            '[Tt]raduzido (por|de)',  // Portuguese
            '[Tt]radução( por)?',     // Portuguese
            '[Tt]raduit par',         // French
            '[Tt]raduction par',      // French
        ];

        $translatedByRx = '';
        foreach ($translatedByWords as $i => $translatedByWord) {
            $translatedByRx .= ($i ? '|' : '') . $translatedByWord;
        }

        $this->translatedByRegExp = '(' . $translatedByRx . ')';

        ///////////
        // Pages //
        ///////////

        $pageWords = [
            '[Pp]ages? ',
            '[Pp]ages? : ',
            '[Pp]p\.? ?',
            'p\.? ?',
            '[Pp]ágs?\. ?',   // Spanish, Portuguese
            '[Bb]lz\. ?',     // Dutch
            '[Hh]al[\.:] ?',  // Indonesian
            '[Hh]lm\. ?',
            '[Ss]s?\. ?(?!l\.)',  // Turkish, Polish, German (($! expression means string is not "s. l." or "s.l."))
            '[Ss]tr\. ?',     // Czech
            'стр\. ?',        // Russian
            'С\. ?',          // Russian
            'გვ\. ?',         // Georgian
        ];

        $firstPage = '[A-Za-z]?[1-9ivxl][0-9ivxl]{0,4}';
        $lastPage = '[A-Za-z]?[0-9ivxl]{1,5}';
        // Page number cannot be followed by letter, to avoid picking up string like "'21 - 2nd Congress".
        // Page range
        $this->pageRange = '(?P<pages>(?P<startPage>' . $firstPage . ') ?-{1,3} ?(?P<endPage>' . $lastPage . '))(?![a-zA-Z])';
        // Single page or page range
        $this->page = '(?P<pages>' . $firstPage . ')( ?-{1,3} ?' . $lastPage . ')?(?![a-zA-Z])';

        $startPagesRegExp = '/(?P<pageWord>';
        $pageWordsRegExp = '(?P<pageWord>';
        foreach ($pageWords as $i => $pageWord) {
            $startPagesRegExp .= ($i ? '|' : '') . '^' . $pageWord;
            $pageWordsRegExp .= ($i ? '|' : '') . $pageWord;
        }
        $startPagesRegExp .= ')[0-9]/';
        $pageWordsRegExp .= ')';

        $this->pageWordsRegExp = $pageWordsRegExp;

        $pageRegExpWithPp = '(' . $pageWordsRegExp . '):? ?' . $this->page;
        $pagesRegExpWithPp = '(' . $pageWordsRegExp . '):? ?' . $this->pageRange;
        $pageRegExp = '(' . $pageWordsRegExp . ')?:? ?' . $this->page;
        $pagesRegExp = '(' . $pageWordsRegExp . ')?:? ?' . $this->pageRange;

        $this->startPagesRegExp = $startPagesRegExp;
        
        // single page or page range, must be preceded by page word
        $this->pageRegExpWithPp = $pageRegExpWithPp;
        // page range, must be preceded by page word
        $this->pagesRegExpWithPp = $pagesRegExpWithPp;
        // single page or page range, page word before is optional
        $this->pageRegExp = $pageRegExp;
        // page range, page word before is optional
        $this->pagesRegExp = $pagesRegExp;

        ////////////
        // Number //
        ////////////

        // (°|º) cannot be replaced by [°º].  Don't know why.
        // Note that "Issues" cannot be followed by "in" --- because "issues in" could be part of journal name
        $numberWords = [
            '[Nn][Oo]s?( ?\.:?| ?:| ) ?',
            '[Nn]úm\.:? ?',
            '[Nn]umbers? ?',
            '[Nn] ?\. (?=\d)',       // must be followed by digit
            '№\.? ?',
            '[Nn]\.? ?(°|º) ?',
            '[Ii]ssue(: ?| )',
            '[Ii]ssues(?! [Ii]n) ?',
            'Issue no\. ?',
            'Iss: ',
            'Heft ',                  // German
            'Broj ',                  // Bosnian
        ];

        $numberRegExp = '';
        foreach ($numberWords as $i => $numberWord) {
            $numberRegExp .= ($i ? '|' : '') . $numberWord;
        }

        // number words
        $this->numberRegExp = $numberRegExp;
        $this->numberAndCodesRegExp = $numberRegExp . '|{\\\bf |\\\textbf{|\\\textit{|\*';
        
        ////////////
        // Volume //
        ////////////

        $volumeWords = [
            '[Vv]olumes?', // English, Portuguese
            '[Vv]ols?\.?',
            'VOL\.?',
            '[Vv]\.',
            '[Tt]omos?',   // Spanish
            '[Tt]omes?',   // French
            '[Bb]ände',    // German (plural)
            '[Bb]and',     // German
            '[Bb]d\.',     // German
            'Т\.',         // Russian
        ];

        $volumeRegExp = '';
        foreach ($volumeWords as $i => $volumeWord) {
            $volumeRegExp .= ($i ? '|' : '') . $volumeWord . ' ?';
        }

        // volume words, with optional space after each one
        $this->volumeRegExp = $volumeRegExp;
        $this->volumeAndCodesRegExp = $volumeRegExp . '|{\\\bf |\\\textbf{|\\\textit{|\\\emph{|\*';
        $this->volumeWithNumberRegExp = '(' . $this->volumeRegExp . ') ?(\\\textit\{|\\\textbf\{|\\\emph\{)?[1-9][0-9]{0,4}';
    
        $this->volumeNumberPagesRegExp = '/(' . $this->volumeRegExp . ')?[0-9]{1,4} ?(' . $this->numberRegExp . ')?[ \(][0-9]{1,4}[ \)]:? ?(' . $this->pageRegExp . ')/u';

        $this->volumeNumberYearRegExp = '/(' . $this->volumeAndCodesRegExp . ')? ?\d{1,4}(, ?| )(' . $this->numberRegExp . ')? ?\d{1,4} [\(\[]?' . $this->yearRegExp . '[\)\]]/u';

        ////////////
        // Thesis //
        ////////////

        $thesisWords = [
            '[Tt]hesis',
            '[Tt]esis',
            '[Tt]hèse',
            '[Tt]ese',
            '[Tt]ezi',
            '[Dd]issertation',
            '[Dd]iss\.',
            '[Dd]issertação',
            '[Dd]isertación',
        ];

        $thesisRegExp = '';
        foreach ($thesisWords as $i => $thesisWord) {
            $thesisRegExp .= ($i ? '|' : '') . $thesisWord . ' ?';
        }

        $this->thesisRegExp = '(^|[ (\[])(' . $thesisRegExp . ')([ .,)\]]|$)';

        $mastersWords = [
            '[Mm]aster(\'?s)?( Degree)?,?',
            'M\.? ?A\.?',
            'M\.? ?Sc\.?',
            'M\. ?S\.',
            'MBA',
            '[Mm]estrado',
            '[Mm]aestría',
            '[Ll]icenciatura',
            'Yayınlanmamış Yüksek [Ll]isans',
            'Yüksek [Ll]isans',
            'Masterproef',
        ];

        $masterRegExp = '';
        foreach ($mastersWords as $i => $mastersWord) {
            $masterRegExp .= ($i ? '|' : '') . $mastersWord . ' ?';
        }

        $this->masterRegExp = $masterRegExp;
        
        $phdWords = [
            'Ph[Dd]',
            'Ph\. ?D\.?',
            '[Dd]octoral',
            '[Dd]octorat',
            '[Dd]oktora',
            '[Dd]outorado',
            '[Pp]roefschrift',
            '[Dd]oktorská',
        ];

        $phdRegExp = '';
        foreach ($phdWords as $i => $phdWord) {
            $phdRegExp .= ($i ? '|' : '') . $phdWord . ' ?';
        }

        $this->phdRegExp = $phdRegExp;
        
        $fullThesisWords = [
            '((' . $this->phdRegExp . '|' . $this->masterRegExp . ') (' . $thesisRegExp . '))',
            '[Tt]hèse de doctorat',              // French
            '[Tt]hèse de master',                // French
            'Thèse de Doctorat en droit',        // French
            'Thèse de Doctorat en droit public', // French
            '[Tt]esis doctoral',                 // Spanish
            '[Dd]isertación [Dd]octoral',        // Spanish
            '[Tt]esis de grado',                 // Spanish
            '[Tt]esis de [Mm]aestría',           // Spanish
            '[Dd]isertación de [Ll]icenciatura', // Spanish
            '[Tt]ese de [Dd]outorado',           // Portuguese
            '[Tt]ese \([Dd]outorado\)',          // Portuguese
            '[Dd]issertação de [Mm]estrado',     // Portuguese
            '[Dd]issertação \([Mm]estrado\)',    // Portuguese
            '[Tt]ese de [Mm]estrado',            // Portuguese
            '[Dd]octoraal [Pp]roefschrift',      // Dutch
            '[Mm]asterproef',                    // Dutch
            '[Dd]oktorská práce',                // Czech
            '[Dd]iplomová práce',                // Czech
            '[Yy]ayımlanmamış doktora tezi',     // Turkish
            '[Dd]oktora [Tt]ezi',                // Turkish
            '[Yy]üksek [Ll]isans [Tt]ezi',       // Turkish
            '[Yy]ükseklisans [Tt]ezi',           // Turkish
        ];

        $fullThesisRegExp = '(';
        foreach ($fullThesisWords as $i => $fullThesisWord) {
            $fullThesisRegExp .= ($i ? '|' : '') . $fullThesisWord . ' ?';
        }
        $fullThesisRegExp .= ')';

        $this->fullThesisRegExp = $fullThesisRegExp;

        ////////////
        // Series //
        ////////////

        $seriesPhrases = [
            '(?<![Tt]ime[ \-])[Ss]eries',
            '[Ll]ecture [Nn]otes',
            'Graduate [Tt]exts',
            'Graduate Studies',
            'Grundlehren der mathematischen Wissenschaften',
            'Monographs',
            'Pure and Applied Mathematics',
            'Tracts',
            'Coleção',
        ];

        $this->seriesRegExp = '';
        foreach ($seriesPhrases as $i => $seriesPhrase) {
            $this->seriesRegExp .= ($i ? '|' : '') . $seriesPhrase . ' ?';
        }

        /////////////////
        // Unpublished //
        /////////////////

        $unpublishedWords = [
            '[Uu]npublished',      // English
            '[Nn]epublikovaný',    // Czech
            '[Nn]on publié',       // French
            '[Nn]ão publicado',    // Portuguese
            '[Ii]nédita',          // Spanish
            '[Ii]nédito',          // Spanish
            '[Yy]ayınlanmamış',    // Turkish
        ];

        $unpublishedRegExp = '';
        foreach ($unpublishedWords as $i => $unpublishedWord) {
            $unpublishedRegExp .= ($i ? '|' : '') . $unpublishedWord . ' ?';
        }

        $this->unpublishedRegExp = '(' . $unpublishedRegExp . ')';
 
        /////////////////////
        // First published //
        /////////////////////

        $firstPublishedWords = [
            '[Ff]irst published( in)?',          // English
            '[Oo]riginally published( in)?',     // English
            '[Ii]nitialement publié( dans)?',    // French
            '[Pp]ublicado originalmente( em)?',  // Portuguese
            '[Pp]ublicado originalmente( en)?',  // Spanish
        ];

        $firstPublishedRegExp = '';
        foreach ($firstPublishedWords as $i => $firstPublishedWord) {
            $firstPublishedRegExp .= ($i ? '|' : '') . $firstPublishedWord . ' ?';
        }

        $this->firstPublishedRegExp = '(' . $firstPublishedRegExp . ')';

        ////////////////
        // ISBN, ISSN //
        ////////////////

        $this->isbnLabelRegExp = 'ISBN(-(10|13))? ?:? ?';
        // ISBN should not have spaces, but allow them.  (ISBN has 10 or 13 digits.)
        $this->isbnNumberRegExp = '[0-9X -]{10,17}';

        $issnNumberFormat = '[0-9]{4}-[0-9]{3}[0-9X]';
        $this->issnRegExps = [
            '/[Oo]nline(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[Pp]rint(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[( ,]ISSN(:|: | )' . $issnNumberFormat . ' ?[( ](print|digital)(\) ?| |$)(' . $issnNumberFormat . ' ?[( ](print|digital)(\)| |$))?/',
            '/[( ,](e-|p-)?ISSN(:|: | )(\(?(online|digital|print)[) ])?' . '(e-?|p-?)?' . $issnNumberFormat . '(\)|,|.| |$)/',
        ];

        ////////////////////
        // Retrieved from //
        ////////////////////

        // Used in: "*Retrieved from* (site)? <url> accessed <date>"
        // and "*Retrieved from* (site)? <url> <date>?"
        $this->retrievedFromRegExp1 = [
            'en' => '(Retrieved from:? |Available( online)? ?(at|from)?:? )',
            'cz' => '(Dostupné z:? |načteno z:? )',
//            'de' => 'Abgerufen von',
            'fr' => '(Récupéré sur |Disponible( (à l\'adresse|sur))?:? )',
            'es' => '(Obtenido de |Disponible( en)?:? )',
//            'id' => '(Diambil kembali dari )',
            'pt' => '(Disponível( em)?:? |Obtido de:? )',
            'my' => '(Retrieved from |Available( at)?:? )',
            'nl' => '(Opgehaald van |Verkrijgbaar( bij)?:? |Beschikbaar op |Available( at)?:? )',
        ];

        // Dates are between 8 and 23 characters long (13 de novembre de 2024,)
        $dateRegExp = '[\p{L}0-9,/\-\. ]{8,23}';

        // Used in: "*Retrieved <date>? from* <url> <note>?
        $this->retrievedFromRegExp2 = [
            'en' => '[Rr]etrieved (?P<date1>' . $dateRegExp . ' )?(, )?from |[Aa]ccessed (?P<date2>' . $dateRegExp . ' )?at ',
            'cz' => '[Dd]ostupné (?P<date1>' . $dateRegExp . ' )?(, )?z |[Zz]přístupněno (?P<date2>' . $dateRegExp . ' )?na |Staženo (?P<date1>' . $dateRegExp . ' )?(, )?z',
            'fr' => '[Rr]écupéré (le )?(?P<date1>' . $dateRegExp . ' )?,? ?(sur|de) |[Cc]onsulté (le )?(?P<date2>' . $dateRegExp . ' )?,? ?(à|sur|de) ',
            'es' => '[Oo]btenido (el )?(?P<date1>' . $dateRegExp . ' )?de |[Rr]ecuperado (el )?(?P<date1>' . $dateRegExp . ' )?de |[Aa]ccedido (?P<date2>' . $dateRegExp . ' )?en ',
            'pt' => '[Oo]btido (?P<date1>' . $dateRegExp . ' )?de |[Aa]cess(ad)?o (?P<date2>' . $dateRegExp . ' )?em |[Aa]cedido (?P<date2>' . $dateRegExp . ' )?em ',
            'my' => '[Rr]etrieved (?P<date1>' . $dateRegExp . ' )?(, )?from |[Aa]ccessed (?P<date2>' . $dateRegExp . ' )?at ',
            'nl' => '[Oo]pgehaald (?P<date1>' . $dateRegExp . ' )?(, )?van |[Gg]eraadpleegd op (?P<date2>' . $dateRegExp . ' )?om ',
        ];

        // Used in: "Retrieved from (site)? <url> *accessed <date>*"
        // <url> *accessed <date>*
        // *acceessed <date>* <url>
        // *accessed <date>*
        $this->accessedRegExp1 = [
            'en' => '(([Ll]ast|[Dd]ate) )?([Rr]etrieved|[Aa]ccessed|[Cc]onsulted|[Vv]iewed|[Vv]isited)( on)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'cz' => '([Nn]ačteno|[Zz]přístupněno|[Zz]obrazeno|[Cc]itováno)( dne)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'fr' => '([Rr]écupéré |[Cc]onsulté )(le )?(?P<date2>' . $dateRegExp . ')',
            'es' => '([Oo]btenido|[Aa]ccedido)[,:]? (?P<date2>' . $dateRegExp . ')',
            'pt' => '([Oo]btido |[Aa]cess(ad)?o( (a|em):?)? |[Aa]cedido (em:?)? )(?P<date2>' . $dateRegExp . ')',
            'my' => '([Ll]ast )?([Rr]etrieved|[Aa]ccessed|[Vv]iewed)( on)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'nl' => '([Oo]opgehaald op|[Gg]eraadpleegd op|[Bb]ekeken|[Bb]ezocht op|[Gg]eopend),? (?P<date2>' . $dateRegExp . ')',
        ];

        ///////////
        // Other //
        ///////////

        $twoPartTitleAbbreviations = [
            ['comb\.', 'nov\.'],
            ['n\.', 'sp\.'],
            ['nov\.', 'sp\.'],
            ['nov\.', 'spec\.'],
            ['sp\.', 'n\.'],
            ['sp\.', 'nov\.'],
            ['spec\.', 'nov\.'],
            ['stat\.', 'n\.'],
            ['stat\.', 'nov\.'],
            ['stat\.', 'rest\.'],
        ];

        $twoPartTitleAbbreviationsRegExp = '';
        foreach ($twoPartTitleAbbreviations as $i => $twoPartTitleAbbreviation) {
            $twoPartTitleAbbreviationsRegExp .= ($i ? '|' : '') . $twoPartTitleAbbreviation[0] . ' ?' . $twoPartTitleAbbreviation[1] . ',?';
        }

        $this->twoPartTitleAbbreviationsRegExp = '(' . $twoPartTitleAbbreviationsRegExp . ')';

        // Other languages?
        $this->inReviewRegExp = '[Ii]n [Rr]eview';
        $this->abbreviationsUsedAsInitials = '(Ch|Mª|Wm|Yu|Zh)';  // including Yu is problematic because it is also a comoplete name

        $this->oclcLabelRegExp = 'OCLC:? ';
        $this->oclcNumberRegExp = '[0-9]+';
   }
}