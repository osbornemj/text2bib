<?php

namespace App\Services;

use Carbon\Carbon;

use Illuminate\Support\Str;

use App\Models\City;
use App\Models\Conversion;
use App\Models\ExcludedWord;
use App\Models\Name;
use App\Models\Publisher;
use App\Models\VonName;

use App\Traits\Stopwords;

use PhpSpellcheck\Spellchecker\Aspell;

class Converter
{
    var $boldCodes;
    var $articleRegExp;
    var $bookTitleAbbrevs;
    var $cities;
    var $displayLines;
    var $editionRegExp;
    var $editorStartRegExp;
    var $editorRegExp;
    var $edsRegExp1;
    var $edsRegExp2;
    var $edsRegExp3;
    var $edsRegExp4;
    var $endForthcomingRegExp;
    var $excludedWords;
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
    var $isbnRegExp;
    var $italicCodes;
    var $italicTitle;
    var $itemType;
    var $journalWord;
    var $masterRegExp;
    var $monthsRegExp;
    var $names;
    var $numberRegExp1;
    var $oclcRegExp;
    var $ordinals;
    var $pagesRegExp1;
    var $pagesRegExp2;
    var $pagesRegExp3;
    var $phdRegExp;
    var $phrases;
    var $proceedingsRegExp;
    var $proceedingsExceptions;
    var $publishers;
    var $startForthcomingRegExp;
    var $thesisRegExp;
    var $volRegExp1;
    var $volRegExp2;
    var $volumeRegExp1;
    var $vonNames;
    var $workingPaperRegExp;
    var $workingPaperNumberRegExp;

    use StopWords;

    public function __construct()
    {
        $this->displayLines = [];
        $this->itemType = null;

        // words that are in dictionary but are abbreviations in journal names
        //$this->excludedWords = ['Trans', 'Ind', 'Int', 'Soc', 'Proc', 'Phys', 'Rev', 'Amer', 'Math', 'Meth', 'Geom', 'Univ', 'Nat', 'Sci',
        //'Austral'];
        $this->excludedWords = ExcludedWord::all()->pluck('word')->toArray();

        // Introduced to facilitate a variety of languages, but the assumption that the language of the 
        // citation --- though not necessarily of the reference itself --- is English pervades the code.
        $this->phrases = [
            'and' => 'and',
            'in' => 'in',
            'editor' => 'editor',
            'editors' => 'editors',
            'ed.' => 'ed.',
            'eds.' => 'eds.',
            'edited by' => 'edited by'
        ];

        $this->ordinals = ['1st', '2nd', '3rd', '4th', '5th'];

        $this->articleRegExp = 'article [0-9]*';

        $this->edsRegExp1 = '/\([Ee]ds?\.?\)|\([Ee]ditors?\)/';
        $this->edsRegExp2 = '/[Ee]dited by/';
        $this->edsRegExp3 = '/[Ee]ds?\.|^[Ee]ds?\.| [Ee]ditors?/';
        $this->edsRegExp4 = '/( [Ee]ds?[\. ]|\([Ee]ds?\.?\)| [Ee]ditors?| \([Ee]ditors?\))/';
        $this->editorStartRegExp = '/^\(?[Ee]dited by|^\(?[Ee]ds?\.?|^\([Ee]ditors?/';
        $this->editorRegExp = '/\([Ee]ds?\.?\)|\([Ee]ditors?\)/';

        $this->editionRegExp = '(1st|first|2nd|second|3rd|third|[4-9]th|[1-9][0-9]th|fourth|fifth|sixth|seventh) (rev\. |revised )?(ed\.|edition)';

        $this->volRegExp1 = '/,? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?\d/';
        $this->volRegExp2 = '/^[Vv]ol(\.|ume)? ?/';
        $this->volumeRegExp1 = '[Vv]olume ?|[Vv]ol\.? ?';

        $this->inRegExp1 = '/^[iI]n:? /';
        $this->inRegExp2 = '/( [iI]n: |, in| in\) )/';

        $this->startForthcomingRegExp = '^\(?forthcoming( at| in)?\)?|^in press|^accepted( at)?|^to appear in';
        $this->endForthcomingRegExp = '(forthcoming|in press|accepted|to appear)\.?\)?$';
        $this->forthcomingRegExp1 = '/^[Ff]orthcoming/';
        $this->forthcomingRegExp2 = '/^[Ii]n [Pp]ress/';
        $this->forthcomingRegExp3 = '/^[Aa]ccepted/';
        $this->forthcomingRegExp4 = '/[Ff]orthcoming\.?\)?$/';
        $this->forthcomingRegExp5 = '/[Ii]n [Pp]ress\.?\)?$/';
        $this->forthcomingRegExp6 = '/[Aa]ccepted\.?\)?$/';
        $this->forthcomingRegExp7 = '/^[Tt]o appear in/';

        $this->proceedingsRegExp = '[Pp]roceedings of |[Cc]onference on |[Ss]ymposium on |Proc\. *[Cc]onference ';
        $this->proceedingsExceptions = ['Proceedings of the National Academy', 'Proceedings of the Royal Society'];

        $this->thesisRegExp = '( [Tt]hesis| [Dd]issertation)';
        $this->fullThesisRegExp = '(PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral|[Mm]aster|MA|M\.A\.)( [Tt]hesis| [Dd]issertation)';
        $this->masterRegExp = '[Mm]aster|MA|M\.A\.';
        $this->phdRegExp = 'PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral';

        $this->inReviewRegExp1 = '/[Ii]n [Rr]eview\.?\)?$/';
        $this->inReviewRegExp2 = '/^[Ii]n [Rr]eview/';
        $this->inReviewRegExp3 = '/(\(?[Ii]n [Rr]eview\.?\)?)$/';

        $this->pagesRegExp1 = 'pp\.?|p\.';
        $this->pagesRegExp2 = 'pp\.?|[pP]ages?';
        $this->pagesRegExp3 = '/^pages |^pp\.?|^p\.|^p /i';

        $this->numberRegExp1 = '[Nn]os?\.?:?|[Nn]umbers?|[Ii]ssues?';

        $this->isbnRegExp = 'ISBN:? [0-9X]+';
        $this->oclcRegExp = 'OCLC:? [0-9]+';

        $this->journalWord = 'Journal';

        $this->bookTitleAbbrevs = ['Proc', 'Amer', 'Conf', 'Sci'];

        $this->workingPaperRegExp = '([Pp]reprint|[Ww]orking [Pp]aper|[Dd]iscussion [Pp]aper|[Tt]echnical [Rr]eport|'
                . '[Rr]esearch [Pp]aper|[Mi]meo|[Uu]npublished [Pp]aper|[Uu]npublished [Mm]anuscript|'
                . '[Uu]nder [Rr]eview)';
        $this->workingPaperNumberRegExp = ' (\\\\#|[Nn]umber|[Nn]o\.?)? ?(\d{1,5}),?/';

        $this->monthsRegExp = 'January|Jan\.?|February|Feb\.?|March|Mar\.?|April|Apr\.?|May|June|Jun\.?|July|Jul\.?|'
                . 'August|Aug\.?|September|Sept\.?|Sep\.?|October|Oct\.?|November|Nov\.?|December|Dec\.?';

        // Van: to deal with case like Van de Stahl, H.
        // la: to deal with de la Monte, for example
        //["de", "De", "der", "da", "das", "della", "la", "van", "Van", "von"];
        $this->vonNames = VonName::all()->pluck('name')->toArray();

        // The script will identify strings as cities and publishers even if they are not in these arrays---but the
        // presence of a string in one of the arrays helps when the elements of the reference are not styled in any way.
        //$this->cities = ['Berlin', 'Boston', 'Cambridge', 'Chicago', 'Greenwich', 'Heidelberg', 'London', 'New York', 'Northampton',
        //    'Oxford', 'Philadelphia',
        //    'Princeton', 'San Diego', 'Upper Saddle River', 'Washington'];
        $this->cities = City::all()->pluck('name')->toArray();
        
        // Springer-Verlag should come before Springer, so that if string contains Springer-Verlag, that is found
        //$this->publishers = ['Academic Press', 'Cambridge University Press', 'Chapman & Hall', 'Edward Elgar', 'Elsevier',
        //    'Harvard University Press', 'JAI Press', 'McGraw-Hill', 'MIT Press', 'Norton', 'Oxford University Press',
        //    'Prentice Hall', 'Princeton University Press', 'Princeton Univ. Press', 'Routledge', 'Springer-Verlag',
        //    'Springer', 'University of Pennsylvania Press', 'University of Pittsburgh Press',
        //    'Van Nostrand Reinhold', 'Wiley', 'Yale University Press'];
        $this->publishers = Publisher::all()->pluck('name')->toArray();
        
        //$this->names = ['American', 'Arrovian', 'Aumann', 'Bayes', 'Bayesian', 'Cournot', 'Gauss', 'Gaussian', 'German', 'Groves', 'Indian',
        //'Ledyard',
        //    'Lindahl', 'Markov', 'Markovian', 'Nash', 'Savage', 'U.S.', 'Walras', 'Walrasian'];
        $this->names = Name::all()->pluck('name')->toArray();
        
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
    public function convertEntry(string $rawEntry, Conversion $conversion): array|null
    {
        // $aspell = Aspell::create();
        // 'john' and 'martin' and 'smith' are in dictionary all lowercase
        // $x = iterator_count($aspell->check('Melissa John Robert Celia john harold martin Carolyn', ['en_US']));
        // dd($x);

        $warnings = $notices = [];

        // Remove comments and concatenate lines in entry
        // (do so before cleaning text, otherwise \textquotedbleft, e.g., at end of line will not be cleaned)
        $entryLines = explode("\n", $rawEntry);
        $entry = '';
        foreach ($entryLines as $line) {
            $truncated = $this->uncomment($line);
            $entry .= $line . (!$truncated ? ' ' : '');
        }

        if (!$entry) {
            return false;
        }

        $originalEntry = $entry;

        $entry = $this->cleanText($entry, $conversion->char_encoding);

        // remove numbers at start of entry, like '6.' or '[14]'.
        $entry = ltrim($entry, ' .0123456789[]()');

        $item = new \stdClass();
        $itemKind = null;
        $itemLabel = null;

        // If entry starts with '\bibitem', get label and remove '\bibitem'
        if (Str::startsWith($entry, ["\\bibitem{", "\\bibitem {"])) {
            if (!$conversion->override_labels) {
                $itemLabel = Str::betweenFirst($entry, '{', '}');
            }
            $entry = trim(Str::after($entry, '}'), '{}');
        } elseif (Str::startsWith($entry, "\\bibitem{}")) {
            $entry = Str::after($entry, '}');
        }

        $starts = ["\\noindent", "\\smallskip", "\\item", "\\bigskip"];
        foreach ($starts as $start) {
            if (Str::startsWith($entry, $start)) {
                $entry = trim(substr($entry, strlen($start)));
            }
        }

        if (!strlen($entry)) {
            return null;
        }

        // Don't put the following earlier---{} may legitimately follow \bibitem
        $entry = str_replace("{}", "", $entry);

        // If first component is authors and entry starts with [n] or (n) for some number n, eliminate it
        if ($conversion->first_component == 'authors') {
            $entry = preg_replace("/^\s*\[\d*\]|^\s*\(\d*\)/", "", $entry);
        }

        $entry = ltrim($entry, ' {');
        $entry = rtrim($entry, ' }');

        $this->verbose(['item' => strip_tags($entry)]);
        if ($itemLabel) {
            $this->verbose(['label' => strip_tags($itemLabel)]);
        }

        $phrases = $this->phrases;

        $isArticle = $containsPageRange = $containsProceedings = false;

        ////////////////////////
        // get the doi if any //
        ////////////////////////

        $doi = $this->extractLabeledContent($entry, ' doi:? | doi: ?|https?://dx.doi.org/|https?://doi.org/', '[a-zA-Z0-9/._\-]+');

        if ($doi) {
            $item->doi = $doi;
            if (!preg_match('/[0-9]+/', $doi)) {
                $warnings[] = 'The doi appears to be invalid.';
            }
            $this->verbose(['fieldName' => 'doi', 'content' => $item->doi]);
        } else {
            $this->verbose("No doi found.");
        }

        ///////////////////////////////
        // get the arXiv info if any //
        ///////////////////////////////

        $eprint = $this->extractLabeledContent($entry, ' arxiv: ?', '\S+');

        if ($eprint) {
            $item->archiveprefix = 'arXiv';
            $item->eprint = $eprint;
            $this->verbose(['fieldName' => 'arXiv info', 'content' => $item->archiveprefix . ':' . $item->eprint]);
        } else {
            $this->verbose("No arXiv info found.");
        }

        ////////////////////////
        // get the url if any //
        ////////////////////////

        // Entry contains "retrieved from http ... <access date>".
        // Assumes URL is at the end of entry.
        $urlAndAccessDate = $this->extractLabeledContent($entry, ' [Rr]etrieved from ', 'http\S+( .*)?$');

        $accessDate = '';
        if ($urlAndAccessDate) {
            if (Str::contains($urlAndAccessDate, ' ')) {
                $url = trim(Str::before($urlAndAccessDate, ' '), ',.;');
                $accessDate = trim(Str::after($urlAndAccessDate, ' '), '.');
            } else {
                $url = trim($urlAndAccessDate);
            }
        } else {
            // Entry ends 'http ... " followed by a string including 'retrieve' or 'access' or 'view'
            if (preg_match('/(http\S+ ).*((retrieve|access|view).*)$/', $entry, $matches)) {
                $url = trim($matches[1], ' ,.;()');
                $possibleDate = $matches[3] ? Str::after($matches[2], ' ') : $matches[2];
                $possibleDate = trim($possibleDate, ' ,.)');
                if ($this->isDate($possibleDate)) {
                    $accessDate = $possibleDate;
                } else {
                    $warnings[] = "The string \"" . $possibleDate . "\" remains unidentified.";
                }
                $entry = Str::before($entry, $matches[0]);
            } else {
                $urlPlus = $this->extractLabeledContent($entry, '', 'https?://\S+ .*$');
                $url = trim(Str::before($urlPlus, ' '), ',.;');
                $afterUrl = Str::after($urlPlus, ' ');
                if ($afterUrl) {
                    $warnings[] = "The string \"" . $afterUrl . "\" remains unidentified.";
                }
            }
        }

        if (isset($url) && $url) {
            $item->url = $url;
            $this->verbose(['fieldName' => 'URL', 'content' => $item->url]);
            if ($accessDate) {
                $item->urldate = $accessDate;
                $this->verbose(['fieldName' => 'URL access date', 'content' => $item->urldate]);
            }
        } else {
            $this->verbose("No url found.");
        }

        ///////////////////////////////////////
        // If entry starts with year, get it //
        ///////////////////////////////////////

        if ($conversion->first_component == 'year') {
            $this->verbose("Entry starts with year.");

            $result = preg_match('%^\D+(\d+)\D%', $entry, $matches, PREG_OFFSET_CAPTURE);

            if (!$result) {
                $this->verbose("No year found near start of entry.");
            } else {
                $year = $matches[1][0];
                $entry = trim(substr($entry, $matches[1][1] + strlen($year)), ' ');
                $item->year = $year;
                if (strlen($year) != 4) {
                    $warnings[] = "Year contains " . strlen($year) . " digits!";
                }
            }
        }

        //////////////////////
        // Look for authors //
        //////////////////////

        $words = explode(" ", $entry);
        /*
        $this->verbose("Words in entry are");
        foreach ($words as $word) {
            $this->verbose(['words' => [$word]]);
        }
        */

        $this->verbose("Looking for authors ...");

        $isEditor = false;

        $remainder = $entry;
        $conversion = $this->convertToAuthors($words, $remainder, $year, $isEditor, true, true);
        $authorstring = $conversion['authorstring'];
        $oneWordAuthor = $conversion['oneWordAuthor'];

        foreach ($conversion['warnings'] as $warning) {
            $warnings[] = $warning;
        }
        $authorstring = trim($authorstring, ',: ');
        $authorstring = $this->trimRightBrace($authorstring);
        if ($authorstring and $authorstring[0] == '{') {
            $authorstring = strstr($authorstring, ' ');
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
            $item->author = $authorstring;
        } else {
            $item->editor = trim(str_replace($editorPhrases, "", $authorstring), ' .,');
        }

        if (isset($item->author)) {
            $this->verbose(['fieldName' => 'Authors', 'content' => strip_tags($item->author)]);
        }

        if (isset($item->editor)) {
            $this->verbose(['fieldName' => 'Editors', 'content' => strip_tags($item->editor)]);
        }

        if ($year) {
            $item->year = $year;
        }

        $remainder = trim($remainder, '.}, ');
        $this->verbose("[1] Remainder: " . strip_tags($remainder));

        ////////////////////
        // Look for title //
        ////////////////////

        unset($this->italicTitle);

        $remainder = ltrim($remainder, ': ');
        $title = $this->getQuotedOrItalic($remainder, true, false, $newRemainder, $posStart, $posEnd);

        // Website
        if (isset($item->url) && $oneWordAuthor) {
            $itemKind = 'online';
            $title = trim($remainder);
            $newRemainder = '';
        }

        if (!$title) {
            $title = $this->getTitle($remainder, $edition, $volume, $isArticle);
            if ($edition) {
                $item->edition = $edition;
                $this->verbose(['fieldName' => 'Edition', 'content' => strip_tags($edition)]);
            }
            if ($volume) {
                $item->volume = $volume;
                $this->verbose(['fieldName' => 'Volume', 'content' => strip_tags($volume)]);
            }
            $newRemainder = $remainder;
        }

        $remainder = $newRemainder;
        $item->title = rtrim($title, '.');

        $this->verbose(['fieldName' => 'Title', 'content' => strip_tags($title)]);
        $this->verbose("Remainder: " . strip_tags($remainder));

        ///////////////////////////////////////////////////////////
        // Look for year if not already found                    //
        // (may already have been found at end of author string) //
        ///////////////////////////////////////////////////////////

        $containsMonth = false;
        if (!isset($item->year)) {
            if (!$year) {
                // space prepended to $remainder in case it starts with year, because getYear requires space 
                // (but perhaps could be rewritten to avoid it).
                $year = $this->getYear(' '. $remainder, false, $newRemainder, true, $month);
            }

            if ($year) {
                $item->year = $year;
            } else {
                $item->year = '';
                $warnings[] = "No year found.";
            }
            if (isset($month)) {
                $containsMonth = true;
                // translate 'Jan' or 'Jan.' or 'January', for example, to 'January'.
                $item->month = Carbon::parse('1 ' . $month)->format('F');
                $this->verbose(['fieldName' => 'Month', 'content' => strip_tags($item->month)]);
            }
        }

        $remainder = ltrim($newRemainder, ' ');

        $this->verbose(['fieldName' => 'Year', 'content' => strip_tags($item->year)]);

        ///////////////////////////////////////////////////////////////////////////////
        // To determine type of item, first record some features of publication info //
        ///////////////////////////////////////////////////////////////////////////////

        // $remainder is item minus authors, year, and title
        $remainder = ltrim($remainder, '., ');
        $this->verbose("[type] Remainder: " . strip_tags($remainder));
        
        $inStart = $containsIn = $italicStart = $containsBoldface = $containsEditors = $containsThesis = false;
        $containsNumber = $containsInteriorVolume = $containsCity = $containsPublisher = false;
        $containsIsbn = $containsEdition = $containsWorkingPaper = false;
        $containsNumberedWorkingPaper = $containsNumber = $pubInfoStartsWithForthcoming = $pubInfoEndsWithForthcoming = false;
        $containsVolume = $endsWithInReview = false;
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

        // contains volume designation, but not at start of $remainder
        if (preg_match($this->volRegExp1, substr($remainder, 3))) {
            $containsInteriorVolume = true;
            $this->verbose("Contains a volume, but not at start.");
        }

        // contains volume designation
        if (preg_match($this->volRegExp1, $remainder)) {
            $containsVolume = true;
            $this->verbose("Contains a volume.");
        }

        // test for 1st, 2nd etc. in third preg_match is to exclude a string like '1st ed.' (see Exner et al. in examples)
        if (preg_match($this->edsRegExp1, $remainder)
                || preg_match($this->edsRegExp2, $remainder)
                || (
                    preg_match($this->edsRegExp3, $remainder, $matches, PREG_OFFSET_CAPTURE)
                    && ! in_array(substr($remainder, $matches[0][1] - 4, 3), $this->ordinals)
                )
        ) {
            $containsEditors = true;
            $this->verbose("Contains editors.");
        }

        // if string like '15: 245:267' is found, '245:267' is assumed to be a page range, and ':' is replaced with '-'
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

        $regExp = '/' . $this->workingPaperRegExp . $this->workingPaperNumberRegExp;
        if (preg_match($regExp, $remainder, $workingPaperMatches, PREG_OFFSET_CAPTURE)) {
            if ($italicStart) {
                // remove italic code at start and recompute $workingPaperMatches (used later)
                foreach ($this->italicCodes as $italicCode) {
                    $remainder = Str::replaceStart($italicCode, '', $remainder);
                    preg_match($regExp, $remainder, $workingPaperMatches, PREG_OFFSET_CAPTURE);
                }
            }
            $containsNumberedWorkingPaper = true;
            $this->verbose("Contains string for numbered working paper.");
        }

        if (preg_match('/' . $this->workingPaperRegExp . '/', $remainder)) {
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

        if ($this->isProceedings($remainder)) {
            $containsProceedings = true;
            $this->verbose("Contains a string suggesting conference proceedings.");
        }

        if ($this->containsFontStyle($remainder, false, 'bold', $startPos, $length)) {
            $containsBoldface = true;
            $this->verbose("Contains string in boldface.");
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
                $cityString = $city;
                $cityLength = strlen($city);
                $remainderMinusPubInfo = Str::replaceFirst($city, '', $remainderMinusPubInfo);
                $this->verbose("Contains city \"" . $city . "\"");
                break;
            }
        }

        if (preg_match('/' . $this->isbnRegExp . '/', $remainder)) {
            $containsIsbn = true;
            $this->verbose("Contains an ISBN string.");
        }

        $commaCount = substr_count($remainder, ',');
        $this->verbose("Number of commas: " . $commaCount);

        if (isset($this->italicTitle)) {
            $this->verbose("Italic title");
        }

        ///////////////////////////////////////////////////////
        // Now use features of string to determine item type //
        ///////////////////////////////////////////////////////

        if (isset($item->url) && $oneWordAuthor) {
            $this->verbose("Item type case 0");
            $itemKind = 'online';
        } elseif (
            $isArticle
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
            /* $commaCount criterion doesn't seem to be useful
              if($commaCount < 6) $itemKind = 'article';
              else $itemKind = 'incollection';
            */
            $this->verbose("Item type case 6");
            $itemKind = 'article';
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [2]";
            }
        } elseif ($containsProceedings) {
            $this->verbose("Item type case 8");
            $itemKind = 'inproceedings';
        } elseif ($containsIsbn || (isset($this->italicTitle) && (($containsCity || $containsPublisher) || isset($item->editor)))) {
            $this->verbose("Item type case 7");
            $itemKind = 'book';
        } elseif (!$containsIn && ($pubInfoStartsWithForthcoming || $pubInfoEndsWithForthcoming)) {
            $this->verbose("Item type case 9");
            $itemKind = 'article';
        } elseif ($endsWithInReview) {
            $this->verbose("Item type case 9a");
            $itemKind = 'unpublished';
        } elseif ($containsPublisher || $inStart) {
            if ((!$containsIn && ! $containsPageRange) || strlen($remainder) - $cityLength - strlen($publisher) < 30) {
                $this->verbose("Item type case 10");
                $itemKind = 'book';
            } else {
                $this->verbose("Item type case 11");
                $itemKind = 'incollection';
            }
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif (!$containsNumber && !$containsPageRange) {
            // condition used to have 'or', which means that an article with a single page number is classified as a book
            if ($containsThesis) {
                $this->verbose("Item type case 12");
                $itemKind = 'thesis';
            } elseif ($endsWithInReview || $containsMonth) {
                $this->verbose("Item type case 12a");
                $itemKind = 'unpublished';
            } else {
                $this->verbose("Item type case 13");
                $itemKind = 'book';
            }
        } elseif ($containsEdition) {
            $this->verbose("Item type case 14");
            $itemKind = 'book';
            if (!$this->itemType) {
                $warnings[] = "Not sure of type; contains \"edition\", so set to " . $itemKind . ".";
            }
        } else {
            $itemKind = 'article';
            if (!$this->itemType) {
                $warnings[] = "Really not sure of type; has to be something, so set to " . $itemKind . ".";
            }
        }

        // Whether thesis is ma or phd is determined later
        if ($itemKind != 'thesis') {
            $this->verbose(['fieldName' => 'Item type', 'content' => strip_tags($itemKind)]);
        }

        unset($journal, $volume, $pages);

        // Remove ISBN and OCLC if any and put them in isbn and oclc fields.
        if ($itemKind == 'book' || $itemKind == 'incollection') {
            $match = $this->extractLabeledContent($remainder, ' ISBN:? ', '[0-9X]+');
            if ($match) {
                $item->isbn = $match;
                $this->verbose(['fieldName' => 'ISBN', 'content' => strip_tags($item->isbn)]);
            }

            $match = $this->extractLabeledContent($remainder, ' OCLC:? ', '[0-9]+');
            if ($match) {
                $item->oclc = $match;
                $this->verbose(['fieldName' => 'OCLC', 'content' => strip_tags($item->oclc)]);
            }
        }

        // if item is not unpublished and ends with 'in review', put 'in review' in notes field and remove it from entry
        // Can this case arise?
        if ($itemKind != 'unpublished') {
            $match = $this->extractLabeledContent($remainder, '', '\(?[Ii]n [Rr]eview\.?\)?$');
            if ($match) {
                $item->note = $match;
                $this->verbose('"In review" string removed and put in note field');
                $this->verbose('Remainder: ' . $remainder);
                $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
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
                // get journal
                $remainder = ltrim($remainder, '., ');

                // If does not start with italics, check whether first word is all numeric and
                // italics starts after first word, in which case
                // classify first word as unidentified.  (Covers case of mistaken duplication of year.)
                if (!$italicStart) {
                    $firstWord = strtok($remainder, ' ');
                    if (preg_match('/^[0-9]*$/', $firstWord)) {
                        $remainderAfterFirstWord = trim(substr($remainder, strlen($firstWord)));
                        if ($this->containsFontStyle($remainderAfterFirstWord, true, 'italics', $startPos, $length)) {
                            $warnings[] = "The string \"" . $firstWord . "\" remains unidentified.";
                            $remainder = $remainderAfterFirstWord;
                            $italicStart = true;
                        }
                    }
                }

                $journal = $this->getJournal($remainder, $item, $italicStart, $pubInfoStartsWithForthcoming, $pubInfoEndsWithForthcoming);
                $journal = rtrim($journal, ' ,');

                $this->verbose('Journal: ' . isset($journal) ? $journal : '');
                $item->journal = isset($journal) ? $journal : '';
                if ($item->journal) {
                    $this->verbose(['fieldName' => 'Journal', 'content' => strip_tags($item->journal)]);
                } else {
                    $warnings[] = "'Journal' field not found.";
                }
                $remainder = trim($remainder, ' ,.');
                $this->verbose("Remainder: " . $remainder);

                // If $remainder ends with 'forthcoming' phrase, put that in note.  Else look for pages & volume etc.
                if (preg_match('/' . $this->endForthcomingRegExp . '/', $remainder)) {
                    $item->note = $remainder;
                    $remainder = '';
                } else {
                    // get pages
                    $this->getPagesForArticle($remainder, $item);

                    $pagesReported = false;
                    if ($item->pages) {
                        $this->verbose(['fieldName' => 'Pages', 'content' => strip_tags($item->pages)]);
                        $pagesReported = true;
                    } else {
                        $warnings[] = "No page range found.";
                    }
                    $this->verbose("[p1] Remainder: " . $remainder);

                    // get month, if any
                    $months = $this->monthsRegExp;
                    $regExp = '(\(?(' . $months . '\)?)([-\/](' . $months . ')\)?)?)';
                    preg_match_all($regExp, $remainder, $matches, PREG_OFFSET_CAPTURE);

                    if (isset($matches[0][0][0]) && $matches[0][0][0]) {
                        $item->month = trim($matches[0][0][0], '()');
                        $this->verbose(['fieldName' => 'Month', 'content' => strip_tags($item->month)]);
                        $remainder = substr($remainder, 0, $matches[0][0][1]) . ltrim(substr($remainder, $matches[0][0][1] + strlen($matches[0][0][0])), ', )');
                        $this->verbose('Remainder: ' . $remainder);
                    }

                    // get volume and number
                    $this->getVolumeAndNumberForArticle($remainder, $item);
                
                    $result = $this->findRemoveAndReturn($remainder, $this->articleRegExp);
                    if ($result) {
                        // If remainder contains article number, put it in the note field
                        $item->note = $result[0];
                    } elseif (!$item->pages && isset($item->number) && $item->number) {
                        // else if no pages have been found and a number has been set, assume the previously assigned number
                        // is in fact a single page
                        $item->pages = $item->number;
                        unset($item->number);
                        $this->verbose('[p5] no pages found, so assuming string previously assigned to number is a single page: ' . $item->pages);
                        $warnings[] = "Not sure the pages value is correct.";
                    }

                    if (!$pagesReported && isset($item->pages) && $item->pages) {
                        $this->verbose(['fieldName' => 'Pages', 'content' => strip_tags($item->pages)]);
                    }

                    if (isset($item->volume) && $item->volume) {
                        $this->verbose(['fieldName' => 'Volume', 'content' => strip_tags($item->volume)]);
                    } else {
                        $warnings[] = "'Volume' field not found.";
                    }
                    if (isset($item->number) && $item->number) {
                        $this->verbose(['fieldName' => 'Number', 'content' => strip_tags($item->number)]);
                    }

                    if (isset($item->note)) {
                        if ($item->note) {
                            $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
                        } else {
                            unset($item->note);
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
                    $item->note = substr($remainder, $length);
                } else {
                    $item->note = $remainder;
                }
                $remainder = '';
                if ($item->note) {
                    $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
                } else {
                    $warnings[] = "Mandatory 'note' field missing.";
                }
                break;

            ////////////////////////////////////////////////
            // Get publication information for techreport //
            ////////////////////////////////////////////////

            case 'techreport':
                // If string before type, take that to be institution, else take string after number
                // to be institution---handles both 'CORE Discussion Paper 34' and 'Discussion paper 34, CORE'
                $item->type = isset($workingPaperMatches[1][0]) ? $workingPaperMatches[1][0] : '';
                $item->number = isset($workingPaperMatches[3][0]) ? $workingPaperMatches[3][0] : '';
                if (isset($workingPaperMatches[0][1]) && $workingPaperMatches[0][1] > 0) {
                    // Chars before 'Working Paper'
                    //dd($workingPaperMatches, $remainder);
                    $item->institution = trim(substr($remainder, 0, $workingPaperMatches[0][1] - 1), ' .,');
                    $remainder = trim(substr($remainder, $workingPaperMatches[3][1] + strlen($item->number)), ' .,');
                } else {
                    // No chars before 'Working paper'---so take string after number to be institution
                    $n = isset($workingPaperMatches[3][1]) ? $workingPaperMatches[3][1] : 0;
                    $item->institution = trim(substr($remainder, $n + strlen($item->number)), ' .,');
                    $remainder = '';
                }

                if ($item->type) {
                    $this->verbose(['fieldName' => 'Type', 'content' => strip_tags($item->type)]);
                }
                
                if ($item->number) {
                    $this->verbose(['fieldName' => 'Number', 'content' => strip_tags($item->number)]);
                } else {
                    unset($item->number);
                }
                
                if ($item->institution) {
                    $this->verbose(['fieldName' => 'Institution', 'content' => strip_tags($item->institution)]);
                } else {
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
                $pubInfoInParens = false;
                $this->verbose("[in1] Remainder: " . $remainder);

                // If $remainder starts with "in", remove it
                if ($inStart) {
                    $this->verbose("Starts with variant of \"in\"");
                    $remainder = ltrim(substr($remainder, 2), ': ');
                }

                // Get pages and remove them from $remainder
                $regExp = '(\()?(' . $this->pagesRegExp2 . ')?( )?([1-9][0-9]{0,4} ?-{1,2} ?[0-9]{1,5})(\))?';
                // Return group 4 of match and remove whole match from $remainder
                $result = $this->findRemoveAndReturn($remainder, $regExp);
                if ($result) {
                    $pages = $result[4];
                    $item->pages = $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '';
                }

                if (isset($item->pages)) {
                    $this->verbose(['fieldName' => 'Pages', 'content' => strip_tags($item->pages)]);
                } else {
                    $warnings[] = "Pages not found.";
                }

                // Next case occurs if remainder previously was like "pages 2-33 in ..."
                if (substr($remainder, 0, 3) == 'in ') {
                    $remainder = substr($remainder, 3);
                }
                $this->verbose("[in2] Remainder: " . $remainder);

                //$remainder = str_replace(array('Proc.', 'Amer.'),  array('Proceedings', 'American'), $remainder);
                $editorStart = false;
                $newRemainder = $remainder;
                
                // If a string in $remainder is quoted or italicized, take that to be book title
                $booktitle = $this->getQuotedOrItalic($remainder, false, false, $newRemainder, $posStart, $posEnd);

                if ($booktitle) {
                    $item->booktitle = $booktitle;
                    if ($posStart > 0) {
                        // Pattern is <string1> <booktitle> <string2> (with <string1> nonempty).
                        // Check whether <string1> starts with "forthcoming"
                        $string1 = trim(substr($remainder, 0, $posStart), ',. ');
                        if (preg_match('/' . $this->startForthcomingRegExp . '/i', $string1, $matches)) {
                            $match = trim($matches[0], '() ');
                            $match = Str::replaceEnd(' in', '', $match);
                            $match = Str::replaceEnd(' at', '', $match);
                            $item->note = isset($item->note) ? $item->note . ' ' . trim($match) : trim($match);
                            $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
                            $possibleEditors = strlen($matches[0]) - strlen($string1) ? substr($string1, strlen($matches[0])) : null;
                        } else {
                            // Assume <string1> is editors
                            $possibleEditors = $string1;
                        }
                        if ($possibleEditors) {
                            if (preg_match($this->editorStartRegExp, $possibleEditors, $matches, PREG_OFFSET_CAPTURE)) {
                                $possibleEditors = trim(substr($possibleEditors, strlen($matches[0][0])));
                            }
                            $conversion = $this->convertToAuthors(explode(' ', $possibleEditors), $remains, $year, $isEditor, false);
                            $item->editor = trim($conversion['authorstring']);
                            $this->verbose('Editors: ' . $item->editor);
                        } else {
                            $this->verbose('No editors found');
                        }
                        // What is left must be the publisher & address
                        $remainder = $newRemainder = trim(substr($remainder, $posEnd+1), '., ');
                    } else {
                        $remainder = ltrim($newRemainder, ', ');
                    }
                }

                $this->verbose('[in0] Remainder: ' . $remainder);
                $updateRemainder = false;

                // If $remainder matches this pattern, it is consistent with its being publisher: address,
                // without any editors.  $remainder will be '';
                $result = $this->extractLabeledContent($remainder, '^[a-zA-Z]*:', '.*', true);
                if ($result) {
                    $item->address = substr($result['label'], 0, -1);
                    $item->publisher = $result['content'];
                }

                // The only reason why $item->editor could be set other than by the previous code block is that the 
                // item is a book with an editor rather than an author.  So probably the following condition could
                // be replaced by } else {.
                if ($remainder && !isset($item->editor)) {
                    $updateRemainder = true;
                    // If a city or publisher has been found, temporarily remove it from remainder to see what is left
                    // and whether info can be extracted from what is left
                    $tempRemainder = $remainder;
                    if ($cityString) {
                        $tempRemainder = $this->findAndRemove($tempRemainder, $cityString);
                    }
                    if ($publisherString) {
                        $tempRemainder = $this->findAndRemove($tempRemainder, $publisherString);
                    }
                    $tempRemainder = trim($tempRemainder, ',.:() ');
                    $this->verbose('tempRemainder: ' . $tempRemainder);

                    // If item doesn't contain string identifying editors, look more carefully to see whether
                    // it contains a string that could be editors' names.
                    if (!$containsEditors) {
                        if (strpos($tempRemainder, '.') === false && strpos($tempRemainder, ',') === false) {
                            $this->verbose("tempRemainder contains no period or comma, so appears to not contain editors' names");
                            if (!$booktitle) {
                                $booktitle = $tempRemainder;
                            }
                            $item->editor = '';
                            $warnings[] = 'No editor found';
                            $item->address = $cityString;
                            $item->publisher = $publisherString;
                            $newRemainder = trim(Str::remove([$cityString, $publisherString], $remainder), ', ');
                        } elseif (strpos($tempRemainder, ',') !== false) {
                            // looking at strings following commas, to see if they are names
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
                                    }
                                    $item->editor = '';
                                    $warnings[] = 'No editor found';
                                    $item->address = $cityString;
                                    $item->publisher = $publisherString;
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
                    // If no string is quoted or italic, try to determine whether $remainder starts with
                    // title or editors.
                    // If $remainder contains string "(Eds.)" or similar (parens required) then check
                    // whether it starts with names
                    // If it doesn't, take start of $remainder up to first comma or period to be title,
                    // followed by editors, up to (Eds.).
                    $postEditorString = '';

                    $regExp = '/( [Ee]ds?[\. ]|\([Ee]ds?\.?\)| [Ee]ditors?| \([Ee]ditors?\))/';
                    $remainderContainsEds = false;
                    if (preg_match($regExp, $remainder, $matches)) {
                        $eds = $matches[0];
                        $before = Str::before($remainder, $eds);
                        $after = Str::after($remainder, $eds);
                        $publisherPosition = strpos($after, $publisher);
                        $remainderContainsEds = true;
                    }

                    // Require string for editors to have at least 6 characters and string for booktitle to have at least 10 characters
                    if ($remainderContainsEds && $before > 5 && $publisherPosition !== false && $publisherPosition > 10) {
                            // $remainder is <editors> eds <booktitle> <publicationInfo>
                            $this->verbose("Remainder seems to be <editors> eds <booktitle> <publicationInfo>");
                            $editorStart = true;
                            $editorString = $before;
                            $determineEnd = false;
                            $postEditorString = $after;
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
                            $this->verbose("[in3] Remainder: " . $remainder);
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
                        if ($this->isNameString($remainder)) {
                            // CASE 4
                            // $remainder is <editors> <booktitle> <publicationInfo>
                            $this->verbose("Remainder does not contain \"(Eds.)\" or similar string in parentheses and does not start with \"Eds\" or similar, but starts with a string that looks like a name");
                            $editorStart = true;
                            $editorString = $remainder;
                            $determineEnd = true;
                            $this->verbose("editorString: " . $editorString);
                            $this->verbose("[in6a] Remainder: " . $remainder);
                        } else {
                            // CASE 5
                            // $remainder is <booktitle> <editors> <publicationInfo>
                            $this->verbose("Remainder does not contain \"(Eds.)\" or similar and does not start with \"Eds\" or similar, and does not start with a string that looks like a name");
                            $editorStart = false;
                            $edStrLen = 0;
                            $endAuthorPos = 0;
                        }
                    }

                    if ($editorStart) {
                        // CASES 1, 3, and 4
                        $this->verbose("[ed1] Remainder starts with editor string");
                        $words = explode(' ', $editorString);
                        // $isEditor is used only for a book (with an editor, not an author)
                        $isEditor = false;

                        $conversion = $this->convertToAuthors($words, $remainder, $trash2, $isEditor, $determineEnd);
                        $editorString = trim($conversion['authorstring'], '() ');
                        foreach ($conversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $item->editor = trim($editorString, ' ,.');
                        $newRemainder = $postEditorString ? $postEditorString : $remainder;
                        // $newRemainder consists of <booktitle> <publicationInfo>
                        $this->verbose("editors: " . $item->editor);
                        $newRemainder = trim($newRemainder, '., ');
                        $this->verbose("[in7] Remainder: " . $newRemainder);
                    } else {
                        // CASES 2 and 5
                        $this->verbose("Remainder: " . $remainder);
                        $this->verbose("[ed2] Remainder starts with book title");
                        // code repeated from above; needs to be refined considerably if used here
                        // if ($cityString) {
                        //     $tempRemainder = $this->findAndRemove($tempRemainder, $cityString);
                        // }
                        // if ($publisherString) {
                        //     $tempRemainder = $this->findAndRemove($tempRemainder, $publisherString);
                        // }
                        // }
                        // $booktitle = trim($remainder, ',.:() ');

                        // Take book title to be string up to first comma or period
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
                                    // don't stop in middle of parenthetical phrase
                                    $leftParenCount == $rightParenCount
                                    &&
                                    (
                                        $j == strlen($remainder) - 1
                                        ||
                                        (
                                            $remainder[$j] == '.'
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
                                $booktitle = substr($remainder, 0, $j+1);
                                $this->verbose("[booktitle1] Booktitle is: " . $booktitle);
                                $newRemainder = rtrim(substr($remainder, $j + 1), ',. ');
                            }
                        }
                        $this->verbose("booktitle: " . $booktitle);
                        if (isset($endAuthorPos) && $endAuthorPos) {
                            // CASE 2
                            $authorstring = trim(substr($remainder, $j, $endAuthorPos - $j), '.,: ');
                            $conversion = $this->convertToAuthors(explode(' ', $authorstring), $trash1, $trash2, $isEditor, false);
                            $item->editor = trim($conversion['authorstring'], ' ');
                            foreach ($conversion['warnings'] as $warning) {
                                $warnings[] = $warning;
                            }
                            $newRemainder = trim(substr($remainder, $endAuthorPos + $edStrLen), ',:. ');
                            $this->verbose("[in8] editors: " . $item->editor);
                        } else {
                            // CASE 5
                        }
                    }
                }

                if ($updateRemainder) {
                    $remainder = ltrim($newRemainder, ", ");
                }
                $this->verbose("[in9] Remainder: " . $remainder);

                // Get editors
                if ($booktitle && ! isset($item->editor)) {
                    // CASE 2
                    if (preg_match($this->editorStartRegExp, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $this->verbose("[ed3] Remainder starts with editor string");
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                        $this->verbose("Remainder is: " . $remainder);
                        // If $remainder starts with "ed." or "eds." or "edited by", guess that potential editors end at period or '('
                        // (to cover case of publication info in parens) preceding
                        // ':' (which could separate publisher and city), if such exists.
                        $colonPos = strrpos($remainder, ':');
                        if ($colonPos !== false) {
                            $this->verbose("Remainder contains colon");
                            $remainderBeforeColon = trim(substr($remainder, 0, $colonPos));
                            // At least last word must be city or part of city name, so remove it
                            $spacePos = strrpos($remainderBeforeColon, ' ');
                            $possibleEditors = trim(substr($remainderBeforeColon, 0, $spacePos));
                            $conversion = $this->convertToAuthors(explode(' ', $possibleEditors), $trash1, $trash2, $isEditor, true);

                            //dd($conversion);
                            // find previous period
                            for ($j = $colonPos; $j > 0 && $remainder[$j] != '.' && $remainder[$j] != '('; $j--) {

                            }
                            $this->verbose("Position of period in remainder is " . $j);
                            // Previous version---why drop first 3 chars?
                            // $editor = trim(substr($remainder, 3, $j-3), ' .,');

                            $conversion = $this->convertToAuthors(explode(' ', trim(substr($remainder, 0, $j), ' .,')), $trash1, $trash2, $isEditor, false);
                            $editor = $conversion['authorstring'];
                            foreach ($conversion['warnings'] as $warning) {
                                $warnings[] = $warning;
                            }

                            $this->verbose("Editor is: " . $editor);
                            $newRemainder = substr($remainder, $j);
                        } else {
                            if ($containsPublisher) {
                                $publisherPos = strpos($remainder, $publisher);
                                $editor = substr($remainder, 0, $publisherPos);
                                $this->verbose("Editor is: " . $editor);
                                $newRemainder = substr($remainder, $publisherPos);
                            } else {
                                $editor = '';
                                $warnings[] = "Unable to determine editors.";
                                $newRemainder = $remainder;
                            }
                        }
                    } elseif (preg_match($this->edsRegExp1, $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        // $remainder contains "(Eds.)" or something similar, so takes form <editor> (Eds.) <publicationInfo>
                        $this->verbose("[ed6] Remainder starts with editor string");
                        $editorString = substr($remainder, 0, $matches[0][1]);
                        $this->verbose("editorString is " . $editorString);
                        $conversion = $this->convertToAuthors(explode(' ', $editorString), $trash1, $trash2, $isEditor, false);
                        $editor = $conversion['authorstring'];
                        foreach ($conversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }
                        $item->editor = trim($editor, ', ');
                        $this->verbose("Editor is: " . $item->editor);
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                    } elseif ($this->initialNameString($remainder)) {
                        $this->verbose("[ed4] Remainder starts with editor string");
                        $conversion = $this->convertToAuthors(explode(' ', $remainder), $remainder, $trash2, $isEditor, true);
                        $editor = $conversion['authorstring'];
                        foreach ($conversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $item->editor = trim($editor, ', ');
                        $this->verbose("Editor is: " . $item->editor);
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

                        $conversion = $this->convertToAuthors($words, $remainder, $trash2, $isEditor, true);
                        $authorstring = $conversion['authorstring'];
                        $item->editor = trim($authorstring, '() ');
                        foreach ($conversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        //$item->editor = trim($this->convertToAuthors($words, &$trash1, $trash2, false), '() ');
                        $remainder = ltrim($newRemainder, ' ,');
                        $this->verbose("[in4a] Remainder: " . $remainder);
                    }
                } elseif (!$booktitle) {
                    // CASES 1, 3, and 4
                    // Case in which $booktitle is not defined: remainder presumably starts with booktitle
                    $remainder = trim($remainder, '., ');
                    $this->verbose("[in6] Remainder: " . $remainder);
                    // $remainder contains book title and publication info.  Need to find boundary.  

                    // Check whether publication info matches pattern for book to be a volume in a series
                    $result = $this->findRemoveAndReturn(
                        $remainder,
                        '(' . $this->volumeRegExp1 . ')( ([1-9][0-9]{0,4}))(( of| in|,) )([^\.,]*\.|,)'
                        );
                    if ($result) {
                        // Take series to be string following 'of' or 'in' or ',' up to next period or comma
                        $item->volume = $result[3];
                        $item->series = $result[6];
                        $booktitle = trim($result['before'], '., ');
                        $remainder = trim($result['after'], ',. ');
                        $this->verbose('Volume found, so book is part of a series');
                        $this->verbose(['fieldName' => 'Volume', 'content' => $item->volume]);
                        $this->verbose(['fieldName' => 'Series', 'content' => $item->series]);
                        $this->verbose('Remainder (publisher and address): ' . $remainder);
                    } else {
                        // if remainder contains a single period, take that as end of booktitle
                        if (substr_count($remainder, '.') == 1) {
                            $this->verbose("Remainder contains single period, so take that as end of booktitle");
                            $periodPos = strpos($remainder, '.');
                            $booktitle = trim(substr($remainder, 0, $periodPos), ' .,');
                            $remainder = substr($remainder, $periodPos);
                        } else {
                            // If publisher has been identified, remove it from $remainder and check
                            // whether it is preceded by a string that could be an address
                            if (isset($publisher) && $publisher) {
                                $item->publisher = $publisher;
                                $tempRemainder = trim(Str::remove($publisher, $remainder), ' .');
                                $afterPeriod = Str::afterLast($tempRemainder, '.');
                                $afterComma = Str::afterLast($tempRemainder, ',');
                                $afterPunc = (strlen($afterComma) < strlen($afterPeriod)) ? $afterComma : $afterPeriod;
                                foreach ($this->cities as $city) {
                                    if (Str::endsWith(trim($afterPunc, '():'), $city)) {
                                        $item->address = $city;
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($city) - 2);
                                        $this->verbose('booktitle case 0');
                                        break;
                                    }
                                }
                                if (!isset($item->address)) {
                                    if (substr_count($afterPunc, ' ') == 1) {
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($afterPunc));
                                        $item->address = $afterPunc;
                                        $this->verbose('booktitle case 1');
                                    } else {
                                        $booktitle = $tempRemainder;
                                        $this->verbose('booktitle case 2');
                                    }
                                    $item->booktitle = $booktitle;
                                    $remainder = '';
                                }
                            } else {
                                $words = explode(" ", $remainder);
                                $sentences = $this->splitIntoSentences($words);
                                // If two or more sentences, take all but last one to be booktitle.
                                if (count($sentences) > 1) {
                                    $booktitle = implode(' ', array_slice($sentences, 0, count($sentences) - 1));
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
                                        $this->verbose('booktitle case 3');
                                    } else {
                                        // Does whole entry end in ')' or ').'?  If so, pubinfo is in parens, so booktitle ends
                                        // at previous '('; else booktitle is all of $potentialTitle
                                        if ($entry[strlen($entry) - 1] == ')' or $entry[strlen($entry) - 2] == ')') {
                                            $booktitle = substr($remainder, 0, strrpos($remainder, '('));
                                            $this->verbose('booktitle case 4');
                                        } else {
                                            $booktitle = $potentialTitle;
                                            $this->verbose('booktitle case 5');
                                        }
                                        $remainder = substr($remainder, strlen($booktitle));
                                    }
                                }
                            }
                        }
                    }
                }

                if (isset($item->editor) && $item->editor) {
                    $this->verbose(['fieldName' => 'Editors', 'content' => strip_tags($item->editor)]);
                } else {
                    $warnings[] = "Editor not found.";
                }

                $remainder = trim($remainder, '[]()., ');
                $this->verbose("[in6b] remainder: " . $remainder);

                // If $remainder contains 'forthcoming' string, remove it and put it in $item->note.
                $result = $this->findRemoveAndReturn($remainder, '^(Forthcoming|In press|Accepted)');
                if ($result) {
                    $item->note .= ' ' . $result[0];
                    $this->verbose('"Forthcoming" string removed and put in note field');
                    $this->verbose('Remainder: ' . $remainder);
                    $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
                }

                // Whatever is left is publisher and address
                //if(!isset($item->publisher) || !isset($item->address)) {
                if ((!isset($item->publisher) || ! $item->publisher) || ( !isset($item->address) || ! $item->address)) {
                    if (isset($item->publisher) && $item->publisher) {
                        $item->address = $remainder;
                        $newRemainder = '';
                    } elseif (isset($item->address) && $item->address) {
                        $item->publisher = $remainder;
                        $newRemainder = '';
                    } else {
                        $newRemainder = $this->extractPublisherAndAddress($remainder, $address, $publisher);
                        $item->publisher = $publisher;
                        $item->address = $address;
                    }
                }

                if ($item->publisher) {
                    $this->verbose(['fieldName' => 'Publisher', 'content' => strip_tags($item->publisher)]);
                } else {
                    $warnings[] = "Publisher not found.";
                }
                if (isset($item->address) && $item->address) {
                    $this->verbose(['fieldName' => 'Address', 'content' => strip_tags($item->address)]);
                } else {
                    $warnings[] = "Address not found.";
                }

                $booktitle = rtrim($booktitle, '.');
                $item->booktitle = trim($booktitle);
                if ($item->booktitle) {
                    $this->verbose(['fieldName' => 'Book title', 'content' => strip_tags($item->booktitle)]);
                } else {
                    $warnings[] = "Book title not found.";
                }

                if ($leftover) {
                    $leftover .= ';';
                }
                $remainder = $leftover . " " . $newRemainder;
                $this->verbose("Remainder: " . $remainder);
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
                        $item->edition = trim($remainingWords[$key - 1], ',. )(');
                        $this->verbose(['fieldName' => 'Edition', 'content' => strip_tags($item->edition)]);
                        array_splice($remainingWords, $key - 1, 2);
                        break;
                    }
                }

                // If remainder contains word 'volume', take next word to be volume number, and if
                // following word is "in" or "of", volume is part of series
                $this->verbose('Looking for volume');
                $done = false;
                $newRemainder = null;
                $remainder = implode(" ", $remainingWords);
                $result = $this->findRemoveAndReturn(
                    $remainder,
                    '(' . $this->volumeRegExp1 . ')( ([1-9][0-9]{0,4}))(( of| in|,|.)? )(.*)$'
                );
                if ($result) {
                    $item->volume = $result[3];
                    if (in_array($result[5], ['.', ','])) {
                        // Volume is volume of book, not part of series
                        // Publisher and possibly address
                        $newRemainder = $result[6];
                    } else {
                        // Volume is part of series
                        $this->verbose('Volume is part of series: assume format is <series> <publisherAndAddress>');
                        $this->verbose(['fieldName' => 'Volume', 'content' => $item->volume]);
                        $seriesAndPublisher = $result[6];
                        // Case in which  publisher has been identified
                        if ($publisher) {
                            $this->verbose('Publisher has been identified');
                            $item->publisher = $publisher;
                            $this->verbose(['fieldName' => 'Publisher', 'content' => $item->publisher]);
                            $after = Str::after($seriesAndPublisher, $publisher);
                            $before = Str::before($seriesAndPublisher, $publisher);
                            if ($after) {
                                // If anything comes after the publisher, it must be the address, and the string
                                // before the publisher must be the series
                                $item->address = trim($after, ',. ');
                                $item->series = trim($before, '., ');
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
                                            $item->address = trim(substr($before, strlen($beforeLastComma)), '.,: ');
                                            $item->series = trim($beforeLastComma, '.,: ');
                                        } else {
                                            $item->address = trim(substr($before, strlen($beforeLastPeriod)), '.,: ');
                                            $item->series = trim($beforeLastPeriod, '.,: ');
                                        }
                                    } elseif ($containsComma) {
                                        $item->address = trim(substr($before, strlen($beforeLastComma)), '.,: ');
                                        $item->series = trim($beforeLastComma, '.,: ');
                                    } elseif ($containsPeriod) {
                                        $item->address = trim(substr($before, strlen($beforeLastPeriod)), '.,: ');
                                        $item->series = trim($beforeLastPeriod, '.,: ');
                                    } else {
                                        $beforeLastSpace = Str::beforeLast($before, ' ');
                                        $item->address = trim(substr($before, strlen($beforeLastSpace)), '.,: ');
                                        $item->series = trim($beforeLastSpace, '.,: ');
                                    }
                                    $this->verbose(['fieldName' => 'Series', 'content' => $item->series]);
                                    $this->verbose(['fieldName' => 'Publication city', 'content' => $item->address]);
                                } else {
                                    // Otherwise there is no address, and the series is the string before the publisher
                                    $item->series = trim($before, '.,: ');
                                    $this->verbose(['fieldName' => 'Series', 'content' => $item->series]);
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
                                $item->series = $result1[1];
                                if ($result1[4] == ',') {
                                    $this->verbose('Series case 1a: format is <publisher>, <address>');
                                    $item->publisher = trim($result1[2], ' ,');
                                    $item->address = $result1[5];
                                } elseif ($result1[4] == ':') {
                                    $this->verbose('Series case 1b: format is <address>: <publisher>');
                                    $item->address = trim($result1[2], ' :');
                                    $item->publisher = $result1[5];
                                }
                                $this->verbose(['fieldName' => 'Series', 'content' => $item->series]);
                                $this->verbose(['fieldName' => 'Publisher', 'content' => $item->publisher]);
                                $this->verbose(['fieldName' => 'Publication city', 'content' => $item->address]);
                            } else {
                                $result2 = $this->findRemoveAndReturn(
                                    $seriesAndPublisher,
                                    '(.*[^.,]*)\. (.*\.?)$'
                                );
                                if ($result2) {
                                    $this->verbose('Series case 2: format is <publisher> (no address)');
                                    $item->series = $result2[1];
                                    $item->publisher = $result2[2];
                                    $this->verbose(['fieldName' => 'Series', 'content' => $item->series]);
                                    $this->verbose(['fieldName' => 'Publisher', 'content' => $item->publisher]);
                                }
                            }
                        }
                        $done = true;
                    }
                }

                // Volume has been identified, but publisher and possibly address reamin
                if (!$done) {
                    $remainder = $newRemainder ? $newRemainder : implode(" ", $remainingWords);

                // foreach ($remainingWords as $key => $word) {
                //     if (count($remainingWords) > $key + 1
                //             && in_array(mb_strtolower(trim($word, ',. ()')), ['volume', 'vol'])) {
                //         $item->volume = trim($remainingWords[$key + 1], ',. ');
                //         $this->verbose(['fieldName' => 'Volume', 'content' => strip_tags($item->volume)]);
                //         array_splice($remainingWords, $key, 2);
                //         $series = [];
                //         if (in_array(mb_strtolower($remainingWords[$key]), ['in', 'of'])) {
                //             for ($k = $key + 1; $k < count($remainingWords); $k++) {
                //                 $series[] = $remainingWords[$k];
                //                 if (in_array(substr($remainingWords[$k], -1), [',', '.'])) {
                //                     if ($series[0][0] == "\\" || substr($series[0], 0, 2) == "{\\") {
                //                         array_shift($series);
                //                     }
                //                     $item->series = trim(implode(" ", $series), '.,}');
                //                     $this->verbose(['fieldName' => 'Series', 'content' => strip_tags($item->series)]);
                //                     array_splice($remainingWords, $key, $k - $key + 1);
                //                     break;
                //                 }
                //             }
                //         }
                //         break;
                //     }
                // }

                    // If string is in italics, get rid of the italics
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                        $remainder = rtrim(substr($remainder, $length), '}');
                    }

                    $remainder = $this->extractPublisherAndAddress($remainder, $address, $publisher);

                    $item->publisher = $publisher;
                    if ($item->publisher) {
                        $this->verbose(['fieldName' => 'Publisher', 'content' => strip_tags($item->publisher)]);
                    } else {
                        $warnings[] = "No publisher identified.";
                    }

                    $item->address = $address;
                    if ($item->address) {
                        $this->verbose(['fieldName' => 'Publication city', 'content' => strip_tags($item->address)]);
                    } else {
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
                    $thesisTypeFound = 0;
                    if (preg_match('/' . $this->masterRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $itemKind = 'mastersthesis';
                        $thesisTypeFound = 1;
                    } elseif (preg_match('/' . $this->phdRegExp . '/', $remainder, $matches, PREG_OFFSET_CAPTURE)) {
                        $itemKind = 'phdthesis';
                        $thesisTypeFound = 1;
                    } else {
                        $itemKind = 'phdthesis';
                        $warnings[] = "Can't determine whether MA or PhD thesis; set to be PhD thesis.";
                    }
                }
                $this->verbose(['fieldName' => 'Item type', 'content' => strip_tags($itemKind)]);

                $remainder = $this->findAndRemove($remainder, $this->fullThesisRegExp);

                $remainder = trim($remainder, ' .,');
                if (strpos($remainder, ':')===false) {
                    $item->school = $remainder;
                } else {
                    $remArray = explode(':', $remainder);
                    $item->school = trim($remArray[1], ' .,');
                }
                $remainder = '';

                if ($item->school) {
                    $this->verbose(['fieldName' => 'School', 'content' => strip_tags($item->school)]);
                } else {
                    $warnings[] = "No school identified.";
                }

                break;
        }

        $remainder = trim($remainder, '.,:;}{ ');

        if ($remainder && !in_array($remainder, ['pages', 'Pages', 'pp', 'pp.'])) {
            $warnings[] = "The string \"" . $remainder . "\" remains unidentified.";
        }

        if (isset($item->pages) && !$item->pages) {
            unset($item->pages);
        }

        if (isset($item->publisher) && $item->publisher == '') {
            unset($item->publisher);
        }

        if (isset($item->address) && !$item->address) {
            unset($item->address);
        }

        if (isset($item->volume) && !$item->volume) {
            unset($item->volume);
        }

        if (isset($item->editor) && !$item->editor) {
            unset($item->editor);
        }

        if (isset($item->booktitle) && $item->booktitle) {
            $item->booktitle = trim($booktitle, ' .,');
        }

        foreach ($warnings as $warning) {
            $this->verbose(['warning' => strip_tags($warning)]);
        }

        foreach ($notices as $notice) {
            $this->verbose(['notice' => strip_tags($notice)]);
        }

        if (isset($item->author)) {
            $item->author = trim($item->author);
        }
        if (isset($item->journal)) {
            $item->journal = trim($item->journal, '} ');
        }
        $item->title = $this->requireUc($item->title);

        $scholarTitle = str_replace(' ', '+', $item->title);
        $scholarTitle = str_replace(["'", '"', "{", "}", "\\"], "", $scholarTitle);

        $returner = [
            'source' => $originalEntry,
            'item' => $item,
            'label' => $itemLabel,
            'itemType' => $itemKind,
            'warnings' => $warnings,
            'notices' => $notices,
            'details' => $this->displayLines,
            'scholarTitle' => $scholarTitle,
        ];

        return $returner;
    }

    // Get title from a string that starts with title and then has publication information.
    // Case in which title is in quotation marks or italics is dealt with separately.
    public function getTitle(string &$remainder, string|null &$edition, string|null &$volume, bool &$isArticle): string|null
    {
        $title = null;
        $originalRemainder = $remainder;

        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;
        $skipNextWord = false;

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

            // If $word is one of the italic codes ending in a space, stop and form title
            if (in_array($word . ' ', $this->italicCodes)) {
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
                $periodFound = Str::endsWith($word, '.');

                $stringToNextPeriod = strtok($remainder, '.?!');

                // When a word ending in punctuation is encountered, check whether it is followed by
                // italics
                // OR a Working Paper string
                // OR a pages string
                // OR "in" OR "Journal" 
                // OR a volume designation
                // OR a year 
                // OR the name of a publisher.
                // If so, the title is $remainder up to the punctuation.
                // Before checking for punctuation at the end of a work, trim ' and " from the end of it, to take care
                // of the cases ``<word>.'' and "<word>."
                if (Str::endsWith(rtrim($word, "'\""), ['.', '!', '?', ':', ',', ';'])) {
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)
                        || preg_match('/^' . $this->workingPaperRegExp . '/', $remainder)
                        || preg_match($this->pagesRegExp3, $remainder)
                        || preg_match('/^[Ii]n |^' . $this->journalWord . ' |^\(?Vol\.? |^\(?Volume /', $remainder)
                        || preg_match('/^(19|20)[0-9][0-9]\./', $remainder)
                        || Str::startsWith($remainder, $this->publishers)
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
                $volumeRegExp = '/(^\(vol\.?|volume) (\d)\.?\)?[.,]?$/i';
                if ($nextWord && $nextButOneWord && preg_match($volumeRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
                    $volume = $matches[2];
                    $this->verbose("Ending title, case 3a");
                    $title = rtrim(implode(' ', $initialWords), ' ,');
                    array_splice($remainingWords, 0, 2);
                    $remainder = implode(' ', $remainingWords);
                    break;
                }

                // Upcoming edition specification
                $editionRegExp = '/(^\(' . $this->editionRegExp . '\)|^' . $this->editionRegExp . ')[.,]?$/i';
                if ($nextWord && $nextButOneWord && preg_match($editionRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
                    $edition = isset($matches[5]) ? $matches[5] : $matches[2];
                    $this->verbose("Ending title, case 3b");
                    $title = rtrim(implode(' ', $initialWords), ' ,');
                    array_splice($remainingWords, 0, 2);
                    $remainder = implode(' ', $remainingWords);
                    break;
                }
                
                // If end of title has not been detected and word ends in period-equivalent or comma,
                if (Str::endsWith($word, ['.', '!', '?', ','])) {
                    // if next letter is lowercase (in which case '.' ended abbreviation?) and does not end in period
                    // or following string starts with a part designation, continue, skipping next word,
                    if (
                        $nextWord 
                            && (
                               (mb_strtolower($nextWord[0]) == $nextWord[0] && substr($nextWord, -1) != '.')
                                     || Str::startsWith($remainder, ['I. ', 'II. ', 'III. '])
                                )
                        ) {
                        $this->verbose("Not ending title, case 1 (next word is " . $nextWord . ")");
                        $skipNextWord = true;
                    // else if next word is short and ends with period, assume it is the first word of the publication info, which
                    // is an abbreviation,
                    } elseif ($nextWord && strlen($nextWord) < 8 && Str::endsWith($nextWord, '.')) {
                        $this->verbose("Ending title, case 4");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // else if following string up to next period contains only letters and spaces and doesn't start with "in"
                    // (which is unlikely to be within a title following punctuation)
                    // and is followed by at least 40 characters (for the publication info), assume it is part of the title,
                    } elseif (preg_match('/[a-zA-Z ]+/', $stringToNextPeriod)
                            && !preg_match($this->inRegExp1, $remainder)
                            && strlen($remainder) > strlen($stringToNextPeriod) + 40) {
                        $this->verbose("Not ending title, case 2 (next word is " . $nextWord . ")");
                    // else if working paper string occurs later in remainder,
                    } elseif (preg_match('/(.*)(' . $this->workingPaperRegExp . ')/', $remainder, $matches)) {
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
                    } elseif (!$periodFound 
                            && $this->containsFontStyle($remainder, false, 'italics', $startPos, $length)) {
                                //dd($remainder, $startPos, $word, $periodFound, $stringToNextPeriod);
                        $this->verbose("Not ending title, case 4 (italics is coming up)");
                    // else if word ends with comma and volume info is coming up, wait for it
                    } elseif (Str::endsWith($word, [',']) && preg_match('/' . $this->volumeRegExp1 . '/', $remainder)) {
                         $this->verbose("Not ending title, case 5 (volume info is coming up)");
                    } else {
                        // else if word ends with period and there are 5 or more words till next punctuation, which is a period,
                        // and at least three non-stopwords are all lowercase, continue [to catch Example 116]
                        $wordsToNextPeriod = explode(' ',  $stringToNextPeriod);
                        $lcWordCount = 0;
                        foreach ($wordsToNextPeriod as $remainingWord) {
                            if (!in_array($remainingWord, $this->stopwords) && mb_strtolower($remainingWord) == $remainingWord) {
                                $lcWordCount++;
                            }
                        }
                        if ($lcWordCount > 2
                            && Str::endsWith($word, ['.']) 
                            && substr_count($stringToNextPeriod, ',') == 0
                            && substr_count($stringToNextPeriod, ':') == 0
                            && substr_count($stringToNextPeriod, ' ') > 3

                        ) {
                            $this->verbose("Not ending title, case 6");
                        // otherwise assume the punctuation ends the title.
                        } else {
                            $this->verbose("Ending title, case 6");
                            $title = rtrim(implode(' ', $initialWords), '.,');
                            break;
                        }
                    }
                } 
            }
        }

        // If no title has been identified and $originalRemainder contains a comma, take title to be string up to first comma.
        // What to do if $remainder contains no comma?
        if (!$title && Str::contains($originalRemainder, ',')) {
            $this->verbose("Title not clearly identified; setting it equal to string up to first comma");
            $title = Str::before($remainder, ',');
            $newRemainder = ltrim(Str::after($remainder, ','), ' ');
        }

        $remainder = isset($newRemainder) ? $newRemainder : $remainder;

        return $title;
    }

    public function requireUc(string $string): string
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

    /*
     * Get next entry in BibTeX file (for check routine)
     */
    /*
    public function getEntry($uploadfile, $entryIndex = 0, $string = null) {
        $itemTypes = array('article', 'book', 'booklet', 'conference', 'inbook', 'incollection', 'inproceedings', 'manual', 'mastersthesis', 'misc', 'phdthesis', 'proceedings', 'techreport', 'unpublished');

        $fields = array('address', 'annote', 'author', 'booktitle', 'chapter', 'edition', 'editor', 'howpublished', 'institution', 'journal', 'key', 'month', 'note', 'number', 'organization', 'pages', 'publisher', 'school', 'series', 'title', 'type', 'volume', 'year');

        $format = 'bib';

        $item = new bibtex;
        $item->originalFormat = $format;

        // $uploadfile is an array of lines in the uploaded file
        // filearray is an array, with one line in each component
        // Each component has \n at the end
        if ($string) {
            $entryRemains = $string;
        } else {
            $string = implode("", file($uploadfile));

            $num = 0;
            // count total number of items
            foreach ($itemTypes as $itemType) {
                $num += substr_count(mb_strtolower($string), '@' . $itemType);
            }

            // no BibTeX entries in file
            if (!$num) {
                return false;
            }

            $remains = $string;

            $entryNumber = -1;

            while ($entryNumber < $entryIndex) {
                $atPos = strpos($remains, '@');
                // get item type
                foreach ($itemTypes as $itemType) {
                    if (mb_strtolower(substr($remains, $atPos + 1, strlen($itemType) + 1)) == $itemType . '{') {
                        $entryNumber++;
                        break;
                    }
                }
                $remains = substr($remains, $atPos + 1);
            }

            $bibtexItem['itemType'] = $itemType;
            $item->itemType = $itemType;

            $pos = strlen($itemType) + 1;
            $remains = substr($remains, $pos);

            // get item label
            $commaPos = strpos($remains, ',');

            $itemLabel = trim(substr($remains, 0, $commaPos));
            $bibtexItem['label'] = $itemLabel;
            $item->itemLabel = $itemLabel;

            $remains = substr($remains, $commaPos + 1);

            // look for brace that ends entry
            $bracketLevel = 0;
            for ($j = 0; $j < strlen($remains); $j++) {
                if ($remains[$j] == '{') {
                    $bracketLevel++;
                } elseif ($remains[$j] == '}') {
                    if ($bracketLevel) {
                        $bracketLevel--;
                    } else {
                        break;
                    }
                }
            }

            $entry = substr($remains, 0, $j);
            $entry = str_replace("\r\n", " ", $entry);
            $entry = str_replace("\n", " ", $entry);
            $entry = str_replace("\t", " ", $entry);
            $entryRemains = $entry;
        }

        while (strpos($entryRemains, '=') !== false) {
            $equalsPos = strpos($entryRemains, '=');
            $field = mb_strtolower(trim(substr($entryRemains, 0, $equalsPos)));

            $entryRemains = substr($entryRemains, $equalsPos + 1);

            // trim spaces on left
            $entryRemains = ltrim($entryRemains, " ");

            if ($entryRemains[0] == '{') {
                $bracketLevel = 0;
                for ($j = 1; $j < strlen($entryRemains); $j++) {
                    if ($entryRemains[$j] == '{') {
                        $bracketLevel++;
                    } elseif ($entryRemains[$j] == '}') {
                        if ($bracketLevel) {
                            $bracketLevel--;
                        } else {
                            break;
                        }
                    }
                }
                $endPos = $j;
                $fieldValue = trim(substr($entryRemains, 1, $endPos - 1));
            } else {
                if ($entryRemains[0] == '"') {
                    $entryRemains = substr($entryRemains, 1);
                    $endPos = strpos(str_replace('\\"', "xx", $entryRemains), '"');
                    $fieldValue = trim(substr($entryRemains, 0, $endPos));
                } else {
                    $fieldValue = '';
                    for ($j = 0; $j < strlen($entryRemains) && in_array($entryRemains[$j], array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0')); $j++) {
                        $fieldValue .= $entryRemains[$j];
                    }
                    $endPos = $j;
                }
            }

            $bibtexItem[$field] = $fieldValue;

            if (in_array($field, $fields)) {
                $item->$field = $fieldValue;
            } else {
                $item->unidentified .= $fieldValue;
            }

            // go to next comma
            for ($i = $endPos; $i < strlen($entryRemains) && $entryRemains[$i] != ','; $i++) {

            }

            $entryRemains = substr($entryRemains, $i + 1);
        }

        $returner = array('num' => isset($num) ? $num : null, 'entry' => isset($entry) ? $entry : null, 'item' => $item, 'bibtexItem' => $bibtexItem);

        return $returner;
    }
    */

    // Truncate $string at first '%' that is not preceded by '\'.  Return 1 if truncated, 0 if not.
    public function uncomment(string &$string) : bool
    {
        for ($i = 0; 
             $i < strlen($string) && ($string[$i] != '%' || ($i && $string[$i] == '%' && $string[$i - 1] == "\\"));
             $i++) {}
        $truncated = ($i < strlen($string));
        $string = substr($string, 0, $i);

        return $truncated;
    }

    // Replace every substring of multiple spaces with a single space.  (\h is a horizontal white space.)
    public function regularizeSpaces(string $string): string
    {
        // Using \h seems to mess up utf-8
        //return preg_replace('%\h+%', ' ', $string);  
        return preg_replace('% +%', ' ', $string);  
    }

    /*
     * Remove all matches for $regExp (regular expression without delimiters), case insensitive, from $string
     * and return resulting string (unaltered if there are no matches).
     */
    public function findAndRemove(string $string, string $regExp): string
    {
        return preg_replace('%' . $regExp . '%i', '', $string);
    }

    /*
     * Find first match for $regExp (regular expression without delimiters), case insensitive, in $string,
     * return group number $groupNumber (defined by parentheses in $regExp)
     * and remove entire match for $regExp from $string after triming ',. ' from substring preceding match.
     * If no match, return false (and do not alter $string).
     */
    public function findRemoveAndReturn(string &$string, string $regExp): false|string|array
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
        for ($i = 0; $i < 100; $i++) {
            if (isset($matches[$i][0])) {
                $result[$i] = $matches[$i][0];
            } else {
                break;
            }
        }
        // if (is_array($groupNumber)) {
        //     foreach ($groupNumber as $number) {
        //         $result[] = $matches[$number][0];
        //     }
        // } else {
        //     $result[] = $matches[$groupNumber][0];
        // }

        //$string = trim(substr($string, 0, $matches[0][1]), ',. ') . substr($string, $matches[0][1] + strlen($matches[0][0]), strlen($string));
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
    public function extractLabeledContent(string &$string, string $labelPattern, string $contentPattern, bool $reportLabel = false): false|string|array
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

        if ($reportLabel) {
            $returner = ['label' => trim($matches['label'][0]), 'content' => $content];
        } else {
            $returner = $content;
        }

        // $content = trim($matches[2][0], ' .,;');
        // $string = substr($string, 0, $matches[1][1]) . substr($string, $matches[2][1] + strlen($matches[2][0]), strlen($string));
        // $string = $this->regularizeSpaces(trim($string, ' .,'));

        // if ($reportLabel) {
        //     $returner = ['label' => trim($matches[1][0]), 'content' => $content];
        // } else {
        //     $returner = $content;
        // }

        return $returner;
    }
    
    /*
     * Split an array of words into sentences.  Each period that 
     * does not follow a single uc letter 
     * AND follows a word that is (EITHER in the dictionary OR follows an initial [in which case it is presumably a name])
     * AND is not an excluded word
     * ends a sentence.
    */
    public function splitIntoSentences(array $words): array
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
                && !in_array(substr($word,0, -1), $this->excludedWords)
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
     * containsFontStyle: report if string contains opening string for font style, at start
     * if $start is true
     * @param $string string The string to be searched
     * @param $start boolean: true if want to restrict to font style starting the string
     * @param $style string: 'italics' [italics or slanted] or 'bold'
     * @param $startPos: position in $string where font style starts
     * @param $length: length of string starting font style
     */
    public function containsFontStyle(string $string, bool $start, string $style, int|null &$startPos, int|null &$length): bool
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

    /**
     * GENERAL COMMENT: This function is an attempt to determine the number of verbal segments in an entry,
     * where a segment is the name of an author, the title, or the journal, for example (but not any numeric information like
     * the year or volume or page numbers.  The idea is that when looking for authors in an entry, not too many segments should
     * be absorbed---at least the title and publication info should be left.  If it worked perfectly, it would do most of the work
     * of the class, so perhaps it is misguided.  Currently it generally overestimates the number of segments.  It helps in cases
     * like
     * Vijay Krishna, Auction Theory, Academic Press, 1992
     * where it finds 3 segments, so that convertToAuthors knows to look only for one (given that a title and publication information
     * must be left).  But it may not help in many other cases.
     *
     * $words: array of "words" (result of explode(" ", $string))
     * Attempt to count number of segments that could the author names, title, and journal/publisher etc.
     * count a segment if it ends in ',' or '.' and is not numeric and not numeric after '-' is deleted
     * and not a single character (e.g. author's initial)
     */
    /*
    public function wordSegmentCount(array $words, bool $countAndAsSeparator = false): array
    {
        $segmentCount = 0;
        $isAnd = false;
        $andStartingSegments = [];
        $segment = '';
        foreach ($words as $i => $word) {
            //print "word: " . $word . "<BR>";
            // if previous word was 'and' and this word starts with a lowercase letter, then the 'and' didn't end a segment
            // (because a segment cannot start with a lowercase letter)
            if ($countAndAsSeparator && $isAnd && mb_strtolower($word[0]) == $word[0]) {
                $segmentCount--;
            }
            $segment .= $word;
            $isAnd = $this->isAnd($word);

            if (
                // if word ends in ',', '.', '}', '"', or "'", or $word is last one or 
                // (we're counting 'and' as a separator and the word is 'and' and the whole segments isn't 'and'),  
                (
                    in_array(substr($word, -1), [',', '.', '}', '"', "'"])
                    || $i = count($words) - 1 
                    || ($countAndAsSeparator && $isAnd && $segment != $this->phrases['and'])
                )
                // and segment is not solely digits and '-', and segment isn't a single character possibly followed by . or ,
                // and segment isn't 'pp' or 'pp.'
                && !ctype_digit(str_replace("-", "", trim($segment, '.,')))
                && strlen(trim($segment, ',.')) > 1
                && trim($segment, '.') != 'pp'
                ) {
                    // then end segment and start new one.
                    $segmentCount++;
                    $segment = '';
            }
            if ($isAnd) {
                $andStartingSegments[] = $segmentCount + 1;
            }
        }

        return ['count' => $segmentCount, 'andStartingSegments' => $andStartingSegments];
    }
    */

    public function isInitials(string $word): bool
    {
        $case = 0;
        if (preg_match('/^[A-Z]\.?$/', $word)) {
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
        * Determine whether $word is component of a name: all letters and either all u.c. or first letter u.c. and rest l.c.
        * If $finalPunc != '', then allow word to end in any character in $finalPunc.
        */

    public function isName($word, $finalPunc = '') {
        $result = false;
        if (in_array(substr($word, -1), str_split($finalPunc))) {
            $word = substr($word, 0, -1);
        }
        if (ctype_alpha($word) and ( ucfirst($word) == $word or strtoupper($word) == $word)) {
            $result = true;
        }

        return $result;
    }

    /*
     * Determine whether string is plausibly a list of names
     * The method checks only the first 2 or 3 words in the string, not the whole string
    */
    public function isNameString($string) {
        $phrases = $this->phrases;
        $this->verbose("isNameString is examining string \"" . $string . "\"");
        $result = false;
        $words = explode(' ', $string);
        $word1 = count($words) > 1 ? rtrim($words[1], ',.') : null;
        if ($this->isInitials($words[0]) && count($words) >= 2) {
            $this->verbose('First word is initials and at least 2 words in string');
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
        } elseif ($this->isName($words[0], ',') && count($words) == 2 && $this->isName($word1, '.')) {
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
     * Report whether string is a date, in a range of formats, including 2 June 2018, 2 Jun 2018, 2 Jun. 2018, June 2, 2018,
     * 6-2-2018, 6/2/2018, 2-6-2018, 2/6/2018.
    */
    public function isDate(string $string): bool
    {
        $str = str_replace([","], "", trim($string, ',. '));
        $isDate1 = preg_match('/^[1-3]?[0-9] (' . $this->monthsRegExp . ') (19|20)[0-9][0-9]/', $str);
        $isDate2 = preg_match('/^(' . $this->monthsRegExp . ') [1-3]?[0-9] (19|20)[0-9][0-9]/', $str);
        $isDate3 = preg_match('/^[1-3]?[0-9][\-\/][01]?[0-9][\-\/](19|20)[0-9][0-9]/', $str);
        $isDate4 = preg_match('/^[01]?[0-9][\-\/][1-3]?[0-9][\-\/](19|20)[0-9][0-9]/', $str);
        $isDate5 = preg_match('/^(19|20)[0-9][0-9][\-\/][1-3]?[0-9][\-\/][01]?[0-9]/', $str);

        return $isDate1 || $isDate2 || $isDate3 || $isDate4 || $isDate5;
    }

    public function isAnd(string $string): bool
    {
        return mb_strtolower($string) == $this->phrases['and'] || in_array($string, ['\\&', '&']);
    }

    public function getStringBeforeChar(string $string, string $char): string
    {
        return Str::before($string, $char);
    }

    /*
     * Determine whether string plausibly starts with a name
     * The method checks only the first few words in the string, not the whole string
    */
    public function initialNameString($string)
    {
        $phrases = $this->phrases;
        $result = false;
        $words = explode(' ', $string);
        if ($this->isInitials($words[0])) {
            if (isset($words[1]) and $this->isName($words[1], '.')) {
                $result = true;
            } elseif (isset($words[1]) and $this->isInitials($words[1])
                    and isset($words[2]) and $this->isName($words[2], '.')) {
                $result = true;
            }
        } elseif ($this->isName($words[0], ',;') and isset($words[1]) and $this->isInitials($words[1])) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') and isset($words[1]) and $this->isName($words[1], '.')) {
            $result = true;
        } elseif ($this->isName($words[0], ',;') and isset($words[1]) and $this->isName($words[1]) and isset($words[2]) and $words[2] == $phrases['and']) {
            $result = true;
        }

        return $result;
    }

    public function verbose(string|array $arg)
    {
        $this->displayLines[] = $arg;
    }

    /**
     * ! Method needs to be re-worked; the logic is currently hard to follow.
     * WARNING: TAKE CARE WHEN MODIFYING THIS FUNCTION.  Modifications that improve the parsing of
     *          some strings may well make worse the parsing of other strings.
     * convertToAuthors: determine the authors in the initial elements of an array of words
     * @param $words array of words
     * @param $remainder string remaining string after authors removed
     * @param $determineEnd boolean if true, figure out where authors end; otherwise take whole string
     *        to be authors
     * return author string
     */
    public function convertToAuthors(array $words, string|null &$remainder, string|null &$year, bool &$isEditor, bool $determineEnd = true): array
    {
        $namePart = $prevWordAnd = $done = $authorIndex = $case = 0;
        $isEditor = $hasAnd = $multipleAuthors = false;
        $authorstring = $fullName = '';
        $remainingWords = $words;
        $warnings = [];
        //$phrases = $this->phrases;

        // $maxAuthors is an attempt to deal with entries like "Vijay Krishna, Auction Theory, Academic Press, 1992",
        // where the authors might otherwise be taken to be Vijay Krishna, Auction Theory, and Academic Press.
        // The idea is that at least two segments have to be devoted to the title and publication information, so in
        // a case like this we know that there is at most one author
        // At least one segment has to be title and at least one segment has to be publication info (e.g. journal name)

        /*
          $segmentCount = $this->wordSegmentCount($words, true);
          $wordSegmentCount = $segmentCount['count'];

          $andStartingSegments = $segmentCount['andStartingSegments'];
          // Largest number of authors possible is $wordSegmentCount - 2.  But that isn't possible if next segment starts "and",
          // because title cannot start with "and".
          if($fullEntry) {
          for($i = $wordSegmentCount - 2; $i > 0; $i--) {
          if(!in_array($i+1, $andStartingSegments)) break;
          }
          $maxAuthors = max($i, 1);
          } else $maxAuthors = 100;
         */

        $maxAuthors = 100;
        $wordHasComma = $prevWordHasComma = $oneWordAuthor = false;

        $this->verbose('convertToAuthors: Looking at each word in turn');
        foreach ($words as $i => $word) {
            $prevWordHasComma = $wordHasComma;
            $wordHasComma = (substr($word,-1) == ',');
            // Get letters in word, eliminating accents, to get accurate length
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
                    // deal with names like Bruine de Bruin, W.
                    && isset($words[$i+1]) // must be at least 2 words left
                    && ! $this->isAnd($words[$i+1]) // next word is not 'and'
                )  {
                $namePart = 0;
                $authorIndex++;
            }
            if ($authorIndex >= $maxAuthors) {
                break;  // exit from foreach
            }
            $vString = $case ? "[convertToAuthors case " . $case . "] authorstring: " . ($authorstring ? $authorstring : '[empty]') . "." : "";
            $this->verbose($vString);
            if (isset($bareWords)) {
                $this->verbose("bareWords: " . implode(' ', $bareWords));
            }
            unset($bareWords);
            if (isset($reason)) {
                $this->verbose('Reason: ' . $reason);
            }
            if (in_array($case, [11, 12, 14]) && $done) {  // 14: et al.
                break;
            }
            $this->verbose(['text' => 'Word ' . $i . ": ", 'words' => [$word], 'content' => " - authorIndex: " . $authorIndex . ", namePart: " . $namePart]);
            $this->verbose("fullName: " . $fullName);
            
            if (isset($itemYear)) {
                $year = $itemYear;
            }
            if ($year) {
                $this->verbose("Year: " . $year);
            }

            // remove first word from $remainingWords
            array_shift($remainingWords);

            if ($authorIndex > 1) {
                $multipleAuthors = true;
            }

            if (in_array($word, [" ", "{\\sc", "\\sc"])) {
                //
            } elseif ($this->isEd($word, $multipleAuthors)) {
                $this->verbose("String for editors, so ending name string (word: " . $word .")");
                // word is 'ed' or 'ed.' if $hasAnd is false, or 'eds' or 'eds.' if $hasAnd is true
                $isEditor = true;
                $remainder = implode(" ", $remainingWords);
                if ($namePart == 0) {
                    $warnings[] = "String for editors detected after only one part of name.";
                }
                // Check for year following editors
                if ($year = $this->getYear($remainder, true, $remains, false, $trash)) {
                    $remainder = $remains;
                    $this->verbose("Year detected, so ending name string (word: " . $word .")");
                } else {
                    $this->verbose("String indicating editors (e.g. 'eds') detected, so ending name string");
                }
                // Under some conditions (von name?), $fullName has already been added to $authorstring.
                if (!Str::endsWith($authorstring, $fullName)) {
                    $authorstring .= $fullName;
                }
                break;  // exit from foreach
            } elseif ($determineEnd && $done) {
                break;  // exit from foreach
            } elseif ($this->isAnd($word)) {
                // word is 'and' or equivalent
                $hasAnd = $prevWordAnd = true;
                $authorstring .= $this->formatAuthor($fullName) . ' and';
                $fullName = '';
                $namePart = 0;
                $authorIndex++;
                $case = 1;
                $reason = 'Word is "and" or equivalent';
            } elseif ($word == 'et') {
                // word is 'et'
                $nextWord = rtrim($words[$i+1], ',');
                $this->verbose('nextWord: ' . $nextWord);
                if (in_array($nextWord, ['al.', 'al'])) {
                    $authorstring .= $this->formatAuthor($fullName) . ' and others';
                    array_shift($remainingWords);
                    $remainder = implode(" ", $remainingWords);
                    $done = 1;
                    $case = 14;
                    $reason = 'Word is "et" and next word is "al." or "al"';
                } else {
                    $this->verbose("'et' not followed by 'al' or 'al.', so not sure what to do");
                }
            } elseif ($determineEnd && substr($word, -1) == '.' && strlen($lettersOnlyWord) > 3
                    && mb_strtolower(substr($word, -2, 1)) == substr($word, -2, 1)) {
                // If $determineEnd and word ends in period and word has > 3 chars (hence not "St.") and previous letter
                // is lowercase (hence not string of initials without spaces):
                if ($namePart == 0) {
                    // If $namePart == 0, something is wrong (need to choose an earlier end for name string) UNLESS string is
                    // followed by year, in which case we may have an entry like "Economist. 2005. ..."
                    if ($year = $this->getYear(implode(" ", $remainingWords), true, $remainder, false, $trash)) {
                        // don't use spaceOutInitials in this case, because string is not initials.  Could be
                        // something like autdiogames.net, in which case don't want to put space before period.
                        //$nameComponent = $this->spaceOutInitials($word);
                        $nameComponent = $word;
                        $fullName .= trim($nameComponent, '.');
                        $authorstring .= $fullName;
                        $case = 2;
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string starts with year';
                        $oneWordAuthor = true;
                        $itemYear = $year; // because $year is recalculated below
                        //break;
                    } else {
                        //$revisedAuthorstring = substr($authorstring, 0, $possibleLastChar);
                        //$remainder = implode(" ", array_slice($words, $possibleLastWord+1));
                        //$fullName = $revisedAuthorstring;
                        //$authorstring = $revisedAuthorstring;
                        // Case like: "Arrow, K. J., Hurwicz. L., and ..." [note period at end of Hurwicz]
                        // "Hurwicz" is current word, which is added back to the remainder.
                        // (Ideally the script should realize the typo and still get the rest of the authors.)
                        $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo? Re-processed with comma instead of period.";
                        $case = 3;
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string does not start with year';
                        // $authorstring .= $this->formatAuthor($fullName);
                        // $remainder = $word . " " . $remainder;
                        $word = substr($word, 0, -1) . ',';
                        goto namePart0;
                    }
                } else {
                    // If $namePart > 0
                    $nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, '.')));
                    $fullName .= " " . $nameComponent;
                    $authorstring .= $this->formatAuthor($fullName);
                    $remainder = implode(" ", $remainingWords);
                    $case = 4;
                    $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, and namePart is > 0';
                }
                $this->verbose("Remainder: " . $remainder);
                if ($year = $this->getYear($remainder, true, $remains, false, $trash)) {
                    $remainder = $remains;
                }
                $this->verbose("Remains: " . $remains);
                $done = 1;
            } elseif ($namePart == 0) {
                namePart0:
                // Check if $word and first word of $remainingWords are plausibly a name.  If not, end search if $determineEnd.
                if ($determineEnd && isset($remainingWords[0]) && $this->isNotName($word, $remainingWords[0])) {
                    $fullName .= $word;
                    $remainder = implode(" ", $remainingWords);
                    $authorstring .= $this->formatAuthor($fullName);
                    if ($year = $this->getYear($remainder, true, $remains, false, $trash)) {
                        $remainder = $remains;
                        $this->verbose("Year detected");
                    }
                    $case = 5;
                    $done = 1;
                } else {
                    if (!$prevWordAnd && $authorIndex) {
                        $authorstring .= $this->formatAuthor($fullName) . ' and';
                        $fullName = '';
                        $prevWordAnd = 1;
                    }
                    //dd($authorstring);
                    $name = $this->spaceOutInitials($word);
                    // If part of name is all uppercase and 3 or more letters long, convert it to ucfirst(mb_strtolower())
                    // For component with 1 or 2 letters, assume it's initials and leave it uc (to be processed by formatAuthor)
                    $nameComponent = (strlen($name) > 2 && strtoupper($name) == $name && strpos($name, '.') === false) ? ucfirst(mb_strtolower($name)) : $name;
                    $fullName .= ' ' . $nameComponent;
                    if (in_array($word, $this->vonNames)) {
                        $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name");
                    } elseif (!Str::endsWith($words[$i], ',') 
                                && isset($words[$i+1]) 
                                && Str::endsWith($words[$i+1], ',') 
                                && !$this->isInitials(substr($words[$i+1], 0, -1)) 
                                && isset($words[$i+2]) 
                                && Str::endsWith($words[$i+2], ',') 
                                && !$this->isYear(trim($words[$i+2], ',()[]'))) {
                        // $words[$i] does not end in a comma AND $words[$i+1] is set and ends in a comma and is not initials AND $words[$i+2]
                        // is set and ends in a comma AND $words[$i+2] is not a year.
                        // E.g. Ait Messaoudene, N., ...
                        $this->verbose("convertToAuthors: '" . $words[$i] . "' identified as first segment of last name, with '" . $words[$i+1] . "' as next segment");
                    } else {
                        $namePart = 1;
                    }
                    // following occurs in case of name that is a single string, like "IMF"
                    if ($year = $this->getYear(implode(" ", $remainingWords), true, $remains, false, $trash)) {
                        $remainder = $remains;
                        $done = 1;
                    }
                    $case = 6;
                }
            } else {
                // namePart > 0 and word doesn't end in some character, then lowercase letter, then period
                $prevWordAnd = 0;

                // 2023.8.2: trimRightBrace removed to deal with conversion of example containing name Oblo{\v z}insk{\' y}
                // However, it must have been included here for a reason, so probably it should be included under
                // some conditions.
                //$nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, ',;')));
                if (Str::startsWith($word, ['Jr.', 'Sr.', 'III'])) {
                    $nameWords = explode(' ', trim($fullName, ' '));
                    if (count($nameWords) == 1) {
                        $fullName = $nameWords[0] . ' ' . $word;
                    } else {
                        // put Jr. or Sr. in right place for BibTeX: format is lastName, Jr., firstName OR lastName Jr., firstName.
                        // Assume last name is single word that is followed by a comma (which covers both
                        // firstName lastName, Jr. and lastName, firstName, Jr.
                        $fullName = ' ';
                        // put Jr. after the last name
                        $k = 0;
                        foreach ($nameWords as $j => $nameWord) {
                            if (substr($nameWord, -1) == ',') {
                                $fullName .= $nameWord . ' ' . rtrim($word, ',') . ',';
                                $k = $j;
                            }
                        }
                        // put the rest of the names after Jr.
                        foreach ($nameWords as $m => $nameWord) {
                            if ($m != $k) {
                                $fullName .= ' ' . $nameWord;
                            }
                        }
                        $namePart = 0;
                        $authorIndex++;
                        if ($year = $this->getYear(implode(" ", $remainingWords), true, $remains, false, $trash)) {
                            $remainder = $remains;
                            $authorstring .= $this->formatAuthor($fullName);
                            $done = 1;
                        }
                    }
                    $this->verbose('Name with Jr., Sr., or III; fullName: ' . $fullName);
                    $case = 15;
                } else {
                    $nameComponent = $this->spaceOutInitials(rtrim($word, ',;'));
                    $fullName .= " " . $nameComponent;
                    //$authorstring .= " " . $nameComponent;
                    // $bareWords is array of words at start of $remainingWords that don't end in ',' or '.' or ')' or ':' and
                    // are not year in parens or brackets and
                    $bareWords = $this->bareWords($remainingWords, false);
                    // If 'and' has not already occurred ($hasAnd is false), its occurrence in $barewords is compatible
                    // with $barewords being part of the authors' names OR being part of the title, so should be ignored.
                    $nameScore = $this->nameScore($bareWords, !$hasAnd);
                    $this->verbose("bareWords (no trailing punct, not year in parens): " . implode(' ', $bareWords));
                    $this->verbose("nameScore: " . $nameScore['score']);
                    if ($nameScore['count']) {
                        $this->verbose('nameScore per word: ' . number_format($nameScore['score'] / $nameScore['count'], 2));
                    }

                    if ($determineEnd && $this->getQuotedOrItalic(implode(" ", $remainingWords), true, false, $remains, $posStart, $posEnd)) {
                        $remainder = implode(" ", $remainingWords);
                        $done = 1;
                        $authorstring .= $this->formatAuthor($fullName);
                        $case = 7;
                    } elseif ($determineEnd && $year = $this->getYear(implode(" ", $remainingWords), true, $remainder, false, $trash)) {
                        $done = 1;
                        $fullName = ($fullName[0] != ' ' ? ' ' : '') . $fullName;
                        $authorstring .= $this->formatAuthor($fullName);
                        //$authorstring .= (substr($authorstring, -1) != ' ' ? ' ' : '') . $this->formatAuthor($fullName);
                        $case = 8;
                        $reason = 'Remainder starts with year';
                    } elseif ($determineEnd && count($bareWords) > 2 && $nameScore['score'] / $nameScore['count'] < 0.25 && ! $this->isInitials($remainingWords[0])) {
                        // Low nameScore relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                        // Note that this check occurs only when $namePart > 0---so it rules out double-barelled
                        // family names that are not followed by commas.  ('Paulo Klinger Monteiro, ...' is OK.)
                        // Cannot set limit to be > 1 bareWord, because then '... Smith, Nancy Lutz and' gets truncated
                        // at comma.
                        $done = 1;
                        $authorstring .= $this->formatAuthor($fullName);
                        $case = 9;
                    } elseif (Str::endsWith($word, [',', ';']) && isset($words[$i + 1]) && ! $this->isEd($words[$i + 1], $hasAnd)) {
                        // $word ends in comma or semicolon and next word is not string for editors
                        if ($hasAnd) {
                            // $word ends in comma or semicolon and 'and' has already occurred
                            // To cover the case of a last name containing a space, look ahead to see if next words
                            // are initials or year.  If so, add back comma taken off above and continue.  Else done.
                            if ($i + 3 < count($words)
                                    && (
                                    $this->isInitials($words[$i + 1])
                                    || $this->getYear($words[$i + 2], true, $trash, false, $trash2)
                                    || ( $this->isInitials($words[$i + 2]) && $this->getYear($words[$i + 3], true, $trash, false, $trash2))
                                    )
                            ) {
                                $fullName .= ',';
                                $case = 10;
                            } else {
                                $done = 1;
                                $authorstring .= $this->formatAuthor($fullName);
                                $case = 11;
                            }
                        } else {
                            // If word ends in comma or semicolon and 'and' has not occurred.
                            // To cover case of last name containing a space, look ahead to see if next word
                            // is a year.  (Including case of next word is initials messes up other cases.)
                            // If so, add back comma and continue.
                            // (Of course this routine won't do the trick if there are more authors after this one.  In
                            // that case, you need to look further ahead.)
                            if (!$prevWordHasComma && $i + 2 < count($words)
                                    && (
                                        //$this->isInitials($words[$i + 1])
                                        //or
                                        $this->getYear($words[$i + 2], true, $trash, false, $trash2)
                                    )) {
                                $fullName .= ',';
                                $case = 14;
                            } else {
                                if ($authorIndex == 0) {
                                    // neither of following two variables are used
                                    $possibleLastChar = strlen($authorstring);
                                    $possibleLastWord = $i;
                                }
                                // Low name score relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                                if ($nameScore['count'] > 2 && $nameScore['score'] / $nameScore['count'] < 0.25) {
                                        $authorstring .= $this->formatAuthor($fullName);
                                    $done = 1;
                                }
                                $case = 12;
                            }
                        }
                    } else {
                        if (in_array($word, $this->vonNames)) {
                            $this->verbose("convertToAuthors: '" . $word . "' identified as 'von' name, so 'namePart' not incremented");
                        } else {
                            $namePart++;
                        }
                        if ($i + 1 == count($words)) {
                            $authorstring .= $this->formatAuthor($fullName);
                        }
                        //$this->verbose("authorstring: " . $authorstring);
                        $case = 13;
                    }
                }
            }
        }

        return ['authorstring' => $authorstring, 'warnings' => $warnings, 'oneWordAuthor' => $oneWordAuthor];
    }

    /*
     * isEd: determine if (string is 'Eds.' or '(Eds.)' or '(Eds)' or 'eds.' or '(eds.)' or '(eds)' and multiple == true)
     * or (singular version and multiple == false)
     * @param $string string
     * @param $multiple boolean
     */

    public function isEd(string $string, bool $multiple = false): bool
    {
        if (($multiple && preg_match('/^\(?[Ee]ds\.?\)?,?$/', $string)) 
           ||
           (!$multiple && preg_match('/^\(?[Ee]d\.\)?,?$/', $string))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getQuotedOrItalic: get first quoted or italicized substring in $string, restricting to start if $start
     * is true and getting only italics if $italicsOnly is true.
     * Quoted means starts with `` or unescaped " and ends with '' or unescaped " OR starts with ' or ` preceded by space and ends with '
     * preceded by non-letter
     * @param $string string
     * @param $start boolean (if true, check only for substring at start of string)
     * @param $italicsOnly boolean (if true, get only italic string, not quoted string)
     * @param $remains what is left of the string after the substring is removed
     * return quoted or italic substring
     */
    public function getQuotedOrItalic($string, $start, $italicsOnly, &$remains, &$posStart, &$posEnd) {
        $quoted = '';
        $remains = $string;
        $quoteExists = 0;

        if ($italicsOnly) {
            $containsQuote = 0;
        } else {
            // Could use the following?
            //  if(preg_match('/``|[^\\\\]"/', $string, $matches, PREG_OFFSET_CAPTURE)) {}

            // check for `` or " or ' or ` in string
            $posQuote1 = (strpos($string, "``") !== false) ? strpos($string, "``") : strlen($string);
            $lenQuote1 = 2;

            for ($j = 0; $j < strlen($string) and!($string[$j] == '"' and ( $j == 0 or $string[$j - 1] != '\\')); $j++) {

            }
            $posQuote2 = $j < strlen($string) ? $j : strlen($string);
            $lenQuote2 = 1;

            for ($j = 0; $j < strlen($string) and!($string[$j] == "'" and ( $j == 0 or $string[$j - 1] == ' ')); $j++) {

            }
            $posQuote3 = $j < strlen($string) ? $j : strlen($string);
            $lenQuote3 = 1;

            for ($j = 0; $j < strlen($string) and!($string[$j] == "`" and ( $j == 0 or $string[$j - 1] == ' ')); $j++) {

            }
            $posQuote4 = $j < strlen($string) ? $j : strlen($string);
            $lenQuote4 = 1;

            $containsQuote = 1;
            // if string contains at least one of ``, ", and ', take first one to start quote.
            if (min([$posQuote1, $posQuote2, $posQuote3, $posQuote4]) < strlen($string)) {
                // Note: $posQuote1 is either equal to $posQuote4 or is strlen($string)
                if ($posQuote1 < min([$posQuote2, $posQuote3])) {
                    $posQuote = $posQuote1;
                    $lenQuote = $lenQuote1;
                    $quoteCharacterType = 1;
                } elseif ($posQuote2 < min([$posQuote1, $posQuote3, $posQuote4])) {
                    $posQuote = $posQuote2;
                    $lenQuote = $lenQuote2;
                    $quoteCharacterType = 2;
                } elseif ($posQuote3 < min([$posQuote1, $posQuote2, $posQuote4])) {
                    $posQuote = $posQuote3;
                    $lenQuote = $lenQuote3;
                    $quoteCharacterType = 3;
                } else {
                    $posQuote = $posQuote4;
                    $lenQuote = $lenQuote4;
                    $quoteCharacterType = 4;
                }
            } else {
                $containsQuote = 0;
            }
        }

        $containsItalics = $this->containsFontStyle($string, $start, 'italics', $posItalics, $lenItalics);

        // Now look for end of quote/italics
        if (
        // quotation marks come first
                ($containsQuote and $containsItalics
                and ( ($start and $posQuote == 0) or ( !$start and $posQuote < $posItalics)))
                or ( $containsQuote and ! $containsItalics
                and ( ($start and $posQuote == 0) or ! $start))) {
            $quoteLevel = 0;
            $break = false;
            for ($j = $posQuote + $lenQuote; $j < strlen($string); $j++) {
                //$this->verbose("string[j]: " . $string[$j] . ". quoteLevel: " . $quoteLevel);
                // Handle case of embedded quote: e.g. "Bugs and "features""
                switch ($quoteCharacterType) {
                    case 1:
                    case 2:
                        if (($string[$j] == "\"" or ( $string[$j] == "'" and $string[$j + 1] == "'") or ( $string[$j] == "`" and $string[$j + 1] == "`")) and $string[$j - 1] == " ") {
                            $quoteLevel++;
                        } elseif (($string[$j] == "'" and $string[$j + 1] == "'") or ( $string[$j] == "\"" and $string[$j - 1] != "\\")) {
                            if ($quoteLevel > 0) {
                                $quoteLevel--;
                            } else {
                                $break = true;
                            }
                        }
                        break;
                    case 3:
                    case 4:
                        if (($string[$j] == "'" or $string[$j] == "`") and $string[$j - 1] == " ") {
                            $quoteLevel++;
                        } elseif ($string[$j] == "'" and $string[$j - 1] != "\\") {
                            if ($quoteLevel > 0) {
                                $quoteLevel--;
                            } else {
                                $break = true;
                            }
                        }
                        break;
                }
                if ($break) {
                    break;
                }
                /*
                  if(($string[$j] == "\"" or ($string[$j] == "'" and $string[$j+1] == "'")) and $string[$j-1] == " ") $quoteLevel++;
                  elseif(($string[$j] == "'" and $string[$j+1] == "'") or ($string[$j] == "\"" and $string[$j-1] != "\\")) {
                  if($quoteLevel > 0) $quoteLevel--;
                  else break;
                  }
                 */
            }
            $posEnd = $j;
            $posStart = $posQuote;
            $quoteDelimiterLen = $lenQuote;
            $quoteExists = 1;
        } elseif (
        // italics come first
                ($containsQuote and $containsItalics
                and ( ($start and $posItalics == 0) or ( !$start and $posItalics < $posQuote)))
                or ( !$containsQuote and $containsItalics
                and ( ($start and $posItalics == 0) or ! $start))) {
            $this->italicTitle = true;
            // look for matching }
            $bracketLevel = 0;
            for ($j = $posItalics + $lenItalics; $j < strlen($string); $j++) {
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
            $posEnd = $j;
            $posStart = $posItalics;
            $quoteDelimiterLen = $lenItalics;
            $quoteExists = 1;
        }

        if ($quoteExists) {
            $quoted = rtrim(substr($string, $posStart + $quoteDelimiterLen, $posEnd - $quoteDelimiterLen - $posStart), ',.');
            $quoted = $this->trimRightPeriod($quoted);
            $remains = substr($string, 0, $posStart) . ltrim(substr($string, $posEnd + 1), ".' ");
        }
        
        return $quoted;
    }

    /**
     * getBold: get first boldface substring in $string, restricting to start if $start is true
     * @param $string string
     * @param $start boolean (if true, check only for substring at start of string)
     * @param $remains what is left of the string after the substring is removed
     * return bold substring
     */
    public function getBold($string, $start, &$remains) {
        $boldText = '';
        $remains = $string;

        $bracketLevel = 0;
        if ($this->containsFontStyle($string, $start, 'bold', $posBold, $lenBold)) {
            for ($j = $posBold + $lenBold; $j < strlen($string); $j++) {
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
            $boldText = rtrim(substr($string, $posBold + $lenBold, $j - $posBold - $lenBold), ',');
            $boldText = $this->trimRightPeriod($boldText);
            $remains = rtrim(substr($string, 0, $posBold), ' .,') . ltrim(substr($string, $j + 1), ' ,.');
        }

        return $boldText;
    }

    /**
     * getYear: get *last* substring in $string that is a year, unless $start is true, in which case restrict to 
     * start of string and take only first match
     * @param $string string
     * @param $start boolean (if true, check only for substring at start of string)
     * @param $remains what is left of the string after the substring is removed
     * @param $allowMonth boolean (allow string like "(April 1998)" or "(April-May 1998)" or "April 1998:"
     * return year (and pointer to month)
     */
    public function getYear(string $string, bool $start, string|null &$remains, bool $allowMonth, string|null &$month): string
    {
        $year = '';
        $remains = $string;

        // year can be in (1980), [1980], '1980 ', '1980,', '1980.', '1980)', '1980:' or end with '1980' if not at start and
        // (1980), [1980], ' 1980 ', '1980,', '1980.', or '1980)' if at start; instead of 1980, can be of form
        // 1980/1 or 1980/81 or 1980-1 or 1980-81
        // NOTE: '1980:' could be a volume number---might need to check for that
        $months = $this->monthsRegExp;
        $monthRegExp = '((' . $months . ')([-\/](' . $months . '))?)?';
        $yearRegExp = '((18|19|20)([0-9]{2})(-[0-9]{1,2}|\/[0-9]{1,2})?)[a-z]?';
        $regExp0 = $allowMonth ? $monthRegExp . ' ?' . $yearRegExp : $yearRegExp;
        // following line might work to allow format '(1980, April)', but yearIndexes and $monthIndexes would have to be adjusted,
        // which seems very painful ...
        // $regExp0 = $allowMonth ? '(' . $monthRegExp . ' ?' . $yearRegExp .')|(' . $yearRegExp . ', ?' . $monthRegExp . ')' : $yearRegExp;
        /*
          $regExp1 = $yearRegExp . '[ .,)]';
          $regExp2 = $yearRegExp . '$';
         */
        // require space in front of year if search is not restricte to start or in parens or brackets,
        // to avoid picking up second part of page range (e.g. 1913-1920)
        $regExp1 = ($start ? '' : ' ') . $regExp0 . '[ .,):;]';
        $regExp2 = ' ' . $regExp0 . '$';
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

        // See file regExpAnalysis.txt in \xy\m\TeX\text2bib (as well as handwritten notes in Projects folder) for logic behind these numbers
        // They are the indexes of the matches for the subpatterns on the regular expression
        if ($allowMonth) {
            $yearIndexes = $start ? [6, 15, 24] : [6, 15, 24, 33];
        } else {
            $yearIndexes = $start ? [2, 7, 12] : [2, 7, 12, 17];
        }

        foreach ($yearIndexes as $i) {
            if (isset($matches[$i]) and count($matches[$i])) {
                if (!$start) {
                    $foundMatch = $matches[$i][count($matches[$i]) - 1];
                    $wholeMatch = $matches[0][count($matches[0]) - 1];
                } else {
                    $foundMatch = $matches[$i];
                    $wholeMatch = $matches[0];
                }
                if (isset($foundMatch) and $foundMatch[1] >= 0) {
                    $year = $foundMatch[0];
                    $remains = rtrim(substr($string, 0, $wholeMatch[1]), '.,') . ' ' . ltrim(substr($string, $wholeMatch[1] + strlen($wholeMatch[0])), '.,');
                    break;
                }
            }
        }

        if ($allowMonth) {
            $monthIndexes = $start ? [2, 11, 20] : [2, 11, 20, 29];
            foreach ($monthIndexes as $i) {
                if (isset($matches[$i]) and count($matches[$i])) {
                    if (!$start) {
                        $foundMatch = $matches[$i][count($matches[$i]) - 1];
                        $wholeMatch = $matches[0][count($matches[0]) - 1];
                    } else {
                        $foundMatch = $matches[$i];
                        $wholeMatch = $matches[0];
                    }
                    if (isset($foundMatch[1]) and $foundMatch[1] >= 0) {
                        $month = $foundMatch[0];
                        $remains = rtrim(substr($string, 0, $wholeMatch[1]), '.,') . ' ' . ltrim(substr($string, $wholeMatch[1] + strlen($wholeMatch[0])), '.,');
                        break;
                    }
                }
            }
        }

        return $year;
    }

    /*
     * get components of string that is date (UNTESTED & UNUSED)
     */
    /*
    public function getDate($string, $includeYear = true) {
        $words = explode(' ', $string);
        if ($includeYear) {
            if (count($words) == 3) {
                if (preg_match($this->monthsRegExp, $words[0], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $words[0];
                    $day = $words[1];
                    $year = $words[2];
                    $returner = array('year' => $year, 'month' => $month, 'day' => $day);
                } elseif (preg_match($this->monthsRegExp, $words[1], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $words[1];
                    $day = $words[0];
                    $year = $words[2];
                    $returner = array('year' => $year, 'month' => $month, 'day' => $day);
                } else {
                    $returner = false;
                }
            } elseif (count($words) == 1) {
                if (preg_match('/([1-9][0-9][0-9][0-9])[-\.\/]([1-9][0-9]?)[-\.\/]([1-9][0-9]?)/', $words[0], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $matches[1][0];
                    $day = $matches[2][0];
                    $year = $matches[0][0];
                    $returner = array('year' => $year, 'month' => $month, 'day' => $day);
                } else {
                    $returner = false;
                }
            } else {
                $returner = false;
            }
        } else {
            if (count($words) == 2) {
                if (preg_match($this->monthsRegExp, $words[0], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $words[0];
                    $day = $words[1];
                    $returner = array('month' => $month, 'day' => $day);
                } elseif (preg_match($this->monthsRegExp, $words[1], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $words[1];
                    $day = $words[0];
                    $returner = array('month' => $month, 'day' => $day);
                } else {
                    $returner = false;
                }
            } elseif (count($words) == 1) {
                if (preg_match('/([1-9][0-9]?)[-\.\/]([1-9][0-9]?)/', $words[0], $matches, PREG_OFFSET_CAPTURE)) {
                    $month = $matches[0][0];
                    $day = $matches[1][0];
                    $returner = array('month' => $month, 'day' => $day);
                } else {
                    $returner = false;
                }
            } else {
                $returner = false;
            }
        }

        return $returner;
    }
    */

    /**
     * trimRightPeriod: remove trailing period if preceding character is not uppercase letter
     * @param $string string
     * return trimmed string
     */
    public function trimRightPeriod(string $string): string
    {
        if ($string == '' or $string == '.') {
            $trimmedString = '';
        } elseif (strlen($string) == 1) {
            $trimmedString = $string;
        } elseif (substr($string, -1) == '.' && strtoupper(substr($string, -2, 1)) != substr($string, -2, 1)) {
            $trimmedString = substr($string, 0, -1);
        } else {
            $trimmedString = $string;
        }

        return $trimmedString;
    }

    /**
     * trimRightBrace: remove trailing brace if and only if string contains one more right brace than left brace
     * (e.g. deals with author's name Andr\'{e})
     * @param $string string
     * return trimmed string
     */
    public function trimRightBrace(string $string): string
    {
        return (substr($string, -1) == '}' && substr_count($string, '}') - substr_count($string, '{') == 1) ? substr($string, 0, -1) : $string;
    }

    /**
     * Assuming $string contains exactly the publisher and address, break it into those two components;
     * return remaining string (if any)
     * @param $string string
     * @param $address string
     * @param $publisher string
     */
    public function extractPublisherAndAddress(string $string, string|null &$address, string|null &$publisher): string
    {
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
            if (!$containsPublisher) {
                $publisher = '';
            }
            if (!$containsCity) {
                $address = '';
            }

            $remainder = $stringMinusPubInfo;
            // If only publisher has been identified, take rest to be city
            if ($containsPublisher and ! $containsCity) {
                $address = trim($remainder, ',.: }{ ');
                $remainder = '';
                // elseif publisher has not been identified, take rest to be publisher (whether or not city has been identified)
            } elseif (!$containsPublisher) {
                $publisher = trim($remainder, ',.: }{ ');
                $remainder = '';
            }
        }
        $publisher = Str::of($publisher)->replaceStart('by', '')->trim();
        $address = ltrim($address, '} ');

        return $remainder;
    }

    // Report whether $string is a year between 1700 and 2100
    public function isYear(string $string): bool
    {
        $number = (int) $string;
        return $number > 1700 && $number < 2100;
    }

    // Report whether $string is the start of the name of the proceedings of a conference
    public function isProceedings(string $string): bool
    {
        $isProceedings = false;

        if (!Str::startsWith($string, $this->proceedingsExceptions) 
                && preg_match('/^' . $this->proceedingsRegExp . '/', $string)) {
            $isProceedings = true;
        }

        return $isProceedings;
    }

    /**
     * bareWords: in array $words of strings, report the elements at the start up until one ends
     * in ',' or '.' or ')' or ':' or is a year in parens or brackets or, if $stopAtAnd is true,
     * is 'and'.
     */
    public function bareWords(array $words, bool $stopAtAnd): array
    {
        $barewords = [];
        foreach ($words as $j => $word) {
            $stop = false;
            if (Str::endsWith($word, ['.', ',', ')', ':'])) {
                $stop = true;
            }
            if (preg_match('/(\(|\[)(18|19|20)([0-9][0-9])(\)|\])/', $word)) {
                $stop = true;
            }
            // if (Str::startsWith($word, ['`', '"', "'"])) {
            //     $stop = true;
            // }

            // 'et' deals with the case 'et al.'
            if ($word == 'et') {
                $stop = true;
            }

            if ($stopAtAnd && $this->isAnd($word)) {
                $stop = true;
            }

            if ($stop) {
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
    public function nameScore(array $words, bool $ignoreAnd): array
    {
        $aspell = Aspell::create();
        $wordsToCheck = [];
        $score = 0;
        foreach ($words as $word) {
            // Names are in dictionary with initial u.c. letter, so convert word to l.c. to exclude them as regular words
            $word = mb_strtolower($word);
            if ($word != 'and' || !$ignoreAnd) {
                $wordsToCheck[] = $word;
                if (in_array($word, $this->stopwords)) {
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
        
        $returner = ['count' => count($wordsToCheck), 'score' => $score];

        return $returner;
    }

    /*
     * Determine whether $word is in the dictionary
     */
    /* Not currently used.
    public function inDict(string $word): bool
    {
        // enchant_broker_init seems to not be installed and currently not to be available
        return true;
        // $tag = 'en_US';
        // $r = enchant_broker_init();
        // if (enchant_broker_dict_exists($r, $tag)) {
        //     $d = enchant_broker_request_dict($r, $tag);
        //     //$dprovides = enchant_dict_describe($d);
        //     $correct = enchant_dict_check($d, $word);
        //     $this->verbose("\"" . $word . "\" is " . ($correct ? "" : "NOT ") . "in dictionary");
        //     if (in_array($word, $this->excludedWords)) {
        //         $this->verbose("but is in the list of excluded words");
        //     }
        //     //enchant_broker_free_dict($d);
        //     unset($d);
        // }

        // //enchant_broker_free($r);
        // unset($r);

        // return $correct;
    }
    */

    /**
     * isNotName: determine if array of words starts with a name
     * @param $words array
     */
    public function isNotName(string $word1, string $word2): bool
    {
        $words = [$word1, $word2];
        $this->verbose(['text' => 'Arguments of isNotName: ', 'words' => [$words[0], $words[1]]]);
        $result = false;
        $accentRegExp = '/^(\\\"|\\\\\'|\\\`|\\\\\^|\\\H|\\\v|\\\~|\\\k|\\\c|\\\\\.)\{?[A-Z]\}?/';
        
        for ($i = 0; $i < 2; $i++) {
            if (preg_match($accentRegExp, $words[$i])) {
                $this->verbose(['text' => 'Name component ', 'words' => [$words[$i]], 'content' => ' starts with accented uppercase character']);
            } elseif (
                // not a name if doesn't start with an accented uppercase letter and it starts with l.c. and is not
                // "d'" and is not a von name
                isset($words[$i][0]) 
                    && mb_strtolower($words[$i][0]) == $words[$i][0]
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
    public function spaceOutInitials(string $string): string
    {
        return preg_replace('/\.([^ -])/', '. $1', $string);
    }

    /**
     * Normalize format to Smith, A. B. or A. B. Smith or Smith, Alan B. or Alan B. Smith.
     * In particular, change Smith AB to Smith, A. B. and A.B. SMITH to A. B. Smith
     * $nameString is a FULL name (e.g. first and last or first middle last)
     */
    public function formatAuthor(string $nameString): string
    {
        $this->verbose(['text' => 'formatAuthor: argument ', 'words' => [$nameString]]);
        $names = explode(' ', $nameString);
        // $initialsStart is index of component (a) that is initials and (b) after which all components are initials
        // initials are any string for which all letters are u.c. and at most two characters that are
        // letter or period
        $initialsStart = count($names);
        $allUppercase = true;
        foreach ($names as $k => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsMaxStringLength = 2; // initials could be 'A' or 'AB' or 'A.'
            $initialsStart = (strtoupper($lettersOnlyName) == $lettersOnlyName 
                    && strlen($lettersOnlyName) <= $initialsMaxStringLength) ? min([$k, $initialsStart]) : count($names);
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
            if (strpos($name, ',') !== false) {
                $commaPassed = true;
            }
            // If name is not ALL uppercase, a period has not yet occured, there are fewer than 3 letters
            // in $name or a comma has occurred, and all letters in the name are uppercase, assume $name
            // is initials.  Put periods and spaces as appropriate.
            if (! $allUppercase && ! $initialPassed && (strlen($lettersOnlyName) < 3 || $commaPassed) 
                        && strtoupper($lettersOnlyName) == $lettersOnlyName && $lettersOnlyName != 'III') {
                // First deal with single accented initial
                // Case of multiple accented initials not currently covered
                if (preg_match('/^\\\\\S\{[a-zA-Z]\}\.$/', $name)) {  // e.g. \'{A}.
                    $fName .= $name; 
                } elseif (preg_match('/^\\\\\S\{[a-zA-Z]\}$/', $name)) {  // e.g. \'{A}
                    $fName .= $name . '.';
                } elseif (preg_match('/^\\\\\S[a-zA-Z]$/', $name)) {  // e.g. \'A
                    $fName .= $name . '.';
                } else {
                    $chars = str_split($name);
                    foreach ($chars as $j => $char) {
                        if (ctype_alpha($char)) {
                            if ($j >= count($chars) - 1 || $chars[$j + 1] != '.') {
                                $fName .= $char . '.';
                                if (count($chars) > $j + 1) {
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
                // if (isset($fName[0]) && $fName[0] == 'M') {
                //     dd($fName);
                // }
            // if name is ALL uppercase and contains no period, translate uppercase component to an u.c. first letter and the rest l.c.
            // (Contains no period to deal with name H.-J., which should not be convered to H.-j.)
            } elseif (strtoupper($lettersOnlyName) == $lettersOnlyName && strpos($name, '.') === false && $lettersOnlyName != 'III') {
                $fName .= ucfirst(mb_strtolower($name));
            } else {
                $fName .= $name;
            }
        }

        $this->verbose(['text' => 'formatAuthor: result ', 'words' => [$fName]]);
        return $fName;
    }


    /*

    public function formatAuthor($nameString) {
        $this->verbose(['text' => 'formatAuthor: argument ', 'words' => [$nameString]]);
        $names = explode(' ', $nameString);
        // $initialsStart is index of component (a) that is initials and (b) after which all components are initials
        // initials are any string all u.c. of length one or two
        $initialsStart = count($names);
        $allUppercase = true;
        foreach ($names as $k => $name) {
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsStringLength = 2; // includes possible period
            if (preg_match('/^\\\\\S\{[a-zA-Z]\}/', $name)) {
                $initialsStringLength = 6;
            } elseif (preg_match('/^\\\\\S[a-zA-Z]/', $name)) {
                $initialsStringLength = 4;
            }
            $initialsStart = (strtoupper($lettersOnlyName) == $lettersOnlyName 
                    && strlen($name) <= $initialsStringLength) ? min([$k, $initialsStart]) : count($names);
            if (strtoupper($lettersOnlyName) != $lettersOnlyName) {
                $allUppercase = false;
            }
        }
        // put comma after Smith in Smith AB format but not in John SMITH
        if (strpos($nameString, ',') === false and $initialsStart > 0 and $initialsStart < count($names)) {
            if (substr($names[$initialsStart - 1], -1) != ',') {
                $names[$initialsStart - 1] .= ',';
            }
        }

        $fName = '';
        $commaPassed = false;
        $initialPassed = false;
        foreach ($names as $i => $name) {
            if ($i) {
                $fName .= ' ';
            }
            if (strpos($name, '.') !== false) {
                $initialPassed = true;
            }
            if (strpos($name, ',') !== false) {
                $commaPassed = true;
            }
            $lettersOnlyName = preg_replace("/[^A-Za-z]/", '', $name);
            $initialsStringLength = 2; // includes possible period
            if (preg_match('/^\\\\\S\{[a-zA-Z]\}/', $name)) {
                $initialsStringLength = 6;
            } elseif (preg_match('/^\\\\\S[a-zA-Z]/', $name)) {
                $initialsStringLength = 4;
            }
            // if name is not ALL uppercase, assume that an uppercase component is initials
            if (strtoupper($lettersOnlyName) != $lettersOnlyName && ! $initialPassed && ( strlen($name) <= $initialsStringLength || $commaPassed) && strtoupper($lettersOnlyName) == $lettersOnlyName) {
                $chars = str_split($name);
                foreach ($chars as $j => $char) {
                    if (ctype_alpha($char)) {
                        if ($j >= count($chars) - 1 || $chars[$j + 1] != '.') {
                            $fName .= $char . '.';
                            if (count($chars) > $j + 1) {
                                $fName .= ' ';
                            }
                        } else {
                            $fName .= $char;
                        }
                    } else {
                        $fName .= $char;
                    }
                }
            // if name is ALL uppercase and contains no period, translate uppercase component to an u.c. first letter and the rest l.c.
            // (Contains no period to deal with name H.-J., which should not be convered to H.-j.)
            } elseif (strtoupper($lettersOnlyName) == $lettersOnlyName and strpos($name, '.') === false) {
                $fName .= ucfirst(mb_strtolower($name));
            } else {
                $fName .= $name;
            }
        }
        $this->verbose(['text' => 'formatAuthor: result ', 'words' => [$fName]]);
        return $fName;
    }
*/

    // Get journal name from $remainder, which includes also publication info
    public function getJournal(string &$remainder, object &$item, bool $italicStart, bool $pubInfoStartsWithForthcoming, bool $pubInfoEndsWithForthcoming): string
    {
        if ($italicStart) {
            $journal = $this->getQuotedOrItalic($remainder, true, false, $remainder, $posStart, $posEnd);
        } elseif ($pubInfoStartsWithForthcoming) {
            // forthcoming at start
            $result = $this->extractLabeledContent($remainder, $this->startForthcomingRegExp, '.*', true);
            $journal = $this->getQuotedOrItalic($result['content'], true, false, $remains, $posStart, $posEnd);
            if (!$journal) {
                $journal = $result['content'];
            }
            $label = $result['label'];
            if (Str::startsWith($label, ['Forthcoming', 'forthcoming', 'Accepted', 'accepted', 'To appear', 'to appear'])) {
                $label = Str::replaceEnd(' in', '', $label);
                $label = Str::replaceEnd(' at', '', $label);
            }
            $item->note = $label;
        } elseif ($pubInfoEndsWithForthcoming) {
            // forthcoming at end
            $result = $this->extractLabeledContent($remainder, '.*', $this->endForthcomingRegExp, true);
            $journal = $result['label'];
            $item->note = $result['content'];
        } else {
            $words = $remainingWords = explode(' ', $remainder);
            $initialWords = [];
            foreach ($words as $key => $word) {
                $initialWords[] = $word;
                array_shift($remainingWords);
                $remainder = implode(' ', $remainingWords);
                if ($key === count($words) - 1 // last word in remainder
                    || Str::contains($words[$key+1], range('1', '9')) // next word contains a digit
                    || preg_match($this->volRegExp2, $remainder) // followed by volume info
                    || preg_match($this->pagesRegExp3, $remainder) // followed by pages info
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
        return $journal;
    }

    // Allows page number to be preceded by uppercase letter.  Second number in range should really be allowed
    // to start with uppercase letter only if first number in range does so---and if pp. is present, almost
    // anything following should be allowed as page numbers?
    // --- shouldn't delimit pages, but might be given by mistake
    public function getPagesForArticle(string &$remainder, object &$item): void
    {
        $numberOfMatches = preg_match_all('/(' . $this->pagesRegExp1 . ')?( )?([A-Z]?[1-9][0-9]{0,4} ?-{1,3} ?[A-Z]?[0-9]{1,5})/', $remainder, $matches, PREG_OFFSET_CAPTURE);
        if ($numberOfMatches) {
            $matchIndex = $numberOfMatches - 1;
            $this->verbose('[p0] matches: 1: ' . $matches[1][$matchIndex][0] . '; 2: ' . $matches[2][$matchIndex][0] . '; 3: ' . $matches[3][$matchIndex][0]);
        }
        $this->verbose("Number of matches for a potential page range: " . $numberOfMatches);
        if (isset($matchIndex)) {
            $this->verbose("Match index: " . $matchIndex);
        }
        if ($numberOfMatches) {
            $item->pages = str_replace('---', '-', $matches[3][$matchIndex][0]);
            $item->pages = str_replace('--', '-', $item->pages);
            $item->pages = str_replace(' ', '', $item->pages);
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

    public function getVolumeAndNumberForArticle(string &$remainder, object &$item): void
    {
        if (ctype_digit($remainder)) {
            $this->verbose('Remainder is entirely numeric, so assume it is the volume');
            $item->volume = $remainder;
            $remainder = '';
        } elseif ($remainder && preg_match('/^[IVXLCDM]{0,8}$/', $remainder)) {
            $this->verbose('Remainder is Roman number, so assume it is the volume');
            $item->volume = $remainder;
            $remainder = '';
        } elseif ($this->containsFontStyle($remainder, false, 'bold', $startPos, $length)) {
            $this->verbose('[v2] bold (startPos: ' . $startPos . ')');
            $item->volume = $this->getBold($remainder, false, $remainder);
            $this->verbose('remainder: ' . ($remainder ? $remainder : '[empty]'));
            if ($remainder && ctype_digit($remainder)) {
                $item->pages = $remainder;  // could be a single page
                $remainder = '';
                $this->verbose('[p3] pages: ' . $item->pages);
            }
        } else {
            // $item->number can be a range (e.g. '6-7')
            // Look for something like 123:6-19
            $this->verbose('[v3]');
            $this->verbose('Remainder: ' . $remainder);
            // 'Volume? 123$'
            $numberOfMatches = preg_match('/^(' . $this->volumeRegExp1 . ')?([1-9][0-9]{0,3})$/', $remainder, $matches, PREG_OFFSET_CAPTURE);
            if($numberOfMatches) {
                $this->verbose('[p2a] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0]);
                $item->volume = $matches[2][0];
                unset($item->number);
                // if a match is empty, [][1] component is -1
                $take = $matches[1][1] >= 0 ? $matches[1][1] : $matches[2][1];
                $drop = $matches[2][1] + strlen($matches[2][0]);
                $this->verbose('take: ' . $take . ', drop: ' . $drop);
                $this->verbose('volume: ' . $item->volume);
                $this->verbose('No number assigned');
            } else {
                // A letter or sequence of letters is permitted after an issue number
                $numberOfMatches = preg_match('/(' . $this->volumeRegExp1 . ')?([1-9][0-9]{0,3})( ?, |\(| | \(|\.|:|;)(' . $this->numberRegExp1 . ')?( )?(([1-9][0-9]{0,6}[a-zA-Z]*)(-[1-9][0-9]{0,6})?)\)?/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                if ($numberOfMatches) {
                    $this->verbose('[p2b] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0] . ', 3: ' . $matches[3][0] . ', 4: ' . $matches[4][0] . ', 5: ' . $matches[5][0] . (isset($matches[6][0]) ? ', 6: ' . $matches[6][0] : '') . (isset($matches[7][0]) ? ', 7: ' . $matches[7][0] : '') . (isset($matches[8][0]) ? ', 8: ' . $matches[8][0] : ''));
                    $item->volume = $matches[2][0];
                    $this->verbose('volume: ' . $item->volume);
                    if (strlen($matches[7][0]) < 5) {
                        $item->number = $matches[6][0];
                        $this->verbose('number: ' . $item->number);
                    } else {
                        $item->note = 'Article ' . $matches[6][0];
                        $this->verbose('note: ' . $item->note);
                    }
                    // if a match is empty, [][1] component is -1
                    $take = $matches[1][1] >= 0 ? $matches[1][1] : $matches[2][1];
                    $drop = $matches[6][1] + strlen($matches[6][0]);
                    $this->verbose('take: ' . $take . ', drop: ' . $drop);
                } else {
                    // Look for "vol" etc. followed possibly by volume number and then something other than an issue number
                    // (e.g. some extraneous text after the entry)
                    $volume = $this->extractLabeledContent($remainder, $this->volumeRegExp1, '[1-9][0-9]{0,3}');
                    if ($volume) {
                        $this->verbose('[p2c]');
                        $item->volume = $volume;
                        $take = $drop = 0;
                    } elseif (preg_match('/^article [0-9]*$/i', $remainder)) {
                        $item->note = $remainder;
                        $take = 0;
                        $drop = strlen($remainder);
                    } else {
                        // Look for something like 123:xxx (where xxx is not a page range)
                        $numberOfMatches = preg_match('/([1-9][0-9]{0,3})( ?, |\(| | \(|\.|:)*(.*)/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                        if ($numberOfMatches) {
                            $this->verbose('[p2d]');
                            if (Str::startsWith($matches[3][0], ['Article', 'article'])) {
                                $item->note = $matches[3][0];
                                $item->volume = $matches[1][0];
                            } else {
                                // Assume all of $remainder is volume (might be something like '123 (Suppl. 19)')
                                $item->volume = $remainder;
                            }
                            unset($item->number);
                            $take = 0;
                            $drop = strlen($remainder);
                            // $take = $matches[1][1];
                            // $len = isset($matches[2][0]) ? strlen($matches[2][0]) : 0;
                            // $drop = $matches[1][1] + strlen($matches[1][0]) + $len;
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
                $item->pages = $remainder;  // could be a single page
                $remainder = '';
                $this->verbose('[p4] pages: ' . $item->pages);
            }
        }
    }

    public function cleanText(string $string, string|null $charEncoding): string
    {
        $string = str_replace("\\newblock", "", $string);
        // Replace each tab with a space
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\\textquotedblleft ", "``", $string);
        $string = str_replace("\\textquotedblright ", "''", $string);
        $string = str_replace("\\textquotedblright", "''", $string);

        if($charEncoding == 'utf8' or $charEncoding == 'utf8leave') {
            // http://www.utf8-chartable.de/unicode-utf8-table.pl (change "go to other block" to see various parts)
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
        }
       
        if($charEncoding == 'utf8') {
            $string = str_replace("\xC3\x80", "{\`A}", $string);
            $string = str_replace("\xC3\x81", "{\'A}", $string);
            $string = str_replace("\xC3\x82", "{\^A}", $string);
            $string = str_replace("\xC3\x83", "{\~A}", $string);
            $string = str_replace("\xC3\x84", "{\"A}", $string);
            $string = str_replace("\xC3\x85", "{\AA}", $string);
            $string = str_replace("\xC3\x86", "{\AE}", $string);
            $string = str_replace("\xC3\x87", "{\c{C}}", $string);
            $string = str_replace("\xC3\x88", "{\`E}", $string);
            $string = str_replace("\xC3\x89", "{\'E}", $string);
            $string = str_replace("\xC3\x8A", "{\^E}", $string);
            $string = str_replace("\xC3\x8B", "{\"E}", $string);
            $string = str_replace("\xC3\x8C", "{\`I}", $string);
            $string = str_replace("\xC3\x8D", "{\'I}", $string);
            $string = str_replace("\xC3\x8E", "{\^I}", $string);
            $string = str_replace("\xC3\x8F", "{\"I}", $string);

            $string = str_replace("\xC3\x90", "{\DH}", $string);
            $string = str_replace("\xC3\x91", "{\~N}", $string);
            $string = str_replace("\xC3\x92", "{\`O}", $string);
            $string = str_replace("\xC3\x93", "{\'O}", $string);
            $string = str_replace("\xC3\x94", "{\^O}", $string);
            $string = str_replace("\xC3\x95", "{\~O}", $string);
            $string = str_replace("\xC3\x96", "{\"O}", $string);
            $string = str_replace("\xC3\x98", "{\O}", $string);
            $string = str_replace("\xC3\x99", "{\`U}", $string);
            $string = str_replace("\xC3\x9A", "{\'U}", $string);
            $string = str_replace("\xC3\x9B", "{\^U}", $string);
            $string = str_replace("\xC3\x9C", "{\"U}", $string);
            $string = str_replace("\xC3\x9D", "{\'Y}", $string);
            $string = str_replace("\xC3\x9E", "{\Thorn}", $string);
            $string = str_replace("\xC3\x9F", "{\ss}", $string);

            $string = str_replace("\xC3\xA0", "{\`a}", $string);
            $string = str_replace("\xC3\xA1", "{\'a}", $string);
            $string = str_replace("\xC3\xA2", "{\^a}", $string);
            $string = str_replace("\xC3\xA3", "{\=a}", $string);
            $string = str_replace("\xC3\xA4", "{\"a}", $string);
            $string = str_replace("\xC3\xA5", "{\aa}", $string);
            $string = str_replace("\xC3\xA6", "{\ae}", $string);
            $string = str_replace("\xC3\xA7", "\c{c}", $string);
            $string = str_replace("\xC3\xA8", "{\`e}", $string);
            $string = str_replace("\xC3\xA9", "{\'e}", $string);
            $string = str_replace("\xC3\xAA", '{\^e}', $string);
            $string = str_replace("\xC3\xAB", '{\"e}', $string);
            $string = str_replace("\xC3\xAC", "{\`\i}", $string);
            $string = str_replace("\xC3\xAD", "{\'\i}", $string);
            $string = str_replace("\xC3\xAE", "{\^\i}", $string);
            $string = str_replace("\xC3\xAF", "{\"\i}", $string);

            $string = str_replace("\xC3\xB0", "{\dh}", $string);
            $string = str_replace("\xC3\xB1", "{\~n}", $string);
            $string = str_replace("\xC3\xB2", "{\`o}", $string);
            $string = str_replace("\xC3\xB3", "{\'o}", $string);
            $string = str_replace("\xC3\xB4", "{\^o}", $string);
            $string = str_replace("\xC3\xB5", "{\=o}", $string);
            $string = str_replace("\xC3\xB6", "{\"o}", $string);
            $string = str_replace("\xC3\xB8", "{\o}", $string);
            $string = str_replace("\xC3\xB9", "{\`u}", $string);
            $string = str_replace("\xC3\xBA", "{\'u}", $string);
            $string = str_replace("\xC3\xBB", "{\^u}", $string);
            $string = str_replace("\xC3\xBC", "{\"u}", $string);
            $string = str_replace("\xC3\xBD", "{\'y}", $string);
            $string = str_replace("\xC3\xBE", "{\thorn}", $string);
            $string = str_replace("\xC3\xBF", "{\"y}", $string);

            $string = str_replace("\xC4\x80", "{\=A}", $string);
            $string = str_replace("\xC4\x81", "{\=a}", $string);
            $string = str_replace("\xC4\x82", "{\u{A}}", $string);
            $string = str_replace("\xC4\x83", "{\u{a}}", $string);
            $string = str_replace("\xC4\x84", "{\k{A}}", $string);
            $string = str_replace("\xC4\x85", "{\k{a}}", $string);
            $string = str_replace("\xC4\x86", "{\'C}", $string);
            $string = str_replace("\xC4\x87", "{\'c}", $string);
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

        $string = str_replace("\\ ", " ", $string);
        $string = str_replace("\\textbf{\\ }", " ", $string);
        // Replace ~ with space if not preceded by \
        $string = preg_replace('/([^\\\])~/', '$1 ', $string);
        $string = str_replace("\\/", "", $string);

        // Delete ^Z and any trailing space (^Z is at end of last entry of DOS file)
        $string = rtrim($string, " \032");
        $string = ltrim($string, ' ');
        
        // Regularize spaces
        $string = $this->regularizeSpaces($string);
        
        return $string;
    }
}