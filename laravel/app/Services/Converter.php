<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Models\City;
use App\Models\Conversion;
use App\Models\DictionaryName;
use App\Models\ExcludedWord;
//use App\Models\Journal;
use App\Models\Name;
use App\Models\Publisher;
use App\Models\JournalWordAbbreviation;

use App\Traits\AuthorPatterns;
use App\Traits\MakeScholarTitle;
use App\Traits\Months;
use App\Traits\Stopwords;
use App\Traits\StringCleaners;
use App\Traits\StringExtractors;
use App\Traits\Utilities;

//use SebastianBergmann\Type\NullType;

//use function Safe\strftime;

class Converter
{
    var $accessedRegExp1;
    var $bookTitleAbbrevs;
    var $cities;
    var $detailLines;
    var $dictionaryNames;
    var $distinctiveJournalWordAbbreviations;
    var $editedByRegExp;
    var $editionWords;
    var $editorStartRegExp;
    var $edsNoParensRegExp;
    var $edsParensRegExp;
    var $edsOptionalParensRegExp;
    var $edsRegExp;
    var $entryPrefixes;
    var $entrySuffixes;
    var $excludedWords;
    var $fullThesisRegExp;
    var $inWords;
    var $inRegExp;
    var $inRegExp1;
    var $inRegExp2;
    var $inReviewRegExp1;
    var $inReviewRegExp2;
    var $inReviewRegExp3;
    var $isbnLabelRegExp;
    var $isbnNumberRegExp;
    var $issnRegExps;
    var $itemType;
    var $journalNames;
    var $masterRegExp;
    var $monthsRegExp;
    var $monthsAbbreviationsRegExp;
    var $names;
    var $oclcLabelRegExp;
    var $oclcNumberRegExp;
    var $ordinals;
    var $pagesRegExp;
    var $pageRegExp;
    var $pageRegExpWithPp;
    var $pagesRegExpWithPp;
    var $pageWordsRegExp;
    var $phdRegExp;
    var $publishers;
    var $retrievedFromRegExp1;
    var $retrievedFromRegExp2;
    var $seriesRegExp;
    var $journalWordAbbreviations;
    var $startPagesRegExp;
    var $thesisRegExp;
    var $translatedByRegExp;
    var $translatorRegExp;
    var $volumeRegExp;
    var $volumeAndCodesRegExp;
    var $volumeNumberPagesRegExp;
    var $volumeNumberYearRegExp;
    var $volumeWithNumberRegExp;

    use AuthorPatterns;
    use MakeScholarTitle;
    use Months;
    use Stopwords;
    use StringCleaners;
    use StringExtractors;
    use Utilities;

    public Dates $dates;
    public ArticlePubInfoParser $articlePubInfoParser;
    public AuthorParser $authorParser;
    public PublisherAddressParser $publisherAddressParser;
    public TitleParser $titleParser;

    public function __construct()
    {
        $this->dates = new Dates();
        $this->authorParser = new AuthorParser();
        $this->articlePubInfoParser = new ArticlePubInfoParser();
        $this->publisherAddressParser = new PublisherAddressParser();
        $this->titleParser = new TitleParser();

        // Words that are in dictionary but are abbreviations in journal names
        $this->excludedWords = ExcludedWord::all()->pluck('word')->toArray();

        // Words that are in dictionary but are names
        $this->dictionaryNames = DictionaryName::all()->pluck('word')->toArray();

        // Journals with distinctive names (not single words like Science and Nature)
        // The names are ordered by string length, longest first, so that if one journal name
        // is a subset of another, the longer name is detected 
        // $this->journalNames = Journal::where('distinctive', 1)
        //     ->where('checked', 1)
        //     ->orderByRaw('CHAR_LENGTH(name) DESC')
        //     ->pluck('name')
        //     ->toArray();
        /*
         * The number of journals is huge, and an array of all of them seems unweildy.
         * Further, if a journal name is in the database and an item contains a journal 
         * with a name that is not in the database but is a superset of the one that is,
         * the shorter name gets assigned to the item.  Checking for that seems like it
         * is pretty much equivalent to determining what part of the string is the journal
         * name, which means the array of existing journal names is not useful.
         */
        $this->journalNames = [];

        // Abbreviations used as the first words of journal names (like "J." or "Bull.")
        $journalWordAbbreviations = JournalWordAbbreviation::where('checked', 1);

        $this->distinctiveJournalWordAbbreviations = $journalWordAbbreviations->where('distinctive', 1)->pluck('word')->toArray();
        $this->journalWordAbbreviations = $journalWordAbbreviations->pluck('word')->toArray();

        $this->cities = City::where('distinctive', 1)
            ->where('checked', 1)
            ->orderByRaw('CHAR_LENGTH(name) DESC')
            ->pluck('name')
            ->toArray();

        $this->publishers = Publisher::where('distinctive', 1)
            ->where('checked', 1)
            ->orderByRaw('CHAR_LENGTH(name) DESC')
            ->pluck('name')
            ->toArray();

        $this->names = Name::all()->pluck('name')->toArray();

        // Introduced to facilitate a variety of languages, but the assumption that the language of the 
        // citation --- though not necessarily of the reference itself --- is English pervades the code.
        $this->ordinals = [
            'en' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'cz' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'fr' =>
                ['1er', '2e', '3e', '4e', '5e', '6e', '7e', '8e', '9e', '10e'],
            'es' =>
                ['1ra', '2da', '3ra', '4ra', '5ra', '6ra', '7ra', '8ra', '9ra', '10ra', '1º', '2º', '3º', '4º', '5º', '6º', '7º', '8º', '9º'],
            'pt' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '1ª', '2ª', '3ª', '4ª', '5ª', '6ª', '7ª', '8ª', '9ª'],
            'my' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'nl' =>
                ['1e', '2e', '3e', '4e', '5e', '6e', '7e', '8e', '9e', '10e'],
        ];

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

        // $this->inRegExp = '([IiEe][nm]| ...)';
        // Following does not use ":?" because may want to match colon if it is present
        // $this->inRegExp1 = '/^([IiEe][nm]:|[IiEe][nm]| ... )/';
        // $this->inRegExp2 = '/( [IiEe][nm]: |[,.] [IiEe][nm] | [IiEe][nm]\) | ... )/';
        foreach ($inWords as $i => $inWord) {
            $this->inRegExp .= ($i ? '|' : '(') . $inWord;
            $this->inRegExp1 .= ($i ? '|' : '/^(') . $inWord . ': |' . $inWord . ' ';
            $this->inRegExp2 .= ($i ? '|' : '/(') . ' ' . $inWord . ': |[,.] ' . $inWord . ' | ' . $inWord . '\) ';
        }
        $this->inRegExp .= ')';
        $this->inRegExp1 .= ')/';
        $this->inRegExp2 .= ')/';

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

        $edsRx1 = $edsRx2 = '';
        foreach ($editorWords as $i => $editorWord) {
            $edsRx1 .= ($i ? '|' : '') . $editorWord;
            //$edsRx2 .= ($i ? '|' : '') . ' ' . $editorWord . ' | [(\[]' . $editorWord . '[)\]]';
            $edsRx2 .= ($i ? '|' : '') . '[(\[ ]' . $editorWord . '[)\],. ]';
        }

        // "[Ee]ditors?" or ...
        $this->edsNoParensRegExp = '(' . $edsRx1 . ')';
        // "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsParensRegExp = '[(\[](' . $edsRx1 . ')[)\]]';
        // "[Ee]ditors?" or "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsOptionalParensRegExp = '[(\[]?(' . $edsRx1 . ')[)\]]?';
        // " [Ee]ditors? " or "([Ee]ditors?)" or "[[Ee]ditors?]" or ...
        $this->edsRegExp = '/(' . $edsRx2 . ')/u';

        $editedByRx = '';
        foreach ($editedByWords as $i => $editedByWord) {
            $editedByRx .= ($i ? '|' : '') . $editedByWord;
        }

        $this->editedByRegExp .= '(' . $editedByRx . ')';

        $this->editorStartRegExp = '/^[(\[]?(' . $editedByRx . '|' . $edsRx1 . ')/u';

        $translatorWords = [
            '[Tt]ranslators?',
            '[Tt]rans\.(?! by)',
            '[Tt]rad\.',
            '[Tt]r\.(?! by)',
            'Çev\.',      // Turkish
        ];

        $translatorRx = '';
        foreach ($translatorWords as $i => $translatorWord) {
            $translatorRx .= ($i ? '|' : '') . $translatorWord;
        }

        $this->translatorRegExp .= '(' . $translatorRx . ')';

        $translatedByWords = [
            '[Tt]ranslat(ed|ion) by',
            '[Tt]ransl?\. by',
            '[Tt]r\.(?! by)',
            '[Tt]r\.? by',
            '[Tt]raducción de',   // Spanish
            '[Tt]raducido por',   // Spanish
            'Übersetzt von',      // German
            'Übersetzung von',    // German
            '[Tt]raduzido por',   // Portuguese
            '[Tt]radução por',    // Portuguese
            '[Tt]raduit par',     // French
            '[Tt]raduction par',  // French
        ];

        $translatedByRx = '';
        foreach ($translatedByWords as $i => $translatedByWord) {
            $translatedByRx .= ($i ? '|' : '') . $translatedByWord;
        }

        $this->translatedByRegExp .= '(' . $translatedByRx . ')';

        /////////////
        // Edition //
        /////////////

        $this->editionWords = [
            'edition', 
            'ed', 
            'edn', 
            'edição', 
            'édition', 
            'edición',
        ];

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
            '[Ss]s?\. ?',     // Turkish, Polish, German
            '[Ss]tr\. ?',     // Czech
            'стр\. ?',        // Russian
            'С\. ?',          // Russian
            'გვ\. ?',         // Georgian
        ];

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
        $this->volumeAndCodesRegExp = $volumeRegExp . '|{\\\bf |\\\textbf{|\\\textit{|\*';
        $this->volumeWithNumberRegExp = '(' . $this->volumeRegExp . ') ?(\\textit\{|\\textbf\{)?[1-9][0-9]{0,4}';
    
        $this->volumeNumberPagesRegExp = '/(' . $this->volumeRegExp . ')?[0-9]{1,4} ?(' . $this->numberRegExp . ')?[ \(][0-9]{1,4}[ \)]:? ?(' . $this->pageRegExp . ')/u';

        $this->volumeNumberYearRegExp = '/(' . $this->volumeAndCodesRegExp . ')? ?\d{1,4},? ?(' . $this->numberRegExp . ')? ?\d{1,4} [\(\[]?' . $this->yearRegExp . '[\)\]]/u';

        ////////////
        // Series //
        ////////////

        $seriesPhrases = [
            '(?<![Tt]ime )[Ss]eries',
            '[Ll]ecture [Nn]otes',
            'Graduate [Tt]exts',
            'Graduate Studies',
            'Grundlehren der mathematischen Wissenschaften',
            'Monographs',
            'Pure and Applied Mathematics',
            'Tracts',
        ];

        $this->seriesRegExp = '';
        foreach ($seriesPhrases as $i => $seriesPhrase) {
            $this->seriesRegExp .= ($i ? '|' : '') . $seriesPhrase . ' ?';
        }

        ////////////
        // Thesis //
        ////////////

        $thesisWords = [
            '[Tt]hesis',
            '[Tt]esis',
            '[Tt]hèse',
            '[Tt]ese',
            '{Tt]ezi',
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
            '[Dd]oktora',
        ];

        $phdRegExp = '';
        foreach ($phdWords as $i => $phdWord) {
            $phdRegExp .= ($i ? '|' : '') . $phdWord . ' ?';
        }

        $this->phdRegExp = $phdRegExp;
        //$this->phdRegExp = 'Ph[Dd]|Ph\. ?D\.?|[Dd]octoral|[Dd]oktora';
        
        $fullThesisWords = [
            '((' . $this->phdRegExp . '|' . $this->masterRegExp . ') (' . $thesisRegExp . '))',
            '[Tt]hèse de doctorat',           // French
            '[Tt]hèse de master',             // French
            'Thèse de Doctorat en droit',     // French
            'Thèse de Doctorat en droit public', // French
            '[Tt]esis doctoral',              // Spanish
            '[Dd]isertación [Dd]octoral',     // Spanish
            '[Tt]esis de grado',              // Spanish
            '[Tt]esis de [Mm]aestría',        // Spanish
            '[Dd]isertación de [Ll]icenciatura', // Spanish
            '[Tt]ese de [Dd]outorado',        // Portuguese
            '[Tt]ese \([Dd]outorado\)',       // Portuguese
            '[Dd]issertação de [Mm]estrado',  // Portuguese
            '[Dd]issertação \([Mm]estrado\)', // Portuguese
            '[Tt]ese de [Mm]estrado',         // Portuguese
            '[Dd]octoraal [Pp]roefschrift',   // Dutch
            '[Mm]asterproef',                 // Dutch
            '[Dd]oktorská práce',             // Czech
            '[Dd]iplomová práce',             // Czech
            '[Yy]ayımlanmamış doktora tezi',  // Turkish
            '[Dd]oktora [Tt]ezi',             // Turkish
            '[Yy]üksek [Ll]isans [Tt]ezi',    // Turkish
            '[Yy]ükseklisans [Tt]ezi',        // Turkish
        ];

        $fullThesisRegExp = '(';
        foreach ($fullThesisWords as $i => $fullThesisWord) {
            $fullThesisRegExp .= ($i ? '|' : '') . $fullThesisWord . ' ?';
        }
        $fullThesisRegExp .= ')';

        $this->fullThesisRegExp = $fullThesisRegExp;
        //$this->fullThesisRegExp = '(((' . $this->phdRegExp . '|' . $this->masterRegExp . ') ([Tt]hesis|[Tt]esis|[Dd]iss(ertation|\.)))|[Tt]hèse de doctorat|[Tt]hèse de master|Tesis doctoral|Disertación Doctoral|Tesis de grado|Tesis de maestría|Tese de doutorado|Tese \(doutorado\)|Dissertação de Mestrado|Dissertação \(Mestrado\)|Tese de mestrado|Doctoraal proefschrift|Masterproef|Doktorská práce|Diplomová práce|[Tt]ezi|Yayımlanmamış doktora tezi|Doktora Tezi|Yüksek lisans tezi|Yükseklisans Tezi)';
        // my: မဟာဘွဲ့စာတမ်း | ပါရဂူစာတမ်း

        ///////////
        // Other //
        ///////////

        $this->inReviewRegExp1 = '/[Ii]n [Rr]eview\.?\)?$/';
        $this->inReviewRegExp2 = '/^[Ii]n [Rr]eview/';
        $this->inReviewRegExp3 = '/(\(?[Ii]n [Rr]eview\.?\)?)$/';

        $this->isbnLabelRegExp = 'ISBN(-(10|13))?:? ?';
        // ISBN should not have spaces, but allow them.  (ISBN has 10 or 13 digits.)
        $this->isbnNumberRegExp = '[0-9X -]{10,17}';

        $issnNumberFormat = '[0-9]{4}-[0-9]{3}[0-9X]';
        $this->issnRegExps = [
            '/[Oo]nline(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[Pp]rint(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[( ,]ISSN(:|: | )' . $issnNumberFormat . ' ?[( ](print|digital)(\) ?| |$)(' . $issnNumberFormat . ' ?[( ](print|digital)(\)| |$))?/',
            '/[( ,](e-|p-)?ISSN(:|: | )(\(?(online|digital|print)[) ])?' . '(e-?|p-?)?' . $issnNumberFormat . '(\)|,|.| |$)/',
        ];

        $this->oclcLabelRegExp = 'OCLC:? ';
        $this->oclcNumberRegExp = '[0-9]+';

        $this->bookTitleAbbrevs = ['Proc', 'Amer', 'Conf', 'Cont', 'Sci', 'Int', "Auto", 'Symp'];

        // Used in: "*Retrieved from* (site)? <url> accessed <date>"
        // and "*Retrieved from* (site)? <url> <date>?"
        $this->retrievedFromRegExp1 = [
            'en' => '(Retrieved from:? |Available( online)? ?(at|from)?:? )',
            'cz' => '(Dostupné z:? |načteno z:? )',
            'fr' => '(Récupéré sur |Disponible( (à l\'adresse|sur))?:? )',
            'es' => '(Obtenido de |Disponible( en)?:? )',
//            'id' => '(Diambil kembali dari )',
            'pt' => '(Disponível( em)?:? |Obtido de:? )',
            'my' => '(Retrieved from |Available( at)?:? )',
            'nl' => '(Opgehaald van |Verkrijgbaar( bij)?:? |Beschikbaar op |Available( at)?:? )',
        ];

        // Dates are between 8 and 23 characters long (13 de novembre de 2024,)
        $dateRegExp = '[a-zA-Z0-9,/\-\. ]{8,23}';

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

        $this->monthsRegExp = $this->dates->monthsRegExp;
        $this->monthsAbbreviationsRegExp = $this->dates->monthsAbbreviationsRegExp;

        // These string are removed from the start of an entry.
        $this->entryPrefixes = ["\\noindent", "\\smallskip", "\\item", "\\bigskip"];
        // These string are removed from the end of an entry.
        // (If "[J]" means the item is a journal article, that info could be used.)
        $this->entrySuffixes = ["\\", "[J].", "[J]"];
    }

    ///////////////////////////////////////////////////
    //////////////// MAIN METHOD //////////////////////
    ///////////////////////////////////////////////////

    // If, after removing numbers etc. at start, entry is empty, return null.
    // Otherwise return array with components
    //   'source': original entry
    //   'item': converted entry
    //   'label'
    //   'itemType'
    //   'warnings'
    //   'notices'
    //   'details': array of text lines
    // $language, $charEncoding: if set, overrides values in $conversion (used when admin converts examples)
    public function convertEntry(string $rawEntry, Conversion $conversion, string|null $language = null, string|null $charEncoding = null, string|null $use = null, $previousAuthor = null): array|null
    {
        $warnings = $notices = [];
        $this->detailLines = [];
        $this->itemType = null;

        $item = new \stdClass();
        $item->note = '';
        $itemKind = null;
        $itemLabel = null;

        $isArticle = $containsPageRange = $containsProceedings = false;

        $language = $language ?: $conversion->language;
        $charEncoding = $charEncoding ?: $conversion->char_encoding;
        $use = $use ?: $conversion->use;
        $bst = $conversion->bst;

        $phrases = $this->phrases[$language];

        // Concatenate lines in entry, removing comments.
        // (Do so before cleaning text, otherwise \textquotedbleft, e.g., at end of line will not be cleaned.)
        $entryLines = explode("\n", $rawEntry);

        $entry = '';
        foreach ($entryLines as $line) {
            $truncated = $conversion->percent_comment ? $this->uncomment($line) : false;
            $entry .= $line . ($truncated ? '' : ' ');
        }

        // If nothing is left, return.
        if (! $entry) {
            return null;
        }

        // If entry has HTML markup that will be useful, translate it to TeX
        $entry = str_replace(['<em>', '</em>'], ['\textit{', '}'], $entry);

        // Remove remaining HTML markup
        $entry = strip_tags($entry);

        // Save original entry, to return as 'source'
        $originalEntry = $entry;

        // Note that cleanText translates « and », and „ and ”, to `` and ''.
        $entry = $this->cleanText($entry);
        $entry = $this->regularizeSpaces($entry);

        if ($charEncoding == 'utf8') {
            $entry = $this->utf8ToTeX($entry);
        }
        
        if ($language == 'my') {
            $entry = $this->burmeseNumeralsToDigits($entry);
        }

        $entry = str_replace(['[Google Scholar]', '[PubMed]', '[Green Version]', '[CrossRef]'], '', $entry);

        // Replace "\' " with "\'" because "\' abc" is equivalent to "\'abc", and the space causes problems if it is within a name.
        $entry = str_replace("\' ", "\'", $entry);

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        // If entry starts with year, extract it.                                                           //
        // Otherwise extract label, if any, and remove numbers and other stray characters at start of entry //
        //////////////////////////////////////////////////////////////////////////////////////////////////////

        if (preg_match('/^(?P<year>' . $this->yearRegExp . ')\*? (?P<remainder>.*)$/', $entry, $matches)) {
            $year = $matches['year'];
            $remainder = ltrim($matches['remainder'], ' |*+');
            $this->verbose(['item' => $entry]);
            $this->setField($item, 'year', $year, 'setField 1');
        } else {
            // interpret string like [Arrow12] or {Arrow12} (must contain at least one letter) at start of entry as label
            if (preg_match('/^[\[{](?P<label>[\p{L}0-9:]{3,10})[\]}] (?P<entry>.*)$/u', $entry, $matches)) {
                if ($matches['label'] && preg_match('/\p{L}/u', $matches['label'])) {
                    $itemLabel = $matches['label'];
                    $entry = $matches['entry'] ?? '';
                }
            }

            // If whole entry is is quotes ("), remove them.
            if (substr($entry, 0, 1) == '"' && substr($entry, -1) == '"') {
                $entry = substr($entry, 1, strlen($entry) - 2);
            }

            // Remove numbers and other symbols at start of entry, like '6.' or '[14]'.
            $entry = ltrim($entry, ' .0123456789[]()|*+:^');

            // If entry starts with '\bibitem [abc] {<label>}', get <label> and remove '\bibitem' and arguments.
            if (preg_match('/^\\\bibitem *(\[[^\]]*\])? *{(?P<label>[^}]*)}(?P<entry>.*)$/', $entry, $matches)) {
                if ($matches['label'] && ! $conversion->override_labels) {
                    $itemLabel = $matches['label'];
                }
                $entry = $matches['entry'];
            }

            // Remove members of $this->entryPrefixes from start of entry
            foreach ($this->entryPrefixes as $entryPrefix) {
                $entry = Str::replaceStart($entryPrefix, '', $entry);
            }

            // If nothing is left, return.
            if (! strlen($entry)) {
                return null;
            }

            // Don't put the following earlier---{} may legitimately follow \bibitem
            $entry = str_replace("{}", "", $entry);

            // Remove members of $this->entrySufixes from end of entry
            foreach ($this->entrySuffixes as $entrySuffix) {
                $entry = Str::replaceEnd($entrySuffix, '', $entry);
            }

            // If entry starts with [n] or (n) for some number n, eliminate it
            $entry = preg_replace("/^\s*\[\d*\]|^\s*\(\d*\)/", "", $entry);

            $entry = ltrim($entry, ' {,');
            $entry = rtrim($entry, ' }');

            $this->verbose(['item' => $entry]);
            if ($itemLabel) {
                $this->verbose(['label' => $itemLabel]);
            }

            $remainder = $entry;
        }

        $completeEntry = $remainder;

        ////////////////////
        // Get doi if any //
        ////////////////////

        $containsUrlAccessInfo = false;

        $urlRegExp = '(\\\url{|\\\href{)?(?P<url>https?://\S+)(})?';

        $retrievedFromRegExp1 = $this->retrievedFromRegExp1[$language];
        $retrievedFromRegExp2 = $this->retrievedFromRegExp2[$language];
        $accessedRegExp1 = $this->accessedRegExp1[$language];

        $urlDate = null;
        // doi in a Markdown-formatted url
        if (preg_match('%\[https://doi\.org/(?P<doi>[^ \]]+)\]\(https://doi\.org/(?P<doi1>[^ \)]+)\)%', $remainder, $matches)) {
            $doi = $matches['doi'];
            $doi1 = $matches['doi1'];
            if ($doi != $doi1) {
                $warnings[] = "doi's in URL are not the same.";                
            }
            $remainder = str_replace($matches[0], '', $remainder);
        }

        // Case of URL that is also link to doi --- record both doi and URL
        if (
            preg_match(
                '%(?P<retrievedFrom> ' . $retrievedFromRegExp2 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<note> .*)?$%iJ',
                $remainder,
                $matches1,
            )
            &&
            preg_match(
                '%^https?://doi.org/(?P<doi>[^ ]+)%',
                $matches1['url'],
                $matches2,
            )
           ) {
            $doi = $matches2['doi'];
            $remainder = substr($remainder, 0, -strlen($matches1[0]));
            $remainder = str_replace('[online]', '', $remainder);
            $retrievedFrom = $matches1['retrievedFrom'] ?? null;
            $date1 = $matches1['date1'] ?? null;
            $date2 = $matches1['date2'] ?? null;
            $date = $date1 ?: $date2;
            $siteName = (! empty($matches['siteName']) && $matches1['siteName'] != '\url{') ? $matches1['siteName'] : null;
            $url = $matches1['url'] ?? null;
            $note = $matches1['note'] ?? null;

            $dateResult = $this->dates->isDate(trim($date, ' .,'), $language, 'contains');

            if ($dateResult) {
                $accessDate = $dateResult['date'];
                $urlDate = rtrim($accessDate, '., ');
                $containsUrlAccessInfo = true;
            }
        }

        // Optional space in segment
        // \\\url{)?https?://doi\.org/ ?
        // is designed to deal with an erroneous space.
        if (empty($doi)) {
            //preg_match('/(?P<precedingChar>.)doi:/', $remainder, $matches);
            //$precedingChar = $matches['precedingChar'] ?? null;
            $doi = $this->extractLabeledContent(
                $remainder,
                ' [\[)]?doi:? | [\[(]?doi: ?|doi:|(' . $retrievedFromRegExp1 . ')?(\\\href\{|\\\url{)?https?://dx\.doi\.org/|(' . $retrievedFromRegExp1 . ')?(\\\href\{|\\\url{)?https?://doi\.org[/:] ?(?=10)|doi\.org',
                '[^ ]+'
            );
        }

        // Every doi starts with '10.'.  URL may be something like https://doi-something.univ.edu.
        if (empty($doi)) {
            preg_match('%(' . $retrievedFromRegExp1 . ')?https?://[a-zA-Z\-\.]*/(?P<doi>10\.[^ ]+)%', $remainder, $matches);
            if (isset($matches['doi'])) {
                $doi = $matches['doi'];
                $remainder = str_replace($matches[0], '', $remainder);
            } 
        }

        // doi might not be labelled at all: look for '10.' followed by at least four digits and then a slash and some more characters
        if (empty($doi)) {
            preg_match('% (?P<doi>10\.[0-9]{4,}/[^ ]{5,})([ .]|$)%', $remainder, $matches);
            if (isset($matches['doi'])) {
                $doi = rtrim($matches['doi'], '.');
                $remainder = str_replace($matches[0], '', $remainder);
            }
        }

        if (substr_count($doi, ')') > substr_count($doi, '(') && substr($doi, -1) == ')') {
            $doi = substr($doi, 0, -1);
        }

        if (substr_count($doi, '}') > substr_count($doi, '{') && substr($doi, -1) == '}') {
            $doi = substr($doi, 0, -1);
        }

        // In case item says 'doi: https://...' or 'doi:doi...'
        $doi = Str::replaceStart('https://doi.org/', '', $doi);
        $doi = Str::replaceStart('doi.', '', $doi);
        $doi = rtrim($doi, ']');
        $doi = ltrim($doi, '/:');

        // escape underscores for latex bst that requires it
        if ($use == 'latex' && $bst && (! $bst->doi || $bst->doi_escape_underscore)) {
            $doi = preg_replace('/([^\\\])_/', '$1\_', $doi);
        }

        // In case doi is repeated, as in \href{https://doi.org/<doi>}{<doi>} (in which case second {...} will remain)
        $remainder = str_replace('{\tt ' . $doi . '}', '', $remainder);
        $remainder = str_replace('{' . $doi . '}', '', $remainder);

        if ($urlDate) {
            if ($use != 'latex' || ($bst && $bst->urldate)) {
                $this->setField($item, 'urldate', $urlDate, 'setField 2');
            } else {
                $this->addToField($item, 'note', 'Retrieved ' . $urlDate . '.', 'addToField 2a');
            }
        }

        if ($doi) {
            if ($use != 'latex' || ($bst && $bst->doi)) {
                $this->setField($item, 'doi', $doi, 'setField 3');
            } else {
                $this->addToField($item, 'note', 'doi:' . $doi . '.', 'addToField 2b');
            }
            $hasDoi = true;
        } else {
            $this->verbose("No doi found.");
            $hasDoi = false;
        }

        ///////////////////////////////
        // Get PMID and PMCID if any //
        ///////////////////////////////

        $pmCodePatterns = ['pmid: [0-9]{6,9}', 'pmcid: [A-Z]{1,4}[0-9]{6,9}'];

        foreach ($pmCodePatterns as $pmCodePattern) {
            if (preg_match('/^(?P<before>.*)(?P<pmcode>' . $pmCodePattern . ')(?P<after>.*)$/i', $remainder, $matches)) {
                $this->addToField($item, 'note', $matches['pmcode'], 'addToField 1a');
                $remainder = $matches['before'] . '' . $matches['after'];
                $remainder = trim($remainder, ' .;');
            }
        }

        //////////////////////////////////////
        // Get arXiv or bioRxiv info if any //
        //////////////////////////////////////

        $hasArxiv = false;
        $hasFullDate = false;

        preg_match('/ arxiv( preprint)?[:,]( art no\.)? ?(?P<afterArxiv>.*)$/i', $remainder, $matches1, PREG_OFFSET_CAPTURE);
        if (isset($matches1['afterArxiv'])) {
            $hasArxiv = true;
            $dateResult = $this->dates->isDate($matches1['afterArxiv'][0], $language, 'starts');
            if ($dateResult) {
                $this->setField($item, 'archiveprefix', 'arXiv', 'setField 4');
                $this->setField($item, 'year', $dateResult['year'], 'setField 5');
                $this->setField($item, 'date', $dateResult['year'] . '-' . (strlen($dateResult['monthNumber']) == 1 ? '0' : '') . $dateResult['monthNumber'] . '-' . $dateResult['day'], 'setField 6');
                $hasFullDate = true;
                $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($dateResult['date']));
            } else {
                if (preg_match('/^(?P<year>' . $this->yearRegExp . ')[,.]? /', $matches1['afterArxiv'][0], $matches2)) {
                    $this->setField($item, 'archiveprefix', 'arXiv', 'setField 7');
                    $this->setField($item, 'year', $matches2['year'], 'setField 8');
                    $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($matches2[0]));
                } else {
                    preg_match('/^\S+/', $matches1['afterArxiv'][0], $eprintMatches, PREG_OFFSET_CAPTURE);
                    $eprint = $eprintMatches[0][0] ?? null;
                    if ($eprint) {
                        $this->setField($item, 'archiveprefix', 'arXiv', 'setField 9');
                        $this->setField($item, 'eprint', rtrim($eprint, '}.,'), 'setField 10');
                        $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($eprint));
                    }
                }
            }
        }

        $eprint = $this->extractLabeledContent($remainder, '([Ii]n|{\\\em)? bioRxiv[ }]?', '\S+ \S+');

        if ($eprint) {
            $this->setField($item, 'archiveprefix', 'bioRxiv', 'setField 11');
            $this->setField($item, 'eprint', trim($eprint, '()'), 'setField 12');
            $hasArxiv = true;
        }

        ////////////////////////////////////
        // Get url and access date if any //
        ////////////////////////////////////

        $remainder = preg_replace('/ ?[\(\[](online|en ligne|internet)[\)\]]/i', '', $remainder, 1, $replacementCount);
        $onlineFlag = $replacementCount > 0;

        $patterns = [
            // Retrieved from (site)? <url> accessed <date>
            '%(?P<retrievedFrom> ' . $retrievedFromRegExp1 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '[.,]? \[?' . $accessedRegExp1 . '\]?$%i',
            // Retrieved from (site)? <url> <date>?
            '%(?P<retrievedFrom> ' . $retrievedFromRegExp1 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<date1> .*)?$%i',
            // Retrieved <date>? from <url> <note>?
            '%(?P<retrievedFrom> ' . $retrievedFromRegExp2 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<note> .*)?$%iJ',
            // <url> accessed <date>
            '%(url: ?)?' . $urlRegExp . ',? ?\(?' . $accessedRegExp1 . '\)?\.?$%i',
            // accessed <date> <url>
            '%' . $accessedRegExp1 . '\.? ' . $urlRegExp . '$%i',
            // accessed <date> [no url]
            '%' . $accessedRegExp1 . '\.?$%i',
            // \href{<url>}{<note>}
            '%' . $urlRegExp . '{(?P<note>.*)}\.?$%i',
            // <url> <note>
            '%(url: ?)?' . $urlRegExp . '(?P<note>.*)$%i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $remainder, $matches)) {
                break;
            }
        }

        if (count($matches)) {
            $remainder = substr($remainder, 0, -strlen($matches[0]));
            $remainder = str_replace('[online]', '', $remainder);
            $retrievedFrom = $matches['retrievedFrom'] ?? null;
            $date1 = $matches['date1'] ?? null;
            $date2 = $matches['date2'] ?? null;
            $date = $date1 ?: $date2;
            $siteName = (! empty($matches['siteName']) && $matches['siteName'] != '\url{') ? $matches['siteName'] : null;
            $url = $matches['url'] ?? null;
            $note = $matches['note'] ?? null;
            $trimmedNote = $note == null ? '' : trim($note);
            if ($this->dates->isYear($trimmedNote)) {
                $this->setField($item, 'year', $trimmedNote, 'setField 13');
                $note = null;
            }

            $dateResult = $date ? $this->dates->isDate(trim($date, ' .,'), $language, 'contains') : null;

            if ($dateResult) {
                $accessDate = $dateResult['date'];
            }
        }

        $urlHasPdf = false;

        // Write access date even if there is no URL.  (Presumably "accessed ..." means it is in fact an online item.)
        if (! empty($accessDate)) {
            if ($use != 'latex' || ($bst && $bst->urldate)) {
                $this->setField($item, 'urldate', rtrim($accessDate, '.,]) '), 'setField 14');
            } else {
                $this->addToField($item, 'note', 'Retrieved ' . rtrim($accessDate, '.,]) ') . '.', 'addToField 2c');
            }
            $containsUrlAccessInfo = true;
        }

        if (! empty($url)) {
            $url = trim($url, '{)}],. ');
            if ($use != 'latex' || ($bst && $bst->url)) {
                $this->setField($item, 'url', $url, 'setField 15');
            } else {
                $this->addToField($item, 'note', $url, 'addToField 2e');
            }
            if (Str::endsWith($url, ['.pdf'])) {
                $urlHasPdf = true;
            }
            if (! empty($year)) {
                $this->setField($item, 'year', $year, 'setField 16');
            }
            if (! empty($siteName)) {
                $this->addToField($item, 'note', trim(trim($retrievedFrom . $siteName, ':,; ') . $note, '{}'), 'addToField 2');
            } elseif ($note) {
                $this->addToField($item, 'note', $note, 'addToField 2a');
            }
        } else {
            $this->verbose("No url found.");
        }

        /////////////////////
        // Get ISBN if any //
        /////////////////////

        $containsIsbn = false;
        $match = $this->extractLabeledContent($remainder, ' \(?' . $this->isbnLabelRegExp, $this->isbnNumberRegExp . '\)?');
        if ($match) {
            $containsIsbn = true;
            $isbn = trim(str_replace(' ', '', $match), '()');
            if ($use != 'latex' || ($bst && $bst->isbn)) {
                $this->setField($item, 'isbn', $isbn, 'setField 17');
            } else {
                $this->addToField($item, 'note', 'ISBN: ' . $isbn, 'addToField 2f');
            }
        }
        
        /////////////////////
        // Get ISSN if any //
        /////////////////////

        $containsIssn = false;

        foreach ($this->issnRegExps as $issnRegExp) {
            preg_match($issnRegExp, $remainder, $matches);
            if (isset($matches[0])) {
                // trim '(' and ')' from '(ISSN 1234-5678)' but not from 'ISSN 1234-5678 (digital)'
                if (substr_count(ltrim($matches[0], '('), '(') < substr_count($matches[0], ')')) {
                    $issn = trim($matches[0], ')');
                } else {
                    $issn = $matches[0];
                }
                $issn = trim($issn, '(., ');
                if ($use != 'latex' || ($bst && $bst->issn)) {
                    $this->setField($item, 'issn', trim(Str::after($issn, 'ISSN'), ': '), 'setField 17a');
                } else {
                    $this->addToField($item, 'note', $issn, 'addToField 2a');
                }
                $remainder = str_replace($matches[0], '', $remainder);
                $containsIssn = true;
                break;
            } 
        }

        /////////////////////
        // Get OCLC if any //
        /////////////////////

        $match = $this->extractLabeledContent($remainder, ' ' . $this->oclcLabelRegExp, $this->oclcNumberRegExp);
        if ($match) {
            $this->setField($item, 'oclc', $match, 'setField 18');
        }

        //////////////////////////
        // Get Epub info if any //
        //////////////////////////

        $result = $this->findRemoveAndReturn($remainder, '(Epub ahead of print|Epub \d{4} [A-Za-z]+ \d{1,2}\.?)');
        if ($result !== false) {
            $this->addToField($item, 'note', rtrim($result[0], '.') . '.', 'addToField 2b');
        }

        ////////////////////////
        // Get chapter if any //
        ////////////////////////

        $match = $this->extractLabeledContent($remainder, ' chapter ', '[1-9][0-9]?');
        if ($match) {
            $this->setField($item, 'chapter', $match, 'setField 19');
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // If remainder ends with phrase like "first published" or "originally published", remove it and put it in Note field //
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $containsOriginalPubDate = false;
        if (preg_match('/(?P<note>\(?([Ff]irst|[Oo]riginally) published (in )?[0-9]{4}\.?\)?\.?)$/', $remainder, $matches)) {
            $this->addToField($item, 'note', ' ' . trim($matches['note'], '(). ') . '.', 'setField 20');
            $containsOriginalPubDate = true;
            $remainder = trim(substr($remainder, 0, -strlen($matches[0])), '() ');
        }

        ///////////////////////////////////////
        // Split remaining string into words //
        ///////////////////////////////////////

        // Exploding on spaces isn't exactly right, because a word containing an accented letter
        // can have a space in it --- e.g. Oblo{\v z}insk{\' y}.  So perform a more sophisticated explosion.

        // To find authors and title, look only up to the point at which some characters have been removed (as a doi
        // or url, for example).  strspn trick to find index of first difference between two strings from w3resource.com.
        // E.g.
        // completeEntry: Smith, J., A theory of something, doi:1234, 2021
        // doi was removed, so
        // remainder: Smith, J., A theory of something, 2021
        // For authors and title, want to look only at
        // Smith, J., A theory of something,

        $completeRemainder = $remainder;
        $mismatchPosition = strspn($completeEntry ^ $remainder, "\0");
        $remains = substr($remainder, 0, $mismatchPosition);

        // Put space between , and ` or ' not followed by space (assumed to be typo).
        // (So ",' " does not get a space added --- as in "'word', ".)
        $remains = str_replace([',`', '( ', ' )'], [', `', '(', ')'], $remains);
        $remains = preg_replace("/,'([^ ])/", ", '$1", $remains);
        //$remains = str_replace([",'"], [", '"], $remains);
        // Replace ",'A" with ",' A", where A is anything except ' or a space
        //$remains = preg_replace('/,\\\'([^\' ])/', ', \\\'$1', $remains);

        // If & is followed immediately by capital letter, put a space between them (assumed to be error)
        $remains = preg_replace('/ &([A-Z])/', ' & $1', $remains);

        $chars = str_split($remains);

        // If period or comma is not followed by space, another period, a comma, a semicolon, a dash, a quotation mark
        // or (EITHER is followed by a digit and the next character is not a colon OR is followed by a lowercase letter)
        // (might be within a URL --- the "name" when citing a web page), treat it as ending
        // word.
        $word = '';
        $words = [];
        $skip = 0;
        foreach ($chars as $i => $char) {
            if ($skip) {
                $skip--;
            } elseif ($char == '.' && isset($chars[$i+4]) && $chars[$i+1] . $chars[$i+2] . $chars[$i+3] . $chars[$i+4] == ' . .') {
                // change '. . .' to '...'
                $words[] = '...';
                $word = '';
                if (isset($chars[$i+5]) && $chars[$i+5] == ' ') {
                    $skip = 5;
                } else {
                    $skip = 4;
                }
            } elseif ($char == '.' && isset($chars[$i+2]) && $chars[$i+1] . $chars[$i+2] == '..') {
                // if '...' isn't followed by space, add a space
                if ($word) {
                    $words[] = $word;
                }
                $words[] = '...';
                $word = '';
                if (isset($chars[$i+3]) && $chars[$i+3] == ' ') {
                    $skip = 3;
                } else {
                    $skip = 2;
                }
            } elseif (
                // Don't add space after period in something like 'M.24'.
                in_array($char, ['.', ',']) 
                && (! isset($chars[$i-1]) 
                    || (
                    $chars[$i-1] != '\\' 
                        && ! (in_array($chars[$i-1], range('A', 'Z')) && isset($chars[$i+1]) && in_array($chars[$i+1], range(0, 9))))
                   )
                && isset($chars[$i+1]) 
                && ! in_array($chars[$i+1], [' ', '.', ',', ';', '-', '"', "'"]) 
                && ! (isset($chars[$i-1]) && ctype_digit($chars[$i-1]) && ctype_digit($chars[$i+1]))
                && ((in_array($chars[$i+1], range('0', '9')) && (! isset($chars[$i+2]) || $chars[$i+2] != ':'))
                        || mb_strtolower($chars[$i+1]) != $chars[$i+1])
                )
            {
                $word .= $char;
                $words[] = $word;
                $word = '';
            } elseif ($char != ' ' || (isset($chars[$i-3]) && $chars[$i-3] == '{' && $chars[$i-2] == '\\')) {
                $word .= $char;
            } else {
                $words[] = $word;
                $word = '';
            }
        }

        if ($word) {
            $words[] = $word;
        }

        if (isset($words[0]) && $words[0] == '\\it') {
            array_shift($words);
        }

        ////////////////////////////////////////
        // Check for presence of journal name //
        ////////////////////////////////////////

        // Has to be done *after* string is split into words, because that process adds spaces 
        // after periods, converting U.S.A. into U. S. A., for example
        $journal = null;
        $containsJournalName = false;
        $wordString = ' ' . implode(' ', $words);
        foreach ($this->journalNames as $name) {
            // Precede journal name by space or {, so that subsets of journal names are not matched (e.g. JASA and EJASA).
            // { is allowed because journal name might be preceded by \textit{.
            if (Str::contains($wordString, [' ' . $name, '{' . $name])) {
                $journal = $name;
                $containsJournalName = true;
                $this->verbose("Entry contains the name of a journal (" . $journal . ").");
                break;
            }
        }

        if (! $containsJournalName && Str::contains($wordString, [' ' . $this->journalWord . ' ', 'Frontiers in ', 'Annals of '])) {
            $containsJournalName = true;
        }

        //////////////////////
        // Look for authors //
        //////////////////////

        $isEditor = false;
        $isTranslator = false;

        // Burmese (custom format)
        if ($language == 'my') {
            preg_match('/^(?P<author>[^,]*, ?[^,]*), ?(?P<remainder>.*)$/', $remainder, $matches);
            $authorConversion = ['authorstring' => $matches['author'], 'warnings' => []];
            array_shift($words);
            array_shift($words);
            $year = trim($words[0], '(),');
            array_shift($words);
            $isEditor = false;
            $month = $day = $date = null;
            $itemKind = 'book';
            $remainder = implode(' ', $words);
        // Entry starts with two more more _'s or -'s => author is one in previous entry
        } elseif (isset($words[0]) && preg_match('/^[_-]{2,}[.,]?$/', $words[0])) {
            $authorConversion = ['authorstring' => $previousAuthor, 'warnings' => []];
            $month = $day = $date = null;
            $isEditor = false;
            array_shift($words);
            if (isset($words[0]) && preg_match('/^[\(\[]?(?P<year>' . $this->yearRegExp . ')[a-z]?[\)\]]?$/', rtrim($words[0], '.,'), $matches)) {
                $year = $matches['year'];
                array_shift($words);
            } else {
                $year = null;
            }
            $remainder = implode(' ', $words);
        } else {
            if (isset($words[0])) {
                $words[0] = ltrim($words[0], '_-');
            }
            $authorConversion = $this->authorParser->convertToAuthors(
                $words, 
                $remainder, 
                $year, 
                $month, 
                $day, 
                $date, 
                $isEditor, 
                $isTranslator, 
                $this->translatorRegExp,
                $this->cities, 
                $this->dictionaryNames, 
                true, 
                'authors', 
                $language
            );
            $this->detailLines = array_merge($this->detailLines, $authorConversion['author_details']);
        }

        $authorIsOrganization = $authorConversion['organization'] ?? false;

        // restore rest of $completeRemainder
        if (isset($completeRemainder[$mismatchPosition]) && $completeRemainder[$mismatchPosition] == '.') {
            $remainder = $remainder . substr($completeRemainder, $mismatchPosition);
        } else {
            $remainder = $remainder . ' ' . substr($completeRemainder, $mismatchPosition);
        }

        $itemYear = $year;
        $itemMonth = $month;
        $itemDay = $day;
        $itemDate = $date;

        $authorstring = $authorConversion['authorstring'];

        foreach ($authorConversion['warnings'] as $warning) {
            $warnings[] = $warning;
        }
        $authorstring = trim($authorstring, ',: ');
        $authorstring = $this->trimRightBrace($authorstring);
        if ($authorstring && $authorstring[0] == '{') {
            $authorstring = strstr($authorstring, ' ');
        }

        if ($month) {
            $monthResult = $this->dates->fixMonth($month, $language);
            $this->setField($item, 'month', $monthResult['months'], 'setField 21');
            if ($day) {
                $this->setField($item, 'date', $year . '-' . $monthResult['month1number'] . '-' . (strlen($day) == 1 ? '0' : '') . $day, 'setField 22');
                $hasFullDate = true;
            }
        }

        //////////////////////////
        // Fix up $authorstring //
        //////////////////////////

        $editorPhrases = [
            '(' . $phrases['editor'] . ')',
            '(' . $phrases['editors'] . ')',
            '(' . $phrases['ed.'] . ')',
            '(' . $phrases['eds.'] . ')'
        ];

        if (preg_match('/^\(eds?\.?\)[.,;]? (?P<remainder>.*)$/', $remainder, $matches)) {
            $isEditor = true;
            $remainder = $matches['remainder'] ?? '';
        }

        if (Str::contains($authorstring, $editorPhrases)) {
            $isEditor = true;
        }

        if (preg_match('/^\((trans|trad)\.?\)[.,;]? (?P<remainder>.*)$/', $remainder, $matches)) {
            $isTranslator = true;
            $remainder = $matches['remainder'] ?? '';
        }

        $multipleAuthors = false;
        if (preg_match('/ and /', $authorstring)) {
            $multipleAuthors = true;
        }

        if ($isEditor) {
            $this->setField($item, 'editor', trim(str_replace($editorPhrases, "", $authorstring), ' ,'), 'setField 24');
            if ($isTranslator) {
                $noteString = $multipleAuthors ? 'Editors are translators.' : 'Editor is translator.';
                $this->addToField($item, 'note', $noteString, 'setField 23a');
            }
        } elseif ($isTranslator) {
            $this->setField($item, 'author', rtrim($authorstring, ','), 'setField 23b');
            $noteString = $multipleAuthors ? 'Authors are translators.' : 'Author is translator.';
            $this->addToField($item, 'note', 'Author is translator.', 'setField 23c');
        } else {
            // For name like "Ash‘arī, Abū al-Ḥasan al-."
            if (Str::endsWith($authorstring, '-.')) {
                $authorstring = rtrim($authorstring, '.');
            }
            $authorstring = rtrim($authorstring, ',\\');
            // If author is organization, name contains a space, and use is either latex or biblatex, put author in braces
            if ($authorIsOrganization && strpos($authorstring, ' ') !== false && in_array($use, ['latex', 'biblatex'])) {
                $authorstring = '{' . $authorstring . '}';
            }
            $this->setField($item, 'author', $authorstring, 'setField 23d');
        }

        $hasSecondaryDate = false;
        if ($year) {
            // If $year ends with ) and contains no (, remove ) at end.
            if (Str::endsWith($year, ')') && strpos($year, '(') === false) {
                $year = substr($year, 0, -1);
            }
            $this->setField($item, 'year', $year, 'setField 25');
            if (preg_match('/[(\[]?[0-9]{4}[)\]]? \[[0-9]{4}\]/', $year)) {
                $hasSecondaryDate = true;
            }
        }

        ////////////////////////////
        // Get page count, if any //
        ////////////////////////////

        $remainder = trim($remainder);

        $pageCount = null;
        // pp cannot be followed by digit, because string could be "vol 10 pp. 20-30".  (Period after (pp?pgs) has to be optional, but 
        // then need it also in the (?! expression.)
        if (preg_match('/^(?P<before>.*?)(,? (?P<pageCount>[0-9ivx +\[\]]+(pp|pgs)\.?(?!\.? [1-9])))(?P<after>.*?)$/', $remainder, $matches)) {
            $pageCount = $matches['pageCount'] ?? null;
            $remainder = ($matches['before'] ?? '') . ' ' . ($matches['after'] ?? '');
        }

        $remainder = trim($remainder, '.},;/ ');

        $this->verbose("[1] Remainder: " . $remainder);

        ////////////////////
        // Look for title //
        ////////////////////

        $remainder = ltrim($remainder, ': ');

        $title = null;
        $titleEndsInPeriod = false;
        $titleStyle = '';
        $containsEdition = false;
        $containsSeries = false;
        $seriesString = null;

        // Does title start with "Doctoral thesis:" or something like that?
        $containsThesis = false;
        if (preg_match('/^' . $this->fullThesisRegExp . '(: | -)(?P<remainder>.*)$/iu', $remainder, $matches)) {
            $containsThesis = true;
            $remainder = $matches['remainder'] ?? '';
        }

        // First deal with the (rare) case of a title in {...}
        // Exclude case in which remainder begins {\ ...
        if (isset($remainder[0]) && isset($remainder[1]) && $remainder[0] == '{' && $remainder[1] != '\\') {
            $before = ltrim(Str::before($remainder, '}'), '{');
            // exclude canse in which $before is all uppercase, in which case {} may delimit string that has to be uppercase
            if (mb_strtoupper($before) != $before) {
                $title = $before;
                $newRemainder = Str::after($remainder, '}');
            }
        }
        
        if (! $title) {
            $title = $this->getQuotedOrItalic($remainder, true, false, $before, $after, $titleStyle);
            $this->verbose('Title is ' . ($titleStyle == 'none' ? 'not styled' : 'styled (' . $titleStyle . ')'));
            $newRemainder = $before . ($after ? ltrim($after, "., ") : '');
        }

        // Custom format for Burmese
        if ($language == 'my') {
            $title = (string) $title;
            $this->setField($item, 'title', trim($title, ', '), 'setField 26');
            $quoteCount = substr_count($newRemainder, '"');
            if ($quoteCount == 4) {
                preg_match('/^"(?P<edition>[^"]+)"(?P<remainder>.*)$/', $newRemainder, $matches);
                if (isset($matches['edition'])) {
                    $this->setField($item, 'edition', trim($matches['edition'], ', '), 'setField 27');
                }
                $newRemainder = $remainder = isset($matches['remainder']) ? trim($matches['remainder']) : '';
            }
        } else {
            // If title has been found and ends in edition specification, take that out and put it in edition field
            $editionRegExp = '/(\(' . $this->editionRegExp . '\)$|' . $this->editionRegExp . ')[.,]?$/iJ';
            if ($title && preg_match($editionRegExp, (string) $title, $matches)) {
                $this->setField($item, 'edition', trim($matches['edition'], ',. '), 'setField 28');
                $title = trim(Str::replaceLast($matches[0], '', $title));
            }

            if (! $title) {
                $originalRemainder = $remainder;
                $result = $this->titleParser->getTitle(
                    $remainder, 
                    $edition, 
                    $volume, 
                    $isArticle, 
                    $year, 
                    $note, 
                    $journal, 
                    $containsUrlAccessInfo, 
                    $this->publishers, 
                    $this->distinctiveJournalWordAbbreviations, 
                    $this->excludedWords, 
                    $this->cities,
                    $this->dictionaryNames,
                    $this->pagesRegExp, 
                    $this->pageRegExp, 
                    $this->startPagesRegExp, 
                    $this->fullThesisRegExp,
                    $this->edsOptionalParensRegExp,
                    $this->monthsRegExp,
                    $this->inRegExp,
                    $this->volumeRegExp,
                    $this->volumeAndCodesRegExp,
                    $this->seriesRegExp,
                    $this->edsNoParensRegExp,
                    $this->translatorRegExp,
                    $this->translatedByRegExp,
                    false, 
                    $language
                );

                $title = $result['title'];
                $containsSeries = $result['seriesNext'] ?? false;
                if ($containsSeries) {
                    $seriesString = $result['stringToNextPeriodOrComma'];
                }

                $this->detailLines = array_merge($this->detailLines, $result['titleDetails']);

                if (substr($originalRemainder, strlen($title), 1) == '.') {
                    $titleEndsInPeriod = true;
                }

                if (! isset($item->year) && $year) {
                    $this->setField($item, 'year', $year, 'setField 29');
                    $remainder = str_replace($year, '', $remainder);
                }
                if ($edition) {
                    $this->setField($item, 'edition', $edition, 'setField 30');
                    $containsEdition = true;
                }
                if ($volume) {
                    $this->setField($item, 'volume', $volume, 'setField 31');
                }
                if ($result['editor']) {
                    if ($use != 'latex') {
                        $editor = rtrim($result['editor'], ', ');
                        if (! Str::endsWith($editor, 'et al.')) {
                            $editor = rtrim($result['editor'], '., ');
                        }
                        $this->setField($item, 'editor', $editor, 'setField 31a');
                    } else {
                        $this->addToField($item, 'note', 'Edited by ' . $result['editor'], 'addToField 4a');
                    }
                }
                if ($result['translator']) {
                    $translator = trim($result['translator'], ',& ');
                    if (! Str::endsWith($translator, 'et al.')) {
                        $translator = trim($result['translator'], '.,& ');
                    }
                    if ($use != 'latex' || ($bst && $bst->translator)) {
                        $this->setField($item, 'translator', $translator, 'setField 31a');
                    } else {
                        $this->addToField($item, 'note', 'Translated by ' . $translator, 'addToField 4a');
                    }
                }
                if ($note) {
                    $note = ltrim($note, ' (');
                    $note = rtrim($note, ') ');
                    $this->addToField($item, 'note', $note, 'addToField 4');
                }

                $newRemainder = $remainder;
            }

            $newRemainder = rtrim($newRemainder, ' .(');
            $remainder = $newRemainder;

            if (preg_match('/^(?P<before>[^.]*)\.(?P<after>.*)$/', $title, $matches)) {
                $beforePeriod = $matches['before'];
                $afterPeriod = $matches['after'];
                if (preg_match('/(' . $this->seriesRegExp . ') /', $afterPeriod, $afterMatches)) {
                    $title = $beforePeriod;
                    // Series string may contain volume number, in boldface
                    $series = $this->removeFontStyle($afterPeriod, 'bold');
                    $this->setField($item, 'series', trim($series), 'setField 31b');
                }
            }

            if (substr($title, -2) == "''" && substr_count($title, '``') == 0) {
                $title = substr($title, 0, -2); 
            }

            // The - is in case it is used as a separator
            if (! $titleEndsInPeriod) {
                $titleEndsInPeriod = substr($title, -1) == '.';
            }
            $title = rtrim($title, ' .,-');
            // Remove '[J]' at end of title (what does it mean?)
            if (preg_match('/\[(J|A)\]$/', $title)) {
                $title = substr($title, 0, -3);
            }

            $title = trim($title, '_- ');

            if (substr($title, 0, 1) == '{' && substr($title, -1) == '}') {
                $title = trim($title, '{}');
            }
            if (substr($title, 0, 1) == '*' && substr($title, -1) == '*') {
                $title = trim($title, '*');
            }
            // Case in which quotation marks were in wrong encoding and appear as ?'s.
            if (substr($title, 0, 1) == '?' && substr($title, -1) == '?') {
                $title = trim($title, '?., ');
            }

            $title = trim($title, ' :');
            $title = Str::replaceEnd('[C]', '', $title);
            $this->setField($item, 'title', $title, 'setField 32');
        }

        $this->verbose("[2] Remainder: " . $remainder);

        ///////////////////////////////////////////////////////////
        // Look for year if not already found                    //
        // (may already have been found at end of author string) //
        ///////////////////////////////////////////////////////////

        // Look for volume-number-year pattern, to use later when determining item type
        $containsVolumeNumberYear = false;
        if (preg_match($this->volumeNumberYearRegExp, $remainder)) {
            $containsVolumeNumberYear = true;
            $this->verbose("Contains volume-number-year string");
        }

        $remainderWithMonthYear = $remainder;
        $containsMonth = false;
        if (! isset($item->year)) {
            if (! $year) {
                // Space prepended to $remainder in case it starts with year, because getDate requires space 
                // (but perhaps could be rewritten to avoid it).
                $year = $this->dates->getDate(' ' . $remainder, $newRemainder, $month, $day, $date, false, true, false, $language);
            }

            if ($year) {
                $this->setField($item, 'year', $year, 'setField 33');
            } else {
                $this->setField($item, 'year', '', 'setField 34');
                $warnings[] = "No year found.";
            }

            if (isset($month)) {
                $containsMonth = true;
                $monthResult = $this->dates->fixMonth($month, $language);
                $this->setField($item, 'month', $monthResult['months'], 'setField 35');
            }

            if ($year && isset($month) && ! empty($day)) {
                $day = strlen($day) == 1 ? '0' . $day : $day;
                $this->setField($item, 'date', $year . '-' . $monthResult['month1number'] . '-' . $day, 'setField 36');
                $hasFullDate = true;
            }

            if (isset($item->url) && ! isset($item->urldate) && $day) {
                if ($use != 'latex' || ($bst && $bst->urldate)) {
                    $this->setField($item, 'urldate', $date, 'setField 37');
                } else {
                    $this->addToField($item, 'note', 'Retrieved ' . $date . '.', 'addToField 2d');
                }
            }
        }

        $yearIsForthcoming = isset($item->year) && preg_match('/^([Ff]orthcoming|[Ii]n [Pp]ress)$/', $item->year);

        $remainder = ltrim($newRemainder, ' ');

        ///////////////////////////////////////////////////
        // Put "Translated by ..." in note or translator //
        // name in translator field.                     //
        // (Not extracted earlier, because the string    //
        // "translated by" may appear in the title.)     //
        ///////////////////////////////////////////////////

        $containsTranslator = false;
        // Editors and translators, or just translators: string up to first period preceded by a lowercase letter.
        // (For an incollection, editors would be not be signified in this way.)
        // Case of "Trans." is handled later, after item type is determined, because "Trans." is an abbreviation used in journal names.
        // (?<=[.,] )
        $result = preg_match('/^(?P<before>.*)(?P<editedAndTranslatedBy>([Ee]d(ited|\.) and [Tt]rans(lated|\.)|[Tt]rans(lated|\.) and [Ee]d(ited|\.)) by) (?P<translator>.*?\p{Ll})(\),?|\.| \()(?P<after>.*)$/', $remainder, $matches);
        if (! $result) {
            $result = preg_match('/^(?P<before>.*?)(^| |\()(?P<translatedBy>(and )?' . $this->translatedByRegExp . ') (?P<translator>.*?\p{Ll})(\),?|\.| \()(?P<after>.*)$/', $remainder, $matches);
        }
        if ($result && isset($matches['translator'])) {
            $translator = $matches['translator'];
            if (Str::endsWith($translator, 'et al')) {
                $translator .= '.';
            }
            $setEditor = isset($matches['editedAndTranslatedBy']);
            $translatedBy = $setEditor ? $matches['editedAndTranslatedBy'] : $matches['translatedBy'];

            if ($use != 'latex' || ($bst && $bst->translator)) {
                $this->setField($item, 'translator', $translator, 'setField 36a');
            } else {
                $this->addToField($item, 'note', $translatedBy . ' ' . $translator, 'addToField 18b');
            }

            if ($setEditor) {
                if ($use == 'latex') {
                    $this->addToField($item, 'note', 'Edited by ' . $translator, 'addToField 18');
                    $notices[] = "BibTeX allows an author OR an editor for a book, but not both, so note about editor added.  (BibLaTeX allows both.)";
                } else {
                    $this->setField($item, 'editor', $translator, 'setField 18a');
                }
            }

            $before = $matches['before'] ?? '';
            $remainder = $before . (! Str::endsWith($before, ['.', '. ']) ? '. ' : '') . ($matches['after'] ?? '');
            $containsTranslator = true;
        }

        ///////////////////////////////////////////////////////////////////////////////
        // To determine type of item, first record some features of publication info //
        ///////////////////////////////////////////////////////////////////////////////

        // $remainder is item minus authors, year, and title
        $remainder = ltrim($remainder, '.,;,() ');
        $this->verbose("[type] Remainder: " . $remainder);
        
        $inStart = $containsIn = $italicStart = $containsEditors = $allWordsInitialCaps = false;
        $containsNumber = $containsInteriorVolume = $containsCity = $containsPublisher = false;
        $containsWorkingPaper = $containsFullThesis = false;
        $containsNumberedWorkingPaper = $containsNumber = $pubInfoStartsWithForthcoming = $pubInfoEndsWithForthcoming = false;
        $containsNumberOutsidePages = false;
        $endsWithInReview = false;
        $containsDigitOutsideVolume = true;
        $containsNumberDesignation = false;
        $containsVolumeNumberPages = false;
        $startsAddressPublisher = $endsAddressPublisher = false;
        $cityLength = 0;
        $publisherString = $cityString = '';

        // Remainder could be journal name without any volume-page info (e.g. "Nutrients" or "Ophthalmol Glaucoma.")
        // OR name of newspaper (e.g. The Washington Post)
        // but also could be name of publisher (without any address) (e.g. Cortez).
        if (preg_match('/^([A-Z][A-Za-z]+ ){0,3}[A-Z][A-Za-z]+$/', rtrim($remainder, '. '))) {
            $allWordsInitialCaps = true;
            $this->verbose("Consists only of letters and spaces");
        }

        if (preg_match($this->inRegExp1, $remainder)) {
            $inStart = true;
            $this->verbose("Starts with variant of \"in\".");
        }

        if (preg_match($this->inRegExp2, $remainder)) {
            $containsIn = true;
            $this->verbose("Contains variant of \"in\".");
        }

        if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
            $italicStart = true;
            $this->verbose("Starts with italics.");
        }

        if (preg_match('/\d/', $remainder) || preg_match('/ [IVXDLC]+[., ]/', $remainder)) {
            $containsNumber = true;
            $this->verbose("Contains a number.");
        }

        if (preg_match('/ (' . $this->monthsRegExp[$language] . ')[,. ]/', $remainder, $matches)) {
            $containsMonth = true;
            $this->verbose("Contains a month name.");
        }

        // Contains volume designation, but not at start of $remainder
        if (preg_match('/[ \(\[](' . $this->volumeAndCodesRegExp . ') ?\d/', substr($remainder, 3))) {
            $containsInteriorVolume = true;
            $this->verbose("Contains a volume, but not at start.");
        }

        if (preg_match($this->volumeNumberPagesRegExp, $remainder)) {
            $containsVolumeNumberPages = true;
            $this->verbose("Contains volume-number-pages info.");
        }

        // Contains volume designation
        if (preg_match('/(^|,? )(' . $this->volumeRegExp . ') ?(\\textit\{|\\textbf\{)?\d/', $remainder, $matches)) {
            $match = $matches[0];
            if ($match) {
                $this->verbose("Contains a volume designation.");
                $remainderMinusVolume = $this->findAndRemove($remainder, $this->volumeWithNumberRegExp);
                $containsDigitOutsideVolume = Str::contains($remainderMinusVolume, range('0','9'));
                if ($containsDigitOutsideVolume) {
                    $this->verbose('Contains digits outside the volume designation');
                } else {
                    $this->verbose('Contains no digits outside the volume designation');
                }
            }
        }

        // Test for 1st, 2nd etc. in third preg_match is to exclude a string like '1st ed.' (see Exner et al. in examples)
        // Would be more natural to write "$ordinal . '\.?'" in the $regExp and use only one preg_match, but an optional
        // character is not supported for negative lookbehind.
        // Either starts with 'eds' 
        // OR contains 'ed.,' or 'eds.,' or 'editor,' or 'editors,' not preceded by '1st', '2nd', ... OR by '1st.', '2nd.', ...  
        // OR contains ', ed.' or ', eds.' or ', editor' or ', editors' not preceded by '1st', '2nd', ... OR by '1st.', '2nd.', ...  
        // (Idea is that it contains '<name>, ed.' or ', ed. <name>')
        // Note that 'Ed.' may appear in a journal name.
        $regExpStart = '/^[Ee]ds?\.|(?<!';

        $regExp11 = $regExp12 = $regExp21 = $regExp22 = $regExpStart;
        foreach ($this->ordinals[$language] as $i => $ordinal) {
            $add = ($i ? '|' : '') . $ordinal;
            $regExp11 .= $add;
            $regExp12 .= $add;
            $regExp21 .= $add . '\.';
            $regExp22 .= $add . '\.';
        }

//        $edPattern = '[ (}]([Ee]ds?\.|[Ee]ditors?|[Ee]dited by)';
//dump($edPattern);
        $edPattern = '[ (}](' . $this->edsNoParensRegExp . '|' . $this->editedByRegExp . ')';
//        $edPattern = '[ (}]' . $this->edsNoParensRegExp;
//dump($edPattern);
        $regExpEnd1 = '|rev\.)' . $edPattern . ',/';
        $regExpEnd2 = '|rev\.), ?' . $edPattern . '/';

        // $regExpEnd1 = '|rev\.)[ \(}](eds?\.|editors?|edited by),/i';
        // $regExpEnd2 = '|rev\.), ?[ \(}](eds?\.|editors?|edited by)/i';

        $regExp11 .= $regExpEnd1;
        $regExp12 .= $regExpEnd2;
        $regExp21 .= $regExpEnd1;
        $regExp22 .= $regExpEnd2;

        if (
            preg_match('/' . $this->edsParensRegExp . '/u', $remainder)
            ||
            preg_match('/' . $this->editedByRegExp . '/', $remainder)
            || 
            (
                preg_match($regExp11, $remainder, $matches)
                && preg_match($regExp21, $remainder, $matches)
            )
            ||
            (
                preg_match($regExp12, $remainder, $matches)
                && preg_match($regExp22, $remainder, $matches)
            )
           ) {
            $containsEditors = true;
            $this->verbose("Contains editors.");
        }

        // If string like '15: 245:267' is found, '245:267' is assumed to be a page range, and ':' is replaced with '-'
        if (preg_match('/([1-9][0-9]{0,3})(, |\(| | \(|\.|: )([1-9][0-9]{0,3})(:)[1-9][0-9]{0,3}\)?/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
            $this->verbose("Page separator: : (at position " . $matches[4][1] . ")");
            $remainder = substr($remainder, 0, $matches[4][1]) . '-' . substr($remainder, $matches[4][1] + 1);
            $this->verbose("Replacing ':' with '-' in page range.  Remainder is now: " . $remainder);
        }

        $regExp = '/' . $this->workingPaperRegExp . $this->workingPaperNumberRegExp . '/i';
        if (preg_match($regExp, $remainder, $workingPaperMatches, PREG_OFFSET_CAPTURE)) {
            if ($italicStart) {
                // Remove italic code at start and recompute $workingPaperMatches (used later)
                foreach ($this->italicCodes as $italicCode) {
                    $remainder = Str::replaceStart($italicCode, '', $remainder);
                    preg_match($regExp, $remainder, $workingPaperMatches, PREG_OFFSET_CAPTURE);
                }
            }
            $containsNumberedWorkingPaper = true;
            $this->verbose("Contains string for numbered working paper.");
        }

        if (preg_match('/' . $this->workingPaperRegExp . '/i', $remainder)) {
            $containsWorkingPaper = true;
        }

        if (substr_count($remainder, '\\#')) {
            $containsNumber = true;
            $this->verbose("Contains number sign (\\#).");
        }

        $regExp = '/(';
        foreach ($this->ordinals[$language] as $i => $ordinal) {
            $regExp .= ($i ? '|' : '') . $ordinal . '\.?';
        }
        $regExp .= ') ed(ition|ição)?(\.| )/i';
        if (preg_match('/ ed(ition|ição)([,.:; )]|$)/i', $remainder) || preg_match($regExp, $remainder)) {
            $containsEdition = true;
        }

        if ($containsEdition) {
            $this->verbose("Contains string for an edition.");
        }

        if (preg_match('/' . $this->fullThesisRegExp . '/iu', $remainder)) {
            $containsFullThesis = true;
            $this->verbose("Contains full thesis.");
        }

        if (preg_match('/' . $this->thesisRegExp . '/u', $remainder)) {
            $containsThesis = true;
            $this->verbose("Contains thesis.");
        }

        $testString = $inStart ? Str::after($remainder, ' ') : $remainder;
        if ($this->isProceedings($testString)) {
            $containsProceedings = true;
            $this->verbose("Contains a string suggesting conference proceedings.");
        }

        // if (preg_match('/[ :][1-9][0-9]{0,3} ?-{1,2} ?[1-9][0-9]{0,3}([\.,\} ]|$)/', $remainder)
        //         ||
        //         preg_match('/([1-9][0-9]{0,3}|p\.)(, |\(| | \(|\.|: )([1-9][0-9]{0,3})(-[1-9][0-9]{0,3})?\)?/', $remainder)) {
        //     $containsPageRange = true;
        //     $this->verbose("Contains page range.");
        // }
        if (
            preg_match('/[ :]' . $this->pageRange . '([\.,\} ]|$)/', $remainder) 
            ||
            preg_match('/' . $this->pagesRegExp . '/', $remainder)
           ) {
            $containsPageRange = true;
            $this->verbose("Contains page range.");
        }

        $remainderMinusPages = $this->findAndRemove($remainder, $this->pagesRegExp);
        if (preg_match('/\d/', $remainderMinusPages)) {
            $containsNumberOutsidePages = true;
        }

        if (preg_match('/' . $this->startForthcomingRegExp . '/i', $remainder)) {
            $pubInfoStartsWithForthcoming = true;
            $this->verbose("Publication info starts with 'forthcoming', 'accepted', 'in press', or 'to appear'.");
        }

        if (preg_match('/' . $this->endForthcomingRegExp . '/i', $remainder)) {
            $pubInfoEndsWithForthcoming = true;
            $this->verbose("Publication info ends with 'forthcoming', 'accepted', or 'in press', or 'to appear'.");
        }

        if (preg_match($this->inReviewRegExp1, $remainder)
                || preg_match($this->inReviewRegExp2, $remainder)) {
            $endsWithInReview = true;
            $this->verbose("Starts with or ends with 'in review' string.");
        }

        if (! $hasSecondaryDate) {
            preg_match('/[Ff]irst published ' . $this->yearRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches[0][0])) {
                $hasSecondaryDate = true;
                $this->addToField($item, 'note', $matches[0][0]);
                $remainder = rtrim(substr($remainder, 0, $matches[0][1]), ' .') . '.' . substr($remainder, $matches[0][1] + strlen($matches[0][0]));
            }
        }

        $remainderMinusPubInfo = $remainder;
        $publisher = '';
        foreach ($this->publishers as $pub) {
            $lcPub = mb_strtolower($pub);
            if (preg_match('%(^| )' . $lcPub . '([ .,;:]|$)%', mb_strtolower($remainder))) {
                $containsPublisher = true;
                $publisherString = $publisher = $pub;
                $remainderMinusPubInfo = Str::replaceFirst($publisher, '', $remainder);
                $this->verbose("Contains publisher \"" . $publisher . "\"");
                break;
            }
        }

        // Check for cities only in $remainder minus publisher, if any.
        foreach ($this->cities as $city) {
            if (Str::contains($remainderMinusPubInfo, $city)) {
                $containsCity = true;
                $afterCity = Str::after($remainderMinusPubInfo, $city);
                // Is city followed by US State abbreviation?
                $state = $this->getUsState($afterCity);

                if ($state) {
                    $cityString = trim($city . $state);
                } else {
                    $cityString = $city;
                }

                $this->verbose("Contains city \"" . $cityString . "\"");
                break;
            }
        }
        $city = null;

        // (?<address>: <publisher>(, <year>)?)?
        // if (preg_match('/^\(?[\p{L},. ]{0,25}: [\p{L}&\- ]{0,25}(, (19|20)[0-9]{2})?\)?/u', $remainder) && ! preg_match('/^Published/', $remainder)) {
        // if (preg_match('/^' . $this->addressPublisherYearRegExp . '/u', $remainder) && ! preg_match('/^Published/', $remainder)) {
        if (
            $this->isAddressPublisher(rtrim($remainder, '.'), finish: false) 
            && 
            ! preg_match('/^Published/', $remainder)
            && 
            ! preg_match('/ journal /i', $remainder)
           ) {
            $startsAddressPublisher = true;
            $this->verbose("Remainder has 'address: publisher' format.");
        }

        if (preg_match('/[a-z ]{0,25}: [a-z ]{0,35},?( ' . $this->pagesRegExp . ')?$/i', $remainder)) {
            $endsAddressPublisher = true;
            $this->verbose("Remainder ends with 'address: publisher' format (and possibly page range).");
        }

        $commaCount = substr_count($remainder, ',');
        $this->verbose("Number of commas: " . $commaCount);

        ///////////////////////////////////////////////////
        // Use features of string to determine item type //
        ///////////////////////////////////////////////////

        if ($itemKind) {
            // $itemKind is already set
        } elseif (! $hasArxiv &&
            (
                (
                    $onlineFlag &&
                    ! $containsInteriorVolume &&
                    ! $containsVolumeNumberPages &&
                    ! $containsVolumeNumberYear &&
                    ! $containsPageRange &&
                    ! $containsJournalName &&
                    ! $allWordsInitialCaps &&
                    ! $isArticle
                )
                ||
                (
                    isset($item->url) &&
                    ! $containsWorkingPaper &&
                    (! $urlHasPdf || $containsUrlAccessInfo) &&
                    ! $inStart &&
                    ! $italicStart &&
                    ! $containsInteriorVolume &&
                    ! $containsVolumeNumberPages &&
                    ! $containsVolumeNumberYear &&
                    ! $containsPageRange &&
                    ! $containsJournalName &&
                    ! $containsThesis &&
                    ! Str::contains($item->url, ['journal']) &&
                    // if remainder has address: publisher format, item is book unless author is organization
                    (! $startsAddressPublisher || $authorIsOrganization) &&
                    (! $allWordsInitialCaps || (isset($item->author) && trim($item->author, '{}') == $remainder)) &&
                    ! $isArticle
                )
            )
        ) {
            $this->verbose("Item type case 0");
            $itemKind = 'online';
        } elseif ($hasArxiv &&
            ! $containsInteriorVolume &&
            ! $containsPageRange &&
            ! $containsJournalName
        ) {
            $this->verbose("Item type case 0a");
            $itemKind = 'unpublished';
        } elseif ($containsFullThesis) {
            $this->verbose("Item type case 0b");
            $itemKind = 'thesis';
        } elseif (
            $isArticle
            ||
            $containsJournalName
            || 
            (
                $italicStart
                &&
                ($containsPageRange || $containsInteriorVolume)
                &&
                ! $containsEditors
                &&
                ! $containsProceedings
                &&
                ! $containsCity
                &&
                ! $containsPublisher
                &&
                ! $containsIsbn
            )
        ) {
            $this->verbose("Item type case 1");
            $itemKind = 'article';
        } elseif ($containsNumberedWorkingPaper || ($containsWorkingPaper && $containsNumber)) {
            $this->verbose("Item type case 2");
            $itemKind = 'techreport';
        } elseif ($containsWorkingPaper || ! $remainder) {
            $this->verbose("Item type case 3");
            $itemKind = 'unpublished';
        } elseif ($containsEditors && ($inStart || $containsPageRange) && ! $containsProceedings) {
            $this->verbose("Item type case 4");
            $itemKind = 'incollection';
        } elseif ($containsEditors && ! $containsProceedings) {
            if ($hasSecondaryDate || $containsTranslator || $containsOriginalPubDate) {
                $this->verbose("Item type case 5a");
                $itemKind = 'book'; // with editor as well as author
            } else {
                $this->verbose("Item type case 5b");
                $itemKind = 'incollection';
            }
        } elseif (
                ($containsPageRange || $containsInteriorVolume)
                && ($containsNumberOutsidePages || $containsMonth)
                && ! $containsProceedings
                && ! $containsPublisher
                && ! $containsCity
                && ! $endsAddressPublisher
                && ! $containsIsbn
                && ! $containsSeries
                ) {
            $this->verbose("Item type case 6");
            $itemKind = 'article';
            if (! $this->itemType && ! $itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [2]";
            }
        } elseif ($containsProceedings) {
            $this->verbose("Item type case 7");
            $itemKind = 'inproceedings';
        } elseif ($containsIsbn || ($titleStyle == 'italic' && ($containsCity || $containsPublisher || isset($item->editor)))) {
            $this->verbose("Item type case 8");
            $itemKind = 'book';
        } elseif (! $containsIn && ! $startsAddressPublisher && ($pubInfoStartsWithForthcoming || $pubInfoEndsWithForthcoming)) {
            $this->verbose("Item type case 9");
            $itemKind = 'article';
        } elseif ($endsWithInReview) {
            $this->verbose("Item type case 10");
            $itemKind = 'unpublished';
        } elseif ($inStart) {
            $this->verbose("Item type case 11");
            $itemKind = 'incollection';
            if (! $this->itemType && ! $itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif ($containsPublisher || $endsAddressPublisher) {
            if ((! $containsIn && ! $containsPageRange) || strlen($remainder) - $cityLength - strlen($publisher) < 30) {
                $this->verbose("Item type case 12");
                $itemKind = 'book';
            } else {
                $this->verbose("Item type case 13");
                $itemKind = 'incollection';
            }
            if (! $this->itemType && ! $itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif (! $containsNumber && ! $containsPageRange) {
            // Condition used to have 'or', which means that an article with a single page number is classified as a book
            if ($containsThesis) {
                $this->verbose("Item type case 14");
                $itemKind = 'thesis';
            } elseif (($endsWithInReview || $containsMonth) && ! $hasDoi) {
                $this->verbose("Item type case 15");
                $itemKind = 'unpublished';
            } elseif ($pubInfoEndsWithForthcoming || $pubInfoStartsWithForthcoming) {
                $this->verbose("Item type case 16");
                $itemKind = 'article';
            } elseif (! $containsEdition 
                && (
                    $titleStyle == 'quoted'
                    || $yearIsForthcoming
                    || ($allWordsInitialCaps && ($hasFullDate || $containsUrlAccessInfo))
                   )) {  //  
                // If $allWordsInitialCaps, $remainder could be journal/newspaper name (although there are no page numbers etc.)
                $this->verbose("Item type case 17a");
                $itemKind = 'article';
            } else {
                $this->verbose("Item type case 17b");
                $itemKind = 'book';
            }
        } elseif ($containsEdition) {
            $this->verbose("Item type case 18");
            $itemKind = 'book';
            if (! $this->itemType) {
                $warnings[] = "Not sure of type; contains \"edition\", so set to " . $itemKind . ".";
            }
        } elseif ($containsDigitOutsideVolume && ! $startsAddressPublisher && ! $containsSeries) {
            $this->verbose("Item type case 19");
            $itemKind = 'article';
            if (! $this->itemType) {
                $warnings[] = "Not sure of type; set to " . $itemKind . ".";
            }
        } else {
            $this->verbose("Item type case 20");
            $itemKind = 'book';
            if (! $this->itemType) {
                $warnings[] = "Not sure of type; set to " . $itemKind . ".";
            }
        }

        // Whether thesis is ma or phd is determined later
        if ($itemKind != 'thesis') {
            $this->verbose(['fieldName' => 'Item type', 'content' => $itemKind]);
        }

        unset($volume, $pages);

        // If item is not unpublished and ends with 'in review', put 'in review' in notes field and remove it from entry
        // Can this case arise?
        if ($itemKind != 'unpublished') {
            $match = $this->extractLabeledContent($remainder, '', '\(?[Ii]n [Rr]eview\.?\)?$');
            if ($match) {
                $this->addToField($item, 'note', $match, 'addToField 5');
                $this->verbose('"In review" string removed and put in note field');
                $this->verbose('Remainder: ' . $remainder);
            }
        }

        // If user is forcing type, specify it here.  The previous section can't be skipped because some of the variables defined in it
        // are used later on.
        if ($this->itemType) {
            $itemKind = $this->itemType;
        }

        $this->verbose('[3] Remainder: ' . $remainder);

        switch ($itemKind) {

            /////////////////////////////////////////////
            // Get publication information for article //
            /////////////////////////////////////////////

            case 'article':
                $journalNameMissingButHasVolume = false;
                $retainFinalPeriod = false;
                $containsNumberDesignation = false;

                // Get journal
                $remainder = ltrim($remainder, '.,; ');
                // If there are any commas not preceded by digits and not followed by digits or spaces, add spaces after them
                $remainder = preg_replace('/([^0-9]),([^ 0-9])/', '$1, $2', $remainder);

                // if starts with "in", remove it and check whether what is left starts with italics
                if (preg_match($this->inRegExp1, $remainder) && ! Str::startsWith($remainder, 'in press')) {
                    $remainder = preg_replace($this->inRegExp1, '', $remainder);
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                        $italicStart = true;
                    }
                }

                if ($journal) {
                    // Remove $journal and any surrounding italics
                    $startItalicRegExp = '';
                    foreach ($this->italicCodes as $code) {
                        $startItalicRegExp .= '|' . str_replace(['\\', '{'], ['\\\\', '\\{'], $code);
                    }
                    $startItalicRegExp = '(((' . substr($startItalicRegExp, 1) . ') *)?)';
                    // Allow periods or not in journal names
                    $remainder = preg_replace('/(' . $startItalicRegExp . $journal . ',? ?\}?)/', '', $remainder);
                } else {
                    // If does not start with italics, check whether first word is all numeric and
                    // italics starts after first word, in which case
                    // classify first word as unidentified.  (Covers case of mistaken duplication of year.)
                    if (! $italicStart) {
                        $firstWord = strtok($remainder, ' ');
                        if (preg_match('/^[0-9]*$/', $firstWord)) {
                            $remainderAfterFirstWord = trim(substr($remainder, strlen($firstWord)));
                            if ($this->containsFontStyle($remainderAfterFirstWord, true, 'italics', $startPos, $length)) {
                                $warnings[] = "[u3] The string \"" . $firstWord . "\" remains unidentified.";
                                $remainder = $remainderAfterFirstWord;
                                $italicStart = true;
                            }
                        }
                    }

                    $result = $this->articlePubInfoParser->getVolumeNumberPagesForArticle(
                        $remainder, 
                        $item, 
                        $language, 
                        $this->pagesRegExp, 
                        $this->pageWordsRegExp, 
                        $this->volumeAndCodesRegExp, 
                        true,
                    );

                    $this->detailLines = array_merge($this->detailLines, $result['pub_info_details']);

                    $containsNumberDesignation = $result['containsNumberDesignation'];

                    if ($result['result'] || preg_match('/^' . $this->volumeWithNumberRegExp . '/', $remainder)) {
                        $journalNameMissingButHasVolume = true;
                        $warnings[] = "Item seems to be article, but journal name not found.";
                    }

                    if (! $journalNameMissingButHasVolume) {
                        $journalResult = $this->articlePubInfoParser->getJournal(
                            $remainder,
                            $item, 
                            $italicStart, 
                            $pubInfoStartsWithForthcoming, 
                            $pubInfoEndsWithForthcoming, 
                            $language, 
                            $this->startPagesRegExp,
                            $this->volumeRegExp
                        );
                        $this->detailLines = array_merge($this->detailLines, $journalResult['pub_info_details']);
                        $journal = rtrim($journalResult['journal'], ' ,(');
                        $retainFinalPeriod = $journalResult['retainFinalPeriod'];
                    }
                }

                if ($journal) {
                    // For journal publications, journal name is not normally preceded by "in", but if it is, remove it.
                    if (Str::startsWith($journal, ['in: ', 'In: '])) {
                        $journal = substr($journal, 4);
                    }
                    if (Str::startsWith($journal, ['in ', 'In '])) {
                        $journal = substr($journal, 3);
                    }

                    // If $journal ends in a period:
                    // Remove period if 
                    // it terminates a word that is not a journal word abbreviation
                    // OR
                    // if the journal name excluding the last word contains at least one journal word abbreviation and none of the journal
                    // word abbreviations end in a period
                    // Journal of Public Economics. => remove period
                    // Journal of Public Econ. => do not remove period
                    // J Pub Econ. => remove period
                    // J Pub. Econ. => do not remove period
                    // J. Pub. Econ. => do not remove period
                    if (substr($journal, -1) == '.') {
                        $journalWords = explode(' ', $journal);
                        $lastJournalWord = array_pop($journalWords);
                        // Is any word before the last one an abbreviation?
                        $journalContainsInteriorAbbreviation = false;
                        $journalInteriorAbbreviationHasPeriod = false;
                        foreach ($journalWords as $word) {
                            if (in_array(rtrim($word, '.'), $this->journalWordAbbreviations)) {
                                $journalContainsInteriorAbbreviation = true;
                                if (substr($word, -1) == '.') {
                                    $journalInteriorAbbreviationHasPeriod = true;
                                }
                            }
                        }
                        if (
                            ! $retainFinalPeriod
                            &&
                            (
                                ! in_array(substr($lastJournalWord, 0, -1), $this->journalWordAbbreviations)
                                ||
                                ($journalContainsInteriorAbbreviation && ! $journalInteriorAbbreviationHasPeriod)
                            )
                        ) {
                            $journal = substr($journal, 0, -1);
                        }
                    }
                    $journal = trim($journal, '_');
                    $this->setField($item, 'journal', trim($journal, '"*,;:{}-| '), 'setField 38');
                } elseif (! $journalNameMissingButHasVolume) {
                    $warnings[] = "Item seems to be article, but journal name not found.  Setting type to unpublished.";
                    $itemKind = 'unpublished';  // but continue processing as if journal
                }
                $remainder = trim($remainder, ' ,.');
                $this->verbose("Remainder: " . $remainder);

                $volumeNumberPages = $remainder;

                if ($remainder) {
                    // No space after \bf => add one
                    $remainder = preg_replace('/\\\bf([0-9])/', '\bf $1', $remainder);

                    // If $remainder ends with 'forthcoming' phrase and contains no digits (which might be volume & number,
                    // for example, even if paper is forthcoming), put that in note.  Else look for pages & volume etc.
                    if (preg_match('/' . $this->endForthcomingRegExp . '/', $remainder) && !preg_match('/[0-9]/', $remainder)) {
                        $this->addToField($item, 'note', trim($remainder, '()'), 'addToField 6');
                        $remainder = '';
                    } else {
                        if (preg_match('/^(?P<month>' . $this->monthsRegExp[$language] . ') (?P<day>[0-9]{1,2})[.,;]/', $remainder, $matches)) {
                            $monthResult = $this->dates->fixMonth($matches['month'], $language);
                            $monthNumber = '00';
                            for ($j = 1; $j <= 12; $j++) {
                                if ($matches['m' . $j]) {
                                    $monthNumber = strlen($j) == 1 ? '0' . $j : $j;
                                    break;
                                }
                            }
                            $day = strlen($matches['day']) == 1 ? '0' . $matches['day'] : $matches['day'];
                            $this->setField($item, 'month', $monthResult['months'], 'setField 39');
                            $this->setField($item, 'date', $year . '-' . $monthNumber . '-' . $day, 'setField 40');
                            $hasFullDate = true;
                            $remainder = substr($remainder, strlen($matches[0]));
                        }

                        // Get pages
                        $result = $this->articlePubInfoParser->getVolumeNumberPagesForArticle(
                            $remainder, 
                            $item, 
                            $language, 
                            $this->pagesRegExp, 
                            $this->pageWordsRegExp,
                            $this->volumeAndCodesRegExp
                        );

                        $this->detailLines = array_merge($this->detailLines, $result['pub_info_details']);

                        $containsNumberDesignation = $result['containsNumberDesignation'];

                        $pagesReported = false;
                        if (! empty($item->pages)) {
                            $pagesReported = true;
                        }

                        $remainder = rtrim($remainder, '"');
                        $this->verbose("[p1] Remainder: " . $remainder);

                        if ($remainder) {
                            // Get month, if any
                            $months = $this->monthsRegExp[$language];
                            $regExp = '/(\(?(?P<month1>' . $months . '\)?)([-\/](?P<month2>' . $months . ')\)?)?)/iJ';
                            preg_match_all($regExp, $remainder, $matches, PREG_OFFSET_CAPTURE);

                            if (! empty($matches[0][0][0])) {
                                $month = trim($matches[0][0][0], '();');
                                $monthResult = $this->dates->fixMonth($month, $language);
                                $this->setField($item, 'month', $monthResult['months'], 'setField 41');
                                $remainder = substr($remainder, 0, $matches[0][0][1]) . ' ' . ltrim(substr($remainder, $matches[0][0][1] + strlen($matches[0][0][0])), ', )');
                                $this->verbose('Remainder: ' . $remainder);
                            }

                            if (! isset($item->volume) && ! isset($item->number)) {
                                // Get volume and number
                                $numberInParens = false;
                                $result = $this->articlePubInfoParser->getVolumeAndNumberForArticle(
                                    $remainder, 
                                    $item, 
                                    $containsNumberDesignation, 
                                    $numberInParens,
                                    $this->volumeAndCodesRegExp
                                );

                                $this->detailLines = array_merge($this->detailLines, $result['pub_info_details']);
                            }

                            $result = $this->findRemoveAndReturn($remainder, $this->articleRegExp);
                            if ($result) {
                                // If remainder contains article number, put it in the note field
                                $this->addToField($item, 'note', $result[0], 'addToField 7');
                            } elseif (empty($item->pages) && ! empty($item->number) && ! $containsNumberDesignation) {
                                // else if no pages have been found and a number has been set, assume the previously assigned number
                                // is in fact a single page
                                if (empty($numberInParens)) {
                                    $this->setField($item, 'pages', $item->number, 'setField 42');
                                    unset($item->number);
                                    $this->verbose('[p5] no pages found, so assuming string previously assigned to number is a single page: ' . $item->pages);
                                    $warnings[] = "Not sure the pages value is correct.";
                                }
                            }

                            if (! $pagesReported && ! empty($item->pages)) {
                                $this->verbose(['fieldName' => 'Pages', 'content' => $item->pages]);
                            }

                            if (! empty($item->volume)) {
                                $this->verbose(['fieldName' => 'Volume', 'content' => $item->volume]);
                            } else {
                                $warnings[] = "'Volume' field not found.";
                            }
                            if (! empty($item->number)) {
                                $this->verbose(['fieldName' => 'Number', 'content' => $item->number]);
                            }

                            if (isset($item->note)) {
                                if ($item->note) {
                                    $this->verbose(['fieldName' => 'Note', 'content' => $item->note]);
                                } else {
                                    unset($item->note);
                                }
                            }
                        }
                    }
                }

                break;

            /////////////////////////////////////////////////
            // Get publication information for unpublished //
            /////////////////////////////////////////////////

            case 'unpublished':
                $remainder = trim($remainder, '.,} ');
                if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                    $this->addToField($item, 'note', substr($remainder, $length), 'addToField 8a');
                } elseif (! preg_match('/[Ii]n(:|\.|$)/', $remainder)) {
                    $this->addToField($item, 'note', $remainder, 'addToField 8b');
                }
                $remainder = '';

                break;

            //////////////////////////
            // Fix entry for online //
            //////////////////////////

            case 'online':
                if (empty($item->month)) {
                    unset($item->month);
                }

                if (empty($item->urldate) && $itemYear && $itemMonth && $itemDay && $itemDate) {
                    if ($use != 'latex' || ($bst && $bst->urldate)) {
                        $this->setField($item, 'urldate', rtrim($itemDate, '., '), 'setField 43');
                    } else {
                        $this->addToField($item, 'note', 'Retrieved ' . rtrim($itemDate, '., ') . '.', 'addToField 2e');
                    }
                }

                $result = $this->dates->getDate($remainder, $remains, $month, $day, $date, true, true, false, $language);
                if ($result) {
                    // $remainder is date, so set it to be date of item (even if date was set earlier on basis
                    // of urldate).
                    $this->setField($item, 'year', $result, 'setField 44');
                    $this->setField($item, 'month', $month, 'setField 45');
                    $this->addToField($item, 'note', $date, 'addToField 13');
                    $remainder = $remains;
                }

                break;

            ////////////////////////////////////////////////
            // Get publication information for techreport //
            ////////////////////////////////////////////////

            case 'techreport':
                // If string before type, take that to be institution, else take string after number
                // to be institution---handles both 'CORE Discussion Paper 34' and 'Discussion paper 34, CORE'
                $type = $workingPaperMatches[1][0] ?? '';
                // Following line deals with "Report No." case
                $type = trim(Str::replaceLast(' No.', '', $type));
                if ($type) {
                    $this->setField($item, 'type', $type, 'setField 46');
                }

                $number = $workingPaperMatches[3][0] ?? '';
                if ($number) {
                    $this->setField($item, 'number', $number, 'setField 47');
                }

                // Institution could conceivably be 3-letter acronym, but not shorter?  (Note that first character of
                // string from which $workingPaperMatches was extracted might be '(', so definitely need condition > 1, at least.)
                if (isset($workingPaperMatches[0][1]) && $workingPaperMatches[0][1] > 2) {
                    // Chars before 'Working Paper'
                    $this->setField($item, 'institution', trim(substr($remainder, 0, $workingPaperMatches[0][1] - 1), ' .,'), 'setField 48');
                    $remainder = trim(substr($remainder, $workingPaperMatches[3][1] + strlen($number)), ' .,');
                } else {
                    // No chars before 'Working paper'---so take string after number to be institution
                    $n = $workingPaperMatches[3][1] ?? 0;
                    $remainder = trim(substr($remainder, $n + strlen($number)), ' .,;');
                    // Are pages referred to next?  If so, put them in a note.
                    if (preg_match('/^(?P<note>[Pp]p?\.? [0-9]+( ?--? ?[0-9]+)?)(?P<remainder>.*)/', $remainder, $matches)) {
                        $this->addToField($item, 'note', $matches['note']);
                        $remainder = trim($matches['remainder'], '()., ');
                    }
                    $this->setField($item, 'institution', $remainder, 'setField 49');
                    $remainder = '';
                }
                if (empty($item->institution)) {
                    $warnings[] = "Mandatory 'institition' field missing";
                }
                $notices[] = "Check institution.";

                break;

            ////////////////////////////////////////////////////////////////////
            // Get publication information for incollection and inproceedings //
            ////////////////////////////////////////////////////////////////////

            case 'incollection':
            case 'inproceedings':
                $leftover = '';
                $this->verbose("[in1a] Remainder: " . $remainder);
                $wordsBeforeEds = [];
                $beforeEds = '';
                $afterEds = '';
                $publisherPosition = false;
                $remainderContainsEds = false;

                // If year is in parens, it is not part of booktitle
                if (isset($year) && Str::contains($remainderWithMonthYear, '(' . $year . ')')) {
                    $remainderWithMonthYear = Str::replace('(' . $year . ')', '', $remainderWithMonthYear);
                }
                $this->verbose("[in1b] Remainder with month and year: " . $remainderWithMonthYear);

                // If booktitle ends with period, not preceded by month abbreviations, then year, remove year.
                if (isset($year) && preg_match('%^(?P<remainder>.*)\. ' . $year . '$%', $remainderWithMonthYear, $matches)) {
                    if (! preg_match('/ (' . $this->monthsAbbreviationsRegExp[$language] . ')$/', $matches['remainder'])) {
                        $remainderWithMonthYear = $matches['remainder'];
                    }
                }

                // If $remainder starts with "in", remove it
                // $remainderWithMonthYear is $remainder before month and year (if any) were removed.
                if ($inStart) {
                    $this->verbose("Starts with variant of \"in\"");
                    $remainder = preg_replace($this->inRegExp1, '', $remainder);
                    $remainderWithMonthYear = ltrim(substr($remainderWithMonthYear, 2), ': ');
                }

                // If remainder starts with "Trans.", remove it and put it in note field.
                if (preg_match('/^(?P<note>(?P<translatedBy>[Tt]rans\.|[Tt]ranslated by) (?P<translator>.*?[a-z])\.)(?P<remainder>.*)$/', $remainder, $matches)) {
                    if (isset($matches['note']) && isset($matches['translator'])) {
                        $translator = $matches['translator'];
                        if (Str::endsWith($translator, 'et al')) {
                            $translator .= '.';
                        }
                        if ($use != 'latex' || ($bst && $bst->translator)) {
                            $this->setField($item, 'translator', $translator, 'setField 48a');
                        } else {
                            $this->addToField($item, 'note', $translatedBy . ' ' . $translator, 'addToField 18');
                        }
                        $remainder = $matches['remainder'] ?? '';
                    }

                    // if (isset($matches['note'])) {
                    //     $this->addToField($item, 'note', $matches['note'], 'addToField 19');
                    //     $remainder = $matches['remainder'] ?? '';
                    // }
                }

                if ($itemKind == 'inproceedings') {
                    // Does $remainderWithMonthYear contain a full date or date range?
                    $dateResult = $this->dates->isDate($remainderWithMonthYear, $language, 'contains', true);
                    // Position of *last* match of year in $remainderWithMonthYear
                    // (in case year occurs in title of proceedings, as well as at end of entry)
                    $yearPos = isset($year) ? strrpos($remainderWithMonthYear, $year) : false;
                    // Remove date from $remainderWithMonthYear, so that day range is not confused with page range
                    if ($dateResult) {
                        $datePos = strrpos($remainderWithMonthYear, $dateResult['date']);
                        $remainderWithoutDate = str_replace($dateResult['date'], '', $remainderWithMonthYear);
                        $this->verbose('$remainderWithoutDate: ' . $remainderWithoutDate);
                        // If date is part of booktitle, don't set month separately
                        if (isset($item->month)) {
                            unset($item->month);
                        }
                        // if (empty($dateResult['year']) && isset($year)) {
                        //     $remainderWithoutDate = str_replace($year, '', $remainderWithoutDate);
                        // }
                    } elseif (isset($year) && $yearPos !== false) {
                        $datePos = $yearPos;
                        $remainderWithoutDate = str_replace($year, '', $remainderWithMonthYear);
                    } else {
                        $datePos = false;
                        $remainderWithoutDate = $remainder;
                    }
                    $this->verbose('Remainder without date: ' . $remainderWithoutDate);

                    // Get last match for page range in $remainderWithoutDate
                    $numberOfMatches = preg_match_all('/(\()?' . $this->pagesRegExp . '(\))?/', $remainderWithoutDate, $matches, PREG_OFFSET_CAPTURE);
                    if ($numberOfMatches) {
                        $matchIndex = $numberOfMatches - 1;
                        $pagesPos = $matches[0][$matchIndex][1];
                        $pages = $matches['pages'][$matchIndex][0];
                        $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', 'setField 50');
                        if ($datePos && $pagesPos && $datePos < $pagesPos) {
                            $remainder = str_replace($matches[0][$matchIndex][0], '', $remainderWithMonthYear);
                        } else {
                            $remainder = str_replace(trim($matches[0][$matchIndex][0] , ' :'), '', $remainder);
                            $remainder = str_replace([', ,', ', .'], ',', $remainder);
                        }
                        $remainder = rtrim($remainder, ';,. ');
                    } else {
                        $remainder = $remainderWithMonthYear;
                        $this->verbose('$remainder = $remainderWithMonthYear: ' . $remainder);
                        // If remainder ends with year and ALSO includes year earlier, remove year at end
                        if (Str::endsWith($remainder, $year) && Str::contains(substr($remainder, 0, -strlen($year)), $year)) {
                            $remainder = rtrim(substr($remainder, 0, -strlen($year)), ', ');
                        }
                    }
                } else {
                    // $itemKind = 'incollection'
                    // Get pages
                    // Return last match for 'pages' and remove whole match from $remainder.
                    // Give preference to match that has page word before it.
                    $result = $this->removeAndReturn($remainder, '(\(| |^)' . $this->pagesRegExpWithPp . '(\))?', ['pages'], 'last');
                    if (! $result) {
                        $result = $this->removeAndReturn($remainder, '(\(|(?<!\p{Ll}) |^)' . $this->pagesRegExp . '(\))?', ['pages'], 'last');
                    }
                    if (! $result) {
                        // single page number, preceded by 'p.'
                        $result = $this->removeAndReturn($remainder, '(\(| |^)' . $this->pageRegExpWithPp . '(\))?', ['pages'], 'last');
                    }
                    if ($result) {
                        $pages = $result['pages'];
                        $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', 'setField 51');
                    }
                }

                if (! isset($item->pages)) {
                    $warnings[] = "Pages not found.";
                }

                $remainder = ltrim($remainder, '., ');

                // Next case occurs if remainder previously was like "pages 2-33 in ..."
                $remainder = preg_replace($this->inRegExp1, '', $remainder);

                $this->verbose("[in2] Remainder: " . $remainder);

                $editorStart = false;
                $newRemainder = $remainder;
                
                // If a string in $remainder is quoted or italicized, take that to be book title
                $booktitle = $this->getQuotedOrItalic($remainder, false, false, $before, $after, $style);

                // If booktitle is followed by volume, append it to booktitle
                if (preg_match('/^(?P<volume>,? ?' . $this->volumeWithNumberRegExp . ')(?P<after>.*)$/', $after, $matches)) {
                    if (isset($matches['volume'])) {
                        $booktitle .= $matches['volume'];
                        $after = $matches['after'] ?? '';
                    }
                }
                $after = ltrim($after, ".,' ");
                $newRemainder = $remainder = $before . $after;
                $booktitle = rtrim($booktitle, ', ');

                if ($booktitle) {
                    $this->setField($item, 'booktitle', $booktitle, 'setField 52');
                    if (strlen($before) > 0) {
                        // Pattern is <string1> <booktitle> <string2> (with <string1> nonempty).
                        // Check whether <string1> starts with "forthcoming"
                        $string1 = trim(substr($remainder, 0, strlen($before)), ',. ');
                        if (preg_match('/' . $this->startForthcomingRegExp . '/i', $string1, $matches)) {
                            $match = trim($matches[0], '() ');
                            $match = Str::replaceEnd(' in', '', $match);
                            $match = Str::replaceEnd(' at', '', $match);
                            $this->addToField($item, 'note', ' ' . trim($match), 'addToField 10');
                            $possibleEditors = strlen($matches[0]) - strlen($string1) ? substr($string1, strlen($matches[0])) : null;
                        } else {
                            // Assume <string1> is editors
                            $possibleEditors = $string1;
                        }
                        if ($possibleEditors) {
                            if (preg_match($this->editorStartRegExp, $possibleEditors, $matches, PREG_OFFSET_CAPTURE)) {
                                $possibleEditors = trim(substr($possibleEditors, strlen($matches[0][0])));
                            }
                            $isEditor = true;
                            $editorConversion = $this->authorParser->convertToAuthors(
                                explode(' ', $possibleEditors), 
                                $remains, 
                                $year, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                false, 
                                'editors', 
                                $language
                            );
                            $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);
                            $this->setField($item, 'editor', trim($editorConversion['authorstring']), 'setField 53');
                        } else {
                            $this->verbose('No editors found');
                        }
                        // What is left must be the publisher & address
                        $remainder = $newRemainder = trim(substr($remainder, strlen($before)), '., ');
                    } else {
                        $remainder = ltrim($newRemainder, ', ');
                    }
                }

                // booktitle is not quoted or in italics
                $remainder = Str::replaceStart('{\em', '', $remainder);
                $remainder = trim($remainder, '}., ');
                $this->verbose('[in3] Remainder: ' . $remainder);
                $updateRemainder = false;

                // Address can have one or two words, publisher can have 1-3 words, the last of which can be in parens.
                $booktitleRegExp = '\p{Lu}[\p{L}\-: ]{5,80}';
                $addressRegExp = '[\p{L},]+( [\p{L}]+)?';
                $publisherRegExp = '[\p{L}\-]+( [\p{L}\-]+)?( [\p{L}\-()]+)?';

                ///////////////////
                // Some patterns //
                ///////////////////

                // $remainder is <address>: <publisher>.
                if (preg_match('/^(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u', $remainder, $matches)) {
                    if (isset($matches['address'])) {
                        $this->setField($item, 'address', $matches['address'], 'setField 54');
                    }
                    if (isset($matches['publisher'])) {
                        $this->setField($item, 'publisher', $matches['publisher'], 'setField 55');
                    }
                    $remainder = '';
                }

                if (! $booktitle) {
                    // $remainder is <booktitle>, <address>: <publisher>.
                    if (preg_match('/^(?P<booktitle>' . $booktitleRegExp . '), (?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u', $remainder, $matches)) {
                        if (isset($matches['booktitle'])) {
                            $this->setField($item, 'booktitle', $matches['booktitle'], 'setField 55a');
                        }
                        if (isset($matches['address'])) {
                            $this->setField($item, 'address', $matches['address'], 'setField 55b');
                        }
                        if (isset($matches['publisher'])) {
                            $this->setField($item, 'publisher', $matches['publisher'], 'setField 55c');
                        }
                        $remainder = '';
                    // $remainder is <booktitle>. Ed. <editors> <address>: <publisher>.
                    } elseif (preg_match('/^(?P<booktitle>' . $booktitleRegExp . ')\. ' . $this->edsNoParensRegExp . ' (?P<remains>.*)$/u', $remainder, $matches)) {
                        if (isset($matches['remains'])) {
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $matches['remains']), 
                                $remainder, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            if ($result) {
                                $this->setField($item, 'editor', trim($result['authorstring']), 'setField 55d');
                                if (isset($matches['booktitle'])) {
                                    $this->setField($item, 'booktitle', $matches['booktitle'], 'setField 55e');
                                }
                                if (preg_match('/(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u', $remainder, $matchesAddressPublisher)) {
                                    if (isset($matchesAddressPublisher['address'])) {
                                        $this->setField($item, 'address', $matchesAddressPublisher['address'], 'setField 55f');
                                    }
                                    if (isset($matchesAddressPublisher['publisher'])) {
                                        $this->setField($item, 'publisher', $matchesAddressPublisher['publisher'], 'setField 55g');
                                    }
                                    $remainder = '';
                                }
                            }
                        }
                    // $remainder is <editor> Ed., <booktitle>, <publisher>, <address>.
                    } elseif (preg_match('/^(?P<editor>.{5,80}) ' . $this->edsNoParensRegExp . ', (?P<booktitle>[^,.]*), (?P<publisher>[\p{L} ]{3,40}), (?P<address>[\p{L}, ]{5,40})$/u', $remainder, $matches)) {
                        if (isset($matches['editor'])) {
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $matches['editor']), 
                                $remainder, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                false, 
                                'editors', 
                                $language
                            );
                            if ($result) {
                                $this->setField($item, 'editor', trim($result['authorstring']), 'setField 55d');
                                if (isset($matches['booktitle'])) {
                                    $this->setField($item, 'booktitle', $matches['booktitle'], 'setField 55e');
                                }
                                if (isset($matches['address'])) {
                                    $this->setField($item, 'address', $matches['address'], 'setField 55f');
                                }
                                if (isset($matches['publisher'])) {
                                    $this->setField($item, 'publisher', $matches['publisher'], 'setField 55g');
                                }
                                $remainder = '';
                            }
                        }
                    // $remainder is <booktitle>, trans. <translator> <address>: <publisher>.
                    } elseif (preg_match('/^(?P<booktitle>' . $booktitleRegExp . ')[.,] (?P<trans>[Tt]rans(\.?|lators?) )(?P<remains>.*)$/u', $remainder, $matches)) {
                        if (isset($matches['remains'])) {
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $matches['remains']), 
                                $remainder, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            if ($result) {
                                if ($use != 'latex' || ($bst && $bst->translator)) {
                                    $this->setField($item, 'translator', trim($result['authorstring']), 'setField 48a');
                                } else {
                                    $this->addToField($item, 'note', $matches['trans'] . trim($result['authorstring']), 'addToField 55');
                                }
        
                                if (isset($matches['booktitle'])) {
                                    $this->setField($item, 'booktitle', $matches['booktitle'], 'setField 55i');
                                }

                                // Note: allow comma in address string
                                if (preg_match('/(?P<address>' . $addressRegExp . '): ?(?P<publisher>' . $publisherRegExp . ')\.?$/u', $remainder, $matchesAddressPublisher)) {
                                    if (isset($matchesAddressPublisher['address'])) {
                                        $this->setField($item, 'address', $matchesAddressPublisher['address'], 'setField 55j');
                                    }
                                    if (isset($matchesAddressPublisher['publisher'])) {
                                        $this->setField($item, 'publisher', $matchesAddressPublisher['publisher'], 'setField 55k');
                                    }
                                    $remainder = '';
                                }
                            }
                        }
                    }
                }

                // The only reason why $item->editor could be set other than by the previous code block is that the 
                // item is a book with an editor rather than an author.  So probably the following condition could
                // be replaced by } else {.
                if ($remainder && ! isset($item->editor)) {
                    $periodPosition = strpos($remainder, '.');
                    // if period is preceded by an ordinal (e.g. 1st., 2nd.) or by "Vol" then go to NEXT period
                    if (
                        Str::endsWith(substr($remainder, 0, $periodPosition), $this->ordinals[$language])
                        ||
                        Str::endsWith(substr($remainder, 0, $periodPosition), [' Vol'])
                       ) {
                        if (strpos(substr($remainder, $periodPosition+1), '.') === false) {
                            $periodPosition = strlen($remainder);
                        } else {
                            $periodPosition = $periodPosition + strpos(substr($remainder, $periodPosition+1), '.') + 1;
                        }
                    }

                    // If type is inproceedings and there is a period that is not preceded by any of the $bookTitleAbbrevs
                    // and $remainder does not contain a string for editors, take booktitle to be $remainder up to period.
                    if (
                        $itemKind == 'inproceedings'
                        && ! $booktitle
                        && $periodPosition !== false
                        && ! Str::endsWith(substr($remainder, 0, $periodPosition), $this->bookTitleAbbrevs)
                        && ! preg_match('/ (' . $this->monthsAbbreviationsRegExp[$language] . ')$/', substr($remainder, 0, $periodPosition))
                        //&& ! Str::endsWith(substr($remainder, 0, $periodPosition), $this->monthsAbbreviationsOld[$language])
                        && ! preg_match('/ edited |[ \(]eds?\.|[ \(]pp\./i', $remainder)
                       ) {
                        if ($periodPosition > $datePos) {
                            $booktitle = substr($remainder, 0, $periodPosition);
                            $remainder = trim(substr($remainder, $periodPosition+1));
                        } else {
                            $booktitle = $remainder;
                            $remainder = '';
                        }
                        $this->verbose('[in3a] booktitle: ' . $booktitle);
                    } else {
                        $updateRemainder = true;
                        // If a city or publisher has been found, temporarily remove it from remainder to see what is left
                        // and whether info can be extracted from what is left
                        $tempRemainder = $remainder;
                        $remainderAfterCityString = trim(Str::after($remainder, $cityString), ', ');
                        $dateNext = $this->dates->isDate($remainderAfterCityString, $language, 'starts', true);
                        if ($cityString && ! $dateNext) {
                            // If there is no publisher string and the type is inproceedings and there is only one word left and
                            // it is not the year, assume it is part of booktitle
                            // Case in which it's the year: something like '... June 3-7, New York, NY, 2010'.
                            if (! $publisherString && $itemKind == 'inproceedings' && strpos($remainderAfterCityString, ' ') === false) {
                                if ($this->dates->isYear(($remainderAfterCityString))) {
                                    $booktitle = trim(Str::before($remainder, $remainderAfterCityString), ',. ');
                                } else {
                                    $booktitle = $remainder;
                                }
                                $this->verbose('[in3b] booktitle: ' . $booktitle);
                                $tempRemainder = $cityString = '';
                            } else {
                                // limit of 1, in case city appears also in publisher name
                                $tempRemainder = $this->findAndRemove($tempRemainder, $cityString, 1);
                            }
                        }
                        if ($publisherString && $cityString) {
                            $this->setField($item, 'address', $cityString, 'setField 55j');
                            $this->setField($item, 'publisher', $publisherString, 'setField 55k');
                            $remainder = $tempRemainder = $newRemainder = trim(Str::remove([$publisherString, $cityString], $tempRemainder), ',: ');
                        } elseif ($publisherString) {
                            $tempRemainder = $this->findAndRemove($tempRemainder, $publisherString);
                        }
                        $tempRemainder = trim($tempRemainder, ',.: ');
                        
                        $tempRemainderEndsWithParen = substr($tempRemainder, -1) == ')';
                        if (substr_count($tempRemainder, '(') == substr_count($tempRemainder, ')') && $tempRemainderEndsWithParen) {
                            $tempRemainder = trim($tempRemainder, ',.:( ');                               
                        } else {
                            $tempRemainder = trim($tempRemainder, ',.:() ');
                        }
                        $this->verbose('[in13] tempRemainder: ' . $tempRemainder);

                        // If item doesn't contain string identifying editors, look more carefully to see whether
                        // it contains a string that could be editors' names.
                        if (! $containsEditors) {
                            if (strpos($tempRemainder, '.') === false && strpos($tempRemainder, ',') === false) {
                                $this->verbose("tempRemainder contains no period or comma, so appears to not contain editors' names");
                                if (! $booktitle) {
                                    $booktitle = $tempRemainder;
                                    $this->verbose('booktitle case 2');
                                }
                                $this->setField($item, 'editor', '', 'setField 56');
                                $warnings[] = 'No editor found';
                                $this->setField($item, 'address', $cityString, 'setField 57');
                                $this->setField($item, 'publisher', $publisherString, 'setField 58');
                                $newRemainder = trim(Str::remove([$cityString, $publisherString, $booktitle], $tempRemainder), ', ');
                            } elseif (strpos($tempRemainder, ',') !== false) {
                                // Looking at strings following commas, to see if they are names
                                $tempRemainderLeft = ', ' . $tempRemainder;
                                $possibleEds = null;
                                while (strpos($tempRemainderLeft, ',') !== false && ! $possibleEds) {
                                    $tempRemainderLeft = trim(strchr($tempRemainderLeft, ','), ', ');
                                    $tempRemainderWords = explode(' ', $tempRemainderLeft);
                                    $bareWordCount = 0;
                                    foreach ($tempRemainderWords as $word) {
                                        if (! Str::endsWith($word, [',', '.'])) {
                                            $bareWordCount++;
                                        } else {
                                            break;
                                        }
                                    }

                                    if ($bareWordCount > 3) {
                                        $this->verbose("String \"" . $tempRemainderLeft . "\" has more than 3 bare words, so not name string");
                                    } else {
                                        $nameStringResult = $this->authorParser->isNameString($tempRemainderLeft, $language);
                                        $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                                        if ($nameStringResult['result']) {
                                            $possibleEds = $tempRemainderLeft;
                                        }
                                    }
                                }

                                if (! $possibleEds) {
                                    $this->verbose("No string that could be editors' names identified in tempRemainder");

                                    if ($cityString || $publisherString) {
                                        if (! $booktitle) {
                                            $booktitle = $itemKind == 'inproceedings' ? $remainder : $tempRemainder;
                                            $this->verbose("Booktitle case 3");
                                        }
                                        $this->setField($item, 'editor', '', 'setField 59');
                                        $warnings[] = 'No editor found';
                                        if (! str_contains($booktitle, $cityString)) {
                                            $this->setField($item, 'address', $cityString, 'setField 60');
                                        }
                                        $this->setField($item, 'publisher', $publisherString, 'setField 61');
                                        $newRemainder = '';
                                    }

                                    // Otherwise leave it to rest of code to figure out whether there is an editor, and
                                    // publisher and address.  (Deals well with Harstad et al. items in Examples.)
                                } else {
                                    $this->verbose("The string \"" . $possibleEds . "\" is a possible string of editors' names");
                                }
                            }
                        }
                    }
                }

                if ($remainder && ! $booktitle) {
                    $updateRemainder = true;
                    $remainderContainsEds = false;
                    $postEditorString = '';

                    // If no string is quoted or italic, try to determine whether $remainder starts with
                    // booktitle or editors.
                    // If $tempRemainder ends with "ed" or similar and neither of previous two words contains digits
                    // (in which case "ed" probably means "edition" --- check last two words to cover "10th revised ed"),
                    // format must be <booktitle> <editor>
                    if (! isset($tempRemainder)) {
                        $tempRemainder = $remainder;
                    }

                    $tempRemainderWords = explode(' ', $tempRemainder);
                    $wordCount = count($tempRemainderWords);

                    if ($wordCount >= 3) {
                        $lastTwoWordsHaveDigits = preg_match('/[0-9]/', $tempRemainderWords[$wordCount - 2] . $tempRemainderWords[$wordCount - 3]); 
                    } else {
                        $lastTwoWordsHaveDigits = false;
                    }

                    // $remainder ends in an editor string, possibly in parentheses, so format is <booktitle> <editor>
                    if (! $lastTwoWordsHaveDigits && preg_match('/( ' . $this->edsOptionalParensRegExp . ')$/u', $tempRemainder)) {
                        // Remove "eds" at end
                        $tempRemainderMinusEds = trim(Str::beforeLast($tempRemainder, ' '), ', ');
                        // If remaining string contains '(', take preceding string to be booktitle and following string to be editors.
                        // Remaining string might not contain '(': might be error, or "eds" could have been "(eds").
                        // <booktitle> (<editor> ed.)
                        if ($tempRemainderEndsWithParen && Str::contains($tempRemainderMinusEds, '(')) {
                            $this->verbose('Format is <booktitle> (<editor> eds).');
                            $booktitle = Str::beforeLast($tempRemainderMinusEds, '(');
                            $editorString = Str::afterLast($tempRemainderMinusEds, '(');
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $editorString), 
                                $remainder, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $editor = trim($result['authorstring']);
                            $this->detailLines = array_merge($this->detailLines, $result['author_details']);
                        } else {
                            $this->verbose("Format is <booktitle> <editor> eds.");
                            // Include edition in title (because no BibTeX field for edition for incollection)
                            $result = $this->getTitleAndEditor($tempRemainderMinusEds, $language);
                            
                            $this->setField($item, 'booktitle', $result['title']);

                            if ($result['editor']) {
                                $editor = $result['editor'];
                            } else {
                                $isEditor = true;
                                $result = $this->authorParser->convertToAuthors(
                                    explode(' ', $tempRemainderMinusEds), 
                                    $tempRemainder, 
                                    $trash, 
                                    $month, 
                                    $day, 
                                    $date, 
                                    $isEditor, 
                                    $isTranslator, 
                                    $this->translatorRegExp,
                                    $this->cities, 
                                    $this->dictionaryNames, 
                                    true, 
                                    'editors', 
                                    $language
                                );
                                $editor = $result['authorstring'];
                                $this->detailLines = array_merge($this->detailLines, $result['author_details']);
                            }

                            if (! empty($note)) {
                                $this->addToField($item, 'note', $note, 'addToField 11');
                            }
                        }
                        $this->setField($item, 'editor', $editor, 'setField 62');
                        $remainderContainsEds = true;
                        $updateRemainder = false;
                        $remainder = '';
                    } elseif (preg_match('/^(?P<booktitle>.*?)' . $this->editedByRegExp . '(?P<rest>.*?)$/i', $remainder, $matches)) {
                        // $remainder does not end in editor string, but contains "edited by".
                        $this->verbose("Remainder contains 'edited by'. Taking it to be <booktitle> edited by <editor> <publicationInfo>");
                        $booktitle = trim($matches['booktitle'], ', ');
                        // Authors and publication info
                        $rest = trim($matches['rest']);
                        if (preg_match('/^(?P<before>.*)' . $this->editionRegExp . '(?P<after>.*)$/', $rest, $matches)) {
                            $this->addToField($item, 'note', $matches['fullEdition']);
                            $rest = $matches['before'] . $matches['after'];
                        }
                        $isEditor = true;
                        $remainder = $rest;

                        if (preg_match('/^(?P<editor>[^(]+) \((?P<address>[^:]+): (?P<publisher>[^)0-9]+)( (?P<year>' . $this->yearRegExp . '))?\)$/', $remainder, $matches)) {
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $matches['editor']), 
                                $remainderFromC2A, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $editor = trim($result['authorstring'], ', ');
                            $this->setField($item, 'address', $matches['address'], 'setField 63');
                            $this->setField($item, 'publisher', rtrim($matches['publisher'], ','), 'setField 64');
                            if (! isset($item->year) || ! $item->year) {
                                $this->setField($item, 'year', $matches['year'], 'setField 65');
                            }
                        } elseif (preg_match('/^(?P<before>.*?)(?P<publisherWord>(University|Press))/', $remainder, $matches)) {
                            // If remainder contains publisher word, look for editor before preceding punctuation
                            $origRemainder = $remainder;
                            $before = $matches['before'];
                            $lastCommaPos = strrpos($before, ',');
                            $lastPeriodPos = strrpos($before, '.');
                            $editorString = substr($before, 0, max($lastCommaPos, $lastPeriodPos));
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $editorString), 
                                $remainderFromC2A, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $editor = trim($result['authorstring'], ', ');
                            $this->detailLines = array_merge($this->detailLines, $result['author_details']);
                            $remainder = $remainderFromC2A . substr($origRemainder, max($lastCommaPos, $lastPeriodPos));
                        } else {
                            $result = $this->authorParser->convertToAuthors(
                                explode(' ', $rest), 
                                $remainder, 
                                $trash, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $editor = trim($result['authorstring'], ', ');
                            $this->detailLines = array_merge($this->detailLines, $result['author_details']);
                        }
                        $this->setField($item, 'editor', $editor, 'setField 66');
                        $updateRemainder = false;
                    } elseif ($containsEditors && preg_match($this->edsRegExp, $remainder, $matches)) {
                        // $remainder does not *end* in editor string or contain "edited by", but contains an editor string.
                        // This case is dealt with in detail in the next code block.
                        $eds = $matches[0];
                        $beforeEds = Str::before($remainder, $eds);
                        $wordsBeforeEds = explode(' ', $before);
                        $afterEds = rtrim(Str::after($remainder, $eds), '; ');
                        $setRemainder = false;

                        // Remove address and publisher, if present, and if match one of these patterns
                        if (
                            preg_match('/^(?P<string>[^(]+) \((?P<address>[^:]+): (?P<publisher>[^)0-9]+)( (?P<year>' . $this->yearRegExp . '))?\)$/', $afterEds, $matches)
                            ||
                            preg_match('/^(?P<string>.+), (?P<address>[\p{L}\-]+): (?P<publisher>[^)0-9,]+)( (?P<year>' . $this->yearRegExp . '))?$/', $afterEds, $matches)
                            ||
                            preg_match('/^(?P<string>[^.]+)\. (?P<publisher>[\p{L}\-]+), (?P<address>[^)0-9,]+)( (?P<year>' . $this->yearRegExp . '))?$/', $afterEds, $matches)
                            ||
                            preg_match('/^(?P<string>[^.]+)\. (?P<address>[\p{L}\- ]+): (?P<publisher>[^)0-9,]+)( (?P<year>' . $this->yearRegExp . '))?$/', $afterEds, $matches)
                            ||
                            preg_match('/^(?P<string>[^.]+)\. (?P<addressOrPublisher>[\p{L}\-]+)( (?P<year>' . $this->yearRegExp . '))?$/', $afterEds, $matches)
                            ||
                            preg_match('/^(?P<string>[^.,]+), (?P<publisher>\p{L}+)$/', $afterEds, $matches)
                           ) {
                            if (isset($matches['address'])) {
                                $this->setField($item, 'address', $matches['address'], 'setField 67');
                            }
                            if (isset($matches['publisher'])) {
                                $this->setField($item, 'publisher', rtrim($matches['publisher'], ','), 'setField 68');
                                //$remainder = $beforeEds . $eds . ($matches['string'] ?? '');
                            }
                            if (isset($matches['addressOrPublisher'])) {
                                $addressOrPublisher = $matches['addressOrPublisher'];
                                if (in_array($addressOrPublisher, $this->cities)) {
                                    $this->setField($item, 'address', $addressOrPublisher, 'setField 69');
                                } else {
                                    $this->setField($item, 'publisher', $addressOrPublisher, 'setField 70');
                                }
                                $setRemainder = true;
                            }
                            if (isset($matches['year']) && (! isset($item->year) || ! $item->year)) {
                                $this->setField($item, 'year', $matches['year'], 'setField 71');
                            }
                            $afterEds = trim($matches['string']) . ($matches['year'] ?? '');
                            if ($setRemainder) {
                                $remainder = $beforeEds . $afterEds;
                            }
                        }

                        //$publisherPosition = 0; //$publisher ? strpos($after, $publisher) : false;
                        $remainderContainsEds = true;
                    }

                    // Set $noWordBeforeEdsInDict = true if no word before 'eds' is in the dictionary
                    // in which case format seems to be <editor> eds <booktitle> <publicationInfo>
                    if (! isset($item->editor)) {
                        if ($remainderContainsEds) {
                            $noWordBeforeEdsInDict = true;
                            foreach ($wordsBeforeEds as $word) {
                                if ($this->inDict(trim($word, ' .,'), $this->dictionaryNames)) {
                                    $noWordBeforeEdsInDict = false;
                                    break;
                                }
                            }
                        }

                        // $remainder contains 'eds', but not at end.  Determine which of $beforeEds and $afterEds is
                        // booktitle and which is editor.

                        // Require string for editors to have at least 6 characters and string for booktitle to have at least 10 characters
                        // if ($remainderContainsEds && 
                        //   ($noWordBeforeEdsInDict || (strlen($beforeEds) > 5 && $publisherPosition !== false && $publisherPosition > 10))
                        if ($remainderContainsEds && $noWordBeforeEdsInDict) {
                            // <editors> eds <booktitle> <publicationInfo>
                            $this->verbose("Remainder seems to be <editors> eds <booktitle> <publicationInfo>");
                            $editorStart = true;
                            $editorString = $beforeEds;
                            $determineEnd = false;
                            $postEditorString = $after;
                        } elseif (preg_match('/' . $this->edsParensRegExp . '/u', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                            // $remainder contains "(Eds.)" (parens required) or similar 

                            $results = $subMatches = $nameStringResults = [];

                            // booktitle, which can contain commas, ends in period
                            $results[1] = preg_match('/^(?P<booktitle>[\p{L}\-:,. ]{15,}\p{Ll})\. (?P<editor>[\p{L}\-., ]{8,})' . $this->edsParensRegExp . '[.,]? (?P<pubInfo>.{10,80})$/u', $remainder, $subMatches[1]);
                           
                            // booktitle, which cannot contain commas, ends in comma
                            $results[2] = preg_match('/^(?P<booktitle>[\p{L}\-:. ]{15,}), (?P<editor>[\p{L}\-., ]{8,})' . $this->edsParensRegExp . '[.,]? (?P<pubInfo>.{10,80})$/u', $remainder, $subMatches[2]);
                           
                            // booktitle, which can contain commas, ends in comma, and subsequent editor string has no commas
                            $results[3] = preg_match('/^(?P<booktitle>[\p{L}\-:., ]{15,}), (?P<editor>[\p{L}\-. ]{8,})' . $this->edsParensRegExp . '[.,]? (?P<pubInfo>.{10,80})$/u', $remainder, $subMatches[3]);

                            foreach ($results as $i => $result) {
                                $nameStringResults[$i] = $result ? $this->authorParser->isNameString($subMatches[$i]['editor'], $language) : false;
                            }

                            $resultFound = false;
                            foreach ($results as $i => $result) {
                                if ($result && $nameStringResults[$i]['result']) {
                                    // <booktitle> <editor> (eds) <publicationInfo>
                                    $this->detailLines = array_merge($this->detailLines, $nameStringResults[$i]['details']);
                                    $this->verbose("Remainder format is <booktitle> <editors> (Eds.) <publicationInfo>");
                                    $this->setField($item, 'booktitle', $subMatches[$i]['booktitle'], 'setField 72');
                                    $isEditor = true;
                                    $conversionResult = $this->authorParser->convertToAuthors(
                                        explode(' ', $subMatches[$i]['editor']), 
                                        $remainder, 
                                        $year, 
                                        $month, 
                                        $day, 
                                        $date, 
                                        $isEditor, 
                                        $isTranslator, 
                                        $this->translatorRegExp,
                                        $this->cities, 
                                        $this->dictionaryNames, 
                                        determineEnd: false, 
                                        type: 'editors', 
                                        language: $language
                                    );
                                    $this->detailLines = array_merge($this->detailLines, $conversionResult['author_details']);
                                    $this->setField($item, 'editor', trim($conversionResult['authorstring'], ', '), 'setField 73');
                                    $remainder = trim($subMatches[$i]['pubInfo'], ',. ');
                                    $resultFound = true;
                                    break;
                                }
                            }

                            if (! $resultFound) {
                                $nameStringResult = $this->authorParser->isNameString($remainder, $language);
                                if ($nameStringResult['result']) {
                                    // <editors> (eds) <booktitle> <publicationInfo>
                                    $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                                    $this->verbose("Remainder format is <editors> (Eds.) <booktitle> <publicationInfo>");
                                    $editorStart = true;
                                    $editorString = substr($remainder, 0, $matches[0][1]);
                                    $determineEnd = false;
                                    $postEditorString = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                                    $this->verbose("editorString: " . $editorString);
                                    $this->verbose("postEditorString: " . $postEditorString);
                                    $this->verbose("[in4a] Remainder: " . $remainder);
                                } else {
                                    // <booktitle> <editors> (eds) <publicationInfo>
                                    $this->verbose("Remainder contains \"(Eds.)\" or similar but starts with string that does not look like a name");
                                    $editorStart = false;
                                    $endAuthorPos = $matches[0][1];
                                    $edStrLen = strlen($matches[0][0]);
                                    $this->verbose("[in4b] Remainder: " . $remainder);
                                }
                            }
                        } elseif (preg_match($this->editorStartRegExp, $remainder)) {
                            // $remainder contains "eds" but not "(Eds.)" or "edited by"
                            // $remainder  starts with "Eds" or similar
                            // $remainder is Eds. <editors> <booktitle> <publicationInfo>
                            $this->verbose("Remainder does not contain string like '(Eds.)' but starts with 'Eds' or similar");
                            $editorStart = true;
                            $remainder = $editorString = ltrim(strstr($remainder, ' '), ' ');
                            $determineEnd = true;
                            $this->verbose("editorString: " . $editorString);
                            $this->verbose("[in5] Remainder: " . $remainder);
                            if (substr($remainder, 0, strlen($phrases['and'])+1) == $phrases['and'] . ' ') {
                                // In this case, the string starts something like 'ed. and '.  Assume the next
                                // word is something like 'translated', and drop it
                                $words = explode(" ", $remainder);
                                $leftover = array_shift($words);
                                $leftover .= " " . array_shift($words);
                                $warnings[] = "Don't know what to do with: " . $leftover;
                                $remainder = $editorString = implode(" ", $words);
                            }
                        } elseif (preg_match('/^(?P<booktitle>.*), by (?P<edsAndPubInfo>.*)$/i', $remainder, $matches)) {
                            // <booktitle> by <editor> <publicationInfo>
                            // (an example has this format)
                            $booktitle = $matches['booktitle'];
                            $remainder = $matches['edsAndPubInfo'];
                            $remainingWords = explode(' ', $remainder);
                            $editorConversion = $this->authorParser->convertToAuthors(
                                $remainingWords, 
                                $remainder, 
                                $trash2, 
                                $trash3, 
                                $trash4, 
                                $trash5, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);
                            $this->setField($item, 'booktitle', $booktitle, 'setField 76');
                            $editorString = trim($editorConversion['authorstring'], ', ');
                            $this->setField($item, 'editor', $editorString, 'setField 77');
                            $editorStart = false;
                            $updateRemainder = false;
                            $this->verbose('[in14] Remainder: ' . $remainder);
                        } else {
                            // $remainder contains 'eds' but not 'edited by' or '(Eds.)' and does not start with "Eds"
                            // Main routine checks whether $beforeEds or $afterEds matches an author pattern

                            $this->verbose('$beforeEds: "' . $beforeEds . '".  $afterEds: "' . $afterEds . '".');
                            $strings = [0 => $beforeEds, 1 => $afterEds];

                            foreach ($strings as $i => $string) {
                                $string = rtrim($string, ',') . ' 1'; // ' 1' appended to provide termination
                                $authorResult = $this->authorParser->checkAuthorPatterns(
                                    $string,
                                    $year,
                                    $month,
                                    $day,
                                    $date,
                                    $isEditor,
                                    $isTranslator,
                                    $this->translatorRegExp,
                                    $language
                                );
                                if ($authorResult) {
                                    $editorConversion = $this->authorParser->convertToAuthors(
                                        explode(' ', $authorResult['authorstring']), 
                                        $remainder, 
                                        $trash2, 
                                        $trash3, 
                                        $trash4, 
                                        $trash5, 
                                        $isEditor, 
                                        $isTranslator, 
                                        $this->translatorRegExp,
                                        $this->cities, 
                                        $this->dictionaryNames, 
                                        true, 
                                        'editors', 
                                        $language
                                    );
                                    $editor = trim($editorConversion['authorstring']);
                                    $this->setField($item, 'editor', $editor, 'setField 78');
                                    $booktitleAndPubInfo = trim($strings[1-$i], ',');

                                    $result = preg_match('/^(?P<booktitle>[^.]*?)\.(?P<publisherAddress>.*?)$/', $booktitleAndPubInfo, $matches);
                                    if (! $result) {
                                        $result = preg_match('/^(?P<booktitle>[^,]*?),(?P<publisherAddress>.*?)$/', $booktitleAndPubInfo, $matches);
                                    }

                                    if (
                                        ! isset($item->address) 
                                        &&
                                        ! isset($item->publisher)
                                        && 
                                        $result
                                    ) {
                                        $booktitle = trim($matches['booktitle']);
                                        $remainder = $this->publisherAddressParser->extractPublisherAndAddress(
                                            $matches['publisherAddress'],
                                            $address, 
                                            $publisher, 
                                            $cityString, 
                                            $publisherString, 
                                            $this->cities, 
                                            $this->publishers
                                        );
                                        $this->setField($item, 'booktitle', $booktitle, 'setField 79a');
                                        if ($address) {
                                            $this->setField($item, 'address', $address, 'setField 79b');
                                        }
                                        if ($publisher) {
                                            $this->setField($item, 'publisher', $publisher, 'setField 79c');
                                        }
                                    } else {
                                        $booktitle = $booktitleAndPubInfo;
                                        $this->setField($item, 'booktitle', rtrim(trim($booktitle), '.'), 'setField 79d');
                                        $remainder = substr($string, 0, -2); // remove ' 1' appended to $string
                                    }
                                    $updateRemainder = false;
                                    break;
                                }
                            }

                            // if (! $authorResult) {
                            //     $nameStringResultBefore = $this->authorParser->isNameString($beforeEds, $language);
                            //     $nameStringResultAfter = $this->authorParser->isNameString($afterEds, $language);
                            //     if ($nameStringResultBefore['result'] && ! $nameStringResultAfter['result']) {
                            //         $this->setField($item, 'editor', $beforeEds, 'setField 79e');
                            //         // booktitle?
                            //     } elseif ($nameStringResultAfter['result'] && ! $nameStringResultBefore['result']) {
                            //         $this->setField($item, 'booktitle', trim($beforeEds, '., '), 'setField 79f');
                            //         // editor?
                            //     }
                            // }

                            // Backup method, using isNameString, which isn't very accurate.
                            if (! isset($item->booktitle) || ! isset($item->editor)) {
                                $nameStringResult = $this->authorParser->isNameString($remainder, $language);
                                if (
                                    $nameStringResult['result']
                                    &&
                                    preg_match('/^(?P<editor>.*?) ' . $this->edsNoParensRegExp . ' (?P<remains>.*)$/', $remainder, $matches)
                                    ) {
                                    // <editor> <booktitle> <publicationInfo>
                                    $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                                    $this->verbose("Remainder does not contain \"(Eds.)\" or similar string in parentheses and does not start with \"Eds\" or similar, but starts with a string that looks like a name");
                                    $editorStart = true;
                                    $editorString = $matches['editor'];
                                    $postEditorString = $matches['remains'];
                                    $determineEnd = true;
                                    $this->verbose("editorString: " . $editorString);
                                    $this->verbose("[in6a] Remainder: " . $remainder);
                                } elseif (
                                    $nameStringResult['result']
                                    &&
                                    $itemKind == 'incollection'
                                    ) {
                                    $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                                    $editorStart = true;
                                    $editorString = $remainder;
                                    $determineEnd = true;
                                    $this->verbose("editorString: " . $editorString);
                                    $this->verbose("[in6b] Remainder: " . $remainder);
                                } else {
                                    // $remainder is <booktitle> <editors> <publicationInfo>
                                    $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                                    $this->verbose("Remainder does not contain \"(Eds.)\" or similar and does not start with \"Eds\" or similar, and does not start with a string that looks like a name");
                                    $editorStart = false;
                                    $edStrLen = 0;
                                    $endAuthorPos = 0;
                                }
                            }
                        }

                        if (! isset($item->booktitle) || ! isset($item->editor)) {
                            // An inproceedings item can start with something like "XI Annual ...", which looks like a name string,
                            // but inproceedings items aren't likely to have editors --- they should be identified more strongly
                            // (e.g with "eds" or "edited by").
                            if ($editorStart || ($itemKind == 'incollection' && $this->authorParser->initialNameString($remainder))) {
                                // CASES 1, 3, and 4
                                $this->verbose("[ed1] Remainder starts with editor string");
                                $words = explode(' ', $editorString ?? $remainder);
                                // $isEditor is used only for a book (with an editor, not an author)
                                $isEditor = false;

                                $editorConversion = $this->authorParser->convertToAuthors(
                                    $words, 
                                    $remainder, 
                                    $trash2, 
                                    $month, 
                                    $day, 
                                    $date, 
                                    $isEditor, 
                                    $isTranslator, 
                                    $this->translatorRegExp,
                                    $this->cities, 
                                    $this->dictionaryNames, 
                                    $determineEnd ?? true, 
                                    'editors', 
                                    $language
                                );

                                $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);
                                $editorString = trim($editorConversion['authorstring'], '() ');
                                foreach ($editorConversion['warnings'] as $warning) {
                                    $warnings[] = $warning;
                                }

                                // Do not rtrim a period (might follow an initial).
                                $this->setField($item, 'editor', trim($editorString, ' ,'), 'setField 80');
                                $newRemainder = $postEditorString ? $postEditorString : $remainder;
                                // $newRemainder consists of <booktitle> <publicationInfo>
                                $newRemainder = trim($newRemainder, '., ');
                                $this->verbose("[in7] Remainder: " . $newRemainder);
                            } else {
                                // CASES 2 and 5
                                $this->verbose("[in15] Remainder: " . $remainder);
                                $this->verbose("[ed2] Remainder starts with book title");

                                // If the editors have been identified, set booktitle to be the string before "eds"
                                // and the remainder to be the string after "eds".
                                //if (isset($beforeEds) && isset($afterEds) && isset($item->editor)) {
                                if (isset($beforeEds) && $beforeEds && isset($afterEds)) {
                                    $booktitle = rtrim($beforeEds, ', ');
                                    $this->setField($item, 'booktitle', $booktitle, 'setField 80a');
                                    $remainder = $newRemainder = $afterEds;
                                    $this->verbose('booktitle case 4a');
                                } elseif ($itemKind == 'inproceedings') {
                                    $booktitle = $remainder;
                                    $newRemainder = '';
                                    $this->verbose('booktitle case 4b');
                                } elseif (preg_match('/^(?P<booktitle>.*)\. (?P<remainder>[^.]+:.+)$/', $remainder, $matches)) {
                                    $booktitle = $matches['booktitle'] ?? '';
                                    $newRemainder = $matches['remainder'] ?? '';
                                    $this->verbose('booktitle case 4c');
                                } else {
                                    // Take book title to be string up to first comma or period that does not follow an uppercase letter
                                    $leftParenCount = $rightParenCount = 0;
                                    for ($j = 0; $j < strlen($remainder) && ! $booktitle; $j++) {
                                        $stringTrue = true;
                                        if ($remainder[$j] == '(') {
                                            $leftParenCount++;
                                        } elseif ($remainder[$j] == ')') {
                                            $rightParenCount++;
                                        }
                                        foreach ($this->bookTitleAbbrevs as $bookTitleAbbrev) {
                                            if (substr($remainder, $j - strlen($bookTitleAbbrev), strlen($bookTitleAbbrev)) == $bookTitleAbbrev) {
                                                $stringTrue = false;
                                            }
                                        }
                                        $nameStringResult = $this->authorParser->isNameString(substr($remainder, $j+1), $language);
                                        if  (
                                                // Don't stop in middle of parenthetical phrase
                                                $leftParenCount == $rightParenCount
                                                &&
                                                (
                                                    $j == strlen($remainder) - 1
                                                    ||
                                                    (
                                                        $remainder[$j] == '.'
                                                        &&
                                                        !($remainder[$j-2] == ' ' && strtoupper($remainder[$j-1]) == $remainder[$j-1])
                                                        &&
                                                        $stringTrue
                                                    )
                                                    ||
                                                        $remainder[$j] == ','
                                                    ||
                                                    (
                                                        in_array($remainder[$j], ['(', '['])
                                                        && $nameStringResult['result']
                                                    )
                                                )
                                            ) {
                                            $booktitle = trim(substr($remainder, 0, $j+1), ', ');
                                            $this->verbose('booktitle case 4d');
                                            $newRemainder = rtrim(substr($remainder, $j + 1), ',. ');
                                        }
                                    }
                                    $this->verbose("booktitle: " . $booktitle);
                                    if (! empty($endAuthorPos)) {
                                        // CASE 2
                                        $authorstring = trim(substr($remainder, $j, $endAuthorPos - $j), '.,: ');
                                        $editorConversion = $this->authorParser->convertToAuthors(
                                            explode(' ', $authorstring), 
                                            $trash1, 
                                            $trash2, 
                                            $month, 
                                            $day, 
                                            $date, 
                                            $isEditor, 
                                            $isTranslator, 
                                            $this->translatorRegExp,
                                            $this->cities, 
                                            $this->dictionaryNames, 
                                            false, 
                                            'editors', 
                                            $language
                                        );
                                        $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);
                                        $this->setField($item, 'editor', trim($editorConversion['authorstring'], ' '), 'setField 81');
                                        foreach ($editorConversion['warnings'] as $warning) {
                                            $warnings[] = $warning;
                                        }
                                        $newRemainder = trim(substr($remainder, $endAuthorPos + $edStrLen), ',:. ');
                                        if (isset($item->editors)) {
                                            $this->verbose("[in8] editors: " . $item->editor);
                                        }
                                    } else {
                                        // CASE 5
                                    }
                                }
                            }
                        }
                    }
                }

                if ($updateRemainder) {
                    $remainder = ltrim($newRemainder, ", ");
                }
                $remainder = trim($remainder, '} ');
                $this->verbose("[in9] Remainder: " . $remainder);

                // If only $cityString remains, no publisher has been identified, so assume $cityString is part
                // of proceedings booktitle
                if ($remainder == $cityString) {
                    $booktitle .= ', ' . $cityString;
                    $remainder = '';
                }

                // If $remainder consists only of letters and spaces, has to be publisher(?)
                if (preg_match('/^[\p{L} ]+$/', $remainder) && ! isset($item->publisher)) {
                    $this->setField($item, 'publisher', $remainder, 'setField 81a');
                    $remainder = '';
                }

                // Get editors
                if ($remainder && $booktitle && ! isset($item->editor)) {
                    // CASE 2
                    if (preg_match($this->editorStartRegExp, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $this->verbose("[ed3] Remainder starts with editor string");
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                        $this->verbose("Remainder: " . $remainder);
                        // If $remainder starts with "ed." or "eds." or "edited by" and contains ':', guess that potential editors
                        // end at last period or comma before ":", unless the word before the colon is a string of two uppercase
                        // letters, in which case it is probably a US state abbreviation, and the previous word is part of the
                        // address.
                        $colonPos = strrpos($remainder, ':');
                        if ($colonPos !== false) {
                            $this->verbose("Remainder contains colon");
                            $remainderBeforeColon = trim(substr($remainder, 0, $colonPos));
                            $remainder = substr($remainder, $colonPos);

                            $possibleEditors = explode(' ', $remainderBeforeColon);
                            $ultimateWord = array_pop($possibleEditors);
                            $remainder = $ultimateWord . ' ' . $remainder;

                            // Last word before colon seems to be a US state abbreviation
                            if (strtoupper($ultimateWord) == $ultimateWord && strlen($ultimateWord) == 2) {
                                $penultimateWord = array_pop($possibleEditors);
                                $remainder = $penultimateWord . ' ' . $remainder;
                            }

                            // Take words in remaining string up to the last ending in a comma or period
                            $j = 0;
                            $reversePossibleEditors = array_reverse($possibleEditors);
                            foreach ($reversePossibleEditors as $i => $word) {
                                if (in_array(substr($word, -1), ['.', ','])) {
                                    $j = count($possibleEditors) - $i;
                                    break;
                                }
                            }

                            $editors = array_splice($possibleEditors, 0, $j);
                            $remainder = implode(' ', array_splice($possibleEditors, $j)) . ' ' . $remainder;
                            $remainder = trim($remainder);

                            $editorConversion = $this->authorParser->convertToAuthors(
                                $editors, 
                                $trash1, 
                                $trash2, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                false, 
                                'editors', 
                                $language
                            );
                            $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);

                            $editor = trim($editorConversion['authorstring']);
                            // If editor ends in period and previous letter is lowercase, remove period
                            if (substr($editor, -1) == '.' && strtolower(substr($editor, -2, 1)) == substr($editor, -2, 1)) {
                                $editor = rtrim($editor, '.');
                            }
                            foreach ($editorConversion['warnings'] as $warning) {
                                $warnings[] = $warning;
                            }
                            $this->setField($item, 'editor', $editor, 'setField 82');

                            /*
                            // At least last word must be city or part of city name, so remove it
                            $spacePos = strrpos($remainderBeforeColon, ' ');
                            $possibleEditors = trim(substr($remainderBeforeColon, 0, $spacePos));
                            //$editorConversion = $this->authorParser->convertToAuthors(explode(' ', $possibleEditors), $trash1, $trash2, $isEditor, true);

                            // Find previous period
                            for ($j = $colonPos; $j > 0 && $remainder[$j] != '.' && $remainder[$j] != '('; $j--) {

                            }
                            $this->verbose("Position of period in remainder is " . $j);
                            // Previous version---why drop first 3 chars?
                            // $editor = trim(substr($remainder, 3, $j-3), ' .,');

                            $editorConversion = $this->authorParser->convertToAuthors(explode(' ', trim(substr($remainder, 0, $j), ' .,')), $remainder, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
                            $editor = trim($editorConversion['authorstring']);
                            $this->setField($item, 'editor', $editor, 'setField 117');
                            foreach ($editorConversion['warnings'] as $warning) {
                                $warnings[] = $warning;
                            }
                            */

                            $this->verbose("Editor is: " . $editor);
                            $newRemainder = $remainder;
                        } else {
                            if ($containsPublisher) {
                                $publisherPos = strpos($remainder, $publisher);
                                $editorString = substr($remainder, 0, $publisherPos);
                                $editorConversion = $this->authorParser->convertToAuthors(
                                    explode(' ', trim($remainder)), 
                                    $remainder, 
                                    $year, 
                                    $month, 
                                    $day, 
                                    $date, 
                                    $isEditor, 
                                    $isTranslator, 
                                    $this->translatorRegExp,
                                    $this->cities, 
                                    $this->dictionaryNames, 
                                    true, 
                                    'editors', 
                                    $language
                                );

                                $editor = $editorConversion['authorstring'];
                                $this->verbose("Editor is: " . $editor);                                
                                $newRemainder = substr($remainder, $publisherPos);
                            } else {
                                $editorConversion = $this->authorParser->convertToAuthors(
                                    explode(' ', trim($remainder)), 
                                    $remainder, 
                                    $year, 
                                    $month, 
                                    $day, 
                                    $date, 
                                    $isEditor, 
                                    $isTranslator, 
                                    $this->translatorRegExp,
                                    $this->cities, 
                                    $this->dictionaryNames, 
                                    true, 
                                    'editors', 
                                    $language
                                );

                                $editor = $editorConversion['authorstring'];
                                $this->verbose("Editor is: " . $editor);
                                foreach ($editorConversion['warnings'] as $warning) {
                                    $warnings[] = $warning;
                                }

                                $newRemainder = $remainder;
                            }
                            $this->setField($item, 'editor', trim($editor), 'setField 83');
                        }
                    } elseif (preg_match('/' . $this->edsParensRegExp . '/u', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        // $remainder contains "(Eds.)" or something similar, so takes form <editor> (Eds.) <publicationInfo>
                        $this->verbose("[ed6] Remainder starts with editor string");
                        $editorString = substr($remainder, 0, $matches[0][1]);
                        $this->verbose("editorString is " . $editorString);
                        $editorConversion = $this->authorParser->convertToAuthors(
                            explode(' ', $editorString), 
                            $trash1, 
                            $trash2, 
                            $month, 
                            $day, 
                            $date, 
                            $isEditor, 
                            $isTranslator, 
                            $this->translatorRegExp,
                            $this->cities, 
                            $this->dictionaryNames, 
                            false, 
                            'editors', 
                            $language
                        );
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }
                        $this->setField($item, 'editor', trim($editor, ', '), 'setField 84');
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                    } elseif ($itemKind == 'incollection' && $this->authorParser->initialNameString($remainder)) {
                        // An editor of an inproceedings has to be indicated by an "eds" string (inproceedings
                        // seem unlikely to have editors), but an 
                        // editor of an incollection does not need such a string
                        $this->verbose("[ed4] Remainder starts with editor string");
                        $editorConversion = $this->authorParser->convertToAuthors(
                            explode(' ', $remainder), 
                            $remainder, 
                            $trash2, 
                            $month, 
                            $day, 
                            $date, 
                            $isEditor, 
                            $isTranslator, 
                            $this->translatorRegExp,
                            $this->cities, 
                            $this->dictionaryNames, 
                            true, 
                            'editors', 
                            $language
                        );
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $this->setField($item, 'editor', trim($editor, ', '), 'setField 85');
                        $newRemainder = $remainder;
                    } else {
                        // Else editors are part of $remainder up to " ed." or "(ed.)" etc.
                        $this->verbose("[ed5] Remainder starts with editor string");
                        $numberOfMatches = preg_match($this->edsRegExp, $remainder, $matches, PREG_OFFSET_CAPTURE);
                        if ($numberOfMatches) {
                            $take = $numberOfMatches ? $matches[0][1] : 0;
                            $match = $numberOfMatches ? $matches[0][0] : '';
                            $editor = rtrim(substr($remainder, 0, $take), '., ');
                            $newRemainder = substr($remainder, $take + strlen($match));
                        } elseif ($containsEditors) {
                            $editorConversion = $this->authorParser->convertToAuthors(
                                explode(' ', $remainder), 
                                $remainder, 
                                $trash2, 
                                $month, 
                                $day, 
                                $date, 
                                $isEditor, 
                                $isTranslator, 
                                $this->translatorRegExp,
                                $this->cities, 
                                $this->dictionaryNames, 
                                true, 
                                'editors', 
                                $language
                            );
                            $editor = $editorConversion['authorstring'];
                            $this->setField($item, 'editor', trim($editor, ', '), 'setField 85a');
                        }
                    }

                    if (! isset($item->editor) && isset($editor)) {
                        $words = explode(' ', ltrim($editor, ','));
                        // Let convertToAuthors figure out where editors end, in case some extra text appears after editors,
                        // before publication info.  Not sure this is a good idea: if convertToAuthors works very well, could
                        // be good, but if it doesn't, might be better to take whole string.  If revert to not letting
                        // convertToAuthors determine end of string, need to redefine remainder below.
                        $isEditor = false;

                        $editorConversion = $this->authorParser->convertToAuthors(
                            $words, 
                            $remainder, 
                            $trash2, 
                            $month, 
                            $day, 
                            $date, 
                            $isEditor, 
                            $isTranslator, 
                            $this->translatorRegExp,
                            $this->cities, 
                            $this->dictionaryNames, 
                            true, 
                            'editors', 
                            $language
                        );
                        $this->detailLines = array_merge($this->detailLines, $editorConversion['author_details']);
                        $authorstring = $editorConversion['authorstring'];
                        $this->setField($item, 'editor', trim($authorstring, '() '), 'setField 86');
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $remainder = ltrim($newRemainder, ' ,');
                        $remainder = Str::replaceStart('{\em ', '', $remainder);
                        $remainder = trim($remainder, '}: ');
                        $this->verbose("[in10] Remainder: " . $remainder);
                    }
                } elseif (! $booktitle) {
                    // CASES 1, 3, and 4
                    // Case in which $booktitle is not defined: remainder presumably starts with booktitle
                    $remainder = trim($remainder, '., ');
                    $remainder = ltrim($remainder, ': ');
                    $this->verbose("[in11] Remainder: " . $remainder);
                    // $remainder contains booktitle and publicationInfo.  Need to find boundary.  
                    $colonPos = strpos($remainder, ':');
                    // Look for last period that is at least 5 characters before colon (to account for addresses like
                    // Washington, D.C.)
                    $periodBeforeColonPos = ($colonPos !== false) ? strrpos(substr($remainder, 0, $colonPos - 5), '.') : false;

                    // Check whether publication info matches pattern for book to be a volume in a series
                    $result = $this->removeAndReturn(
                        $remainder,
                        '(?P<volumeSeriesName>(' . $this->volumeAndCodesRegExp . ')( (?P<volume>[1-9][0-9]{0,4}))(( of| in|,) )((?P<seriesName>[^\.,]*)\.|,))',
                        ['volumeSeriesName', 'seriesName', 'volume']
                        );
                    if ($result) {
                        // Take series to be matched string, starting with volume, up to next period or comma
                        // (The volume field is for a volume of a book, not the volume number in a series.)
                        if (strlen($result['seriesName']) < 10) {
                            $this->setField($item, 'volume', $result['volume'], 'setField 88a');
                            $booktitle = trim($result['before'], '., ');
                            $remainder = trim($result['after']);
                        } else {
                            if (isset($result['volumeSeriesName'])) {
                                $this->setField($item, 'series', $result['volumeSeriesName'], 'setField 88b');
                            }
                            $booktitle = trim($result['before'], '., ');
                            $this->verbose('booktitle case 5');
                            $remainder = trim($result['after'], ',. ');
                            $this->verbose('Volume found, so book is part of a series');
                            $this->verbose('Remainder (publisher and address): ' . $remainder);
                        }
                    } elseif (! empty($cityString) && ! empty($publisher)) {
                        $this->setField($item, 'address', $cityString, 'setField 89');
                        $this->setField($item, 'publisher', $publisher, 'setField 90');
                        $remainder = Str::remove([$publisher, $cityString], $remainder);
                        $remainder = rtrim($remainder, ' :)(');
                        $booktitle = $remainder;
                        $remainder = '';
                    } elseif (substr_count($remainder, ':') == 1 && $colonPos !== false && $periodBeforeColonPos !== false && $colonPos - $periodBeforeColonPos < 23)  {
                        // if $remainder contains one colon and a period preceding it by at most 23 characters, take string before
                        // period to be booktitle, following string up to colon to be address, and rest to be publisher
                        $booktitle = substr($remainder, 0, $periodBeforeColonPos);
                        $address = substr($remainder, $periodBeforeColonPos + 1, $colonPos - $periodBeforeColonPos - 1);
                        $address = trim($address, ', ');
                        $this->setField($item, 'address', $address, 'setField 91c');
                        $publisher = substr($remainder, $colonPos+1);
                        $publisher = trim($publisher, '; ');
                        $this->setField($item, 'publisher', $publisher, 'setField 92');
                        $remainder = '';
                    } elseif (
                        preg_match('/(?P<booktitle>[^\(]{5,150})\((?P<address>[^:]{4,22}):(?P<publisher>[^.]{4,40})\)(?P<remains>.*)$/i', $remainder, $matches)
                        ||
                        preg_match('/(?P<booktitle>.{5,150})\.(?P<address>[^:.]{4,22}):(?P<publisher>[^.]{4,40})([,.](?P<remains>.*)$|$)/i', $remainder, $matches)
                        ) {
                        // common patterns: <booktitle> (<address>: <publisher>) or <booktitle>. <address>: <publisher>
                        $booktitle = $matches['booktitle'];
                        $address = $matches['address'];
                        $this->setField($item, 'address', trim($address, '. '), 'setField 93');
                        $publisher = trim($matches['publisher']);
                        $this->setField($item, 'publisher', $publisher, 'setField 94');
                        $remainder = $matches['remains'] ?? '';
                        if (Str::endsWith($remainder, 'pp')) {
                            $this->setField($item, 'note', $remainder . '.', 'setField 95');
                            $remainder = '';
                        }
                    } else {
                        if (preg_match('/^\((?P<address>\p{L}+): (?P<publisher>.+)$/', $remainder, $matches)) {
                            if (isset($matches['address'])) {
                                $this->setField($item, 'address', $matches['address'], 'setField 93a');
                            }
                            if (isset($matches['publisher'])) {
                                $this->setField($item, 'publisher', trim($matches['publisher'], ',. '), 'setField 94a');
                            }
                            $remainder = '';
                        } elseif (substr_count($remainder, '.') == 1 && ! in_array(substr($remainder, strpos($remainder, '.') - 2, 3), ['Jr.', 'Sr.', 'St.'])) {
                            // if remainder contains a single period, not ending "St." etc, take that as end of booktitle
                            $this->verbose("Remainder contains single period, so take that as end of booktitle");
                            $periodPos = strpos($remainder, '.');
                            $booktitle = trim(substr($remainder, 0, $periodPos), ' .,');
                            $this->verbose('booktitle case 6');
                            $remainder = substr($remainder, $periodPos);
                        } else {
                            // If publisher has been identified, remove it from $remainder and check
                            // whether it is preceded by a string that could be an address
                            if (! empty($publisher)) {
                                $this->setField($item, 'publisher', $publisher, 'setField 96');
                                $tempRemainder = trim(Str::remove($publisher, $remainder), ' .');
                                $afterPeriod = Str::afterLast($tempRemainder, '.');
                                $afterComma = Str::afterLast($tempRemainder, ',');
                                $afterPunc = (strlen($afterComma) < strlen($afterPeriod)) ? $afterComma : $afterPeriod;
                                foreach ($this->cities as $city) {
                                    if (Str::endsWith(trim($afterPunc, '():'), $city)) {
                                        $this->setField($item, 'address', $city, 'setField 97');
                                        $tempRemainder = trim(Str::remove($city, $afterPunc), ' .');
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($city) - 2);
                                        $this->verbose('booktitle case 7');
                                        break;
                                    }
                                }

                                $afterPunc = trim($afterPunc, ' ;');
                                if (! isset($item->address)) {
                                    if (substr_count($afterPunc, ' ') == 1) {
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($afterPunc));
                                        $this->setField($item, 'address', $afterPunc, 'setField 98');
                                        $this->verbose('booktitle case 8');
                                    } else {
                                        $booktitle = $tempRemainder;
                                        $this->verbose('booktitle case 9');
                                    }
                                    $this->setField($item, 'booktitle', trim($booktitle, ' ,;'), 'setField 99');
                                    $remainder = '';
                                }
                            // $remainder ends with pattern like 'city: publisher'
                            } elseif (! empty($cityString) && preg_match('/( ?' . $cityString . ': (?P<publisher>[^:.,]*)\.?$)/', $remainder, $matches)) {
                                $booktitle = Str::before($remainder, $matches[0]);
                                $this->setField($item, 'booktitle', trim($booktitle, ',:( '), 'setField 100');
                                // Eliminate space between letters in US state abbreviation containing periods.
                                $cityString = preg_replace('/ ([A-Z]\.) ([A-Z]\.)/', ' $1' . '$2', $cityString);
                                $this->setField($item, 'address', $cityString, 'setField 101');
                                $this->setField($item, 'publisher', trim($matches['publisher'], ' .'), 'setField 102');
                                $this->verbose('booktitle case 14a');
                                $remainder = '';
                            } elseif (preg_match('/^(?P<booktitle>[^.]+)\. \. (?P<address>.*): (?P<publisher>[^:.,]*)$/', $remainder, $matches)) {
                                $this->setField($item, 'booktitle', trim($matches['booktitle'], ',. '), 'setField 103');
                                $this->setField($item, 'address', trim($matches['address'], ',. '), 'setField 104');
                                $this->setField($item, 'publisher', trim($matches['publisher'], ',. '), 'setField 105');
                                $this->verbose('booktitle case 14b');
                                $remainder = '';
                            } elseif (preg_match('/^(?P<booktitle>.*) (?P<address>[^ ]*): (?P<publisher>[^:.,]*)$/', $remainder, $matches)) {
                                $booktitle = $matches['booktitle'];
                                $this->setField($item, 'booktitle', trim($booktitle, ',. '), 'setField 106a');
                                $this->setField($item, 'address', trim($matches['address'], '()'), 'setField 106b');
                                $this->setField($item, 'publisher', $matches['publisher'], 'setField 106c');
                                $this->verbose('booktitle case 14c');
                                $remainder = '';
                            // $remainder is <booktitle>, <publisher> [with no commas or periods in booktitle or publisher]
                            } elseif (preg_match('/^(?P<booktitle>[^,.]*), (?P<publisher>[^,.]*)$/', $remainder, $matches)) {
                                $booktitle = $matches['booktitle'];
                                $this->setField($item, 'booktitle', $booktitle, 'setField 106d');
                                $this->setField($item, 'publisher', $matches['publisher'], 'setField 106e');
                                $this->verbose('booktitle case 14d');
                                $remainder = '';
                            } else {
                                $words = explode(" ", $remainder);
                                $sentences = $this->splitIntoSentences($words);
                                // If two or more sentences, take all but last one to be booktitle.
                                if (count($sentences) > 1) {
                                    $booktitle = implode(' ', array_slice($sentences, 0, count($sentences) - 1));
                                    $this->verbose('booktitle case 10');
                                    $remainder = $sentences[count($sentences) -  1];
                                } else {
                                    $n = count($words);
                                    for ($j = $n - 2; $j > 0 && $this->authorParser->isInitials($words[$j]); $j--) {

                                    }
                                    $potentialTitle = implode(" ", array_slice($words, 0, $j));
                                    $this->verbose("Potential title: " . $potentialTitle);
                                    $periodPos = strpos(rtrim($potentialTitle, '.'), '.');
                                    if ($periodPos !== false) {
                                        $booktitle = trim(substr($remainder, 0, $periodPos), ' .,');
                                        $remainder = substr($remainder, $periodPos);
                                        $this->verbose('booktitle case 11');
                                    } else {
                                        // Does whole entry end in ')' or ').'?  If so, pubinfo is in parens, so booktitle ends
                                        // at previous '('; else booktitle is all of $potentialTitle
                                        if ($entry[strlen($entry) - 1] == ')' || $entry[strlen($entry) - 2] == ')') {
                                            $booktitle = substr($remainder, 0, strrpos($remainder, '('));
                                            $this->verbose('booktitle case 12');
                                        } else {
                                            $booktitle = $potentialTitle;
                                            $this->verbose('booktitle case 13');
                                        }
                                        $remainder = substr($remainder, strlen($booktitle));
                                    }
                                }
                            }
                        }
                    }
                }

                if (! empty($item->editor)) {
                    $this->verbose(['fieldName' => 'Editors', 'content' => $item->editor]);
                } else {
                    $warnings[] = "Editor not found.";
                }

                $remainder = trim($remainder, '[]()., ');
                $this->verbose("[in12] Remainder: " . ($remainder ? $remainder : '[empty]'));

                // If $remainder contains 'forthcoming' string, remove it and put it in $item->note.
                $result = $this->findRemoveAndReturn($remainder, '^(Forthcoming|In press|Accepted)');
                if ($result) {
                    $this->addToField($item, 'note', ' ' . $result[0], 'addToField 12');
                    $this->verbose('"Forthcoming" string removed and put in note field');
                    $this->verbose('Remainder: ' . $remainder);
                    $this->verbose(['fieldName' => 'Note', 'content' => $item->note]);
                }

                $newRemainder = '';

                if ($itemKind == 'inproceedings' && empty($booktitle) && empty($item->booktitle)) {
                    $this->setField($item, 'booktitle', $remainder, 'setField 107');
                } elseif (empty($item->publisher) || empty($item->address)) {
                    if (! empty($item->publisher)) {
                        $this->setField($item, 'address', $remainder, 'setField 108');
                        $newRemainder = '';
                    } elseif (! empty($item->address)) {
                        $this->setField($item, 'publisher', $remainder, 'setField 109');
                        $newRemainder = '';
                    } else {
                        if (str_contains($booktitle, trim($cityString, '. '))) {
                            $cityString = '';
                        }
                        if (preg_match('/^(?P<remains>.*?)' . $this->pagesRegExp . '$/', $remainder, $matches)) {
                            $this->setField($item, 'pages', $matches['pages'], 'setField 110');
                            $remainder = trim($matches['remains'], ';., ');
                        }
                        // publisher cannot be all-numeric
                        if (! preg_match('/^\d+$/', $remainder)) {                        
                            $newRemainder = $this->publisherAddressParser->extractPublisherAndAddress($remainder, $address, $publisher, $cityString, $publisherString, $this->cities, $this->publishers);
                            $this->setField($item, 'publisher', $publisher, 'setField 111');
                            $this->setField($item, 'address', $address, 'setField 112');
                        }
                    }
                }

                if (! empty($item->publisher)) {
                    // Check whether any edition info has been included in publisher
                    $publisher = $item->publisher;
                    preg_match('/\(?' . $this->editionRegExp . '\)?/', $publisher, $matches);
                    if (! empty($matches[0])) {
                        $editionInfo = $matches[0];
                        $item->publisher = trim(str_replace($editionInfo, '', $publisher), ' .');
                    }
                    $this->verbose(['fieldName' => 'Publisher', 'content' => $item->publisher]);
                } else {
                    $warnings[] = "Publisher not found.";
                }
                
                if (! empty($item->address)) {
                    $this->verbose(['fieldName' => 'Address', 'content' => $item->address]);
                } else {
                    $warnings[] = "Address not found.";
                }

                $lastWordInBooktitle = Str::afterLast($booktitle, ' ');
                if ($this->inDict($lastWordInBooktitle, $this->dictionaryNames)) {
                    $booktitle = rtrim($booktitle, '.');
                }

                if (! isset($item->booktitle)) {
                    $booktitle = trim($booktitle, ' .,(:;');
                    if ($booktitle) {
                        // If title starts with In <uc letter>, take off the "In".
                        if (preg_match('/^[IiEe]n [A-Z]/', $booktitle)) {
                            $booktitle = substr($booktitle, 3);
                        }
                        if (substr($booktitle, 0, 1) == '*' && substr($booktitle, -1) == '*') {
                            $booktitle = trim($booktitle, '*');
                        }
                        if (! empty($editionInfo)) {
                            $booktitle .= ' ' . $editionInfo;
                        }
                        $booktitle = preg_replace('/\[C\](?=\.)/', '', $booktitle);
                        $this->setField($item, 'booktitle', $booktitle, 'setField 113');
                    } else {
                        // Change item type to book
                        $itemKind = 'book';
                        $this->verbose(['fieldName' => 'Item type', 'content' => 'changed to ' . $itemKind]);
                        $this->verbose('Both author and editor set, so for bibtex editor moved to note field.');
                        if ($use == 'latex' && ! empty($item->author) && ! empty($item->editor)) {
                            $this->addToField($item, 'note', 'Edited by ' . $item->editor . '.');
                            unset($item->editor);
                            $notices[] = "BibTeX allows an author OR an editor for a book, but not both, so note about editor added.  (BibLaTeX allows both.)";
                        }
                        unset($warnings[array_search('Pages not found.', $warnings)]);
                    }
                }
                
                if ($leftover) {
                    $leftover .= ';';
                }
                $remainder = $leftover . " " . $newRemainder;
                $this->verbose("Remainder: " . $remainder);

                if ($itemKind == 'book') {
                    $this->itemType = 'book';
                }

                break;

            //////////////////////////////////////////
            // Get publication information for book //
            //////////////////////////////////////////

            case 'book':
                if ($language == 'my') {
                    if (preg_match('/^"(?P<pubinfo>[^"]*)"(?P<pages>.*)$/', $remainder, $matches)) {
                        $pubinfo = $matches['pubinfo'] ?? null;
                        $pages = $matches['pages'] ?? null;
                        
                        if ($pubinfo) {
                            $pubinfoParts = explode('။', $pubinfo);
                            if (isset($pubinfoParts[0]) && isset($pubinfoParts[1])) {
                                $this->setField($item, 'publisher', $pubinfoParts[0] . '။' . $pubinfoParts[1] . '။');
                                $this->setField($item, 'publisher-name', $pubinfoParts[0] . '။');
                                $this->setField($item, 'publisher-address', trim($pubinfoParts[1]) . '။');
                            }
                            if (isset($pubinfoParts[2]) && isset($pubinfoParts[3])) {
                                $this->setField($item, 'address', trim($pubinfoParts[2]) . '။' . $pubinfoParts[3] . '။');
                                $this->setField($item, 'printer-name', trim($pubinfoParts[2]) . '။');
                                $this->setField($item, 'printer-address', trim($pubinfoParts[3]) . '။');
                            }
                        }
                        if ($pages) {
                            $this->setField($item, 'pages', trim($pages, ' ,'), 'setField 113a');
                            $this->setField($item, 'numPages', trim($pages, ' ,'), 'setField 113b');
                        }
                    }

                    $remainder = '';
                }

                // If getTitle has reported series name is next, assign series to be $seriesString and remove it from $remainder
                if ($containsSeries) {
                    $this->setField($item, 'series', trim($this->removeFontStyle($seriesString, 'bold'), '.,?! '), 'setField 113c');
                    $remainder = substr($remainder, strlen($seriesString));
                    $remainder = trim($remainder, ' ,.?!');
                }

                // If remainder starts with pages, put them in Note and remove them from remainder
                if (preg_match('/^\(?(?P<note>' . $this->pageRegExpWithPp . ')/', $remainder, $matches)) {
                    $this->setField($item, 'note', $matches['note'], 'setField 114');
                    $remainder = trim(substr($remainder, strlen($matches[0])), '() ');
                }

                // Look for "edited by" in remainder
                if (preg_match('/(?P<editorString>' . $this->editedByRegExp . ' .*?[a-z] ?\.)/', $remainder, $matches)) {
                    $this->addToField($item, 'note', str_replace(' .', '.', $matches['editorString']), 'setField 115');
                    $remainder = str_replace($matches['editorString'], '', $remainder);
                }

                if (preg_match('/(?P<remains>.*)forthcoming\)?\.?$/i', $remainder, $matches)) {
                    $this->addToField($item, 'note', 'Forthcoming.', 'setField 116');
                    $remainder = $matches['remains'];
                } 

                $remainingWords = explode(" ", $remainder);

                // If remainder contains word 'edition', take previous word as the edition number or, if the previous word
                // is 'revised' and the word before that is an ordinal, take the previous two words as the edition number
                $this->verbose('Looking for edition');
                foreach ($remainingWords as $key => $word) {
                    if ($key && in_array(mb_strtolower(trim($word, ',. ()')), $this->editionWords)) {
                        if (
                            isset($remainingWords[$key-1])
                            && in_array($remainingWords[$key-1], ['Revised', 'revised'])
                            && isset($remainingWords[$key-2])
                            && in_array($remainingWords[$key-2], $this->ordinals[$language])
                           ) {
                            $this->setField($item, 'edition', trim($remainingWords[$key - 2] . ' '. $remainingWords[$key - 1], ',. )('), 'setField 117');
                            array_splice($remainingWords, $key - 2, 3);
                        } else {
                            $this->setField($item, 'edition', trim($remainingWords[$key - 1], ',. )('), 'setField 118');
                            array_splice($remainingWords, $key - 1, 2);
                        }
                        break;
                    }
                }

                // 1 = volumeDesignation, 4 = volume, 6 = punc
                // If remainder contains word 'volume', take next word to be volume number.  If
                // following word is "in" or "of" or a comma or period, following string is taken as series name
                $this->verbose('Looking for volume');
                $done = false;
                $newRemainder = null;
                $remainder = implode(" ", $remainingWords);
                $this->verbose('Remainder: ' . $remainder);
                $origRemainder = $remainder;
                $result = $this->removeAndReturn(
                    $remainder,
                    '(?P<volumeDesignation>(\(?' . $this->volumeAndCodesRegExp . ')( (?P<volume>[1-9][0-9]{0,4}|[IVXL]{1,3})\)?))((?P<punc> of| in|,|.|:)? )(?P<seriesAndPubInfo>.*)$',
                    ['volumeDesignation', 'volume', 'punc', 'seriesAndPubInfo']
                );

                if ($result) {
                    if (in_array($result['punc'], ['.', ',', ':']) && substr_count(trim($result['before']), ' ') <= 1) {
                        // Volume is volume of book, not part of series
                        // Publisher and possibly address
                        $this->setField($item, 'volume', $result['volume'], 'setField 119');
                        $newRemainder = $result['before'] . ' ' . $result['seriesAndPubInfo'] . ' ' . $result['after'];
                    } elseif (! isset($item->series) && substr_count(trim($result['before']), ' ') > 1) {
                        // Words before volume designation are series name.  **Book has no field for volume of
                        // series, so add volume designation to series field.**
                        $this->setField($item, 'series', trim($result['before']) . ' ' . $result['volumeDesignation'], 'setField 119a');
                        $newRemainder = $result['seriesAndPubInfo'];
                    } elseif (! isset($item->series)) {
                        // Volume is part of series
                        $this->verbose('Volume is part of series: assume format is <series>? <publisherAndAddress>');
                        $seriesAndPublisher = $origRemainder;
                        // Case in which  publisher has been identified
                        if ($publisher) {
                            $this->setField($item, 'publisher', $publisher, 'setField 121');
                            $after = Str::after($seriesAndPublisher, $publisher);
                            $before = Str::before($seriesAndPublisher, $publisher);
                            if ($after) {
                                // If anything comes after the publisher, it must be the address, and the string
                                // before the publisher must be the series
                                $this->setField($item, 'address', trim($after, ',. '), 'setField 122');
                                $series = trim($before, '., ');
                                if ($this->containsFontStyle($series, false, 'italics', $startPos, $length)) {
                                    $series = substr($series, 0, $startPos) . substr($series, $startPos + $length);
                                    $series = rtrim($series, '}');
                                    $this->setField($item, 'series', $series, 'setField 123');
                                    $this->verbose('Removed italic formatting from series name');
                                } else {
                                    $this->setField($item, 'series', $series, 'setField 124');
                                }
                            } else {
                                // If nothing comes after the publisher,
                                if (Str::endsWith($before, ': ')) {
                                    // if the string before the publisher ends with ': ', the string that precedes
                                    // ':' back to the previous comma or period, whichever comes first, is the address
                                    // and the string before that is the series.  If the string that precedes ':'
                                    // contains no period or comma, the address is the string from the last space to
                                    // ':'.
                                    $containsComma = Str::contains($before, ',');
                                    $containsPeriod = Str::contains($before, '.');
                                    $beforeLastComma = Str::beforeLast($before, ',');
                                    $beforeLastPeriod = Str::beforeLast($before, '.');
                                    if ($containsComma && $containsPeriod) {
                                        if (strlen($beforeLastComma > $beforeLastPeriod)) {
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), 'setField 125');
                                            $this->setField($item, 'series', trim($beforeLastComma, '.,: '), 'setField 126');
                                        } else {
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), 'setField 127');
                                            $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), 'setField 128');
                                        }
                                    } elseif ($containsComma) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), 'setField 129');
                                        $this->setField($item, 'series', trim($beforeLastComma, '.,: '), 'setField 130');
                                    } elseif ($containsPeriod) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), 'setField 131');
                                        $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), 'setField 132');
                                    } else {
                                        $beforeLastSpace = Str::beforeLast($before, ' ');
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastSpace)), '.,: '), 'setField 133');
                                        $this->setField($item, 'series', trim($beforeLastSpace, '.,: '), 'setField 134');
                                    }
                                } else {
                                    // Otherwise there is no address, and the series is the string before the publisher
                                    $this->setField($item, 'series', trim($before, '.,: '), 'setField 135');
                                }
                            }
                        } else {
                            // Case in which no publisher has been identified so far
                            $this->verbose('No publisher has yet been identified');
                            $result1 = $this->findRemoveAndReturn(
                                $seriesAndPublisher,
                                '(.*)[.,]( ([^,.])*(,|:)) ([^,.]*)\.?$'
                            );
                            if ($result1) {
                                $this->setField($item, 'series', $result1[1], 'setField 136');
                                if ($result1[4] == ',') {
                                    $this->verbose('Series case 1a: format is <publisher>, <address>');
                                    $this->setField($item, 'publisher', trim($result1[2], ' ,'), 'setField 137');
                                    $this->setField($item, 'address', $result1[5], 'setField 138');
                                } elseif ($result1[4] == ':') {
                                    $this->verbose('Series case 1b: format is <address>: <publisher>');
                                    $this->setField($item, 'address', trim($result1[2], ' :'), 'setField 139');
                                    $this->setField($item, 'publisher', $result1[5], 'setField 140');
                                }
                            } else {
                                $result2 = $this->findRemoveAndReturn(
                                    $seriesAndPublisher,
                                    '(.*[^.,]*)\. (.*\.?)$'
                                );
                                if ($result2) {
                                    $this->verbose('Series case 2a: format is <publisher> (no address)');
                                    $this->setField($item, 'series', $result2[1], 'setField 141');
                                    $this->setField($item, 'publisher', $result2[2], 'setField 142');
                                } else {
                                    $this->verbose('Series case 2b: format is <publisher>');
                                    $this->setField($item, 'publisher', trim($seriesAndPublisher, ', '), 'setField 143');
                                }
                            }
                        }
                        $done = true;
                    }
                }

                // Possibly series, publisher, and address remain
                if (! $done) {
                    $this->verbose('[4] Remainder: ' . $remainder);
                    $this->verbose('$cityString: ' . ($cityString ?: '[none]') . ' . $publisherString: ' . ($publisherString ?: '[none]'));
                    $remainder = $newRemainder ?? implode(" ", $remainingWords);
                    $remainder = trim($remainder, ' .');

                    // If string is in italics, get rid of the italics
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                        $remainder = rtrim(substr($remainder, $length), '}');
                    }

                    if (! empty($cityString)) {
                        $remainderMinusPubInfo = preg_replace('/(.*)' . $cityString . '[.,:]?/', '$1', $remainder);
                    }
                    if (! empty($publisherString)) {
                        // Replace *last* occurrence of $publisherString in $remainderMinusPubInfo
                        // (Deals with cases like "... Springer Series on ..., Springer, Berlin")
                        $remainderMinusPubInfo = preg_replace('/(.*)' . $publisherString . '[.,]?/', '$1', $remainderMinusPubInfo);
                    }
                    $remainderMinusPubInfo = trim($remainderMinusPubInfo, '., ');

                    if (! isset($item->series)) {
                        // If $cityString and $publisherString exist, so that $remainderMinusPubInfo contains no address or publisher info
                        // and title is strongly identified or $remainderMinusPubInfo contains no period or comma and matches
                        // $this->seriesRegExp, then $remainderMinusPubInfo is series.
                        if (
                            ! empty($cityString)
                            &&
                            ! empty($publisherString)
                            && 
                            (
                                ($titleStyle && $titleStyle != 'none')
                                ||
                                (
                                    preg_match('/(' . $this->seriesRegExp . ')/', $remainderMinusPubInfo)
                                    &&
                                    strpos($remainderMinusPubInfo, '.') === false 
                                    &&
                                    strpos($remainderMinusPubInfo, ',') === false
                                )
                            )
                            ) {
                            $this->setField($item, 'series', $this->removeFontStyle($remainderMinusPubInfo, 'bold'), 'setField 144');
                            $this->setField($item, 'address', $cityString, 'setField 145');
                            $this->setField($item, 'publisher', $publisherString, 'setField 146');
                            $remainder = '';
                        } else {
                            // If remainder contains a period following a lowercase letter and preceding string matches
                            // $this->seriesRegExp, string before period is series name
                            $periodPos = strpos($remainderMinusPubInfo, '.');
                            if ($periodPos !== false && strtolower($remainderMinusPubInfo[$periodPos-1]) == $remainderMinusPubInfo[$periodPos-1]) {
                                $beforePeriod = trim(Str::before($remainderMinusPubInfo, '.'));
                                if (preg_match('/(' . $this->seriesRegExp . ')/', $beforePeriod)) {
                                    $series = $beforePeriod;
                                    if (preg_match('/^(?P<volume> ?' . $this->volumeWithNumberRegExp . ')(?P<remainder>.*?)$/', Str::after($remainderMinusPubInfo, '.'), $matches)) {
                                        if (isset($matches['volume'])) {
                                            $series .= '.' . $matches['volume'];
                                            $remainder = $matches['remainder'] ?? '';
                                        }
                                    } else {
                                        $remainder = trim(Str::remove($beforePeriod, $remainder));
                                    }
                                    $this->setField($item, 'series', $series, 'setField 147');
                                }
                            }

                            // First use routine to find publisher and address, to catch cases where address
                            // contains more than one city, for example.

                            // If item is a book, $cityString and $publisherString are set, and existing title is followed by comma
                            // in $entry, string preceding $cityString
                            // and $publisherString must be part of title (which must have been ended prematurely). 
                            if ($itemKind == 'book' && ! empty($cityString) && !empty($publisherString)) {
                                $afterTitle = Str::after($entry, $item->title ?? '');
                                if ($afterTitle[0] == ',') {
                                    $beforeCity = Str::before($remainder, $cityString);
                                    $beforePublisher = Str::before($remainder, $publisherString);
                                    $beforeCityPublisher = strlen($beforeCity) < strlen($beforePublisher) ? $beforeCity : $beforePublisher;
                                    if ($beforeCityPublisher) {
                                        $entryStartingWithTitle = strstr($entry, $item->title);
                                        $title = strstr($entryStartingWithTitle, $beforeCityPublisher, true) . $beforeCityPublisher;
                                        $this->setField($item, 'title', trim($title, ' ,'), 'setField 148');
                                        $remainder = $cityString . ':' . $publisherString;
                                    }
                                }
                            }
                        }
                    }

                    // Cannot put this earlier, before type has been determined, because "Trans." is an abbreviation
                    // used in journal names.  ("Translated by" is heandled earlier.)
                    $result = $this->findRemoveAndReturn($remainder, '[Tt]rans\. .*?[a-z]\.', false);
                    if (! $result) {
                        $result = $this->findRemoveAndReturn($remainder, '^\([A-Za-z. ]+,? [Tt]rans\.\)', false);
                    }
                    if ($result) {
                        $this->addToField($item, 'note', ucfirst($result[0]), 'addToField 3');
                        $before = Str::replaceEnd(' and ', '', $result['before']);
                        $remainder = $before . '.' . $result['after'];
                    }

                    $remainder = $this->publisherAddressParser->extractPublisherAndAddress(
                        $remainder, 
                        $address, 
                        $publisher, 
                        $cityString, 
                        $publisherString, 
                        $this->cities, 
                        $this->publishers
                    );

                    if ($publisher) {
                        // Code moved earlier
                        // if (preg_match('/^(?P<publisher>[^,]+)(, (?P<remains>[0-9ivx +\[\]]+pp))?$/', $publisher, $matches)) {
                        //     if (isset($matches['remains'])) {
                        //         $this->setField($item, 'note', $matches['remains'] . '.', 'setField 149');
                        //         $publisher = $matches['publisher'];
                        //     }
                        // }
                        $this->setField($item, 'publisher', trim($publisher, '();{} '), 'setField 150');
                    }

                    if ($address) {
                        if (isset($item->year)) {
                            $address = Str::replace($item->year, '', $address);
                            $address = rtrim($address, ', ');
                        }
                        $this->setField($item, 'address', $address, 'setField 151');
                    }

                    // Then fall back on publisher and city previously identified.
                    if (! $publisher && $publisherString && ! $address && $cityString) {
                        $this->setField($item, 'publisher', $publisherString, 'setField 152');
                        $this->setField($item, 'address', $cityString, 'setField 153');
                        $remainder = $this->findAndRemove((string) $remainder, $publisherString);
                        $remainder = $this->findAndRemove($remainder, $cityString);
                    } 

                    if (!isset($item->publisher)) {
                        $warnings[] = "No publisher identified.";
                    }
                    
                    if (!isset($item->address)) {
                        $warnings[] = "No place of publication identified.";
                    }
                }

                break;

            ////////////////////////////////////////////
            // Get publication information for thesis //
            ////////////////////////////////////////////

            case 'thesis':
                if (preg_match('/' . $this->masterRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                    $itemKind = 'mastersthesis';
                } elseif (preg_match('/' . $this->phdRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                    $itemKind = 'phdthesis';
                } else {
                    $itemKind = 'phdthesis';
                    $warnings[] = "Can't determine whether MA or PhD thesis; set to be PhD thesis.";
                }
                $this->verbose(['fieldName' => 'Item type', 'content' => $itemKind]);

                if (preg_match('/^\(' . $this->fullThesisRegExp . '(?P<school>[^\)]*)\)(?P<remainder>.*)/u', $remainder, $matches)) {
                    $this->setField($item, 'school', trim($matches['school'], ', '), 'setField 154');
                    $remainder = $matches['remainder'];
                    if (empty($item->school)) {
                        $this->setField($item, 'school', trim($remainder, ',.() '), 'setField 155');
                    } else {
                        $this->addToField($item, 'note', trim($remainder, ',. '), 'setField 156');
                    }
                } else {
                    if (preg_match('/\(' . $this->fullThesisRegExp . '\)/u', $remainder)) {
                        $remainder = $this->findAndRemove($remainder, ',? ?\(' . $this->fullThesisRegExp . '\)');
                    //} elseif (preg_match('/^(Thesis|Dissertation)[.,]? (?P<remainder>.*)$/', $remainder, $matches)) {
                    } elseif (preg_match('/' . $this->thesisRegExp . ' (?P<remainder>.*)$/', $remainder, $matches)) {
                        $remainder = $matches['remainder'] ?? '';
                    } else {
                        $remainder = preg_replace('/([Uu]npublished|[Yy]ayınlanmamış) /', '', $remainder);
                        $remainder = $this->findAndRemove($remainder, $this->fullThesisRegExp);
                    }
                    $remainder = trim($remainder, ' -.,)[]');
                    // if remainder contains number of pages, put them in note
                    $result = $this->removeAndReturn($remainder, '(?P<pageWithPp>\(?' . $this->pageRegExpWithPp . '\)?)', ['pageWithPp']);
                    if ($result) {
                        $this->setField($item, 'note', $result['pageWithPp'], 'setField 157');
                        $remainder = trim($remainder, '., ');
                    }

                    if (strpos($remainder, ':') === false) {
                        // If there's an extra year before the $remainder, remove it
                        if (preg_match('/^(?P<year>' . $this->yearRegExp . ')/', $remainder, $matches)) {
                            $extraYear = $matches['year'];
                            $remainder = substr($remainder, 4);
                            $remainder = ltrim($remainder, '., ');
                            $warnings[] = "The string \"" . $extraYear . "\" before the schoool name remains unidentified.";
                        }
                        if (preg_match('/(?P<month>' . $this->monthsRegExp[$language] . ')?,? (?P<year>' . $this->yearRegExp . ')$/', $remainder, $matches)) {
                            if (isset($matches['month'])) {
                                $this->setField($item, 'month', $matches['month'], 'setField 157a');
                            }
                            if (! isset($item->year) && isset($matches['year'])) {
                                $this->setField($item, 'year', $matches['year'], 'setField 157b');
                            }
                            $remainder = substr($remainder, 0, strlen($remainder) - strlen($matches[0]));
                            $remainder = trim($remainder, '. ');
                        }

                        $this->setField($item, 'school', $remainder, 'setField 158');
                    } else {
                        $remArray = explode(':', $remainder);
                        $this->setField($item, 'school', trim($remArray[1], ' .,'), 'setField 159');
                        $this->addToField($item, 'note', $remArray[0], 'setField 160');
                    }
                    $remainder = '';
                }

                if (empty($item->school)) {
                    $warnings[] = "No school identified.";
                }

                break;
        }

        /////////////////////////////////
        // Fix up $remainder and $item //
        /////////////////////////////////

        $remainderEndsInColon = substr($remainder, -1) == ':';
        $remainder = trim($remainder, '.,:;}{ ');

        if ($remainder && ! in_array($remainder, ['pages', 'Pages', 'pp', 'pp.'])) {
            $year = $this->dates->getDate($remainder, $remainds, $month, $day, $date, true, true);

            if (is_numeric($year) && $month && $day) {
                $this->setField($item, 'date', $year . '-' . $month . '-' . $day, 'setField 161');
                $remainder = '';
            } elseif (preg_match('/^' . $this->endForthcomingRegExp . '/i', $remainder)
                ||
                preg_match('/^' . $this->startForthcomingRegExp . '/i', $remainder)
                ) {
                $this->addToField($item, 'note', $remainder, 'addToField 14');
            } elseif ($itemKind == 'online') {
                if (
                    preg_match('/^[a-zA-Z ]*$/', $remainder) 
                    && (! $titleEndsInPeriod || ! $allWordsInitialCaps) 
                    && ! $remainderEndsInColon
                    && $titleStyle != 'quoted'
                    && (! isset($item->author) || trim($item->author, '{}') != $remainder)
                   ) {
                    // If remainder is all letters and spaces, assume it is part of title,
                    // which must have been ended prematurely.
                    $this->addToField($item, 'title', $remainder, 'addToField 16');
                } else {
                    $this->addToField($item, 'note', $remainder, 'addToField 15');
                }
            } elseif (preg_match('/^Paper no\. [0-9]+\.?$/i', $remainder)) {
                $this->addToField($item, 'note', $remainder, 'addToField 17a');
            } elseif (
                preg_match('/^[0-9]{1,3}$/', $remainder) 
                ||
                in_array($remainder, ['Vol', 'No'])
                ||
                (isset($item->year) && $remainder == $item->year)
                ) {
                // 1--3 digit numbers are ignored (4-digit number could be year)
                $warnings[] = "[u4] The string \"" . $remainder . "\" remains unidentified.";
            } else {
                $this->addToField($item, 'note', $remainder, 'addToField 17b');
            }
        }

        if ($pageCount) {
            if ($use == 'biblatex' && in_array($itemKind, ['book', 'phdthesis', 'mastersthesis'])) {
                $this->setField($item, 'pagetotal', $pageCount, 'setField 25a');
            } else {
                $this->addToField($item, 'note', $pageCount, 'addToField 21');
                $this->verbose('Adding page count to note field.');
            }
        }

        if (isset($item->note)) {
            $item->note = trim($item->note);
        }

        if (empty($item->pages)) {
            if (isset($item->pages)) {
                unset($item->pages);
            }
            if (in_array($itemKind, ['article', 'incollection', 'inproceedings'])) {
                $warnings[] = "No page range found.";
            }
        }

        if ($itemKind == 'unpublished' && !isset($item->note)) {
            $warnings[] = "Mandatory 'note' field missing.";
        }

        if (isset($item->publisher) && $item->publisher == '') {
            unset($item->publisher);
        }

        if (isset($item->address) && ! $item->address) {
            unset($item->address);
        }

        if (isset($item->volume) && ! $item->volume) {
            unset($item->volume);
        }

        if (isset($item->editor) && ! $item->editor) {
            unset($item->editor);
        }

        if (isset($item->institution) && ! $item->institution) {
            unset($item->institution);
        }

        if (isset($item->note) && ! $item->note) {
            unset($item->note);
        }

        if (isset($item->year) && ! $item->year) {
            unset($item->year);
        }

        foreach ($warnings as $warning) {
            $this->verbose(['warning' => $warning]);
        }

        foreach ($notices as $notice) {
            $this->verbose(['notice' => $notice]);
        }

        if (isset($item->journal)) {
            $item->journal = trim($item->journal, '}; ');
        }

        if (isset($item->title)) {
            if (in_array($use, ['latex', 'biblatex'])) {
                $item->title = $this->requireUc($item->title);
            }
            $scholarTitle = $this->makeScholarTitle($item->title);
        } else {
            $scholarTitle = '';
        }

        if ($language == 'my') {
            foreach ($item as $name => $field) {
                $item->$name = $this->digitsToBurmeseNumerals($field);
            }
            $item->language = 'Burmese';
        }

        $returner = [
            'source' => $originalEntry,
            'item' => $item,
            'label' => $itemLabel,
            'itemType' => $itemKind,
            'warnings' => $warnings,
            'notices' => $notices,
            'details' => $conversion->report_type == 'detailed' ? $this->detailLines : [],
            'scholarTitle' => $scholarTitle,
            'author_pattern' => $authorConversion['author_pattern'] ?? null,
        ];

        return $returner;
    }

    /*
     * Get title from a string that starts with title and then has authors (e.g. editors, in <booktitle> <editor> format)
     */
    private function getTitleAndEditor(string &$remainder, string $language = 'en'): array
    {
        $title = '';
        $remainder = str_replace('  ', ' ', $remainder);

        // Main routine, using author patterns
        // Go through $remainder, stopping at each comma or period and checking whether the following
        // string matches an author pattern.  (' 1' added to $remainder because author patterns require terminating string.)
        $remains = $remainder . ' 1';
        $isEditor = true;
        $isTranslator = false;
        while ($remains) {
            if (preg_match('/^(?P<before>[^.,]*)(?P<punc>[.,])(?P<after>.*)$/', $remains, $matches)) {
                $title .= $matches['before'];
                $after = trim($matches['after']);
                // Does $after match an author pattern?
                $result = $this->authorParser->checkAuthorPatterns(
                    $after,
                    $year,
                    $month,
                    $day,
                    $date,
                    $isEditor,
                    $isTranslator,
                    $this->translatorRegExp,
                    $language
                );

                if ($result) {
                    $editor = trim($result['authorstring']);
                    return [
                        'title' => $title,
                        'editor' => $editor,
                    ];
                } else {
                    $title .= $matches['punc'];
                    $remains = $matches['after'];
                }
            } else {
                $remains = '';
            }
        }

        // Backup routine, relying on isNameString (which isn't very reliable).
        if (preg_match('/^(?P<title>[^.,]{10,})[\.,] (?P<remainder>.{10,})$/u', $remainder, $matches)) {
            $title = $matches['title'];
            $remainder = $matches['remainder'];    
        } else {
            $words = explode(' ', $remainder);
            $initialWords = [];
            $remainingWords = $words;

            foreach ($words as $word) {
                array_shift($remainingWords);
                $remainder = implode(' ', $remainingWords);
                $initialWords[] = $word;
                $nameStringResult = $this->authorParser->isNameString($remainder, $language);
                if (Str::endsWith($word, ['.', ',']) && $nameStringResult['result']) {
                    $this->detailLines = array_merge($this->detailLines, $nameStringResult['details']);
                    $title = rtrim(implode(' ', $initialWords), ',');
                    break;
                }
            }
        }

        return [
            'title' => $title,
            'editor' => null,
        ];
    }

}
