<?php
namespace App\Services;

use Str;

use App\Traits\Countries;
use App\Traits\Utilities;

class TitleParser
{
    var $titleDetails = [];

    private Dates $dates;

    public function __construct()
    {
        $this->dates = new Dates();
    }

    /**
     * Countries are used to check last word of title, following a comma and followed by a period --- country
     * names that are not abbreviations used at the start of journal names or other publication info
     */
    use Countries;
    use Utilities;

    // Overrides method in Utilities trait
    private function verbose(string|array $arg): void
    {
        $this->titleDetails[] = $arg;
    }

    /**
     * Get title from a string that starts with title and then has publication information.
     * Case in which title is in quotation marks or italics is dealt with separately.
     * Case in which title is followed by authors (editors), as in <booktitle> <editor> format, is handled by
     * getTitlePrecedingAuthor method.
     */
    public function getTitle(
        string &$remainder, 
        string|null &$edition, 
        string|null &$volume, 
        bool &$isArticle, 
        string|null &$year = null, 
        string|null &$note, 
        string|null $journal, 
        bool $containsUrlAccessInfo, 
        array $publishers, 
        array $startJournalAbbreviations, 
        array $excludedWords, 
        array $cities, 
        array $dictionaryNames, 
        string $pagesRegExp, 
        string $pageRegExp, 
        string $startPagesRegExp, 
        string $fullThesisRegExp, 
        string $edsOptionalParensRegExp, 
        array $monthsRegExp,
        string $inRegExp, 
        bool $includeEdition = false, 
        string $language = 'en'
       ): array
    {
        $title = null;
        $originalRemainder = $remainder;

        $remainder = str_replace('  ', ' ', $remainder);
        $words = explode(' ', $remainder);
        $initialWords = [];
        $remainingWords = $words;
        $skipNextWord = false;

        $note = null;

        $italicCodesRegExp = '';
        foreach ($this->italicCodes as $i => $italicCode) {
            $italicCodesRegExp .= ($i ? '|' : '') . str_replace('\\', '\\\\', $italicCode);
        }

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

            $result['title'] = $title;
            $result['titleDetails'] = $this->titleDetails;
    
            return $result;
        }

        // If $remainder ends with string in parenthesis, look at the string
        if (preg_match('/\(([^\(]*)\)$/', rtrim($remainder, '. '), $matches)) {
            $match = $matches[1];
            if (Str::contains($match, $publishers)) {
                // String in parentheses seems like it's the publication info; set $title equal to preceding string
                $title = rtrim(Str::before($remainder, $match), ' (');
                $remainder = $match;
                $this->verbose('Taking title to be string preceding string in parentheses, which is taken to be publication info');

                $result['title'] = $title;
                $result['titleDetails'] = $this->titleDetails;
        
                return $result;
            }
        }

        // Common pattern for journal article.  
        // Find strings preceding and following first period (because of ? in expression .*?) that is followed by
        // a space and then not a lowercase letter.
        // (Allow year at end of title, but no other pattern with digits, otherwise whole string,
        // including journal name and volume, number, and page info may be included.)
        if (preg_match('/^(?P<title>.*? (?P<lastWord>(\p{L}+|' . $this->yearRegExp . ')))\. (?P<remainder>[^\p{Ll}][\p{L}\.,\\\' ]{5,30} [0-9;():\-.,\. ]{9,})$/', $remainder, $matches)) {
            $lastWord = $matches['lastWord'];
            // Last word has to be in the dictionary (proper nouns allowed) and not an excluded word, OR start with a lowercase letter.
            // That excludes cases in which the period ends an abbreviation in a journal name (like "A theory of something, Bull. Amer.").
            if (
                ! in_array($lastWord, $startJournalAbbreviations)
                && 
                ! in_array($lastWord, ['no', 'vol', 'pp'])
                &&
                (
                    ($this->inDict($lastWord, $dictionaryNames, false) && ! in_array($lastWord, $excludedWords))
                    ||
                    mb_strtolower($lastWord[0]) == $lastWord[0]
                )
               ) {
                $title = $matches['title'];
                $remainder = $matches['remainder'];
                $this->verbose('Taking title to be string preceding period.');

                $result['title'] = $title;
                $result['titleDetails'] = $this->titleDetails;
        
                return $result;
            }
        }

        $containsPages = preg_match('/(\()?' . $pagesRegExp . '(\))?/', $remainder);
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

            if (Str::startsWith($word, '//')) {
                $this->verbose("Ending title, case 1a");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                $remainder = ltrim(implode(' ', $remainingWords), '/');
                break;
            }

            array_shift($remainingWords);
            $remainder = ltrim(implode(' ', $remainingWords), '/');

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
                $this->verbose("Ending title, case 1b");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                break;
            }

            if (Str::endsWith($word, '//')) {
                $this->verbose("Ending title, case 1c");
                $title = rtrim(implode(' ', $initialWords), ',:;.') . ' ' . substr($word, 0, -2);
                break;
            }

            $initialWords[] = $word;

            if (preg_match('/^vol(\.?|ume) [0-9]/', $remainder)) {
                $this->verbose("Ending title, case 1d");
                $title = rtrim(implode(' ', $initialWords), ',:;.');
                break;
            }

            if ($skipNextWord) {
                $skipNextWord = false;
            } else {
                $nextWord = $words[$key + 1] ?? null;
                $nextButOneWord = $words[$key + 2] ?? null;
                $word = trim($word);
                $nextWord = $nextWord == null ? '' : trim($nextWord);

                if (empty($nextWord)) {
                    $title = rtrim(implode(' ', $initialWords), ',:;.');
                    break;
                }

                // String up to next '?', '!', ',', or '.' not preceded by ' J'.
                $chars = mb_str_split($remainder, 1, 'UTF-8');
                $stringToNextPeriodOrComma = '';

                foreach ($chars as $i => $char) {
                    if ($char == '(' && ($i == 0 || $chars[$i-1] == ' ')) {
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
                    if ($char == '(' && ($i == 0 || $chars[$i-1] == ' ')) {
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
                    $upcomingYear = $this->dates->isYear(trim($remainderFollowingNextPeriodOrComma));
                    $upcomingVolumePageYear = preg_match('/^(Vol\.? |Volume )?[0-9\(\)\., p\-]{2,}$/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingVolumeNumber = preg_match('/^(' . $this->volRegExp3 . ')[0-9]{1,4},? (' . $this->numberRegExp . ')? ?\(?[0-9]{1,4}\)?/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingRoman = preg_match('/^[IVXLCD]{1,6}[.,; ] ?/', trim($remainderFollowingNextPeriodOrComma));
                    $followingRemainderMinusMonth = preg_replace('/' . $monthsRegExp[$language] . '/', '', $remainderFollowingNextPeriodOrComma);
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
                $journalPubInfoNext = preg_match('/^' . $this->yearRegExp . '(,|;| ) ?(' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?([0-9, \-p\.():]*$|\([0-9]{2,4}\))/', $remainder);

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
                    $remainder = ltrim($remainder, '{ ');

                    if (
                        ! Str::endsWith($word, ':')
                        &&
                        (
                            // e.g. SIAM J. ... (Don't generalize too much, because 'J.' can be an editor's initial.)
                            preg_match('/^(SIAM (J\.|Journal)|IEEE Transactions|ACM Transactions)/', $remainder)
                            // journal name, pub info?
                            || preg_match('/^[A-Z][a-z]+( [A-Z][a-z]+)?,? [0-9, \-p\.]*$/', $remainder)
                            || in_array('Journal', $wordsToNextPeriodOrComma)
                            || preg_match('/^Revue /', $remainder)
                            // journal name, pub info ('}' after volume # for \textbf{ (in $this->volumeRegExp))
                            // ('?' is a possible character in a page range because it can appear for '-' due to an encoding error)
                            // The following pattern allows too much latitude --- e.g. "The MIT Press. 2015." matches it.
                            // || preg_match('/^[A-Z][A-Za-z &]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->numberRegExp . ')?[0-9, \-p\.():\?]*$/', $remainder) 
                            // journal name, volume (year?) issue? page
                            // Note: permits no space or punctuation between volume number and page number
                            || preg_match('/^\p{Lu}[\p{L} &()}]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?([(\[]?' . $this->yearRegExp . '[)\]]?,? ?)?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $pagesRegExp . '\.? ?$/u', $remainder) 
                            // similar, but requires some punctuation or space between volume and page numbers, but allows a single
                            // page --- does not require a page range.
                            || preg_match('/^\p{Lu}[\p{L} &()}]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?(, |: | )([(\[]?' . $this->yearRegExp . '[)\]]?,? ?)?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $pageRegExp . '\.? ?$/u', $remainder) 
                            // journal name followed by year and publication info, allowing issue number and page
                            // numbers to be preceded by letters and issue number to have / or - in it.
                            || preg_match('/^\p{Lu}[\p{L} &()\-]+[,.]? ' . $this->yearRegExp . ',? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $pagesRegExp . '\.? ?$/u', $remainder)
                            // year followed by journal name and publication info, allowing issue number and page
                            // numbers to be preceded by letters and issue number to have / or - in it.
                            // Note that this case allows a single page or a page range.
                            || preg_match('/^' . $this->yearRegExp . ',? \p{Lu}[\p{L} &()\-]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $pageRegExp . '\.? ?$/u', $remainder)
                            // journal name followed by more specific publication info, year at end, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^\p{Lu}[\p{L} &()]+[,.]? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $pagesRegExp . '(, |. |.)(\(?' . $this->yearRegExp . '\)?)$/u', $remainder) 
                            // journal name followed by more specific publication info, year first, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^\p{Lu}[\p{L} &()]+[,.]? ' . $this->yearRegExp . ',? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $pagesRegExp . '\.? ?$/u', $remainder)
                            // journal name (no commas) followed by comma, volume, number (and possible page numbers).
                            || preg_match('/^\p{Lu}[\p{L} &]+,? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:( ] ?(' . $this->numberRegExp . ')?[0-9, \-p\.():\/]*$/u', $remainderMinusArticle)
                            // $word ends in period && journal name (can include commma), pub info ('}' after volume # for \textbf{ (in $this->volumeRegExp))
                            || (Str::endsWith($word, ['.']) && preg_match('/^\p{Lu}[\p{L}, &]+,? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:( ] ?(' . $this->numberRegExp . ')?[0-9, \-p\.():\/]*$/u', $remainderMinusArticle))
                            || (Str::endsWith($word, ['.']) && preg_match('/^\p{Lu}[\p{L}, &]+,? (' . $this->volumeRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->numberRegExp . ')?\([0-9]{2,4}\)/u', $remainderMinusArticle))
                        )
                    ) {
                        $upcomingJournalAndPubInfo = true;
                        $isArticle = true;
                        $this->verbose('Followed by journal name and publication info, so classified as article');
                    }

                    // $word ends in period && then there are letters and spaces, and then a page range in parens
                    // (so string before page range is booktitle?)
                    if (Str::endsWith($word, ['.']) && preg_match('/^[A-Z][A-Za-z ]+,? ?\(?(' . $pagesRegExp . ')/', $remainder)) { 
                        $upcomingPageRange = true;
                    }

                    /////////////////
                    // Translators //
                    /////////////////
                    $translatorNext = false;
                    // "(John Smith, trans.)"
                    if (in_array($nextWord[0], ['('])) {
                        $translatorNext = preg_match('/^\((?P<translator>[^)]+[Tt]rans\.)\)(?P<remainder>.*)/', $remainder, $matches);
                        if (isset($matches['translator'])) {
                            $note = ($note ? $note . '. ' : '') . $matches['translator'];
                            $remainder = $matches['remainder'];
                        }
                    } elseif (in_array($nextWord,  ['Translated', 'Translation']) && $nextButOneWord == 'by') {
                        // Extraction of translators' names handled separately.
                        $translatorNext = true;
                    } elseif ($nextWord == 'trans.') {
                        // "trans. John Smith."
                        // Here trans must start with lowercase, because journal name might start with Trans. and period
                        // cannnot be preceded by uppercase letter (which would be initial of translator)
                        if (preg_match('/^trans\. (?P<translator>[^.]+(?<!\p{Lu})\.)(?P<remainder>.*)/u', $remainder, $matches)) {
                            $translatorNext = true;
                            $note = ($note ? $note . '. ' : '') . 'Translated by ' . $matches['translator'];
                            $remainder = $matches['remainder'];
                        } elseif (preg_match('/^trans\. (?P<translator>[^,]+), (?P<remainder>.{5,})/', $remainder, $matches)) {
                            $translatorNext = true;
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
                        || preg_match($startPagesRegExp, $remainder)
                        || preg_match('/^' . $inRegExp . ':? (`|``|\'|\'\'|"' . $italicCodesRegExp . ')?([A-Z1-9]|' . $this->yearRegExp . ')/', $remainder)
                        || preg_match('/^' . $this->journalWord . ' |^Annals |^Proc(eedings)? |^\(?Vols?\.? |^\(?VOL\.? |^\(?Volume |^\(?v\. /', $remainder)
                        || (
                            $nextWord 
                            && Str::endsWith($nextWord, '.') 
                            && in_array(substr($nextWord, 0, -1), $startJournalAbbreviations)
                           )
                        || (
                            $nextWord 
                            && $nextButOneWord 
                            && (Str::endsWith($nextWord, range('a', 'z')) || in_array($nextWord, ['IEEE', 'ACM'])) 
                            && Str::endsWith($nextButOneWord, '.') 
                            && in_array(substr($nextButOneWord, 0, -1), $startJournalAbbreviations)
                           )
                        // pages (e.g. within book)
                        || preg_match('/^\(?pp?\.? [0-9]/', $remainder)
                        || preg_match('/' . $this->startForthcomingRegExp . '/i', $remainder)
                        || preg_match('/^' . $this->yearRegExp . '(\.|$)/', $remainder)
                        // editor next
                        || preg_match('/^' . $edsOptionalParensRegExp . ' /', $remainder)
                        // address [no spaces]: publisher in db
                        || (
                            preg_match('/^[A-Z][a-z]+: (?P<publisher>[A-Za-z ]*),/', $remainder, $matches) 
                            && in_array(trim($matches['publisher']), $publishers)
                           )
                        // address [city in db]: publisher
                        || (
                            preg_match('/^(?P<city>[A-Z][a-z]+): /', $remainder, $matches) 
                            && in_array(trim($matches['city']), $cities)
                           )
                        // one- or two-word publisher, address [city in db], <year>?
                        || (
                            preg_match('/^[A-Z][a-z]+( [A-Z][a-z]+)?, (?P<city>[A-Za-z, ]+)(, ' . $this->yearRegExp . ')?$/', $remainder, $matches) 
                            && in_array(trim($matches['city']), $cities)
                           )
                        // one- or two-word publisher, city (up to 2 words), US State, <year>?
                        || preg_match('/^[A-Z][a-z]+( [A-Z][a-z]+)?, (?P<city>[A-Z][a-z]+( [A-Z][a-z]+)?, [A-Z]{2})(, ' . $this->yearRegExp . ')?$/', $remainder, $matches) 
                        // [,.] <address>: <publisher>(, <year>)?$ OR (<address>: <publisher>(, <year>)?)
                        // Note that ',' is allowed in address and
                        // '.' and '&' are allowed in publisher.  May need to put a limit on length of publisher part?
                        || (
                            (Str::endsWith($word, '.') || Str::endsWith($word, ',') || $nextWord[0] == '(')
                            &&
                            $this->isAddressPublisher($remainder)
                           )
                        // <publisher>, <address>, <year>
                        || preg_match('/^(?P<publisher>[\p{L}&\\\ ]{5,20}), (?P<address>[\p{L} ]{5,15}), (?P<year>' . $this->yearRegExp . ')\.?$/u', $remainder, $matches) 
                        // <publisher>, <address> (<year>)
                        || preg_match('/^(?P<publisher>[\p{L}&\\\ ]{5,20}), (?P<address>[\p{L} ]{5,15}) \((?P<year>' . $this->yearRegExp . ')\)\.?$/u', $remainder, $matches) 
                        // (<publisher> in db
                        || Str::startsWith(ltrim($remainder, '('), $publishers)
                        // (<city> in db
                        || Str::startsWith(ltrim($remainder, '('), $cities)
                        // Thesis
                        || preg_match('/^[\(\[\-]? ?' . $fullThesisRegExp . '/i', $remainder)
                        || preg_match('/^[\(\[\-]? ?Thesis[.,] /', $remainder)
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
                        &&
                        (
                            (ctype_alpha($nextWord[0]) && mb_strtolower($nextWord[0]) == $nextWord[0] && substr($nextWord, -1) != '.' && rtrim($nextWord, ':') != 'in')
                            || 
                            ($word == 'A.' && $nextWord == 'D.')
                            || 
                            ($word == 'B.' && $nextWord == 'C.')
                            || 
                            preg_match('/^(Part )?(I|II|III|[1-9])[:.] /', $remainder)
                        )
                        &&
                        ! ($nextWord == 'edited' && $nextButOneWord == 'by')
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
                            (! Str::endsWith($word, ',') 
                                || (! $this->inDict(substr($nextWord, 0, -1), $dictionaryNames) && ! in_array(substr($nextWord, 0, -1), $this->countries)) 
                                || $this->isInitials($nextWord) 
                                || (
                                    mb_strtolower($nextWord[0]) == $nextWord[0]
                                    &&
                                    isset($words[$key+2][0])
                                    &&
                                    mb_strtolower($words[$key+2][0]) == $words[$key+2][0]
                                   )
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
                    } elseif (preg_match('/^[\(\[]' . $fullThesisRegExp . '[\)\]]/', $stringToNextPeriodOrComma)) {
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
                            preg_match('/^[a-zA-Z0-9 \-\(\)`"\':,\/]+$/', substr($stringToNextPeriodOrComma, 0, -1))
                            //preg_match('/[a-zA-Z -]+/', substr($stringToNextPeriodOrComma,0,-1))
                            && ! preg_match('/^' . $inRegExp . ':? /', $remainder)
                            && ! $this->isProceedings($remainder)
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
                            && ! $this->isProceedings($modStringToNextPeriod)
                            && substr_count($modStringToNextPeriod, ',') == 0
                            && substr_count($modStringToNextPeriod, ':') == 0
                            && (! preg_match('/^[0-9;:\.\- ]*$/', $remainderFollowingNextPeriodOrComma) || $containsUrlAccessInfo)
                        ) {
                            $this->verbose("Not ending title, case 6 (word '" . $word ."')");
                        } elseif (! isset($words[$key+2])) {
                            if ($this->dates->isYear($nextWord)) {
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
                        // Next case was intended for title followed by authors (as in <booktitle> <editors>) ---
                        // but that case is now handled separately
                        // } elseif (Str::endsWith($word, [',']) && preg_match('/[A-Z][a-z]+, [A-Z]\. /', $remainder)) {
                        //     $this->verbose("Ending title, case 6a (word '" . $word ."')");
                        //     $title = rtrim(implode(' ', $initialWords), '.,');
                        //     break;
                        } elseif (Str::endsWith($word, [','])) {
                            $this->verbose("Not ending title, case 7a (word '" . $word ."')");
                        } elseif (in_array(rtrim($wordAfterNextCommaOrPeriod, '.'), $startJournalAbbreviations)) {
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

        $result['title'] = $title;
        $result['titleDetails'] = $this->titleDetails;

        return $result;
    }
}