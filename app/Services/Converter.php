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
use stdClass;

class Converter
{
    var $boldCodes;
    var $articleRegExp;
    var $bookTitleAbbrevs;
    var $cities;
    var $detailLines;
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
    var $volRegExp0;
    var $volRegExp1;
    var $volRegExp2;
    var $volumeRegExp1;
    var $vonNames;
    var $workingPaperRegExp;
    var $workingPaperNumberRegExp;

    use Stopwords;

    public function __construct()
    {
        // Words that are in dictionary but are abbreviations in journal names
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

        $this->volRegExp0 = ',? ?[Vv]ol(\.|ume)? ?(\\textit\{|\\textbf\{)?[1-9][0-9]{0,4}';
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

        // If next reg exp works, (conf\.|conference) can be deleted, given '?' at end.
        $this->proceedingsRegExp = 'proceedings of |conference on |symposium on |.* meeting |proc\..*(conf\.|conference)?';
        $this->proceedingsExceptions = ['Proceedings of the National Academy', 'Proceedings of the Royal Society'];

        $this->thesisRegExp = '( [Tt]hesis| [Dd]issertation)';
        $this->fullThesisRegExp = '(PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral|[Mm]aster|MA|M\.A\.)( [Tt]hesis| [Dd]issertation)';
        $this->masterRegExp = '[Mm]aster|MA|M\.A\.';
        $this->phdRegExp = 'PhD|Ph\.D\.|Ph\. D\.|Ph\.D|[Dd]octoral';

        $this->inReviewRegExp1 = '/[Ii]n [Rr]eview\.?\)?$/';
        $this->inReviewRegExp2 = '/^[Ii]n [Rr]eview/';
        $this->inReviewRegExp3 = '/(\(?[Ii]n [Rr]eview\.?\)?)$/';

        $this->pagesRegExp1 = '[Pp]p\.?|[Pp]\.';
        $this->pagesRegExp2 = '[Pp]p\.?|[pP]ages?';
        $this->pagesRegExp3 = '/^pages |^pp\.?|^p\.|^p /i';

        $this->numberRegExp1 = '[Nn]os?\.?:?|[Nn]umbers?|[Ii]ssues?';

        $this->isbnRegExp = 'ISBN:? [0-9X]+';
        $this->oclcRegExp = 'OCLC:? [0-9]+';

        $this->journalWord = 'Journal';

        $this->bookTitleAbbrevs = ['Proc', 'Amer', 'Conf', 'Cont', 'Sci', 'Int', "Auto", 'Symp'];

        $this->workingPaperRegExp = '(preprint|working paper|discussion paper|technical report|'
                . 'research paper|mimeo|unpublished paper|unpublished manuscript|'
                . 'under review|submitted)';
        $this->workingPaperNumberRegExp = ' (\\\\#|number|no\.?)? ?(\d{1,5}),?';

        $this->monthsRegExp = 'January|Jan\.?|February|Feb\.?|March|Mar\.?|April|Apr\.?|May|June|Jun\.?|July|Jul\.?|'
                . 'August|Aug\.?|September|Sept\.?|Sep\.?|October|Oct\.?|November|Nov\.?|December|Dec\.?';

        $this->vonNames = VonName::all()->pluck('name')->toArray();

        // The script will identify strings as cities and publishers even if they are not in these arrays---but the
        // presence of a string in one of the arrays helps when the elements of the reference are not styled in any way.
        $this->cities = City::all()->pluck('name')->toArray();
        
        // Springer-Verlag should come before Springer, so that if string contains Springer-Verlag, that is found
        $this->publishers = Publisher::all()->pluck('name')->toArray();
        
        $this->names = Name::all()->pluck('name')->toArray();
        
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
    public function convertEntry(string $rawEntry, Conversion $conversion): array|null
    {
        // $aspell = Aspell::create();
        // 'john' and 'martin' and 'smith' are in dictionary all lowercase
        // $x = iterator_count($aspell->check('Melissa John Robert Celia john harold martin Carolyn', ['en_US']));

        $warnings = $notices = [];
        $this->detailLines = [];
        $this->itemType = null;

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

        // Remove numbers at start of entry, like '6.' or '[14]'.
        $entry = ltrim($entry, ' .0123456789[]()');

        $item = new \stdClass();
        $itemKind = null;
        $itemLabel = null;

        // If entry starts with '\bibitem [abc] {<label>}', get <label> and remove '\bibitem' and arguments
        if (preg_match('/^\\\bibitem *(\[[^\]]*\])? *{([^}]*)}(.*)$/', $entry, $matches)) {
            if ($matches[2] && !$conversion->override_labels) {
                $itemLabel = $matches[2];
            }
            $entry = $matches[3];
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

        ////////////////////
        // Get doi if any //
        ////////////////////

        $doi = $this->extractLabeledContent($entry, ' doi:? | doi: ?|https?://dx.doi.org/|https?://doi.org/', '[a-zA-Z0-9/._\-]+');

        if ($doi) {
            if (!preg_match('/[0-9]+/', $doi)) {
                $warnings[] = 'The doi appears to be invalid.';
            }
            $this->setField($item, 'doi', $doi, '1');
        } else {
            $this->verbose("No doi found.");
        }

        ///////////////////////////
        // Get arXiv info if any //
        ///////////////////////////

        $eprint = $this->extractLabeledContent($entry, ' arxiv: ?', '\S+');

        if ($eprint) {
            $this->setField($item, 'archiveprefix', 'arXiv', '2');
            $this->setField($item, 'eprint', $eprint, '3');
        } else {
            $this->verbose("No arXiv info found.");
        }

        ////////////////////
        // Get url if any //
        ////////////////////

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
                    $warnings[] = "[u1] The string \"" . $possibleDate . "\" remains unidentified.";
                }
                $entry = Str::before($entry, $matches[0]);
            } else {
                $urlPlus = $this->extractLabeledContent($entry, '', 'https?://\S+ ?.*$');
                $url = trim(Str::before($urlPlus, ' '), ',.;');
                $afterUrl = (strpos($urlPlus, ' ') !== false) ? Str::after($urlPlus, ' ') : '';
                if ($afterUrl) {
                    $warnings[] = "[u2] The string \"" . $afterUrl . "\" remains unidentified.";
                }
            }
        }

        if (isset($url) && $url) {
            $this->setField($item, 'url', $url, '4');
            if ($accessDate) {
                $this->setField($item, 'urldate', $accessDate, '5');
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
                $this->setField($item, 'year', $year, '6');
                if (strlen($year) != 4) {
                    $warnings[] = "Year contains " . strlen($year) . " digits!";
                }
            }
        }

        //////////////////////
        // Look for authors //
        //////////////////////

        // Exploding on spaces isn't exactly right, because a word containing an accented letter
        // can have a space in it --- e.g. Oblo{\v z}insk{\' y}.  So perform a more sophisticated explosion.
        $chars = str_split($entry);

        // If period is not followed by space, another period, a comma, a semicolon, a dash, a quotation mark
        // or a lowercase letter (might be within a URL --- the "name" when citing a web page), treat it as ending
        // word.
        $word = '';
        $words = [];
        foreach ($chars as $i => $char) {
            if ($char == '.' 
                    && isset($chars[$i+1]) 
                    && !in_array($chars[$i+1], [' ', '.', ',', ';', '-', '"', "'"]) 
                    && mb_strtolower($chars[$i+1]) != $chars[$i+1]) {
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

        $this->verbose("Looking for authors ...");

        $isEditor = false;

        $remainder = $entry;

        $authorConversion = $this->convertToAuthors($words, $remainder, $year, $isEditor, true, true);

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
            $this->setField($item, 'author', rtrim($authorstring, ','), '7');
        } else {
            $this->setField($item, 'editor', trim(str_replace($editorPhrases, "", $authorstring), ' .,'), '8');
        }

        if ($year) {
            $this->setField($item, 'year', $year, '9');
        }

        $remainder = trim($remainder, '.}, ');
        $this->verbose("[1] Remainder: " . strip_tags($remainder));

        ////////////////////
        // Look for title //
        ////////////////////

        unset($this->italicTitle);

        $remainder = ltrim($remainder, ': ');
        $title = $this->getQuotedOrItalic($remainder, true, false, $before, $after);
        $newRemainder = $before . ltrim($after, "., ");

        // Website
        if (isset($item->url) && $oneWordAuthor) {
            $itemKind = 'online';
            $title = trim($remainder);
            $newRemainder = '';
        }

        if (!$title) {
            $title = $this->getTitle($remainder, $edition, $volume, $isArticle);
            if ($edition) {
                $this->setField($item, 'edition', $edition, '10');
            }
            if ($volume) {
                $this->setField($item, 'volume', $volume, '11');
            }
            $newRemainder = $remainder;
        }

        $remainder = $newRemainder;

        $this->setField($item, 'title', rtrim($title, ' .,'), '12');
        
        $this->verbose("Remainder: " . strip_tags($remainder));

        ///////////////////////////////////////////////////////////
        // Look for year if not already found                    //
        // (may already have been found at end of author string) //
        ///////////////////////////////////////////////////////////

        $containsMonth = false;
        if (!isset($item->year)) {
            if (!$year) {
                // Space prepended to $remainder in case it starts with year, because getYear requires space 
                // (but perhaps could be rewritten to avoid it).
                $year = $this->getYear(' '. $remainder, false, $newRemainder, true, $month);
            }

            if ($year) {
                $this->setField($item, 'year', $year, '13');
            } else {
                $this->setField($item, 'year', '', '14');
                $warnings[] = "No year found.";
            }
            if (isset($month)) {
                $containsMonth = true;
                // If month is parsable, parse it: 
                // translate 'Jan' or 'Jan.' or 'January', for example, to 'January'.
                $month1 = Str::before($month, '-');
                if (preg_match('/^[a-zA-Z.]*$/', $month1)) {
                    $fullMonth1 = Carbon::parse('1 ' . $month1)->format('F');
                } else {
                    $fullMonth1 = $month1;
                }

                $month2 = Str::contains($month, '-') ? Str::after($month, '-') : null;
                if ($month2 && preg_match('/^[a-zA-Z.]*$/', $month2)) {
                    $fullMonth2 = Carbon::parse('1 ' . $month2)->format('F');
                } elseif ($month2) {
                    $fullMonth2 = $month2;
                } else {
                    $fullMonth2 = null;
                }

                $this->setField($item, 'month', $fullMonth1 . ($fullMonth2 ? '-' . $fullMonth2 : ''), '15');
            }
        }

        $remainder = ltrim($newRemainder, ' ');

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
                $containsVolume = true;
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

        if (preg_match('/' . $this->isbnRegExp . '/', $remainder)) {
            $containsIsbn = true;
            $this->verbose("Contains an ISBN string.");
        }

        $commaCount = substr_count($remainder, ',');
        $this->verbose("Number of commas: " . $commaCount);

        if (isset($this->italicTitle)) {
            $this->verbose("Italic title");
        }

        ///////////////////////////////////////////////////
        // Use features of string to determine item type //
        ///////////////////////////////////////////////////

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
        } elseif ($containsPublisher || $inStart) {
            if ((!$containsIn && ! $containsPageRange) || strlen($remainder) - $cityLength - strlen($publisher) < 30) {
                $this->verbose("Item type case 11");
                $itemKind = 'book';
            } else {
                $this->verbose("Item type case 12");
                $itemKind = 'incollection';
            }
            if (!$this->itemType && !$itemKind) {
                $notices[] = "Not sure of type; guessed to be " . $itemKind . ".  [3]";
            }
        } elseif (!$containsNumber && !$containsPageRange) {
            // Condition used to have 'or', which means that an article with a single page number is classified as a book
            if ($containsThesis) {
                $this->verbose("Item type case 13");
                $itemKind = 'thesis';
            } elseif ($endsWithInReview || $containsMonth) {
                $this->verbose("Item type case 14");
                $itemKind = 'unpublished';
            } else {
                $this->verbose("Item type case 15");
                $itemKind = 'book';
            }
        } elseif ($containsEdition) {
            $this->verbose("Item type case 16");
            $itemKind = 'book';
            if (!$this->itemType) {
                $warnings[] = "Not sure of type; contains \"edition\", so set to " . $itemKind . ".";
            }
        } elseif ($containsDigitOutsideVolume) {
            $this->verbose("Item type case 17");
            $itemKind = 'article';
            if (!$this->itemType) {
                $warnings[] = "Really not sure of type; has to be something; set to " . $itemKind . ".";
            }
        } else {
            $this->verbose("Item type case 18");
            $itemKind = 'book';
            if (!$this->itemType) {
                $warnings[] = "Really not sure of type; has to be something; set to " . $itemKind . ".";
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
                $this->setField($item, 'isbn', $match, '16');
            }

            $match = $this->extractLabeledContent($remainder, ' OCLC:? ', '[0-9]+');
            if ($match) {
                $this->setField($item, 'oclc', $match, '17');
            }
        }

        // If item is not unpublished and ends with 'in review', put 'in review' in notes field and remove it from entry
        // Can this case arise?
        if ($itemKind != 'unpublished') {
            $match = $this->extractLabeledContent($remainder, '', '\(?[Ii]n [Rr]eview\.?\)?$');
            if ($match) {
                $this->setField($item, 'note', $match, '18');
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
                $remainder = ltrim($remainder, '., ');

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
                $journal = rtrim($journal, ' ,');

                $this->setField($item, 'journal', $journal, '19');
                if (!$journal) {
                    $warnings[] = "'Journal' field not found.";
                    $itemKind = 'unpublished';  // but continue processing as if journal
                }
                $remainder = trim($remainder, ' ,.');
                $this->verbose("Remainder: " . $remainder);

                // If $remainder ends with 'forthcoming' phrase, put that in note.  Else look for pages & volume etc.
                if (preg_match('/' . $this->endForthcomingRegExp . '/', $remainder)) {
                    $this->setField($item, 'note', $remainder, '20');
                    $remainder = '';
                } else {
                    // Get pages
                    $this->getPagesForArticle($remainder, $item);

                    $pagesReported = false;
                    if ($item->pages) {
                        $this->verbose(['fieldName' => 'Pages', 'content' => strip_tags($item->pages)]);
                        $pagesReported = true;
                    } else {
                        $warnings[] = "No page range found.";
                    }
                    $this->verbose("[p1] Remainder: " . $remainder);

                    // Get month, if any
                    $months = $this->monthsRegExp;
                    $regExp = '(\(?(' . $months . '\)?)([-\/](' . $months . ')\)?)?)';
                    preg_match_all($regExp, $remainder, $matches, PREG_OFFSET_CAPTURE);

                    if (isset($matches[0][0][0]) && $matches[0][0][0]) {
                        $this->setField($item, 'month', trim($matches[0][0][0], '()'), '21');
                        $remainder = substr($remainder, 0, $matches[0][0][1]) . ltrim(substr($remainder, $matches[0][0][1] + strlen($matches[0][0][0])), ', )');
                        $this->verbose('Remainder: ' . $remainder);
                    }

                    // Get volume and number
                    $this->getVolumeAndNumberForArticle($remainder, $item, $containsNumberDesignation);
                
                    $result = $this->findRemoveAndReturn($remainder, $this->articleRegExp);
                    if ($result) {
                        // If remainder contains article number, put it in the note field
                        $this->setField($item, 'note', $result[0], '22');
                    } elseif (!$item->pages && isset($item->number) && $item->number && !$containsNumberDesignation) {
                        // else if no pages have been found and a number has been set, assume the previously assigned number
                        // is in fact a single page
                        $this->setField($item, 'pages', $item->number, '23');
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
                    $this->setField($item, 'note', substr($remainder, $length), '24');
                } else {
                    $this->setField($item, 'note', $remainder, '25');
                }
                $remainder = '';

                if (!$item->note && isset($item->url) && $item->url) {
                    $this->verbose('Moving content of url field to note');
                    $this->setField($item, 'note', $item->url, '26');
                    unset($item->url);
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
                $type = isset($workingPaperMatches[1][0]) ? $workingPaperMatches[1][0] : '';
                if ($type) {
                    $this->setField($item, 'type', $type, '27');
                }

                $number = isset($workingPaperMatches[3][0]) ? $workingPaperMatches[3][0] : '';
                if ($number) {
                    $this->setField($item, 'number', $number, '28');
                }

                if (isset($workingPaperMatches[0][1]) && $workingPaperMatches[0][1] > 0) {
                    // Chars before 'Working Paper'
                    $this->setField($item, 'institution', trim(substr($remainder, 0, $workingPaperMatches[0][1] - 1), ' .,'), '29');
                    $remainder = trim(substr($remainder, $workingPaperMatches[3][1] + strlen($item->number)), ' .,');
                } else {
                    // No chars before 'Working paper'---so take string after number to be institution
                    $n = isset($workingPaperMatches[3][1]) ? $workingPaperMatches[3][1] : 0;
                    $this->setField($item, 'institution', trim(substr($remainder, $n + strlen($item->number)), ' .,'), '30');
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
                $regExp = '(\()?(' . $this->pagesRegExp2 . ')?( )?([1-9][0-9]{0,4} ?-{1,2} ?[0-9]{1,5})(\))?';
                // Return group 4 of match and remove whole match from $remainder
                $result = $this->findRemoveAndReturn($remainder, $regExp);
                if ($result) {
                    $pages = $result[4];
                    $this->setField($item, 'pages', $pages ? str_replace(['--', ' '], ['-', ''], $pages) : '', '31');
                }

                if (!isset($item->pages)) {
                    $warnings[] = "Pages not found.";
                }

                // Next case occurs if remainder previously was like "pages 2-33 in ..."
                if (substr($remainder, 0, 3) == 'in ') {
                    $remainder = substr($remainder, 3);
                }
                $this->verbose("[in2] Remainder: " . $remainder);

                $editorStart = false;
                $newRemainder = $remainder;
                
                // If a string in $remainder is quoted or italicized, take that to be book title
                $booktitle = $this->getQuotedOrItalic($remainder, false, false, $before, $after);
                $newRemainder = $remainder = $before . ltrim($after, ".,' ");
                $this->verbose('booktitle case 1');

                if ($booktitle) {
                    $this->setField($item, 'booktitle', $booktitle, '32');
                    if (strlen($before) > 0) {
                        // Pattern is <string1> <booktitle> <string2> (with <string1> nonempty).
                        // Check whether <string1> starts with "forthcoming"
                        $string1 = trim(substr($remainder, 0, strlen($before)), ',. ');
                        if (preg_match('/' . $this->startForthcomingRegExp . '/i', $string1, $matches)) {
                            $match = trim($matches[0], '() ');
                            $match = Str::replaceEnd(' in', '', $match);
                            $match = Str::replaceEnd(' at', '', $match);
                            $this->setField($item, 'note', isset($item->note) ? $item->note . ' ' . trim($match) : trim($match), '33');
                            $possibleEditors = strlen($matches[0]) - strlen($string1) ? substr($string1, strlen($matches[0])) : null;
                        } else {
                            // Assume <string1> is editors
                            $possibleEditors = $string1;
                        }
                        if ($possibleEditors) {
                            if (preg_match($this->editorStartRegExp, $possibleEditors, $matches, PREG_OFFSET_CAPTURE)) {
                                $possibleEditors = trim(substr($possibleEditors, strlen($matches[0][0])));
                            }
                            $editorConversion = $this->convertToAuthors(explode(' ', $possibleEditors), $remains, $year, $isEditor, false);
                            $this->setField($item, 'editor', trim($editorConversion['authorstring']), '34');
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

                // If $remainder matches this pattern, it is consistent with its being publisher: address,
                // without any editors.  $remainder will be '';
                $result = $this->extractLabeledContent($remainder, '^[a-zA-Z]*:', '.*', true);
                if ($result) {
                    $this->setField($item, 'address', substr($result['label'], 0, -1), '35');
                    $this->setField($item, 'publisher', $result['content'], '36');
                }

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
                            $tempRemainder = $this->findAndRemove($tempRemainder, $cityString);
                        }
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
                                $this->verbose('booktitle case 2');
                            }
                            $this->setField($item, 'editor', '', '37');
                            $warnings[] = 'No editor found';
                            $this->setField($item, 'address', $cityString, '38');
                            $this->setField($item, 'publisher', $publisherString, '39');
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
                                    $this->setField($item, 'editor', '', '40');
                                    $warnings[] = 'No editor found';
                                    $this->setField($item, 'address', $cityString, '41');
                                    $this->setField($item, 'publisher', $publisherString, '42');
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
                        if ($this->isNameString($remainder)) {
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

                    if ($editorStart) {
                        // CASES 1, 3, and 4
                        $this->verbose("[ed1] Remainder starts with editor string");
                        $words = explode(' ', $editorString);
                        // $isEditor is used only for a book (with an editor, not an author)
                        $isEditor = false;

                        $editorConversion = $this->convertToAuthors($words, $remainder, $trash2, $isEditor, $determineEnd);
                        $editorString = trim($editorConversion['authorstring'], '() ');
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $this->setField($item, 'editor', trim($editorString, ' ,.'), '43');
                        $newRemainder = $postEditorString ? $postEditorString : $remainder;
                        // $newRemainder consists of <booktitle> <publicationInfo>
                        $newRemainder = trim($newRemainder, '., ');
                        $this->verbose("[in7] Remainder: " . $newRemainder);
                    } else {
                        // CASES 2 and 5
                        $this->verbose("Remainder: " . $remainder);
                        $this->verbose("[ed2] Remainder starts with book title");

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
                        if (isset($endAuthorPos) && $endAuthorPos) {
                            // CASE 2
                            $authorstring = trim(substr($remainder, $j, $endAuthorPos - $j), '.,: ');
                            $authorConversion = $this->convertToAuthors(explode(' ', $authorstring), $trash1, $trash2, $isEditor, false);
                            $this->setField($item, 'editor', trim($authorConversion['authorstring'], ' '), '44');
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
                            //$editorConversion = $this->convertToAuthors(explode(' ', $possibleEditors), $trash1, $trash2, $isEditor, true);

                            // Find previous period
                            for ($j = $colonPos; $j > 0 && $remainder[$j] != '.' && $remainder[$j] != '('; $j--) {

                            }
                            $this->verbose("Position of period in remainder is " . $j);
                            // Previous version---why drop first 3 chars?
                            // $editor = trim(substr($remainder, 3, $j-3), ' .,');

                            $editorConversion = $this->convertToAuthors(explode(' ', trim(substr($remainder, 0, $j), ' .,')), $trash1, $trash2, $isEditor, false);
                            $editor = $editorConversion['authorstring'];
                            foreach ($editorConversion['warnings'] as $warning) {
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
                        $editorConversion = $this->convertToAuthors(explode(' ', $editorString), $trash1, $trash2, $isEditor, false);
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }
                        $this->setField($item, 'editor', trim($editor, ', '), '45');
                        $remainder = substr($remainder, $matches[0][1] + strlen($matches[0][0]));
                    } elseif ($itemKind == 'incollection' && $this->initialNameString($remainder)) {
                        // An editor of an inproceedings has to be indicated by an "eds" string (inproceedings
                        // seem unlikely to have editors), but an 
                        // editor of an incollection does not need such a string
                        $this->verbose("[ed4] Remainder starts with editor string");
                        $editorConversion = $this->convertToAuthors(explode(' ', $remainder), $remainder, $trash2, $isEditor, true);
                        $editor = $editorConversion['authorstring'];
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $this->setField($item, 'editor', trim($editor, ', '), '46');
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

                        $editorConversion = $this->convertToAuthors($words, $remainder, $trash2, $isEditor, true);
                        $authorstring = $editorConversion['authorstring'];
                        $this->setField($item, 'editor', trim($authorstring, '() '), '47');
                        foreach ($editorConversion['warnings'] as $warning) {
                            $warnings[] = $warning;
                        }

                        $remainder = ltrim($newRemainder, ' ,');
                        $this->verbose("[in10] Remainder: " . $remainder);
                    }
                } elseif (!$booktitle) {
                    // CASES 1, 3, and 4
                    // Case in which $booktitle is not defined: remainder presumably starts with booktitle
                    $remainder = trim($remainder, '., ');
                    $this->verbose("[in11] Remainder: " . $remainder);
                    // $remainder contains book title and publication info.  Need to find boundary.  

                    // Check whether publication info matches pattern for book to be a volume in a series
                    $result = $this->findRemoveAndReturn(
                        $remainder,
                        '(' . $this->volumeRegExp1 . ')( ([1-9][0-9]{0,4}))(( of| in|,) )([^\.,]*\.|,)'
                        );
                    if ($result) {
                        // Take series to be string following 'of' or 'in' or ',' up to next period or comma
                        $this->setField($item, 'volume', $result[3], '48');
                        $this->setField($item, 'series', $result[6], '49');
                        $booktitle = trim($result['before'], '., ');
                        $this->verbose('booktitle case 5');
                        $remainder = trim($result['after'], ',. ');
                        $this->verbose('Volume found, so book is part of a series');
                        $this->verbose('Remainder (publisher and address): ' . $remainder);
                    } else {
                        // if remainder contains a single period, take that as end of booktitle
                        if (substr_count($remainder, '.') == 1) {
                            $this->verbose("Remainder contains single period, so take that as end of booktitle");
                            $periodPos = strpos($remainder, '.');
                            $booktitle = trim(substr($remainder, 0, $periodPos), ' .,');
                            $this->verbose('booktitle case 6');
                            $remainder = substr($remainder, $periodPos);
                        } else {
                            // If publisher has been identified, remove it from $remainder and check
                            // whether it is preceded by a string that could be an address
                            if (isset($publisher) && $publisher) {
                                $this->setField($item, 'publisher', $publisher, '50');
                                $tempRemainder = trim(Str::remove($publisher, $remainder), ' .');
                                $afterPeriod = Str::afterLast($tempRemainder, '.');
                                $afterComma = Str::afterLast($tempRemainder, ',');
                                $afterPunc = (strlen($afterComma) < strlen($afterPeriod)) ? $afterComma : $afterPeriod;
                                foreach ($this->cities as $city) {
                                    if (Str::endsWith(trim($afterPunc, '():'), $city)) {
                                        $this->setField($item, 'address', $city, 51);
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($city) - 2);
                                        $this->verbose('booktitle case 7');
                                        break;
                                    }
                                }
                                if (!isset($item->address)) {
                                    if (substr_count($afterPunc, ' ') == 1) {
                                        $booktitle = substr($tempRemainder, 0, strlen($tempRemainder) - strlen($afterPunc));
                                        $this->setField($item, 'address', $afterPunc, 52);
                                        $this->verbose('booktitle case 8');
                                    } else {
                                        $booktitle = $tempRemainder;
                                        $this->verbose('booktitle case 9');
                                    }
                                    $this->setField($item, 'booktitle', $booktitle, 53);
                                    $this->verbose(['fieldName' => 'Booktitle', 'content' => $item->booktitle]);
                                    $remainder = '';
                                }
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

                if (isset($item->editor) && $item->editor) {
                    $this->verbose(['fieldName' => 'Editors', 'content' => strip_tags($item->editor)]);
                } else {
                    $warnings[] = "Editor not found.";
                }

                $remainder = trim($remainder, '[]()., ');
                $this->verbose("[in12] Remainder: " . ($remainder ? $remainder : '[empty]'));

                // If $remainder contains 'forthcoming' string, remove it and put it in $item->note.
                $result = $this->findRemoveAndReturn($remainder, '^(Forthcoming|In press|Accepted)');
                if ($result) {
                    $item->note .= ' ' . $result[0];
                    $this->verbose('"Forthcoming" string removed and put in note field');
                    $this->verbose('Remainder: ' . $remainder);
                    $this->verbose(['fieldName' => 'Note', 'content' => strip_tags($item->note)]);
                }

                // Whatever is left is publisher and address
                if ((!isset($item->publisher) || ! $item->publisher) || ( !isset($item->address) || ! $item->address)) {
                    if (isset($item->publisher) && $item->publisher) {
                        $this->setField($item, 'address', $remainder, '54');
                        $newRemainder = '';
                    } elseif (isset($item->address) && $item->address) {
                        $this->setField($item, 'publisher', $remainder, '55');
                        $newRemainder = '';
                    } else {
                        $newRemainder = $this->extractPublisherAndAddress($remainder, $address, $publisher);
                        $this->setField($item, 'publisher', $publisher, '56');
                        $this->setField($item, 'address', $address, '57');
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

                $lastWordInBooktitle = Str::afterLast($booktitle, ' ');
                if ($this->inDict($lastWordInBooktitle)) {
                    $booktitle = rtrim($booktitle, '.');
                }
                $this->setField($item, 'booktitle', trim($booktitle, ' .,'), '58');
                if (!$item->booktitle) {
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
                        $this->setField($item, 'edition', trim($remainingWords[$key - 1], ',. )('), '59');
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
                    '(\(?' . $this->volumeRegExp1 . ')( ([1-9][0-9]{0,4})\)?)(( of| in|,|.)? )(.*)$'
                );
                if ($result) {
                    $this->setField($item, 'volume', $result[3], '60');
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
                            $this->setField($item, 'publisher', $publisher, '61');
                            $after = Str::after($seriesAndPublisher, $publisher);
                            $before = Str::before($seriesAndPublisher, $publisher);
                            if ($after) {
                                // If anything comes after the publisher, it must be the address, and the string
                                // before the publisher must be the series
                                $this->setField($item, 'address', trim($after, ',. '), '62');
                                $series = trim($before, '., ');
                                if ($this->containsFontStyle($series, true, 'italics', $startPos, $length)) {
                                    $this->setField($item, 'series', rtrim(substr($series, $length), '}'), '63');
                                    $this->verbose('Removed italic formatting from series name');
                                } else {
                                    $this->setField($item, 'series', $series, '64');
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
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), '65');
                                            $this->setField($item, 'series', trim($beforeLastComma, '.,: '), '66');
                                        } else {
                                            $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), '67');
                                            $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), '68');
                                        }
                                    } elseif ($containsComma) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastComma)), '.,: '), '69');
                                        $this->setField($item, 'series', trim($beforeLastComma, '.,: '), '70');
                                    } elseif ($containsPeriod) {
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastPeriod)), '.,: '), '71');
                                        $this->setField($item, 'series', trim($beforeLastPeriod, '.,: '), '72');
                                    } else {
                                        $beforeLastSpace = Str::beforeLast($before, ' ');
                                        $this->setField($item, 'address', trim(substr($before, strlen($beforeLastSpace)), '.,: '), '73');
                                        $this->setField($item, 'series', trim($beforeLastSpace, '.,: '), '74');
                                    }
                                } else {
                                    // Otherwise there is no address, and the series is the string before the publisher
                                    $this->setField($item, 'series', trim($before, '.,: '), '75');
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
                                $this->setField($item, 'series', $result1[1], '76');
                                if ($result1[4] == ',') {
                                    $this->verbose('Series case 1a: format is <publisher>, <address>');
                                    $this->setField($item, 'publisher', trim($result1[2], ' ,'), '77');
                                    $this->setField($item, 'address', $result1[5], '78');
                                } elseif ($result1[4] == ':') {
                                    $this->verbose('Series case 1b: format is <address>: <publisher>');
                                    $this->setField($item, 'address', trim($result1[2], ' :'), '79');
                                    $this->setField($item, 'publisher', $result1[5], '80');
                                }
                            } else {
                                $result2 = $this->findRemoveAndReturn(
                                    $seriesAndPublisher,
                                    '(.*[^.,]*)\. (.*\.?)$'
                                );
                                if ($result2) {
                                    $this->verbose('Series case 2: format is <publisher> (no address)');
                                    $this->setField($item, 'series', $result2[1], '81');
                                    $this->setField($item, 'publisher', $result2[2], '82');
                                }
                            }
                        }
                        $done = true;
                    }
                }

                // Volume has been identified, but publisher and possibly address reamin
                if (!$done) {
                    $remainder = $newRemainder ? $newRemainder : implode(" ", $remainingWords);

                    if ($publisherString && $cityString) {
                        $this->setField($item, 'publisher', $publisherString, '83');
                        $this->setField($item, 'address', $cityString, '84');
                        $remainder = $this->findAndRemove($remainder, $publisherString);
                        $remainder = $this->findAndRemove($remainder, $cityString);
                    } else {
                        // If string is in italics, get rid of the italics
                        if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)) {
                            $remainder = rtrim(substr($remainder, $length), '}');
                        }

                        $remainder = $this->extractPublisherAndAddress($remainder, $address, $publisher);

                        if ($publisher) {
                            $this->setField($item, 'publisher', $publisher, '85');
                        } else {
                            $warnings[] = "No publisher identified.";
                        }

                        if ($address) {
                            $this->setField($item, 'address', $address, '86');
                        } else {
                            $warnings[] = "No place of publication identified.";
                        }
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
                $this->verbose(['fieldName' => 'Item type', 'content' => strip_tags($itemKind)]);

                $remainder = $this->findAndRemove($remainder, $this->fullThesisRegExp);

                $remainder = trim($remainder, ' .,');
                if (strpos($remainder, ':') === false) {
                    $this->setField($item, 'school', $remainder, '87');
                } else {
                    $remArray = explode(':', $remainder);
                    $this->setField($item, 'school', trim($remArray[1], ' .,'), '88');
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
            $warnings[] = "[u4] The string \"" . $remainder . "\" remains unidentified.";
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

        foreach ($warnings as $warning) {
            $this->verbose(['warning' => strip_tags($warning)]);
        }

        foreach ($notices as $notice) {
            $this->verbose(['notice' => strip_tags($notice)]);
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
            'details' => $conversion->report_type == 'detailed' ? $this->detailLines : [],
            'scholarTitle' => $scholarTitle,
        ];

        return $returner;
    }

    private function setField(stdClass &$item, string $fieldName, string $string, string $id = ''): void
    {
        $item->$fieldName = strip_tags($string);
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
        if (preg_match('/^(,? ?[A-Z][A-Z])[,.: ]/', $string, $matches)) {
            return $matches[1];
        }
        
        return false;
    }

    // Get title from a string that starts with title and then has publication information.
    // Case in which title is in quotation marks or italics is dealt with separately.
    private function getTitle(string &$remainder, string|null &$edition, string|null &$volume, bool &$isArticle): string|null
    {
        $title = null;
        $originalRemainder = $remainder;

        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;
        $skipNextWord = false;

        // If $remainder ends with  string in parenthesis, look at the string
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

                // When a word ending in punctuation or preceding a word starting with ( is encountered, check whether
                // it is followed by
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
                if (Str::endsWith(rtrim($word, "'\""), ['.', '!', '?', ':', ',', ';']) || ($nextWord && $nextWord[0] == '(')) {
                    if ($this->containsFontStyle($remainder, true, 'italics', $startPos, $length)
                        || preg_match('/^' . $this->workingPaperRegExp . '/i', $remainder)
                        || preg_match($this->pagesRegExp3, $remainder)
                        || preg_match('/^[Ii]n |^' . $this->journalWord . ' |^\(?Vol\.? |^\(?Volume /', $remainder)
                        || preg_match('/^(19|20)[0-9][0-9]\./', $remainder)
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
                    } elseif (!$periodFound 
                            && $this->containsFontStyle($remainder, false, 'italics', $startPos, $length)) {
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
            $title = Str::before($originalRemainder, ',');
            $newRemainder = ltrim(Str::after($originalRemainder, ','), ' ');
        }

        $remainder = isset($newRemainder) ? $newRemainder : $remainder;

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
    private function findAndRemove(string $string, string $regExp): string
    {
        return preg_replace('%' . $regExp . '%i', '', $string);
    }

    /*
     * Find first match for $regExp (regular expression without delimiters), case insensitive, in $string,
     * return group number $groupNumber (defined by parentheses in $regExp)
     * and remove entire match for $regExp from $string after triming ',. ' from substring preceding match.
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
    private function isName(string $word, string $finalPunc = ''): bool
    {
        $result = false;
        if (in_array(substr($word, -1), str_split($finalPunc))) {
            $word = substr($word, 0, -1);
        }
        if (ctype_alpha($word) && ( ucfirst($word) == $word || strtoupper($word) == $word)) {
            $result = true;
        }

        return $result;
    }

    /*
     * Determine whether string is plausibly a list of names
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
    private function isDate(string $string): bool
    {
        $str = str_replace([","], "", trim($string, ',. '));
        $isDate1 = preg_match('/^[1-3]?[0-9] (' . $this->monthsRegExp . ') (19|20)[0-9][0-9]/', $str);
        $isDate2 = preg_match('/^(' . $this->monthsRegExp . ') [1-3]?[0-9] (19|20)[0-9][0-9]/', $str);
        $isDate3 = preg_match('/^[1-3]?[0-9][\-\/][01]?[0-9][\-\/](19|20)[0-9][0-9]/', $str);
        $isDate4 = preg_match('/^[01]?[0-9][\-\/][1-3]?[0-9][\-\/](19|20)[0-9][0-9]/', $str);
        $isDate5 = preg_match('/^(19|20)[0-9][0-9][\-\/][1-3]?[0-9][\-\/][01]?[0-9]/', $str);

        return $isDate1 || $isDate2 || $isDate3 || $isDate4 || $isDate5;
    }

    private function isAnd(string $string): bool
    {
        return mb_strtolower($string) == $this->phrases['and'] || in_array($string, ['\\&', '&']);
    }

    private function getStringBeforeChar(string $string, string $char): string
    {
        return Str::before($string, $char);
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

    private function verbose(string|array $arg): void
    {
        $this->detailLines[] = $arg;
    }

    /**
     * convertToAuthors: determine the authors in the initial elements of an array of words
     * After making a change, check that all examples are still converted correctly.
     * @param $words array of words
     * @param $remainder string remaining string after authors removed
     * @param $determineEnd boolean if true, figure out where authors end; otherwise take whole string
     *        to be authors
     * return author string
     */
    private function convertToAuthors(array $words, string|null &$remainder, string|null &$year, bool &$isEditor, bool $determineEnd = true): array
    {
        $namePart = $authorIndex = $case = 0;
        $prevWordAnd = $done = $isEditor = $hasAnd = $multipleAuthors = false;
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
                    && ! $this->isAnd($words[$i+1]) // next word is not 'and'
                )  {
                $namePart = 0;
                $authorIndex++;
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

            // Remove first word from $remainingWords
            array_shift($remainingWords);

            if ($authorIndex > 0) {
                $multipleAuthors = true;
            }

            if (in_array($word, [" ", "{\\sc", "\\sc"])) {
                //
            } elseif ($this->isEd($word, $multipleAuthors)) {
                $this->verbose("String for editors, so ending name string (word: " . $word .")");
                // Word is 'ed' or 'ed.' if $hasAnd is false, or 'eds' or 'eds.' if $hasAnd is true
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
                    $this->addToAuthorString(1, $authorstring, $fullName);
                }
                break;  // exit from foreach
            } elseif ($determineEnd && $done) {
                break;  // exit from foreach
            } elseif ($this->isAnd($word)) {
                // Word is 'and' or equivalent
                $hasAnd = $prevWordAnd = true;
                $this->addToAuthorString(2, $authorstring, $this->formatAuthor($fullName) . ' and');
                $fullName = '';
                $namePart = 0;
                $authorIndex++;
                $case = 1;
                $reason = 'Word is "and" or equivalent';
            } elseif ($word == 'et') {
                // Word is 'et'
                $nextWord = rtrim($words[$i+1], ',');
                $this->verbose('nextWord: ' . $nextWord);
                if (in_array($nextWord, ['al..', 'al.', 'al'])) {
                    $this->addToAuthorString(3, $authorstring, $this->formatAuthor($fullName) . ' and others');
                    array_shift($remainingWords);
                    $remainder = implode(" ", $remainingWords);
                    $done = true;
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
                        // Don't use spaceOutInitials in this case, because string is not initials.  Could be
                        // something like autdiogames.net, in which case don't want to put space before period.
                        $nameComponent = $word;
                        $fullName .= trim($nameComponent, '.');
                        $this->addToAuthorString(4, $authorstring, $fullName);
                        $case = 2;
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string starts with year';
                        $oneWordAuthor = true;
                        $itemYear = $year; // because $year is recalculated below
                    } else {
                        // Case like: "Arrow, K. J., Hurwicz. L., and ..." [note period at end of Hurwicz]
                        // "Hurwicz" is current word, which is added back to the remainder.
                        // (Ideally the script should realize the typo and still get the rest of the authors.)
                        $warnings[] = "Unexpected period after \"" . substr($word, 0, -1) . "\" in source.  Typo? Re-processed with comma instead of period.";
                        $case = 3;
                        $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, namePart is 0, and remaining string does not start with year';
                        $word = substr($word, 0, -1) . ',';
                        goto namePart0;
                    }
                } else {
                    // If $namePart > 0
                    $nameComponent = $this->trimRightBrace($this->spaceOutInitials(rtrim($word, '.')));
                    $fullName .= " " . $nameComponent;
                    $this->addToAuthorString(5, $authorstring, $this->formatAuthor($fullName));
                    $remainder = implode(" ", $remainingWords);
                    $case = 4;
                    $reason = 'Word ends in period and has more than 3 letters, previous letter is lowercase, and namePart is > 0';
                }
                $this->verbose("Remainder: " . $remainder);
                if ($year = $this->getYear($remainder, true, $remains, false, $trash)) {
                    $remainder = $remains;
                }
                $this->verbose("Remains: " . $remains);
                $done = true;
            } elseif ($namePart == 0) {
                namePart0:
                // Check if $word and first word of $remainingWords are plausibly a name.  If not, end search if $determineEnd.
                if ($determineEnd && isset($remainingWords[0]) && $this->isNotName($word, $remainingWords[0])) {
                    $fullName .= $word;
                    $remainder = implode(" ", $remainingWords);
                    $this->addToAuthorString(6, $authorstring, ' ' . $this->formatAuthor($fullName));
                    if ($year = $this->getYear($remainder, true, $remains, false, $trash)) {
                        $remainder = $remains;
                        $this->verbose("Year detected");
                    }
                    $case = 5;
                    $done = true;
                } else {
                    if (!$prevWordAnd && $authorIndex) {
                        $this->addToAuthorString(7, $authorstring, $this->formatAuthor($fullName) . ' and');
                        $fullName = '';
                        $prevWordAnd = true;
                    }
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
                    // Following occurs in case of name that is a single string, like "IMF"
                    if ($year = $this->getYear(implode(" ", $remainingWords), true, $remains, false, $trash)) {
                        $remainder = $remains;
                        $done = true;
                    }
                    $case = 6;
                }
            } else {
                // namePart > 0 and word doesn't end in some character, then lowercase letter, then period
                $prevWordAnd = false;

                // 2023.8.2: trimRightBrace removed to deal with conversion of example containing name Oblo{\v z}insk{\' y}
                // However, it must have been included here for a reason, so probably it should be included under
                // some conditions.
                if (Str::startsWith($word, ['Jr.', 'Sr.', 'III'])) {
                    $nWords = explode(' ', trim($fullName, ' '));
                    $nameWords = [];
                    foreach ($nWords as $nWord) {
                        $nameWords[] = Str::endsWith($nWord, [',,']) ? substr($nWord, 0, -1): $nWord;
                    }
                    if (count($nameWords) == 1) {
                        $fullName = $nameWords[0] . ' ' . $word;
                        $nameComplete = false;
                    } else {
                        // Put Jr. or Sr. in right place for BibTeX: format is lastName, Jr., firstName OR lastName Jr., firstName.
                        // Assume last name is single word that is followed by a comma (which covers both
                        // firstName lastName, Jr. and lastName, firstName, Jr.
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
                    $case = 15;
                } else {
                    // Don't rtrim '}' because it could be part of the name: e.g. Oblo{\v z}insk{\' y}.
                    // Don't trim comma from word before Jr. etc, because that is valuable info
                    $trimmedWord = (isset($words[$i+1]) && Str::startsWith($words[$i+1], ['Jr.', 'Sr.', 'III'])) ? $word : rtrim($word, ',;');
                    $nameComponent = $this->spaceOutInitials($trimmedWord);
                    $fullName .= " " . $nameComponent;
                }

                // $bareWords is array of words at start of $remainingWords that don't end ends in ','
                // or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
                $bareWords = $this->bareWords($remainingWords, false);
                // If 'and' has not already occurred ($hasAnd is false), its occurrence in $barewords is compatible
                // with $barewords being part of the authors' names OR being part of the title, so should be ignored.
                $nameScore = $this->nameScore($bareWords, !$hasAnd);
                $this->verbose("bareWords (no trailing punct, not year in parens): " . implode(' ', $bareWords));
                $this->verbose("nameScore: " . $nameScore['score']);
                if ($nameScore['count']) {
                    $this->verbose('nameScore per word: ' . number_format($nameScore['score'] / $nameScore['count'], 2));
                }

                if ($determineEnd && $this->getQuotedOrItalic(implode(" ", $remainingWords), true, false, $before, $after)) {
                    $remainder = implode(" ", $remainingWords);
                    $done = true;
                    $this->addToAuthorString(9, $authorstring, $this->formatAuthor($fullName));
                    $case = 7;
                } elseif ($determineEnd && $year = $this->getYear(implode(" ", $remainingWords), true, $remainder, false, $trash)) {
                    $done = true;
                    $fullName = ($fullName[0] != ' ' ? ' ' : '') . $fullName;
                    $this->addToAuthorString(10, $authorstring, $this->formatAuthor($fullName));
                    $case = 8;
                    $reason = 'Remainder starts with year';
                } elseif (
                        $determineEnd &&
                        count($bareWords) > 2 && 
                        !$this->isAnd($remainingWords[0]) && 
                        $nameScore['score'] / $nameScore['count'] < 0.25 &&
                        ! $this->isInitials($remainingWords[0])
                    ) {
                    // Low nameScore relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                    // Note that this check occurs only when $namePart > 0---so it rules out double-barelled
                    // family names that are not followed by commas.  ('Paulo Klinger Monteiro, ...' is OK.)
                    // Cannot set limit to be > 1 bareWord, because then '... Smith, Nancy Lutz and' gets truncated
                    // at comma.
                    $done = true;
                    $this->addToAuthorString(11, $authorstring, $this->formatAuthor($fullName));
                    $case = 9;
                } elseif ($nameComplete && Str::endsWith($word, [',', ';']) && isset($words[$i + 1]) && ! $this->isEd($words[$i + 1], $hasAnd)) {
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
                            $done = true;
                            $this->addToAuthorString(12, $authorstring, $this->formatAuthor($fullName));
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
                                    $this->getYear($words[$i + 2], true, $trash, false, $trash2)
                                )) {
                            $fullName .= ',';
                            $case = 14;
                        } else {
                            // Low name score relative to number of bareWords (e.g. less than 25% of words not in dictionary)
                            if ($nameScore['count'] > 1 && $nameScore['score'] / $nameScore['count'] < 0.25) {
                                $this->addToAuthorString(13, $authorstring, $this->formatAuthor($fullName));
                                $done = true;
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
                        $this->addToAuthorString(14, $authorstring, $this->formatAuthor($fullName));
                    }
                    $case = 13;
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
    private function isEd(string $string, bool $multiple = false): bool
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
     * return quoted or italic substring
     */
    private function getQuotedOrItalic(string $string, bool $start, bool $italicsOnly, string|null &$before, string|null &$after): string|bool
    {

        $matchedText = $quotedText = $beforeQuote = $afterQuote = '';

        /* 
         * Rather than using the following loop, could use regular expressions.  Versions of expressions
         * are given in a comment after the loop.  However, these expressions are incomplete, and are complex
         * because of the need to exclude escaped quotes.  I find the loop easier to understand and maintain.
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
                } elseif ($begin == '``' || $begin == "''") {
                    if ($char == "'" && $chars[$i-1] != '\\' && $chars[$i+1] && $chars[$i+1] == "'") {
                        $end = true;
                        $skip = true;
                    } elseif ($char == '"') {
                        $end = true;
                    } else {
                        $quotedText .= $char;
                    }
                } elseif ($begin == '`' || $begin == "'") {
                    if ($char == "'" && $chars[$i-1] != '\\' 
                                && (!isset($chars[$i+1]) || !in_array(strtolower($chars[$i+1]), range('a', 'z')))) {
                        $end = true;
                    } else {
                        $quotedText .= $char;
                    }
                } elseif ($begin == '"') {
                    if ($char == '"' && $chars[$i-1] != '\\') {
                        $end = true;
                    } else {
                        $quotedText .= $char;
                    }
                // before match has begun
                } elseif ($char == '`') {
                    if ((!isset($chars[$i-1]) || $chars[$i-1] != '\\') && isset($chars[$i+1]) && $chars[$i+1] == "`") {
                        $begin = '``';
                        $skip = true;
                    } elseif (!isset($chars[$i-1]) || $chars[$i-1] != '\\') {
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
            $styledText = $this->trimRightPeriod($styledText);
            $before = substr($string, 0, $position);
            $after = substr($string, $j + 1);
            $remains = rtrim($before, ' .,') . ltrim($after, ' ,.');
        }

        return $styledText;
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
    private function getYear(string $string, bool $start, string|null &$remains, bool $allowMonth, string|null &$month): string
    {
        $year = '';
        $remains = $string;

        // Year can be (1980), [1980], '1980 ', '1980,', '1980.', '1980)', '1980:' or end with '1980' if not at start and
        // (1980), [1980], ' 1980 ', '1980,', '1980.', or '1980)' if at start; instead of 1980, can be of form
        // 1980/1 or 1980/81 or 1980-1 or 1980-81
        // NOTE: '1980:' could be a volume number---might need to check for that
        $months = $this->monthsRegExp;
        $monthRegExp = '((' . $months . ')([-\/](' . $months . '))?)?';
        $yearRegExp = '((18|19|20)([0-9]{2})(-[0-9]{1,2}|\/[0-9]{1,2})?)[a-z]?';
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
        // See \xy\m\TeX\text2bib\regExpAnalysis.txt (as well as handwritten notes in Projects folder) for logic behind these numbers.
        // They are the indexes of the matches for the subpatterns on the regular expression
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
     * trimRightPeriod: remove trailing period if preceding character is not uppercase letter
     * @param $string string
     * return trimmed string
     */
    private function trimRightPeriod(string $string): string
    {
        if ($string == '' || $string == '.') {
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
    private function trimRightBrace(string $string): string
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
    private function extractPublisherAndAddress(string $string, string|null &$address, string|null &$publisher): string
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
    private function isYear(string $string): bool
    {
        $number = (int) $string;
        return $number > 1700 && $number < 2100;
    }

    // Report whether $string is the start of the name of the proceedings of a conference
    private function isProceedings(string $string): bool
    {
        $isProceedings = false;

        if (!Str::startsWith($string, $this->proceedingsExceptions) 
                && preg_match('/^' . $this->proceedingsRegExp . '/i', $string)) {
            $isProceedings = true;
        }

        return $isProceedings;
    }

    /**
     * bareWords: in array $words of strings, report the elements at the start up until one ends
     * in ',' or '.' or ')' or ':' or is a year in parens or brackets or starts with quotation mark
     * or, if $stopAtAnd is true, is 'and'.
     */
    private function bareWords(array $words, bool $stopAtAnd): array
    {
        $barewords = [];
        foreach ($words as $j => $word) {
            $stop = false;
            if (Str::endsWith($word, ['.', ',', ')', ':', '}'])) {
                $stop = true;
            }
            if (preg_match('/(\(|\[)(18|19|20)([0-9][0-9])(\)|\])/', $word)) {
                $stop = true;
            }
            if (Str::startsWith($word, ['`', '"', "'"])) {
                $stop = true;
            }

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
    private function nameScore(array $words, bool $ignoreAnd): array
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
    private function inDict(string $word): bool
    {
        $aspell = Aspell::create();
        return 0 == iterator_count($aspell->check($word));
    }

    /**
     * isNotName: determine if array of words starts with a name
     * @param $words array
     */
    private function isNotName(string $word1, string $word2): bool
    {
        $words = [$word1, $word2];
        $this->verbose(['text' => 'Arguments of isNotName: ', 'words' => [$words[0], $words[1]]]);
        $result = false;
        $accentRegExp = '/^(\\\"|\\\\\'|\\\`|\\\\\^|\\\H|\\\v|\\\~|\\\k|\\\c|\\\\\.)\{?[A-Z]\}?/';
        
        for ($i = 0; $i < 2; $i++) {
            if (preg_match($accentRegExp, $words[$i])) {
                $this->verbose(['text' => 'Name component ', 'words' => [$words[$i]], 'content' => ' starts with accented uppercase character']);
            } elseif (
                // Not a name if doesn't start with an accented uppercase letter and it starts with l.c. and is not
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
            if (!in_array($name, ['.', ','])) {
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

            // If name is ALL uppercase and contains no period, translate uppercase component to an u.c. first letter and the rest l.c.
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

    // Get journal name from $remainder, which includes also publication info
    private function getJournal(string &$remainder, object &$item, bool $italicStart, bool $pubInfoStartsWithForthcoming, bool $pubInfoEndsWithForthcoming): string
    {
        if ($italicStart) {
            $italicText = $this->getQuotedOrItalic($remainder, true, false, $before, $after);
            if (preg_match('/ [0-9]/', $italicText)) {
                // Seems that more than just the journal name is included in the italics/quotes, so forget the quotes/italics
                // and continue
                $remainder = $before . $italicText . $after;
            } else {
                $remainder = $before . $after;
                return $italicText;
            }
        }

        if ($pubInfoStartsWithForthcoming) {
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
            $this->setField($item, 'note', $label, '89');
        } elseif ($pubInfoEndsWithForthcoming) {
            // forthcoming at end
            $result = $this->extractLabeledContent($remainder, '.*', $this->endForthcomingRegExp, true);
            $journal = $result['label'];
            $this->setField($item, 'note', $result['content'], '90');
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

        // To deal with (erroneous) extra quotes at start
        $journal = ltrim($journal, "' ");

        return $journal;
    }

    // Allows page number to be preceded by uppercase letter.  Second number in range should really be allowed
    // to start with uppercase letter only if first number in range does so---and if pp. is present, almost
    // anything following should be allowed as page numbers?
    // '---' shouldn't be used in page range, but might be used by mistake
    private function getPagesForArticle(string &$remainder, object &$item): void
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
            $this->setField($item, 'pages', str_replace(['---', '--', ' '], ['-', '-', ''], $matches[3][$matchIndex][0]), '91');
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

    private function getVolumeAndNumberForArticle(string &$remainder, object &$item, bool &$containsNumberDesignation): void
    {
        if (ctype_digit($remainder)) {
            $this->verbose('Remainder is entirely numeric, so assume it is the volume');
            $this->setField($item, 'volume', $remainder, '92');
            $remainder = '';
        } elseif ($remainder && preg_match('/^[IVXLCDM]{0,8}$/', $remainder)) {
            $this->verbose('Remainder is Roman number, so assume it is the volume');
            $this->setField($item, 'volume', $remainder, '93');
            $remainder = '';
        } elseif ($this->containsFontStyle($remainder, false, 'bold', $startPos, $length)) {
            $this->verbose('[v2] bold (startPos: ' . $startPos . ')');
            $this->setField($item, 'volume', $this->getStyledText($remainder, false, 'bold', $before, $after, $remainder), '94');
            $this->verbose('remainder: ' . ($remainder ? $remainder : '[empty]'));
            if ($remainder && ctype_digit($remainder)) {
                $this->setField($item, 'pages', $remainder, '95');  // could be a single page
                $remainder = '';
                $this->verbose('[p3] pages: ' . $item->pages);
            }
        } else {
            // $item->number can be a range (e.g. '6-7')
            // Look for something like 123:6-19
            $this->verbose('[v3]');
            $this->verbose('Remainder: ' . $remainder);
            // 'Volume? 123$'
            $numberOfMatches1 = preg_match('/^(' . $this->volumeRegExp1 . ')?([1-9][0-9]{0,3})$/', $remainder, $matches1, PREG_OFFSET_CAPTURE);
            // $this->volumeRegExp1 has space at end of it, but no further space is allowed.
            // So 'Vol. A2' is matched but not 'Vol. 2, no. 3'
            $numberOfMatches2 = preg_match('/^(' . $this->volumeRegExp1 . ')([^ 0-9]*[1-9][0-9]{0,3})$/', $remainder, $matches2, PREG_OFFSET_CAPTURE);

            if ($numberOfMatches1) {
                $matches = $matches1;
            } elseif ($numberOfMatches2) {
                $matches = $matches2;
            } else {
                $matches = null;
            }

            if ($matches) {
                $this->verbose('[p2a] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0]);
                $this->setField($item, 'volume', $matches[2][0], '96');
                unset($item->number);
                // if a match is empty, [][1] component is -1
                $take = $matches[1][1] >= 0 ? $matches[1][1] : $matches[2][1];
                $drop = $matches[2][1] + strlen($matches[2][0]);
                $this->verbose('take: ' . $take . ', drop: ' . $drop);
                $this->verbose('No number assigned');
            } else {
                // A letter or sequence of letters is permitted after an issue number
                $numberOfMatches = preg_match('/(' . $this->volumeRegExp1 . ')?([1-9][0-9]{0,3})( ?, |\(| | \(|\.|:|;)(' . $this->numberRegExp1 . ')?( )?(([1-9][0-9]{0,6}[a-zA-Z]*)(-[1-9][0-9]{0,6})?)\)?/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                if ($numberOfMatches) {
                    $this->verbose('[p2b] matches: 1: ' . $matches[1][0] . ', 2: ' . $matches[2][0] . ', 3: ' . $matches[3][0] . ', 4: ' . $matches[4][0] . ', 5: ' . $matches[5][0] . (isset($matches[6][0]) ? ', 6: ' . $matches[6][0] : '') . (isset($matches[7][0]) ? ', 7: ' . $matches[7][0] : '') . (isset($matches[8][0]) ? ', 8: ' . $matches[8][0] : ''));
                    $this->setField($item, 'volume', $matches[2][0], '97');
                    if (strlen($matches[7][0]) < 5) {
                        $this->setField($item, 'number', $matches[6][0], '98');
                        if ($matches[4][0]) {
                            $containsNumberDesignation = true;
                        }
                    } else {
                        $this->setField($item, 'note', 'Article ' . $matches[6][0], '99');
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
                        $this->setField($item, 'volume', $volume, '100');
                        $take = $drop = 0;
                    } elseif (preg_match('/^article [0-9]*$/i', $remainder)) {
                        $this->setField($item, 'note', $remainder, '101');
                        $take = 0;
                        $drop = strlen($remainder);
                    } else {
                        // Look for something like 123:xxx (where xxx is not a page range)
                        $numberOfMatches = preg_match('/([1-9][0-9]{0,3})( ?, |\(| | \(|\.|:)*(.*)/', $remainder, $matches, PREG_OFFSET_CAPTURE);
                        if ($numberOfMatches) {
                            $this->verbose('[p2d]');
                            if (Str::startsWith($matches[3][0], ['Article', 'article'])) {
                                $this->setField($item, 'note', $matches[3][0], '102');
                                $this->setField($item, 'volume', $matches[1][0], '103');
                            } elseif (preg_match('/^([0-9]*) *([0-9]*)[ ]*$/', $remainder, $matches)) {
                                $this->setField($item, 'pages', $matches[2], '104');
                                $this->setField($item, 'volume', $matches[1], '105');
                            } else {
                                // Assume all of $remainder is volume (might be something like '123 (Suppl. 19)')
                                $this->setField($item, 'volume', $remainder, '106');
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
                $this->setField($item, 'pages', $remainder, '107'); // could be a single page
                $remainder = '';
                $this->verbose('[p4] pages: ' . $item->pages);
            }
        }
    }

    private function cleanText(string $string, string|null $charEncoding): string
    {
        $string = str_replace("\\newblock", "", $string);
        // Replace each tab with a space
        $string = str_replace("\t", " ", $string);
        $string = str_replace("\\textquotedblleft ", "``", $string);
        $string = str_replace("\\textquotedblright ", "''", $string);
        $string = str_replace("\\textquotedblright", "''", $string);

        if ($charEncoding == 'utf8' || $charEncoding == 'utf8leave') {
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
       
        if ($charEncoding == 'utf8') {
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