<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

use Illuminate\Support\Str;

use App\Models\City;
use App\Models\Conversion;
use App\Models\DictionaryName;
use App\Models\ExcludedWord;
use App\Models\Journal;
use App\Models\Name;
use App\Models\Publisher;
use App\Models\StartJournalAbbreviation;
use App\Models\VonName;

use App\Traits\MakeScholarTitle;
use App\Traits\Stopwords;
use App\Traits\Countries;

use PhpSpellcheck\Spellchecker\Aspell;
use SebastianBergmann\Type\NullType;
use stdClass;

use function Safe\strftime;

class Converter
{
    var $accessedRegExp1;
    var $andWords;
    var $articleRegExp;
    var $boldCodes;
    var $bookTitleAbbrevs;
    var $cities;
    var $detailLines;
    var $dictionaryNames;
    var $editionRegExp;
    var $editionWords;
    var $editorStartRegExp;
    var $editorEndRegExp;
    var $editorRegExp;
    var $edsRegExp1;
    var $edsRegExp2;
    var $edsRegExp4;
    var $endForthcomingRegExp;
    var $excludedWords;
    var $forthcomingRegExp;
    var $forthcomingRegExp1;
    var $forthcomingRegExp2;
    var $forthcomingRegExp3;
    var $forthcomingRegExp4;
    var $forthcomingRegExp5;
    var $forthcomingRegExp6;
    var $forthcomingRegExp7;
    var $fullThesisRegExp;
    var $inRegExp1;
    var $inRegExp2; 
    var $inReviewRegExp1;
    var $inReviewRegExp2;
    var $inReviewRegExp3;
    var $isbnRegExp1;
    var $isbnRegExp2;
    var $issnRegExps;
    var $italicCodes;
    var $italicTitle;
    var $itemType;
    var $journalWord;
    var $journalNames;
    var $masterRegExp;
    var $monthsRegExp;
    var $monthsAbbreviations;
    var $names;
    var $nameSuffixes;
    var $numberRegExp;
    var $oclcRegExp1;
    var $oclcRegExp2;
    var $ordinals;
    var $pagesRegExp;
    var $pageRegExp;
    var $pageRegExpWithPp;
    var $pagesRegExpWithPp;
    var $pageRange;
    var $page;
    var $pageWords;
    var $pageWordsRegExp;
    var $startPagesRegExp;
    var $phdRegExp;
    var $phrases;
    var $proceedingsRegExp;
    var $proceedingsExceptions;
    var $publishers;
    var $retrievedFromRegExp1;
    var $retrievedFromRegExp2;
    var $startForthcomingRegExp;
    var $startJournalAbbreviations;
    var $thesisRegExp;
    var $volRegExp0;
    var $volRegExp1;
    var $volRegExp2;
    var $volRegExp3;
    var $volumeRegExp;
    var $vonNames;
    var $workingPaperRegExp;
    var $workingPaperNumberRegExp;

    use Stopwords;
    // Countries used to check last word of title, following a comma and followed by a period --- country
    // names that are not abbreviations used at the start of journal names or other publication info
    use Countries;
    use MakeScholarTitle;

    public function __construct()
    {
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
         * The number of journals is huge, and an array of all of them is unweildy.
         * Further, if a journal name is in the database and an item contains a journal 
         * with a name that is not in the database but is a superset of the one that is,
         * the shorter name gets assigned to the item.  Checking for that seems like it
         * is pretty much equivalent to determining what part of the string is the journal
         * name, which means the array of existing journal names is not useful.
         */
        $this->journalNames = [];

        // Abbreviations used as the first words of journal names (like "J." or "Bull.")
        $this->startJournalAbbreviations = StartJournalAbbreviation::where('distinctive', 1)
            ->where('checked', 1)
            ->pluck('word')
            ->toArray();

        // The script will identify strings as cities and publishers even if they are not in these arrays---but the
        // presence of a string in one of the arrays helps when the elements of the reference are not styled in any way.
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

        $this->nameSuffixes = ['Jr', 'Sr', 'III'];

        // Introduced to facilitate a variety of languages, but the assumption that the language of the 
        // citation --- though not necessarily of the reference itself --- is English pervades the code.
        $this->phrases = [
            'en' =>
                [
                'and' => 'and',
                'in' => 'in',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'edited by'
                ],
            'cz' =>
                [
                'and' => 'a',
                'in' => 'v',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'upravil'
                ],
            'fr' =>
                [
                'and' => 'et',
                'in' => 'en',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'edited by'
                ],
            'es' =>
                [
                'and' => 'y',
                'in' => 'en',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'edited by'
                ],
            'pt' =>
                [
                'and' => 'e',
                'in' => 'em',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'edited by'
                ],
            'my' =>
                [
                'and' => 'နှင့်',
                'in' => 'in',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'edited by'
                ],
            'nl' =>
                [
                'and' => 'en',
                'in' => 'in',
                'editor' => 'editor',
                'editors' => 'editors',
                'ed.' => 'ed.',
                'eds.' => 'eds.',
                'edited by' => 'bewerkt door'
                ],

        ];

        $this->ordinals = [
            'en' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'cz' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'fr' =>
                ['1er', '2e', '3e', '4e', '5e', '6e', '7e', '8e', '9e', '10e'],
            'es' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'pt' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '1ª', '2ª', '3ª', '4ª', '5ª', '6ª', '7ª', '8ª', '9ª'],
            'my' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'],
            'nl' =>
                ['1e', '2e', '3e', '4e', '5e', '6e', '7e', '8e', '9e', '10e'],
        ];

        $this->articleRegExp = 'article (id |no\.? ?)?[0-9]*';

        // 'a cura di': Italian. რედ: Georgian.
        $this->edsRegExp1 = '/[\(\[]([Ee]ds?\.?|რედ?\.?|[Ee]ditors?|a cura di)[\)\]]/';
        $this->edsRegExp2 = '/ed(\.|ited) by/i';
        $this->edsRegExp4 = '/( [Ee]ds?[\. ]|[\(\[][Ee]ds?\.?[\)\]]| [Ee]ditors?| [\(\[][Ee]ditors?[\)\]])/';
        $this->editorStartRegExp = '/^[\(\[]?[Ee]dited by|^[\(\[]?[Ee]ds?\.?|^[\(\[][Ee]ditors?/';
        // Needs space before 'eds?' to exclude word ending in 'ed'.
        $this->editorEndRegExp = '[^0-9] eds?\.?[\)\]]?$|[\(\[]?editors?[\)\]]?$';
        // რედ: Georgian
        $this->editorRegExp = '( eds?[\. ]|[\(\[]eds?\.?[\)\]]|[\(\[ ]edits\.[\(\] ]| editors?| [\(\[]editors?[\)\]]|[\(\[]რედ?\.?[\)\]])';

        $this->editionWords = ['edition', 'ed', 'edn', 'edição', 'édition', 'edición'];
        $this->editionRegExp = '(?P<fullEdition>(?P<edition>(1st|first|2nd|second|3rd|third|[4-9]th|[1-9][0-9]th|fourth|fifth|sixth|seventh|[12][0-9]{3}|revised) (rev\.|revised )?)(ed\.|edition|vydání|édition|edición|edição|editie))';

        $this->volRegExp0 = ',? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?[1-9][0-9]{0,4}';
        $this->volRegExp1 = '/,? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?\d/';
        $this->volRegExp2 = '/^\(?vol(\.|ume)? ?|^\(?v\. /i';
        $this->volumeRegExp = '[Vv]olume ?|[Vv]ols? ?\.? ?|VOL ?\.? ?|[Vv]\. |{\\\bf |\\\textbf{|\\\textit{|\*';
        $this->volRegExp3 = '[Vv]olume ?|[Vv]ol ?\.? ?|VOL ?\.? ?|[Vv]\. ';

        $this->numberRegExp = '[Nn][Oo]s? ?\.?:? ?|[Nn]umbers? ?|[Nn] ?\. |№ ?|n° ?|[Ii]ssues?:? ?|Issue no. ?|Iss: ';

        // page range
        // (page number cannot be followed by letter, to avoid picking up string like "'21 - 2nd Congress")
        $this->pageRange = '(?P<pages>[A-Z]?[1-9][0-9]{0,4} ?-{1,3} ?[A-Z]?[0-9]{1,5})(?![a-zA-Z])';
        // single page or page range
        $this->page = '(?P<pages>[A-Z]?[1-9][0-9]{0,4})( ?-{1,3} ?[A-Z]?[0-9]{1,5})?(?![a-zA-Z])';

        $this->pageWords = [
            '[Pp]ages? ',
            '[Pp]ages? : ',
            '[Pp]p\.? ?',
            'p\. ?',
            'p ?',
            '[Pp]ágs?\. ?',// Spanish, Portuguese
            'стр\. ?',     // Russian
            '[Bb]lz\. ?',  // Dutch
            '[Hh]lm\. ?',
            '[Hh]al\. ?',  // Indonesian
            '[Ss]s?\. ?',  // Turkish
            '[Ss]tr\. ?',  // Czech
            'გვ\. ?',      // Georgian
        ];

        // 'y' is for Spanish, 'e' for Portuguese, 'et' for French, 'en' for Dutch, 'und' for German, 'и' for Russian, 'v' for Turkish,
        // 'dan' for Indonesian, 'şi' for Romanian
        $this->andWords = [
            'and', '\&', '&', '$\&$', 'y', 'e', 'et', 'en', 'und', 'и', 've', 'dan', 'şi'
        ];

        $startPagesRegExp = '/(';
        $pageWordsRegExp = '';
        foreach ($this->pageWords as $i => $pageWord) {
            $startPagesRegExp .= ($i ? '|' : '') . '^' . $pageWord;
            $pageWordsRegExp .= ($i ? '|' : '') . $pageWord;
        }
        $startPagesRegExp .= ')[0-9]/';

        $this->pageWordsRegExp = $pageWordsRegExp;

        $pageRegExpWithPp = '(' . $pageWordsRegExp . '):?( )?' . $this->page;
        $pagesRegExpWithPp = '(' . $pageWordsRegExp . '):?( )?' . $this->pageRange;
        $pageRegExp = '(' . $pageWordsRegExp . ')?:?( )?' . $this->page;
        $pagesRegExp = '(' . $pageWordsRegExp . ')?:?( )?' . $this->pageRange;

        $this->startPagesRegExp = $startPagesRegExp;
        
        // single page or page range, must be preceded by pp
        $this->pageRegExpWithPp = $pageRegExpWithPp;
        // page range, must be preceded by pp
        $this->pagesRegExpWithPp = $pagesRegExpWithPp;
        // single page or page range, pp before is optional
        $this->pageRegExp = $pageRegExp;
        // page range, pp before is optional
        $this->pagesRegExp = $pagesRegExp;

        //$this->pagesRegExp = '([Pp]p\.?|[Pp]\.|[Pp]ages?|გვ\.)?:?( )?' . $this->pageRange;
        //$this->pagesRegExpWithPp = '([Pp]p\.?|[Pp]\.|[Pp]ages?):?( )?' . $this->pageRange;
        //$this->startPagesRegExp = '/(^pages? |^pp\.? ?|^p\. ?|^p ?|^стр\. ?|^hlm\. ?|^hal\. ?|^S.\.|^ss?\. ?|^გვ\. ?)[0-9]/i';

        // en for Spanish (and French?), em for Portuguese
        $this->inRegExp1 = '/^[iIeE]n:? /';
        $this->inRegExp2 = '/( [iIeE]n: |[,.] [IiEe]n | [ei]n\) | [eE]m: |[,.] [Ee]m | [ei]m\) )/';

        $this->startForthcomingRegExp = '^\(?forthcoming( at| in)?\)?|^in press|^accepted( at)?|^to appear in';
        $this->forthcomingRegExp = 'forthcoming( at| in)?|in press|accepted( at)?|to appear in';
        $this->endForthcomingRegExp = '( |\()(forthcoming|in press|accepted|to appear)\.?\)?$';
        $this->forthcomingRegExp2 = '/^[Ii]n [Pp]ress/';
        $this->forthcomingRegExp3 = '/^[Aa]ccepted/';
        $this->forthcomingRegExp4 = '/[Ff]orthcoming\.?\)?$/';
        $this->forthcomingRegExp5 = '/[Ii]n [Pp]ress\.?\)?$/';
        $this->forthcomingRegExp1 = '/^[Ff]orthcoming/';
        $this->forthcomingRegExp6 = '/[Aa]ccepted\.?\)?$/';
        $this->forthcomingRegExp7 = '/^[Tt]o appear in/';

        // If next reg exp works, (conf\.|conference) can be deleted, given '?' at end.
        // Could add "symposium" to list of words
        //$this->proceedingsRegExp = '(^proceedings of |^conference on |^((19|20)[0-9]{2} )?(.*)(international )?conference|symposium on | meeting |congress of the | conference proceedings| proceedings of the (.*) conference|^proc\..*(conf\.|conference)?| workshop|^actas del )';
        $this->proceedingsRegExp = '(^proceedings of |proceedings of the (.*) (conference|congress)|conference|symposium on | meeting |congress of the |^proc\.| workshop|^actas del )';
        $this->proceedingsExceptions = '^Proceedings of the American Mathematical Society|^Proceedings of the VLDB Endowment|^Proceedings of the AMS|^Proceedings of the National Academy|^Proc\.? Natl?\.? Acad|^Proc\.? Amer\.? Math|^Proc\.? National Acad|^Proceedings of the [a-zA-Z]+ Society|^Proc\.? R\.? Soc\.?|^Proc\.? Roy\.? Soc\.? A|^Proc\.? Roy\.? Soc\.?|^Proceedings of the International Association of Hydrological Sciences|^Proc\.? IEEE(?! [a-zA-Z])|^Proceedings of the IEEE(?! (International )?(Conference|Congress))|^Proceedings of the IRE|^Proc\.? Inst\.? Mech\.? Eng\.?|^Proceedings of the American Academy|^Proceedings of the American Catholic|^Carnegie-Rochester conference';

        $this->thesisRegExp = '[ \(\[]([Tt]hesis|[Tt]esis|[Dd]issertation|[Tt]hèse|[Tt]esis|[Tt]ese|{Tt]ezi|[Dd]issertação)([ \.,\)\]]|$)';
        $this->masterRegExp = '[Mm]aster(\'?s)?( Degree)?,?|M\.?A\.?|M\.?Sc\.?|Yayınlanmamış Yüksek [Ll]isans|Yüksek [Ll]isans|Masterproef';
        $this->phdRegExp = 'Ph[Dd]|Ph\. ?D\.?|[Dd]octoral|[Dd]oktora';
        $this->fullThesisRegExp = '(((' . $this->phdRegExp . '|' . $this->masterRegExp . ') ([Tt]hesis|[Tt]esis|[Dd]iss(ertation|\.)))|[Tt]hèse de doctorat|[Tt]hèse de master|Tesis doctoral|Tesis de grado|Tesis de maestría|Tese de doutorado|Tese \(doutorado\)|Dissertação de Mestrado|Tese de mestrado|Doctoraal proefschrift|Masterproef|Doktorská práce|Diplomová práce|[Tt]ezi|Yayımlanmamış doktora tezi|Doktora Tezi|Yüksek lisans tezi|Yükseklisans Tezi)';
        // Variant in French: Thèse de Doctorat en droit, Thèse de Doctorat en droit public
        // pt: Dissertação de Mestrado | Tese de mestrado
        // es: Tesis de maestría
        // nl: Masterproef | Doctoraal proefschrift
        // fr: thèse de master
        // my: မဟာဘွဲ့စာတမ်း | ပါရဂူစာတမ်း
        // cz: Doktorská práce | Diplomová práce

        $this->inReviewRegExp1 = '/[Ii]n [Rr]eview\.?\)?$/';
        $this->inReviewRegExp2 = '/^[Ii]n [Rr]eview/';
        $this->inReviewRegExp3 = '/(\(?[Ii]n [Rr]eview\.?\)?)$/';

        $this->isbnRegExp1 = 'ISBN(-(10|13))?:? ?';
        // ISBN should not have spaces, but allow them.  (ISBN has 10 or 13 digits.)
        $this->isbnRegExp2 = '[0-9X -]{10,17}';

        $issnNumberFormat = '[0-9]{4}-[0-9]{3}[0-9X]';
        $this->issnRegExps = [
            '/[Oo]nline(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[Pp]rint(:|: | )' . $issnNumberFormat . '(\)|,|.| |$)/',
            '/[( ,]ISSN(:|: | )' . $issnNumberFormat . ' ?[( ](print|digital)(\) ?| |$)(' . $issnNumberFormat . ' ?[( ](print|digital)(\)| |$))?/',
            '/[( ,](e-|p-)?ISSN(:|: | )(\(?(online|digital|print)[) ])?' . '(e-?|p-?)?' . $issnNumberFormat . '(\)|,|.| |$)/',
        ];

        $this->oclcRegExp1 = 'OCLC:? ';
        $this->oclcRegExp2 = '[0-9]+';

        $this->journalWord = 'Journal';

        $this->bookTitleAbbrevs = ['Proc', 'Amer', 'Conf', 'Cont', 'Sci', 'Int', "Auto", 'Symp'];

        $this->workingPaperRegExp = '(preprint|arXiv preprint|bioRxiv|working paper|texto para discussão|discussion paper|'
                . 'technical report|report no.|'
                . 'research paper|mimeo|unpublished paper|unpublished manuscript|manuscript|'
                . 'under review|submitted|in preparation)';
        // Working paper number can contain letters and dashes, but must contain at least one digit
        // (otherwise word following "manuscript" will be matched, for example)
        $this->workingPaperNumberRegExp = ' (\\\\#|number|no\.?)? ?(?=.*[0-9])([a-zA-Z0-9\-]+),?';

        $this->retrievedFromRegExp1 = [
            'en' => '(Retrieved from:? |Available( at)?:? )',
            'cz' => '(Dostupné z:? |načteno z:? )',
            'fr' => '(Récupéré sur |Disponible( à)?:? )',
            'es' => '(Obtenido de |Disponible( en)?:? )',
//            'id' => '(Diambil kembali dari )',
            'pt' => '(Disponível( em)?:? |Obtido de:? )',
            'my' => '(Retrieved from |Available( at)?:? )',
            'nl' => '(Opgehaald van |Verkrijgbaar( bij)?:? |Available( at)?:? )',
        ];

        // Dates are between 8 and 23 characters long (13 de novembre de 2024,)
        $dateRegExp = '[a-zA-Z0-9,/\-\. ]{8,23}';
        $this->retrievedFromRegExp2 = [
            'en' => '[Rr]etrieved (?P<date1>' . $dateRegExp . ' )?(, )?from |[Aa]ccessed (?P<date2>' . $dateRegExp . ' )?at ',
            'cz' => '[Dd]ostupné (?P<date1>' . $dateRegExp . ' )?(, )?z |[Zz]přístupněno (?P<date2>' . $dateRegExp . ' )?na ',
            'fr' => '[Rr]écupéré (le )?(?P<date1>' . $dateRegExp . ' )?,? ?(sur|de) |[Cc]onsulté (le )?(?P<date2>' . $dateRegExp . ' )?,? ?(à|sur|de) ',
            'es' => '[Oo]btenido (el )?(?P<date1>' . $dateRegExp . ' )?de |[Rr]ecuperado (el )?(?P<date1>' . $dateRegExp . ' )?de |[Aa]ccedido (?P<date2>' . $dateRegExp . ' )?en ',
            'pt' => '[Oo]btido (?P<date1>' . $dateRegExp . ' )?de |[Aa]cesso (?P<date2>' . $dateRegExp . ' )?em ',
            'my' => '[Rr]etrieved (?P<date1>' . $dateRegExp . ' )?(, )?from |[Aa]ccessed (?P<date2>' . $dateRegExp . ' )?at ',
            'nl' => '[Oo]pgehaald (?P<date1>' . $dateRegExp . ' )?(, )?van |[Gg]eraadpleegd op (?P<date2>' . $dateRegExp . ' )?om ',
        ];

        $this->accessedRegExp1 = [
            'en' => '([Ll]ast )?([Rr]etrieved|[Aa]ccessed|[Vv]iewed|[Vv]isited)( on)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'cz' => '([Nn]ačteno|[Zz]přístupněno|[Zz]obrazeno)( dne)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'fr' => '([Rr]écupéré |[Cc]onsulté )(le )?(?P<date2>' . $dateRegExp . ')',
            'es' => '([Oo]btenido|[Aa]ccedido)[,:]? (?P<date2>' . $dateRegExp . ')',
            'pt' => '([Oo]btido |[Aa]cesso (em:?)? )(?P<date2>' . $dateRegExp . ')',
            'my' => '([Ll]ast )?([Rr]etrieved|[Aa]ccessed|[Vv]iewed)( on)?[,:]? (?P<date2>' . $dateRegExp . ')',
            'nl' => '([Oo]opgehaald op|[Gg]eraadpleegd op|[Bb]ekeken|[Gg]eopend),? (?P<date2>' . $dateRegExp . ')',
        ];

        // Month abbreviations in many languages: https://web.library.yale.edu/cataloging/months
        // Do not add ';' to following list of punctuation marks
        $p = '[.,]?';
        $this->monthsRegExp = [
            'en' => '(?P<m1>January|Jan' . $p . ')|(?P<m2>February|Feb' . $p . ')|(?P<m3>March|Mar' . $p . ')|(?P<m4>April|Apr' . $p . ')|'
                . '(?P<m5>May)|(?P<m6>June|Jun' . $p . ')|(?P<m7>July|Jul' . $p . ')|(?P<m8>August|Aug' . $p . ')|'
                . '(?P<m9>September|Sept?' . $p . ')|(?P<m10>October|Oct' . $p . ')|(?P<m11>November|Nov' . $p . ')|(?P<m12>December|Dec' . $p . ')',
            'cz' => '(?P<m1>leden|led' . $p . ')|(?P<m2>únor|ún' . $p . ')|(?P<m3>březen|břez' . $p . ')|(?P<m4>duben|dub' . $p . ')|'
                . '(?P<m5>květen|květ' . $p . ')|(?P<m6>červen|červ' . $p . ')|(?P<m7>červenec|červen' . $p . ')|(?P<m8>srpen|srp' . $p . ')|'
                . '(?P<m9>září|zář' . $p . ')|(?P<m10>říjen|říj' . $p . ')|(?P<m11>listopad|list' . $p . ')|(?P<m12>prosinec|pros' . $p . ')',
            'fr' => '(?P<m1>janvier|janv' . $p . ')|(?P<m2>février|févr' . $p . ')|(?P<m3>mars)|(?P<m4>avril|avr' . $p . ')|'
                . '(?P<m5>mai)|(?P<m6>juin)|(?P<m7>juillet|juill?' . $p . ')|(?P<m8>aout|août)|'
                . '(?P<m9>septembre|sept?' . $p . ')|(?P<m10>octobre|oct' . $p . ')|(?P<m11>novembre|nov' . $p . ')|(?P<m12>décembre|déc' . $p . ')',
            // 'id' => '(?P<m1>Januari|Jan' . $p . '|Djan' . $p . ')|(?P<m2>Februari|Peb' . $p . ')|(?P<m3>Maret|Mrt' . $p . ')|(?P<m4>April|Apr' . $p . ')|'
            //     . '(?P<m5>Mei)|(?P<m6>Juni|Djuni)|(?P<m7>Juli|Djuli)|(?P<m8>Augustus|Ag' . $p . ')|'
            //     . '(?P<m9>September|Sept' . $p . ')|(?P<m10>Oktober|Okt' . $p . ')|(?P<m11>November|Nop' . $p . ')|(?P<m12>Desember|des' . $p . ')',
            'es' => '(?P<m1>enero)|(?P<m2>febrero|feb' . $p . ')|(?P<m3>marzo|mar' . $p . ')|(?P<m4>abril|abr' . $p . ')|'
                . '(?P<m5>mayo)|(?P<m6>junio|jun' . $p . ')|(?P<m7>julio|jul' . $p . ')|(?P<m8>agosto)|'
                . '(?P<m9>septiembre|sept?' . $p . ')|(?P<m10>octubre|oct' . $p . ')|(?P<m11>noviembre|nov' . $p . ')|(?P<m12>deciembre|dec' . $p . ')',
            'pt' => '(?P<m1>janeiro|jan' . $p . ')|(?P<m2>fevereiro|fev' . $p . ')|(?P<m3>março|mar' . $p . ')|(?P<m4>abril|abr' . $p . ')|'
                . '(?P<m5>maio|mai' . $p . ')|(?P<m6>junho|jun' . $p . ')|(?P<m7>julho|jul' . $p . ')|(?P<m8>agosto|ago' . $p . ')|'
                . '(?P<m9>setembro|set' . $p . ')|(?P<m10>outubro|oct' . $p . ')|(?P<m11>novembro|nov' . $p . ')|(?P<m12>dezembro|dez' . $p . ')',
            'my' => '(?P<m1>ဇန်နဝါရီလ)|(?P<m2>ဖေဖော်ဝါရီ)|(?P<m3>မတ်လ)|(?P<m4>ဧပြီလ)|'
                . '(?P<m5>မေ)|(?P<m6>ဇွန်လ)|(?P<m7>ဇူလိုင်လ)|(?P<m8>ဩဂုတ်လ)|'
                . '(?P<m9>စက်တင်ဘာ)|(?P<m10>အောက်တိုဘာလ)|(?P<m11>နိုဝင်ဘာလ)|(?P<m12>ဒီဇင်ဘာ)',
            'nl' => '(?P<m1>januari|jan' . $p . ')|(?P<m2>februari|febr' . $p . ')|(?P<m3>maart|mrt' . $p . ')|(?P<m4>april|apr' . $p . ')|'
                . '(?P<m5>mei)|(?P<m6>juni)|(?P<m7>juli)|(?P<m8>augustus|aug' . $p . ')|'
                . '(?P<m9>september|sep' . $p . ')|(?P<m10>oktober|okt' . $p . ')|(?P<m11>november|nov' . $p . ')|(?P<m12>december|dec' . $p . ')',
        ];

        $this->monthsAbbreviations = [
            'en' => ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Sept', 'Oct', 'Nov', 'Dec'],
            'cz' => ['led', 'ún', 'břez', 'dub', 'květ', 'červ', 'červen', 'srp', 'zář', 'říj', 'list', 'pros'],
            'fr' => ['janv', 'févr', 'avr', 'juil', 'juill', 'sept', 'oct', 'nov', 'déc'],
            'es' => ['feb', 'mar', 'abr', 'jun', 'jul', 'set', 'sept', 'oct', 'nov', 'dec'],
            'pt' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dez'],
            'my' => ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Sept', 'Oct', 'Nov', 'Dec'],
            'nl' => ['jan', 'febr', 'mrt', 'apr', 'aug', 'sep', 'Okt', 'nov', 'dec'],
        ];

        $this->vonNames = VonName::all()->pluck('name')->toArray();

        // Codes are ended by } EXCEPT \em, \it, and \sl, which have to be ended by something like \normalfont.  Code
        // that gets italic text handles only the cases in which } ends italics.
        // \enquote{ is not a signal of italics, but it is easiest to classify it thus.
        $this->italicCodes = ["\\textit{", "\\textsl{", "\\textsc{", "\\emph{", "{\\em ", "\\em ", "{\\it ", "\\it ", "{\\sl ", "\\sl ", "\\enquote{"];
        $this->boldCodes = ["\\textbf{", "{\\bf ", "{\\bfseries "];
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

        $language = $language ?: $conversion->language;
        $charEncoding = $charEncoding ?: $conversion->char_encoding;
        $use = $use ?: $conversion->use;

        $phrases = $this->phrases[$language];

        // Remove comments and concatenate lines in entry
        // (do so before cleaning text, otherwise \textquotedbleft, e.g., at end of line will not be cleaned)
        $entryLines = explode("\n", $rawEntry);

        $entry = '';
        foreach ($entryLines as $line) {
            $truncated = $this->uncomment($line);
            $entry .= $line . (! $truncated ? ' ' : '');
        }

        if (!$entry) {
            return null;
        }

        // If entry has some HTML markup that will be useful, translate it to TeX
        $entry = str_replace(['<em>', '</em>'], ['\textit{', '}'], $entry);

        // Remove remaining HTML markup
        $entry = strip_tags($entry);
        $originalEntry = $entry;

        // Note that cleanText translates « and », and „ and ”, to `` and ''.
        $entry = $this->cleanText($entry, $charEncoding, $language);

        $entry = str_replace(['[Google Scholar]', '[PubMed]', '[Green Version]', '[CrossRef]'], '', $entry);

        // Replace "\' " with "\'" because "\' abc" is equivalent to "\'abc", and the space causes problems if it is within a name.
        $entry = str_replace("\' ", "\'", $entry);

        $firstComponent = 'authors';
        // If entry starts with year, extract it.
        if (preg_match('/^(?P<year>[1-9][0-9]{3})\*? (?P<remainder>.*)$/', $entry, $matches)) {
            $firstComponent = 'year';
            $year = $matches['year'];
            $remainder = ltrim($matches['remainder'], ' |*+');
        }

        if ($firstComponent == 'authors') {
            // interpret string like [Arrow12] at start of entry as label
            if (preg_match('/^(?P<label>[\[{][a-zA-Z0-9:]{3,10}[\]}]) (?P<entry>.*)$/', $entry, $matches)) {
                if ($matches['label'] && preg_match('/[a-z]/', $matches['label'])) {
                    $itemLabel = $matches['label'];
                    $entry = $matches['entry'] ?? '';
                }
            }
            // Remove numbers and other symbols at start of entry, like '6.' or '[14]'.
            if (substr($entry, 0, 1) == '"' && substr($entry, -1) == '"') {
                $entry = substr($entry, 1, strlen($entry) - 2);
            }
            $entry = ltrim($entry, ' .0123456789[]()|*+:^');

            // If entry starts with '\bibitem [abc] {<label>}', get <label> and remove '\bibitem' and arguments
            if (preg_match('/^\\\bibitem *(\[[^\]]*\])? *{(?P<label>[^}]*)}(?P<entry>.*)$/', $entry, $matches)) {
                if ($matches['label'] && !$conversion->override_labels) {
                    $itemLabel = $matches['label'];
                }
                $entry = $matches['entry'];
            }

            $starts = ["\\noindent", "\\smallskip", "\\item", "\\bigskip"];
            foreach ($starts as $start) {
                if (Str::startsWith($entry, $start)) {
                    $entry = trim(substr($entry, strlen($start)));
                }
            }
        }

        if (! strlen($entry)) {
            return null;
        }

        // Don't put the following earlier---{} may legitimately follow \bibitem
        $entry = str_replace("{}", "", $entry);

        // It could be that a [J] at the end signifies a journal article, in which case that info could be used.
        $entry = Str::replaceEnd('[J].', '', $entry);
        $entry = Str::replaceEnd('[J]', '', $entry);

        $entry = Str::replaceEnd('\\', '', $entry);

        // If first component is authors and entry starts with [n] or (n) for some number n, eliminate it
        if ($firstComponent == 'authors') {
            $entry = preg_replace("/^\s*\[\d*\]|^\s*\(\d*\)/", "", $entry);
        }

        $entry = ltrim($entry, ' {');
        $entry = rtrim($entry, ' }');

        $this->verbose(['item' => $entry]);
        if ($itemLabel) {
            $this->verbose(['label' => $itemLabel]);
        }

        $isArticle = $containsPageRange = $containsProceedings = false;

        /////////////////////////////////////////////////////////////////
        // If first component is year, get it and remove it from entry //
        /////////////////////////////////////////////////////////////////

        if ($firstComponent == 'year') {
            $this->setField($item, 'year', $year, 'setField 114');
        } else {
            $remainder = $entry;
        }

        // Remove spaces before commas (assumed to be errors)
        $remainder = str_replace(' ,', ',', $remainder);

        $completeEntry = $remainder;

        ////////////////////
        // Get doi if any //
        ////////////////////

        $urlRegExp = '(\\\url{|\\\href{)?(?P<url>https?://\S+)(})?';

        $retrievedFromRegExp1 = $this->retrievedFromRegExp1[$language];
        $retrievedFromRegExp2 = $this->retrievedFromRegExp2[$language];
        $accessedRegExp1 = $this->accessedRegExp1[$language];

        // doi in a Markdown-formatted url
        if (preg_match('%\[https://doi\.org/(?P<doi1>[^ \]]+)\]\(https://doi\.org/(?P<doi2>[^ \)]+)\)%', $remainder, $matches)) {
            $doi = $matches['doi1'];
            $doi1 = $matches['doi2'];
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

            $dateResult = $this->isDate(trim($date, ' .,'), $language, 'contains');

            if ($dateResult) {
                $accessDate = $dateResult['date'];
                $this->setField($item, 'urldate', rtrim($accessDate, '., '), 'setField 5c');
                $containsUrlAccessInfo = true;
            }
        }

        if (empty($doi)) {
            $doi = $this->extractLabeledContent(
                $remainder,
                ' [\[\)]?doi:? | [\[\(]?doi: ?|;doi:|(Available (from|at):? )?(\\\href\{|\\\url{)?https?://dx\.doi\.org/|(Available (from|at):? )?(\\\href\{|\\\url{)?https?://doi\.org/|doi\.org',
                '[^ ]+'
            );
        }

        // Every doi starts with '10.'.  URL may be something like https://doi-something.univ.edu.
        if (empty($doi)) {
            preg_match('%https?://[a-zA-Z\-\.]*/(?P<doi>10\.[^ ]+)%', $remainder, $matches);
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
        if (in_array($use, ['latex', 'biblatex'])) {
            $doi = preg_replace('/([^\\\])_/', '$1\_', $doi);
        }

        // In case doi is repeated, as in \href{https://doi.org/<doi>}{<doi>} (in which case second {...} will remain)
        $remainder = str_replace('{\tt ' . $doi . '}', '', $remainder);
        $remainder = str_replace('{' . $doi . '}', '', $remainder);
        // $remainder = str_replace($doi, '', $remainder);

        if ($doi) {
            $this->setField($item, 'doi', $doi, 'setField 1');
            $hasDoi = true;
        } else {
            $this->verbose("No doi found.");
            $hasDoi = false;
        }

        //////////////////////////////
        // Get PMID and PMCIDif any //
        //////////////////////////////

        if (preg_match('/pmid: [0-9]{6,9}/i', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
            $this->addToField($item, 'note', $matches[0][0], 'addToField 1a');
            $remainder = substr($remainder, 0, $matches[0][1]) . substr($remainder, $matches[0][1] + strlen($matches[0][0]));
            $remainder = trim($remainder, ' .');
        }

        if (preg_match('/pmcid: [A-Z]{1,4}[0-9]{6,9}/i', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
            $this->addToField($item, 'note', $matches[0][0], 'addToField 1b');
            $remainder = substr($remainder, 0, $matches[0][1]) . substr($remainder, $matches[0][1] + strlen($matches[0][0]));
            $remainder = trim($remainder, ' .');
        }

        //////////////////////////////////////
        // Get arXiv or bioRxiv info if any //
        //////////////////////////////////////

        $hasArxiv = false;
        $hasFullDate = false;

        preg_match('/ arxiv[:,] ?(?P<afterArxiv>.*)$/i', $remainder, $matches1, PREG_OFFSET_CAPTURE);
        if (isset($matches1['afterArxiv'])) {
            $hasArxiv = true;
            $dateResult = $this->isDate($matches1['afterArxiv'][0], $language, 'starts');
            if ($dateResult) {
                $this->setField($item, 'archiveprefix', 'arXiv', 'setField 2b');
                $this->setField($item, 'year', $dateResult['year'], 'setField 3b');
                $this->setField($item, 'date', $dateResult['year'] . '-' . (strlen($dateResult['monthNumber']) == 1 ? '0' : '') . $dateResult['monthNumber'] . '-' . $dateResult['day'], 'setField 4b');
                $hasFullDate = true;
                $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($dateResult['date']));
            } else {
                if (preg_match('/^(?P<year>(19|20)[0-9]{2})[,.]? /', $matches1['afterArxiv'][0], $matches2)) {
                    $this->setField($item, 'archiveprefix', 'arXiv', 'setField 2b');
                    $this->setField($item, 'year', $matches2['year'], 'setField 2c');
                    $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($matches2[0]));
                } else {
                    preg_match('/^\S+/', $matches1['afterArxiv'][0], $eprintMatches, PREG_OFFSET_CAPTURE);
                    $eprint = $eprintMatches[0][0];
                    if ($eprint) {
                        $this->setField($item, 'archiveprefix', 'arXiv', 'setField 2');
                        $this->setField($item, 'eprint', rtrim($eprint, '}.,'), 'setField 3');
                        $remainder = substr($remainder, 0, $matches1[0][1]) . ' ' . substr($remainder, $matches1['afterArxiv'][1] + strlen($eprint));
                    }
                }
            }
        }

        //$eprint = $this->extractLabeledContent($remainder, ' arxiv[:,] ?', '\S+');

        $eprint = $this->extractLabeledContent($remainder, '([Ii]n|{\\\em)? bioRxiv[ }]?', '\S+ \S+');

        if ($eprint) {
            $this->setField($item, 'archiveprefix', 'bioRxiv', 'setField 2a');
            $this->setField($item, 'eprint', trim($eprint, '()'), 'setField 3a');
            $hasArxiv = true;
        }

        ////////////////////////////////////
        // Get url and access date if any //
        ////////////////////////////////////

        $remainder = preg_replace('/[\(\[](online|en ligne|internet)[\)\]]/i', '', $remainder, 1, $replacementCount);
        $onlineFlag = $replacementCount > 0;

        // Retrieved from (site)? <url> accessed <date>
        preg_match(
            '%(?P<retrievedFrom> ' . $retrievedFromRegExp1 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '[.,]? \[?' . $accessedRegExp1 . '\]?$%i',
            $remainder,
            $matches,
        );

        // Retrieved from (site)? <url> <date>?
        if (! count($matches)) {
            preg_match(
                '%(?P<retrievedFrom> ' . $retrievedFromRegExp1 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<date1> .*)?$%i',
                $remainder,
                $matches,
            );
        }

        // Retrieved <date>? from <url> <note>?
        if (! count($matches)) {
            preg_match(
                '%(?P<retrievedFrom> ' . $retrievedFromRegExp2 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<note> .*)?$%iJ',
                $remainder,
                $matches,
            );
        }

        // <url> accessed <date>
        if (! count($matches)) {
            preg_match(
                '%(url: ?)?' . $urlRegExp . ',? ?\(?' . $accessedRegExp1 . '\)?\.?$%i',
                $remainder,
                $matches,
            );
        }

        // accessed <date> <url>
        if (! count($matches)) {
            preg_match(
                '%' . $accessedRegExp1 . '\.? ' . $urlRegExp . '$%i',
                $remainder,
                $matches,
            );
        }

        // accessed <date> [no url]
        if (! count($matches)) {
            preg_match(
                '%' . $accessedRegExp1 . '\.?$%i',
                $remainder,
                $matches,
            );
        }

        // <url> <note>
        if (! count($matches)) {
            preg_match(
                '%(url: ?)?' . $urlRegExp . '(?P<note>.*)$%i',
                $remainder,
                $matches,
            );
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
            $trimmedNote = trim($note);
            if ($this->isYear($trimmedNote)) {
                $this->setField($item, 'year', $trimmedNote, 'setField 4c');
                $note = null;
            }

            $dateResult = $this->isDate(trim($date, ' .,'), $language, 'contains');

            if ($dateResult) {
                $accessDate = $dateResult['date'];
            }
        }

        $containsUrlAccessInfo = false;
        $urlHasPdf = false;

        // Write access date even if there is no URL.  (Presumably "accessed ..." means it is in fact an online item.)
        if (! empty($accessDate)) {
            $this->setField($item, 'urldate', rtrim($accessDate, '., '), 'setField 5a');
            $containsUrlAccessInfo = true;
        }

        if (! empty($url)) {
            $url = trim($url, '{)}],. ');
            $this->setField($item, 'url', $url, 'setField 4');
            if (Str::endsWith($url, ['.pdf'])) {
                $urlHasPdf = true;
            }
            if (! empty($year)) {
                $this->setField($item, 'year', $year, 'setField 5b');
            }
            if (! empty($siteName)) {
                $this->addToField($item, 'note', trim(trim($retrievedFrom . $siteName, ':,; ') . $note, '{}'), 'addToField 2');
            }
        } else {
            $this->verbose("No url found.");
        }

        /////////////////////
        // Get ISBN if any //
        /////////////////////

        $containsIsbn = false;
        $match = $this->extractLabeledContent($remainder, ' \(?' . $this->isbnRegExp1, $this->isbnRegExp2 . '\)?');
        if ($match) {
            $containsIsbn = true;
            $this->setField($item, 'isbn', trim(str_replace(' ', '', $match), '()'), 'setField 16');
        }
        
        ///////////////////////////////
        // Put ISSN, if any, in note //
        ///////////////////////////////

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
                $this->addToField($item, 'note', trim($issn, '(., '), 'addToField 2a');
                $remainder = str_replace($matches[0], '', $remainder);
                $containsIssn = true;
                break;
            } 
        }

        /////////////////////
        // Get OCLC if any //
        /////////////////////

        $match = $this->extractLabeledContent($remainder, ' ' . $this->oclcRegExp1, $this->oclcRegExp2);
        if ($match) {
            $this->setField($item, 'oclc', $match, 'setField 17a');
        }

        ////////////////////////
        // Get chapter if any //
        ////////////////////////

        $match = $this->extractLabeledContent($remainder, ' chapter ', '[1-9][0-9]?');
        if ($match) {
            $this->setField($item, 'chapter', $match, 'setField 17b');
        }

        /////////////////////////////////////
        // Put "Translated by ..." in note //
        /////////////////////////////////////

        $containsTranslator = false;
        // Translators: string up to first period preceded by a lowercase letter.
        // Case of "Trans." is handled later, after item type is determined, because "Trans." is an abbreviation used in journal names.
        $result = $this->findRemoveAndReturn($remainder, '[Tt]ranslat(ed|ion) by .*?[a-z]\)?(\.| \()', false);
        if ($result) {
            $this->addToField($item, 'note', trim(ucfirst($result[0]), ')( '), 'addToField 3');
            $before = Str::replaceEnd(' and ', '', $result['before']);
            $remainder = $before . (! Str::endsWith($before, ['.', '. ']) ? '. ' : '') . $result['after'];
            $containsTranslator = true;
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
        
        // Put space between , and ` or ' (assumed to be typo)
        $remains = str_replace([',`', '( ', ' )'], [', `', '(', ')'], $remains);
        $remains = str_replace([",'"], [", '"], $remains);
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
            // Precede jounnal name by space or {, so that subsets of journal names are not matched (e.g. JASA and EJASA).
            // { allowed because journal name might be preceded by \textit{.
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

        $this->verbose("Looking for authors ...");

        $isEditor = false;

        //$authorTitle = null;
        if ($language == 'my') {
            preg_match('/^(?P<author>[^,]*, ?[^,]*), ?(?P<remainder>.*)$/', $remainder, $matches);
            //$this->setField($item, 'author', rtrim($words[0], ',') ?? '', 'setField m1');
            $authorConversion = ['authorstring' => $matches['author'], 'warnings' => []];
            array_shift($words);
            //$authorTitle = rtrim($words[0], ',') ?? '';
            array_shift($words);
            $year = trim($words[0], '(),');
            array_shift($words);
            $isEditor = false;
            $month = $day = $date = null;
            $itemKind = 'book';
            $remainder = implode(' ', $words);
        // Entry starts ______ or --- [i.e. author from previous entry]
        } elseif (isset($words[0]) && preg_match('/^[_-]+[.,]?$/', $words[0])) {
            $authorConversion = ['authorstring' => $previousAuthor, 'warnings' => []];
            $month = $day = $date = null;
            $isEditor = false;
            array_shift($words);
            if (isset($words[0]) && preg_match('/^[\(\[]?(?P<year>(19|20)[0-9]{2})[a-z]?[\)\]]?$/', rtrim($words[0], '.,'), $matches)) {
                $year = $matches['year'];
                array_shift($words);
            } else {
                $year = null;
            }
            $remainder = implode(' ', $words);
        } else {
            $authorConversion = $this->convertToAuthors($words, $remainder, $year, $month, $day, $date, $isEditor, true, 'authors', $language);
            $authorIsOrganization = $authorConversion['organization'] ?? false;
        }

        // restore rest of $completeRemainder
        $remainder = $remainder . ' ' . substr($completeRemainder, $mismatchPosition);

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
        if ($authorstring and $authorstring[0] == '{') {
            $authorstring = strstr($authorstring, ' ');
        }

        if ($month) {
            $monthResult = $this->fixMonth($month, $language);
            $this->setField($item, 'month', $monthResult['months'], 'setField 112');
            if ($day) {
                $this->setField($item, 'date', $year . '-' . $monthResult['month1number'] . '-' . (strlen($day) == 1 ? '0' : '') . $day, 'setField 112a');
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

        if ($isEditor === false && ! Str::contains($authorstring, $editorPhrases)) {
            $this->setField($item, 'author', rtrim($authorstring, ','), 'setField 7');
        } else {
            $this->setField($item, 'editor', trim(str_replace($editorPhrases, "", $authorstring), ' ,'), 'setField 8');
        }

        $hasSecondaryDate = false;
        if ($year) {
            $this->setField($item, 'year', $year, 'setField 9');
            if (preg_match('/[\(\[]?[0-9]{4}[\)\]]? \[[0-9]{4}\]/', $year)) {
                $hasSecondaryDate = true;
            }
        }

        $remainder = trim($remainder, '.},;/ ');
        $this->verbose("[1] Remainder: " . $remainder);

        ////////////////////
        // Look for title //
        ////////////////////

        unset($this->italicTitle);

        $remainder = ltrim($remainder, ': ');

        $title = null;
        $titleEndsInPeriod = false;
        $titleStyle = '';
        $containsEdition = false;

        // Does title start with "Doctoral thesis:" or something like that?
        $containsThesis = false;
        if (preg_match('/^' . $this->fullThesisRegExp . '(: | -)(?P<remainder>.*)$/i', $remainder, $matches)) {
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
        
        $style = '';
        if (! $title) {
            $title = $this->getQuotedOrItalic($remainder, true, false, $before, $after, $style);
            $titleStyle = $style;
            $newRemainder = $before . ltrim($after, "., ");
        }

        if ($language == 'my') {
            $title = (string) $title;
            $this->setField($item, 'title', trim($title, ', '), 'setField m3');
            $quoteCount = substr_count($newRemainder, '"');
            if ($quoteCount == 4) {
                preg_match('/^"(?P<edition>[^"]+)"(?P<remainder>.*)$/', $newRemainder, $matches);
                if (isset($matches['edition'])) {
                    $this->setField($item, 'edition', trim($matches['edition'], ', '), 'setField m4');
                }
                $newRemainder = $remainder = isset($matches['remainder']) ? trim($matches['remainder']) : '';
            }
        } else {
            // If title has been found and ends in edition specification, take that out and put it in edition field
            $editionRegExp = '/(\(' . $this->editionRegExp . '\)$|' . $this->editionRegExp . ')[.,]?$/iJ';
            if ($title && preg_match($editionRegExp, (string) $title, $matches)) {
                $this->setField($item, 'edition', trim($matches['edition'], ',. '), 'setField 108');
                $title = trim(Str::replaceLast($matches[0], '', $title));
            }

            if (! $title) {
                $originalRemainder = $remainder;
                $title = $this->getTitle($remainder, $edition, $volume, $isArticle, $year, $note, $journal, $containsUrlAccessInfo, false, $language);
                if (substr($originalRemainder, strlen($title), 1) == '.') {
                    $titleEndsInPeriod = true;
                }

                if (! isset($item->year) && $year) {
                    $this->setField($item, 'year', $year, 'setField 10a');
                    $remainder = str_replace($year, '', $remainder);
                }
                if ($edition) {
                    $this->setField($item, 'edition', $edition, 'setField 10b');
                    $containsEdition = true;
                }
                if ($volume) {
                    $this->setField($item, 'volume', $volume, 'setField 11');
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

            if (substr($title, -2) == "''" && substr_count($title, '``') == 0) {
                $title = substr($title, 0, -2); 
            }

            // The - is in case it is used as a separator
            if (! $titleEndsInPeriod) {
                $titleEndsInPeriod = substr($title, -1) == '.';
            }
            $title = rtrim($title, ' .,-');
            // Remove '[J]' at end of title (why does it appear?)
            if (preg_match('/\[J\]$/', $title)) {
                $title = substr($title, 0, -3);
            }

            $title = trim($title, '_- ');

            if (substr($title, 0, 1) == '{' && substr($title, -1) == '}') {
                $title = trim($title, '{}');
            }
            // Case in which quotation marks were in wrong encoding and appear as ?'s.
            if (substr($title, 0, 1) == '?' && substr($title, -1) == '?') {
                $title = trim($title, '?., ');
            }

            $title = trim($title, ' :');
            $title = Str::replaceEnd('[C]', '', $title);
            $this->setField($item, 'title', $title, 'setField 12');
        }

        $this->verbose("Remainder: " . $remainder);

        ///////////////////////////////////////////////////////////
        // Look for year if not already found                    //
        // (may already have been found at end of author string) //
        ///////////////////////////////////////////////////////////

        $remainderWithMonthYear = $remainder;
        $containsMonth = false;
        if (! isset($item->year)) {
            if (! $year) {
                // Space prepended to $remainder in case it starts with year, because getDate requires space 
                // (but perhaps could be rewritten to avoid it).
                $year = $this->getDate(' ' . $remainder, $newRemainder, $month, $day, $date, false, true, false, $language);
            }

            if ($year) {
                $this->setField($item, 'year', $year, 'setField 13');
            } else {
                $this->setField($item, 'year', '', 'setField 14');
                $warnings[] = "No year found.";
            }

            if (isset($month)) {
                $containsMonth = true;
                $monthResult = $this->fixMonth($month, $language);
                $this->setField($item, 'month', $monthResult['months'], 'setField 15');
            }

            if ($year && isset($month) && ! empty($day)) {
                $day = strlen($day) == 1 ? '0' . $day : $day;
                $this->setField($item, 'date', $year . '-' . $monthResult['month1number'] . '-' . $day, 'setField 15a');
                $hasFullDate = true;
            }

            if (isset($item->url) && ! isset($item->urldate) && $day) {
                $this->setField($item, 'urldate', $date, 'setField 14a');
            }
        }

        $yearIsForthcoming = isset($item->year) && in_array($item->year, ['forthcoming', 'Forthcoming', 'in press', 'In press', 'In Press']);

        $remainder = ltrim($newRemainder, ' ');

        ///////////////////////////////////////////////////////////////////////////////
        // To determine type of item, first record some features of publication info //
        ///////////////////////////////////////////////////////////////////////////////

        // $remainder is item minus authors, year, and title
        $remainder = ltrim($remainder, '.,; ');
        $this->verbose("[type] Remainder: " . $remainder);
        
        $inStart = $containsIn = $italicStart = $containsEditors = $allWordsInitialCaps = false;
        $containsNumber = $containsInteriorVolume = $containsCity = $containsPublisher = false;
        $containsWorkingPaper = $containsFullThesis = false;
        $containsNumberedWorkingPaper = $containsNumber = $pubInfoStartsWithForthcoming = $pubInfoEndsWithForthcoming = false;
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
            $this->verbose("Starts with \"in |In |in: |In: \".");
        }

        if (preg_match($this->inRegExp2, $remainder)) {
            $containsIn = true;
            $this->verbose("Contains \"in |in) |In |in: |In: \".");
        }

        if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
            $italicStart = true;
            $this->verbose("Starts with italics.");
        }

        if (preg_match('/\d/', $remainder) || preg_match('/ [IVXDLC]+[., ]/', $remainder)) {
            $containsNumber = true;
            $this->verbose("Contains a number.");
        }

        // Contains volume designation, but not at start of $remainder
        if (preg_match('/[ \(\[](' . $this->volumeRegExp . ') ?\d/', substr($remainder, 3))) {
            $containsInteriorVolume = true;
            $this->verbose("Contains a volume, but not at start.");
        }

        $volumeNumberPagesRegExp = '/(' . $this->volRegExp3 . ')?[0-9]{1,4} ?(' . $this->numberRegExp . ')?[ \(][0-9]{1,4}[ \)]:? ?(' . $this->pageRegExp . ')/';
        if (preg_match($volumeNumberPagesRegExp, $remainder)) {
            $containsVolumeNumberPages = true;
            $this->verbose("Contains volume-number-pages info.");
        }

        // Contains volume designation
        if (preg_match($this->volRegExp1, $remainder, $matches)) {
            $match = $matches[0];
            if ($match) {
                $this->verbose("Contains a volume designation.");
                $remainderMinusVolume = $this->findAndRemove($remainder, $this->volRegExp0);
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
        $regExpStart = '/^eds?\.|(?<!';
        $regExpEnd1 = '|rev\.)[ \(}](eds?\.|editors?|edited by),/i';
        $regExpEnd2 = '|rev\.), ?[ \(}](eds?\.|editors?|edited by)/i';

        $regExp11 = $regExp12 = $regExp21 = $regExp22 = $regExpStart;
        foreach ($this->ordinals[$language] as $i => $ordinal) {
            $add = ($i ? '|' : '') . $ordinal;
            $regExp11 .= $add;
            $regExp12 .= $add;
            $regExp21 .= $add . '\.';
            $regExp22 .= $add . '\.';
        }
        $regExp11 .= $regExpEnd1;
        $regExp12 .= $regExpEnd2;
        $regExp21 .= $regExpEnd1;
        $regExp22 .= $regExpEnd2;

        if (
            preg_match($this->edsRegExp1, $remainder)
            || preg_match($this->edsRegExp2, $remainder)
            || (
                preg_match($regExp11, $remainder, $matches)
                && preg_match($regExp21, $remainder, $matches)
               )
            || (
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

        if (preg_match('/' . $this->fullThesisRegExp . '/i', $remainder)) {
            $containsFullThesis = true;
            $this->verbose("Contains full thesis.");
        }

        if (preg_match('/' . $this->thesisRegExp . '/', $remainder)) {
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
            preg_match('/[Ff]irst published (18|19|20)[0-9]{2}/', $remainder, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches[0][0])) {
                $hasSecondaryDate = true;
                $this->addToField($item, 'note', $matches[0][0]);
                $remainder = rtrim(substr($remainder, 0, $matches[0][1]), ' .') . '.' . substr($remainder, $matches[0][1] + strlen($matches[0][0]));
            }
        }

        $remainderMinusPubInfo = $remainder;
        $publisher = '';
        foreach ($this->publishers as $pub) {
            if (
                Str::contains(mb_strtolower($remainder), ' ' . mb_strtolower($pub))
                ||
                Str::startsWith(mb_strtolower($remainder), mb_strtolower($pub))
               ) {
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
        if ($this->isAddressPublisher(rtrim($remainder, '.'), finish: false) && ! preg_match('/^Published/', $remainder)) {
            $startsAddressPublisher = true;
            $this->verbose("Remainder has 'address: publisher' format.");
        }

        if (preg_match('/[a-z ]{0,25}: [a-z ]{0,35},?( ' . $this->pagesRegExp . ')?$/i', $remainder)) {
            $endsAddressPublisher = true;
            $this->verbose("Remainder ends with 'address: publisher' format (and possibly page range).");
        }

        $commaCount = substr_count($remainder, ',');
        $this->verbose("Number of commas: " . $commaCount);

        if (isset($this->italicTitle)) {
            $this->verbose("Italic title");
        }

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
                    ! $containsPageRange &&
                    ! $containsJournalName &&
                    ! $containsThesis &&
                    ! Str::contains($item->url, ['journal']) &&
                    // if remainder has address: publisher format, item is book unless author is organization
                    (! $startsAddressPublisher || $authorIsOrganization) &&
                    (! $allWordsInitialCaps || (isset($item->author) && $item->author == $remainder)) &&
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
            if ($hasSecondaryDate || $containsTranslator) {
                $this->verbose("Item type case 5a");
                $itemKind = 'book'; // with editor as well as author
            } else {
                $this->verbose("Item type case 5b");
                $itemKind = 'incollection';
            }
        } elseif (
                ($containsPageRange || $containsInteriorVolume)
                && ! $containsProceedings
                && ! $containsPublisher
                && ! $containsCity
                && ! $endsAddressPublisher
                ) {
            $this->verbose("Item type case 6");
            $itemKind = 'article';
            if (! $this->itemType && ! $itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [2]";
            }
        } elseif ($containsProceedings) {
            $this->verbose("Item type case 7");
            $itemKind = 'inproceedings';
        } elseif ($containsIsbn || (isset($this->italicTitle) && (($containsCity || $containsPublisher) || isset($item->editor)))) {
            $this->verbose("Item type case 8");
            $itemKind = 'book';
        } elseif (! $containsIn && ($pubInfoStartsWithForthcoming || $pubInfoEndsWithForthcoming)) {
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
            if (! $this->itemType && !$itemKind) {
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
        } elseif ($containsDigitOutsideVolume && ! $startsAddressPublisher) {
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

        $this->verbose('Remainder: ' . $remainder);

        switch ($itemKind) {

            /////////////////////////////////////////////
            // Get publication information for article //
            /////////////////////////////////////////////

            case 'article':
                $journalNameMissingButHasVolume = false;
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

                    if ($this->getVolumeNumberPagesForArticle($remainder, $item, $language, true) || preg_match('/^' . $this->volRegExp0 . '/', $remainder)) {
                        $journalNameMissingButHasVolume = true;
                        $warnings[] = "Item seems to be article, but journal name not found.";
                    }

                    if (! $journalNameMissingButHasVolume) {
                        $journal = $this->getJournal($remainder, $item, $italicStart, $pubInfoStartsWithForthcoming, $pubInfoEndsWithForthcoming, $language);
                        $journal = rtrim($journal, ' ,(');
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
                    // Attempt to remove period at end of journal name when it shouldn't be there
                    // But too many strings used as abbreviations in journal names are in the dictionary
                    // E.g. 'electron', 'soc', 'Am', 'phys'.
                    // $journalWords = explode(' ', $journal);
                    // $lastJournalWord = array_pop($journalWords);
                    // if (substr($lastJournalWord, -1) == '.' && $this->inDict(substr($lastJournalWord, 0, -1))) {
                    //     $lastJournalWord = substr($lastJournalWord, 0, -1);
                    //     $journal = substr($journal, 0, -1);
                    // }
                    $journal = trim($journal, '_');
                    $this->setField($item, 'journal', trim($journal, '"*,; '), 'setField 19');
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
                            $monthResult = $this->fixMonth($matches['month'], $language);
                            $monthNumber = '00';
                            for ($j = 1; $j <= 12; $j++) {
                                if ($matches['m' . $j]) {
                                    $monthNumber = strlen($j) == 1 ? '0' . $j : $j;
                                    break;
                                }
                            }
                            $day = strlen($matches['day']) == 1 ? '0' . $matches['day'] : $matches['day'];
                            $this->setField($item, 'month', $monthResult['months'], 'setField 21a');
                            $this->setField($item, 'date', $year . '-' . $monthNumber . '-' . $day, 'setField 21b');
                            $hasFullDate = true;
                            $remainder = substr($remainder, strlen($matches[0]));
                        }

                        // Get pages
                        $result = $this->getVolumeNumberPagesForArticle($remainder, $item, $language);

                        $pagesReported = false;
                        if (! empty($item->pages)) {
                            $pagesReported = true;
                        }
                        $this->verbose("[p1] Remainder: " . $remainder);

                        if ($remainder) {
                            // Get month, if any
                            $months = $this->monthsRegExp[$language];
                            $regExp = '/(\(?(' . $months . '\)?)([-\/](' . $months . ')\)?)?)/iJ';
                            preg_match_all($regExp, $remainder, $matches, PREG_OFFSET_CAPTURE);

                            if (! empty($matches[0][0][0])) {
                                $month = trim($matches[0][0][0], '();');
                                $monthResult = $this->fixMonth($month, $language);
                                $this->setField($item, 'month', $monthResult['months'], 'setField 21');
                                $remainder = substr($remainder, 0, $matches[0][0][1]) . ' ' . ltrim(substr($remainder, $matches[0][0][1] + strlen($matches[0][0][0])), ', )');
                                $this->verbose('Remainder: ' . $remainder);
                            }

                            if (! isset($item->volume) && ! isset($item->number)) {
                                // Get volume and number
                                $numberInParens = false;
                                $this->getVolumeAndNumberForArticle($remainder, $item, $containsNumberDesignation, $numberInParens);
                            }

                            $result = $this->findRemoveAndReturn($remainder, $this->articleRegExp);
                            if ($result) {
                                // If remainder contains article number, put it in the note field
                                $this->addToField($item, 'note', $result[0], 'addToField 7');
                            } elseif (! $item->pages && ! empty($item->number) && !$containsNumberDesignation) {
                                // else if no pages have been found and a number has been set, assume the previously assigned number
                                // is in fact a single page
                                if (empty($numberInParens)) {
                                    $this->setField($item, 'pages', $item->number, 'setField 23');
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

                // Somehow strlen of $item->note can be 1 even though dd says it is "".
                // if (strlen($item->note) <= 1 && ! empty($item->url)) {
                //     $this->verbose('Moving content of url field to note');
                //     $this->addToField($item, 'note', trim($item->url, '{}'), 'addToField 9');
                //     unset($item->url);
                // }

                break;

            //////////////////////////
            // Fix entry for online //
            //////////////////////////

            case 'online':
                if (empty($item->month)) {
                    unset($item->month);
                }

                if (empty($item->urldate) && $itemYear && $itemMonth && $itemDay && $itemDate) {
                    $this->setField($item, 'urldate', rtrim($itemDate, '., '), 'setField 116');
                }

                $result = $this->getDate($remainder, $remains, $month, $day, $date, true, true, false, $language);
                if ($result) {
                    // $remainder is date, so set it to be date of item (even if date was set earlier on basis
                    // of urldate).
                    $this->setField($item, 'year', $result, 'setField 127');
                    $this->setField($item, 'month', $month, 'setField 128');
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
                    $this->setField($item, 'type', $type, 'setField 27');
                }

                $number = $workingPaperMatches[3][0] ?? '';
                if ($number) {
                    $this->setField($item, 'number', $number, 'setField 28');
                }

                // Institution could conceivably be 3-letter acronym, but not shorter?  (Note that first character of
                // string from which $workingPaperMatches was extracted might be '(', so definitely need condition > 1, at least.)
                if (isset($workingPaperMatches[0][1]) && $workingPaperMatches[0][1] > 2) {
                    // Chars before 'Working Paper'
                    $this->setField($item, 'institution', trim(substr($remainder, 0, $workingPaperMatches[0][1] - 1), ' .,'), 'setField 29');
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
                    $this->setField($item, 'institution', $remainder, 'setField 30');
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
                $regExp = '';
                foreach ($this->monthsAbbreviations[$language] as $month) {
                    $regExp .= ($i ? '|' : '') . $month;
                }
                if (isset($year) && preg_match('/^(?P<remainder>.*)\. ' . $year . '$/', $remainderWithMonthYear, $matches)) {
                    if (! preg_match('/ (' . $regExp . ')$/', $matches['remainder'])) {
                        $remainderWithMonthYear = $matches['remainder'];
                    }
                }

                // If $remainder starts with "in", remove it
                // $remainderWithMonthYear is $remainder before month and year (if any) were removed.
                if ($inStart) {
                    $this->verbose("Starts with variant of \"in\"");
                    $remainder = ltrim(substr($remainder, 2), ': ');
                    $remainderWithMonthYear = ltrim(substr($remainderWithMonthYear, 2), ': ');
                }

                if ($itemKind == 'inproceedings') {
                    // Does $remainderWithMonthYear contain a full date or date range?
                    $dateResult = $this->isDate($remainderWithMonthYear, $language, 'contains', true);
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

                    // Get pages in $remainderWithoutDate
                    preg_match('/(\()?' . $this->pagesRegExp . '(\))?/', $remainderWithoutDate, $matches, PREG_OFFSET_CAPTURE);
                    $pagesPos = isset($matches['pages']) ? $matches[0][1] : false;
                    if (isset($matches['pages'])) {
                        $pages = $matches['pages'][0];
                        $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', 'setField 31a');
                        if ($datePos && $pagesPos && $datePos < $pagesPos) {
                            $remainder = str_replace($matches[0][0], '', $remainderWithMonthYear);
                        } else {
                            $remainder = str_replace(trim($matches[0][0] , ' :'), '', $remainder);
                            $remainder = str_replace(', ,', ',', $remainder);
                            $remainder = str_replace(', .', ',', $remainder);
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
                    // Return group 4 of match and remove whole match from $remainder
                    // Give preference to match that has 'pp' or similar before it
                    $result = $this->findRemoveAndReturn($remainder, '(\(| |^)' . $this->pagesRegExpWithPp . '(\))?');
                    if (! $result) {
                        $result = $this->findRemoveAndReturn($remainder, '(\(| |^)' . $this->pagesRegExp . '(\))?');
                    }
                    if (! $result) {
                        // single page number, preceded by 'p.'
                        $result = $this->findRemoveAndReturn($remainder, '(\(| |^)' . $this->pageRegExpWithPp . '(\))?');
                    }
                    if ($result) {
                        $pages = $result[4];
                        $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', 'setField 31b');
                    }
                }

                if (! isset($item->pages)) {
                    $warnings[] = "Pages not found.";
                }

                $remainder = ltrim($remainder, '., ');
                // Next case occurs if remainder previously was like "pages 2-33 in ..."
                if (Str::startsWith($remainder, ['in ', 'In ', 'in: ', 'In: '])) {
                    $remainder = ltrim(substr($remainder, 3));
                }
                $this->verbose("[in2] Remainder: " . $remainder);

                $editorStart = false;
                $newRemainder = $remainder;
                
                // If a string in $remainder is quoted or italicized, take that to be book title
                // (string) on next line to stop VSCode complaining
                $booktitle = (string) $this->getQuotedOrItalic($remainder, false, false, $before, $after, $style);
                if (preg_match('/^(?P<volume>' . $this->volRegExp0 . ')(?P<after>.*)$/', $after, $matches)) {
                    if (isset($matches['volume'])) {
                        $booktitle .= $matches['volume'];
                        $after = $matches['after'] ?? '';
                    }
                }
                $after = ltrim($after, ".,' ");
                $newRemainder = $remainder = $before . $after;
                $booktitle = rtrim($booktitle, ', ');

                if ($booktitle) {
                    $this->setField($item, 'booktitle', $booktitle, 'setField 32');
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
                            $editorConversion = $this->convertToAuthors(explode(' ', $possibleEditors), $remains, $year, $month, $day, $date, $isEditor, false, 'editors', $language);
                            $this->setField($item, 'editor', trim($editorConversion['authorstring']), 'setField 34');
                        } else {
                            $this->verbose('No editors found');
                        }
                        // What is left must be the publisher & address
                        $remainder = $newRemainder = trim(substr($remainder, strlen($before)), '., ');
                    } else {
                        $remainder = ltrim($newRemainder, ', ');
                    }
                }

                $remainder = Str::replaceStart('{\em', '', $remainder);
                $remainder = trim($remainder, '} ');
                $this->verbose('[in3] Remainder: ' . $remainder);
                $updateRemainder = false;

                // address can have one or two words, publisher can have 1-3 word, the last of which can be in parens
                if (preg_match('/^(?P<address>[\p{L}]+( [\p{L}]+)?): ?(?P<publisher>[\p{L}\-]+( [\p{L}\-]+)?( [\p{L}\-()]+)?)\.?$/u', $remainder, $matches)) {
                    if (isset($matches['address'])) {
                        $this->setField($item, 'address', $matches['address'], 'setField 35');
                    }
                    if (isset($matches['publisher'])) {
                        $this->setField($item, 'publisher', $matches['publisher'], 'setField 36');
                    }
                    $remainder = '';
                }

                // The only reason why $item->editor could be set other than by the previous code block is that the 
                // item is a book with an editor rather than an author.  So probably the following condition could
                // be replaced by } else {.
                if ($remainder && ! isset($item->editor)) {
                    $periodPosition = strpos($remainder, '.');
                    // if period is preceded by an ordinal (e.g. 1st., 2nd.) then go to NEXT period
                    if (Str::endsWith(substr($remainder, 0, $periodPosition), $this->ordinals[$language])) {
                        $periodPosition = $periodPosition + strpos(substr($remainder, $periodPosition+1), '.') + 1;
                    }

                    // If type is inproceedings and there is a period that is not preceded by any of the $bookTitleAbbrevs
                    // and $remainder does not contain a string for editors, take booktitle to be $remainder up to period.
                    if (
                        $itemKind == 'inproceedings'
                        && $periodPosition !== false
                        && ! Str::endsWith(substr($remainder, 0, $periodPosition), $this->bookTitleAbbrevs)
                        && ! Str::endsWith(substr($remainder, 0, $periodPosition), $this->monthsAbbreviations[$language])
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
                        $dateNext = $this->isDate($remainderAfterCityString, $language, 'starts', true);
                        if ($cityString && ! $dateNext) {
                            // If there is no publisher string and the type is inproceedings and there is only one word left and
                            // it is not the year, assume it is part of booktitle
                            // Case in which it's the year: something like '... June 3-7, New York, NY, 2010'.
                            if (! $publisherString && $itemKind == 'inproceedings' && strpos($remainderAfterCityString, ' ') === false) {
                                if ($this->isYear(($remainderAfterCityString))) {
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
                        if ($publisherString) {
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
                                if (!$booktitle) {
                                    $booktitle = $tempRemainder;
                                    $this->verbose('booktitle case 2');
                                }
                                $this->setField($item, 'editor', '', 'setField 37');
                                $warnings[] = 'No editor found';
                                $this->setField($item, 'address', $cityString, 'setField 38');
                                $this->setField($item, 'publisher', $publisherString, 'setField 39');
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
                                    if ($bareWordCount < 4 && $this->isNameString($tempRemainderLeft)) {
                                        $possibleEds = $tempRemainderLeft;
                                    }
                                }
                                if (!$possibleEds) {
                                    $this->verbose("No string that could be editors' names identified in tempRemainder");

                                    if ($cityString || $publisherString) {
                                        if (! $booktitle) {
                                            $booktitle = $itemKind == 'inproceedings' ? $remainder : $tempRemainder;
                                            $this->verbose("Booktitle case 3");
                                        }
                                        $this->setField($item, 'editor', '', 'setField 40');
                                        $warnings[] = 'No editor found';
                                        if (! str_contains($booktitle, $cityString)) {
                                            $this->setField($item, 'address', $cityString, 'setField 41');
                                        }
                                        $this->setField($item, 'publisher', $publisherString, 'setField 42');
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
                    // title or editors.
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

                    if (! $lastTwoWordsHaveDigits && preg_match('/(.*)' . $this->editorEndRegExp . '/i', $tempRemainder, $matches)) {
                        $this->verbose('Remainder minus pub info ends with \'eds\' or similar, so format is <booktitle> <editor>');
                        // Remove "eds" at end
                        $tempRemainderMinusEds = Str::beforeLast($tempRemainder, ' ');
                        // If remaining string contains '(', take preceding string to be booktitle and following string to be editors.
                        // Remaining string might not contain '(': might be error, or "eds" could have been "(eds").
                        if ($tempRemainderEndsWithParen && Str::contains($tempRemainderMinusEds, '(')) {
                            $this->verbose('tempRemainder contains \'(\' and ends with \')\'');
                            $booktitle = Str::beforeLast($tempRemainderMinusEds, '(');
                            $editorString = Str::afterLast($tempRemainderMinusEds, '(');
                            $result = $this->convertToAuthors(explode(' ', $editorString), $remainder, $trash, $month, $day, $date, $isEditor, true, 'editors', $language);
                        } else {
                            $trash2 = false;
                            // Include edition in title (because no BibTeX field for edition for incollection)
                            $booktitle = $this->getTitlePrecedingAuthor($tempRemainder, $language);
                            if (substr($booktitle, -3) != 'ed.') {
                                $booktitle = rtrim($booktitle, '.');
                            }
                            $this->setField($item, 'booktitle', $booktitle);
                            if (! empty($note)) {
                                $this->addToField($item, 'note', $note, 'addToField 11');
                            }
                            $isEditor = true;
                            $result = $this->convertToAuthors(explode(' ', $tempRemainder), $remainder, $trash, $month, $day, $date, $isEditor, true, 'editors', $language);
                        }
                        $this->setField($item, 'editor', trim($result['authorstring']), 'setField 119');
                        $remainderContainsEds = true;
                        $updateRemainder = false;
                    } elseif ($containsEditors && preg_match('/' . $this->editorRegExp . '/i', $remainder, $matches)) {
                        // If $remainder contains string "(Eds.)" or similar then check
                        // whether it starts with names.
                        // $containsEditors to make sure we are picking up editors, not an edition
                        $eds = $matches[0];
                        $beforeEds = Str::before($remainder, $eds);
                        $wordsBeforeEds = explode(' ', $before);
                        $afterEds = Str::after($remainder, $eds);
                        $publisherPosition = strpos($after, $publisher);
                        $remainderContainsEds = true;
                    } elseif (preg_match('/^(?P<booktitle>.*?)(edited by)(?P<rest>.*?)$/i', $remainder, $matches)) {
                        // If it doesn't, take start of $remainder up to first comma or period to be title,
                        // followed by editors, up to (Eds.).
                        $this->verbose('Remainder contains \'edited by\'.  Taking it to be <booktitle> edited by <editor> <publicationInfo>');
                        $booktitle = trim($matches['booktitle'], ', ');
                        // Authors and publication info
                        $rest = trim($matches['rest']);
                        if (preg_match('/^(?P<before>.*)' . $this->editionRegExp . '(?P<after>.*)$/', $rest, $matches)) {
                            $this->addToField($item, 'note', $matches['fullEdition']);
                            $rest = $matches['before'] . $matches['after'];
                        }
                        $isEditor = true;
                        $result = $this->convertToAuthors(explode(' ', $rest), $remainder, $trash, $month, $day, $date, $isEditor, true, 'editors', $language);
                        $this->setField($item, 'editor', trim($result['authorstring'], ', '), 'setField 120');
                        $updateRemainder = false;
                    }

                    if (! isset($item->editor)) {
                        if ($remainderContainsEds) {
                            $wordsBeforeAllNames = true;
                            foreach ($wordsBeforeEds as $word) {
                                if ($this->inDict(trim($word, ' .,'))) {
                                    $wordsBeforeAllNames = false;
                                    break;
                                }
                            }
                        }

                        // Require string for editors to have at least 6 characters and string for booktitle to have at least 10 characters
                        if ($remainderContainsEds && 
                                ($wordsBeforeAllNames || (strlen($beforeEds) > 5 && $publisherPosition !== false && $publisherPosition > 10))
                            ) {
                            // $remainder is <editors> eds <booktitle> <publicationInfo>
                            $this->verbose("Remainder seems to be <editors> eds <booktitle> <publicationInfo>");
                            $editorStart = true;
                            $editorString = $beforeEds;
                            $determineEnd = false;
                            $postEditorString = $after;
                        } elseif (preg_match($this->edsRegExp2, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                            $this->verbose("Remainder format is <booktitle> ed(.|ited) by <editors> <publicationInfo>");
                            $booktitle = trim(substr($remainder, 0, $matches[0][1]), ' ,');
                            $this->setField($item, 'booktitle', $booktitle, 'setField 111');
                            $editorStart = true;
                            $remainder = trim(substr($remainder, $matches[0][1] + strlen($matches[0][0])));
                        } elseif (preg_match($this->edsRegExp1, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                            // $remainder contains "(Eds.)" (parens required) or similar and  starts with namestring OR
                            // contains "(Eds.)," [note comma] --- in which case editors precede "eds".
                            // if ($this->isNameString($remainder) || preg_match('/\([Ee]ds?\.?\),/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                            $result = preg_match('/^(?P<booktitle>[\p{L}\-: ]{15,}), (?P<editor>[\p{L}\-. ]{6,})\([Ee]ds?\.?\),? (?P<pubInfo>.*)$/u', $remainder, $matches2);
                            if ($result) {
                                // CASE 1
                                $this->verbose("Remainder format is <booktitle> <editors> (Eds.) <publicationInfo>");
                                $this->setField($item, 'booktitle', $matches2['booktitle'], 'setField 130');
                                $isEditor = true;
                                $conversionResult = $this->convertToAuthors(explode(' ', $matches2['editor']), $remainder, $year, $month, $day, $date, $isEditor, determineEnd: false, type: 'editors', language: $language);
                                $this->setField($item, 'editor', trim($conversionResult['authorstring'], ', '), 'setField 131');
                                $remainder = trim($matches2['pubInfo'], ',. ');
                            } elseif ($this->isNameString($remainder)) {
                                // CASE 2
                                // $remainder starts with names, and so
                                // $remainder is <editors> (Eds.) <booktitle> <publicationInfo>
                                $this->verbose("Remainder format is <editors> (Eds.) <booktitle> <publicationInfo>");
                                $editorStart = true;
                                $editorString = substr($remainder, 0, $matches[0][1]);
                                $determineEnd = false;
                                $postEditorString = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                                $this->verbose("editorString: " . $editorString);
                                $this->verbose("postEditorString: " . $postEditorString);
                                $this->verbose("[in4] Remainder: " . $remainder);
                            } else {
                                // CASE 3
                                // $remainder does not start with names, and so
                                // $remainder is <booktitle>[,.] <editors> (Eds.) <publicationInfo>
                                $this->verbose("Remainder contains \"(Eds.)\" or similar but starts with string that does not look like a name");
                                $editorStart = false;
                                $endAuthorPos = $matches[0][1];
                                $edStrLen = strlen($matches[0][0]);
                            }
                        } elseif (preg_match($this->editorStartRegExp, $remainder)) {
                            // CASE 3
                            // $remainder does not contain "(Eds.)" but starts with "Eds" or similar, and so
                            // $remainder is Eds. <editors> <booktitle> <publicationInfo>
                            $this->verbose("Remainder does not contain string like \"(Eds.)\" but starts with \"Eds\" or similar");
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
                        } else {
                            // $remainder does not contain "(Eds.)" or start with "Eds"
                            // Next case: example 370
                            if (preg_match('/^(?P<booktitle>.*), (edited )?by (?P<edsAndPubInfo>.*)$/i', $remainder, $matches)) {
                                $booktitle = $matches['booktitle'];
                                $remainder = $matches['edsAndPubInfo'];
                                $remainingWords = explode(' ', $remainder);
                                $editorConversion = $this->convertToAuthors($remainingWords, $remainder, $trash2, $trash3, $trash4, $trash5, $isEditor, true, 'editors', $language);
                                $this->setField($item, 'booktitle', $booktitle, 'setField 121');
                                $editorString = trim($editorConversion['authorstring'], ', ');
                                $this->setField($item, 'editor', $editorString, 'setField 122');
                                $editorStart = false;
                                $updateRemainder = false;
                                $this->verbose('Remainder: ' . $remainder);
                            } elseif (
                                    $this->isNameString($remainder)
                                    && preg_match('/^(?P<editor>.*?) ed(itor)?s?\.?,? (?P<remains>.*)$/', $remainder, $matches)
                                ) {
                                // CASE 4.
                                // (Exclusion of [IXVLC] match is to exclude title starting with Roman number ("XI Annual Meeting..."))
                                // $remainder is <editors> <booktitle> <publicationInfo>
                                $this->verbose("Remainder does not contain \"(Eds.)\" or similar string in parentheses and does not start with \"Eds\" or similar, but starts with a string that looks like a name");
                                $editorStart = true;
                                $editorString = $matches['editor'];
                                $postEditorString = $matches['remains'];
                                $determineEnd = true;
                                $this->verbose("editorString: " . $editorString);
                                $this->verbose("[in6a] Remainder: " . $remainder);
                            } elseif ($itemKind == 'incollection' && $this->isNameString($remainder)) {
                                $editorStart = true;
                                $editorString = $remainder;
                                $determineEnd = true;
                                $this->verbose("editorString: " . $editorString);
                                $this->verbose("[in6b] Remainder: " . $remainder);
                            } else {
                                // CASE 5
                                // $remainder is <booktitle> <editors> <publicationInfo>
                                $this->verbose("Remainder does not contain \"(Eds.)\" or similar and does not start with \"Eds\" or similar, and does not start with a string that looks like a name");
                                $editorStart = false;
                                $edStrLen = 0;
                                $endAuthorPos = 0;
                            }
                        }

                        if (! isset($item->booktitle) || ! isset($item->editor)) {
                            // An inproceedings item can start with something like "XI Annual ...", which looks like a name string,
                            // but inproceedings items aren't likely to have editors --- they should be identified more strongly
                            // (e.g with "eds" or "edited by").
                            if ($editorStart || ($itemKind == 'incollection' && $this->initialNameString($remainder))) {
                                // CASES 1, 3, and 4
                                $this->verbose("[ed1] Remainder starts with editor string");
                                $words = explode(' ', $editorString ?? $remainder);
                                // $isEditor is used only for a book (with an editor, not an author)
                                $isEditor = false;

                                $editorConversion = $this->convertToAuthors($words, $remainder, $trash2, $month, $day, $date, $isEditor, $determineEnd ?? true, 'editors', $language);
                                $editorString = trim($editorConversion['authorstring'], '() ');
                                foreach ($editorConversion['warnings'] as $warning) {
                                    $warnings[] = $warning;
                                }

                                // Do not rtrim a period (might follow an initial).
                                $this->setField($item, 'editor', trim($editorString, ' ,'), 'setField 43');
                                $newRemainder = $postEditorString ? $postEditorString : $remainder;
                                // $newRemainder consists of <booktitle> <publicationInfo>
                                $newRemainder = trim($newRemainder, '., ');
                                $this->verbose("[in7] Remainder: " . $newRemainder);
                            } else {
                                // CASES 2 and 5
                                $this->verbose("Remainder: " . $remainder);
                                $this->verbose("[ed2] Remainder starts with book title");

                                // If the editors have been identified, set booktitle to be the string before "eds"
                                // and the remainder to be the string after "eds".
                                if (isset($beforeEds) && isset($afterEds) && isset($item->editor)) {
                                    $booktitle = $beforeEds;
                                    $remainder = $afterEds;
                                } elseif ($itemKind == 'inproceedings') {
                                    $booktitle = $remainder;
                                    $newRemainder = '';
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
                                                        && $this->isNameString(substr($remainder, $j+1))
                                                    )
                                                )
                                            ) {
                                            $booktitle = trim(substr($remainder, 0, $j+1), ', ');
                                            $this->verbose('booktitle case 4');
                                            $newRemainder = rtrim(substr($remainder, $j + 1), ',. ');
                                        }
                                    }
                                    $this->verbose("booktitle: " . $booktitle);
                                    if (! empty($endAuthorPos)) {
                                        // CASE 2
                                        $authorstring = trim(substr($remainder, $j, $endAuthorPos - $j), '.,: ');
                                        $editorConversion = $this->convertToAuthors(explode(' ', $authorstring), $trash1, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
                                        $this->setField($item, 'editor', trim($editorConversion['authorstring'], ' '), 'setField 44');
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
                $this->verbose("[in9] Remainder: " . $remainder);

                // If only $cityString remains, no publisher has been identified, so assume $cityString is part
                // of proceedings booktitle
                if ($remainder == $cityString) {
                    $booktitle .= ', ' . $cityString;
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

                            $editorConversion = $this->convertToAuthors($editors, $remainder, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
                            $editor = trim($editorConversion['authorstring']);
                            // If editor ends in period and previous letter is lowercase, remove period
                            if (substr($editor, -1) == '.' && strtolower(substr($editor, -2, 1)) == substr($editor, -2, 1)) {
                                $editor = rtrim($editor, '.');
                            }
                            foreach ($editorConversion['warnings'] as $warning) {
                                $warnings[] = $warning;
                            }
                            $this->setField($item, 'editor', $editor, 'setField 118');

                            /*
                            // At least last word must be city or part of city name, so remove it
                            $spacePos = strrpos($remainderBeforeColon, ' ');
                            $possibleEditors = trim(substr($remainderBeforeColon, 0, $spacePos));
                            //$editorConversion = $this->convertToAuthors(explode(' ', $possibleEditors), $trash1, $trash2, $isEditor, true);

                            // Find previous period
                            for ($j = $colonPos; $j > 0 && $remainder[$j] != '.' && $remainder[$j] != '('; $j--) {

                            }
                            $this->verbose("Position of period in remainder is " . $j);
                            // Previous version---why drop first 3 chars?
                            // $editor = trim(substr($remainder, 3, $j-3), ' .,');

                            $editorConversion = $this->convertToAuthors(explode(' ', trim(substr($remainder, 0, $j), ' .,')), $remainder, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
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
                                $editorConversion = $this->convertToAuthors(explode(' ', trim($remainder)), $remainder, $year, $month, $day, $date, $isEditor, true, 'editors', $language);

                                $editor = $editorConversion['authorstring'];
                                $this->verbose("Editor is: " . $editor);                                
                                $newRemainder = substr($remainder, $publisherPos);
                            } else {
                                $editorConversion = $this->convertToAuthors(explode(' ', trim($remainder)), $remainder, $year, $month, $day, $date, $isEditor, true, 'editors', $language);

                                $editor = $editorConversion['authorstring'];
                                $this->verbose("Editor is: " . $editor);
                                foreach ($editorConversion['warnings'] as $warning) {
                                    $warnings[] = $warning;
                                }

                                $newRemainder = $remainder;
                            }
                            $this->setField($item, 'editor', trim($editor), 'setField 115');
                        }
                    } elseif (preg_match($this->edsRegExp1, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        // $remainder contains "(Eds.)" or something similar, so takes form <editor> (Eds.) <publicationInfo>
                        $this->verbose("[ed6] Remainder starts with editor string");
                        $editorString = substr($remainder, 0, $matches[0][1]);
                        $this->verbose("editorString is " . $editorString);
                        $editorConversion = $this->convertToAuthors(explode(' ', $editorString), $trash1, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }
                        $this->setField($item, 'editor', trim($editor, ', '), 'setField 45');
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                    } elseif ($itemKind == 'incollection' && $this->initialNameString($remainder)) {
                        // An editor of an inproceedings has to be indicated by an "eds" string (inproceedings
                        // seem unlikely to have editors), but an 
                        // editor of an incollection does not need such a string
                        $this->verbose("[ed4] Remainder starts with editor string");
                        $editorConversion = $this->convertToAuthors(explode(' ', $remainder), $remainder, $trash2, $month, $day, $date, $isEditor, true, 'editors', $language);
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $this->setField($item, 'editor', trim($editor, ', '), 'setField 46');
                        $newRemainder = $remainder;
                    } else {
                        // Else editors are part of $remainder up to " ed." or "(ed.)" etc.
                        $this->verbose("[ed5] Remainder starts with editor string");
                        $numberOfMatches = preg_match($this->edsRegExp4, $remainder, $matches, PREG_OFFSET_CAPTURE);
                        $take = $numberOfMatches ? $matches[0][1] : 0;
                        $match = $numberOfMatches ? $matches[0][0] : '';
                        $editor = rtrim(substr($remainder, 0, $take), '., ');
                        $newRemainder = substr($remainder, $take + strlen($match));
                    }

                    if (! isset($item->editor)) {
                        $words = explode(' ', ltrim($editor, ','));
                        // Let convertToAuthors figure out where editors end, in case some extra text appears after editors,
                        // before publication info.  Not sure this is a good idea: if convertToAuthors works very well, could
                        // be good, but if it doesn't, might be better to take whole string.  If revert to not letting
                        // convertToAuthors determine end of string, need to redefine remainder below.
                        $isEditor = false;

                        $editorConversion = $this->convertToAuthors($words, $remainder, $trash2, $month, $day, $date, $isEditor, true, 'editors', $language);
                        $authorstring = $editorConversion['authorstring'];
                        $this->setField($item, 'editor', trim($authorstring, '() '), 'setField 47');
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
                    $this->verbose("[in11] Remainder: " . $remainder);
                    // $remainder contains book title and publication info.  Need to find boundary.  

                    // Check whether publication info matches pattern for book to be a volume in a series
                    $result = $this->findRemoveAndReturn(
                        $remainder,
                        '(' . $this->volumeRegExp . ')( ([1-9][0-9]{0,4}))(( of| in|,) )([^\.,]*\.|,)'
                        );
                    if ($result) {
                        // Take series to be string following 'of' or 'in' or ',' up to next period or comma
                        $this->setField($item, 'volume', $result[3], 'setField 48');
                        $series = trim($result[6], '., ');
                        if ($series) {
                            $this->setField($item, 'series', $result[6], 'setField 49');
                        }
                        $booktitle = trim($result['before'], '., ');
                        $this->verbose('booktitle case 5');
                        $remainder = trim($result['after'], ',. ');
                        $this->verbose('Volume found, so book is part of a series');
                        $this->verbose('Remainder (publisher and address): ' . $remainder);
                    } elseif (! empty($cityString) && ! empty($publisher)) {
                        $remainder = Str::remove([$publisher, $cityString], $remainder);
                        $remainder = rtrim($remainder, ' :)(');
                        $booktitle = $remainder;
                        $remainder = '';
                    } elseif (
                        preg_match('/(?P<booktitle>[^\(]{5,100})\((?P<address>[^:]{4,20}):(?P<publisher>[^\.]{4,40})\)/i', $remainder, $matches)
                        ||
                        preg_match('/(?P<booktitle>[^\.]{5,100})\.(?P<address>[^:]{4,20}):(?P<publisher>[^\.]{4,40})[,.]/i', $remainder, $matches)
                        ) {
                        // common pattern: <booktitle> (<address>: <publisher>).
                        $booktitle = $matches['booktitle'];
                        $address = $matches['address'];
                        $this->setField($item, 'address', trim($address), 'setField 49a');
                        $publisher = trim($matches['publisher']);
                        $this->setField($item, 'publisher', $publisher, 'setField 49b');
                        $remainder = '';
                    } else {
                        // if remainder contains a single period, take that as end of booktitle
                        if (substr_count($remainder, '.') == 1) {
                            $this->verbose("Remainder contains single period, so take that as end of booktitle");
                            $periodPos = strpos($remainder, '.');
                            $booktitle = trim(substr($remainder, 0, $periodPos), ' .,');
                            // If title starts with In <uc letter>, take off the "In".
                            if (preg_match('/^[Ii]n [A-Z]/', $booktitle)) {
                                $booktitle = substr($booktitle, 3);
                            }
                            $this->verbose('booktitle case 6');
                            $remainder = substr($remainder, $periodPos);
                        } else {
                            // If publisher has been identified, remove it from $remainder and check
                            // whether it is preceded by a string that could be an address
                            if (! empty($publisher)) {
                                $this->setField($item, 'publisher', $publisher, 'setField 50');
                                $tempRemainder = trim(Str::remove($publisher, $remainder), ' .');
                                $afterPeriod = Str::afterLast($tempRemainder, '.');
                                $afterComma = Str::afterLast($tempRemainder, ',');
                                $afterPunc = (strlen($afterComma) < strlen($afterPeriod)) ? $afterComma : $afterPeriod;
                                foreach ($this->cities as $city) {
                                    if (Str::endsWith(trim($afterPunc, '():'), $city)) {
                                        $this->setField($item, 'address', $city, 'setField 51');
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
                                        $this->setField($item, 'address', $afterPunc, 'setField 52');
                                        $this->verbose('booktitle case 8');
                                    } else {
                                        $booktitle = $tempRemainder;
                                        $this->verbose('booktitle case 9');
                                    }
                                    $this->setField($item, 'booktitle', trim($booktitle, ' ,;'), 'setField 53');
                                    $remainder = '';
                                }
                            // $remainder ends with pattern like 'city: publisher'
                            } elseif (! empty($cityString) && preg_match('/( ' . $cityString . ': (?P<publisher>[^:.,]*)\.?$)/', $remainder, $matches)) {
                                $booktitle = Str::before($remainder, $matches[0]);
                                $this->setField($item, 'booktitle', trim($booktitle, ', '), 'setField 123');
                                // Eliminate space between letters in US state abbreviation containing periods.
                                $cityString = preg_replace('/ ([A-Z]\.) ([A-Z]\.)/', ' $1' . '$2', $cityString);
                                $this->setField($item, 'address', $cityString, 'setField 124');
                                $this->setField($item, 'publisher', trim($matches['publisher'], ' .'), 'setField 125');
                                $this->verbose('booktitle case 14a');
                                $remainder = '';
                            } elseif (preg_match('/^(?P<booktitle>[^.]+)\. \. (?P<address>.*): (?P<publisher>[^:.,]*)$/', $remainder, $matches)) {
                                $this->setField($item, 'booktitle', trim($matches['booktitle'], ',. '), 'setField 131');
                                $this->setField($item, 'address', trim($matches['address'], ',. '), 'setField 132');
                                $this->setField($item, 'publisher', trim($matches['publisher'], ',. '), 'setField 133');
                                $this->verbose('booktitle case 14b');
                                $remainder = '';
                            } elseif (preg_match('/( ([^ ]*): ([^:.,]*)$)/', $remainder, $matches)) {
                                $booktitle = Str::before($remainder, $matches[0]);
                                $this->setField($item, 'booktitle', trim($booktitle, ',. '), 'setField 126');
                                $this->verbose('booktitle case 14c');
                                $remainder = $matches[0];
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
                                    for ($j = $n - 2; $j > 0 && $this->isInitials($words[$j]); $j--) {

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
                    $this->setField($item, 'booktitle', $remainder, 'setField 54a');
                } elseif (empty($item->publisher) || empty($item->address)) {
                    if (! empty($item->publisher)) {
                        $this->setField($item, 'address', $remainder, 'setField 54');
                        $newRemainder = '';
                    } elseif (! empty($item->address)) {
                        $this->setField($item, 'publisher', $remainder, 'setField 55');
                        $newRemainder = '';
                    } else {
                        if (str_contains($booktitle, trim($cityString, '. '))) {
                            $cityString = '';
                        }
                        if (preg_match('/^(?P<remains>.*?)' . $this->pagesRegExp . '$/', $remainder, $matches)) {
                            $this->setField($item, 'pages', $matches['pages'], 'setField 56a');
                            $remainder = trim($matches['remains'], ';., ');
                        }
                        $newRemainder = $this->extractPublisherAndAddress($remainder, $address, $publisher, $cityString, $publisherString);
                        $this->setField($item, 'publisher', $publisher, 'setField 56b');
                        $this->setField($item, 'address', $address, 'setField 56c');
                    }
                }

                if (! empty($item->publisher)) {
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
                if ($this->inDict($lastWordInBooktitle)) {
                    $booktitle = rtrim($booktitle, '.');
                }

                if (! isset($item->booktitle)) {
                    if ($booktitle) {
                        $this->setField($item, 'booktitle', trim($booktitle, ' .,('), 'setField 58');
                    } else {
                        // Change item type to book
                        $itemKind = 'book';
                        $this->verbose(['fieldName' => 'Item type', 'content' => 'changed to ' . $itemKind]);
                        $this->verbose('Both author and editor set, so editor moved to note field.');
                        if (! empty($item->author) && ! empty($item->editor)) {
                            $this->addToField($item, 'note', 'Edited by ' . $item->editor . '.');
                            unset($item->editor);
                        }
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
                            $this->setField($item, 'pages', trim($pages, ' ,'));
                            $this->setField($item, 'numPages', trim($pages, ' ,'));
                        }
                    }

                    $remainder = '';
                }

                // If remainder starts with pages, put them in Note and remove them from remainder
                if (preg_match('/^\(?(?P<note>pp?\.? [1-9][0-9]{0,3}(-[1-9][0-9]{0,3})?)/', $remainder, $matches)) {
                    $this->setField($item, 'note', $matches['note'], 'setField 59a');
                    $remainder = trim(substr($remainder, strlen($matches[0])), '() ');
                }

                // If remainder ends with phrase like "first published" or "originally published", remove it and put it
                // in the note field
                if (preg_match('/(?P<note>\(?([Ff]irst|[Oo]riginally) published (in )?[0-9]{4}\.?\)?\.?)$/', $remainder, $matches)) {
                    $this->addToField($item, 'note', ' ' . trim($matches['note'], '() '), 'setField 59b');
                    $remainder = trim(substr($remainder, 0, -strlen($matches[0])), '() ');
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
                            $this->setField($item, 'edition', trim($remainingWords[$key - 2] . ' '. $remainingWords[$key - 1], ',. )('), 'setField 59a');
                            array_splice($remainingWords, $key - 2, 3);
                        } else {
                            $this->setField($item, 'edition', trim($remainingWords[$key - 1], ',. )('), 'setField 59c');
                            array_splice($remainingWords, $key - 1, 2);
                        }
                        break;
                    }
                }

                // If remainder contains word 'volume', take next word to be volume number.  If
                // following word is "in" or "of" or a comma or period, following string is taken as series name
                $this->verbose('Looking for volume');
                $done = false;
                $newRemainder = null;
                $remainder = implode(" ", $remainingWords);
                $result = $this->findRemoveAndReturn(
                    $remainder,
                    '((\(?' . $this->volumeRegExp . ')( ([1-9][0-9]{0,4})\)?))(( of| in|,|.)? )(.*)$'
                );
                if ($result) {
                    if (in_array($result[6], ['.', ',']) && substr_count(trim($result['before']), ' ') <= 1) {
                        // Volume is volume of book, not part of series
                        // Publisher and possibly address
                        $this->setField($item, 'volume', $result[4], 'setField 60');
                        $newRemainder = $result[7];
                    } elseif (substr_count(trim($result['before']), ' ') > 1) {
                        // Words before volume designation are series name.  Book has no field for volume of
                        // series, so add volume designation to series field.
                        $this->setField($item, 'series', trim($result['before']) . ' ' . $result[1]);
                        $newRemainder = $remainder = $result[7];
                    } else {
                        // Volume is part of series
                        $this->verbose('Volume is part of series: assume format is <series>? <publisherAndAddress>');
                        $this->setField($item, 'volume', $result[4], 'setField 109');
                        $this->verbose(['fieldName' => 'Volume', 'content' => $item->volume]);
                        $seriesAndPublisher = $result[7];
                        // Case in which  publisher has been identified
                        if ($publisher) {
                            $this->setField($item, 'publisher', $publisher, 'setField 61');
                            $after = Str::after($seriesAndPublisher, $publisher);
                            $before = Str::before($seriesAndPublisher, $publisher);
                            if ($after) {
                                // If anything comes after the publisher, it must be the address, and the string
                                // before the publisher must be the series
                                $this->setField($item, 'address', trim($after, ',. '), 'setField 62');
                                $series = trim($before, '., ');
                                if ($this->containsFontStyle($series, true, 'italics', $startPos, $length)) {
                                    $this->setField($item, 'series', rtrim(substr($series, $length), '}'), 'setField 63');
                                    $this->verbose('Removed italic formatting from series name');
                                } else {
                                    $this->setField($item, 'series', $series, 'setField 64');
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
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), 'setField 65');
                                            $this->setField($item, 'series', trim($beforeLastComma, '.,: '), 'setField 66');
                                        } else {
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), 'setField 67');
                                            $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), 'setField 68');
                                        }
                                    } elseif ($containsComma) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), 'setField 69');
                                        $this->setField($item, 'series', trim($beforeLastComma, '.,: '), 'setField 70');
                                    } elseif ($containsPeriod) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), 'setField 71');
                                        $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), 'setField 72');
                                    } else {
                                        $beforeLastSpace = Str::beforeLast($before, ' ');
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastSpace)), '.,: '), 'setField 73');
                                        $this->setField($item, 'series', trim($beforeLastSpace, '.,: '), 'setField 74');
                                    }
                                } else {
                                    // Otherwise there is no address, and the series is the string before the publisher
                                    $this->setField($item, 'series', trim($before, '.,: '), 'setField 75');
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
                                $this->setField($item, 'series', $result1[1], 'setField 76');
                                if ($result1[4] == ',') {
                                    $this->verbose('Series case 1a: format is <publisher>, <address>');
                                    $this->setField($item, 'publisher', trim($result1[2], ' ,'), 'setField 77');
                                    $this->setField($item, 'address', $result1[5], 'setField 78');
                                } elseif ($result1[4] == ':') {
                                    $this->verbose('Series case 1b: format is <address>: <publisher>');
                                    $this->setField($item, 'address', trim($result1[2], ' :'), 'setField 79');
                                    $this->setField($item, 'publisher', $result1[5], 'setField 80');
                                }
                            } else {
                                $result2 = $this->findRemoveAndReturn(
                                    $seriesAndPublisher,
                                    '(.*[^.,]*)\. (.*\.?)$'
                                );
                                if ($result2) {
                                    $this->verbose('Series case 2a: format is <publisher> (no address)');
                                    $this->setField($item, 'series', $result2[1], 'setField 81');
                                    $this->setField($item, 'publisher', $result2[2], 'setField 82');
                                } else {
                                    $this->verbose('Series case 2b: format is <publisher>');
                                    $this->setField($item, 'publisher', trim($seriesAndPublisher), 'setField 82a');
                                }
                            }
                        }
                        $done = true;
                    }
                }

                // Volume has been identified, but publisher and possibly address remain
                if (! $done) {
                    $remainder = $newRemainder ?? implode(" ", $remainingWords);
                    $remainder = trim($remainder, ' .');
                    if (preg_match('/(?P<editorString>Edited by .*?[a-z] ?\.)/', $remainder, $matches)) {
                        $this->addToField($item, 'note', str_replace(' .', '.', $matches['editorString']), 'setField 110a');
                        $remainder = str_replace($matches['editorString'], '', $remainder);
                    }

                    // If string is in italics, get rid of the italics
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                        $remainder = rtrim(substr($remainder, $length), '}');
                    }

                    $remainderMinusPubInfo = Str::remove($cityString, $remainder);
                    $remainderMinusPubInfo = Str::remove($publisherString, $remainderMinusPubInfo);
                    // If remainder contains a period following a lowercase letter, string before period is series name
                    $periodPos = strpos($remainderMinusPubInfo, '.');
                    if ($periodPos !== false && strtolower($remainderMinusPubInfo[$periodPos-1]) == $remainderMinusPubInfo[$periodPos-1]) {
                        $beforePeriod = trim(Str::before($remainderMinusPubInfo, '.'));
                        if (Str::contains($beforePeriod, ['series', 'Series'])) {
                            $this->setField($item, 'series', $beforePeriod, 'setField 110');
                            $remainder = trim(Str::remove($beforePeriod, $remainder));
                        }
                        //$this->setField($item, 'title', $item->title . $series, 'setField 110');
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
                                $this->setField($item, 'title', trim($title, ' ,'), 'setField 113');
                                $remainder = $cityString . ':' . $publisherString;
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

                    $remainder = $this->extractPublisherAndAddress($remainder, $address, $publisher, $cityString, $publisherString);

                    if ($publisher) {
                        $this->setField($item, 'publisher', trim($publisher, '(); '), 'setField 85');
                    }

                    if ($address) {
                        if (isset($item->year)) {
                            $address = Str::replace($item->year, '', $address);
                            $address = rtrim($address, ', ');
                        }
                        $this->setField($item, 'address', $address, 'setField 86');
                    }

                    // Then fall back on publisher and city previously identified.
                    if (!$publisher && $publisherString && !$address && $cityString) {
                        $this->setField($item, 'publisher', $publisherString, 'setField 83');
                        $this->setField($item, 'address', $cityString, 'setField 84');
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

                if (preg_match('/^\(' . $this->fullThesisRegExp . '(?P<school>[^\)]*)\)(?P<remainder>.*)/', $remainder, $matches)) {
                    $this->setField($item, 'school', trim($matches['school'], ', '), 'setField 87a');
                    $remainder = $matches['remainder'];
                    if (empty($item->school)) {
                        $this->setField($item, 'school', trim($remainder, ',.() '), 'setField 87b');
                    } else {
                        $this->addToField($item, 'note', trim($remainder, ',. '), 'setField 87c');
                    }
                } else {
                    if (preg_match('/\(' . $this->fullThesisRegExp . '\)/', $remainder)) {
                        $remainder = $this->findAndRemove($remainder, ',? ?\(' . $this->fullThesisRegExp . '\)');
                    } else {
                        $remainder = preg_replace('/^([Uu]npublished|[Yy]ayınlanmamış) /', '', $remainder);
                        $remainder = $this->findAndRemove($remainder, $this->fullThesisRegExp);
                    }
                    $remainder = trim($remainder, ' -.,)[]');
                    // if remainder contains number of pages, put them in note
                    $result = $this->findRemoveAndReturn($remainder, '(\()?' . $this->pageRegExpWithPp . '(\))?');
                    if ($result) {
                        $this->setField($item, 'note', $result[0], 'setField 87d');
                        $remainder = trim($remainder, '., ');
                    }

                    if (strpos($remainder, ':') === false) {
                        $this->setField($item, 'school', $remainder, 'setField 87e');
                    } else {
                        $remArray = explode(':', $remainder);
                        $this->setField($item, 'school', trim($remArray[1], ' .,'), 'setField 87f');
                        $this->addToField($item, 'note', $remArray[0], 'setField 87g');
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
            $year = $this->getDate($remainder, $remainds, $month, $day, $date, true, true);

            if (is_numeric($year) && $month && $day) {
                $this->setField($item, 'date', $year . '-' . $month . '-' . $day, 'setField 129');
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
                    && $style != 'quoted'
                    && (! isset($item->author) || $item->author != $remainder)
                   ) {
                    // If remainder is all letters and spaces, assume it is part of title,
                    // which must have been ended prematurely.
                    $this->addToField($item, 'title', $remainder, 'addToField 16');
                } else {
                    $this->addToField($item, 'note', $remainder, 'addToField 15');
                }
            } elseif (preg_match('/^Paper no\. [0-9]+\.?$/i', $remainder)) {
                $this->addToField($item, 'note', $remainder, 'addToField 17');
            } else {
                $warnings[] = "[u4] The string \"" . $remainder . "\" remains unidentified.";
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
//                if ($name != 'year') {
                    $item->$name = $this->translate($field, 'my');
//                }
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
            'author-pattern' => $authorConversion['author-pattern'] ?? null,
        ];

        return $returner;
    }

    /*
     * If month (or month range) is parsable, parse it: 
     * translate 'Jan' or 'Jan.' or 'January' or 'JANUARY', for example, to 'January'.
    */
    private function fixMonth(string $month, string $language = 'en'): array
    {
        if (is_numeric($month)) {
            return ['months' => $month, 'month1number' => $month, 'month2number' => null];
        }

        Carbon::setLocale($language);

        $month1number = $month2number = null;

        $month1 = trim(Str::before($month, '-'), ', ');
        if (preg_match('/^[a-zA-Z.]*$/', $month1)) {
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

        $months = $fullMonth1 . ($fullMonth2 ? '-' . $fullMonth2 : '');

        return ['months' => $months, 'month1number' => $month1number, 'month2number' => $month2number];
    }

    private function setField(stdClass &$item, string $fieldName, string|null $string, string $id = ''): void
    {
        if ($string) {
            $item->$fieldName = $string;
            $this->verbose(['fieldName' => ($id ? '('. $id . ') ' : '') . ucfirst($fieldName), 'content' => $item->$fieldName]);
        }
    }

    private function addToField(stdClass &$item, string $fieldName, string|null $string, string $id = ''): void
    {
        if ($string) {
            if (isset($item->$fieldName) && $item->$fieldName && ! in_array(substr($item->$fieldName, -1), ['.', '?', '!'])) {
                $item->$fieldName .= '.';
            }
            $this->setField($item, $fieldName, (isset($item->$fieldName) ? $item->$fieldName . ' ' : '') . $string) . 
            $this->verbose(['fieldName' => ($id ? '('. $id . ') ' : '') . ucfirst($fieldName), 'content' => $item->$fieldName]);
        }
    }

    private function addToAuthorString(int $i, string &$string, string $addition): void
    {
        $string .= $addition;
        $this->verbose(['addition' => "(" . $i . ") Added \"" . $addition . "\" to list of authors"]);
    }

    // Does $string start with US State abbreviation, possibly preceded by ', '?
    private function getUsState(string $string): string|bool
    {
        if (preg_match('/^(,? ?[A-Z]\.? ?[A-Z]\.?)[,.: ]/', $string, $matches)) {
            return $matches[1];
        }
        
        return false;
    }

    // Get title from a string that starts with title and then has publication information.
    // Case in which title is in quotation marks or italics is dealt with separately.
    // Case in which title is followed by authors (editors), as in <booktitle> <editor> format, is handled by
    // getTitlePrecedingAuthor method.
    private function getTitle(string &$remainder, string|null &$edition, string|null &$volume, bool &$isArticle, string|null &$year = null, string|null &$note, string|null $journal, bool $containsUrlAccessInfo, bool $includeEdition = false, string $language = 'en'): string|null
    {
        $title = null;
        $originalRemainder = $remainder;

        $remainder = str_replace('  ', ' ', $remainder);
        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;
        $skipNextWord = false;

        $note = null;

        // If $remainder contains $journal, take $title to be string up to start of $journal, possibly
        // prepended by a 'forthcoming' string.
        // Remove italic codes, if any, from remainder starting with first word of $journal
        if ($journal) {
            $strippedRemainder = $remainder;
            foreach ($this->italicCodes as $italicCode) {
                $strippedRemainder = str_replace($italicCode . $journal, $journal, $strippedRemainder);
            }
            $journalStartPos = strpos($strippedRemainder, $journal);
            $title = substr($remainder, 0, $journalStartPos);
            if (preg_match('/(?P<title>.*)\(?(?P<forthcoming>' . $this->forthcomingRegExp . ')\)?$/i', rtrim($title, '., '), $matches)) {
                $title = $matches['title'];
                $note = $matches['forthcoming'];     
            }
            // $remainder includes $italicCode at start
            $remainder = substr($remainder, $journalStartPos);
            return $title;
        }

        // If $remainder ends with string in parenthesis, look at the string
        if (preg_match('/\((.*?)\)$/', rtrim($remainder, '. '), $matches)) {
            $match = $matches[1];
            if (Str::contains($match, $this->publishers)) {
                // String in parentheses seems like it's the publication info; set $title equal to preceding string
                $title = rtrim(Str::before($remainder, $match), ' (');
                $remainder = $match;
                $this->verbose('Taking title to be string preceding string in parentheses, which is taken to be publication info');
                return $title;
            }
        }

        // Common pattern for journal article.  (Allow year at end of title, but no other pattern with digits, otherwise whole string,
        // including journal name and volume, number, and page info may be included.)
        if (preg_match('/^(?P<title>[^\.]+ (?P<lastWord>([a-zA-Z]+|(19|20)[0-9]{2})))\. (?P<remainder>[a-zA-Z\.,\\\' ]{5,30} [0-9;():\-.,\. ]*)$/', $remainder, $matches)) {
            $lastWord = $matches['lastWord'];
            // Last word has to be in the dictionary (proper nouns allowed) and not an excluded word, OR start with a lowercase letter
            // That excludes cases in which the period ends an abbreviation in a journal name (like "A theory of something, Bull. Amer.").
            if (
                ! in_array($lastWord, ['J'])
                &&
                (
                    ($this->inDict($lastWord, false) && ! in_array($lastWord, $this->excludedWords))
                    ||
                    mb_strtolower($lastWord[0]) == $lastWord[0]
                )
               ) {
                $title = $matches['title'];
                $remainder = $matches['remainder'];
                $this->verbose('Taking title to be string preceding period.');
                return $title;
            }
        }

        $containsPages = preg_match('/(\()?' . $this->pagesRegExp . '(\))?/', $remainder);
        $volumeRegExp = '/(^\(v(ol)?\.?|volume) (\d)\.?\)?[.,]?$/i';
        $editionRegExp = '/(^(?P<fullEdition>\(' . $this->editionRegExp . '\)|^' . $this->editionRegExp . '))(?P<remains>.*$)/iJ';

        // Go through the words in $remainder one at a time.
        foreach ($words as $key => $word) {
            if (substr($word, 0, 1) == '"') {
                $word = '``' . substr($word, 1);
            }
            if (substr($word, -1) == '"') {
                $word = substr($word, 0, -1) . "''";
            }

            array_shift($remainingWords);
            $remainder = implode(' ', $remainingWords);

            // If $word is one of the italic codes ending in a space and previous word ends in some punctuation, OR
            // word is '//' (used as separator in some references (Russian?)), stop and form title
            if (
                    (
                    in_array($word . ' ', $this->italicCodes) &&
                    isset($words[$key-1]) &&
                    in_array(substr($words[$key-1], -1), [',', '.', ':', ';', '!', '?'])
                    )
                    ||
                    $word == '//'
                ) {
                $this->verbose("Ending title, case 1a");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                break;
            }

            if (Str::endsWith($word, '//')) {
                $this->verbose("Ending title, case 1b");
                $title = rtrim(implode(' ', $initialWords), ',:;.') . ' ' . substr($word, 0, -2);
                break;
            }

            $initialWords[] = $word;

            if (preg_match('/^vol(\.?|ume) [0-9]/', $remainder)) {
                $this->verbose("Ending title, case 1c");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                break;
            }

            if ($skipNextWord) {
                $skipNextWord = false;
            } else {
                $nextWord = $words[$key + 1] ?? null;
                $nextButOneWord = $words[$key + 2] ?? null;
                $word = trim($word);
                $nextWord = trim($nextWord);

                if (empty($nextWord)) {
                    $title = rtrim(implode(' ', $initialWords), ',:;.');
                    break;
                }

                // String up to next '?', '!', ',', or '.' not preceded by ' J'.
                $chars = mb_str_split($remainder, 1, 'UTF-8');
                $stringToNextPeriodOrComma = '';

                foreach ($chars as $i => $char) {
                    if ($char == '(') {
                        break;
                    }
                    $stringToNextPeriodOrComma .= $char;
                    if (
                            in_array($char, ['?', '!', ','])
                            ||
                            (
                                $char == '.' &&
                                    (
                                        ($i == 1 && $chars[0] != 'J') 
                                            || ($i >= 2 && ! ($chars[$i-1] == 'J' && $chars[$i-2] == ' '))
                                    )
                            )
                        ) {
                        break;
                    }
                }

                $stringToNextPeriod = '';

                foreach ($chars as $i => $char) {
                    if ($char == '(') {
                        break;
                    }
                    $stringToNextPeriod .= $char;
                    if (
                            in_array($char, ['?', '!'])
                            ||
                            (
                                $char == '.' &&
                                    (
                                        ($i == 1 && $chars[0] != 'J') 
                                            || ($i >= 2 && ! ($chars[$i-1] == 'J' && $chars[$i-2] == ' '))
                                    )
                            )
                        ) {
                        break;
                    }
                }

                $wordAfterNextCommaOrPeriod = strtok(substr($remainder, 1 + strlen($stringToNextPeriodOrComma)), ' ');
                $upcomingRoman = $upcomingArticlePubInfo = false;

                $upcomingYear = $upcomingVolumePageYear = $upcomingVolumeNumber = false;
                if ($stringToNextPeriodOrComma) {
                    $remainderFollowingNextPeriodOrComma = mb_substr($remainder, mb_strlen($stringToNextPeriodOrComma));
                    $remainderFollowingNextPeriod = mb_substr($remainder, mb_strlen($stringToNextPeriod));
                    $upcomingYear = $this->isYear(trim($remainderFollowingNextPeriodOrComma));
                    $upcomingVolumePageYear = preg_match('/^[0-9\(\)\., p\-]{2,}$/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingVolumeNumber = preg_match('/^(' . $this->volRegExp3 . ')[0-9]{1,4},? (' . $this->numberRegExp . ')? ?\(?[0-9]{1,4}\)?/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingRoman = preg_match('/^[IVXLCD]{1,6}[.,; ] ?/', trim($remainderFollowingNextPeriodOrComma));
                    $followingRemainderMinusMonth = preg_replace('/' . $this->monthsRegExp[$language] . '/', '', $remainderFollowingNextPeriodOrComma);
                    $upcomingArticlePubInfo = preg_match('/^[0-9.,;:\-() ]{8,}$/', $followingRemainderMinusMonth);
                }

                $upcomingJournalAndPubInfo = $upcomingPageRange = false;
                $wordsToNextPeriodOrComma = explode(' ', $stringToNextPeriodOrComma);

                // This case may arise if a string has been removed from $remainder and a lone '.' is left in the middle of it.
                // if (isset($remainingWords[0]) && $remainingWords[0] == '.') {
                //     array_shift($remainingWords);
                //     $remainder = ltrim($remainder, ' .');
                // }
                $upcomingBookVolume = preg_match('/(^\(?Vols?\.? |^\(?VOL\.? |^\(?Volume |^\(?v\. )\S+ (?!of)/', $remainder);
                $upcomingVolumeCount = preg_match('/^\(?(?P<note>[1-9][0-9]{0,1} ([Vv]ols?\.?|[Vv]olumes))\)?/', $remainder, $volumeCountMatches);
                $journalPubInfoNext = preg_match('/^(19|20)[0-9]{2}(,|;| ) ?(' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?([0-9, \-p\.():]*$|\([0-9]{2,4}\))/', $remainder);

                if ($journalPubInfoNext) {
                    $this->verbose("Ending title, case 2a (journal pub info next, with no journal name");
                    $title = rtrim(implode(' ', $initialWords), ',:;.');
                    $isArticle = true;
                    break;
                }

                // When a word ending in punctuation or preceding a word starting with ( is encountered, check whether
                // it is followed by
                // italics
                // OR a Working Paper string
                // OR a pages string
                // OR "in" OR "Journal"
                // OR a volume designation
                // OR the remainder consists of a string of letters followed by a comma (journal name?), then numbers (volume, pages etc)
                // OR words like 'forthcoming' or 'to appear in'
                // OR a year 
                // OR the name of a publisher.
                // If so, the title is $remainder up to the punctuation.
                // Before checking for punctuation at the end of a work, trim ' and " from the end of it, to take care
                // of the cases ``<word>.'' and "<word>."
                if (
                    ! in_array($word, ['St.'])
                    &&
                    (
                        Str::endsWith(rtrim($word, "'\""), ['.', '!', '?', ':', ',', ';']) 
                        ||
                        ($nextWord && in_array($nextWord[0], ['(', '['])) 
                        || 
                        ($nextWord && $nextWord == '-')
                    )
                   ) {
                    $remainderMinusArticle = preg_replace('/[\( ][Aa]rticle /', '', $remainder);

                    if (
                        ! Str::endsWith($word, ':')
                        &&
                        (
                            // e.g. SIAM J. ... (Don't generalize too much, because 'J.' can be an editor's initial.)
                            preg_match('/^(SIAM (J\.|Journal)|IEEE Transactions|ACM Transactions)/', $remainder)
                            // journal name, pub info?
                            || preg_match('/^[A-Z][a-z]+,? [0-9, -p\.]*$/', $remainder)
                            || in_array('Journal', $wordsToNextPeriodOrComma)
                            || preg_match('/^Revue /', $remainder)
                            // journal name, pub info ('}' after volume # for \textbf{ (in $this->volumeRegExp))
                            // ('?' is a possible character in a page range because it can appear for '-' due to an encoding error)
                            // The following pattern allows too much latitude --- e.g. "The MIT Press. 2015." matches it.
                            // || preg_match('/^[A-Z][A-Za-z &]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?[0-9, \-p\.():\?]*$/', $remainder) 
                            // journal name followed by publication info, allowing issue number and page
                            // numbers to be preceded by letters --- no year.
                            || preg_match('/^[A-Z][A-Za-z &()]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/]{0,4}\)?,? ?' . $this->pagesRegExp . '\.? ?$/', $remainder) 
                            // journal name followed by more specific publication info, year at end, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^[A-Z][A-Za-z &()]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $this->pagesRegExp . '(, |. |.)(\(?(19|20)[0-9]{2}\)?)$/', $remainder) 
                            // journal name followed by more specific publication info, year first, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^[A-Z][A-Za-z &()]+[,.]? (19|20)[0-9]{2},? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $this->pagesRegExp . '\.? ?$/', $remainder)
                            // $word ends in period && journal name (can include commma), pub info ('}' after volume # for \textbf{ (in $this->volumeRegExp))
                            || (Str::endsWith($word, ['.']) && preg_match('/^[A-Z][A-Za-z, &]+,? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?([0-9, \-p\.():\/]*$|\([0-9]{2,4}\))/', $remainderMinusArticle))
                        )
                    ) {
                        $upcomingJournalAndPubInfo = true;
                        $isArticle = true;
                        $this->verbose('Followed by journal name and publication info, so classified as article');
                    }

                    // $word ends in period && then there are letters and spaces, and then a page range in parens
                    // (so string before page range is booktitle?)
                    if (Str::endsWith($word, ['.']) && preg_match('/^[A-Z][A-Za-z ]+,? ?\(?(' . $this->pagesRegExp . ')/', $remainder)) { 
                        $upcomingPageRange = true;
                    }

                    $translatorNext = false;
                    // "(John Smith, trans.)"
                    if (in_array($nextWord[0], ['('])) {
                        $translatorNext = preg_match('/^\((?P<translator>[^)]+[Tt]rans\.)\)(?P<remainder>.*)/', $remainder, $matches);
                        if (isset($matches['translator'])) {
                            $note = ($note ? $note . '. ' : '') . $matches['translator'];
                            $remainder = $matches['remainder'];
                        }
                    } else {
                        // "trans. John Smith)"
                        // Here trans must start with lowercase, because journal name might start with Trans.
                        $translatorNext = preg_match('/^trans\. (?P<translator>[^.]+\.)(?P<remainder>.*)/', $remainder, $matches);
                        if (isset($matches['translator'])) {
                            $note = ($note ? $note . '. ' : '') . 'Translated by ' . $matches['translator'];
                            $remainder = $matches['remainder'];
                        }
                    }

                    if (
                        $this->containsFontStyle($remainder, true, 'italics', $startPos, $length)
                        || $upcomingJournalAndPubInfo
                        || $upcomingPageRange
                        || $translatorNext
                        // After stringToNextPeriod, there are only digits and punctuation for volume-number-page-year info
                        || (
                            Str::endsWith(rtrim($word, "'\""), [',', '.']) 
                            && ($upcomingVolumePageYear || $upcomingVolumeNumber || $upcomingRoman || $upcomingArticlePubInfo || $upcomingBookVolume || $upcomingVolumeCount)
                           )
                        || preg_match('/^\(?' . $this->workingPaperRegExp . '/i', $remainder)
                        || preg_match($this->startPagesRegExp, $remainder)
                        || preg_match('/^[Ii]n:? [`\']?([A-Z]|[19|20][0-9]{2})|^' . $this->journalWord . ' |^Annals |^Proceedings |^\(?Vols?\.? |^\(?VOL\.? |^\(?Volume |^\(?v\. | Meeting /', $remainder)
                        || (
                            $nextWord 
                            && Str::endsWith($nextWord, '.') 
                            && in_array(substr($nextWord,0,-1), $this->startJournalAbbreviations)
                           )
                        || (
                            $nextWord 
                            && $nextButOneWord 
                            && (Str::endsWith($nextWord, range('a', 'z')) || in_array($nextWord, ['IEEE', 'ACM'])) 
                            && Str::endsWith($nextButOneWord, '.') 
                            && in_array(substr($nextButOneWord,0,-1), $this->startJournalAbbreviations)
                           )
                        // pages (e.g. within book)
                        || preg_match('/^\(?pp?\.? [0-9]/', $remainder)
                        || preg_match('/' . $this->startForthcomingRegExp . '/i', $remainder)
                        || preg_match('/^(19|20)[0-9][0-9](\.|$)/', $remainder)
                        // address [no spaces]: publisher in db
                        || (
                            preg_match('/^[A-Z][a-z]+: (?P<publisher>[A-Za-z ]*),/', $remainder, $matches) 
                            && in_array(trim($matches['publisher']), $this->publishers)
                           )
                        // address [city in db]: publisher
                        || (
                            preg_match('/^(?P<city>[A-Z][a-z]+): /', $remainder, $matches) 
                            && in_array(trim($matches['city']), $this->cities)
                           )
                        // publisher, address [city in db], <year>?
                        || (
                            preg_match('/^[A-Z][a-z]+, (?P<city>[A-Za-z ]+)(, (19|20)[0-9]{2})?$/', $remainder, $matches) 
                            && in_array(trim($matches['city']), $this->cities)
                           )
                        // . <address>: <publisher>(, <year>)?$ OR (<address>: <publisher>(, <year>)?)
                        // Note that ',' is allowed in address and
                        // '.' and '&' are allowed in publisher.  May need to put a limit on length of publisher part?
                        || (
                            (Str::endsWith($word, '.') || $nextWord[0] == '(')
                            //&& preg_match('/^\(?[\p{L}, ]+: [\p{L}&\-. ]+(, (19|20)[0-9]{2})?\)?$/u', $remainder, $matches) 
                            //&& preg_match('/^' . $this->addressPublisherYearRegExp . '$/u', $remainder, $matches) 
                            && $this->isAddressPublisher($remainder)
                           )
                        // (<publisher> in db
                        || Str::startsWith(ltrim($remainder, '('), $this->publishers)
                        // (<city> in db
                        || Str::startsWith(ltrim($remainder, '('), $this->cities)
                        // Thesis
                        || preg_match('/^[\(\[\-]? ?' . $this->fullThesisRegExp . '/i', $remainder)
                        ) {
                        $this->verbose("Ending title, case 2 (word '" . $word . "')");
                        $title = rtrim(implode(' ', $initialWords), ',:;.');
                        if (preg_match('/^' . $this->journalWord . ' /', $remainder)) {
                            $isArticle = true;
                        }
                        if ($upcomingBookVolume) {
                            $volume = trim($nextButOneWord, '.,) ');
                            $remainder = implode(' ', array_splice($remainingWords, 2));
                        }
                        if ($upcomingVolumeCount) {
                            $note = $volumeCountMatches['note'];
                            $remainder = implode(' ', array_splice($remainingWords, 2));
                        }
                        break;
                    }
                }

                // Upcoming volume specification
                if ($nextWord && $nextButOneWord && preg_match($volumeRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
                    $volume = $matches[2];
                    $this->verbose('volume set to "' . $volume . '"');
                    $this->verbose("Ending title, case 3a");
                    $title = rtrim(implode(' ', $initialWords), ' ,');
                    array_splice($remainingWords, 0, 2);
                    $remainder = implode(' ', $remainingWords);
                    break;
                }

                // Upcoming edition specification
                $testString = implode(' ', $remainingWords);

                if (preg_match($editionRegExp, $testString, $matches)) {
                    $edition = trim($matches['edition']);
                    $this->verbose('edition set to "' . $edition . '"');
                    $fullEdition = $matches['fullEdition'];
                    $this->verbose("Ending title, case 3b");
                    $title = $includeEdition ? rtrim(implode(' ', $initialWords) . ' ' . $fullEdition, ' ,') : rtrim(implode(' ', $initialWords), ' ,');
                    $remainder = $matches['remains'];
                    break;
                }

                // If end of title has not been detected and word ends in period-equivalent or comma
                if (
                    Str::endsWith($word, ['.', '!', '?', ','])
                    ) {
                        $this->verbose('$stringToNextPeriodOrComma: ' . $stringToNextPeriodOrComma);
                        $this->verbose('$wordAfterNextCommaOrPeriod: ' . $wordAfterNextCommaOrPeriod);
                        $this->verbose('$stringToNextPeriod: ' . $stringToNextPeriod);
                    // if first character of next word is lowercase letter and does not end in period
                    // OR $word and $nextWord are A. and D. or B. and C.
                    // OR following string starts with a part designation, continue, skipping next word,
                    if (
                        $nextWord 
                            && (
                            (ctype_alpha($nextWord[0]) && mb_strtolower($nextWord[0]) == $nextWord[0] && substr($nextWord, -1) != '.' && rtrim($nextWord, ':') != 'in')
                                    || ($word == 'A.' && $nextWord == 'D.')
                                    || ($word == 'B.' && $nextWord == 'C.')
                                    || preg_match('/^(Part )?II?I?[:.] /', $remainder)
                                )
                        ) {
                        $this->verbose("Not ending title, case 1 (next word is " . $nextWord . ")");
                        $skipNextWord = true;
                    } elseif 
                        (
                            $nextWord 
                            && 
                            strlen($nextWord) < 8 
                            &&
                            Str::endsWith($nextWord, '.') 
                            && 
                            isset($words[$key+2]) 
                            &&
                            ! in_array($words[$key+2], ['J', 'J.', 'Journal'])
                            &&
                            (! Str::endsWith($word, ',') || 
                                (! $this->inDict(substr($nextWord, 0, -1)) && ! in_array(substr($nextWord, 0, -1), $this->countries)) || 
                                $this->isInitials($nextWord) || 
                                (mb_strtolower($nextWord[0]) == $nextWord[0]
                                &&
                                mb_strtolower($words[$key+2][0]) == $words[$key+2][0])
                            ) 
                            && 
                            (! $journal || rtrim($nextWord, '.') == rtrim(strtok($journal, ' '), '.'))
                            &&
                            ! ($word == 'U.' && in_array($nextWord, ['K.', 'S.'])) // special case of 'U. S.' or 'U. K.' in title
                            &&
                            $word != 'St.' 
                        ) {
                        $this->verbose("Ending title, case 4");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // elseif next sentence starts with a thesis designation, terminate title
                    } elseif (preg_match('/^[\(\[]' . $this->fullThesisRegExp . '[\)\]]/', $stringToNextPeriodOrComma)) {
                        $this->verbose("Ending title, case 4a");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // elseif next sentence contains word 'series', terminate title
                    } elseif (preg_match('/(?<!time) series/i', $stringToNextPeriodOrComma)) {
                        $this->verbose("Ending title, case 4b (next sentence contains 'series' not preceded by 'time')");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    } elseif (preg_match('/edited by/i', $nextWord . ' ' . $nextButOneWord)) {
                        $this->verbose("Ending title, case 4c");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // else if string up to next period contains only letters, spaces, hyphens, (, ), \, ,, :, and
                    // quotation marks and doesn't start with "in"
                    // (which is unlikely to be within a title following punctuation)
                    // and is followed by at least 30 characters or 37 if it contains pages (for the publication info),
                    // assume it is part of the title,
                    } elseif (
                            preg_match('/^[a-zA-Z0-9 \-\(\)`"\':,\/]+$/', substr($stringToNextPeriodOrComma,0,-1))
                            //preg_match('/[a-zA-Z -]+/', substr($stringToNextPeriodOrComma,0,-1))
                            && !preg_match($this->inRegExp1, $remainder)
                            && strlen($remainder) > strlen($stringToNextPeriodOrComma) + ($containsPages ? 37 : 30)
                            && ! $upcomingYear
                            ) {
                        $this->verbose("Not ending title, case 2 (next word is '" . $nextWord . "', and string to next period or comma is '" . $stringToNextPeriodOrComma . "')");
                    // else if working paper string occurs later in remainder,
                    } elseif (preg_match('/(.*)(' . $this->workingPaperRegExp . ')/i', $remainder, $matches)) {
                        // if no intervening punctuation, end title
                        if (!Str::contains($matches[1], ['.', ',', ':'])) {
                            $this->verbose("Ending title, case 5");
                            $title = rtrim(Str::before($originalRemainder, $matches[0]), '., ');
                            break;
                        // otherwise keep going
                        } else {
                            $this->verbose("Not ending title, case 3 (working paper string is coming up)");
                        }
                    // else if there has been no period so far and italics is coming up, 
                    // wait for the italics (journal name?)
                    } elseif ($this->containsFontStyle($remainder, false, 'italics', $startPos, $length)) {
                        $this->verbose("Not ending title, case 4 (italics is coming up)");
                    // else if word ends with comma and remainder doesn't start with "[a-z]+ journal "
                    // and volume info is coming up, wait for it
                    } elseif (Str::endsWith($word, [',']) && preg_match('/^[a-z]+ journal/i', $remainder)) {
                        $this->verbose("Ending title, case 5a (word: \"" . $word . "\"; journal info is next)");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // } elseif (Str::endsWith($word, [',']) && preg_match('/' . $this->volumeRegExp . '/', $remainder)) {
                    //     $this->verbose("Not ending title, case 5 (word: \"" . $word . "\"; volume info is coming up)");
                    } else {
                        // else if 
                        // (word ends with period or comma and there are 4 or more words till next punctuation, which is a period)
                        // OR entry contains url access info [in which case there is no more publication info to come]
                        // AND ... AND stringToNextPeriod doesn't start with In or pp and doesn't contain commas or colons
                        // AND (the rest of the remainder is not all-numbers and punctuation (has to include publication info) OR
                        // entry contains url access info (which has been removed))
                        // Treat hyphens in words as spaces
                        $modStringToNextPeriod = preg_replace('/([a-z])-([a-z])/', '$1 $2', $stringToNextPeriodOrComma);
                        $wordsToNextPeriodOrComma = explode(' ',  $modStringToNextPeriod);
                        // $lcWordCount = 0;
                        // foreach ($wordsToNextPeriodOrComma as $remainingWord) {
                        //     if (! in_array($remainingWord, $this->stopwords) && isset($remainingWord[0]) && ctype_alpha($remainingWord[0]) && mb_strtolower($remainingWord) == $remainingWord) {
                        //         $lcWordCount++;
                        //     }
                        // }
                        //if ((($lcWordCount > 2 && substr_count($modStringToNextPeriod, ' ') > 3) || $containsUrlAccessInfo)
                        if ((substr_count($modStringToNextPeriod, ' ') > 3) // || $containsUrlAccessInfo)
                            // comma added in next line to deal with one case, but it may be dangerous
                            && Str::endsWith($word, ['.', ',', '?', '!']) 
                            && ! Str::startsWith($modStringToNextPeriod, ['In']) 
                            && ! Str::contains($modStringToNextPeriod, ['pp.']) 
                            && substr_count($modStringToNextPeriod, ',') == 0
                            && substr_count($modStringToNextPeriod, ':') == 0
                            && (! preg_match('/^[0-9;:\.\- ]*$/', $remainderFollowingNextPeriodOrComma) || $containsUrlAccessInfo)
                        ) {
                            $this->verbose("Not ending title, case 6 (word '" . $word ."')");
                        } elseif (! isset($words[$key+2])) {
                            if ($this->isYear($nextWord)) {
                                $year = $nextWord;
                                $remainder = '';
                                $this->verbose("Ending title: last word in remainder is year");
                            } elseif (substr($word, -1) == '.' && substr($nextWord, -1) == ':') {
                                $title = implode(' ', $initialWords);
                                $remainder = $nextWord;
                            } elseif (substr($word, -1) == '.') {
                                $this->verbose("Ending title (word '" . $word ."')");
                                $title = implode(' ', $initialWords);
                                break;
                            } else {
                                $this->verbose("Adding \$nextWord (" . $nextWord . "), last in string, and ending title (word '" . $word ."')");
                                $title = implode(' ', $initialWords) . ' ' . $nextWord;
                                $remainder = '';
                            }
                            break;
                        // Next case was intended for title followed by authors (as is <booktitle> <editors>) ---
                        // but that case is now handled separately
                        // } elseif (Str::endsWith($word, [',']) && preg_match('/[A-Z][a-z]+, [A-Z]\. /', $remainder)) {
                        //     $this->verbose("Ending title, case 6a (word '" . $word ."')");
                        //     $title = rtrim(implode(' ', $initialWords), '.,');
                        //     break;
                        } elseif (Str::endsWith($word, [','])) {
                            $this->verbose("Not ending title, case 7a (word '" . $word ."')");
                        } elseif (in_array(rtrim($wordAfterNextCommaOrPeriod, '.'), $this->startJournalAbbreviations)) {
                            // Word after next comma or period is a start journal abbreviation
                            $this->verbose("Not ending title, case 7b");
                        } elseif (
                            isset($remainderFollowingNextPeriodOrComma) 
                            && preg_match('/^[A-Z][a-z]+: [A-Z][a-z]+$/', trim($remainderFollowingNextPeriodOrComma, '. '))
                            ) {
                            // one-word address: one-word publisher follow next period.  (Could intervening sentence be series in this case?)
                            $this->verbose("Not ending title, case 7c (word '" . $word ."'): <address>: <publisher> follow next comma or period");
                        } elseif (
                            isset($remainderFollowingNextPeriod) 
                            && strlen($stringToNextPeriod) > 5 
                            && preg_match('/[a-z][.,]$/', $stringToNextPeriod) 
                            && ! Str::endsWith($stringToNextPeriod, 'Univ.') 
                            //&& preg_match('/^[\p{L}., ]+: [\p{L}&\- ]+$/u', trim($remainderFollowingNextPeriod, '. '))
                            //&& preg_match('/^' . $this->addressPublisherRegExp . '$/u', trim($remainderFollowingNextPeriod, '. '))
                            && $this->isAddressPublisher(trim($remainderFollowingNextPeriod, '. '), allowYear: false)
                            ) {
                            // <address>: <publisher> follows string to next period: If string to next period
                            // (note: comma not allowed, because comma may appear in address --- New York, NY)
                            // has at least 6 characters and a lowercase letter preceded the punctuation,
                            // allow spaces and periods (and any utf8 letter) in the <address> 
                            $this->verbose("Not ending title, case 7d (word '" . $word ."'): <address>: <publisher> follow next comma or period");
                        } else {
                            // otherwise assume the punctuation ends the title.
                            $this->verbose("Ending title, case 6b (word '" . $word ."')");
                            $title = rtrim(implode(' ', $initialWords), '.,');
                            break;
                        }
                    }
                } 
            }
        }

        // If no title has been identified and $originalRemainder contains a comma, take title to be string up to first comma.
        // Otherwise take title to be whole string.
        if (! $title) {
            if (Str::contains($originalRemainder, ',')) {
                $this->verbose("Title not clearly identified; setting it equal to string up to first comma");
                $title = Str::before($originalRemainder, ',');
                $newRemainder = ltrim(Str::after($originalRemainder, ','), ' ');
            } else {
                $title = implode(' ', $initialWords);
            }
        }

        $remainder = $newRemainder ?? $remainder;
        // if (isset($remainder[0]) && $remainder[0] == '(') {
        //     $remainder = substr($remainder, 1);
        // }

        return $title;
    }

    // Get title from a string that starts with title and then has authors (e.g. editors, in <booktitle> <editor> format)
    private function getTitlePrecedingAuthor(string &$remainder, string $language = 'en'): string|null
    {
        $title = null;

        $remainder = str_replace('  ', ' ', $remainder);
        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;

        foreach ($words as $word) {
            array_shift($remainingWords);
            $remainder = implode(' ', $remainingWords);
            $initialWords[] = $word;

            if (Str::endsWith($word, ['.', ',']) && $this->isNameString($remainder)) {
                $title = rtrim(implode(' ', $initialWords), ',');
                break;
            }
        }
 
        return $title;
    }

    private function requireUc(string $string): string
    {
        $words = explode(" ", $string);
        $returnString = '';
        foreach ($words as $word) {
            $returnString .= ' ';
            if (in_array($word, $this->names)) {
                $returnString .= '{' . $word[0] . '}' . substr($word, 1);
            } else {
                $returnString .= $word;
            }
        }

        $returnString = ltrim($returnString, " ");

        return $returnString;
    }

    // Truncate $string at first '%' that is not preceded by '\'.  Return true if truncated, false if not.
    private function uncomment(string &$string) : bool
    {
        $truncated = false;
        $pos = strpos($string, '%');
        if ($pos !== false && ($pos === 0 || $string[$pos-1] != '\\')) {
            $string = substr($string, 0, $pos);
            $truncated = true;
        }

        return $truncated;
    }

    // Replace every substring of multiple spaces with a single space.  (\h is a horizontal white space.)
    private function regularizeSpaces(string $string): string
    {
        // Using \h seems to mess up utf-8
        //return preg_replace('%\h+%', ' ', $string);  
        return preg_replace('% +%', ' ', $string);  
    }

    /*
     * Remove all matches for $regExp (regular expression without delimiters), case insensitive, from $string
     * and return resulting string (unaltered if there are no matches).
     */
    private function findAndRemove(string $string, string $regExp, int $limit = -1): string
    {
        return preg_replace('%' . $regExp . '%i', '', $string, $limit);
    }

    /*
     * Find first match for $regExp (regular expression without delimiters), case insensitive, in $string,
     * return group number $groupNumber (defined by parentheses in $regExp)
     * and remove entire match for $regExp from $string after trimming ',. ' from substring preceding match.
     * If no match, return false (and do not alter $string).
     */
    private function findRemoveAndReturn(string &$string, string $regExp, bool $caseInsensitive = true): false|string|array
    {
        $matched = preg_match(
            '%' . $regExp . '%' . ($caseInsensitive ? 'i' : ''),
            $string,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if (! $matched) {
            return false;
        }

        $result = [];
        for ($i = 0; isset($matches[$i][0]); $i++) {
            $result[$i] = $matches[$i][0];
        }

        $result['before'] = substr($string, 0, $matches[0][1]);
        $result['after'] = substr($string, $matches[0][1] + strlen($matches[0][0]), strlen($string));
        $string = substr($string, 0, $matches[0][1]) . ' ' . substr($string, $matches[0][1] + strlen($matches[0][0]), strlen($string));
        $string = $this->regularizeSpaces(trim($string));

        return $result;
    }

    /*
     * If $reportLabel is false: 
     * For $string that matches <label><content>, remove match for <label> and <content> and return match for <content>,
     * where <label> and <content> are regular expressions (without delimiters).  Matching is case-insensitive.
     * If no matches, return false.
     * If $reportLabel is true, return array with components 'label' and 'content'.
     * Example: $doi = $this->extractLabeledContent($string, ' doi:? | doi: ?|https?://dx.doi.org/|https?://doi.org/', '[a-zA-Z0-9/._]+');
     */ 
    private function extractLabeledContent(string &$string, string $labelPattern, string $contentPattern, bool $reportLabel = false): false|string|array
    {
        $matched = preg_match(
            '%(?P<label>' . $labelPattern . ')(?P<content>' . $contentPattern . ')%i',
            $string,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if (!$matched) {
            return false;
        }

        $content = trim($matches['content'][0], ' .,;');
        $string = substr($string, 0, $matches['label'][1]) . substr($string, $matches['content'][1] + strlen($matches['content'][0]), strlen($string));
        $string = $this->regularizeSpaces(trim($string, ' .,'));

        $returner = $reportLabel ? ['label' => trim($matches['label'][0]), 'content' => $content] : $content;

        return $returner;
    }
    
    /*
     * Split an array of words into sentences.  Each period that 
     * does not follow a single uc letter 
     * AND follows a word that is (EITHER in the dictionary OR follows an initial [in which case it is presumably a name])
     * AND is not an excluded word
     * ends a sentence.
    */
    private function splitIntoSentences(array $words): array
    {
        $aspell = Aspell::create();

        $sentences = [];
        $sentence = '';
        $wordCount = count($words);
        $prevWordInitial = false;
        foreach ($words as $key => $word) {
            $sentence .= ($sentence ? ' ' : '') . $word;
            $isInitial = (strlen($word) == 2 && strtoupper($word) == $word);
            if (
                substr($word, -1) == '.' 
                && ! $isInitial
                && ($prevWordInitial || 0 == iterator_count($aspell->check($word)))
                && !in_array(substr($word, 0, -1), $this->excludedWords)
            ) {
                $sentences[] = $sentence;
                $sentence = '';
            } elseif ($key == $wordCount - 1) {
                $sentences[] = $sentence;
            }
            $prevWordInitial = false;
            if ($isInitial) {
                $prevWordInitial = true;
            }
        }

        return $sentences;
    }

    /**
     * Report whether string contains opening string for font style, at start if $start is true
     * @param $string string The string to be searched
     * @param $start boolean: true if want to restrict to font style starting the string
     * @param $style string: 'italics' [italics or slanted] or 'bold'
     * @param $startPos: position in $string where font style starts
     * @param $length: length of string starting font style
     */
    private function containsFontStyle(string $string, bool $start, string $style, int|null &$startPos, int|null &$length): bool
    {
        if ($style == 'italics') {
            $codes = $this->italicCodes;
        } elseif ($style == 'bold') {
            $codes = $this->boldCodes;
        }
        foreach ($codes as $code) {
            $length = strlen($code);
            $startPos = strpos($string, $code);
            if ($startPos !== false && (($start && $startPos == 0) || !$start)) {
                return true;
            }
        }
        return false;
    }

    private function isInitials(string $word): bool
    {
        $case = 0;
        // Allow two periods after letter, in case of typo or initial at end of name string.
        if (preg_match('/^[A-Z]\.?\.?$/', $word)) { // A or A. or A..
            $case = 1;
        } elseif (preg_match('/^[A-Z]\.[A-Z]\.$/', $word)) { // A.B.
            $case = 2;
        } elseif (preg_match('/^[A-Z][A-Z]$/', $word)) { // AB
            $case = 3;
        } elseif (preg_match('/^[A-Z]\.[A-Z]\.[A-Z]\.$/', $word)) { // A.B.C.
            $case = 4;
        } elseif (preg_match('/^[A-Z][A-Z][A-Z]$/', $word)) { // ABC
            $case = 5;
        } elseif (preg_match('/^[A-Z]\.-[A-Z]\.$/', $word)) { // A.-B.
            $case = 6;
        } elseif (preg_match('/^{\\\\.I}$/', $word)) { // capital I with dot
            $case = 7;
        } elseif (in_array($word, ['Á.', 'Á'])) { // Á
            $case = 8;
        }

        if ($case) {
            $this->verbose("isInitials case " . $case);
            return true;
        } else {
            return false;
        }
    }

    /*
     * Report whether string is a date OR, if $type is 'contains', report the date it contains,
     * in a range of formats, including 2 June 2018, 2 Jun 2018, 2 Jun. 2018, June 2, 2018,
     * 6-2-2018, 6/2/2018, 2-6-2018, 2/6/2018.
     * If $allowRange is true, dates like 6-8 June, 2024 are allowed AND year is optional
     */
    private function isDate(string $string, string $language = 'en', string $type = 'is', bool $allowRange = false): bool|array
    {
        $ofs = ['en' => '', 'cz' => '', 'fr' => '', 'es' => 'de', 'my' => '', 'nl' => '', 'pt' => 'de '];

        $year = '(?P<year>(19|20)[0-9]{2})';
        $monthName = '(?P<monthName>' . $this->monthsRegExp[$language] . ')';
        $of = $ofs[$language];
        $day = '(?P<day>[0-3]?[0-9])';
        $dayRange = '(?P<day>[0-3]?[0-9](--?[0-3]?[0-9])?)';
        $monthNumber = '(?P<monthNumber>[01]?[0-9])';

        if ($type == 'is') {
            $starts = '^';
            $ends = '$';
        } elseif ($type == 'contains') {
            $starts = '';
            $ends = '';
        } elseif ($type == 'starts') {
            $starts = '^';
            $ends = '';
        }

        $matches = [];
        $isDates = [];
        if ($allowRange) {
            $isDates[1] = preg_match('/(' . $starts . $dayRange . '( ' . $of . ')?' . ' ' . $monthName . ',? ?' . '(' . $of . ' )?' . $year . '?' . $ends . ')/i' , $string, $matches[1]);
            $isDates[2] = preg_match('/(' . $starts . $monthName . ' ?' . $dayRange . ',? '. $year . '?' . $ends . ')/i', $string, $matches[2]);
        } else {
            $isDates[1] = preg_match('/(' . $starts . $day . '( ' . $of . ')?' . ' ' . $monthName . ',? ?' . '(' . $of . ' )?' . $year . $ends . ')/i' , $string, $matches[1]);
            $isDates[2] = preg_match('/(' . $starts . $monthName . ' ?' . $day . '(,? ' . $year . ')?' . $ends . ')/i', $string, $matches[2]);
            $isDates[3] = preg_match('/(' . $starts . $day . '[\-\/ ]' . $of . $monthNumber . ',?[\-\/ ]'. $of . $year . $ends . ')/i', $string, $matches[3]);
            $isDates[4] = preg_match('/(' . $starts . $monthNumber . '[\-\/ ]' . $day . ',?[\-\/ ]'. $year . $ends . ')/i', $string, $matches[4]);
            $isDates[5] = preg_match('/(' . $starts . $year . '[\-\/, ]' . $day . '[\-\/ ]' . $monthNumber . $ends . ')/i', $string, $matches[5]);
            $isDates[6] = preg_match('/(' . $starts . $year . '[, ]' . $monthName . ' ' . $day . $ends . ')/i', $string, $matches[6]);
            $isDates[7] = preg_match('/(' . $starts . $year . '[, ]' . $day . ' ' . $monthName . $ends . ')/i', $string, $matches[7]);
        }

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
                    return [
                        'date' => $matches[$i][0],
                        'year' => $matches[$i]['year'] ?? '',
                        'day' => $matches[$i]['day'] ?? '',
                        'monthNumber' => $matches[$i]['monthNumber'] ?? $monthNumber,
                        'monthName' => $matches[$i]['monthName'] ?? '',
                    ];
                }
            }
            return false;
        }
    }

    private function isAnd(string $string, $language = 'en'): bool
    {
        // 'with' is allowed to cover lists of authors like Smith, J. with Jones, A.
        return mb_strtolower($string) == $this->phrases[$language]['and'] || in_array($string, $this->andWords) || $string == 'with';
    }

    /*
     * Determine whether $word is component of a name: all letters and either all u.c. or first letter u.c. and rest l.c.
     * (and may be TeX accents)
     * If $finalPunc != '', then allow word to end in any character in $finalPunc.
     */
    private function isName(string $word, string $finalPunc = ''): bool
    {
        $result = false;
        if (in_array(substr($word, -1), str_split($finalPunc))) {
            $word = substr($word, 0, -1);
        }
        if (preg_match('/^[a-z{}\\\"\'\-]+$/i', $word) && (ucfirst($word) == $word || strtoupper($word) == $word)) {
            $result = true;
        }

        return $result;
    }

    // The following two methods use similar logic to attempt to determine whether a string
    // starts with names.  They should be consolidated.

    /*
     * Determine whether $string plausibly starts with a list of names
     * The method checks only the first 2 or 3 words in the string, not the whole string
     */
    private function isNameString(string $string): bool
    {
        $phrases = $this->phrases;
        $this->verbose("isNameString is examining string \"" . $string . "\"");
        $result = false;
        $words = explode(' ', $string);
        $word1 = count($words) > 1 ? rtrim($words[1], ',.;') : null;
        if ($this->isInitials($words[0]) && count($words) >= 2) {
            $this->verbose('First word is initials and there are at least 2 words in string');
            if ($this->isName(rtrim($word1, '.,')) && (ctype_alpha($word1) || count($words) == 2)) {
                $this->verbose("isNameString: string is name (case 1): <initial> <name>");
                $result = true;
            } elseif (
                    $this->isInitials($word1)
                    && count($words) >= 3
                    && $this->isName(rtrim($words[2], '.,'))
                    && ctype_alpha(rtrim($words[2], '.,'))
            ) {
                $this->verbose("isNameString: string is name (case 2): <initial> <initial> <name>");
                $result = true;
            } elseif (
                in_array($word1, $this->vonNames)
                && count($words) >= 3
                && $this->isName(rtrim($words[2], '.,'))
                && ctype_alpha(rtrim($words[2], '.,'))
            ){
                $this->verbose("isNameString: string is name (case 3): <initial> <vonName> <name>");
                $result = true;
            } else {
                $this->verbose("isNameString: string is not name (1)");
            }
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isInitials($word1)) {
            $this->verbose("isNameString: string is name (case 4): <name> <initial>");
            $result = true;
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isName($word1, '.')) {
            $this->verbose("isNameString: string is name (case 5): <name> <name>");
            $result = true;
        } elseif ($this->isName($words[0], ',') && count($words) >= 2 && $this->isName($word1) && $words[2] == $phrases['and']) {
            $this->verbose("isNameString: string is name (case 6): <name> <name> and");
            $result = true;
        } else {
            $this->verbose("isNameString: string is not name (2)");
        }

        return $result;
    }

    /*
     * Determine whether string plausibly starts with a name
     * The method checks only the first few words in the string, not the whole string
     */
    private function initialNameString(string $string): bool
    {
        $phrases = $this->phrases;
        $result = false;
        $words = explode(' ', $string);
        if ($this->isInitials($words[0])) {
            if (isset($words[1]) && $this->isName($words[1], '.')) {
                $result = true;
            } elseif (isset($words[1]) && $this->isInitials($words[1])
                    && isset($words[2]) && $this->isName($words[2], '.')) {
                $result = true;
            }
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isInitials($words[1])) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isName($words[1], '.')) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') && isset($words[1]) && $this->isName($words[1]) && isset($words[2]) && $words[2] == $phrases['and']) {
            $result = true;
        }

        return $result;
    }

    private function verbose(string|array $arg): void
    {
        $this->detailLines[] = $arg;
    }

    /**
     * convertToAuthors: determine the authors in the initial elements of an array of words
     * After making a change, check that all examples are still converted correctly.
     * @param array $words array of words
     * @param string|null $remainder: remaining string after authors removed
     * @param string|null $year
     * @param string|null $month
     * @param string|null $day
     * @param string|null $date
     * @param boolean $determineEnd: if true, figure out where authors end; otherwise take whole string to be authors
     * @param string $type: 'authors' or 'editors'
     * @return array, with author string and warnings
     */
    private function convertToAuthors(array $words, string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, bool $determineEnd = true, string $type = 'authors', string $language = 'en'): array
    {
        $namePart = $authorIndex = $case = 0;
        $prevWordAnd = $prevWordVon = $done = $isEditor = $hasAnd = $multipleAuthors = false;
        $authorstring = $fullName = '';
        $remainingWords = $words;
        if ($type == 'authors') {
            $remainder = implode(' ', $remainingWords);
        }
        $warnings = [];
        $skip = false;

        // if author list is in \textsc, remove the \textsc
        if (isset($words[0]) && Str::startsWith($words[0], '\textsc{')) {
            $words[0] = substr($words[0], 8);
        }

        $wordHasComma = $prevWordHasComma = false;

        /////////////////////////////////
        // Check for organization name //
        /////////////////////////////////
        /*
         * If first 3-6 words are all letters and in the dictionary except possibly last one, which is letters with a period at the end
         * then they make up the name of an organization
         * (Dictionary check is to exclude strings like 'John Doe and Jane Doe' or 'Doe J and Doe K', which needs processing
         * as names, to insert commas after the last names.  A word that is not in the dictionary and could not be part
         * of a name, like 'American', is also possible.)
         */
        $this->verbose('convertToAuthors: Checking for name of organization');
        $name = '';
        foreach ($words as $i => $word) {
            if ($this->isInitials($word)) {
                break;
            } elseif ($i == 0 && strlen($word) > 3 && substr($word, -1) == '.') {
                $remainder = implode(' ', array_slice($words, 1));
                $year = $this->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                return [
                    'authorstring' => substr($word, 0, -1),
                    'warnings' => [],
                    'organization' => true,
                ];
            } elseif (ctype_alpha((string) $word) && ($this->inDict($word) || in_array($word, ['American']))) {
                $name .= ($i ? ' ' : '') . $word;
            } else {
                $xword = substr($word, 0, -1);
                // possibly last character could be other punctuation?
                if (ctype_alpha((string) $xword) && $this->inDict($xword) && in_array(substr($word, -1), ['.'])) {
                    if ($i >= 2 && $i <= 5) {
                        $remainder = implode(' ', array_slice($words, $i+1));
                        $year = $this->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                        return [
                            'authorstring' => $name . ' ' . $xword,
                            'warnings' => [],
                            'organization' => true,
                        ];
                    } else {
                        break;
                    }
                } elseif ($i >= 3 && $i <= 6) {
                    $remainder = implode(' ', array_slice($words, $i));
                    $year = $this->getDate($remainder, $remainder, $month, $day, $date, true, true, true, $language);
                    return [
                        'authorstring' => $name,
                        'warnings' => [],
                        'organization' => true,
                    ];
                } else {
                    break;
                }        
            }
        }

        $this->verbose('convertToAuthors: Organization name not found.');

        ////////////////////////////////////
        // Check for some common patterns //
        ////////////////////////////////////
        /*
         * Pattern
         * name1 end1 (name2 end1)* name2 end2 name2 end3
         * is matched.  (Notice that first name can have different format from following names, to accommodate author strings like
         * Smith, A., B. Jones, and C. Gonzalez.)
         * 'initials' => true means treat string of u.c. letters as initials if length is at most 4 (rather than default 2)
         */
        $vonNameRegExp = '(';
        foreach ($this->vonNames as $i => $vonName) {
            $vonNameRegExp .= ($i ? '|' : '') . $vonName;
        }
        $vonNameRegExp .= ')';

        // last name has to start with uppercase letter
        $lastNameRegExp = '(' . $vonNameRegExp . ' )?\p{Lu}[\p{L}\-\']+';
        // other name has to start with uppercase letter and include at least one lowercase letter
        $otherNameRegExp = '(?=[^ ]*\p{Ll})\p{Lu}[\p{L}\-\']+';
        $andRegExp = '(and|&|\\\&|et|y)';
        $initialRegExp = '(\p{Lu}\.?|\p{Lu}\.-\p{Lu}\.)';
        
        $authorRegExps = [
            // 0. Smith AB[:\.] [must be at least two initials, otherwise could be start of name --- e.g. Smith A. Jones]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{2,3}',
                'end1' => '(: |\. (?!' . $andRegExp . '))',
                'end2' => null,
                'end3' => null,
                'initials' => true
            ],
            // 1. Smith A. et al.
            [
                'name1' => $lastNameRegExp . ' \p{Lu}',
                'end1' => '\. et.? al.?',
                'end2' => null,
                'end3' => null,
                'initials' => false,
                'etal' => true,
            ],
            // 2. Smith AB, Jones CD[:\.]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}',
                'end1' => ', ', 
                'end2' => '(: |\. (?!' . $andRegExp . '))', 
                'end3' => null, 
                'initials' => true
            ],
            // 3. Smith AB, Jones CD, Gonzalez JD[:\.]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ', (?!' . $andRegExp . ')', 
                'end3' => '[:\.] ', 
                'initials' => true
            ],
            // 4. Smith AB and Gonzalez JD[:\.,]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ' ' . $andRegExp . ' ', 
                'end2' => '[:\.,]', 
                'end3' => null, 
                'initials' => true
            ],
            // 5. Smith AB, Jones CD and Gonzalez JD[:\.]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ' ' . $andRegExp . ' ', 
                'end3' => '[:\.]', 
                'initials' => true
            ],
            // 6. Smith AB, Jones CD, Gonzalez JD, et al.
            // [Whole regexp will be matched, but when components are matched sequentially, third component will not be 
            // reached, because matches of first component are enough. So 'et al.' will not be included in match, and is added afterwards.]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ', (?!' . $andRegExp . ')', 
                'end3' => ', et\.? al\.?', 
                'initials' => true
            ],
            // 7. Smith, A. B.[,;] Jones, C. D.[,;] Gonzalez, J. D.,
            [
                'name1' => $lastNameRegExp . ',( ' . $initialRegExp . '){1,3}', 
                'end1' => '[;,] ', 
                'end2' => '; (?!' . $andRegExp . ')', 
                'end3' => ',', 
                'initials' => false
            ],
            // 8. Smith,? A. B., Jones,? C. D., and Gonzalez,? J. D. 
            [
                'name1' => $lastNameRegExp . ',?( ' . $initialRegExp . '){1,3}', 
                'end1' => ', (?!' . $andRegExp . ')', 
                'end2' => ', ' . $andRegExp . ' ', 
                'end3' => '[,\. ]', 
                'initials' => false
            ],
            // 9. A. B. Smith[.:] 
            [
                'name1' => '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp, 
                'end1' => '[\.:] ', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false
            ],
            // 10. A. B. Smith et.? al.? 
            [
                'name1' => '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp, 
                'end1' => ' et\.? al\.?', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false,
                'etal' => true,
            ],
            // 11. A. B. Smith and C. D. Jones[\., ]
            [
                'name1' => '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp, 
                'end1' => ',? ' . $andRegExp . ' ', 
                'end2' => '[\., ]', 
                'end3' => null, 
                'initials' => false
            ],
            // 12. A. B. Smith, C. D. Jones and J. D. Gonzalez[\., ]
            [
                'name1' => '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp, 
                'end1' => ', (?!' . $andRegExp . ')', 
                'end2' => ',? ' . $andRegExp . ' ', 
                'end3' => '[\., ]', 
                'initials' => false
            ],
            // 13. Smith, A. B., C. D. Jones,? and J. D. Gonzalez[\., ]
            [
                'name1' => $lastNameRegExp . ',( ' . $initialRegExp . '){1,3}', 
                'name2' => '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp, 
                'end1' => ', (?!' . $andRegExp . ')', 
                'end2' => ',? ' . $andRegExp . ' ', 
                'end3' => '(\. |, | \()', // can't be simply space, because then if last author has two-word last name, only first is included
                'initials' => false
            ],
            // 14. Smith, Jane( J)?.
            [
                'name1' => $lastNameRegExp . ', ' . $otherNameRegExp . '( \p{Lu})?', 
                'end1' => '\. (?!' . $andRegExp . ')', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false
            ],
            // 15. Smith J.:
            [
                'name1' => $lastNameRegExp . ' ' . $initialRegExp, 
                'end1' => ': ', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false
            ],
            // 16. Jane A. Smith.
            [
                'name1' => $otherNameRegExp . ' (\p{Lu}\.? )?' . $lastNameRegExp, 
                'end1' => '\. ', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false
            ],
            // 17. Smith, J. A. <followed by>[\(|\[|``|"]
            [
                'name1' => $lastNameRegExp . ',( ' . $initialRegExp . '){1,3}', 
                'end1' => ' (?=(\(|\[|``|"))', 
                'end2' => null, 
                'end3' => null, 
                'initials' => false
            ],
            // 18. Smith, J. A., Jones, A. B. <followed by>[\(|\[|``|"]
            [
                'name1' => $lastNameRegExp . ',( ' . $initialRegExp . '){1,3}', 
                'end1' => ', ', 
                'end2' => ' (?=(\(|\[|``|"))', 
                'end3' => null, 
                'initials' => false
            ],
            // 19. Smith, J\.? and Jones, A[:\.,]
            [
                'name1' => $lastNameRegExp . ',? (' . $initialRegExp . ')( ' . $initialRegExp . ')?', 
                'end1' => ',? ' . $andRegExp . ' ', 
                'end2' => '[:\.,]? ', 
                'end3' => null, 
                'initials' => false
            ],
            // 20. Smith, Jane( J\.?)? and Susan( K\.?)? Jones[,.] 
            [
                'name1' => $lastNameRegExp . ', ' . $otherNameRegExp . '( ' . $initialRegExp . ')?',
                'name2' => $otherNameRegExp . ' (' . $initialRegExp . ' )?' . $lastNameRegExp,
                'end1' => ' ' . $andRegExp . ' ',
                'end2' => '[\.,] ',
                'end3' => null,
                'initials' => false],
            // 21. Jane Smith, Susan Jones, Hilda Gonzalez. 
            [
                'name1' => $otherNameRegExp . ' ' . $lastNameRegExp, 
                'end1' => ', (?!' . $andRegExp . ')', 
                'end2' => ', (?!' . $andRegExp . ')', 
                'end3' => '\. ', 
                'initials' => false
            ],
        ];

        $authorstring = '';
        foreach ($authorRegExps as $i => $r) {
            $name1 = $r['name1'];
            $name2 = $r['name2'] ?? $name1;

            $regExp = '%^' . $name1 . $r['end1'] . '(' . $name2 . $r['end1'] . ')*';
            if ($r['end2']) {
                $regExp .= $name2 . $r['end2'];
            }
            if ($r['end3']) {
                $regExp .= $name2 . $r['end3'];
            }
            $regExp .= '%u';

            if (preg_match($regExp, $remainder)) {
                $result = preg_match('%^(?P<author>'. $name1 . ')' . $r['end1'] . '(?P<remainder>.*)$%u', $remainder, $matches);
                if (isset($matches['author'])) {
                    $authorstring .= $this->formatAuthor($matches['author'], $r['initials']);
                    $remainder = $matches['remainder'];
                }
                $result = 1;
                while ($result == 1) {
                    $result = preg_match('%^(?P<author>'. $name2 . ')' . $r['end1'] . '(?P<remainder>.*)$%u', $remainder, $matches);
                    if (isset($matches['author'])) {
                        $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($matches['author'], $r['initials']);
                        $remainder = $matches['remainder'];
                    }
                }
                if ($r['end2'] && preg_match('%^(?P<author>'. $name2 . ')' . $r['end2'] . '(?P<remainder>.*)$%u', $remainder, $matches)) {
                    if (isset($matches['author'])) {
                        $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($matches['author'], $r['initials']);
                        $remainder = $matches['remainder'];
                    }
                }
                if ($r['end3'] && preg_match('%^(?P<author>'. $name2 . ')' . $r['end3'] . '(?P<remainder>.*)$%u', $remainder, $matches)) {
                    if (isset($matches['author'])) {
                        $authorstring .= ($authorstring ? ' and ' : '') . $this->formatAuthor($matches['author'], $r['initials']);
                        $remainder = $matches['remainder'];
                    }
                }
                if (preg_match('%^et.? al.?(?P<remainder>.*)$%', $remainder, $matches)) {
                    $authorstring .= ' and others';
                    $remainder = $matches['remainder'];
                } elseif (! empty($r['etal'])) {
                    $authorstring .= ' and others';
                }
                $this->verbose('Authors match pattern ' . $i);
                break;
            }
        }
        
        if ($authorstring) {
            $year = $this->getDate(trim($remainder), $remainder, $month, $day, $date, true, true, true, $language);
            if (preg_match('%^(?P<firstWord>[^ ]+) (?P<remains>.*)$%', $remainder, $matches)) {
                if ($this->isEd($matches['firstWord'])) {
                    $isEditor = true;
                    $remainder = $matches['remains'];
                    if ($year = $this->getDate($remainder, $remains, $month, $day, $date, true, true, true, $language)) {
                        $remainder = $remains;
                    }
                }
            }
            return [
                'authorstring' => $authorstring,
                'warnings' => [],
                'organization' => false,
                'author-pattern' => $i,
            ];
        }

        ///////////////////////////////////////////////////////////
        // If no pattern matches, go through string word by word //
        ///////////////////////////////////////////////////////////

        foreach ($words as $i => $word) {
            if ($skip) {
                $skip = false;
                continue;
            }

            $wordEndsName = false;
            if (substr($word, -1) == ';') {
                $word = substr($word, 0, -1) . ',';
                $wordEndsName = true;
            }

            // Word is in vonNames or it is all uppercase and lowercased version of it is a lowercased vonName
            $wordIsVon = in_array($word, $this->vonNames)
                 || (preg_match('/^[A-Z]*$/', $word) && in_array(strtolower($word), array_map('strtolower', $this->vonNames)));

            // If word is all uppercase, with no trailing punctuation, and next word is not all uppercase,
            // and word and is not a von name and is not "and"
            // then add a comma at the end
            // The idea is to interpret SMITH John to be SMITH, John.
            if (
                strlen($word) > 2 &&
                ! $wordIsVon &&
                preg_match('/^[A-Z]*$/', $word) &&
                isset($words[$i+1]) &&
                mb_strtoupper($words[$i+1]) != $words[$i+1] &&
                ! $this->isAnd(strtolower($word), $language)
               ) {
                $word = $word . ',';
            }

            $nameComplete = true;
            $prevWordHasComma = $wordHasComma;
            $wordHasComma = substr($word, -1) == ',';
            // Get letters in word, eliminating accents & other non-letters, to get accurate length
            $lettersOnlyWord = preg_replace("/[^A-Za-z]/", '', $word);

            // Deal with specific cases of no space at end of authors.  Note that case Smith~(1980) is
            // already handled.
            // These cases are very specific --- how to generalize?  
            // Smith},``Title... 
            if (Str::contains($word, ['},`'])) {
                $firstWord = strtok($word, '}');
                $remain = $remainingWords;
                array_shift($remain);
                $remainingWords = array_merge([$firstWord, ltrim(Str::after($word, '}'), ',.')], $remain);
                $word = $firstWord;
            // Smith(2001)
            } elseif (preg_match('/[a-zA-Z]\([19|20]/', $word)) {
                $firstWord = strtok($word, '(');
                $remain = $remainingWords;
                array_shift($remain);
                $remainingWords = array_merge([$firstWord, '(' . Str::after($word, '(')], $remain);
                $word = $firstWord;
            }

            if ($case == 12 
                    && ! in_array($word, ['Jr.', 'Jr.,', 'Sr.', 'Sr.,', 'III', 'III,']) 
                    // Deal with names like Bruine de Bruin, W.
                    && isset($words[$i+1]) // must be at least 2 words left
                    && ! $this->isAnd($words[$i+1], $language) // next word is not 'and'
                )  {
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
            }

            if (isset($bareWords)) {
                $this->verbose("bareWords: " . implode(' ', $bareWords));
            }
            unset($bareWords);

            if (isset($reason)) {
                $this->verbose('Reason: ' . $reason);
                unset($reason);
            }

            if (in_array($case, [11, 12, 14]) && $done) {  // 14: et al.
                break;
            }

            if (! $done) {
                $this->verbose(['text' => 'Word ' . $i . ": ", 'words' => [$word], 'content' => " - authorIndex: " . $authorIndex . ", namePart: " . $namePart]);
                $this->verbose("fullName: " . $fullName);
            }

            if (isset($itemYear)) {
                $year = $itemYear;
            }
            if ($year) {
                $this->verbose("Year: " . $year);
            }

            // Remove first word from $remainingWords
            array_shift($remainingWords);

            if ($authorIndex > 0) {
                $multipleAuthors = true;
            }

            $edResult = $this->isEd($word);

            $nextWord = isset($words[$i+1]) ? rtrim($words[$i+1], ',;') : null;

            // Case of initials A. -B. (with space).
            if ($nextWord && preg_match('/^[A-Z]\./', $word) && preg_match('/^-[A-Z]\./', $nextWord)) {
                $word = $word . $nextWord;
                array_shift($remainingWords);
                $skip = true;
            }

            if (in_array($word, [" ", "{\\sc", "\\sc"])) {
                //
            } elseif (in_array($word, ['...', '…'])) {
                $this->verbose('[convertToAuthors 1]');
                $formattedAuthor = $this->formatAuthor(($fullName));
                if (Str::endsWith($authorstring, $formattedAuthor)) {
                    $this->addToAuthorString(3, $authorstring, ' and others');
                } else {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                }
                $fullName = '';
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
                $remainder = implode(" ", $remainingWords);
                $done = false;
            } elseif ($edResult) {
                $this->verbose('[convertToAuthors 2]');
                if ($edResult == 1 && $multipleAuthors) {
                   $warnings[] = "More than one editor has been identified, but string denoting editors (\"" . $word . "\") is singular";
                }
                $this->verbose("String for editors, so ending name string (word: " . $word .")");
                // Word is 'ed' or 'ed.' if $hasAnd is false, or 'eds' or 'eds.' if $hasAnd is true
                $isEditor = true;
                $remainder = implode(" ", $remainingWords);
                if ($namePart == 0) {
                    $warnings[] = "String for editors detected after only one part of name.";
                }
                // Check for year following editors
                if ($year = $this->getDate($remainder, $remains, $trash1, $trash2, $trash3, true, false, true, $language)) {
                    $remainder = $remains;
                    $this->verbose("Year detected, so ending name string (word: " . $word .")");
                } else {
                    $this->verbose("String indicating editors (e.g. 'eds') detected, so ending name string");
                }
                // Under some conditions (von name?), $fullName has already been added to $authorstring.
                if (!Str::endsWith($authorstring, $fullName)) {
                    $this->addToAuthorString(1, $authorstring, $this->formatAuthor($fullName));
                }
                break;  // exit from foreach
            } elseif ($determineEnd && $done) {
                break;  // exit from foreach
            } elseif ($this->isAnd($word, $language) && ($word != 'et' || ! in_array($nextWord, ['al..', 'al.', 'al', 'al.:']))) {
                $this->verbose('[convertToAuthors 3]');
                // Word is 'and' or equivalent, and if it is "et" it is not followed by "al".
                $hasAnd = $prevWordAnd = true;
                $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName) . ' and');
                $fullName = '';
                $namePart = 0;
                $this->verbose("\$namePart set to 0");
                $authorIndex++;
                $reason = 'Word is "and" or equivalent';
            } elseif (in_array($word, ['et', 'et.'])) {
                // Word is 'et'
                $this->verbose('nextWord: ' . $nextWord);
                if (in_array($nextWord, ['al..', 'al.', 'al', 'al.:'])) {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                    array_shift($remainingWords);
                    if (count($remainingWords)) {
                        $remainder = implode(" ", $remainingWords);
                        $this->verbose('[c2a getDate 2]');
                        if (preg_match('/^(18|19|20)[0-9]{2}$/', $remainingWords[0])) {
                            // If first remaining word is a year **with no punctuation**, assume it starts title
                        } else {
                            $year = $this->getDate($remainder, $remains, $trash1, $trash2, $trash3, true, true, true, $language);
                            $remainder = trim($remains);
                        }
                    }
                    $done = true;
                    $case = 14;
                    $reason = 'Word is "et" and next word is "al." or "al"';
                }
            } elseif ($type == 'editors' && isset($word[0]) && $word[0] == '(' && $remainder && $this->isAddressPublisher($remainder, finish: false)) {
                $this->addToAuthorString(17, $authorstring, $this->formatAuthor($fullName));
                $done = true;
            } elseif ($determineEnd && substr($word, -1) == ':') {
                if (
                        $namePart >= 2 
                        && isset($words[$i-1]) 
                        && in_array(substr($words[$i-1], -1), ['.', ',', ';']) 
                        && (! $this->isInitials($words[$i-1]) || (isset($words[$i-2]) && in_array(substr($words[$i-2], -1), [',', ';'])))
                    ) {
                    $this->verbose('Word ends in colon, $namePart is at least 2, and previous word ends in comma or period, and either previous word is not initials or word before it ends in comma, so assuming word is first word of title.');
                    $this->addToAuthorString(16, $authorstring, $this->formatAuthor($fullName));
                    $remainder = $word . ' ' . implode(" ", $remainingWords);
                    $done = true;
                } elseif (
                        $type == 'editors'
                        && isset($words[$i-1])
                        && in_array(substr($words[$i-1], -1), ['.', ',', ';'])
                    ) { 
                    $this->verbose('Looking for editor, word ends in colon, and previous word ends in comma or period, so assuming word is start of publication info (address of publisher).');
                    $this->addToAuthorString(16, $authorstring, $this->formatAuthor($fullName));
                    $remainder = $word . ' ' . implode(" ", $remainingWords);
                    $done = true;
                } else {
                    $this->verbose('[convertToAuthors 5]');
                    $nameComponent = $word;
                    $fullName .= ' '. trim($nameComponent, '.');
                    $this->addToAuthorString(17, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $reason = 'Word ends in colon';
                    $done = true;
                }
            } elseif ($determineEnd && substr($word, -1) == '.' && strlen($lettersOnlyWord) > 3
                    && mb_strtolower(substr($word, -2, 1)) == substr($word, -2, 1)) {
                // If $determineEnd and word ends in period and word has > 3 chars (hence not "St.") and previous letter
                // is lowercase (hence not string of initials without spaces):
                if ($namePart == 0) {
                    // If $namePart == 0, something is wrong (need to choose an earlier end for name string) UNLESS author has
                    // a single name.  If string is followed by year or quotedOrItalic, that seems right,
                    // in which case we may have an entry like "Economist. 2005. ..."
                    $remainder = implode(" ", $remainingWords);
                    $this->verbose('[c2a getDate 3]');
                    if ($year = $this->getDate($remainder, $remainder, $trash1, $trash2, $trash3, true, false, true, $language)) {
                        $this->verbose('[convertToAuthors 6]');
                        // Don't use spaceOutInitials in this case, because string is not initials.  Could be
                        // something like autdiogames.net, in which case don't want to put space before period.
                        $nameComponent = $word;
                        $fullName .= trim($nameComponent, '.');
                        if (isset($fullName[0]) && $fullName[0] != ' ') {
                            $fullName = ' ' . $fullName;
                        }
                        $this->addToAuthorString(4, $authorstring, $fullName);
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string starts with year';
                        $itemYear = $year; // because $year is recalculated below
                        $done = true;
                    } elseif ($this->getQuotedOrItalic($remainder, true, false, $before, $after, $style)) {
                        $this->verbose('[convertToAuthors 7]');
                        $nameComponent = $word;
                        $fullName .= ($fullName ? ' and ' : '') . trim($nameComponent, '.');
                        $this->addToAuthorString(18, $authorstring, $fullName);
                        $reason = 'Word ends in period, namePart is 0, and remaining string starts with quoted or italic';
                        $done = true;
                    } else {
                        $this->verbose('[convertToAuthors 8]');
                        // Case like: "Arrow, K. J., Hurwicz. L., and ..." [note period at end of Hurwicz]
                        // "Hurwicz" is current word, which is added back to the remainder.
                        // (Ideally the script should realize the typo and still get the rest of the authors.)
                        // If remainder starts with quotes or italic, assume author has just one name
                        $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo?  Processed with comma instead of period.";
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string does not start with year';
                        $word = substr($word, 0, -1) . ',';
                        goto namePart0;
                    }
                } elseif (! isset($remainingWords[0]) || ! $this->isInitials(trim($remainingWords[0], ', '))) {
                    // If $namePart > 0
                    $this->verbose('[convertToAuthors 9]');
                    $nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, '.')));
                    $fullName .= " " . $nameComponent;
                    $this->addToAuthorString(5, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, and namePart is > 0';
                } elseif (
                        isset($remainingWords[0])
                        && isset($remainingWords[1])
                        && (
                            (substr(trim($remainingWords[0], ', '), -1) == '.' && $this->isInitials(trim($remainingWords[0], ', ')))
                            ||
                             $this->isAnd($remainingWords[0])
                           )
                        ) {
                    // case like "Richerson, Peter. J., and ..."
                    $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo?  Period was ignored.";
                    $word = substr($word, 0, -1);
                    goto namePart0;
                } else {
                    // period at end of list of authors
                    $this->verbose('[convertToAuthors 7a]');
                    $nameComponent = $word;
                    $fullName .= " " . trim($nameComponent, '.');
                    $this->addToAuthorString(18, $authorstring, $fullName);
                    $reason = 'Word ends in period, namePart is 1, and next word is not initials or "and", so ending author list.';
                    $done = true;
                    $replaced = false;
                    foreach ($this->andWords as $andWord) {
                        if (Str::startsWith($remainder, $andWord . ' ')) {
                            // can't make replacement based on $fullName, because case might differ from case in $remainder
                            // (e.g. last name all-uppercase vs. formatted with uppercase first letter and rest lowercase)
                            $remainder = substr($remainder, strlen($andWord) + 1 + strlen(trim($fullName)));
                            $replaced = true;
                            break;
                        }
                    }
                    if (! $replaced) {
                        //$remainder = Str::replaceStart(trim($fullName), '', $remainder);
                        $remainder = substr($remainder, strlen(trim($fullName)));
                    }
                }
                $this->verbose("Remainder: " . $remainder);
                $this->verbose('[c2a getDate 4]');
                $this->verbose('[convertToAuthors 10]');
                if (! isset($year) || ! $year) {
                    if ($year = $this->getDate($remainder, $remains, $month, $day, $trash3, true, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Remains: " . $remains);
                    }
                }
                $done = true;
            } elseif (
                // next word starts with uppercase letter and next word but one starts with lowercase letter, so cannot start title.
                // So $words[$i+1] is first word of title.  So terminate name string now.
                $type == 'authors'
                && $this->isInitials($word)
                && isset($words[$i+1])
                && ! in_array($words[$i+1], ['et', 'et.', 'al', 'al.'])
                && isset($words[$i+2])
                && isset($words[$i+2][0])
                && ! preg_match('/^eds?/', $words[$i+2])
                && preg_match('/[A-Z]/', $words[$i+1][0])
                && preg_match('/[a-z]/', $words[$i+2][0])
                && (
                    ! $this->isAnd($words[$i+2], $language)
                    ||
                    (isset($words[$i+3][0]) && preg_match('/[a-z]/', $words[$i+3][0]) && ! in_array($words[$i+3], $this->vonNames))
                   )
                && (
                    ! in_array($words[$i+2], $this->vonNames)
                    ||
                    (isset($words[$i+3][0]) && preg_match('/[a-z]/', $words[$i+3][0]) && ! in_array($words[$i+3], $this->vonNames))
                   )
                && ! in_array($words[$i+2], ['et', 'et.', 'al', 'al.'])
                && (! isset($words[$i+3]) || ! preg_match('/^[\(\[]?(19|20)[0-9]{2}[\)\]]?$/', trim($words[$i+3], '.')))
                ) {
                $this->verbose('[convertToAuthors 14a]');
                $fullName .= ' ' . $word;
                $remainder = implode(" ", $remainingWords);
                $done = true;
                $this->addToAuthorString(29, $authorstring, $this->formatAuthor($fullName));
            } elseif ($namePart == 0) {
                namePart0:
                if (isset($words[$i+1]) && $this->isAnd($words[$i+1], $language)) {
                    $this->verbose('[convertToAuthors 11]');
                    // Next word is 'and' or equivalent.  Happens if $namePart is 0 because of von name.  Seems impossible to tell
                    // whether in "Werner de la ..." the "Werner" is the first name or part of the last name, to be followed by
                    // a first name later (without looking ahead --- attempted in convertToAuthors 6a below).
                    // On encounting a von name, namepart is not incremented, which is
                    // wrong if Werner is a first name.
                    $hasAnd = $prevWordAnd = true;
                    $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName . ' ' . $word));
                    $fullName = '';
                    $namePart = 0;
                    $this->verbose("\$namePart set to 0");
                    $authorIndex++;
                    $reason = 'Word is "and" or equivalent';
                // Check if $word and first word of $remainingWords are plausibly a name.  If not, end search if $determineEnd.
                } elseif ($determineEnd && isset($remainingWords[0]) && $this->isNotName($word, $remainingWords[0])) {
                    $this->verbose('[convertToAuthors 12]');
                    $fullName .= ' ' . $word;
                    $remainder = implode(" ", $remainingWords);
                    $this->addToAuthorString(6, $authorstring, ' ' . ltrim($this->formatAuthor($fullName)));
                    $origRemainder = $remainder;
                    $origRemainingWords = $remainingWords;
                    if ($type == 'authors' && $this->isEd($remainingWords[0])) {
                        $isEditor = true;
                        array_shift($remainingWords);
                        $remainder = implode(" ", $remainingWords);
                        $this->verbose("Editors detected");
                    }
                    if ($year = $this->getDate($remainder, $remains, $month, $day, $date, true, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Year detected");
                    }
                    if (! $year) {
                        // eds will be detected on next round
                        $remainder = $origRemainder;
                        $remainingWords = $origRemainingWords;
                    }
                    $done = true;
                } elseif ($this->isInitials($word) && isset($words[$i+1]) && 
                        (
                            ($this->isInitials($words[$i+1]) && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language))
                            ||
                            (in_array(substr($words[$i+1],-1), [',', ';']) && $this->isInitials(substr($words[$i+1],0,-1)))
                        )
                    ) {
                    $this->verbose('[convertToAuthors 13]');
                    $fullName .= ' ' . $word;
                // $nextWord not initials, to rule out case like GUO, J., where GUO gets classified as initials because it is all
                // uppercase and has only 3 letters.  Logically could make condition $namePart > 0, but $namePart is sometimes set to
                // 0 in the middle of a name.
                } elseif (substr($word,-1) == ',' && $this->isInitials(substr($word,0,-1)) && ! $this->isInitials(substr($nextWord, 0, -1))) {
                    $this->verbose('[convertToAuthors 14]');
                    $fullName .= ' ' . substr($word, 0, -1);
                    $namePart = 0;
                    $this->verbose("\$namePart set to 0");
                    $authorIndex++;
                } elseif (
                    // $word is publication city and either it ends in a colon or next word is two-letter state abbreviation followed by colon
                    $type == 'editors'
                    && in_array(trim($word, ',: '), $this->cities)
                    && 
                        (
                            substr($word, -1) == ':'
                            ||
                            (
                                isset($words[$i+1])
                                && preg_match('/^[A-Z]{2}:$/', $words[$i+1])
                            )
                        )
                ) {
                    $done = true;
                    $this->addToAuthorString(28, $authorstring, $this->formatAuthor($fullName));
                } else {
                    $this->verbose('[convertToAuthors 15]');
                    if (! $prevWordAnd && $authorIndex) {
                        $this->addToAuthorString(7, $authorstring, $this->formatAuthor($fullName) . ' and');
                        $fullName = '';
                        $prevWordAnd = true;
                    }
                    $name = $this->spaceOutInitials($word);
                    // If part of name is all uppercase and 3 or more letters long, convert it to ucfirst(mb_strtolower())
                    // For component with 1 or 2 letters, assume it's initials and leave it uc (to be processed by formatAuthor)
                    if (strlen($name) > 2 && strtoupper($name) == $name && strpos($name, '.') === false) {
                        $nameComponent = ucfirst(mb_strtolower($name));
                        // Simpler version of following code, without check for hyphen, produces strange result ---
                        // the *next* word has a period replaced by a comma
                        if (str_contains($nameComponent, '-')) {
                            $nameChars = mb_str_split($nameComponent);
                            $nameComponent = '';
                            foreach ($nameChars as $i => $char) {
                                $nameComponent .= ($i > 0 && $nameChars[$i-1] == '-') ? mb_strtoupper($char) : $char;
                            }
                        }
                    } else {
                        $nameComponent = $name;
                    }
                    $oldFullName = $fullName;
                    $fullName .= ' ' . $nameComponent;
                    if ($wordIsVon) {
                        $this->verbose('[convertToAuthors 16]');
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name");
                        if ($oldFullName && ! $prevWordVon) {
                            $this->verbose("convertToAuthors: incrementing \$namePart");
                            $namePart++;
                        }
                        $prevWordVon = true;
                    } else {
                        $prevWordVon = false;
                        if (isset($words[$i]) && ! Str::endsWith($words[$i], ',') 
                                && isset($words[$i+1]) 
                                && Str::endsWith($words[$i+1], [',', ';']) 
                                && ! $this->isInitials(substr($words[$i+1], 0, -1)) 
                                && isset($words[$i+2]) 
                                && Str::endsWith($words[$i+2], [',', ';']) 
                                && ! $this->isYear(trim($words[$i+2], ',()[]'))
                                //&& ! $this->isYearRange(trim($words[$i+2], ',()[]'))
                            ) {
                            // $words[$i] does not end in a comma AND $words[$i+1] is set and ends in a comma and is not initials AND $words[$i+2]
                            // is set and ends in a comma AND $words[$i+2] is not a year.
                            // E.g. Ait Messaoudene, N., ...
                            $this->verbose('[convertToAuthors 17]');
                            $this->verbose("convertToAuthors: '" . $words[$i] . "' identified as first segment of last name, with '" . $words[$i+1] . "' as next segment");
                        } elseif (isset($words[$i+1]) && ! in_array($words[$i+1], $this->vonNames)) {
                            $this->verbose('[convertToAuthors 18]');
                            $this->verbose("convertToAuthors: incrementing \$namePart");
                            $namePart++;
                        }
                        // Following occurs in case of name that is a single string, like "IMF"
                        if ($year = $this->getDate(implode(" ", $remainingWords), $remains, $trash1, $trash2, $trash3, true, true, true, $language)) {
                            $this->verbose('[c2a getDate 6]');
                            $this->verbose('[convertToAuthors 19]');
                            $remainder = $remains;
                            $done = true;
                        }
                        $case = 6;
                    }
                }
            } else {
                // namePart > 0 and word doesn't end in some character, then lowercase letter, then period
                $prevWordAnd = false;

                // 2023.8.2: trimRightBrace removed to deal with conversion of example containing name Oblo{\v z}insk{\' y}
                // However, it must have been included here for a reason, so probably it should be included under
                // some conditions.
                if (in_array(rtrim($word, '.,'), $this->nameSuffixes)) {
                    $this->verbose('[convertToAuthors 20]');
                    $fullName = $this->formatAuthor($fullName);
                    $nWords = explode(' ', trim($fullName, ' '));
                    $nameWords = [];
                    foreach ($nWords as $nWord) {
                        $nameWords[] = Str::endsWith($nWord, [',,']) ? substr($nWord, 0, -1): $nWord;
                    }
                    if (count($nameWords) == 1) {
                        $this->verbose('[convertToAuthors 21]');
                        $fullName = $nameWords[0] . ' ' . $word;
                        $nameComplete = false;
                    } else {
                        // Put Jr. or Sr. in right place for BibTeX: format is lastName, Jr., firstName OR lastName Jr., firstName.
                        // Assume last name is single word that is followed by a comma (which covers both
                        // firstName lastName, Jr. and lastName, firstName, Jr.
                        $this->verbose('[convertToAuthors 22]');
                        $fullName = ' ';
                        // Put Jr. after the last name
                        $k = -1;
                        foreach ($nameWords as $j => $nameWord) {
                            if (substr($nameWord, -1) == ',') {
                                $fullName .= $nameWord . ' ' . rtrim($word, ',') . ',';
                                $k = $j;
                                break;
                            }
                        }
                        if ($k >= 0) {
                            // Put the rest of the names after Jr.
                            foreach ($nameWords as $m => $nameWord) {
                                if ($m != $k) {
                                    $fullName .= ' ' . $nameWord;
                                }
                            }
                        }

                        // No comma at end of any of $nameWords
                        if ($k == -1) {
                            $n = count($nameWords);
                            $fullName .= $nameWords[$n-1] . ' '. rtrim($word, ',') . ',';
                            foreach ($nameWords as $i => $nameWord) {
                                if ($i < $n - 1) {
                                    $fullName .= ' ' . $nameWord;
                                }
                            }
                        }

                        $namePart = 0;
                        $this->verbose("\$namePart set to 0");
                        $authorIndex++;
                    }
                    $this->verbose('Name with Jr., Sr., or III; fullName: ' . $fullName);
                } else {
                    $this->verbose('[convertToAuthors 23]');
                    // Don't rtrim '}' because it could be part of the name: e.g. Oblo{\v z}insk{\' y}.
                    // Don't trim comma from word before Jr. etc, because that is valuable info
                    $trimmedWord = (isset($words[$i+1]) && in_array(rtrim($words[$i+1], '.,)'), $this->nameSuffixes)) ? $word : rtrim($word, ',;');
                    $nameComponent = $this->spaceOutInitials($trimmedWord);
                    $nameComponent = preg_replace('/([A-Za-z][a-z])\.$/', '$1', $nameComponent);
                    $fullName .= " " . $nameComponent;
                }

                // $bareWords is array of words at start of $remainingWords that don't end end in ','
                // or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
                $bareWordsResult = $this->bareWords($remainingWords, false, $language);
                $bareWords = $bareWordsResult['barewords'];
                $wordAfterBareWords = $bareWordsResult['nextword'];
                // If 'and' has not already occurred ($hasAnd is false), its occurrence in $barewords is compatible
                // with $barewords being part of the authors' names OR being part of the title, so should be ignored.
                $nameScore = $this->nameScore($bareWords, ! $hasAnd);
                $this->verbose("bareWords (no trailing punct, not year in parens): " . implode(' ', $bareWords));
                $this->verbose("nameScore: " . $nameScore['score'] . ". Count: " . $nameScore['count']);
                if ($nameScore['count']) {
                    $this->verbose('[convertToAuthors 24]');
                    $this->verbose('nameScore per word: ' . number_format($nameScore['score'] / $nameScore['count'], 2));
                }

                $wordsRemainingAfterNext = $remainingWords;
                array_shift($wordsRemainingAfterNext);
                $upcomingQuotedText = $this->getQuotedOrItalic(implode(" ", $wordsRemainingAfterNext), true, false, $before, $after, $style);

                if ($determineEnd && $text = $this->getQuotedOrItalic(implode(" ", $remainingWords), true, false, $before, $after, $style)) {
                    if (in_array($text, ['et al', 'et al.', 'et. al.', 'et al.:'])) {
                        $this->verbose('[convertToAuthors 25]');
                        $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                        if (isset($remainingWords[0]) && $remainingWords[0] == '{\em') {
                            array_shift($remainingWords);
                        }
                        array_shift($remainingWords);
                        array_shift($remainingWords);
                        $remainder = implode(" ", $remainingWords);
                        $done = true;
                        $case = 19;
                    } else {
                        $this->verbose('[convertToAuthors 26a]');
                        $remainder = implode(" ", $remainingWords);
                        $done = true;
                        $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                        $case = 7;
                    }
                } elseif ($determineEnd && $year = $this->getDate(implode(" ", $remainingWords), $remainder, $month, $day, $date, true, true, true, $language)) {
                    $this->verbose('[convertToAuthors 14a] Ending author string: word is "'. $word . '", year is next.');
                    $done = true;
                    $fullName = ($fullName[0] != ' ' ? ' ' : '') . $fullName;
                    $this->addToAuthorString(10, $authorstring, $this->formatAuthor($fullName));
                } elseif ($determineEnd && $upcomingQuotedText && $upcomingQuotedText != 'et al.') {
                    $this->verbose('[convertToAuthors 26b]');
                    $remainder = implode(" ", $wordsRemainingAfterNext);
                    $done = true;
                    if (Str::endsWith($word, ',') && ! $prevWordHasComma) {
                        $fullName .= ',';
                    }
                    $fullName = $fullName . ' ' . rtrim($nextWord, ',. ');
                    $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                    $case = 7;
                } elseif (
                    // stop if ...
                        $determineEnd
                        &&
                        isset($remainingWords[0])
                        &&
                        ! $this->isAnd($remainingWords[0], $language)
                        && 
                        // don't stop if next word ends in a colon and is not in the dictionary
                        // (to catch case in which author list is terminated by a colon, which is ignored when computing
                        // bareWords (because colons often occur after the first or second word of a title))
                        (
                            substr($remainingWords[0], -1) != ':'
                            ||
                            $this->inDict($remainingWords[0])
                        )
                        &&
                        (
                            count($bareWords) > 3
                            ||
                            (
                                // next word ends in '.' or ',', then there is another word, and then numbers
                                // Two words seems to be the minimum (e.g. one-word article title and one-word journal name),
                                // so terminate authors.
                                in_array(substr($nextWord, -1), ['.', ',']) && isset($remainingWords[2]) && preg_match('/^[0-9.,;:\-()]*$/', $remainingWords[2])
                            )
                            ||
                            (
                                $wordAfterBareWords
                                && ctype_alpha($wordAfterBareWords)
                                && $wordAfterBareWords == mb_strtolower($wordAfterBareWords)
                                && ! $this->isAnd($wordAfterBareWords, $language) 
                                && ! in_array($wordAfterBareWords, $this->vonNames)
                                && ! in_array($wordAfterBareWords, ['al.'])
                            )
                            ||
                            (
                                $this->inDict(trim($remainingWords[0], ',;')) 
                                && ! $this->isInitials(trim($remainingWords[0], ',;'))
                                && ! in_array(trim($remainingWords[0], '.,'), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[0])
                                && ! empty($remainingWords[1]) 
                                && $this->inDict($remainingWords[1]) 
                                && ! $this->isInitials(trim($remainingWords[1], ',;'))
                                && ! in_array(trim($remainingWords[1], '.,'), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[1])
//                                && strtolower($remainingWords[1][0]) == $remainingWords[1][0] 
                                && $remainingWords[1] != '...' 
                                && ! in_array($remainingWords[1][0], ["'", "`"])
                                && (! isset($remainingWords[2]) 
                                    ||
                                    ($this->inDict($remainingWords[2])
                                    && ! $this->isInitials(trim($remainingWords[2], ',;'))
                                    && ! in_array(trim($remainingWords[2], '.,'), $this->nameSuffixes)
                                    && ! preg_match('/[0-9]/', $remainingWords[2])
                                    )
                                )
                            )
                        )
                        &&
                        (
                            $nameScore['count'] == 0
                            || $nameScore['score'] / $nameScore['count'] < 0.26
                            || (
                                isset($bareWords[1])
                                && mb_strtolower($bareWords[1]) == $bareWords[1]
                                && ! $this->isAnd($bareWords[1], $language)
                                && (
                                    ! in_array($bareWords[1], $this->vonNames)
                                    ||
                                        (
                                        isset($bareWords[2])
                                        && mb_strtolower($bareWords[2]) == $bareWords[2]
                                        && ! in_array($bareWords[2], $this->vonNames)
                                        && ! $this->isAnd($bareWords[2], $language)
                                        )
                                   )
                                && ! in_array($remainingWords[1], ['et', 'et.', 'al', 'al.'])
                               )
                            || (
                                // title cannot start with lowercase letter (assumes that only words in author string that
                                // can begin with lowercase letters are 'and', a von name, and 'et' or 'al').
                                isset($remainingWords[1])
                                && preg_match('/^[a-z]/', $remainingWords[1])
                                && ! $this->isAnd($remainingWords[1])
                                && ! in_array($remainingWords[1], $this->vonNames)
                                && ! in_array($remainingWords[1], ['et', 'et.', 'al', 'al.'])
                               )
                        )
                        &&
                        (
                            ! $this->isInitials(rtrim($remainingWords[0], ',: '))
                            ||
                            ($remainingWords[0] == 'A' && isset($remainingWords[1]) && $remainingWords[1][0] == strtolower($remainingWords[1][0]))
                        )
                    ) {
                    // Low nameScore relative to number of bareWords (e.g. less than 26% of words not in dictionary)
                    // Note that this check occurs only when $namePart > 0---so it rules out double-barelled
                    // family names that are not followed by commas.  ('Paulo Klinger Monteiro, ...' is OK.)
                    // Cannot set limit to be > 1 bareWord, because then '... Smith, Nancy Lutz and' gets truncated
                    // at comma.

                    $this->verbose('[convertToAuthors 28]');
                    $done = true;
                    $this->addToAuthorString(11, $authorstring, $this->formatAuthor($fullName));
                } elseif ($nameComplete && Str::endsWith($word, [',', ';']) && isset($words[$i + 1]) && ! $this->isEd($words[$i + 1])) {
                    // $word ends in comma or semicolon and next word is not string for editors
                    if (
                        $type == 'editors'
                        && in_array(trim($words[$i+1], ', '), $this->cities)
                        &&
                            (
                                substr($words[$i+1], -1) == ':'
                                ||
                                (
                                    isset($words[$i+2])
                                    &&
                                    (
                                        strlen($words[$i+2]) == 2
                                        &&
                                        substr($words[$i+2], -1) == ':'
                                    )
                                )
                            )
                    ) {
                        $done = true;
                        $this->addToAuthorString(27, $authorstring, $this->formatAuthor($fullName));
                    } elseif ($hasAnd) {
                        $this->verbose('[convertToAuthors 29]');
                        // $word ends in comma or semicolon and 'and' has already occurred
                        // To cover the case of a last name containing a space, look ahead to see if next words
                        // are initials or year.  If so, add back comma taken off above and continue.  Else done.
                        if ($i + 3 < count($words)
                            &&
                            (
                                $this->isInitials($words[$i + 1])
                                || $this->getDate($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, true, $language)
                                || ($this->isInitials($words[$i + 2]) && $this->getDate($words[$i + 3], $trash, $trash1, $trash2, $trash3, true, true, true, $language))
                            )
                        ) {
                            $fullName .= ',';
                        } else {
                            $done = true;
                            $this->addToAuthorString(12, $authorstring, $this->formatAuthor($fullName));
                            $case = 11;
                        }
                    } elseif (! in_array(substr($words[$i+1],-1), [',', ';']) && ! $this->isInitials($words[$i+1]) && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language)) {
                        // $nameComplete and next word does not end in a comma and following word is 'and'
                        $this->verbose('[convertToAuthors 30]');
                        $this->addToAuthorString(13, $authorstring, $this->formatAuthor($fullName));
                        $done = true;
                    } else {
                        // If word ends in comma or semicolon and 'and' has not occurred.
                        // To cover case of last name containing a space, look ahead to see if next word
                        // is a year or starts quoted or italic. 
                        // If so, add back comma and continue.
                        // (Of course this routine won't do the trick if there are more authors after this one.  In
                        // that case, you need to look further ahead.)
                        $this->verbose('[c2a getDate 9]');
                        if (! $prevWordHasComma && $i + 2 < count($words)
                                && (
                                    $this->getDate($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, true, $language)
                                    ||
                                    $this->getQuotedOrItalic($words[$i + 2], true, false, $before, $after, $style)
                                )) {
                            $this->verbose('[convertToAuthors 31]');
                            $fullName .= ',';
                        } else {
                            // Low name score relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                            if ($nameScore['count'] > 2 && $nameScore['score'] / $nameScore['count'] < 0.25) {
                                $this->verbose('[convertToAuthors 32]');
                                $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            // publication info must take at least 7 words [although it may already have been removed],
                            // so with author name there must be at least 9 words left for author to be added.
                            // (Applies mostly to books with short titles.)  However, if next word is "and", that definitely
                            // is not the start of the title and if nameScore per word is 1 or more, upcoming words really must be names
                            } elseif ($type == 'authors' && count($remainingWords) < 9 && isset($remainingWords[0]) && ! $this->isAnd($remainingWords[0]) && $determineEnd && $nameScore['count'] > 0 && $nameScore['score'] / $nameScore['count'] < 1) {
                                $this->verbose('[convertToAuthors 33]');
                                $this->addToAuthorString(15, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            }
                            $case = 12;
                            $this->verbose('[convertToAuthors 34]');
                        }
                    }
                } else {
                    $this->verbose('[convertToAuthors 35]');
                    if ($wordIsVon) {
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name, so 'namePart' not incremented");
                    } else {
                        $namePart++;
                        $this->verbose("\$namePart set to 0");
                    }
                    if ($i + 1 == count($words)) {
                        $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                    }
                }
            }
        }

        return [
            'authorstring' => $authorstring,
            'warnings' => $warnings,
            'organization' => false,
            'author-pattern' => null,
        ];
    }

    /**
      * isEd: determine if string is 'Eds.' or 'Editors' (or with parens or brackets) or
      * singular version of one of these, with possible trailig . or ,
      * @param $string string
      * @return 0 if not match, 1 if singular form is matched, 2 if plural form is matched
      */
    private function isEd(string $string): int
    {
        preg_match('/^[\(\[]?[Ee]d(itor)?(?P<plural>s?)\.?[\)\]]?[.,]?$/', $string, $matches);
        if (count($matches) == 0) {
            return 0;
        } else {
            return $matches['plural'] == 's' ? 2 : 1;
        }
    }

    private function isAddressPublisher(string $string, bool $start = true, bool $finish = true, bool $allowYear = true): bool
    {
        $returner = false;
        $begin = $start ? '/^' : '/';
        $end = $finish ? '$/u' : '/u';

        $addressPublisher = '(?P<address>[\p{L},. ]{0,25}): ?(?P<publisher>[\p{L}&\-. ]{0,50})';

        if ($allowYear) {
            $match = preg_match($begin . '\(?' . $addressPublisher . '(, (?P<year>(19|20)[0-9]{2}))?\)?' . $end, $string, $matches);
        } else {
            $match = preg_match($begin . '\(?' . $addressPublisher . '\)?' . $end, $string, $matches);
        }

        if ($match) {
            $returner = true;
            $addressPublisher = $matches['address'] . ' ' . $matches['publisher'];
            $words = explode(' ', $addressPublisher);
            foreach ($words as $word) {
                if (substr($word, -1) == '.') {
                    if (! in_array($word, ['St.']) && ! preg_match('/^[A-Z]\.$/', $word)) {
                        $returner = false;
                        break;
                    }
                }
            }
        }

        return $returner;
    }

    /**
      * Find first quoted or italicized substring in $string, restricting to start if $start is true
      * and getting only italics if $italicsOnly is true.  Return false if no quoted or italicized string found.
      * Quoted means:
      * starts with `` and ends with '' or "
      * OR starts with '' and ends with '' or "
      * OR starts with unescaped " and ends with unescaped "
      * OR starts with <space>'<not '> and ends with <not \>'<not letter>
      * OR starts with unescaped `<not `> and ends with <not \>'<not letter>
      * @param $string string
      * @param $start boolean: (if true, check only for substring at start of string)
      * @param $italicsOnly boolean: (if true, get only italic string, not quoted string)
      * @param $before: part of $string preceding left delimiter and matched text
      * @param $after: part of $string following matched text and right delimiter
      * @param $style style detected: 'none', 'italic', or 'quoted'
      * @return $matchedText: quoted or italic substring
      */
    private function getQuotedOrItalic(string $string, bool $start, bool $italicsOnly, string|null &$before, string|null &$after, string|null &$style): string|bool
    {
        $matchedText = $quotedText = $beforeQuote = $afterQuote = '';
        $style = 'none';
        $end = false;

        /* 
         * Rather than using the following loop, could use regular expressions.  However, it seems that these expressions
         * would be complex because of the need to exclude escaped quotes and the various other exceptions.
         * I find the loop easier to understand and maintain.
         * NOTE: cleanText replaces French guillemets and other quotation marks with `` and ''.
        */
        if (! $italicsOnly) {
            $skip = 0;
            $begin = '';
            $end = false;
            $level = 0;

            $chars = str_split($string);

            foreach ($chars as $i => $char) {
                if ($skip) {
                    $skip--;
                // after match has ended
                } elseif ($end) {
                    $afterQuote .= $char;
                } elseif ($char == '`') {
                    if ((! isset($chars[$i-1]) || $chars[$i-1] != '\\') && isset($chars[$i+1]) && $chars[$i+1] == "`") {
                        $level++;
                        if ($begin) {
                            $quotedText .= $char . $chars[$i+1];
                            $skip = 1;
                        } else {
                            $begin = '``';
                            $skip = 1;
                        }
                    } elseif (! isset($chars[$i-1]) || $chars[$i-1] != '\\') {
                        $level++;
                        if ($begin) {
                            $quotedText .= $char;
                        } else {
                            $begin = '`';
                        }
                    } elseif ($begin) {
                        $quotedText .= $char;
                    } else {
                        $beforeQuote .= $char;
                    }
                } elseif ($char == "'") {
                    // ''
                    if (($i == 0 || $chars[$i-1] != '\\') && isset($chars[$i+1]) && $chars[$i+1] == "'") {
                        if (isset($chars[$i+2]) && $chars[$i+2] == "'") {
                            // '''
                            if ($begin == "''") {
                                $quotedText .= $char;
                                $end = true;
                                $skip = 2;
                            } elseif ($begin == "'") {
                                $quotedText .= $char;
                                $quotedText .= $chars[$i+1];
                                $end = true;
                                $skip = 1;
                            } else {
                                // Assuming quote is enclosed in '' and ' is start of nested quote.
                                $begin = "''";
                                $quotedText .= $chars[$i+2];
                                $level += 2;
                            }
                        } elseif ($begin == "``" || $begin == "''" || $begin == '"') {
                            $end = true;
                            $skip = 1;
                        } elseif ($begin) {
                            $quotedText .= $char . $chars[$i+1];
                            $skip = 1;
                            $level--;
                        } else {
                            $begin = "''";
                            $skip = 1;
                            $level++;
                        }
                    // ' at start, not followed by another '
                    } elseif ($i == 0) {
                        $level++;
                        $begin = "'";
                    // <space>' not followed by another '
                    } elseif ($chars[$i-1] == ' ') {
                        if ($begin == "'") {
                            $end = true;
                        } elseif ($begin == "`" && isset($chars[$i+1]) && $chars[$i+1] == ' ') {
                            $end = true;
                        } elseif ($begin) {
                            $level++;
                            $quotedText .= $char;
                        } else {
                            $level++;
                            $begin = "'";
                        }
                    // ' not preceded by space and not followed by ' or letter or \ [example: "\'\i"]
                    } elseif (! isset($chars[$i+1]) || ($chars[$i+1] != "'" && ! preg_match('/^[\p{L}\\\]$/u', $chars[$i+1]))) {
                    //in_array(strtolower($chars[$i+1]), range('a', 'z')))) {
                        $level--;
                        if ($begin == "'" || $begin == "`") {
                            $end = $level ? false : true;
                            if (! $end) {
                                $quotedText .= $char;
                            }
                        } elseif ($begin) {
                            $quotedText .= $char;
                        } else {
                            $beforeQuote .= $char;
                        }
                    // ' followed by letter and not preceded by space
                    } elseif ($begin) {
                        $quotedText .= $char;
                    } else {
                        $beforeQuote .= $char;
                    }
                } elseif ($char == '"') {
                    if ($i == 0 || $chars[$i-1] != '\\') {
                        if ($begin == '"' || $begin == '``' || $begin == '?') {
                            $level--;
                            $end = $level ? false : true;
                            if (! $end) {
                                $quotedText .= $char;
                            }
                        } elseif ($begin) {
                            $quotedText .= $char;
                        } else {
                            $begin = '"';
                            $level++;
                        }
                    } elseif ($begin) {
                        $quotedText .= $char;
                    } else {
                        $beforeQuote .= $char;
                    }
                } elseif ($char == '?' && $i == 0) {
                    $begin = '?';
                    $level++;
                } elseif ($char == '?' && $begin == '?') {
                    $end = true;
                } elseif ($begin) {
                    $quotedText .= $char;
                } else {
                    $beforeQuote .= $char;
                }
            }
        
            // There is no matching end quote
            if (! $start && $begin && $end == false) {
                $quotedText = $beforeQuote = '';
                $afterQuote = $string;
            }
        }

        // If quoted text ends in a lowercase letter, not punctuation for example, and is followed by a space and then a lowercase letter,
        // and is not followed by " in" or by " forthcoming", it is the first word of the title that is in 
        // quotes --- it is not the entire title.  E.g. "Global" cardiac ...
        if (
            $quotedText 
            && preg_match('/[a-z]$/', $quotedText)
            && preg_match('/^ [a-z]/', $afterQuote) 
            && ! preg_match('/^ (in|en|em)[ :,]/', $afterQuote) 
            && ! preg_match('/^ (forthcoming|to appear|accepted|submitted)/', $afterQuote)
            ) {
            $quotedText = '';
        } else {
            $before = $beforeQuote;
            $after = $afterQuote;
        }

        $italicText = $this->getStyledText($string, $start, 'italics', $beforeItalics, $afterItalics, $remains);

        if ($italicsOnly) {
            if ($italicText && (! $start || strlen($beforeItalics) == 0)) {
                $before = $beforeItalics;
                $after = $afterItalics;
                $matchedText = $italicText;
            }
        } elseif ($quotedText && $italicText) {
            $quoteFirst = strlen($beforeQuote) < strlen($beforeItalics);
            $style = $quoteFirst ? 'quoted' : 'italic';
            $before =  $quoteFirst ? $beforeQuote : $beforeItalics;
            $after = $quoteFirst ? $afterQuote : $afterItalics;
            if (! $start || strlen($before) == 0) {
                $matchedText = $quoteFirst ? $quotedText : $italicText;
            }
        } elseif ($quotedText) {
            $style = 'quoted';
            $before = $beforeQuote;
            $after = $afterQuote;
            if (! $start || strlen($before) == 0) {
                $matchedText = $quotedText;
            }
        } elseif ($italicText) {
            $style = 'italic';
            $before = $beforeItalics;
            $after = $afterItalics;
            if (! $start || strlen($before) == 0) {
                $matchedText = $italicText;
            }
        }

        return $matchedText;
    }

    /**
     * Get first styled substring in $string, restricting to start if $start is true.  Return false if 
     * string contains no styled text.
     * @param $string string
     * @param $start boolean (if true, check only for substring at start of string)
     * @param $style: 'bold' or 'italics'
     * @param $before: the string preceding the styled text; null if none 
     * @param $after: the string following the styled text; null if none 
     * @param $remains: $before rtrimmed of ' .,' concatenated with $after ltrimmed to ' .,'
     * return bold substring
     */
    private function getStyledText(string $string, bool $start, string $style, string|null &$before, string|null &$after, string|null &$remains): string|bool
    {
        $styledText = false;
        $before = $after = null;
        $remains = $string;

        $bracketLevel = 0;
        if ($this->containsFontStyle($string, $start, $style, $position, $codeLength)) {
            for ($j = $position + $codeLength; $j < strlen($string); $j++) {
                if ($string[$j] == '{') {
                    $bracketLevel++;
                } elseif ($string[$j] == '}') {
                    if ($bracketLevel) {
                        $bracketLevel--;
                    } else {
                        break;
                    }
                }
            }
            $styledText = rtrim(substr($string, $position + $codeLength, $j - $position - $codeLength), ',');
            // If period needs to be trimmed, it should be trimmed later
            //$styledText = $this->trimRightPeriod($styledText);
            $before = substr($string, 0, $position);
            $after = substr($string, $j + 1);
            $remains = rtrim($before, ' .,') . ltrim($after, ' ,.');
        }

        return $styledText;
    }

    /**
     * getDate: get *last* substring in $string that is a date, unless $start is true, in which case restrict to 
     * start of string and take only first match
     * @param string $string 
     * @param string|null $remains what is left of the string after the substring is removed
     * @param string|null $month
     * @param boolean $start = true (if true, check only for substring at start of string)
     * @param boolean $allowMonth = false (allow string like "(April 1998)" or "(April-May 1998)" or "April 1998:"
     * @return string year
     */
    private function getDate(string $string, string|null &$remains, string|null &$month, string|null &$day, string|null &$date, bool $start = true, bool $allowMonth = false, bool $allowEarlyYears = false, string $language = 'en'): string
    {
        $year = '';
        $remains = $string;
        $months = $this->monthsRegExp[$language];

        $centuries = $allowEarlyYears ? '13|14|15|16|17|18|19|20' : '18|19|20';

        // en => n.d., es => 's.f.', pt => 's.d.'?
        if (preg_match('/^(?P<year>[\(\[]?(n\. ?d\.|s\. ?f\.|s\. ?d\.)[\)\]]?|[\(\[]?[Ff]orthcoming[\)\]]?|[\(\[]?[Ii]n [Pp]ress[\)\]]?)(?P<remains>.*)$/', $remains, $matches0)) {
            $remains = $matches0['remains'];
            $year = trim($matches0['year'], '[]()');
            return $year;
        // (2020) [1830] OR 2020 [1830]
        } elseif (preg_match('/^(?P<year>\(?(' . $centuries . ')[0-9]{2}\)? [\[\(](' . $centuries . ')[0-9]{2}[\]\)])(?P<remains>.*)$/i', $remains, $matches0)) {
            $remains = trim($matches0['remains'], ') ');
            if ($matches0['year'][0] == '(') {
                $year = substr($matches0['year'], 1);
            } else {
                $year = $matches0['year'];
            }
            //$year = str_replace(['(', ')'], '', $matches0['year']);
            return $year;
        }

        if ($allowMonth) {
            if (
                // (year month) or (year month day) (or without parens or with brackets)
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2}),? (?P<month>' . $months . ') ?(?P<day>[0-9]{1,2})?)[\)\]]?/i', $string, $matches1)
                ||
                // (year, day month) 
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2}),? (?P<day>[0-9]{1,2}) (?P<month>' . $months . ') ?)[\)\]]?/i', $string, $matches1)
                ||                
                // (day month year) or (month year) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Portuguese
                preg_match('/^ ?[\(\[]?(?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>' . $months . ') ?(de )?(?P<year>(' . $centuries . ')[0-9]{2}))[\)\]]?/i', $string, $matches1)
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
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(' . $centuries . ')[0-9]{2}),?[ \/](?P<month>[0-9]{1,2})[ \/](?P<day>[0-9]{1,2}))[\)\]]?/i', $string, $matches1)
                ) {
                $year = $matches1['year'] ?? null;
                $month = $matches1['month'] ?? null;
                $day = $matches1['day'] ?? null;
                $date = $matches1['date'] ?? null;
                $remains = substr($remains, strlen($matches1[0]));

                return $year;
            }
        }

        if (! $start && $allowMonth) {
            if (
                // <year> <month> <day>? [month cannot be followed by '-': in that case it's a month range, picked up in next preg_match]
                // space after $months is not optional, otherwise will pick up '9' as day in '2020 Aug;9(8):473-480'
                preg_match('/[ \(](?P<date>(?P<year>(' . $centuries . ')[0-9]{2}) (?P<month>(' . $months . ')(?!-))( (?P<day>[0-9]{1,2}))?)/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                ||
                // <year> <month>-<month> <day>?
                // space after $months is not optional, otherwise will pick up '9' as day in '2020 Aug-Sep;9(8):473-480'
                preg_match('/[ \(](?P<date>(?P<year>(' . $centuries . ')[0-9]{2}) (?P<month>(' . $months . ')-(' . $months . '))([ ;](?P<day>[0-9]{1,2}))?)/iJ', $string, $matches2, PREG_OFFSET_CAPTURE)
                ||
                // <month> <day> <year>
                preg_match('/[ \(](?P<date>(?P<month>' . $months . ') (?P<day>[0-9]{1,2}) (?P<year>(' . $centuries . ')[0-9]{2}))/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                ||
                // <day>? <month> <year>
                // The optional "de" between day and month and between month and year is for Spanish
                preg_match('/[ \(](?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>' . $months . ') ?(de )?(?P<year>(' . $centuries . ')[0-9]{2}))/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                ||
                // <year> followed by volume, number, pages
                // volume, number, pages pattern may need to be relaxed
                // (pages might look like years, so this case should not be left to the routine below, which takes the last year-like string)
                preg_match('/(?P<date>(?P<year>(' . $centuries . ')[0-9]{2}))[.,;] ?[0-9]{1,4} ?\([0-9]{1,4}\)[:,.] ?[0-9]{1,5} ?- ?[0-9]{1,5}/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                ) {
                $year = $matches2['year'][0] ?? null;
                $month = $matches2['month'][0] ?? null;
                if ($month) {
                    $month = rtrim($month, '.,; ');
                }
                $day = $matches2['day'][0] ?? null;
                $date = $matches2['date'][0] ?? null;
                $remains = substr($string, 0, $matches2['date'][1]) . substr($string, $matches2['date'][1] + strlen($matches2['date'][0]));
                return $year;
            }
        }

        // Remove labels from months (because of hard-coded indexes below)
        $months = preg_replace('/\(\?P<m[1-9][0-2]?>/', '', $months);
        $months = preg_replace('/\)|/', '', $months);
        // Year can be (1980), [1980], '1980 ', '1980,', '1980.', '1980)', '1980:' or end with '1980' if not at start and
        // (1980), [1980], ' 1980 ', '1980,', '1980.', or '1980)' if at start; instead of 1980, can be of form
        // 1980/1 or 1980/81 or 1980/1981 or 1980-1 or 1980-81 or 1980-1981
        // NOTE: '1980:' could be a volume number---might need to check for that
        $monthRegExp = '((' . $months . ')([-\/](' . $months . '))?)?';
        // In following line, [1-2]?[0-9]? added to allow second year to have four digits.  Should be (18|19|20), but that
        // would mean adding a group, which would require the recalculation of all the indices ...
        // Year should not be preceded by 'pp. ', which would mean it is in fact a page number/page range.
        $yearRegExp = '((?<!pp\. )(' . $centuries . ')([0-9]{2})(--?[1-2]?[0-9]?[0-9]{1,2}|\/[0-9]{1,4})?)[a-z]?';
        $regExp0 = $allowMonth ? $monthRegExp . '\.?,? *?' . $yearRegExp : $yearRegExp;

        // Require space or ',' in front of year if search is not restricted to start or in parens or brackets,
        // to avoid picking up second part of page range (e.g. 1913-1920).  (Comma allowed in case of no space: e.g. 'vol. 17,1983'.)
        $regExp1 = ($start ? '' : '[ ,]') . $regExp0 . '[ .,):;]';
        $regExp2 = '[ ,]' . $regExp0 . '$';
        $regExp3 = '\(' . $regExp0 . '\)';
        $regExp4 = '\[' . $regExp0 . '\]';

        if ($start) {
            $regExp = '^(' . $regExp1 . ')|^(' . $regExp3 . ')|^(' . $regExp4 . ')';
        } else {
            $regExp = '(' . $regExp1 . ')|(' . $regExp2 . ')|(' . $regExp3 . ')|(' . $regExp4 . ')';
        }

        // /J: allow duplicate names
        $regExp = '/' . $regExp . '/J';

        if ($start) {
            preg_match($regExp, $string, $matches, PREG_OFFSET_CAPTURE);
        } else {
            preg_match_all($regExp, $string, $matches, PREG_OFFSET_CAPTURE);
        }

        // Using labels for matches seems non-straightforward because the patterns are used more than once in each
        // regular expression.
        // These are the indexes of the matches for the subpatterns of the regular expression:
        if ($allowMonth) {
            $yearIndexes = $start ? [6, 15, 24] : [6, 15, 24, 33];
        } else {
            $yearIndexes = $start ? [2, 7, 12] : [2, 7, 12, 17];
        }

        foreach ($yearIndexes as $i) {
            if (isset($matches[$i]) && count($matches[$i])) {
                if (! $start) {
                    $foundMatch = $matches[$i][count($matches[$i]) - 1];
                    $wholeMatch = $matches[0][count($matches[0]) - 1];
                } else {
                    $foundMatch = $matches[$i];
                    $wholeMatch = $matches[0];
                }
                if (isset($foundMatch) && $foundMatch[1] >= 0) {
                    $year = $foundMatch[0];
                    $remains = rtrim(substr($string, 0, $wholeMatch[1]), '.,') . ' ' . ltrim(substr($string, $wholeMatch[1] + strlen($wholeMatch[0])), '.,');
                    break;
                }
            }
        }

        if ($allowMonth) {
            $monthIndexes = $start ? [2, 11, 20] : [2, 11, 20, 29];
            foreach ($monthIndexes as $i) {
                if (isset($matches[$i]) && count($matches[$i])) {
                    if (! $start) {
                        $foundMatch = $matches[$i][count($matches[$i]) - 1];
                        $wholeMatch = $matches[0][count($matches[0]) - 1];
                    } else {
                        $foundMatch = $matches[$i];
                        $wholeMatch = $matches[0];
                    }
                    if (isset($foundMatch[1]) && $foundMatch[1] >= 0) {
                        $month = $foundMatch[0];
                        $remains = rtrim(substr($string, 0, $wholeMatch[1]), '.,') . ' ' . ltrim(substr($string, $wholeMatch[1] + strlen($wholeMatch[0])), '.,');
                        break;
                    }
                }
            }
        }

        return $year;
    }

    /**
     * trimRightPeriod: remove trailing period if preceding character is not uppercase letter and word is in dictionary
     * @param $string string
     * return trimmed string
     */
    /* Unused
    private function trimRightPeriod(string $string): string
    {
        $lastWord = substr($string, strrpos($string, ' ')+1, -1);

        if ($string == '' || $string == '.') {
            $trimmedString = '';
        } elseif (strlen($string) == 1) {
            $trimmedString = $string;
        } elseif (substr($string, -1) == '.' 
                && strtoupper(substr($string, -2, 1)) != substr($string, -2, 1)
                && $this->inDict($lastWord)) {
            $trimmedString = substr($string, 0, -1);
        } else {
            $trimmedString = $string;
        }

        return $trimmedString;
    }
    */

    /**
     * trimRightBrace: remove trailing brace if and only if string contains one more right brace than left brace
     * (e.g. deals with author's name Andr\'{e})
     * @param $string string
     * return trimmed string
     */
    private function trimRightBrace(string $string): string
    {
        return (substr($string, -1) == '}' && substr_count($string, '}') - substr_count($string, '{') == 1) ? substr($string, 0, -1) : $string;
    }

    /**
     * Assuming $string contains the publisher and address, isolate those two components;
     * @param $string string
     * @param $address string
     * @param $publisher string
     * @return $remainder string
     */
    private function extractPublisherAndAddress(string $string, string|null &$address, string|null &$publisher, string|null $cityString, string|null $publisherString): string
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
            $result = $this->findRemoveAndReturn($remainder, '((19|20)[0-9]{2})');
            $dupYear = $result ? $result[0] : null;

            $periodPos = strpos($remainder, '.');

            // If period follows 'St.' at start of string or ' St.' later in string, ignore it and find next period
            if (
                $periodPos !== false 
                && 
                (($periodPos == 2 && substr($remainder, 0, 3) == 'St.') || ($periodPos > 2 && substr($remainder, $periodPos - 3, 4) == ' St.'))
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
        // preceding first colon and and city to be rest
        } elseif (! substr_count($string, ':') && substr_count($string, ',')) {
            $wordBeforeComma = trim(substr($string, 0, strpos($string, ',')), ',. ');
            $wordAfterComma = trim(substr($string, strpos($string, ',') + 1), ',.: ');
            if ($wordBeforeComma == $cityString) {
                $address = $wordBeforeComma . ', ' . $wordAfterComma;
                $publisher = '';
            } else {
                $publisher = $wordBeforeComma;
                $address = $wordAfterComma;
            }
            $remainder = '';
        // else take publisher/city to be strings that match list above and report rest to be
        // city/publisher
        } else {
            $stringMinusPubInfo = $string;
            foreach ($this->publishers as $publisherFromList) {
                $publisherPos = strpos($string, $publisherFromList);
                if ($publisherPos !== false) {
                    $containsPublisher = true;
                    $publisher = $publisherFromList;
                    $stringMinusPubInfo = substr($string, 0, $publisherPos) . substr($string, $publisherPos + strlen($publisherFromList));
                    break;
                }
            }
            foreach ($this->cities as $cityFromList) {
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

    // Report whether $string is a year between 1700 and 2100
    private function isYear(string $string): bool
    {
        $number = (int) $string;
        return $number > 1700 && $number < 2100;
    }

    // Report whether $string is a year between 1700 and 2100
    private function isYearRange(string $string): bool
    {
        return preg_match('/(19|20)[0-9]{2} ?- ?(19|20)[0-9]{2}/', $string);
    }

    // Report whether $string is the start of the name of the proceedings of a conference
    private function isProceedings(string $string): bool
    {
        $isProceedings = false;

        foreach ($this->italicCodes as $code) {
            $string = Str::replaceStart($code, '', $string);
        }

        if (preg_match('/' . $this->proceedingsRegExp . '/i', $string)
                && ! preg_match('/' . $this->proceedingsExceptions . '/i', $string)) {
            $isProceedings = true;
        }

        return $isProceedings;
    }

    /**
     * bareWords: in array $words of strings, report the elements at the start up until one ends
     * in ',' or '.' or ')' or ';' or is a year in parens or brackets or starts with quotation mark
     * or, if $stopAtAnd is true, is 'and'.
     */
    private function bareWords(array $words, bool $stopAtAnd, string $language = 'en'): array
    {
        $barewords = [];
        $j = 0;
        foreach ($words as $j => $word) {
            $stop = false;
            $endsWithPunc = false;
            $include = true;

            // Don't add : to this list (otherwise problems with titles that have : after first or second word)
            if (Str::endsWith($word, ['.', ',', ')', ';', '}'])) {
                $stop = true;
                $endsWithPunc = true;
            }

            if (preg_match('/[A-Z]\.:/', $word)) {
                $stop = true;
                $endsWithPunc = true;
            }

            if (preg_match('/(\(|\[)?(18|19|20)([0-9][0-9])(\)|\])?/', $word) || Str::startsWith($word, '(')) {
                $stop = true;
                $include = false;
            }

            if (Str::startsWith($word, ['`', '"', "'", '\emph{', '{\em'])) {
                $stop = true;
            }

            // 'et' deals with the case 'et al.'
            if ($word == 'et') {
                $stop = true;
            }

            if ($stopAtAnd && $this->isAnd($word, $language)) {
                $stop = true;
            }

            if ($stop) {
                if ($include) {
                    $barewords[] = $endsWithPunc ? substr($word, 0, -1) : $word;
                }
                break;
            } else {
                $barewords[] = $word;
            }
        }

        return ['barewords' => $barewords, 'nextword' => $words[$j+1] ?? null];
    }

    /*
     * Assign a score to an array of words, with higher numbers meaning it is more likely to consist of names than
     * the title of an item.  If $ignoreAnd is true, ignore occurrences of 'and'.
     * +1 for each word in the string that is not in the dictionary
     * +2 for each word in the string that is a von name
     * -2 for each word in the string that is a stopword
     */
    private function nameScore(array $words, bool $ignoreAnd): array
    {
        $aspell = Aspell::create();
        $wordsToCheck = [];
        $score = 0;

        foreach ($words as $word) {
            // Names are in dictionary with initial u.c. letter, so convert word to l.c. to exclude them as regular words
            $lcword = mb_strtolower($word);

            if (($this->isAnd($word) && ! $ignoreAnd) || $this->isInitials(($word))) {
                $score++;
            } elseif (
                // not using isAnd here, because that allows "with"
                    ($word != 'and' || ! $ignoreAnd) &&
//                    ((isset($word[0]) && mb_strtoupper($word[0]) == $word[0]) || in_array($word, $this->vonNames)) &&
                    ! $this->isInitials($word) &&
                    ! in_array($word, $this->dictionaryNames)
                ) {
                $wordsToCheck[] = $lcword;
                if (in_array($lcword, $this->stopwords) && ! in_array($word, $this->vonNames)) {
                    $score -= 2;
                }
                if (in_array($word, $this->vonNames)) { // && ! $this->inDict($lcword)
                    $score += 2;
                }
            }
        }

        $string = implode(' ', $wordsToCheck);
        // Number of words in $wordsToCheck not in dictionary
        $score += iterator_count($aspell->check($string, ['en_US']));
        
        $returner = ['count' => count($wordsToCheck), 'score' => $score];

        return $returner;
    }

    /*
     * Determine whether $word is in the dictionary and not in the list of names in the dictionary
     */
    private function inDict(string $word, bool $lowercaseOnly = true): bool
    {
        $aspell = Aspell::create();
        // strtolower to exclude proper names (e.g. Federico is in dictionary)
        $inDict = 0 == iterator_count($aspell->check($lowercaseOnly ? strtolower($word) : $word));
        $notName = ! in_array($word, $this->dictionaryNames);
        return $inDict && $notName;
    }

    /**
     * isNotName: determine if $word1 and $word2 might be names: start with u.c. letter or is a von name
     * or "d'" and is not an initial
     * @param $words array
     */
    private function isNotName(string $word1, string $word2): bool
    {
        $words = [$word1, $word2];

        foreach ($words as $i => $word) {
            // in case word is like {J}.-{P}.
            $word = preg_replace('/\{([A-Z])\}/', '$1', $word);
            // in case word is {Smith} or {Smith},
            $endsWithComma = false;
            if (Str::endsWith($word, ',')) {
                $word = rtrim($word, ',');
                $endsWithComma = true;
            }
            if (Str::startsWith($word, '{') && Str::endsWith($word, '}')) {
                $words[$i] = trim($word, '{}') . ($endsWithComma ? ',' : '');
            } else {
                $words[$i] = $word;
            }
        }

        $this->verbose(['text' => 'Arguments of isNotName: ', 'words' => [$words[0], $words[1]]]);
        $result = false;
        // Following reg exp allows {\'A} and \'{A} and \'A (but also allows {\'{A}, which it shouldn't)
        $accentRegExp = '/(^\{?(\\\"|\\\\\'|\\\`|\\\\\^|\\\H|\\\v|\\\A|\\\~|\\\k|\\\c|\\\\\.)\{?[A-Z]\}?|^\{\\\O\})/';
        
        for ($i = 0; $i < 2; $i++) {
            if (preg_match($accentRegExp, $words[$i])) {
                $this->verbose(['text' => 'Name component ', 'words' => [$words[$i]], 'content' => ' starts with accented uppercase character']);
            } elseif (
                // Not a name if doesn't start with an accented uppercase letter and it starts with l.c. and is not
                // "d'" and is not a von name and is not a single (possibly lowercase) letter followed by a period
                // [e.g. as in E. v. d. Boom]
                isset($words[$i][0]) 
                    && mb_strtolower($words[$i][0]) == $words[$i][0]
                    && ! (strlen($words[$i]) == 2 && in_array($words[$i][0], range('a', 'z')) && $words[$i][1] == '.')
                    && substr($words[$i], 0, 2) != "d'" 
                    && ! in_array($words[$i], ['...', '…']) 
                    && ! in_array($words[$i], $this->vonNames)
                    && $words[$i] != 'of'  // To deal with "Nicholas of Breslov" as author; don't add to vonNames, because they are used in other places, and adding them will mean that parts of titles are classified as names
                ) {
                $this->verbose(['text' => 'isNotName: ', 'words' => [$words[$i]], 'content' => ' appears not to be a name']);
                return true;
            }
        }

        $this->verbose(['text' => 'isNotName: ', 'words' => [$word1, $word2], 'content' => ' could be names']);
        return $result;
    }

    /**
     * Regularize A.B. or A. B. to A. B. (but keep A.-B. as it is)
     * @param $string string
     */
    private function spaceOutInitials(string $string): string
    {
        return preg_replace('/(?<!\\\)\.([^ -])/', '. $1', $string);
    }

    /**
     * Normalize format to Smith, A. B. or A. B. Smith or Smith, Alan B. or Alan B. Smith.
     * In particular, change Smith AB to Smith, A. B. and A.B. SMITH to A. B. Smith
     * $nameString is a FULL name (e.g. first and last or first middle last)
     */
    private function formatAuthor(string $nameString, bool $initials = false): string
    {
        $this->verbose(['text' => 'formatAuthor: argument ', 'words' => [$nameString]]);

        // If $nameString contains no space, just return it.  (Probably name for website?)
        if (! str_contains(trim($nameString, ' .'), ' ')) {
            return trim($nameString, ' .');
        }

        $nameString = str_replace('..', '.', $nameString);
        if (! str_contains($nameString, '{')) {
            $nameString = rtrim($nameString, '}');
        }

        $namesRaw = explode(' ', $nameString);

        // $initialsStart is index of component (a) that is initials and (b) after which all components are initials
        // initials are any string for which all letters are u.c. and at most two characters that are
        // letter or period
        $initialsStart = count($namesRaw);
        $allUppercase = true;
        $names = [];
        $initialsMaxStringLength = $initials ? 4 : 2; // initials could be 'A' or 'AB' or 'A.'

        foreach ($namesRaw as $k => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsStart = (strtoupper($lettersOnlyName) == $lettersOnlyName 
                    && strlen($lettersOnlyName) <= $initialsMaxStringLength) ? min([$k, $initialsStart]) : count($namesRaw);
            // Ignore $name that is '.' or ',' (a typo)
            if (! in_array($name, ['.', ','])) {
                $names[] = $name;
            }
        }

        // If word does not end in comma and names do not begin with initials and ??
        // add comma to end of preceding word if there isn't one there already
        if (strpos($nameString, ',') === false && $initialsStart > 0 && $initialsStart < count($names)) {
            if (substr($names[$initialsStart - 1], -1) != ',') {
                $names[$initialsStart - 1] .= ',';
            }
        }

        $fName = '';
        $commaPassed = false;
        //$initialPassed = false;

        $lettersOnlyNameString = preg_replace("/[^A-Za-z]/", '', $nameString);
        if (strtoupper($lettersOnlyNameString) != $lettersOnlyNameString) {
            $allUppercase = false;
        }

        foreach ($names as $i => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            if ($i) {
                $fName .= ' ';
            }
            // if (strpos($name, '.') !== false) {
            //     $initialPassed = true;
            // }

            // If name (all components) is not ALL uppercase, there are fewer than 3 letters
            // in $name or a comma has occurred and there are fewer than 4 letters, and all letters in the name are uppercase, assume $name
            // is initials.  Put periods and spaces as appropriate.
            if (
                ! $allUppercase &&
                (strlen($lettersOnlyName) < 3 || ($commaPassed && ($initials || strlen($lettersOnlyName) < 4))) &&
                strtoupper($lettersOnlyName) == $lettersOnlyName &&
                $lettersOnlyName != 'III'
                ) {
                // First deal with single accented initial
                // Case of multiple accented initials not currently covered
                if (preg_match('/^\\\\\S\{[a-zA-Z]\}\.$/', $name)) {  // e.g. \'{A}.
                    $fName .= $name; 
                } elseif (preg_match('/^\{\\\\\S[a-zA-Z]\}\.$/', $name)) {  // e.g. {\'A}.
                    $fName .= $name;
                } elseif (preg_match('/^\{\\\\\S[a-zA-Z]\}$/', $name)) {  // e.g. {\'A}
                    $fName .= $name . '.';
                } elseif (preg_match('/^\\\\\S\{[a-zA-Z]\}$/', $name)) {  // e.g. \'{A}
                    $fName .= $name . '.';
                } elseif (preg_match('/^\\\\\S[a-zA-Z]$/', $name)) {  // e.g. \'A
                    $fName .= $name . '.';
                } else {
                    $chars = mb_str_split($name);
                    foreach ($chars as $j => $char) {
                        if (ctype_alpha($char)) {
                            if ($j >= count($chars) - 1 || $chars[$j + 1] != '.') {
                                $fName .= $char . '.';
                                if (count($chars) > $j + 1 && $chars[$j+1] != '-') {
                                    $fName .= ' ';
                                }
                            } else {
                                $fName .= $char;
                            }
                        } else {
                            $fName .= $char;
                        }
                    }
                }

            // If name is ALL uppercase and contains no period, translate uppercase component to an u.c. first letter and the rest l.c.
            // (Contains no period to deal with name H.-J., which should not be convered to H.-j.)
            } elseif (
                strtoupper($lettersOnlyName) == $lettersOnlyName &&
                (strpos($name, '.') === false || strpos($name, '.') < strlen($name)) &&
                $lettersOnlyName != 'III'
                ) {
                $chars = mb_str_split($name);
                $lcName = '';
                foreach ($chars as $i => $char) {
                    $lcName .= ($i && $chars[$i-1] != '-') ? mb_strtolower($char) : $char;
                }
                $fName .= strlen($lcName) > 2 ? rtrim($lcName, '.') : $lcName;
            } else {
                $fName .= $name;
            }

            if (strpos($name, ',') !== false) {
                $commaPassed = true;
            }
        }

        $this->verbose(['text' => 'formatAuthor: result ', 'words' => [$fName]]);
        return $fName;
    }

    // Get journal name from $remainder, which includes also publication info
    private function getJournal(string &$remainder, object &$item, bool $italicStart, bool $pubInfoStartsWithForthcoming, bool $pubInfoEndsWithForthcoming, string $language): string
    {
        if ($italicStart) {
            // (string) on next line to stop VSCode complaining
            $italicText = (string) $this->getQuotedOrItalic($remainder, true, false, $before, $after, $style);
            if (preg_match('/ [0-9]/', $italicText)) {
                // Seems that more than just the journal name is included in the italics/quotes, so forget the quotes/italics
                // and continue
                $remainder = $before . $italicText . $after;
            } else {
                $remainder = $before . $after;
                return $italicText;
            }
        }

        $containsDigit = preg_match('/[0-9]/', $remainder);

        if ($pubInfoStartsWithForthcoming && ! $containsDigit) {
            // forthcoming at start
            $result = $this->extractLabeledContent($remainder, $this->startForthcomingRegExp, '.*', true);
            $journal = $this->getQuotedOrItalic($result['content'], true, false, $before, $after, $style);
            if (! $journal) {
                $journal = $result['content'];
            }
            $label = $result['label'];
            if (Str::startsWith($label, ['Forthcoming', 'forthcoming', 'Accepted', 'accepted', 'To appear', 'to appear'])) {
                $label = Str::replaceEnd(' in', '', $label);
                $label = Str::replaceEnd(' at', '', $label);
            }
            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . $label, 'getJournal 1');
        } elseif ($pubInfoEndsWithForthcoming && !$containsDigit) {
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
                    || preg_match('/^(' . $this->monthsRegExp[$language] . ')( [0-9]{1,2})?[.,;]/', $remainder) // <month> or <month day> next
                    || preg_match($this->volRegExp2, $remainder) // followed by volume info
                    || preg_match($this->startPagesRegExp, ltrim($remainder, '( ')) // followed by pages info
                    || preg_match('/^' . $this->articleRegExp . '/i', $remainder) // followed by article info
                    || $this->containsFontStyle($remainder, true, 'bold', $posBold, $lenBold) // followed by bold
                    || $this->containsFontStyle($remainder, true, 'italics', $posItalic, $lenItalic) // followed by italics
                    // (Str::endsWith($word, '.') && strlen($word) > 2 && $this->inDict($word) && !in_array($word, $this->excludedWords))
                )
                {
                    $this->verbose('[getJournal] Remainder: ' . $remainder);
                    $journal = rtrim(implode(' ', $initialWords), ', ');
                    $remainder = ltrim($remainder, ',.');
                    break;
                }
            }
        }

        // To deal with (erroneous) extra quotes at start
        $journal = ltrim($journal, "' ");

        return $journal;
    }

    // Allows page number to be preceded by uppercase letter.  Second number in range should really be allowed
    // to start with uppercase letter only if first number in range does so---and if pp. is present, almost
    // anything following should be allowed as page numbers?
    // '---' shouldn't be used in page range, but might be used by mistake
    private function getVolumeNumberPagesForArticle(string &$remainder, object &$item, string $language, bool $start = false): bool
    {
        $remainder = trim($this->regularizeSpaces($remainder), ' ;.,\'');
        $result = false;

        $months = $this->monthsRegExp[$language];

        // First check for some common patterns
        // p omitted from permitted starting letters, to all p100 to be interpreted as page 100.
        $number = '[A-Za-oq-z]?([Ss]upp )?[0-9][0-9]{0,12}[A-Za-z]?';
        $numberWithRoman = '([1-9][0-9]{0,3}|[IVXLCD]{1,6})';
        $letterNumber = '([A-Z]{1,3})?-?' . $number;
        $numberRange = $number . '(( ?--?-? ?|_|\?)' . $number . ')?';
        // slash is permitted in range of issues (e.g. '1/2'), but not for volume, because '12/3' is interepreted to mean
        // volume 12 number 3
        $numberRangeWithSlash = $number . '(( ?--?-? ?|_|\/)' . $number . ')?( ?\(?(' . $months . ')([-\/](' . $months . '))?\)?)?';
        //$monthRange = '\(?(?P<month1>' . $months . ')(-(?P<month2>' . $months . '))?\)?';
        // Ð is for non-utf8 encoding of en-dash(?)
        $letterNumberRange = $letterNumber . '(( ?--?-? ?|_|\?|Ð)' . $letterNumber . ')?';
        $numberRangeWithRoman = $numberWithRoman . '((--?-?|_)' . $numberWithRoman . ')?';
        $volumeRx = '('. $this->volumeRegExp . ')?(?P<vol>' . $numberRange . ')';
        $volumeWithRomanRx = '('. $this->volumeRegExp . ')?(?P<vol>' . $numberRangeWithRoman . ')';
        $numberRx = '('. $this->numberRegExp . ')?(?P<num>' . $numberRangeWithSlash . ')';
        //$volumeWordRx = '('. $this->volumeRegExp . ')(?P<vol>' . $numberRange . ')';
        // Letter in front of volume is allowed only if preceded by "vol(ume)" and is single number
        $volumeWordLetterRx = '('. $this->volumeRegExp . ')(?P<vol>' . $letterNumber . ')';
        $numberWordRx = '('. $this->numberRegExp . ')(?P<num>' . $numberRangeWithSlash . ')';
        $pagesRx = '(?P<pageWord>'. $this->pageWordsRegExp . ')?(?P<pp>' . $letterNumberRange . ')';
        $punc1 = '(}?[ ,] ?|, ?| ?: ?|,? ?\(\(?|\* ?\()';
        $punc2 = '(\)?[ :] ?|\)?\)?, ?| ?: ?)';

        $dashEquivalents = ['---', '--', ' - ', '- ', ' -', '_', 'Ð', '?'];

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
        // e.g. Volume A6, No. 3 
        } elseif (preg_match('/^' . $volumeWordLetterRx . $punc1 . $numberWordRx . '$/J', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 6');
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 7');
            $remainder = '';
            $result = true;
        // e.g. Volume A6, 41-75$
        } elseif (preg_match('/^' . $volumeWordLetterRx . $punc1 . $pagesRx . '$/J', $remainder, $matches)) {
               $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 8');
               $this->setField($item, 'pages', str_replace($dashEquivalents, '-', $matches['pp']), 'getVolumeNumberPagesForArticle 9');
               $remainder = '';
               $result = true;
        } elseif (! $start) {
            // If none of the common patterns fits, fall back on approach that first looks for a page range then
            // uses the method getVolumeAndNumberForArticle to figure out the volume and number, if any
            $numberOfMatches = preg_match_all('/' . $this->pagesRegExp . '/J', $remainder, $matches, PREG_OFFSET_CAPTURE);
            if ($numberOfMatches) {
                $matchIndex = $numberOfMatches - 1;
                $this->verbose('[p0] matches: 1: ' . $matches[1][$matchIndex][0] . '; 2: ' . $matches[2][$matchIndex][0] . '; 3: ' . $matches[3][$matchIndex][0]);
                $this->verbose("Number of matches for a potential page range: " . $numberOfMatches);
                $this->verbose("Match index: " . $matchIndex);
                $this->setField($item, 'pages', str_replace(['---', '--', ' '], ['-', '-', ''], $matches[3][$matchIndex][0]), 'getVolumeNumberPagesForArticle 10');
                $take = $matches[0][$matchIndex][1];
                $drop = $matches[3][$matchIndex][1] + strlen($matches[3][$matchIndex][0]);
                $result = true;
                // single page
            } elseif (preg_match('/p\. (?P<pp>[1-9][0-9]{0,5})/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                if (isset($matches['pp'])) {
                    $this->setField($item, 'pages', $matches['pp'][0], 'getVolumeNumberPagesForArticle 10a');
                    $take = $matches[0][1];
                    $drop = $matches[1][1] + strlen($matches[1][0]);
                    $result = true;
                }
            } else {
                $item->pages = '';
                $take = 0;
                $drop = 0;
            }

            $remainder = rtrim(substr($remainder, 0, $take) . ' ' . substr($remainder, $drop), ',.: ');
            $remainder = trim($remainder, ',. ');
        }

        return $result;
    }

    private function getVolumeAndNumberForArticle(string &$remainder, object &$item, bool &$containsNumberDesignation, bool &$numberInParens): void
    {
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
            }
        } else {
            // $item->number can be a range (e.g. '6-7')
            // Look for something like 123:6-19
            // First consider case in which there is only a volume
            $this->verbose('[v3]');
            $this->verbose('Remainder: ' . $remainder);
            // 'Volume? 123$'
            $numberOfMatches1 = preg_match('/^(' . $this->volumeRegExp . ')?([1-9][0-9]{0,3})$/', $remainder, $matches1, PREG_OFFSET_CAPTURE);
            // $this->volumeRegExp has space at end of it, but no further space is allowed.
            // So 'Vol. A2' is matched but not 'Vol. 2, no. 3'
            $numberOfMatches2 = preg_match('/^(' . $this->volumeRegExp . ')([^ 0-9]*[1-9][0-9]{0,3})$/', $remainder, $matches2, PREG_OFFSET_CAPTURE);

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
                preg_match('/^(' . $this->volumeRegExp . ')(?P<volume>[1-9][0-9]{0,3})[,\.\/ ](--? ?)?/', $remainder, $matches);
                if (isset($matches['volume'])) {
                    $volume = $matches['volume'];
                    $this->setField($item, 'volume', $volume, 'getVolumeAndNumberForArticle 17');
                    $remainder = trim(str_replace($matches[0], '', $remainder));
                    // Does a number follow the volume?
                    // The /? allows a format 125/6 for volume/number
                    preg_match('%^(?P<numberDesignation>' . $this->numberRegExp . ')?[ /]?(?P<number>([0-9]{1,20}[a-zA-Z]*)(-[1-9][0-9]{0,6})?)\)?%', $remainder, $matches);
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
                    $numberOfMatches = preg_match('%(' . $this->volumeRegExp . '|[^0-9]|^)(?P<volume>[1-9][0-9]{0,3})(?P<punc1> ?, |\(| | \(|\.|:|;|/)(?P<numberDesignation>' . $this->numberRegExp . ')? ?(?P<number>([0-9]{1,20}[a-zA-Z]*)([/-][1-9][0-9]{0,6})?)\)?%', $remainder, $matches, PREG_OFFSET_CAPTURE);
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
                        $volume = $this->extractLabeledContent($remainder, $this->volumeRegExp, '[1-9][0-9]{0,3}');
                        if ($volume) {
                            $this->verbose('[p2c]');
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
                                } else {
                                    // Assume all of $remainder is volume (might be something like '123 (Suppl. 19)')
                                    if (! Str::contains($remainder, ['('])) {
                                        $remainder = rtrim($remainder, ')');
                                    }
                                    // If volume is in parens, remove them.
                                    if (preg_match('/^\((?P<volume>.*?)\)$/', $remainder, $matches)) {
                                        $remainder = $matches['volume'];
                                    }
                                    $this->setField($item, 'volume', trim($remainder, ' ,;:.'), 'getVolumeAndNumberForArticle 13');
                                }
                                unset($item->number);
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
    }

    private function translate(string $string, string $language) {
        if ($language == 'my') {
            $string = str_replace("0", "\xE1\x81\x80", $string);
            $string = str_replace("1", "\xE1\x81\x81", $string);
            $string = str_replace("2", "\xE1\x81\x82", $string);
            $string = str_replace("3", "\xE1\x81\x83", $string);
            $string = str_replace("4", "\xE1\x81\x84", $string);
            $string = str_replace("5", "\xE1\x81\x85", $string);
            $string = str_replace("6", "\xE1\x81\x86", $string);
            $string = str_replace("7", "\xE1\x81\x87", $string);
            $string = str_replace("8", "\xE1\x81\x88", $string);
            $string = str_replace("9", "\xE1\x81\x89", $string);
        }

        return $string;
    }

    private function cleanText(string $string, string|null $charEncoding, string|null $language): string
    {
        $string = str_replace("\\newblock", "", $string);
        $string = str_replace("\\newpage", "", $string);
        // Replace each tab with a space
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\\textquotedblleft ", "``", $string);
        $string = str_replace("\\textquotedblleft{}", "``", $string);
        $string = str_replace("\\textquotedblright ", "''", $string);
        $string = str_replace("\\textquotedblright", "''", $string);
        $string = str_replace("\\textquoteright ", "'", $string);
        $string = str_replace("\\textendash ", "--", $string);
        $string = str_replace("\\textendash{}", "--", $string);
        $string = str_replace("\\textemdash ", "---", $string);
        $string = str_replace("\\textemdash{}", "---", $string);
        $string = str_replace("•", "", $string);

        if ($charEncoding == 'utf8' || $charEncoding == 'utf8leave') {
            // Replace non-breaking space with regular space
            $string = str_replace("\xC2\xA0", " ", $string);
            // Replace thin space with regular space
            $string = str_replace("\xE2\x80\x89", " ", $string);
            // Replace punctuation space with regular space
            $string = str_replace("\xE2\x80\x88", " ", $string);
            // Remove left-to-right mark
            $string = str_replace("\xE2\x80\x8E", "", $string);
            // Remove zero width joiner
            $string = str_replace("\xE2\x80\x8D", "", $string);
            // Replace zero-width non-breaking space with regular space
            // Change is now made when file is uploaded
            //$string = str_replace("\xEF\xBB\xBF", " ", $string);

            // http://www.utf8-chartable.de/unicode-utf8-table.pl (change "go to other block" to see various parts)
            $string = str_replace("\xE2\x80\x90", "-", $string);
            $string = str_replace("\xE2\x80\x91", "-", $string);
            $string = str_replace("\xE2\x80\x93", "--", $string);
            $string = str_replace("\xE2\x80\x94", "---", $string);
            $string = str_replace("\xE2\x80\x98", "`", $string);
            $string = str_replace("\xE2\x80\x99", "'", $string);
            $string = str_replace("\xE2\x80\x9C", "``", $string);
            $string = str_replace("\xE2\x80\x9D", "''", $string);
            $string = str_replace("\xEF\xAC\x80", "ff", $string);
            $string = str_replace("\xEF\xAC\x81", "fi", $string);
            $string = str_replace("\xEF\xAC\x82", "fl", $string);
            $string = str_replace("\xEF\xAC\x83", "ffi", $string);
            $string = str_replace("\xEF\xAC\x84", "ffl", $string);

            // French guillemets
            $string = str_replace("\xC2\xAB", "``", $string);
            $string = str_replace("\xC2\xBB", "''", $string);
            // „ and ”
            $string = str_replace("\xE2\x80\x9E", "``", $string);
            $string = str_replace("\xE2\x80\x9D", "''", $string);
            // ‘ and ’
            $string = str_replace("\xE2\x80\x98", "``", $string);
            $string = str_replace("\xE2\x80\x99", "''", $string);
            // ‚ [single low quotation mark, but here translated to comma, which it looks like]
            // If it is ever used as an opening quote, may need to make translation depend on
            // whether it is preceded or followed by a space
            $string = str_replace("\xE2\x80\x9A", ",", $string);

            $string = str_replace("\xEF\xBC\x81", "!", $string);
            $string = str_replace("\xEF\xBC\x82", '"', $string);
            $string = str_replace("\xEF\xBC\x83", '#', $string);
            $string = str_replace("\xEF\xBC\x84", '$', $string);
            $string = str_replace("\xEF\xBC\x85", '%', $string);
            $string = str_replace("\xEF\xBC\x86", '&', $string);
            $string = str_replace("\xEF\xBC\x87", "'", $string);
            $string = str_replace("\xEF\xBC\x88", "(", $string);
            $string = str_replace("\xEF\xBC\x89", ")", $string);
            $string = str_replace("\xEF\xBC\x8A", "*", $string);
            $string = str_replace("\xEF\xBC\x8B", "+", $string);
            $string = str_replace("\xEF\xBC\x8C", ", ", $string);
            $string = str_replace("\xEF\xBC\x8D", "-", $string);
            $string = str_replace("\xEF\xBC\x8E", ".", $string);
            $string = str_replace("\xEF\xBC\x8F", "/", $string);
            $string = str_replace("\xEF\xBC\x9A", ":", $string);
            $string = str_replace("\xEF\xBC\x9B", ";", $string);
            $string = str_replace("\xEF\xBC\x9F", "?", $string);
            $string = str_replace("\xEF\xBC\x3B", "[", $string);
            $string = str_replace("\xEF\xBC\x3D", "]", $string);
            $string = str_replace("\xEF\xBD\x80", "`", $string);
            $string = str_replace("\xEF\xBC\xBB", "[", $string);
            $string = str_replace("\xEF\xBC\xBD", "]", $string);
            $string = str_replace("\xEF\xBC\xBE", "^", $string);

            if ($language == 'my') {
                // Burmese numerals
                $string = str_replace("\xE1\x81\x80", "0", $string);
                $string = str_replace("\xE1\x81\x81", "1", $string);
                $string = str_replace("\xE1\x81\x82", "2", $string);
                $string = str_replace("\xE1\x81\x83", "3", $string);
                $string = str_replace("\xE1\x81\x84", "4", $string);
                $string = str_replace("\xE1\x81\x85", "5", $string);
                $string = str_replace("\xE1\x81\x86", "6", $string);
                $string = str_replace("\xE1\x81\x87", "7", $string);
                $string = str_replace("\xE1\x81\x88", "8", $string);
                $string = str_replace("\xE1\x81\x89", "9", $string);
            }
        }

        if ($charEncoding == 'utf8') {
            $string = str_replace("\xC3\x80", "{\`A}", $string);
            $string = str_replace("\xC3\x81", "{\\'A}", $string);
            $string = str_replace("\xC3\x82", "{\^A}", $string);
            $string = str_replace("\xC3\x83", "{\~A}", $string);
            $string = str_replace("\xC3\x84", "{\\\"A}", $string);
            $string = str_replace("\xC3\x85", "{\AA}", $string);
            $string = str_replace("\xC3\x86", "{\AE}", $string);
            $string = str_replace("\xC3\x87", "{\c{C}}", $string);
            $string = str_replace("\xC3\x88", "{\`E}", $string);
            $string = str_replace("\xC3\x89", "{\\'E}", $string);
            $string = str_replace("\xC3\x8A", "{\^E}", $string);
            $string = str_replace("\xC3\x8B", "{\\\"E}", $string);
            $string = str_replace("\xC3\x8C", "{\`I}", $string);
            $string = str_replace("\xC3\x8D", "{\\'I}", $string);
            $string = str_replace("\xC3\x8E", "{\^I}", $string);
            $string = str_replace("\xC3\x8F", "{\\\"I}", $string);

            $string = str_replace("\xC3\x90", "{\DH}", $string);
            $string = str_replace("\xC3\x91", "{\~N}", $string);
            $string = str_replace("\xC3\x92", "{\`O}", $string);
            $string = str_replace("\xC3\x93", "{\\'O}", $string);
            $string = str_replace("\xC3\x94", "{\^O}", $string);
            $string = str_replace("\xC3\x95", "{\~O}", $string);
            $string = str_replace("\xC3\x96", "{\\\"O}", $string);
            $string = str_replace("\xC3\x98", "{\O}", $string);
            $string = str_replace("\xC3\x99", "{\`U}", $string);
            $string = str_replace("\xC3\x9A", "{\\'U}", $string);
            $string = str_replace("\xC3\x9B", "{\^U}", $string);
            $string = str_replace("\xC3\x9C", "{\\\"U}", $string);
            $string = str_replace("\xC3\x9D", "{\\'Y}", $string);
            $string = str_replace("\xC3\x9E", "{\Thorn}", $string);
            $string = str_replace("\xC3\x9F", "{\ss}", $string);

            $string = str_replace("\xC3\xA0", "{\`a}", $string);
            $string = str_replace("\xC3\xA1", "{\\'a}", $string);
            $string = str_replace("\xC3\xA2", "{\^a}", $string);
            $string = str_replace("\xC3\xA3", "{\=a}", $string);
            $string = str_replace("\xC3\xA4", "{\\\"a}", $string);
            $string = str_replace("\xC3\xA5", "{\aa}", $string);
            $string = str_replace("\xC3\xA6", "{\ae}", $string);
            $string = str_replace("\xC3\xA7", "\c{c}", $string);
            $string = str_replace("\xC3\xA8", "{\`e}", $string);
            $string = str_replace("\xC3\xA9", "{\\'e}", $string);
            $string = str_replace("\xC3\xAA", '{\^e}', $string);
            $string = str_replace("\xC3\xAB", '{\\"e}', $string);
            $string = str_replace("\xC3\xAC", "{\`\i}", $string);
            $string = str_replace("\xC3\xAD", "{\\'\i}", $string);
            $string = str_replace("\xC3\xAE", "{\^\i}", $string);
            $string = str_replace("\xC3\xAF", "{\\\"\i}", $string);

            $string = str_replace("\xC3\xB0", "{\dh}", $string);
            $string = str_replace("\xC3\xB1", "{\~n}", $string);
            $string = str_replace("\xC3\xB2", "{\`o}", $string);
            $string = str_replace("\xC3\xB3", "{\\'o}", $string);
            $string = str_replace("\xC3\xB4", "{\^o}", $string);
            $string = str_replace("\xC3\xB5", "{\=o}", $string);
            $string = str_replace("\xC3\xB6", "{\\\"o}", $string);
            $string = str_replace("\xC3\xB8", "{\o}", $string);
            $string = str_replace("\xC3\xB9", "{\`u}", $string);
            $string = str_replace("\xC3\xBA", "{\\'u}", $string);
            $string = str_replace("\xC3\xBB", "{\^u}", $string);
            $string = str_replace("\xC3\xBC", "{\\\"u}", $string);
            $string = str_replace("\xC3\xBD", "{\\'y}", $string);
            $string = str_replace("\xC3\xBE", "{\\thorn}", $string);
            $string = str_replace("\xC3\xBF", "{\\\"y}", $string);

            $string = str_replace("\xC4\x80", "{\=A}", $string);
            $string = str_replace("\xC4\x81", "{\=a}", $string);
            $string = str_replace("\xC4\x82", "{\u{A}}", $string);
            $string = str_replace("\xC4\x83", "{\u{a}}", $string);
            $string = str_replace("\xC4\x84", "{\k{A}}", $string);
            $string = str_replace("\xC4\x85", "{\k{a}}", $string);
            $string = str_replace("\xC4\x86", "{\\'C}", $string);
            $string = str_replace("\xC4\x87", "{\\'c}", $string);
            $string = str_replace("\xC4\x88", "{\^C}", $string);
            $string = str_replace("\xC4\x89", "{\^c}", $string);
            $string = str_replace("\xC4\x8A", "{\.C}", $string);
            $string = str_replace("\xC4\x8B", "{\.c}", $string);
            $string = str_replace("\xC4\x8C", "\\v{C}", $string);
            $string = str_replace("\xC4\x8D", "\\v{c}", $string);
            $string = str_replace("\xC4\x8E", "\\v{D}", $string);

            //$string = str_replace("\xC4\x90", "{}", $string);
            //$string = str_replace("\xC4\x91", "{}", $string);
            $string = str_replace("\xC4\x92", "{\=E}", $string);
            $string = str_replace("\xC4\x93", "{\=e}", $string);
            $string = str_replace("\xC4\x94", "{\u{E}}", $string);
            $string = str_replace("\xC4\x95", "{\u{e}}", $string);
            $string = str_replace("\xC4\x96", "{\.E}", $string);
            $string = str_replace("\xC4\x97", "{\.e}", $string);
            $string = str_replace("\xC4\x98", "{\k{E}}", $string);
            $string = str_replace("\xC4\x99", "{\k{e}}", $string);
            $string = str_replace("\xC4\x9A", "\\v{E}", $string);
            $string = str_replace("\xC4\x9B", "\\v{e}", $string);
            $string = str_replace("\xC4\x9C", "{\^G}", $string);
            $string = str_replace("\xC4\x9D", "{\^g}", $string);
            //$string = str_replace("\xC4\x9E", "{\u{G}}", $string);
            //$string = str_replace("\xC4\x9F", "{\u{g}}", $string);

            $string = str_replace("\xC4\xA0", "{\.G}", $string);
            $string = str_replace("\xC4\xA1", "{\.g}", $string);
            $string = str_replace("\xC4\xA2", "{\k{G}}", $string);
            //$string = str_replace("\xC4\xA3", "", $string);
            $string = str_replace("\xC4\xA4", "{\^H}", $string);
            $string = str_replace("\xC4\xA5", "{\^h}", $string);
            //$string = str_replace("\xC4\xA6", "{}", $string);
            //$string = str_replace("\xC4\xA7", "{}", $string);
            $string = str_replace("\xC4\xA8", "{\~I}", $string);
            $string = str_replace("\xC4\xA9", "{\=\i}", $string);
            $string = str_replace("\xC4\xAA", "{\=I}", $string);
            $string = str_replace("\xC4\xAB", "{\=\i}", $string);
            //$string = str_replace("\xC4\xAC", "{\u{I}}", $string);
            //$string = str_replace("\xC4\xAD", "{\u{\i}}", $string);
            $string = str_replace("\xC4\xAE", "{\k{I}}", $string);
            $string = str_replace("\xC4\xAF", "{\k{i}}", $string);

            $string = str_replace("\xC4\xb0", "{\.I}", $string);
            $string = str_replace("\xC4\xb1", "{\i}", $string);
            //$string = str_replace("\xC4\xb2", "", $string);
            //$string = str_replace("\xC4\xb3", "", $string);
            $string = str_replace("\xC4\xb4", "{\^J}", $string);
            $string = str_replace("\xC4\xb5", "{\^\j}", $string);
            //$string = str_replace("\xC4\xb6", "{}", $string);
            //$string = str_replace("\xC4\xb7", "{}", $string);
            //$string = str_replace("\xC4\xb8", "{\~I}", $string);
            $string = str_replace("\xC4\xb9", "{\'L}", $string);
            $string = str_replace("\xC4\xbA", "{\'l}", $string);
            //$string = str_replace("\xC4\xbB", "", $string);
            //$string = str_replace("\xC4\xbC", "", $string);
            //$string = str_replace("\xC4\xbD", "", $string);
            //$string = str_replace("\xC4\xbE", "", $string);
            //$string = str_replace("\xC4\xbF", "", $string);

            $string = str_replace("\xC5\xA0", "\\v{S}", $string);
            $string = str_replace("\xC5\xA1", "\\v{s}", $string);
        }

        /*
        if($charEncoding == 'windows1252') {
            // Following two are windows encoding of opening and closing quotes(?) [might conflict with other encodings?---
            // 93 is o circumflex and 94 is o umlaut]
            // see http://en.wikipedia.org/wiki/Windows-1252#Codepage_layout
            $string = str_replace("\x91", "`", $string);
            $string = str_replace("\x92", "'", $string);
            $string = str_replace("\x93", "``", $string);
            $string = str_replace("\x94", "''", $string);
            $string = str_replace("\x95", ".", $string);
            $string = str_replace("\x96", "--", $string);
            $string = str_replace("\x97", "---", $string);
            $string = str_replace("\x98", "~", $string);
            $string = str_replace("\xE0", "{\`a}", $string);
            $string = str_replace("\xE1", "{\'a}", $string);
            $string = str_replace("\xE2", "{\^a}", $string);
            $string = str_replace("\xE3", "{\~a}", $string);
            $string = str_replace("\xE4", "{\"a}", $string);
            $string = str_replace("\xE5", "{\aa}", $string);
            $string = str_replace("\xE6", "{\ae}", $string);
            $string = str_replace("\xE7", "{\c{c}}", $string);
            $string = str_replace("\xE8", "{\`e}", $string);
            $string = str_replace("\xE9", "{\'e}", $string);
            $string = str_replace("\xEA", "{\^e}", $string);
            $string = str_replace("\xEB", "{\"e}", $string);
            $string = str_replace("\xEC", "{\`\i}", $string);
            $string = str_replace("\xED", "{\'\i}", $string);
            $string = str_replace("\xEE", "{\^\i}", $string);
            $string = str_replace("\xEF", "{\"\i}", $string);
            $string = str_replace("\xF0", "{\dh}", $string);
            $string = str_replace("\xF1", "{\~n}", $string);
            $string = str_replace("\xF2", "{`\o}", $string);
            $string = str_replace("\xF3", "{\'o}", $string);
            $string = str_replace("\xF4", "{\^o}", $string);
            $string = str_replace("\xF5", "{\~o}", $string);
            $string = str_replace("\xF6", "{\"o}", $string);
            //$string = str_replace("\xF7", "", $string);
            $string = str_replace("\xF8", "{\o}", $string);
            $string = str_replace("\xF9", "{\`u}", $string);
            $string = str_replace("\xFA", "{\'u}", $string);
            $string = str_replace("\xFB", "{\^u}", $string);
            $string = str_replace("\xFC", "{\"u}", $string);
        }
        */

        $string = str_replace("&nbsp;", " ", $string);
        $string = str_replace("\\ ", " ", $string);
        $string = str_replace("\\textbf{ }", " ", $string);
        $string = str_replace("\\textbf{\\ }", " ", $string);
        $string = str_replace("\\textit{ }", " ", $string);
        $string = str_replace("\\textit{\\ }", " ", $string);
        // Replace ~ with space if not preceded by \ or / or : (as it will be in a URL; actualy #:~: in URL)
        $string = preg_replace('/([^\/\\\:])~/', '$1 ', $string);
        $string = str_replace("\\/", "", $string);
        // Remove copyright symbol
        $string = str_replace("©", "", $string);

        // Fix errors like 'x{\em ' by adding space after the x [might not be error?]
        $string = preg_replace('/([^ ])(\{\\\[A-Za-z]{2,8} )/', '$1 $2', $string);

        // Delete ^Z and any trailing space (^Z is at end of last entry of DOS file)
        $string = rtrim($string, " \032");
        $string = ltrim($string, ' ');
        
        // Regularize spaces
        $string = $this->regularizeSpaces($string);
        
        return $string;
    }
}
