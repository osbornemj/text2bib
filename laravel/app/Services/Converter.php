<?php

namespace App\Services;

use Carbon\Carbon;

use Illuminate\Support\Str;

use App\Models\City;
use App\Models\Conversion;
use App\Models\DictionaryName;
use App\Models\ExcludedWord;
use App\Models\Journal;
use App\Models\Name;
use App\Models\Publisher;
use App\Models\VonName;

use App\Traits\MakeScholarTitle;
use App\Traits\Stopwords;

use PhpSpellcheck\Spellchecker\Aspell;
use stdClass;

class Converter
{
    var $accessedRegExp1;
    var $articleRegExp;
    var $boldCodes;
    var $bookTitleAbbrevs;
    var $cities;
    var $detailLines;
    var $dictionaryNames;
    var $editionRegExp;
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
    var $italicCodes;
    var $italicTitle;
    var $itemType;
    var $journalWord;
    var $journalNames;
    var $masterRegExp;
    var $monthsRegExp;
    var $names;
    var $nameSuffixes;
    var $numberRegExp;
    var $oclcRegExp1;
    var $oclcRegExp2;
    var $ordinals;
    var $pagesRegExp;
    var $startPagesRegExp;
    var $phdRegExp;
    var $phrases;
    var $proceedingsRegExp;
    var $proceedingsExceptions;
    var $publishers;
    var $retrievedFromRegExp1;
    var $retrievedFromRegExp2;
    var $startForthcomingRegExp;
    var $thesisRegExp;
    var $volRegExp0;
    var $volRegExp1;
    var $volRegExp2;
    var $volumeRegExp;
    var $vonNames;
    var $workingPaperRegExp;
    var $workingPaperNumberRegExp;

    use Stopwords;
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
        $this->journalNames = Journal::where('distinctive', 1)
            ->where('checked', 1)
            ->orderByRaw('CHAR_LENGTH(name) DESC')
            ->pluck('name')
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

        $this->nameSuffixes = ['Jr.', 'Sr.', 'III'];

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
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th'],
            'fr' =>
                ['1er', '2e', '3e', '4e', '5e', '6e', '7e'],
            'es' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th'],
            'pt' =>
                ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th'],
            'nl' =>
                ['1e', '2e', '3e', '4e', '5e', '6e', '7e'],
        ];

        $this->articleRegExp = 'article (id )?[0-9]*';

        $this->edsRegExp1 = '/[\(\[]([Ee]ds?\.?|[Ee]ditors?)[\)\]]/';
        $this->edsRegExp2 = '/ed(\.|ited) by/i';
        $this->edsRegExp4 = '/( [Ee]ds?[\. ]|[\(\[][Ee]ds?\.?[\)\]]| [Ee]ditors?| [\(\[][Ee]ditors?[\)\]])/';
        $this->editorStartRegExp = '/^[\(\[]?[Ee]dited by|^[\(\[]?[Ee]ds?\.?|^[\(\[][Ee]ditors?/';
        $this->editorEndRegExp = '[\(\[]?eds?\.?[\)\]]?$|[\(\[]?editors?[\)\]]?$';
        $this->editorRegExp = '( eds?[\. ]|[\(\[]eds?\.?[\)\]]| editors?| [\(\[]editors?[\)\]])';

        $this->editionRegExp = '(1st|first|2nd|second|3rd|third|[4-9]th|[1-9][0-9]th|fourth|fifth|sixth|seventh) (rev\. |revised )?(ed\.|edition)';

        $this->volRegExp0 = ',? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?[1-9][0-9]{0,4}';
        $this->volRegExp1 = '/,? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?\d/';
        $this->volRegExp2 = '/^\(?vol(\.|ume)? ?|^\(?v\. /i';
        $this->volumeRegExp = '[Vv]olume ?|[Vv]ol\.? ?|VOL\.? ?|[Vv]\. |{\\\bf |\\\textbf{|\\\textit{';

        $this->numberRegExp = '[Nn][Oo]s?\.?:? ?|[Nn]umbers? ?|[Nn]\. |[Ii]ssues? ?';

        $this->pagesRegExp = '([Pp]p\.?|[Pp]\.|[Pp]ages?)?( )?(?P<pages>[A-Z]?[1-9][0-9]{0,4} ?-{1,3} ?[A-Z]?[0-9]{1,5})';
        $this->startPagesRegExp = '/^pages |^pp\.?|^p\.|^p /i';

        // en for Spanish (and French?), em for Portuguese
        $this->inRegExp1 = '/^[iIeE]n:? /';
        $this->inRegExp2 = '/( [iIeE]n: |[,.] [IiEe]n | [ei]n\) | [eE]m: |[,.] [Ee]m | [ei]m\) )/';

        $this->startForthcomingRegExp = '^\(?forthcoming( at| in)?\)?|^in press|^accepted( at)?|^to appear in';
        $this->forthcomingRegExp = 'forthcoming( at| in)?|in press|accepted( at)?|to appear in';
        $this->endForthcomingRegExp = ' (forthcoming|in press|accepted|to appear)\.?\)?$';
        $this->forthcomingRegExp1 = '/^[Ff]orthcoming/';
        $this->forthcomingRegExp2 = '/^[Ii]n [Pp]ress/';
        $this->forthcomingRegExp3 = '/^[Aa]ccepted/';
        $this->forthcomingRegExp4 = '/[Ff]orthcoming\.?\)?$/';
        $this->forthcomingRegExp5 = '/[Ii]n [Pp]ress\.?\)?$/';
        $this->forthcomingRegExp6 = '/[Aa]ccepted\.?\)?$/';
        $this->forthcomingRegExp7 = '/^[Tt]o appear in/';

        // If next reg exp works, (conf\.|conference) can be deleted, given '?' at end.
        $this->proceedingsRegExp = '(^proceedings of |^conference on |^((19|20)[0-9]{2} )?(.*)(international )?conference on|^symposium on |^.* meeting |^.* conference proceedings|^.* proceedings of the (.*) conference|^proc\..*(conf\.|conference)?|^.* workshop |^actas del )';
        $this->proceedingsExceptions = '^Proceedings of the National Academy|^Proceedings of the [a-zA-Z]+ Society|^Proc. R. Soc.';

        $this->thesisRegExp = '[ \(\[]?([Tt]hesis|[Tt]esis|[Dd]issertation)';
        $this->fullThesisRegExp = '(PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral|[Mm]aster\\\'?s?|MA|M\.A\.)( [Tt]hesis| [Dd]issertation)';
        $this->masterRegExp = '[Mm]aster|MA|M\.A\.';
        $this->phdRegExp = 'PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral';

        $this->inReviewRegExp1 = '/[Ii]n [Rr]eview\.?\)?$/';
        $this->inReviewRegExp2 = '/^[Ii]n [Rr]eview/';
        $this->inReviewRegExp3 = '/(\(?[Ii]n [Rr]eview\.?\)?)$/';

        $this->isbnRegExp1 = 'ISBN(-10)?:? ?';
        $this->isbnRegExp2 = '[0-9X-]+';
        $this->oclcRegExp1 = 'OCLC:? ';
        $this->oclcRegExp2 = '[0-9]+';

        $this->journalWord = 'Journal';

        $this->bookTitleAbbrevs = ['Proc', 'Amer', 'Conf', 'Cont', 'Sci', 'Int', "Auto", 'Symp'];

        $this->workingPaperRegExp = '(preprint|arXiv preprint|bioRxiv|working paper|discussion paper|technical report|report no.|'
                . 'research paper|mimeo|unpublished paper|unpublished manuscript|manuscript|'
                . 'under review|submitted|in preparation)';
        // Working paper number can contain letters and dashes, but must contain at least one digit
        // (otherwise word following "manuscript" will be matched, for example)
        $this->workingPaperNumberRegExp = ' (\\\\#|number|no\.?)? ?(?=.*[0-9])([a-zA-Z0-9\-]+),?';

        $this->retrievedFromRegExp1 = [
            'en' => '(Retrieved from |Available( at)?:? )',
            'fr' => '(Récupéré sur |Disponible( à)?:? )',
            'es' => '(Obtenido de |Disponible( en)?:? )',
            'pt' => '(Disponível( em)?:? |Obtido de:? )',
            'nl' => '(Opgehaald van |Verkrijgbaar( bij)?:? |Available( at)?:? )',
        ];

        // Dates are between 8 and 18 characters long
        $dateRegExp = '[a-zA-Z0-9,/\-\. ]{8,18}';
        $this->retrievedFromRegExp2 = [
            'en' => '[Rr]etrieved (?P<date1>' . $dateRegExp . ' )?(, )?from |[Aa]ccessed (?P<date2>' . $dateRegExp . ' )?at ',
            'fr' => '[Rr]écupéré (?P<date1>' . $dateRegExp . ' )?sur |[Cc]onsulté (le )?(?P<date2>' . $dateRegExp . ' )?(à|sur) ',
            'es' => '[Oo]btenido (?P<date1>' . $dateRegExp . ' )?de |[Aa]ccedido (?P<date2>' . $dateRegExp . ' )?en ',
            'pt' => '[Oo]btido (?P<date1>' . $dateRegExp . ' )?de |[Aa]cesso (?P<date2>' . $dateRegExp . ' )?em ',
            'nl' => '[Oo]pgehaald (?P<date1>' . $dateRegExp . ' )?(, )?van |[Gg]eraadpleegd op (?P<date2>' . $dateRegExp . ' )?om ',
        ];

        $this->accessedRegExp1 = [
            'en' => '([Ll]ast )?([Rr]etrieved|[Aa]ccessed|[Vv]iewed)[,:]? (?P<date2>' . $dateRegExp . ')',
            'fr' => '([Rr]écupéré |[Cc]onsulté (le )?)(?P<date2>' . $dateRegExp . ')',
            'es' => '([Oo]btenido|[Aa]ccedido)[,:]? (?P<date2>' . $dateRegExp . ')',
            'pt' => '([Oo]btido |[Aa]cesso (em:?)? )(?P<date2>' . $dateRegExp . ')',
            'nl' => '([Oo]opgehaald op|[Gg]eraadpleegd op|[Bb]ekeken),? (?P<date2>' . $dateRegExp . ')',
        ];

        $this->monthsRegExp = [
            'en' => 'January|Jan[.,; ]|February|Feb[.,; ]|March|Mar[.,; ]|April|Apr[.,; ]|May|June|Jun[.,; ]|July|Jul[.,; ]|'
                . 'August|Aug[.,; ]|September|Sept?[.,; ]|October|Oct[.,; ]|November|Nov[.,; ]|December|Dec[.,; ]',
            'fr' => 'janvier|janv[.,; ]|février|févr[.,; ]|mars|avril|mai|juin|juillet|juil[.,; ]|'
                . 'aout|août|septembre|sept?[.,; ]|octobre|oct[.,; ]|novembre|nov[.,; ]|décembre|déc[.,; ]',
            'es' => 'enero|febrero|feb[.,; ]|marzo|mar[.,; ]|abril|abr[.,; ]|mayo|junio|jun[.,; ]|julio|jul[.,; ]|'
                . 'agosto|septiembre|sept?[.,; ]|octubre|oct[.,; ]|noviembre|nov[.,; ]|deciembre|dec[.,; ]',
            'pt' => 'janeiro|jan[.,; ]|fevereiro|fev[.,; ]|março|mar[.,; ]|abril|abr[.,; ]|maio|mai[.,; ]|junho|jun[.,; ]|julho|jul[.,; ]|'
                . 'agosto|ago[.,; ]setembro|set[.,; ]|outubro|oct[.,; ]|novembro|nov[.,; ]|dezembro|dez[.,; ]',
            'nl' => 'januari|jan[.,; ]|februari|febr[.,; ]|maart|mrt[.,; ]|april|apr[.,; ]|mei|juni|juli|'
                . 'augustus|aug[.,; ]|september|sep[.,; ]|oktober|okt[.,; ]|november|nov[.,; ]|december|dec[.,; ]',
        ];

        $this->vonNames = VonName::all()->pluck('name')->toArray();

        // Codes are ended by } EXCEPT \em, \it, and \sl, which have to be ended by something like \normalfont.  Code
        // that gets italic text handles only the cases in which } ends italics.
        $this->italicCodes = ["\\textit{", "\\textsl{", "\\emph{", "{\\em ", "\\em ", "{\\it ", "\\it ", "{\\sl ", "\\sl "];
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
    public function convertEntry(string $rawEntry, Conversion $conversion, string|null $language = null, string|null $charEncoding = null): array|null
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

        $phrases = $this->phrases[$language];

        // Remove comments and concatenate lines in entry
        // (do so before cleaning text, otherwise \textquotedbleft, e.g., at end of line will not be cleaned)
        $entryLines = explode("\n", $rawEntry);

        $entry = '';
        foreach ($entryLines as $line) {
            $truncated = $this->uncomment($line);
            $entry .= $line . (!$truncated ? ' ' : '');
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
        $entry = $this->cleanText($entry, $charEncoding);

        $firstComponent = 'authors';
        // If entry starts with year, extract it.
        if (preg_match('/^(?P<year>[1-9][0-9]{3})\*? (?P<remainder>.*)$/', $entry, $matches)) {
            $firstComponent = 'year';
            $year = $matches['year'];
            $remainder = ltrim($matches['remainder'], ' |*+');
        }

        if ($firstComponent == 'authors') {
            // Remove numbers at start of entry, like '6.' or '[14]'.
            $entry = ltrim($entry, ' .0123456789[]()|*+');

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

        if (!strlen($entry)) {
            return null;
        }

        // Don't put the following earlier---{} may legitimately follow \bibitem
        $entry = str_replace("{}", "", $entry);

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

        ////////////////////
        // Get doi if any //
        ////////////////////

        $doi = $this->extractLabeledContent(
            $remainder,
            ' \[?doi:? | \[?doi: ?|https?://dx.doi.org/|https?://doi.org/|doi.org/',
            '[^ ]+'
        );

        if (substr_count($doi, ')') > substr_count($doi, '(') && substr($doi, -1) == ')') {
            $doi = substr($doi, 0, -1);
        }

        // In case item says 'doi: https://...'
        $doi = Str::replaceStart('https://doi.org/', '', $doi);
        $doi = rtrim($doi, ']');
        $doi = ltrim($doi, '/');
        $doi = preg_replace('/([^\\\])_/', '$1\_', $doi);

        if ($doi) {
            $this->setField($item, 'doi', $doi, 'setField 1');
        } else {
            $this->verbose("No doi found.");
        }

        /////////////////////
        // Get PMID if any //
        /////////////////////

        if (preg_match('/pmid: [0-9]{6,9}/i', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
            $this->addToField($item, 'note', $matches[0][0], 'addToField 1');
            $remainder = substr($remainder, 0, $matches[0][1]) . substr($remainder, $matches[0][1] + strlen($matches[0][0]));
            $remainder = trim($remainder, ' .');
        }

        //////////////////////////////////////
        // Get arXiv or bioRxiv info if any //
        //////////////////////////////////////

        $eprint = $this->extractLabeledContent($remainder, ' arxiv[:,] ?', '\S+');

        if ($eprint) {
            $this->setField($item, 'archiveprefix', 'arXiv', 'setField 2');
            $this->setField($item, 'eprint', rtrim($eprint, '}'), 'setField 3');
            $itemKind = 'unpublished';
        }

        $eprint = $this->extractLabeledContent($remainder, '([Ii]n)? bioRxiv ?', '\S+ \S+');

        if ($eprint) {
            $this->setField($item, 'archiveprefix', 'bioRxiv', 'setField 2a');
            $this->setField($item, 'eprint', trim($eprint, '()'), 'setField 3a');
            $itemKind = 'unpublished';
        }

        ////////////////////////////////////
        // Get url and access date if any //
        ////////////////////////////////////

        $retrievedFromRegExp1 = $this->retrievedFromRegExp1[$language];
        $retrievedFromRegExp2 = $this->retrievedFromRegExp2[$language];
        $accessedRegExp1 = $this->accessedRegExp1[$language];

        $urlRegExp = '(\\\url{|\\\href{)?(?P<url>https?://\S+)(})?';

        // Retrieved from (site)? <url> accessed <date>
        preg_match(
            '%(?P<retrievedFrom> ' . $retrievedFromRegExp1 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '[.,]? ' . $accessedRegExp1 . '$%i',
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
                '%(?P<retrievedFrom> ' . $retrievedFromRegExp2 . ')\[?(?P<siteName>.*)? ?\[?' . $urlRegExp . '(?P<note> .*)?$%i',
                $remainder,
                $matches,
            );
        }

        // <url> accessed <date>
        if (! count($matches)) {
            preg_match(
                '%(url: ?)?' . $urlRegExp . ',? ?\(?' . $accessedRegExp1 . '\)?$%i',
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
            $siteName = $matches['siteName'] ?? null;
            $url = $matches['url'] ?? null;
            $note = $matches['note'] ?? null;

            $dateResult = $this->isDate(trim($date, ' .,'), $language, 'contains');
            if ($dateResult) {
                $accessDate = $dateResult['date'];
                $year = $dateResult['year'];
            }
        }

        $containsUrlAccessInfo = false;
        $urlHasPdf = false;

        if (! empty($url)) {
            $url = trim($url, ')}],. ');
            $this->setField($item, 'url', $url, 'setField 4');
            if (Str::endsWith($url, ['.pdf'])) {
                $urlHasPdf = true;
            }
            if (! empty($accessDate)) {
                $this->setField($item, 'urldate', rtrim($accessDate, '., '), 'setField 5a');
                $containsUrlAccessInfo = true;
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
        $match = $this->extractLabeledContent($remainder, ' ' . $this->isbnRegExp1, $this->isbnRegExp2);
        if ($match) {
            $containsIsbn = true;
            $this->setField($item, 'isbn', $match, 'setField 16');
        }
        
        /////////////////////
        // Get OCLC if any //
        /////////////////////

        $match = $this->extractLabeledContent($remainder, ' ' . $this->oclcRegExp1, $this->oclcRegExp2);
        if ($match) {
            $this->setField($item, 'oclc', $match, 'setField 17');
        }

        /////////////////////////////////////
        // Put "Translated by ..." in note //
        /////////////////////////////////////

        $result = $this->findRemoveAndReturn($remainder, 'translated by [^.,)]*[.,)]');
        if ($result) {
            $this->addToField($item, 'note', $result[0], 'addToField 3');
            $remainder = $result['before'] . $result['after'];
        }

        ///////////////////////////////////////
        // Split remaining string into words //
        ///////////////////////////////////////

        // Exploding on spaces isn't exactly right, because a word containing an accented letter
        // can have a space in it --- e.g. Oblo{\v z}insk{\' y}.  So perform a more sophisticated explosion.
        // First remove spaces before commas (assumed to be errors)
        $remainder = str_replace(' ,', ',', $remainder);

        $chars = str_split($remainder);

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
            } elseif (in_array($char, ['.', ',']) 
                    && isset($chars[$i+1]) 
                    && ! in_array($chars[$i+1], [' ', '.', ',', ';', '-', '"', "'"]) 
                    && ! (isset($chars[$i-1]) && ctype_digit($chars[$i-1]) && ctype_digit($chars[$i+1]))
                    && ((in_array($chars[$i+1], range('0', '9')) && (!isset($chars[$i+2]) || $chars[$i+2] != ':'))
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

        //////////////////////
        // Look for authors //
        //////////////////////

        $this->verbose("Looking for authors ...");

        $isEditor = false;

        $authorConversion = $this->convertToAuthors($words, $remainder, $year, $month, $day, $date, $isEditor, true, 'authors', $language);

        $itemYear = $year;
        $itemMonth = $month;
        $itemDay = $day;
        $itemDate = $date;

        $authorstring = $authorConversion['authorstring'];
        $oneWordAuthor = $authorConversion['oneWordAuthor'];

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

        if ($isEditor === false && !Str::contains($authorstring, $editorPhrases)) {
            $this->setField($item, 'author', rtrim($authorstring, ','), 'setField 7');
        } else {
            $this->setField($item, 'editor', trim(str_replace($editorPhrases, "", $authorstring), ' .,'), 'setField 8');
        }

        if ($year) {
            $this->setField($item, 'year', $year, 'setField 9');
        }

        $remainder = trim($remainder, '.},; ');
        $this->verbose("[1] Remainder: " . $remainder);

        ////////////////////
        // Look for title //
        ////////////////////

        unset($this->italicTitle);

        $remainder = ltrim($remainder, ': ');
        $title = $this->getQuotedOrItalic($remainder, true, false, $before, $after);
        $newRemainder = $before . ltrim($after, "., ");

        // If title has been found and ends in edition specification, take that out and put it in edition field
        $editionRegExp = '/(\(' . $this->editionRegExp . '\)$|' . $this->editionRegExp . ')[.,]?$/i';
        if ($title && preg_match($editionRegExp, (string) $title, $matches)) {
            $this->setField($item, 'edition', trim(substr($matches[0], 0, strpos($matches[0], ' '))), 'setField 108');
            $title = trim(Str::replaceLast($matches[0], '', $title));
        }

        // Website
        if (isset($item->url) && $oneWordAuthor) {
            $itemKind = 'online';
            $title = trim($remainder);
            $newRemainder = '';
        }

        if (! $title) {
            $title = $this->getTitle($remainder, $edition, $volume, $isArticle, $year, $note, $journal, $containsUrlAccessInfo);
            if ($edition) {
                $this->setField($item, 'edition', $edition, 'setField 10');
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

        $this->setField($item, 'title', rtrim($title, ' .,'), 'setField 12');

        $this->verbose("Remainder: " . $remainder);

        ///////////////////////////////////////////////////////////
        // Look for year if not already found                    //
        // (may already have been found at end of author string) //
        ///////////////////////////////////////////////////////////

        $containsMonth = false;
        if (! isset($item->year)) {
            if (! $year) {
                // Space prepended to $remainder in case it starts with year, because getYear requires space 
                // (but perhaps could be rewritten to avoid it).
                $year = $this->getYear(' '. $remainder, $newRemainder, $month, $day, $date, false, true, $language);
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
                $this->setField($item, 'date', $year . '-' . $monthResult['month1number'] . '-' . $day, 'setField 15a');
            }

            if (isset($item->url) && ! isset($item->urldate) && $day) {
                $this->setField($item, 'urldate', $date, 'setField 14a');
            }
        }

        $remainder = ltrim($newRemainder, ' ');

        ///////////////////////////////////////////////////////////////////////////////
        // To determine type of item, first record some features of publication info //
        ///////////////////////////////////////////////////////////////////////////////

        // $remainder is item minus authors, year, and title
        $remainder = ltrim($remainder, '.,; ');
        $this->verbose("[type] Remainder: " . $remainder);
        
        $inStart = $containsIn = $italicStart = $containsEditors = $containsThesis = false;
        $containsNumber = $containsInteriorVolume = $containsCity = $containsPublisher = false;
        $containsEdition = $containsWorkingPaper = false;
        $containsNumberedWorkingPaper = $containsNumber = $pubInfoStartsWithForthcoming = $pubInfoEndsWithForthcoming = false;
        $endsWithInReview = false;
        $containsDigitOutsideVolume = true;
        $containsNumberDesignation = false;
        $cityLength = 0;
        $publisherString = $cityString = '';

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

        if (preg_match('/\d/', $remainder)) {
            $containsNumber = true;
            $this->verbose("Contains a number.");
        }

        // Contains volume designation, but not at start of $remainder
        if (preg_match($this->volRegExp1, substr($remainder, 3))) {
            $containsInteriorVolume = true;
            $this->verbose("Contains a volume, but not at start.");
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
        $regExp = '/^eds?\.|(?<!';
        foreach ($this->ordinals[$language] as $i => $ordinal) {
            $regExp .= ($i ? '|' : '') . $ordinal;
        }
        $regExp .= '|rev\.)[ \(}](eds?\.|editors?)/i';

        if (preg_match($this->edsRegExp1, $remainder)
                || preg_match($this->edsRegExp2, $remainder)
                || preg_match($regExp, $remainder, $matches)
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

        if (preg_match('/[ :][1-9][0-9]{0,3} ?-{1,2} ?[1-9][0-9]{0,3}([\.,\} ]|$)/', $remainder)
                ||
                preg_match('/([1-9][0-9]{0,3}|p\.)(, |\(| | \(|\.|: )([1-9][0-9]{0,3})(-[1-9][0-9]{0,3})?\)?/', $remainder)) {
            $containsPageRange = true;
            $this->verbose("Contains page range.");
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

        if (preg_match('/ edition(,|.|:|;| )/i', $remainder)) {
            $containsEdition = true;
            $this->verbose("Contains word \"edition\".");
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

        $remainderMinusPubInfo = $remainder;
        $publisher = '';
        foreach ($this->publishers as $pub) {
            if (Str::contains(mb_strtolower($remainder), mb_strtolower($pub))) {
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

        // if (preg_match('/' . $this->isbnRegExp1 . $this->isbnRegExp2 . '/', $remainder)) {
        //     $containsIsbn = true;
        //     $this->verbose("Contains an ISBN string.");
        // }

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
        } elseif (
            isset($item->url) &&
            ! $containsWorkingPaper &&
            ($oneWordAuthor || ! $urlHasPdf || $containsUrlAccessInfo) &&
            ! $inStart &&
            ! $italicStart &&
            ! $containsInteriorVolume &&
            ! $containsPageRange
             // && $itemYear && $itemMonth && $itemDay))
        ) {
            $this->verbose("Item type case 0");
            $itemKind = 'online';
        } elseif (
            $isArticle
            ||
            $containsJournalName
            || 
            ($italicStart
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
        } elseif ($containsEditors && ( $inStart || $containsPageRange)) {
            $this->verbose("Item type case 4");
            $itemKind = 'incollection';
        } elseif ($containsEditors) {
            $this->verbose("Item type case 5");
            $itemKind = 'incollection';
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [1]";
            }
        } elseif (($containsPageRange || $containsInteriorVolume) && ! $containsProceedings && ! $containsPublisher && ! $containsCity) {
            $this->verbose("Item type case 6");
            $itemKind = 'article';
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [2]";
            }
        } elseif ($containsProceedings) {
            $this->verbose("Item type case 7");
            $itemKind = 'inproceedings';
        } elseif ($containsIsbn || (isset($this->italicTitle) && (($containsCity || $containsPublisher) || isset($item->editor)))) {
            $this->verbose("Item type case 8");
            $itemKind = 'book';
        } elseif (!$containsIn && ($pubInfoStartsWithForthcoming || $pubInfoEndsWithForthcoming)) {
            $this->verbose("Item type case 9");
            $itemKind = 'article';
        } elseif ($endsWithInReview) {
            $this->verbose("Item type case 10");
            $itemKind = 'unpublished';
        } elseif ($inStart) {
            $this->verbose("Item type case 11");
            $itemKind = 'incollection';
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif ($containsPublisher) {
            if ((! $containsIn && ! $containsPageRange) || strlen($remainder) - $cityLength - strlen($publisher) < 30) {
                $this->verbose("Item type case 12");
                $itemKind = 'book';
            } else {
                $this->verbose("Item type case 13");
                $itemKind = 'incollection';
            }
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif (!$containsNumber && !$containsPageRange) {
            // Condition used to have 'or', which means that an article with a single page number is classified as a book
            if ($containsThesis) {
                $this->verbose("Item type case 14");
                $itemKind = 'thesis';
            } elseif ($endsWithInReview || $containsMonth) {
                $this->verbose("Item type case 15");
                $itemKind = 'unpublished';
            } elseif ($pubInfoEndsWithForthcoming || $pubInfoStartsWithForthcoming) {
                $this->verbose("Item type case 16");
                $itemKind = 'article';
            } else {
                $this->verbose("Item type case 17");
                $itemKind = 'book';
            }
        } elseif ($containsEdition) {
            $this->verbose("Item type case 18");
            $itemKind = 'book';
            if (!$this->itemType) {
                $warnings[] = "Not sure of type; contains \"edition\", so set to " . $itemKind . ".";
            }
        } elseif ($containsDigitOutsideVolume) {
            $this->verbose("Item type case 19");
            $itemKind = 'article';
            if (!$this->itemType) {
                $warnings[] = "Not sure of type; set to " . $itemKind . ".";
            }
        } else {
            $this->verbose("Item type case 20");
            $itemKind = 'book';
            if (!$this->itemType) {
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

        switch ($itemKind) {

            /////////////////////////////////////////////
            // Get publication information for article //
            /////////////////////////////////////////////

            case 'article':
                // Get journal
                $remainder = ltrim($remainder, '.,; ');
                // If there are any commas not preceded by digits and not followed by digits or spaces, add spaces after them
                $remainder = preg_replace('/([^0-9]),([^ 0-9])/', '$1, $2', $remainder);

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
                    if (!$italicStart) {
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

                    $journal = $this->getJournal($remainder, $item, $italicStart, $pubInfoStartsWithForthcoming, $pubInfoEndsWithForthcoming);
                    $journal = rtrim($journal, ' ,(');
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
                    $this->setField($item, 'journal', trim($journal, '*'), 'setField 19');
                } else {
                    $warnings[] = "Item seems to be article, but journal not found.  Setting type to unpublished.";
                    $itemKind = 'unpublished';  // but continue processing as if journal
                }
                $remainder = trim($remainder, ' ,.');
                $this->verbose("Remainder: " . $remainder);

                $volumeNumberPages = $remainder;

                // If $remainder ends with 'forthcoming' phrase and contains no digits (which might be volume & number,
                // for example, even if paper is forthcoming), put that in note.  Else look for pages & volume etc.
                if (preg_match('/' . $this->endForthcomingRegExp . '/', $remainder) && !preg_match('/[0-9]/', $remainder)) {
                    $this->addToField($item, 'note', trim($remainder, '()'), 'addToField 6');
                    $remainder = '';
                } else {
                    // Get pages
                    $this->getVolumeNumberPagesForArticle($remainder, $item);

                    $pagesReported = false;
                    if (! empty($item->pages)) {
                        $pagesReported = true;
                    }
                    $this->verbose("[p1] Remainder: " . $remainder);

                    if ($remainder) {
                        // Get month, if any
                        $months = $this->monthsRegExp[$language];
                        $regExp = '/(\(?(' . $months . '\)?)([-\/](' . $months . ')\)?)?)/i';
                        preg_match_all($regExp, $remainder, $matches, PREG_OFFSET_CAPTURE);

                        if (! empty($matches[0][0][0])) {
                            $month = trim($matches[0][0][0], '();');
                            $monthResult = $this->fixMonth($month, $language);
                            $this->setField($item, 'month', $monthResult['months'], 'setField 21');
                            $remainder = substr($remainder, 0, $matches[0][0][1]) . ltrim(substr($remainder, $matches[0][0][1] + strlen($matches[0][0][0])), ', )');
                            $this->verbose('Remainder: ' . $remainder);
                        }

                        // Get volume and number
                        $numberInParens = false;
                        $this->getVolumeAndNumberForArticle($remainder, $item, $containsNumberDesignation, $numberInParens);

                        $result = $this->findRemoveAndReturn($remainder, $this->articleRegExp);
                        if ($result) {
                            // If remainder contains article number, put it in the note field
                            $this->addToField($item, 'note', $result[0], 'addToField 7');
                        } elseif (! $item->pages && ! empty($item->number) && !$containsNumberDesignation) {
                            // else if no pages have been found and a number has been set, assume the previously assigned number
                            // is in fact a single page
                            if (! $numberInParens) {
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

                break;

            /////////////////////////////////////////////////
            // Get publication information for unpublished //
            /////////////////////////////////////////////////

            case 'unpublished':
                $remainder = trim($remainder, '.,} ');
                if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                    $this->addToField($item, 'note', substr($remainder, $length), 'addToField 8a');
                } else {
                    $this->addToField($item, 'note', $remainder, 'addToField 8b');
                }
                $remainder = '';

                // Somehow strlen of $item->note can be 1 even though dd says it is "".
                    if (strlen($item->note) <= 1 && ! empty($item->url)) {
                    $this->verbose('Moving content of url field to note');
                    $this->addToField($item, 'note', trim($item->url, '{}'), 'addToField 9');
                    unset($item->url);
                }

                break;

            //////////////////////////
            // Fix entry for online //
            //////////////////////////

            case 'online':
                if (empty($item->month)) {
                    unset($item->month);
                }
                if (empty($item->urldate) && $itemYear && $itemMonth && $itemDay && $itemDate) {
                    $this->setField($item, 'urldate', $itemDate, 'setField 116');
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

                if (isset($workingPaperMatches[0][1]) && $workingPaperMatches[0][1] > 0) {
                    // Chars before 'Working Paper'
                    $this->setField($item, 'institution', trim(substr($remainder, 0, $workingPaperMatches[0][1] - 1), ' .,'), 'setField 29');
                    $remainder = trim(substr($remainder, $workingPaperMatches[3][1] + strlen($number)), ' .,');
                } else {
                    // No chars before 'Working paper'---so take string after number to be institution
                    $n = $workingPaperMatches[3][1] ?? 0;
                    $this->setField($item, 'institution', trim(substr($remainder, $n + strlen($number)), ' .,'), 'setField 30');
                    $remainder = '';
                }
                if (!$item->institution) {
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
                $this->verbose("[in1] Remainder: " . $remainder);

                // If $remainder starts with "in", remove it
                if ($inStart) {
                    $this->verbose("Starts with variant of \"in\"");
                    $remainder = ltrim(substr($remainder, 2), ': ');
                }

                // Get pages and remove them from $remainder
                // Return group 4 of match and remove whole match from $remainder
                $result = $this->findRemoveAndReturn($remainder, '(\()?' . $this->pagesRegExp . '(\))?');
                if ($result) {
                    $pages = $result[4];
                    $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', 'setField 31');
                }

                if (!isset($item->pages)) {
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
                $booktitle = (string) $this->getQuotedOrItalic($remainder, false, false, $before, $after);
                $newRemainder = $remainder = $before . ltrim($after, ".,' ");
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

                $this->verbose('[in3] Remainder: ' . $remainder);
                $updateRemainder = false;

                // If $remainder starts with [a-zA-Z]*: and has no more occurrences of this pattern,
                // it is consistent with its being publisher: address, without any editors.
                if (preg_match_all('/[a-zA-Z]*:/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                    // Next line to stop VSCode complaining
                    $matches = (array) $matches;
                    //  If only one occurrence of string followed by colon, and it's at the start
                    if (count($matches[0]) == 1 && $matches[0][0][1] == 0) {
                        $this->setField($item, 'address', substr($matches[0][0][0], 0, -1), 'setField 35');
                        $this->setField($item, 'publisher', trim(substr($remainder, strlen($matches[0][0][0]))), 'setField 36');
                        $remainder = '';
                    }
                }

                // $result = $this->extractLabeledContent($remainder, '^[a-zA-Z]*:', '.*', true);
                // if ($result) {
                //     $this->setField($item, 'address', substr($result['label'], 0, -1), 'setField 35');
                //     $this->setField($item, 'publisher', $result['content'], 'setField 36');
                // }

                // The only reason why $item->editor could be set other than by the previous code block is that the 
                // item is a book with an editor rather than an author.  So probably the following condition could
                // be replaced by } else {.
                if ($remainder && !isset($item->editor)) {
                    $updateRemainder = true;
                    // If a city or publisher has been found, temporarily remove it from remainder to see what is left
                    // and whether info can be extracted from what is left
                    $tempRemainder = $remainder;
                    $remainderAfterCityString = trim(Str::after($remainder, $cityString), ', ');
                    if ($cityString) {
                        // If there is no publisher string and the type is inproceedings and there is only one word left, assume
                        // it is part of booktitle
                        if (!$publisherString && $itemKind == 'inproceedings' && strpos($remainderAfterCityString, ' ') === false) {
                            $booktitle = $remainder;
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
                    $tempRemainder = trim($tempRemainder, ',.:() ');
                    $this->verbose('[in13] tempRemainder: ' . $tempRemainder);

                    // If item doesn't contain string identifying editors, look more carefully to see whether
                    // it contains a string that could be editors' names.
                    if (!$containsEditors) {
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
                                    if (!Str::endsWith($word, [',', '.'])) {
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
                                    if (!$booktitle) {
                                        $booktitle = $tempRemainder;
                                        $this->verbose("Booktitle case 3");
                                    }
                                    $this->setField($item, 'editor', '', 'setField 40');
                                    $warnings[] = 'No editor found';
                                    $this->setField($item, 'address', $cityString, 'setField 41');
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

                if ($remainder && !$booktitle) {
                    $updateRemainder = true;
                    $remainderContainsEds = false;
                    $postEditorString = '';

                    // If no string is quoted or italic, try to determine whether $remainder starts with
                    // title or editors.
                    // If $tempRemainder ends with "ed" or similar and neither of previous two words contains digits
                    // (in which case "ed" probably means "edition" --- check last two words to cover "10th revised ed"),
                    // format must be <booktitle> <editor>
                    if (!isset($tempRemainder)) {
                        $tempRemainder = $remainder;
                    }
                    $tempRemainderWords = explode(' ', $tempRemainder);
                    $wordCount = count($tempRemainderWords);
                    if ($wordCount >= 3) {
                        $lastTwoWordsHaveDigits = preg_match('/[0-9]/', $tempRemainderWords[$wordCount - 2] . $tempRemainderWords[$wordCount - 3]); 
                    } else {
                        $lastTwoWordsHaveDigits = false;
                    }
                    if (!$lastTwoWordsHaveDigits && preg_match('/(.*)' . $this->editorEndRegExp . '/i', $tempRemainder, $matches)) {
                        $this->verbose('Remainder minus pub info ends with \'eds\' or similar, so format it <booktitle> <editor>');
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
                            $booktitle = $this->getTitle($tempRemainder, $edition, $trash1, $trash2, $year, $note, $journal, false, true);
                            if (substr($booktitle, -3) != 'ed.') {
                                $booktitle = rtrim($booktitle, '.');
                            }
                            $this->setField($item, 'booktitle', $booktitle);
                            if ($note) {
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
                    } elseif (preg_match('/^(.*?)(edited by)(.*?)$/i', $remainder, $matches)) {
                        // If it doesn't, take start of $remainder up to first comma or period to be title,
                        // followed by editors, up to (Eds.).
                        $this->verbose('Remainder contains \'edited by\'.  Taking it to be <booktitle> edited by <editor> <publicationInfo>');
                        $booktitle = trim($matches[1], ', ');
                        // Authors and publication info
                        $rest = trim($matches[3]);
                        $isEditor = true;
                        $result = $this->convertToAuthors(explode(' ', $rest), $remainder, $trash, $month, $day, $date, $isEditor, true, 'editors', $language);
                        $this->setField($item, 'editor', trim($result['authorstring'], ', '), 'setField 120');
                        $updateRemainder = false;
                    }

                    if (!isset($item->editor)) {
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
                            // $remainder contains "(Eds.)" (parens required) or similar
                            if ($this->isNameString($remainder)) {
                                // CASE 1
                                // $remainder starts with names, and so
                                // $remainder is <editors> (Eds.) <booktitle> <publicationInfo>
                                $this->verbose("Remainder contains \"(Eds.)\" or similar and starts with string that looks like a name");
                                $editorStart = true;
                                $editorString = substr($remainder, 0, $matches[0][1]);
                                $determineEnd = false;
                                $postEditorString = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                                $this->verbose("editorString: " . $editorString);
                                $this->verbose("postEditorString: " . $postEditorString);
                                $this->verbose("[in4] Remainder: " . $remainder);
                            } else {
                                // CASE 2
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
                            } elseif ($this->isNameString($remainder)) {
                                // CASE 4
                                // $remainder is <editors> <booktitle> <publicationInfo>
                                $this->verbose("Remainder does not contain \"(Eds.)\" or similar string in parentheses and does not start with \"Eds\" or similar, but starts with a string that looks like a name");
                                $editorStart = true;
                                $editorString = $remainder;
                                $determineEnd = true;
                                $this->verbose("editorString: " . $editorString);
                                $this->verbose("[in6] Remainder: " . $remainder);
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
                            if ($editorStart || $this->initialNameString($remainder)) {
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
                                        $authorConversion = $this->convertToAuthors(explode(' ', $authorstring), $trash1, $trash2, $month, $day, $date, $isEditor, false, 'editors', $language);
                                        $this->setField($item, 'editor', trim($authorConversion['authorstring'], ' '), 'setField 44');
                                        foreach ($authorConversion['warnings'] as $warning) {
                                            $warnings[] = $warning;
                                        }
                                        $newRemainder = trim(substr($remainder, $endAuthorPos + $edStrLen), ',:. ');
                                        $this->verbose("[in8] editors: " . $item->editor);
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
                                $editor = substr($remainder, 0, $publisherPos);
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

                    if (!isset($item->editor)) {
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
                    } elseif (preg_match('/(?P<booktitle>[^\(]{5,50})\((?P<address>[^:]{4,20}):(?P<publisher>[^\.]{4,40})/i', $remainder, $matches)) {
                        // common pattern: <booktitle> (<address>: <publisher>).
                        $booktitle = $matches['booktitle'];
                        $address = $matches['address'];
                        $publisher = $matches['publisher'];
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
                            // $remainder ends with pattern like 'city: publisher'; take booktitle to be preceding string
                            } elseif (preg_match('/( ([^ ]*): ([^:.,]*)$)/', $remainder, $matches)) {
                                $booktitle = Str::before($remainder, $matches[0]);
                                $this->setField($item, 'booktitle', trim($booktitle, ', '), 'setField 112');
                                $this->verbose('booktitle case 14');
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
                // Whatever is left is publisher and address
                if (empty($item->publisher) || empty($item->address)) {
                    if (! empty($item->publisher)) {
                        $this->setField($item, 'address', $remainder, 'setField 54');
                        $newRemainder = '';
                    } elseif (! empty($item->address)) {
                        $this->setField($item, 'publisher', $remainder, 'setField 55');
                        $newRemainder = '';
                    } else {
                        $newRemainder = $this->extractPublisherAndAddress($remainder, $address, $publisher, $cityString, $publisherString);
                        $this->setField($item, 'publisher', $publisher, 'setField 56');
                        $this->setField($item, 'address', $address, 'setField 57');
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
                    }
                }
                
                // if (! $item->booktitle) {
                //     $warnings[] = "Book title not found.";
                // }

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
                $remainingWords = explode(" ", $remainder);

                // If remainder contains word 'edition', take previous word as the edition number
                $this->verbose('Looking for edition');
                foreach ($remainingWords as $key => $word) {
                    if ($key && in_array(mb_strtolower(trim($word, ',. ()')), ['edition', 'ed'])) {
                        $this->setField($item, 'edition', trim($remainingWords[$key - 1], ',. )('), 'setField 59');
                        array_splice($remainingWords, $key - 1, 2);
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
                        $this->verbose('Volume is part of series: assume format is <series> <publisherAndAddress>');
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
                                    $this->verbose('Series case 2: format is <publisher> (no address)');
                                    $this->setField($item, 'series', $result2[1], 'setField 81');
                                    $this->setField($item, 'publisher', $result2[2], 'setField 82');
                                }
                            }
                        }
                        $done = true;
                    }
                }

                // Volume has been identified, but publisher and possibly address remain
                if (!$done) {
                    $remainder = $newRemainder ?? implode(" ", $remainingWords);
                    $remainder = trim($remainder, ' .');

                    // If string is in italics, get rid of the italics
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                        $remainder = rtrim(substr($remainder, $length), '}');
                    }

                    $remainderMinusPubInfo = Str::remove($cityString, $remainder);
                    $remainderMinusPubInfo = Str::remove($publisherString, $remainderMinusPubInfo);
                    // If remainder contains a period following a lowercase letter, string before period is series name
                    $periodPos = strpos($remainderMinusPubInfo, '.');
                    if ($periodPos !== false && strtolower($remainderMinusPubInfo[$periodPos-1]) == $remainderMinusPubInfo[$periodPos-1]) {
                        $series = trim(Str::before($remainderMinusPubInfo, '.'));
                        $this->setField($item, 'series', $series, 'setField 110');
                        $remainder = trim(Str::remove($series, $remainder));
                    }

                    // First use routine to find publisher and address, to catch cases where address
                    // contains more than one city, for example.

                    // If item is a book, $cityString and $publisherString are set, and existing title is followed by comma
                    // in $entyr, string preceding $cityString
                    // and $publisherString must be part of title (which must have been ended prematurely). 
                    if ($itemKind == 'book' && ! empty($cityString) && !empty($publisherString)) {
                        $afterTitle = Str::after($entry, $item->title);
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

                    $remainder = $this->extractPublisherAndAddress($remainder, $address, $publisher, $cityString, $publisherString);

                    if ($publisher) {
                        $this->setField($item, 'publisher', $publisher, 'setField 85');
                    }

                    if ($address) {
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
            case 'phdthesis':
            case 'mathesis':
                if ($itemKind == 'thesis') {
                    if (preg_match('/' . $this->masterRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $itemKind = 'mastersthesis';
                    } elseif (preg_match('/' . $this->phdRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $itemKind = 'phdthesis';
                    } else {
                        $itemKind = 'phdthesis';
                        $warnings[] = "Can't determine whether MA or PhD thesis; set to be PhD thesis.";
                    }
                }
                $this->verbose(['fieldName' => 'Item type', 'content' => $itemKind]);

                $remainder = $this->findAndRemove($remainder, $this->fullThesisRegExp);

                $remainder = trim($remainder, ' .,)');
                if (strpos($remainder, ':') === false) {
                    $this->setField($item, 'school', $remainder, 'setField 87');
                } else {
                    $remArray = explode(':', $remainder);
                    $this->setField($item, 'school', trim($remArray[1], ' .,'), 'setField 88');
                }
                $remainder = '';

                if (!isset($item->school)) {
                    $warnings[] = "No school identified.";
                }

                break;
        }

        /////////////////////////////////
        // Fix up $remainder and $item //
        /////////////////////////////////

        $remainder = trim($remainder, '.,:;}{ ');

        if ($remainder && !in_array($remainder, ['pages', 'Pages', 'pp', 'pp.'])) {
            if (preg_match('/^' . $this->endForthcomingRegExp . '/i', $remainder)
                ||
                preg_match('/^' . $this->startForthcomingRegExp . '/i', $remainder)
                ) {
                $this->addToField($item, 'note', $remainder, 'addToField 14');
            } elseif ($itemKind == 'online') {
                $this->addToField($item, 'note', $remainder, 'addToField 15');
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

        $item->title = $this->requireUc($item->title);

        $scholarTitle = $this->makeScholarTitle($item->title);

        $returner = [
            'source' => $originalEntry,
            'item' => $item,
            'label' => $itemLabel,
            'itemType' => $itemKind,
            'warnings' => $warnings,
            'notices' => $notices,
            'details' => $conversion->report_type == 'detailed' ? $this->detailLines : [],
            'scholarTitle' => $scholarTitle,
        ];

        return $returner;
    }

    /*
     * If month (or month range) is parsable, parse it: 
     * translate 'Jan' or 'Jan.' or 'January' or 'JANUARY', for example, to 'January'.
    */
    private function fixMonth(string $month, string $language = 'en'): array
    {
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

    private function setField(stdClass &$item, string $fieldName, string $string, string $id = ''): void
    {
        $item->$fieldName = $string;
        $this->verbose(['fieldName' => ($id ? '('. $id . ') ' : '') . ucfirst($fieldName), 'content' => $item->$fieldName]);
    }

    private function addToField(stdClass &$item, string $fieldName, string $string, string $id = ''): void
    {
        $this->setField($item, $fieldName, (isset($item->$fieldName) ? $item->$fieldName . ' ' : '') . $string) . 
        $this->verbose(['fieldName' => ($id ? '('. $id . ') ' : '') . ucfirst($fieldName), 'content' => $item->$fieldName]);
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
    private function getTitle(string &$remainder, string|null &$edition, string|null &$volume, bool &$isArticle, string|null &$year = null, string|null &$note, string|null $journal, bool $containsUrlAccessInfo, bool $includeEdition = false): string|null
    {
        $title = null;
        $originalRemainder = $remainder;

        $remainder = str_replace('  ', ' ', $remainder);
        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;
        $skipNextWord = false;

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

        // Common pattern for journal article
        if (preg_match('/^(?P<title>[^\.]+)\. (?P<remainder>[a-zA-Z\., ]{5,30} [0-9;():\-.,\. ]*)$/', $remainder, $matches)) {
            $title = $matches['title'];
            $remainder = $matches['remainder'];
            $this->verbose('Taking title to be string preceding period.');
            return $title;
        }

        $containsPages = preg_match('/(\()?' . $this->pagesRegExp . '(\))?/', $remainder);
        $volumeRegExp = '/(^\(v(ol)?\.?|volume) (\d)\.?\)?[.,]?$/i';
        $editionRegExp = '/(^\(' . $this->editionRegExp . '\)|^' . $this->editionRegExp . ')[.,]?$/i';

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

            // If $word is one of the italic codes ending in a space and previous word ends in some punctuation, stop and form title
            if (in_array($word . ' ', $this->italicCodes) && isset($words[$key-1]) && in_array(substr($words[$key-1], -1), [',', '.', ':', ';', '!', '?'])) {
                $this->verbose("Ending title, case 1");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                break;
            }

            $initialWords[] = $word;

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

                //$stringToNextPeriod = strtok($remainder, '.?!');
                // String up to next ?, !, or . not preceded by ' J'.
                $chars = mb_str_split($remainder, 1, 'UTF-8');
                $stringToNextPeriod = '';
                foreach ($chars as $i => $char) {
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

                // When a word ending in punctuation or preceding a word starting with ( is encountered, check whether
                // it is followed by
                // italics
                // OR a Working Paper string
                // OR a pages string
                // OR "in" OR "Journal"
                // OR a volume designation
                // OR words like 'forthcoming' or 'to appear in'
                // OR a year 
                // OR the name of a publisher.
                // If so, the title is $remainder up to the punctuation.
                // Before checking for punctuation at the end of a work, trim ' and " from the end of it, to take care
                // of the cases ``<word>.'' and "<word>."
                if (Str::endsWith(rtrim($word, "'\""), ['.', '!', '?', ':', ',', ';']) || ($nextWord && $nextWord[0] == '(')) {
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)
                        || preg_match('/^' . $this->workingPaperRegExp . '/i', $remainder)
                        || preg_match($this->startPagesRegExp, $remainder)
                        || preg_match('/^[Ii]n |^' . $this->journalWord . ' |^Proceedings |^\(?Vol\.? |^\(?VOL\.? |^\(?Volume |^\(?v\. /', $remainder)
                        || preg_match('/' . $this->startForthcomingRegExp . '/i', $remainder)
                        || preg_match('/^(19|20)[0-9][0-9]\./', $remainder)
                        || preg_match('/^' . $this->fullThesisRegExp . '/', $remainder)
                        || Str::startsWith(ltrim($remainder, '('), $this->publishers)
                        ) {
                        $this->verbose("Ending title, case 2");
                        $title = rtrim(implode(' ', $initialWords), ',:;.');
                        if (preg_match('/^' . $this->journalWord . ' /', $remainder)) {
                            $isArticle = true;
                        }
                        break;
                    }
                }

                // Upcoming volume specification
                if ($nextWord && $nextButOneWord && preg_match($volumeRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
                    $volume = $matches[2];
                    $this->verbose("Ending title, case 3a");
                    $title = rtrim(implode(' ', $initialWords), ' ,');
                    array_splice($remainingWords, 0, 2);
                    $remainder = implode(' ', $remainingWords);
                    break;
                }

                // Upcoming edition specification
                if ($nextWord && $nextButOneWord && preg_match($editionRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
                    $edition = $matches[5] ?? $matches[2];
                    $fullEdition = $matches[1];
                    $this->verbose("Ending title, case 3b");
                    $title = $includeEdition ? rtrim(implode(' ', $initialWords) . ' ' . $fullEdition, ' ,') : rtrim(implode(' ', $initialWords), ' ,');
                    array_splice($remainingWords, 0, 2);
                    $remainder = implode(' ', $remainingWords);
                    break;
                }

                // If end of title has not been detected and word ends in period-equivalent or comma, or next word starts with '('
                if (
                    Str::endsWith($word, ['.', '!', '?', ','])
                    ||
                    ($nextWord && $nextWord[0] == '(' && substr($nextWord, -1) != ')')
                    ) {
                    // if first character  of next word is lowercase letter and does not end in period
                    // OR $word and $nextWord are A. and D. or B. and C.
                    // OR following string starts with a part designation, continue, skipping next word,
                    if (
                        $nextWord 
                            && (
                            (ctype_alpha($nextWord[0]) && mb_strtolower($nextWord[0]) == $nextWord[0] && substr($nextWord, -1) != '.')
                                    || ($word == 'A.' && $nextWord == 'D.')
                                    || ($word == 'B.' && $nextWord == 'C.')
                                    || Str::startsWith($remainder, ['I. ', 'II. ', 'III. '])
                                )
                        ) {
                        $this->verbose("Not ending title, case 1 (next word is " . $nextWord . ")");
                        $skipNextWord = true;
                    // else if next word is short and ends with period, and either $word does not end in a comma, or $nextWord
                    // is the last one or $nextWord is not
                    // in the dictionary or $nextWord is initials or the following word starts with a lowercase letter,
                    // assume it is the first word of the publication info, which is an abbreviation.
                    } elseif 
                        (
                            $nextWord && 
                            strlen($nextWord) < 8 &&
                            Str::endsWith($nextWord, '.') && 
                            isset($words[$key+2]) &&
                            (! Str::endsWith($word, ',') || ! $this->inDict(substr($nextWord,0,-1)) || $this->isInitials($nextWord) || mb_strtolower($words[$key+2][0]) == $words[$key+2][0]) && 
                            (! $journal || rtrim($nextWord, '.') == rtrim(strtok($journal, ' '), '.'))
                        ) {
                        $this->verbose("Ending title, case 4");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // elseif next sentence contains word 'series', terminate title
                    } elseif (preg_match('/ series/i', $stringToNextPeriod)) {
                        $this->verbose("Ending title, case 4a");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    } elseif (preg_match('/edited by/i', $nextWord . ' ' . $nextButOneWord)) {
                        $this->verbose("Ending title, case 4b");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // else if following string up to next period contains only letters, spaces, and hyphens and doesn't start with "in"
                    // (which is unlikely to be within a title following punctuation)
                    // and is followed by at least 30 characters (for the publication info), assume it is part of the title,
                    } elseif (preg_match('/[a-zA-Z -]+/', $stringToNextPeriod)
                            && !preg_match($this->inRegExp1, $remainder)
                            && strlen($remainder) > strlen($stringToNextPeriod) + ($containsPages ? 40 : 30)) {
                        $this->verbose("Not ending title, case 2 (next word is " . $nextWord . ")");
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
                    // else if word ends with comma and volume info is coming up, wait for it
                    } elseif (Str::endsWith($word, [',']) && preg_match('/' . $this->volumeRegExp . '/', $remainder)) {
                        $this->verbose("Not ending title, case 5 (word: \"" . $word . "\"; volume info is coming up)");
                    } else {
                        // else if 
                        // (word ends with period or comma and there are 4 or more words till next punctuation, which is a period)
                        // OR entry contains url access info [in which case there is no more publication info to come]
                        // and at least three non-stopwords are all lowercase, continue [to catch Example 116]
                        // Treat hyphens in words as spaces
                        $modStringToNextPeriod = preg_replace('/([a-z])-([a-z])/', '$1 $2', $stringToNextPeriod);
                        $wordsToNextPeriod = explode(' ',  $modStringToNextPeriod);
                        $lcWordCount = 0;
                        foreach ($wordsToNextPeriod as $remainingWord) {
                            if (! in_array($remainingWord, $this->stopwords) && isset($remainingWord[0]) && ctype_alpha($remainingWord[0]) && mb_strtolower($remainingWord) == $remainingWord) {
                                $lcWordCount++;
                            }
                        }
                        if ((($lcWordCount > 2 && substr_count($modStringToNextPeriod, ' ') > 3) || $containsUrlAccessInfo)
                            // comma added in next line to deal with one case, but it may be dangerous
                            && Str::endsWith($word, ['.', ',']) 
                            && substr_count($modStringToNextPeriod, ',') == 0
                            && substr_count($modStringToNextPeriod, ':') == 0
                        ) {
                            $this->verbose("Not ending title, case 6 (word '" . $word ."')");
                        } elseif (! isset($words[$key+2])) {
                            if ($this->isYear($nextWord)) {
                                $year = $nextWord;
                            } else {
                                $this->verbose("Adding \$nextWord (" . $nextWord . "), last in string, and ending title (word '" . $word ."')");
                                $title = implode(' ', $initialWords) . ' ' . $nextWord;
                            }
                            $remainder = '';
                            break;
                        } else {
                            // otherwise assume the punctuation ends the title.
                            $this->verbose("Ending title, case 6 (word '" . $word ."')");
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
        if (isset($remainder[0]) && $remainder[0] == '(') {
            $remainder = substr($remainder, 1);
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
    private function findRemoveAndReturn(string &$string, string $regExp): false|string|array
    {
        $matched = preg_match(
            '%' . $regExp . '%i',
            $string,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if (!$matched) {
            return false;
        }

        $result = [];
        for ($i = 0; isset($matches[$i][0]); $i++) {
            $result[$i] = $matches[$i][0];
        }

        $result['before'] = substr($string, 0, $matches[0][1]);
        $result['after'] = substr($string, $matches[0][1] + strlen($matches[0][0]), strlen($string));
        $string = substr($string, 0, $matches[0][1]) . substr($string, $matches[0][1] + strlen($matches[0][0]), strlen($string));
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
        if (preg_match('/^[A-Z]\.?\.?$/', $word)) {
            $case = 1;
        } elseif (preg_match('/^[A-Z]\.[A-Z]\.$/', $word)) {
            $case = 2;
        } elseif (preg_match('/^[A-Z][A-Z]$/', $word)) {
            $case = 3;
        } elseif (preg_match('/^[A-Z]\.[A-Z]\.[A-Z]\.$/', $word)) {
            $case = 4;
        } elseif (preg_match('/^[A-Z][A-Z][A-Z]$/', $word)) {
            $case = 5;
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
     */
    private function isDate(string $string, string $language = 'en', string $type = 'is'): bool|array
    {
        $ofs = ['en' => '', 'nl' => '', 'fr' => '', 'es' => '', 'pt' => 'de'];

        $year = '(?P<year>(19|20)[0-9]{2})';
        $monthName = $this->monthsRegExp[$language];
        $of = $ofs[$language];
        $day = '[0-3]?[0-9]';
        $monthNumber = '[01]?[0-9]';

        $starts = $type == 'is' ? '^' : '';
        $ends = $type == 'is' ? '$' : '';
 
        //$str = str_replace([","], "", trim($string, ',. '));
        $matches = [];
        $isDates = [];
        $isDates[1] = preg_match('/(' . $starts . $day . '( ' . $of . ')?' . ' (' . $monthName . '),? ?' . '(' . $of . ')?' . $year . $ends . ')/i' , $string, $matches[1]);
        $isDates[2] = preg_match('/(' . $starts . '(' . $monthName . ') ?' . $day . ',? '. $year . $ends . ')/i', $string, $matches[2]);
        $isDates[3] = preg_match('/(' . $starts . $day . '[\-\/ ]' . $of . $monthNumber . '[\-\/ ]'. $of . $year . $ends . ')/i', $string, $matches[3]);
        $isDates[4] = preg_match('/(' . $starts . $monthNumber . '[\-\/ ]' . $day . '[\-\/ ]'. $year . $ends . ')/i', $string, $matches[4]);
        $isDates[5] = preg_match('/(' . $starts . $year . '[\-\/, ]' . $day . '[\-\/ ]' . $monthNumber . $ends . ')/i', $string, $matches[5]);
        $isDates[6] = preg_match('/(' . $starts . $year . '[, ]' . $monthName . ' ' . $day . $ends . ')/i', $string, $matches[6]);
        $isDates[7] = preg_match('/(' . $starts . $year . '[, ]' . $day . ' ' . $monthName . $ends . ')/i', $string, $matches[7]);

        if ($type == 'is') {
            return max($isDates);
        } elseif ($type == 'contains') {
            foreach ($isDates as $i => $isDate) {
                if (isset($matches[$i][0]) && $matches[$i][0]) {
                    return ['date' => $matches[$i][0], 'year' => isset($matches[$i]['year']) ? $matches[$i]['year'] : ''];
                }
            }
            return false;
        }
    }

    private function isAnd(string $string, $language = 'en'): bool
    {
        // 'with' is allowed to cover lists of authors like Smith, J. with Jones, A.
        // 'y' is for Spanish, 'e' for Portuguese, 'et' for French, 'en' for Dutch
        return mb_strtolower($string) == $this->phrases[$language]['and'] || in_array($string, ['\&', '&', 'y', 'e', 'et', 'en']) || $string == 'with';
    }

    /*
     * Determine whether $word is component of a name: all letters and either all u.c. or first letter u.c. and rest l.c.
     * If $finalPunc != '', then allow word to end in any character in $finalPunc.
     */
    private function isName(string $word, string $finalPunc = ''): bool
    {
        $result = false;
        if (in_array(substr($word, -1), str_split($finalPunc))) {
            $word = substr($word, 0, -1);
        }
        if (ctype_alpha($word) && (ucfirst($word) == $word || strtoupper($word) == $word)) {
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
        $word1 = count($words) > 1 ? rtrim($words[1], ',.') : null;
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
     * @return array, with author string, warnings, and oneWordAuthor flag
     */
    private function convertToAuthors(array $words, string|null &$remainder, string|null &$year, string|null &$month, string|null &$day, string|null &$date, bool &$isEditor, bool $determineEnd = true, string $type = 'authors', string $language = 'en'): array
    {
        $namePart = $authorIndex = $case = 0;
        $prevWordAnd = $prevWordVon = $done = $isEditor = $hasAnd = $multipleAuthors = false;
        $authorstring = $fullName = '';
        $remainingWords = $words;
        $warnings = [];

        $wordHasComma = $prevWordHasComma = $oneWordAuthor = false;

        $this->verbose('convertToAuthors: Looking at each word in turn');
        foreach ($words as $i => $word) {
            $nameComplete = true;
            $prevWordHasComma = $wordHasComma;
            $wordHasComma = substr($word,-1) == ',';
            // Get letters in word, eliminating accents & other non-letters, to get accurate length
            $lettersOnlyWord = preg_replace("/[^A-Za-z]/", '', $word);
            $wordIsVon = in_array($word, $this->vonNames);

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

            if (!$done) {
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

            $nextWord = isset($words[$i+1]) ? rtrim($words[$i+1], ',') : null;

            if (in_array($word, [" ", "{\\sc", "\\sc"])) {
                //
            } elseif (in_array($word, ['...', '…'])) {
                $this->verbose('[convertToAuthors 1]');
                if (isset($words[$i+1]) && $this->isAnd($words[$i+1], $language)) {
                    $this->addToAuthorString(3, $authorstring, ' and others');
                } else {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                }
                //array_shift($remainingWords);
                $fullName = '';
                $namePart = 0;
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
                if ($year = $this->getYear($remainder, $remains, $trash1, $trash2, $trash3, true, false, $language)) {
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
            } elseif ($this->isAnd($word, $language) && ($word != 'et' || ! in_array($nextWord, ['al..', 'al.', 'al']))) {
                $this->verbose('[convertToAuthors 3]');
                // Word is 'and' or equivalent, and if it is "et" it is not followed by "al".
                $hasAnd = $prevWordAnd = true;
                $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName) . ' and');
                $fullName = '';
                $namePart = 0;
                $authorIndex++;
                $reason = 'Word is "and" or equivalent';
            } elseif ($word == 'et') {
                // Word is 'et'
                $this->verbose('nextWord: ' . $nextWord);
                if (in_array($nextWord, ['al..', 'al.', 'al'])) {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                    array_shift($remainingWords);
                    if (count($remainingWords)) {
                        $remainder = implode(" ", $remainingWords);
                        $this->verbose('[c2a getYear 2]');
                        if (preg_match('/^(18|19|20)[0-9]{2}$/', $remainingWords[0])) {
                            // If first remaining word is a year **with no punctuation**, assume it starts title
                        } else {
                            $year = $this->getYear($remainder, $remains, $trash1, $trash2, $trash3, true, true, $language);
                            $remainder = trim($remains);
                        }
                    }
                    $done = true;
                    $case = 14;
                    $reason = 'Word is "et" and next word is "al." or "al"';
                }
            } elseif ($determineEnd && substr($word, -1) == ':') {
                if ($namePart >= 2 && isset($words[$i-1]) && in_array(substr($words[$i-1], -1), ['.', ','])) {
                    $this->verbose('Word ends in colon, \$namePart is at least 2, and previous word ends in comma or period, so assuming word is first word of title.');
                    $this->addToAuthorString(16, $authorstring, $this->formatAuthor($fullName));
                    $remainder = $word . ' ' . implode(" ", $remainingWords);
                    $done = true;
                } else {
                    $this->verbose('[convertToAuthors 5]');
                    $nameComponent = $word;
                    $fullName .= ' '. trim($nameComponent, '.');
                    $this->addToAuthorString(17, $authorstring, $fullName);
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
                    $this->verbose('[c2a getYear 3]');
                    if ($year = $this->getYear($remainder, $remainder, $trash1, $trash2, $trash3, true, false, $language)) {
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
                        $oneWordAuthor = true;
                        $itemYear = $year; // because $year is recalculated below
                        $done = true;
                    } elseif ($this->getQuotedOrItalic($remainder, true, false, $before, $after)) {
                        $this->verbose('[convertToAuthors 7]');
                        $nameComponent = $word;
                        $fullName .= ' and ' . trim($nameComponent, '.');
                        $this->addToAuthorString(18, $authorstring, $fullName);
                        $reason = 'Word ends in period, namePart is 0, and remaining string starts with quoted or italic';
                        $done = true;
                        $oneWordAuthor = true;
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
                } else {
                    // If $namePart > 0
                    $this->verbose('[convertToAuthors 9]');
                    $nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, '.')));
                    $fullName .= " " . $nameComponent;
                    $this->addToAuthorString(5, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, and namePart is > 0';
                }
                $this->verbose("Remainder: " . $remainder);
                $this->verbose('[c2a getYear 4]');
                $this->verbose('[convertToAuthors 10]');
                if (! isset($year) || ! $year) {
                    if ($year = $this->getYear($remainder, $remains, $month, $day, $trash3, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Remains: " . $remains);
                    }
                }
                $done = true;
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
                    $authorIndex++;
                    $reason = 'Word is "and" or equivalent';
                // Check if $word and first word of $remainingWords are plausibly a name.  If not, end search if $determineEnd.
                } elseif ($determineEnd && isset($remainingWords[0]) && $this->isNotName($word, $remainingWords[0])) {
                    $this->verbose('[convertToAuthors 12]');
                    $fullName .= ' ' . $word;
                    $remainder = implode(" ", $remainingWords);
                    $this->addToAuthorString(6, $authorstring, ' ' . ltrim($this->formatAuthor($fullName)));
                    if ($year = $this->getYear($remainder, $remains, $trash1, $trash2, $trash3, true, true, $language)) {
                        $remainder = $remains;
                        $this->verbose("Year detected");
                    }
                    $done = true;
                } elseif ($this->isInitials($word) && isset($words[$i+1]) && 
                        (
                            ($this->isInitials($words[$i+1]) && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language))
                            ||
                            (substr($words[$i+1],-1) == ',' && $this->isInitials(substr($words[$i+1],0,-1)))
                        )
                    ) {
                    $this->verbose('[convertToAuthors 13]');
                    $fullName .= ' ' . $word;
                } elseif (substr($word,-1) == ',' && $this->isInitials(substr($word,0,-1))) {
                    $this->verbose('[convertToAuthors 14]');
                    $fullName .= ' ' . substr($word,0,-1);
                    $namePart = 0;
                    $authorIndex++;
                } else {
                    $this->verbose('[convertToAuthors 15]');
                    if (!$prevWordAnd && $authorIndex) {
                        $this->addToAuthorString(7, $authorstring, $this->formatAuthor($fullName) . ' and');
                        $fullName = '';
                        $prevWordAnd = true;
                    }
                    $name = $this->spaceOutInitials($word);
                    // If part of name is all uppercase and 3 or more letters long, convert it to ucfirst(mb_strtolower())
                    // For component with 1 or 2 letters, assume it's initials and leave it uc (to be processed by formatAuthor)
                    $nameComponent = (strlen($name) > 2 && strtoupper($name) == $name && strpos($name, '.') === false) ? ucfirst(mb_strtolower($name)) : $name;
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
                        if (!Str::endsWith($words[$i], ',') 
                                && isset($words[$i+1]) 
                                && Str::endsWith($words[$i+1], ',') 
                                && ! $this->isInitials(substr($words[$i+1], 0, -1)) 
                                && isset($words[$i+2]) 
                                && Str::endsWith($words[$i+2], ',') 
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
                        if ($year = $this->getYear(implode(" ", $remainingWords), $remains, $trash1, $trash2, $trash3, true, true, $language)) {
                            $this->verbose('[c2a getYear 6]');
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
                if (Str::startsWith($word, $this->nameSuffixes)) {
                    $this->verbose('[convertToAuthors 20]');
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
                        $k = 0;
                        foreach ($nameWords as $j => $nameWord) {
                            if (substr($nameWord, -1) == ',') {
                                $fullName .= $nameWord . ' ' . rtrim($word, ',') . ',';
                                $k = $j;
                                break;
                            }
                        }
                        // Put the rest of the names after Jr.
                        foreach ($nameWords as $m => $nameWord) {
                            if ($m != $k) {
                                $fullName .= ' ' . $nameWord;
                            }
                        }
                        $namePart = 0;
                        $authorIndex++;
                    }
                    $this->verbose('Name with Jr., Sr., or III; fullName: ' . $fullName);
                } else {
                    $this->verbose('[convertToAuthors 23]');
                    // Don't rtrim '}' because it could be part of the name: e.g. Oblo{\v z}insk{\' y}.
                    // Don't trim comma from word before Jr. etc, because that is valuable info
                    $trimmedWord = (isset($words[$i+1]) && Str::startsWith($words[$i+1], $this->nameSuffixes)) ? $word : rtrim($word, ',;');
                    $nameComponent = $this->spaceOutInitials($trimmedWord);
                    $fullName .= " " . $nameComponent;
                }

                // $bareWords is array of words at start of $remainingWords that don't end end in ','
                // or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
                $bareWords = $this->bareWords($remainingWords, false, $language);
                // If 'and' has not already occurred ($hasAnd is false), its occurrence in $barewords is compatible
                // with $barewords being part of the authors' names OR being part of the title, so should be ignored.
                $nameScore = $this->nameScore($bareWords, !$hasAnd);
                $this->verbose("bareWords (no trailing punct, not year in parens): " . implode(' ', $bareWords));
                $this->verbose("nameScore: " . $nameScore['score']);
                if ($nameScore['count']) {
                    $this->verbose('[convertToAuthors 24]');
                    $this->verbose('nameScore per word: ' . number_format($nameScore['score'] / $nameScore['count'], 2));
                }

                // if (isset($bareWords[0]) && $bareWords[0] == 'Layer') {
                //     dd($determineEnd, $remainingWords, $bareWords, 
                //     $this->inDict(trim($remainingWords[0], ',')), 
                //     ! $this->isInitials(trim($remainingWords[0], ',')),
                //     ! in_array(trim($remainingWords[0], ','), $this->nameSuffixes),
                //     ! preg_match('/[0-9]/', $remainingWords[0]),
                //     ! empty($remainingWords[1]),
                //     $this->inDict($remainingWords[1]),
                //     ! $this->isInitials(trim($remainingWords[1], ',')),
                //     ! in_array(trim($remainingWords[1], ','), $this->nameSuffixes),
                //     ! preg_match('/[0-9]/', $remainingWords[1]),
                //     $remainingWords[1] != '...', 
                //     ! in_array($remainingWords[1][0], ["'", "`"]),
                //     ! empty($remainingWords[2]), 
                //     $this->inDict($remainingWords[2]),
                //     ! $this->isInitials(trim($remainingWords[2], ',')),
                //     ! in_array(trim($remainingWords[2], ','), $this->nameSuffixes),
                //     ! preg_match('/[0-9]/', $remainingWords[2]));
                // }

                if ($determineEnd && $text = $this->getQuotedOrItalic(implode(" ", $remainingWords), true, false, $before, $after)) {
                    if (in_array($text, ['et al', 'et al.', 'et. al.'])) {
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
                        $this->verbose('[convertToAuthors 26]');
                        $remainder = implode(" ", $remainingWords);
                        $done = true;
                        $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                        $case = 7;
                    }
                } elseif ($determineEnd && $year = $this->getYear(implode(" ", $remainingWords), $remainder, $month, $day, $date, true, true, $language)) {
                    $this->verbose('[convertToAuthors 14a] Ending author string: word is "'. $word . '", year is next.');
                    $done = true;
                    $fullName = ($fullName[0] != ' ' ? ' ' : '') . $fullName;
                    $this->addToAuthorString(10, $authorstring, $this->formatAuthor($fullName));
                } elseif (
                        $determineEnd
                        &&
                        isset($remainingWords[0])
                        &&
                        ! $this->isAnd($remainingWords[0], $language)
                        && 
                        (
                            count($bareWords) > 3
                            ||
                            (
                                $this->inDict(trim($remainingWords[0], ',')) 
                                && ! $this->isInitials(trim($remainingWords[0], ','))
                                && ! in_array(trim($remainingWords[0], ','), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[0])
                                && ! empty($remainingWords[1]) 
                                && $this->inDict($remainingWords[1]) 
                                && ! $this->isInitials(trim($remainingWords[1], ','))
                                && ! in_array(trim($remainingWords[1], ','), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[1])
//                                && strtolower($remainingWords[1][0]) == $remainingWords[1][0] 
                                && $remainingWords[1] != '...' 
                                && ! in_array($remainingWords[1][0], ["'", "`"])
                                && ! empty($remainingWords[2]) 
                                && $this->inDict($remainingWords[2])
                                && ! $this->isInitials(trim($remainingWords[2], ','))
                                && ! in_array(trim($remainingWords[2], ','), $this->nameSuffixes)
                                && ! preg_match('/[0-9]/', $remainingWords[2])
//                                && strtolower($remainingWords[2][0]) == $remainingWords[2][0]
                            )
                        )
                        &&
                        (
                            $nameScore['count'] == 0 ||
                            $nameScore['score'] / $nameScore['count'] < 0.25 ||
                            (isset($bareWords[1]) && mb_strtolower($bareWords[1]) == $bareWords[1] && ! $this->isAnd($bareWords[1], $language) && ! in_array($bareWords[1], $this->vonNames))
                        )
                        &&
                        (
                            ! $this->isInitials($remainingWords[0])
                            ||
                            ($remainingWords[0] == 'A' && isset($remainingWords[1]) && $remainingWords[1][0] == strtolower($remainingWords[1][0]))
                        )
                    ) {
                    // Low nameScore relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                    // Note that this check occurs only when $namePart > 0---so it rules out double-barelled
                    // family names that are not followed by commas.  ('Paulo Klinger Monteiro, ...' is OK.)
                    // Cannot set limit to be > 1 bareWord, because then '... Smith, Nancy Lutz and' gets truncated
                    // at comma.
                    //dd($remainingWords[0], $this->inDict($remainingWords[0]), in_array($remainingWords[0], $this->dictionaryNames));
                    $this->verbose('[convertToAuthors 28]');
                    $done = true;
                    $this->addToAuthorString(11, $authorstring, $this->formatAuthor($fullName));
                } elseif ($nameComplete && Str::endsWith($word, [',', ';']) && isset($words[$i + 1]) && ! $this->isEd($words[$i + 1])) {
                    // $word ends in comma or semicolon and next word is not string for editors
                    if ($hasAnd) {
                        $this->verbose('[convertToAuthors 29]');
                        // $word ends in comma or semicolon and 'and' has already occurred
                        // To cover the case of a last name containing a space, look ahead to see if next words
                        // are initials or year.  If so, add back comma taken off above and continue.  Else done.
                        if ($i + 3 < count($words)
                            &&
                            (
                                ($this->isInitials($words[$i + 1]) && $namePart == 0)
                                || $this->getYear($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, $language)
                                || ($this->isInitials($words[$i + 2]) && $this->getYear($words[$i + 3], $trash, $trash1, $trash2, $trash3, true, true, $language))
                            )
                        ) {
                            $fullName .= ',';
                        } else {
                            $done = true;
                            $this->addToAuthorString(12, $authorstring, $this->formatAuthor($fullName));
                            $case = 11;
                        }
                    } elseif (substr($words[$i+1],-1) != ',' && isset($words[$i+2]) && $this->isAnd($words[$i+2], $language)) {
                        // $nameComplete and next word does not end in a comma and following work is 'and'
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
                        $this->verbose('[c2a getYear 9]');
                        if (!$prevWordHasComma && $i + 2 < count($words)
                                && (
                                    $this->getYear($words[$i + 2], $trash, $trash1, $trash2, $trash3, true, true, $language)
                                    ||
                                    $this->getQuotedOrItalic($words[$i + 2], true, false, $before, $after)
                                )) {
                            $this->verbose('[convertToAuthors 31]');
                            $fullName .= ',';
                        } else {
                            // Low name score relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                            if ($nameScore['count'] > 2 && $nameScore['score'] / $nameScore['count'] < 0.25) {
                                //dump($nameScore);
                                $this->verbose('[convertToAuthors 32]');
                                $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            // publication info must take at least 7 words [although it may already have been removed],
                            // so with author name there must be atleast 9 words left for author to be added.
                            // (Applies mostly to books with short titles.)  However, if next word is "and", that definitely
                            // is not the start of the title.
                            } elseif ($type == 'authors' && count($remainingWords) < 9 && isset($remainingWords[0]) && ! $this->isAnd($remainingWords[0]) && $determineEnd) {
                                $this->verbose('[convertToAuthors 33]');
                                $this->addToAuthorString(15, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
                            }
                            $this->verbose('[convertToAuthors 34]');
                            $case = 12;
                        }
                    }
                } else {
                    $this->verbose('[convertToAuthors 35]');
                    if ($wordIsVon) {
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name, so 'namePart' not incremented");
                    } else {
                        $namePart++;
                    }
                    if ($i + 1 == count($words)) {
                        $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                    }
                }
            }
        }

        return ['authorstring' => $authorstring, 'warnings' => $warnings, 'oneWordAuthor' => $oneWordAuthor];
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
      * @param $start boolean (if true, check only for substring at start of string)
      * @param $italicsOnly boolean (if true, get only italic string, not quoted string)
      * @param $before part of $string preceding left delimiter and matched text
      * @param $after part of $string following matched text and right delimiter
      * @return $matchedText: quoted or italic substring
      */
    private function getQuotedOrItalic(string $string, bool $start, bool $italicsOnly, string|null &$before, string|null &$after): string|bool
    {
        $matchedText = $quotedText = $beforeQuote = $afterQuote = '';

        /* 
         * Rather than using the following loop, could use regular expressions.  Versions of expressions
         * are given in a comment after the loop.  However, these expressions are incomplete, and are complex
         * because of the need to exclude escaped quotes.  I find the loop easier to understand and maintain.
         * NOTE: cleanText replaces French guillemets and other quotation marks with `` and ''.
        */
        if (!$italicsOnly) {
            $skip = false;
            $begin = '';
            $end = false;

            $chars = str_split($string);

            foreach ($chars as $i => $char) {
                if ($skip) {
                    $skip = false;
                // after match has ended
                } elseif ($end) {
                    $afterQuote .= $char;
                // inside match
                } elseif ($begin == '``' || $begin == "''" || $begin == '"') {
                    if ($char == "'" && $chars[$i-1] != '\\' && $chars[$i+1] && $chars[$i+1] == "'") {
                        $end = true;
                        $skip = true;
                    } elseif ($char == '"' && $chars[$i-1] != '\\') {
                        $end = true;
                    } else {
                        $quotedText .= $char;
                    }
                } elseif ($begin == '`' || $begin == "'") {
                    if ($char == "'" && $chars[$i-1] != '\\' 
                                && (! isset($chars[$i+1]) || ! in_array(strtolower($chars[$i+1]), range('a', 'z')))) {
                        $end = true;
                    } else {
                        $quotedText .= $char;
                    }
                // before match has begun
                } elseif ($char == '`') {
                    if ((! isset($chars[$i-1]) || $chars[$i-1] != '\\') && isset($chars[$i+1]) && $chars[$i+1] == "`") {
                        $begin = '``';
                        $skip = true;
                    } elseif (! isset($chars[$i-1]) || $chars[$i-1] != '\\') {
                        $begin = '`';
                    } else {
                        $beforeQuote .= $char;
                    }
                } elseif ($char == "'") {
                    if (($i == 0 || $chars[$i-1] != '\\') && $chars[$i+1] == "'") {
                        $begin = "''";
                        $skip = true;
                    } elseif ($i == 0 || $chars[$i-1] == ' ') {
                        $begin = "'";
                    } else {
                        $beforeQuote .= $char;
                    }
                } elseif ($char == '"') {
                    if ($i == 0 || $chars[$i-1] != '\\') {
                        $begin = '"';
                    } else {
                        $beforeQuote .= $char;
                    }
                } else {
                    $beforeQuote .= $char;
                }
            }
        }

        $before = $beforeQuote;
        $after = $afterQuote;

        /*
        $matchedText = false;
        $quotedText = $italicText = false;
        $beforeQuote = $afterQuote = null;

        // Possible regular expressions to replace code above.  However, they need work: they do not cover all cases
        // and e.g. do not detect quote in string that starts with ".
        if (!$italicsOnly) {
            $quoteRegExps = [];
            // ``...''
            $quoteRegExps[0] = "/^(.*?)``(.*?)''(.*)$/";
            // ''...''
            $quoteRegExps[1] = "/^(.*?)''(.*?)''(.*)$/";
            // "..."
            $quoteRegExps[2] = '/^(.*?[^\\\\])"(.*?[^\\\\])"(.*)$/';
            // '...'
            $quoteRegExps[3] = "/^(.*? )'([^'].*?[^\\\\])'([^a-zA-Z].*)$/";
            // `...'
            $quoteRegExps[4] = "/^(.*? )`([^'].*?[^\\\\])'([^a-zA-Z].*)$/";

            // Find which pattern matches first, if any
            $results = [];
            $quoteStartIndex = strlen($string);
            // key on $quoteRegExps that matches; -1 means no matches
            $matchKey = -1;
            foreach ($quoteRegExps as $i => $regExp) {
                $results[$i] = preg_match($regExp, $string, $match, PREG_OFFSET_CAPTURE);
                $matches[$i] = $match;
                if ($results[$i] && $matches[$i][2][1] < $quoteStartIndex) {
                    $quoteStartIndex = $matches[$i][2][1];
                    $matchKey = $i;
                }
            }

            //$quoteExists = in_array(1, $results);
            if ($matchKey >= 0) {
                $quotedText = $matches[$matchKey][2][0];
                $beforeQuote = $matches[$matchKey][1][0];
                $afterQuote = $matches[$matchKey][3][0];
            }
        }
        */

        $italicText = $this->getStyledText($string, $start, 'italics', $beforeItalics, $afterItalics, $remains);

        if ($italicsOnly) {
            if ($italicText && (!$start || strlen($beforeItalics) == 0)) {
                $before = $beforeItalics;
                $after = $afterItalics;
                $matchedText = $italicText;
            }
        } elseif ($quotedText && $italicText) {
            $quoteFirst = strlen($beforeQuote) < strlen($beforeItalics);
            $before =  $quoteFirst ? $beforeQuote : $beforeItalics;
            $after= $quoteFirst ? $afterQuote : $afterItalics;
            if (!$start || strlen($before) == 0) {
                $matchedText = $quoteFirst ? $quotedText : $italicText;
            }
        } elseif ($quotedText) {
            $before = $beforeQuote;
            $after = $afterQuote;
            if (!$start || strlen($before) == 0) {
                $matchedText = $quotedText;
            }
        } elseif ($italicText) {
            $before = $beforeItalics;
            $after = $afterItalics;
            if (!$start || strlen($before) == 0) {
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
     * getYear: get *last* substring in $string that is a year, unless $start is true, in which case restrict to 
     * start of string and take only first match
     * @param string $string 
     * @param string|null $remains what is left of the string after the substring is removed
     * @param string|null $month
     * @param boolean $start = true (if true, check only for substring at start of string)
     * @param boolean $allowMonth = false (allow string like "(April 1998)" or "(April-May 1998)" or "April 1998:"
     * @return string year
     */
    private function getYear(string $string, string|null &$remains, string|null &$month, string|null &$day, string|null &$date, bool $start = true, bool $allowMonth = false, string $language = 'en'): string
    {
        $year = '';
        $remains = $string;
        $months = $this->monthsRegExp[$language];

        if (Str::startsWith($remains, ['(n.d.)', '[n.d.]'])) {
            $remains = substr($remains, 6);
            $year = 'n.d.';
            return $year;
        }

        if ($allowMonth) {
            if (
                // (year month) or (year month day) (or without parens or with brackets)
                preg_match('/^ ?[\(\[]?(?P<date>(?P<year>(18|19|20)[0-9]{2}),? (?P<month>' . $months . ') ?(?P<day>[0-9]{1,2})?)[\)\]]?/i', $string, $matches1)
                ||
                // (day month year) or (month year) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Spanish
                preg_match('/^ ?[\(\[]?(?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>' . $months . ') ?(de )?(?P<year>(18|19|20)[0-9]{2}))[\)\]]?/i', $string, $matches1)
                ||
                // (day monthNumber year) or (monthNumber year) (or without parens or with brackets)
                // The optional "de" between day and month and between month and year is for Spanish
                preg_match('/^ ?[\(\[]?(?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>[0-9]{1,2}) ?(de )?(?P<year>(18|19|20)[0-9]{2}))[\)\]]?/i', $string, $matches1)
                ) {
                $year = $matches1['year'] ?? null;
                $month = $matches1['month'] ?? null;
                $day = $matches1['day'] ?? null;
                $date = $matches1['date'] ?? null;
                $remains = substr($remains, strlen($matches1[0]));
                return $year;
            }
        }

        if (!$start && $allowMonth) {
            if (
                // <year> <month> <day>?
                preg_match('/[ \(](?P<date>(?P<year>(18|19|20)[0-9]{2}) (?P<month>' . $months . ')( ?(?P<day>[0-3][0-9]))?)/i', $string, $matches2, PREG_OFFSET_CAPTURE)
                ||
                // <day>? <month> <year>
                // The optional "de" between day and month and between month and year is for Spanish
                preg_match('/[ \(](?P<date>(?P<day>[0-9]{1,2})? ?(de )?(?P<month>' . $months . ') ?(de )?(?P<year>(18|19|20)[0-9]{2}))/i', $string, $matches2, PREG_OFFSET_CAPTURE)
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

        // Year can be (1980), [1980], '1980 ', '1980,', '1980.', '1980)', '1980:' or end with '1980' if not at start and
        // (1980), [1980], ' 1980 ', '1980,', '1980.', or '1980)' if at start; instead of 1980, can be of form
        // 1980/1 or 1980/81 or 1980/1981 or 1980-1 or 1980-81 or 1980-1981
        // NOTE: '1980:' could be a volume number---might need to check for that
        $monthRegExp = '((' . $months . ')([-\/](' . $months . '))?)?';
        // In following line, [1-2]?[0-9]? added to allow second year to have four digits.  Should be (18|19|20), but that
        // would mean adding a group, which would require the recalculation of all the indices ...
        $yearRegExp = '((18|19|20)([0-9]{2})(-[1-2]?[0-9]?[0-9]{1,2}|\/[0-9]{1,4})?)[a-z]?';
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

        $regExp = '/' . $regExp . '/';

        if ($start) {
            preg_match($regExp, $string, $matches, PREG_OFFSET_CAPTURE);
        } else {
            preg_match_all($regExp, $string, $matches, PREG_OFFSET_CAPTURE);
        }

        // Using labels for matches seems non-straightforward because the patterns are using more than once in each
        // regular expression.
        // These are the indexes of the matches for the subpatterns of the regular expression:
        if ($allowMonth) {
            $yearIndexes = $start ? [6, 15, 24] : [6, 15, 24, 33];
        } else {
            $yearIndexes = $start ? [2, 7, 12] : [2, 7, 12, 17];
        }

        foreach ($yearIndexes as $i) {
            if (isset($matches[$i]) && count($matches[$i])) {
                if (!$start) {
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
                    if (!$start) {
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
            $colonPos = strpos($string, ':');
            for ($j = $colonPos; $j > 0 and $string[$j] != ')' and $string[$j] != '('; $j--) {

            }
            if ($string[$j] == '(') {
                $remainder = substr($string, 0, $j);
                $string = substr($string, $j + 1);
            } else {
                $remainder = '';
            }
            $address = rtrim(ltrim(substr($string, 0, strpos($string, ':')), ',. '), ': ');
            $publisher = trim(substr($string, strpos($string, ':') + 1), ',.: ');
        // else if string contains no colon and at least one ',', take publisher to be string
        // preceding first colon and and city to be rest
        } elseif (!substr_count($string, ':') and substr_count($string, ',')) {
            $publisher = trim(substr($string, 0, strpos($string, ',')), ',. ');
            $address = trim(substr($string, strpos($string, ',') + 1), ',.: ');
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

        if (! preg_match('/' . $this->proceedingsExceptions . '/i', $string) 
                && preg_match('/' . $this->proceedingsRegExp . '/i', $string)) {
            $isProceedings = true;
        }

        return $isProceedings;
    }

    /**
     * bareWords: in array $words of strings, report the elements at the start up until one ends
     * in ',' or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
     * or, if $stopAtAnd is true, is 'and'.
     */
    private function bareWords(array $words, bool $stopAtAnd, string $language = 'en'): array
    {
        $barewords = [];
        foreach ($words as $j => $word) {
            $stop = false;
            $endsWithPunc = false;
            $include = true;

            if (Str::endsWith($word, ['.', ',', ')', ':', '}'])) {
                $stop = true;
                $endsWithPunc = true;
            }
            if (preg_match('/(\(|\[)?(18|19|20)([0-9][0-9])(\)|\])?/', $word)) {
                $stop = true;
                $include = false;
            }
            if (Str::startsWith($word, ['`', '"', "'"])) {
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
        return $barewords;
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
                    ($lcword != 'and' || ! $ignoreAnd) &&
                    ((isset($word[0]) && mb_strtoupper($word[0]) == $word[0]) || in_array($word, $this->vonNames)) &&
                    ! $this->isInitials($word) &&
                    ! in_array($word, $this->dictionaryNames)
                ) {
                $wordsToCheck[] = $lcword;
                if (in_array($lcword, $this->stopwords)) {
                    $score -= 2;
                }
                if (in_array($word, $this->vonNames)) {
                    $score += 2;
                }
            }
        }
        $string = implode(' ', $wordsToCheck);
        // Number of words in string not in dictionary
        $score += iterator_count($aspell->check($string, ['en_US']));
        
        $returner = ['count' => count($words), 'score' => $score];

        return $returner;
    }

    /*
     * Determine whether $word is in the dictionary
     */
    private function inDict(string $word, bool $excludeNames = true): bool
    {
        $aspell = Aspell::create();
        // strtolower to exclude proper names (e.g. Federico is in dictionary)
        $inDict = 0 == iterator_count($aspell->check(strtolower($word)));
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
        $this->verbose(['text' => 'Arguments of isNotName: ', 'words' => [$words[0], $words[1]]]);
        $result = false;
        // Following reg exp allows {\'A} and \'{A} and \'A (but also allows {\'{A}, which it shouldn't)
        $accentRegExp = '/(^\{?(\\\"|\\\\\'|\\\`|\\\\\^|\\\H|\\\v|\\\~|\\\k|\\\c|\\\\\.)\{?[A-Z]\}?|^\{\\\O\})/';
        
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
                    && ! in_array($words[$i], $this->vonNames)
                ) {
                $this->verbose(['text' => 'isNotName: ', 'words' => [$words[$i]], 'content' => ' appears not to be a name']);
                return true;
            }
        }

        $this->verbose(['text' => 'isNotName: ', 'words' => [$word1, $word2], 'content' => ' appear to be names']);
        return $result;
    }

    /**
     * Regularize A.B. or A. B. to A. B. (but keep A.-B. as it is)
     * @param $string string
     */
    private function spaceOutInitials(string $string): string
    {
        return preg_replace('/\.([^ -])/', '. $1', $string);
    }

    /**
     * Normalize format to Smith, A. B. or A. B. Smith or Smith, Alan B. or Alan B. Smith.
     * In particular, change Smith AB to Smith, A. B. and A.B. SMITH to A. B. Smith
     * $nameString is a FULL name (e.g. first and last or first middle last)
     */
    private function formatAuthor(string $nameString): string
    {
        $this->verbose(['text' => 'formatAuthor: argument ', 'words' => [$nameString]]);

        $nameString = str_replace('..', '.', $nameString);

        $namesRaw = explode(' ', $nameString);
        
        // $initialsStart is index of component (a) that is initials and (b) after which all components are initials
        // initials are any string for which all letters are u.c. and at most two characters that are
        // letter or period
        $initialsStart = count($namesRaw);
        $allUppercase = true;
        $names = [];
        foreach ($namesRaw as $k => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsMaxStringLength = 2; // initials could be 'A' or 'AB' or 'A.'
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
        $initialPassed = false;

        $lettersOnlyNameString = preg_replace("/[^A-Za-z]/", '', $nameString);
        if (strtoupper($lettersOnlyNameString) != $lettersOnlyNameString) {
            $allUppercase = false;
        }

        foreach ($names as $i => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            if ($i) {
                $fName .= ' ';
            }
            if (strpos($name, '.') !== false) {
                $initialPassed = true;
            }

            // If name (all components) is not ALL uppercase, there are fewer than 3 letters
            // in $name or a comma has occurred, and all letters in the name are uppercase, assume $name
            // is initials.  Put periods and spaces as appropriate.
            if (! $allUppercase && (strlen($lettersOnlyName) < 3 || $commaPassed) 
                        && strtoupper($lettersOnlyName) == $lettersOnlyName && $lettersOnlyName != 'III') {
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
            } elseif (strtoupper($lettersOnlyName) == $lettersOnlyName && strpos($name, '.') === false && $lettersOnlyName != 'III') {
                $fName .= ucfirst(mb_strtolower($name));
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
    private function getJournal(string &$remainder, object &$item, bool $italicStart, bool $pubInfoStartsWithForthcoming, bool $pubInfoEndsWithForthcoming): string
    {
        if ($italicStart) {
            // (string) on next line to stop VSCode complaining
            $italicText = (string) $this->getQuotedOrItalic($remainder, true, false, $before, $after);
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

        if ($pubInfoStartsWithForthcoming && !$containsDigit) {
            // forthcoming at start
            $result = $this->extractLabeledContent($remainder, $this->startForthcomingRegExp, '.*', true);
            $journal = $this->getQuotedOrItalic($result['content'], true, false, $before, $after);
            if (!$journal) {
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
            $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . rtrim($result['content'], ')'), 'getJournal 2');
        } else {
            $words = $remainingWords = explode(' ', $remainder);
            $initialWords = [];
            foreach ($words as $key => $word) {
                $initialWords[] = $word;
                array_shift($remainingWords);
                $remainder = implode(' ', $remainingWords);
                if ($key === count($words) - 1 // last word in remainder
                    || Str::contains($words[$key+1], range('1', '9')) // next word contains a digit
                    || preg_match('/^[IVXLCD]*$/', $words[$key+1]) // next word is Roman number
                    || preg_match($this->volRegExp2, $remainder) // followed by volume info
                    || preg_match($this->startPagesRegExp, $remainder) // followed by pages info
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
    private function getVolumeNumberPagesForArticle(string &$remainder, object &$item): void
    {
        $remainder = trim($this->regularizeSpaces($remainder), ' ;.,\'');
        // First check for some common patterns
        $number = '[a-z]?[1-9][0-9]{0,5}[A-Za-z]?';
        $numberWithRoman = '([1-9][0-9]{0,3}|[IVXLCD]{1,6})';
        $letterNumber = '([A-Z]{1,3})?-?' . $number;
        $numberRange = $number . '((--?-?|_)' . $number . ')?';
        $numberRangeWithRoman = $numberWithRoman . '((--?-?|_)' . $numberWithRoman . ')?';
        $pages = '[Pp]p?( |\. )|[Pp]ages |s.\ ?';
        $volumeRx = '('. $this->volumeRegExp . ')?(?P<vol>' . $numberRange . ')';
        $volumeWithRomanRx = '('. $this->volumeRegExp . ')?(?P<vol>' . $numberRangeWithRoman . ')';
        $numberRx = '('. $this->numberRegExp . ')?(?P<num>' . $numberRange . ')';
        $volumeWordRx = '('. $this->volumeRegExp . ')(?P<vol>' . $numberRange . ')';
        // Letter in front of volume is allowed only if preceded by "vol(ume)" and is single number
        $volumeWordLetterRx = '('. $this->volumeRegExp . ')(?P<vol>' . $letterNumber . ')';
        $numberWordRx = '('. $this->numberRegExp . ')(?P<num>' . $numberRange . ')';
        $pagesRx = '('. $pages . ')?(?P<pp>' . $numberRange . ')';
        $punc1 = '(}? |, ?| ?: ?| ?\()';
        $punc2 = '(\)?[ :] ?|\)?, ?| ?: ?)';

        // e.g. Volume 6, No. 3, pp. 41-75 OR 6(3) 41-75
        if (preg_match('/^' . $volumeWithRomanRx . $punc1 . $numberRx . $punc2 . $pagesRx . '/', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 1');
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 2');
            $this->setField($item, 'pages', str_replace(['---', '--', '_'], '-', $matches['pp']), 'getVolumeNumberPagesForArticle 3');
            $remainder = '';
        // e.g. Volume 6, 41-75$ OR 6 41-75$
       } elseif (preg_match('/^' . $volumeRx . $punc1 . $pagesRx . '$/', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 4');
            if (str_contains($matches['pp'], '-') || str_contains($matches['pp'], '_') || strlen($matches['pp']) < 6) {
                $this->setField($item, 'pages', str_replace(['---', '--', '_'], '-', $matches['pp']), 'getVolumeNumberPagesForArticle 5a');
            } else {
                $this->addToField($item, 'note', 'Article ' . $matches['pp'], 'getVolumeNumberPagesForArticle 5b');
            }
            $remainder = '';
        // e.g. Volume 6, No. 3 
        } elseif (preg_match('/^' . $volumeWordRx . $punc1 . $numberWordRx . '$/', $remainder, $matches)) {
            $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 6');
            $this->setField($item, 'number', str_replace(['---', '--'], '-', $matches['num']), 'getVolumeNumberPagesForArticle 7');
            $remainder = '';
        // e.g. Volume A6, 41-75$
        } elseif (preg_match('/^' . $volumeWordLetterRx . $punc1 . $pagesRx . '$/', $remainder, $matches)) {
               $this->setField($item, 'volume', str_replace(['---', '--'], '-', $matches['vol']), 'getVolumeNumberPagesForArticle 8');
               $this->setField($item, 'pages', str_replace(['---', '--', '_'], '-', $matches['pp']), 'getVolumeNumberPagesForArticle 9');
               $remainder = '';
        } else {
            // If none of the common patterns fits, fall back on approach that first looks for a page range then
            // uses the method getVolumeAndNumberForArticle to figure out the volume and number, if any
            $numberOfMatches = preg_match_all('/' . $this->pagesRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE);
            if ($numberOfMatches) {
                $matchIndex = $numberOfMatches - 1;
                $this->verbose('[p0] matches: 1: ' . $matches[1][$matchIndex][0] . '; 2: ' . $matches[2][$matchIndex][0] . '; 3: ' . $matches[3][$matchIndex][0]);
                $this->verbose("Number of matches for a potential page range: " . $numberOfMatches);
                $this->verbose("Match index: " . $matchIndex);
                $this->setField($item, 'pages', str_replace(['---', '--', ' '], ['-', '-', ''], $matches[3][$matchIndex][0]), 'getVolumeNumberPagesForArticle 10');
                $take = $matches[0][$matchIndex][1];
                $drop = $matches[3][$matchIndex][1] + strlen($matches[3][$matchIndex][0]);
            } else {
                $item->pages = '';
                $take = 0;
                $drop = 0;
            }

            $remainder = rtrim(substr($remainder, 0, $take) . substr($remainder, $drop), ',.: ');
            $remainder = trim($remainder, ',. ');
        }
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
            $number = '[a-z]?[1-9][0-9]{0,5}[A-Za-z]?';
            $numberRange = $number . '((--?-?|_)' . $number . ')';
            if (preg_match('/^' . $numberRange . '$/', $remainder, $matches)) {
                $this->setField($item, 'pages', str_replace(['---', '--', '_'], '-', $remainder), 'getVolumeAndNumberForArticle 3a');
                $this->verbose('[p3a] pages: ' . $item->pages);
            } elseif ($remainder && ctype_digit($remainder)) {
                if (strlen($remainder) < 7) {
                    $this->setField($item, 'pages', $remainder, 'getVolumeAndNumberForArticle 3b');  // could be a single page
                    $this->verbose('[p3b] pages: ' . $item->pages);
                } else {
                    $this->setField($item, 'note', (isset($item->note) ? $item->note . ' ' : '') . 'Article ' . $remainder, 'getVolumeAndNumberForArticle 3c');  // could be a single page
                    $this->verbose('[p3c] note: ' . $item->note);
                }
                $remainder = '';
            }
        } else {
            // $item->number can be a range (e.g. '6-7')
            // Look for something like 123:6-19
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
                // A letter or sequence of letters is permitted after an issue number
                $numberOfMatches = preg_match('/(' . $this->volumeRegExp . '|[^0-9]|^)(?P<volume>[1-9][0-9]{0,3})(?P<punc1> ?, |\(| | \(|\.|:|;)(?P<numberDesignation>' . $this->numberRegExp . ')? ?(?P<number>([0-9]{1,20}[a-zA-Z]*)(-[1-9][0-9]{0,6})?)\)?/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                $numberInParens = isset($matches['punc1']) && in_array($matches['punc1'][0], ['(', ' (']);
                if ($numberOfMatches) {
                    $this->verbose('[p2b] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0] . ', 3: ' . $matches[3][0] . ', 4: ' . $matches[4][0] . ', 5: ' . $matches[5][0] . (isset($matches[6][0]) ? ', 6: ' . $matches[6][0] : '') . (isset($matches[7][0]) ? ', 7: ' . $matches[7][0] : '') . (isset($matches[8][0]) ? ', 8: ' . $matches[8][0] : ''));
                    $this->setField($item, 'volume', $matches['volume'][0], 'getVolumeAndNumberForArticle 14');
                    if (strlen($matches['number'][0]) < 5) {
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
                            if (Str::startsWith($matches[3][0], ['Article', 'article'])) {
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

    private function cleanText(string $string, string|null $charEncoding): string
    {
        $string = str_replace("\\newblock", "", $string);
        $string = str_replace("\\newpage", "", $string);
        // Replace each tab with a space
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\\textquotedblleft ", "``", $string);
        $string = str_replace("\\textquotedblleft{}", "``", $string);
        $string = str_replace("\\textquotedblright ", "''", $string);
        $string = str_replace("\\textquotedblright", "''", $string);

        if ($charEncoding == 'utf8' || $charEncoding == 'utf8leave') {
            // Replace non-breaking space with regular space
            $string = str_replace("\xC2\xA0", " ", $string);
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
