<?php
namespace App\Services;

use Str;

use App\Traits\Countries;
use App\Traits\Utilities;

use App\Services\RegularExpressions;
use SebastianBergmann\Type\FalseType;

class TitleParser
{
    var $titleDetails = [];

    private Dates $dates;
    private RegularExpressions $regExps;
    public AuthorParser $authorParser;

    public function __construct()
    {
        $this->dates = new Dates();
        $this->regExps = new RegularExpressions;
        $this->authorParser = new AuthorParser();
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
        array $journalWordAbbreviations, 
        array $excludedWords, 
        array $cities, 
        array $dictionaryNames, 
        bool $includeEdition = false, 
        string $language = 'en'
       ): array
    {
        $titleAbbreviations = [
            'St.',
            'vs.',
        ];

        $title = $editor = $translator = null;
        $this->titleDetails = [];
        $seriesNext = false;
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
            $result['editor'] = $editor;
            $result['translator'] = $translator;
            $result['editionNumber'] = null;
            $result['fullEdition'] = null;
    
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
                $result['editor'] = $editor;
                $result['translator'] = $translator;
                $result['editionNumber'] = null;
                $result['fullEdition'] = null;
        
                return $result;
            }
        }

        // Common pattern for journal article.  
        // Find strings preceding and following first period (because of ? in expression .*?) that is followed by
        // a space and then not a lowercase letter.
        // (Allow year at end of title, but no other pattern with digits, otherwise whole string,
        // including journal name and volume, number, and page info may be included.)
        if (
            preg_match('/^(?P<title>[^\.]*? (?P<lastWord>(\p{L}+|' . $this->yearRegExp . ')))\. (?P<remainder>[^\p{Ll}][\p{L}\.,\\\' ]{5,30} [0-9;():\-,\. ]{9,})$/u', $remainder, $matches)
            ||
            preg_match('/^(?P<title>[^\.]*? (?P<lastWord>(\p{L}+|' . $this->yearRegExp . ')))\. (?P<remainder>[^\p{Ll}][\p{L}, ]{5,60} [0-9;():\-,\. ]{9,})$/u', $remainder, $matches)
            ) {
            $lastWord = $matches['lastWord'];
            $title = $matches['title'];
            $remainder = $matches['remainder'];
            $firstRemainderWord = explode(' ', $remainder)[0];
            // Last word has to be in the dictionary (proper nouns allowed) and not an excluded word, OR start with a lowercase letter.
            // That excludes cases in which the period ends an abbreviation in a journal name (like "A theory of something, Bull. Amer.").
            if (
                ! in_array($lastWord, $journalWordAbbreviations)
                && 
                ! in_array($lastWord, ['no', 'vol', 'pp'])
                &&
                (
                    ($this->inDict($lastWord, $dictionaryNames, false) && ! in_array($lastWord, $excludedWords))
                    ||
                    mb_strtolower($lastWord[0]) == $lastWord[0]
                )
                &&
                (
                    substr($firstRemainderWord, -1) != '.'
                    ||
                    in_array($lastWord, $journalWordAbbreviations)
                )
               ) {
                $this->verbose('Taking title to be string preceding period.');

                $result['title'] = $title;
                $result['titleDetails'] = $this->titleDetails;
                $result['editor'] = $editor;
                $result['translator'] = $translator;
                $result['editionNumber'] = null;
                $result['fullEdition'] = null;
        
                return $result;
            }
        }

        $containsPages = preg_match('/(\()?' . $this->regExps->pagesRegExp . '(\))?/', $remainder);
        $volumeWithDigitRegExp = '/^(' . $this->regExps->volumeRegExp . ') (\d)\.?\)?[.,]?$/i';

        $parensLevel = 0;
        $bracketLevel = 0;
        // $skip = 0;
        // Go through the words in $remainder one at a time.
        foreach ($words as $key => $word) {
            // if ($skip) {
            //     $skip--;
            //     continue;
            // }

            if (substr($word, 0, 1) == '(' && strpos($word, ')') === false) {
                $parensLevel++;
                $this->verbose("Parens level changed to " . $parensLevel . " (word \"" . $word . "\")");
            } elseif (strpos($word, '(') === false && strpos($word, ')') !== false) {
                $parensLevel--;
                $this->verbose("Parens level changed to " . $parensLevel . " (word \"" . $word . "\")");
            }

            if (substr($word, 0, 1) == '[' && strpos($word, ']') === false) {
                $bracketLevel++;
                $this->verbose("Bracket level changed to " . $bracketLevel . " (word \"" . $word . "\")");
            } elseif (strpos($word, '[') === false && strpos($word, ']') !== false) {
                $bracketLevel--;
                $this->verbose("Bracket level changed to " . $bracketLevel . " (word \"" . $word . "\")");
            }

            if (substr($word, 0, 1) == '"') {
                $word = '``' . substr($word, 1);
            }
            if (substr($word, -1) == '"') {
                $word = substr($word, 0, -1) . "''";
            }

            if (Str::startsWith($word, '//')) {
                $this->verbose("Ending title, case 1a");
                $title = rtrim(implode(' ', $initialWords), ',:;');
                $remainder = ltrim(implode(' ', $remainingWords), '/');
                break;
            }

            array_shift($remainingWords);
            $remainder = ltrim(implode(' ', $remainingWords), '/');

            // // Unsuccessful attempt to deal with date range preceded or followed by A. D. or B. C. in a title.
            // if (
            //     preg_match('/^(?P<dateRange>(A\. D\.|B\. C\.) \d{3,4}--?\d{3,4})[.,](?P<rest>.*)$/', $remainder, $matches)
            //     ||
            //     preg_match('/^(?P<dateRange>\d{3,4}--?\d{3,4} (A\. D\.|B\. C\.))[.,](?P<rest>.*)$/', $remainder, $matches)
            //     )
            //     {
            //     if (isset($matches['dateRange'])) {
            //         $initialWords[] = $word;
            //         $initialWords = array_merge($initialWords, explode(' ', $matches['dateRange']));
            //         $remainder = trim($matches['rest']);
            //         $remainingWords = explode(' ', $remainder);
            //         $this->verbose('Date range with A. D. or B. C. detected, so added to title');
            //         $skip = 3;
            //         continue;
            //     }
            // }

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
                $title = rtrim(implode(' ', $initialWords), ',:;');
                break;
            }

            if (Str::endsWith($word, '//')) {
                $this->verbose("Ending title, case 1c");
                $title = rtrim(implode(' ', $initialWords), ',:;.') . ' ' . substr($word, 0, -2);
                break;
            }

            $initialWords[] = $word;

            // volume is next
            if (preg_match('/^(' . $this->regExps->volumeRegExp . ') [0-9]/', $remainder)) {
                $this->verbose("Ending title, case 1d");
                $title = rtrim(implode(' ', $initialWords), ',:;');
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
                    $title = rtrim(implode(' ', $initialWords), ',:;');
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
                    $upcomingVolumePageYear = preg_match('/^(' . $this->regExps->volumeRegExp . ' )?[0-9\(\)\., p\-]{2,}$/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingVolumeNumber = preg_match('/^(' . $this->regExps->volumeAndCodesRegExp . ')[0-9]{1,4},? (' . $this->regExps->numberRegExp . ')? ?\(?[0-9]{1,4}\)?/', trim($remainderFollowingNextPeriodOrComma));
                    $upcomingRoman = preg_match('/^[IVXLCD]{1,6}[.,; ] ?/', trim($remainderFollowingNextPeriodOrComma));
                    $followingRemainderMinusMonth = preg_replace('/' . $this->dates->monthsRegExp[$language] . '/', '', $remainderFollowingNextPeriodOrComma);
                    $upcomingArticlePubInfo = preg_match('/^[0-9.,;:\-() ]{8,}$/', $followingRemainderMinusMonth);
                }

                $upcomingJournalAndPubInfo = $upcomingPageRange = false;
                $wordsToNextPeriodOrComma = explode(' ', $stringToNextPeriodOrComma);

                // This case may arise if a string has been removed from $remainder and a lone '.' is left in the middle of it.
                // if (isset($remainingWords[0]) && $remainingWords[0] == '.') {
                //     array_shift($remainingWords);
                //     $remainder = ltrim($remainder, ' .');
                // }
                // Space before \S+ is important, because space after Vol in $volumeRegExp is optional.
                $upcomingBookVolume = preg_match('/^\(?(' . $this->regExps->volumeRegExp . ') \S+ (?!of)/', $remainder);
                $upcomingVolumeCount = preg_match('/^\(?(?P<note>[1-9][0-9]{0,1} (' . $this->regExps->volumeRegExp . '))\)?/', $remainder, $volumeCountMatches);
                $journalPubInfoNext = preg_match('/^' . $this->yearRegExp . '(,|;| ) ?(' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?([0-9, \-p\.():]*$|\([0-9]{2,4}\))/', $remainder);

                if ($journalPubInfoNext) {
                    $this->verbose("Ending title, case 2a (journal pub info next, with no journal name)");
                    $title = rtrim(implode(' ', $initialWords), ',:;');
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
                    ! in_array($word, $titleAbbreviations)
                    &&
                    (
                        Str::endsWith(rtrim($word, "'\""), ['.', '!', '?', ':', ',', ';']) 
                        ||
                        ($nextWord && in_array($nextWord[0], ['(', '['])) 
                        || 
                        ($nextWord && $nextWord == '-')
                    )
                    &&
                    // if $word is followed by comma, and then a country name followed by ":", add $word to title
                    ! (
                        substr($word, -1) == ','
                        &&
                        substr($nextWord, -1) == ':'
                        &&
                        in_array(substr($nextWord, 0, -1), $this->countries)
                    )
                   ) {
                    $remainderMinusArticle = preg_replace('/[\( ][Aa]rticle /', '', $remainder);
                    $remainder = ltrim($remainder, '{ ');

                    if (
                        // Some styles use a colon to separate the title from the publication information,
                        // and removing the next condition does not affect the conversion of any other item.
                        // ! Str::endsWith($word, ':')
                        // &&
                        (
                            // e.g. SIAM J. ... (Don't generalize too much, because 'J.' can be an editor's initial.)
                            preg_match('/^(SIAM (J\.|Journal)|IEEE Transactions|ACM Transactions)/', $remainder)
                            // 1- or 2-word journal name followed by numbers, 'p', '.', ' ', and '-' (pub info)
                            || preg_match('/^\p{Lu}\p{Ll}+( \p{Lu}\p{Ll}+)?,? [0-9, \-p\.]*$/u', $remainder)
                            || (in_array('Journal', $wordsToNextPeriodOrComma) && ! preg_match('/^[a-z]/', $remainder))
                            || preg_match('/^' . $this->regExps->journalRegExp . ' /', $remainder)
                            // journal name, pub info ('}' after volume # for \textbf{ (in $volumeAndCodesRegExp))
                            // ('?' is a possible character in a page range because it can appear for '-' due to an encoding error)
                            // The following pattern allows too much latitude --- e.g. "The MIT Press. 2015." matches it.
                            // || preg_match('/^\p{Lu}[A-Za-z &]+[,.]? (' . $volumeAndCodesRegExp . ')? ?[0-9]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?[0-9, \-p\.():\?]*$/', $remainder) 
                            // journal name, forthcoming/in press/... 
                            || preg_match('/^\p{Lu}[\p{L} &()}]+[,.]?(' . $this->endForthcomingRegExp . ')/u', $remainder) 
                            // journal name of form "Aaaa, Aaaa & Aaaa" followed by volume(number) page range 
                            || 
                            (
                                preg_match('/^((\p{Lu}\p{L}+|&),? ){1,4}[0-9(),\- ]{5,20}$/u', $remainder) 
                                &&
                                ! preg_match('/' . $this->regExps->workingPaperRegExp . '/u', $remainder)
                            )
                            // journal name, volume (year?) issue? page
                            // Note: permits no space or punctuation between volume number and page number
                            || preg_match('/^\p{Lu}[\p{L} &()}]+[,.]? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?([(\[]?' . $this->yearRegExp . '[)\]]?,? ?)?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $this->regExps->pagesRegExp . '\.? ?$/u', $remainder)
                            // similar, but requires some punctuation or space between volume and page numbers, but allows a single
                            // page --- does not require a page range.
                            || preg_match('/^\p{Lu}[\p{L} &()}]+[,.]? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?(, |: | )([(\[]?' . $this->yearRegExp . '[)\]]?,? ?)?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $this->regExps->pageRegExp . '\.? ?$/u', $remainder) 
                            // journal name, year, volume, number, pages allowing issue number and page
                            // numbers to be preceded by letters and issue number to have / or - in it.
                            || preg_match('/^\p{Lu}[\p{L} &()\-]+[,.]? (' . $this->dates->monthsRegExp[$language] . ')? ?' . $this->yearRegExp . '[,;]? ?(' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?(supp )?[A-Z]?[0-9\/\-]{0,4}\)?[:,]? ?' . $this->regExps->pagesRegExp . '\.? ?$/u', $remainder)
                            // year, journal name, volume, number, pages, allowing issue number and page
                            // numbers to be preceded by letters and issue number to have / or - in it.
                            // Note that this case allows a single page or a page range.
                            || preg_match('/^' . $this->yearRegExp . ',? \p{Lu}[\p{L} &()\-]+[,.]? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $this->regExps->pageRegExp . '\.? ?$/u', $remainder)
                            // journal name, volume, number, pages, year, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^\p{Lu}[\p{L} &()]+[,.]? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $this->regExps->pagesRegExp . '(, |. |.)(\(?' . $this->yearRegExp . '\)?)$/u', $remainder) 
                            // journal name, year, volume, number, pages, allowing issue number and page
                            // numbers to be preceded by letters.
                            || preg_match('/^\p{Lu}[\p{L} &()]+[,.]? ' . $this->yearRegExp . ',? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/]{1,4}\)?,? ' . $this->regExps->pagesRegExp . '\.? ?$/u', $remainder)
                            // journal name (no commas) followed possibly by comma, then volume (possibly styled), number (possibly styled) (and possibly page numbers).  The "Sul" accommodates an issue called "Suppl 1" and page numbers starting with S
                            || preg_match('/^\p{Lu}[\p{L} &]+,? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?(, ?|: ?| \()(' . $this->regExps->numberAndCodesRegExp . ')?[0-9, \-p.():\/{}Sul]*$/u', $remainderMinusArticle)
                            // journal name that can contain commas and periods, but every period must be preceded by an uppercase letter
                            // (to allow names like "Current Psychology (New Brunswick, N. J.)"); the first comma must come near the end of
                            // the name (to allow strings like "N. J.", but not longer strings that themselves could be the journal name)
                            ||
                            (
                                preg_match('/^(?P<journal>(?:[\p{L}\-() ]*|[\p{L}\-(), ]*[A-Z]\.)*[\p{L}\-() ]*), [\d(),\- ]{5,}$/', $remainderMinusArticle, $matches)
                                &&
                                (
                                    strpos($matches['journal'], ',') === false
                                    ||
                                    strlen(Str::after($matches['journal'], ',')) < 10
                                )
                            )
                            // $word ends in period && journal name (can include commma), pub info ('}' after volume # for \textbf{ (in $volumeAndCodesRegExp))
                            || (Str::endsWith($word, ['.']) && preg_match('/^\p{Lu}[\p{L}, &]+,? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:( ] ?(' . $this->regExps->numberRegExp . ')?[0-9, \-p\.():\/]*$/u', $remainderMinusArticle))
                            || (Str::endsWith($word, ['.']) && preg_match('/^\p{Lu}[\p{L}, &]+,? (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?[,:(]? ?(' . $this->regExps->numberRegExp . ')?\([0-9]{2,4}\)/u', $remainderMinusArticle))
                        )
                    ) {
                        $upcomingJournalAndPubInfo = true;
                        $isArticle = true;
                        $this->verbose('Followed by journal name and publication info, so classified as article');
                    }

                    // $word ends in period && then there are letters and spaces, and then a page range in parens
                    // (so string before page range is booktitle?)
                    if (Str::endsWith($word, ['.']) && preg_match('/^\p{Lu}[\p{L} ]{3,}(?P<punc>,?) ?\(?(' . $this->regExps->pagesRegExp . ')/u', $remainder, $matches)) { 
                        // If number range is preceded by at least 3 letters/spaces, with no punctuation, it is not a page range
                        if (isset($matches['punc']) && ($matches['pageWord'] || $matches['punc'] == ',')) {
                            $upcomingPageRange = true;
                        }
                    }

                    /////////////////
                    // Translators //
                    /////////////////

                    $translatorNext = false;
                    // "(Jane Smith, trans.)" or "(Volume 2, Jane Smith, trans.)"
                    if (in_array($nextWord[0], ['('])) {
                        $translatorNext = preg_match('/^\((?P<string>(?P<editor>[^)]+) ' . $this->regExps->edsNoParensRegExp . '(?P<translator>[^)]+)' . $this->regExps->translatorRegExp . ')\)(?P<remainder>.*)/', $remainder, $matches);
                        if (! $translatorNext) {
                            $translatorNext = preg_match('/^\((?P<string>(?P<translator>[^)]+) ' . $this->regExps->translatorRegExp . '(?P<editor>[^)]+)' . $this->regExps->edsNoParensRegExp . ')\)(?P<remainder>.*)/', $remainder, $matches);
                        }
                        if (! $translatorNext) {
                            $translatorNext = preg_match('/^\((?P<string>(?P<translator>[^)]+) ' . $this->regExps->translatorRegExp . ')\)(?P<remainder>.*)/', $remainder, $matches);
                        }
                        if (! $translatorNext) {
                            $translatorNext = preg_match('/^\(?' . $this->regExps->translatedByRegExp . '$/', $nextWord . ' ' . $nextButOneWord);
                        }
                        if (isset($matches['string'])) {
                            if (preg_match('/^(' . $this->regExps->volumeRegExp . ')(?P<volume>[\dIVXL]+)[, ](?P<translator>.*?)' . $this->regExps->translatorRegExp . '$/', $matches['string'], $volumeMatches)) {
                                if (isset($volumeMatches['volume'])) {
                                    $volume = $volumeMatches['volume'];
                                }
                                if (isset($volumeMatches['translator'])) {
                                    $translator = trim($volumeMatches['translator']);
                                }
                            } else {
                                $editor = $matches['editor'] ?? '';
                                $translator = $matches['translator'] ?? '';
                            }
                            $upcomingBookVolume = false;
                            $remainder = trim($matches['remainder'], '. ');
                            $remainingWords = explode(' ', $remainder);
                        }
                    } elseif (preg_match('/^' . $this->regExps->translatedByRegExp . '$/', $nextWord . ' ' . $nextButOneWord)) {
                        // Extraction of translators' names handled separately.
                        $translatorNext = true;
                    } elseif (preg_match('/^' . $this->regExps->translatorRegExp . '$/', $nextWord)) {
                        // "trans. John Smith."
                        // Here trans must start with lowercase, because journal name might start with Trans. and period
                        // cannnot be preceded by uppercase letter (which would be initial of translator)
                        if (preg_match('/^' . $this->regExps->translatorRegExp . ' (?P<translator>[^.]+(?<!\p{Lu})\.)(?P<remainder>.*)/u', $remainder, $matches)) {
                            $translatorNext = true;
                            $translator = $matches['translator'];
                            $remainder = $matches['remainder'];
                        } elseif (preg_match('/^tr(ans)?\. (?P<translator>[^,]+), (?P<remainder>.{5,})/', $remainder, $matches)) {
                            $translatorNext = true;
                            $translator = $matches['translator'];
                            $remainder = $matches['remainder'];
                        }
                    } elseif (preg_match('/((?!^[^(]*\p{Ll}\..*)^(?P<translator>[\p{L}. ]*))\(' . $this->regExps->translatorRegExp . '\)(?P<remainder>.*)$/u', $remainder, $matches)) {
                        // Jessica J. Smith (trans.), ...
                        // string up to first ( does not contain lowercase letter followed by period and string matches letters, spaces,
                        // and periods followed by '(Trans.)' or similar then any characters
                        $translatorNext = true;
                        $translator = $matches['translator'];
                        $remainder = $matches['remainder'] ?? '';
                    }

                    if (
                        $this->containsFontStyle($remainder, true, 'italics', $startPos, $length)
                        || $upcomingJournalAndPubInfo
                        || $upcomingPageRange
                        || $translatorNext
                        // After stringToNextPeriod, there are only digits and punctuation for volume-number-page-year info
                        // Condition used to include $upcomingRoman, but that is too general --- e.g. conference title could begin
                        // with Roman numerals.
                        || (
                            (Str::endsWith(rtrim($word, "'\""), [',', '.']) || Str::startsWith(rtrim($nextWord, "'\""), ['(']))
                            &&
                            (
                                ($upcomingVolumePageYear && ! preg_match('/^[a-z]/', $remainder))
                                || 
                                $upcomingVolumeNumber 
                                || 
                                $upcomingArticlePubInfo 
                                || 
                                $upcomingBookVolume 
                                || 
                                $upcomingVolumeCount
                            )
                           )
                        || preg_match('/^\(?' . $this->regExps->workingPaperRegExp . '/u', $remainder)
                        || preg_match($this->regExps->startPagesRegExp, $remainder)
                        || preg_match('/^' . $this->regExps->inRegExp . ':? (`|``|\'|\'\'|"|' . $italicCodesRegExp . ')?([A-Z1-9]|' . $this->yearRegExp . ')/', $remainder)
                        || preg_match('/^(Journal |Annals |Proc(eedings)? |Bulletin )/', $remainder)
                        || preg_match('/^\(?(' . $this->regExps->volumeRegExp . ') /', $remainder)
                        || (
                            $nextWord 
                            &&
                            Str::endsWith($nextWord, '.') 
                            &&
                            in_array(substr($nextWord, 0, -1), $journalWordAbbreviations)
                            &&
                            ! ($nextWord == 'A.' && $nextButOneWord == 'D.')
                            &&
                            ! ($nextWord == 'B.' && $nextButOneWord == 'C.')
                           )
                        || (
                            $nextWord 
                            &&
                            $nextButOneWord 
                            &&
                            (Str::endsWith($nextWord, range('a', 'z')) || in_array($nextWord, ['IEEE', 'ACM'])) 
                            &&
                            Str::endsWith($nextButOneWord, '.') 
                            &&
                            in_array(substr($nextButOneWord, 0, -1), $journalWordAbbreviations)
                           )
                        // pages (e.g. within book)
                        || preg_match('/^\(?pp?\.? [0-9]/', $remainder)
                        || preg_match('/' . $this->startForthcomingRegExp . '/i', $remainder)
                        || preg_match('/^' . $this->yearRegExp . '[a-z]?(\.|$)/', $remainder)
                        // title (letters and spaces) and then editors in parens next
                        || preg_match('%^\p{Lu}[\p{L} ]+ \([\p{L}.& ]+, ' . $this->regExps->edsNoParensRegExp . '\)%u', $remainder)
                        // editor next
                        || preg_match('/^' . $this->regExps->edsOptionalParensRegExp . ' /', $remainder)
                        || preg_match('/^' . $this->regExps->editedByRegExp . ' /', $remainder)
                        // address [no spaces]: publisher in db
                        || (
                            preg_match('/^\p{Lu}\p{L}+: (?P<publisher>[\p{L} ]*),/u', $remainder, $matches) 
                            &&
                            in_array(trim($matches['publisher']), $publishers)
                           )
                        // address [city in db]: publisher
                        || (
                            preg_match('/^(?P<city>\p{Lu}[\p{L} ]+): /u', $remainder, $matches) 
                            &&
                            in_array(trim($matches['city']), $cities)
                           )
                        // one- or two-word publisher, address [city in db], <year>?
                        || (
                            preg_match('/^\p{Lu}\p{Ll}+( \p{Lu}\p{Ll}+)?, (?P<city>[\p{L}, ]+)(, ' . $this->yearRegExp . ')?$/u', $remainder, $matches) 
                            &&
                            in_array(trim($matches['city']), $cities)
                           )
                        // <publisher> (1 or 2 words), <city> (1 or 2 words), US State, <year>?
                        || preg_match('/^\p{Lu}\p{Ll}+( \p{Lu}\p{Ll}+)?, (?P<city>\p{Lu}\p{Ll}+( \p{Lu}\p{Ll}+)?, \p{Lu}{2})(, ' . $this->yearRegExp . ')?$/u', $remainder, $matches) 
                        // [,.] <address>: <publisher>(, <year>)?$ OR (<address>: <publisher>(, <year>)?)
                        // Note that ',' is allowed in address and '.' and '&' are allowed in publisher.
                        // May need to put a limit on length of publisher part?
                        || (
                            (Str::endsWith($word, '.') || Str::endsWith($word, ',') || $nextWord[0] == '(')
                            &&
                            $this->isAddressPublisher($remainder)
                           )
                        // <publisher>, <address>, <year>
                        || preg_match('/^(?P<publisher>[\p{L}&\\\ ]{5,20}), (?P<address>[\p{L} ]{5,15}), (?P<year>' . $this->yearRegExp . ')\.?$/u', $remainder, $matches) 
                        // <publisher>, <address>,? (<year>) OR <publisher>, <address>,? [<year>]
                        || preg_match('/^(?P<publisher>[\p{L}&\\\ ]{5,20}), (?P<address>[\p{L} ]{5,15}),? [(\[](?P<year>' . $this->yearRegExp . ')[)\]]\.?$/u', $remainder, $matches) 
                        // (<publisher> in db
                        || Str::startsWith(ltrim($remainder, '('), $publishers)
                        // (<city> in db
                        || Str::startsWith(ltrim($remainder, '('), $cities)
                        // Thesis
                        || preg_match('/^[(\[\-]? ?' . $this->regExps->fullThesisRegExp . '/', $remainder)
                        || preg_match('/^[(\[\-]? ?' . $this->regExps->thesisRegExp . '/', $remainder)
                        ) {
                        $this->verbose("Ending title, case 2 (word '" . $word . "')");
                        $title = rtrim(implode(' ', $initialWords), ',:;');
                        //$title = $this->fixFinalPeriod($title);
                        if (preg_match('/^Journal /', $remainder)) {
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
                if ($nextWord && $nextButOneWord && preg_match($volumeWithDigitRegExp, $nextWord . ' ' . $nextButOneWord, $matches)) {
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
                $editionRegExp = '/(^\(' . $this->regExps->editionRegExp . '\)|^' . $this->regExps->editionRegExp . ')(?P<remains>.*$)/iJu';
                if (preg_match($editionRegExp, $testString, $matches)) {
                    for ($i = 1; $i <= 11; $i++) {
                        if ($matches['n' . $i]) {
                            $edition = $matches['n' . $i];
                            $editionNumber = $i;
                            $fullEdition = $matches['fullEdition'];
                            break;
                        }
                    }

                    $this->verbose('edition set to "' . $edition . '"');
                    $fullEdition = $matches['fullEdition'];
                    $this->verbose("Ending title, case 3b");
                    $title = $includeEdition ? rtrim(implode(' ', $initialWords) . ' ' . $fullEdition, ' ,') : rtrim(implode(' ', $initialWords), ' ,');
                    $remainder = $matches['remains'];
                    break;
                }

                // If end of title has not been detected and word ends in period-equivalent or comma
                if (
                    (
                        Str::endsWith($word, ['.', '!', '?', ',']) 
                        && 
                        $parensLevel == 0 
                        && 
                        $bracketLevel == 0
                        &&
                        ! in_array($word, ['vol.', 'Vol.', 'v.'])
                        &&
                        ! preg_match('/^\p{Lu}\.$/u', $word)
                    )
                    ) {
                        $this->verbose('$stringToNextPeriodOrComma: ' . $stringToNextPeriodOrComma);
                        $this->verbose('$wordAfterNextCommaOrPeriod: ' . $wordAfterNextCommaOrPeriod);
                        $this->verbose('$stringToNextPeriod: ' . $stringToNextPeriod);
                    // if first character of next word is lowercase letter and does not end in period
                    // OR $word and $nextWord are A. and D. or B. and C.
                    // OR following string starts with a part designation,
                    // continue, skipping next word,
                    // s.l. (sine loco) is used for citation for which address of publisher is unknown
                    if (
                        $nextWord 
                        &&
                        (
                            (
                                ctype_alpha($nextWord[0])
                                &&
                                mb_strtolower($nextWord[0]) == $nextWord[0]
                                &&
                                substr($nextWord, -1) != '.'
                                &&
                                rtrim($nextWord, ':') != 'in'
                                &&
                                ! Str::startsWith($nextWord, 's.l.')
                                &&
                                ! Str::startsWith($nextWord, 'al-') // Could be start of name of Arabic publisher
                            )
                            || 
                            ($word == 'A.' && $nextWord == 'D.')
                            || 
                            ($word == 'B.' && $nextWord == 'C.')
                            ||
                            in_array($word, $titleAbbreviations)
                            || 
                            preg_match('/^(Part )?(I|II|III|[1-9])[:.] /', $remainder)
                        )
                        &&
                        ! ($nextWord == 'edited' && $nextButOneWord == 'by')
                    ) {
                        $this->verbose("Not ending title, case 1 (next word is " . $nextWord . ")");
                        //$skipNextWord = true;
                    } elseif (
                        preg_match('/' . $this->regExps->twoPartTitleAbbreviationsRegExp . '/', $word . ' ' . $remainder)
                        ||
                        // $word ends in period, next word starts with '(', and next character is letter
                        (substr($word, -1) == '.' && substr($nextWord, 0, 1) == '(' && ctype_alpha(substr($nextWord, 1, 2)))
                    ) {
                        $this->verbose("Not ending title, case 1a (word is " . $word . ")");
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
                            ! ($nextWord == 'A.' && $nextButOneWord == 'D.')
                            && 
                            ! ($nextWord == 'B.' && $nextButOneWord == 'C.')
                            && 
                            ! ($nextWord == 'B.' && $nextButOneWord == 'C.--A.') // '100 B. C.--A. D. 500'
                            && 
                            ! ($nextWord == 'C.--A.' && $nextButOneWord == 'D.')
                            && 
                            ! ($nextWord == 'D.' && preg_match('/^\d\d\d/', $nextButOneWord))
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
                            !in_array($word, $titleAbbreviations) 
                        ) {
                        $this->verbose("Ending title, case 4");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // elseif next sentence starts with a thesis designation, terminate title
                    } elseif (preg_match('/^[\(\[]' . $this->regExps->fullThesisRegExp . '[\)\]]/', $stringToNextPeriodOrComma)) {
                        $this->verbose("Ending title, case 4a");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // elseif next sentence contains word 'series', terminate title
                    } elseif (preg_match('/' . $this->regExps->seriesRegExp . '/', $stringToNextPeriodOrComma)) {
                        $this->verbose("Ending title, case 4b (next sentence contains a phrase indicating a 'series')");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        $seriesNext = true;
                        break;
                    } elseif (preg_match('/edited (and translated )?by/i', $remainder)) {
                        $this->verbose("Ending title, case 4c");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // else if string up to next period contains only letters, spaces, hyphens, (, ), \, ,, :, and
                    // quotation marks and doesn't start with "in"
                    // (which is unlikely to be within a title following punctuation)
                    // and is followed by at least 30 characters or 37 if it contains pages (for the publication info),
                    // assume it is part of the title,
                    // unless it ends in an uppercase letter followed by a period or is more than a single word 
                    // or is followed by "edited by"
                    } elseif (
                        (
                            preg_match('/^[\p{L}0-9 \-\(\)`"\':,\/]+$/u', substr($stringToNextPeriodOrComma, 0, -1))
                            //preg_match('/[a-zA-Z -]+/', substr($stringToNextPeriodOrComma,0,-1))
                            && 
                            ! preg_match('/^' . $this->regExps->inRegExp . ':? /', $remainder)
                            && 
                            ! $this->isProceedings($remainder)
                            && 
                            strlen($remainder) > strlen($stringToNextPeriodOrComma) + ($containsPages ? 37 : 30)
                            && 
                            ! $upcomingYear
                            && 
                            ! preg_match('/^' . $this->regExps->journalRegExp . '[,. ]/u', $remainder)
                            // && 
                            // (
                            //     preg_match('/\p{Lu}[,.]$/u', $stringToNextPeriodOrComma) // $stringToNextCommaOrPeriod is X. or X, for any letter X
                            //     || 
                            //     strpos($stringToNextPeriodOrComma, ' ') !== false // $stringToNextPeriodOrComma is more than a single word
                            // )
                        )
                        ||
                        // After next word, "edited by" phrase occurs
                        preg_match('%^' . $this->regExps->editedByRegExp . '%u', ltrim(substr($remainder, strpos($remainder, ' ') ?: 0)))
                        ) {
                        $this->verbose("Not ending title, case 2 (next word is '" . $nextWord . "', and string to next period or comma is '" . $stringToNextPeriodOrComma . "')");
                    // else if working paper string occurs later in remainder,
                    } elseif (preg_match('/(.*)(' . $this->regExps->workingPaperRegExp . ')/u', $remainder, $matches)) {
                        // if no intervening punctuation, end title
                        if (!Str::contains($matches[1], ['.', ',', ':'])) {
                            $this->verbose("Ending title, case 5");
                            $title = rtrim(Str::before($originalRemainder, $matches[0]), ', ');
                            break;
                        // otherwise keep going
                        } else {
                            $this->verbose("Not ending title, case 3 (working paper string is coming up)");
                        }
                    // else if there has been no period so far and italics is coming up, 
                    // wait for the italics (journal name?)
                    } elseif ($this->containsFontStyle($remainder, false, 'italics', $startPos, $length)) {
                        $this->verbose("Not ending title, case 4 (italics is coming up)");
                    // else if word ends with comma and remainder doesn't start with "\p{Ll}+ journal "
                    // and volume info is coming up, wait for it
                    } elseif (Str::endsWith($word, [',']) && preg_match('/^\p{L}+ journal/iu', $remainder)) {
                        $this->verbose("Ending title, case 5a (word: \"" . $word . "\"; journal info is next)");
                        $title = rtrim(implode(' ', $initialWords), ' ,');
                        break;
                    // } elseif (Str::endsWith($word, [',']) && preg_match('/' . $volumeAndCodesRegExp . '/', $remainder)) {
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
                        // } elseif (Str::endsWith($word, [',']) && preg_match('/\p{Lu}\p{Ll}+, \p{Lu}\. /u', $remainder)) {
                        //     $this->verbose("Ending title, case 6a (word '" . $word ."')");
                        //     $title = rtrim(implode(' ', $initialWords), '.,');
                        //     break;
                        } elseif (Str::endsWith($word, [','])) {
                            $this->verbose("Not ending title, case 7a (word '" . $word ."')");
                        } elseif (in_array(rtrim($wordAfterNextCommaOrPeriod, '.'), $journalWordAbbreviations)) {
                            // Word after next comma or period is a start journal abbreviation
                            $this->verbose("Not ending title, case 7b");
                        } elseif (
                            isset($remainderFollowingNextPeriodOrComma) 
                            && 
                            preg_match('/^\p{Lu}\p{Ll}+: \p{Lu}\p{Ll}+$/u', trim($remainderFollowingNextPeriodOrComma, '. '))
                            ) {
                            // one-word address: one-word publisher follow next period.  (Could intervening sentence be series in this case?)
                            $this->verbose("Not ending title, case 7c (word '" . $word ."'): <address>: <publisher> follow next comma or period");
                        } elseif (
                            isset($remainderFollowingNextPeriod) 
                            &&
                            strlen($stringToNextPeriod) > 5 
                            &&
                            preg_match('/\p{Ll}[.,]$/u', $stringToNextPeriod) 
                            &&
                            // $stringToNextPeriod is not title of Proceedings
                            ! preg_match('/(^| )[Cc]onference[., ]/', $stringToNextPeriod)
                            &&
                            ! Str::endsWith($stringToNextPeriod, ['Univ.']) 
                            //&& preg_match('/^[\p{L}., ]+: [\p{L}&\- ]+$/u', trim($remainderFollowingNextPeriod, '. '))
                            //&& preg_match('/^' . $this->addressPublisherRegExp . '$/u', trim($remainderFollowingNextPeriod, '. '))
                            &&
                            $this->isAddressPublisher(trim($remainderFollowingNextPeriod, '. '), allowYear: false)
                            ) {
                            // <address>: <publisher> follows string to next period: If string to next period
                            // (note: comma not allowed, because comma may appear in address --- New York, NY)
                            // has at least 6 characters and a lowercase letter preceded the punctuation,
                            // allow spaces and periods (and any utf8 letter) in the <address> 
                            $this->verbose("Not ending title, case 7d (word '" . $word ."'): <address>: <publisher> follow next comma or period");
                        } elseif (
                            substr($nextWord, -1) == '.' 
                            && 
                            ! in_array($nextWord, $journalWordAbbreviations) 
                            &&
                            ! in_array($nextButOneWord[0], range(0,9))
                            ) {
                            $this->verbose("Not ending title, case 7e (next word, '" . $nextWord . "', ends in period and is not a journal word abbreviation, and following word does not start with a digit)");
                        } else {
                            // otherwise assume the punctuation ends the title.
                            $this->verbose("Ending title, case 6b (word '" . $word ."')");
                            $title = rtrim(implode(' ', $initialWords), ',');
                            break;
                        }
                    }
                } elseif (Str::endsWith($word, [':'])) {
                    // Journal name (spaces and letters), volume-number-page info
                    if (preg_match('/^\p{Lu}[\p{L} ]{4,30}, (' . $this->regExps->volumeAndCodesRegExp . ')? ?[0-9IVXLC]+}?(, |: | )([(\[]?' . $this->yearRegExp . '[)\]]?,? ?)?(' . $this->regExps->numberRegExp . ')?[A-Z]?[0-9\/\-]{0,4}\)?,? ?' . $this->regExps->pageRegExp . '\.? ?$/u', $remainder)) {
                        $upcomingJournalAndPubInfo = true;
                        $isArticle = true;
                        $this->verbose('Followed by journal name and publication info, so classified as article');
                        $this->verbose("Ending title, case 7 (word '" . $word ."')");
                        $title = rtrim(implode(' ', $initialWords), ',');
                        break;
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

        $title = str_replace(['A. D.', 'B. C.'], ['A.D.', 'B.C.'], $title);

        $result['title'] = $title;
        $result['titleDetails'] = $this->titleDetails;
        $result['seriesNext'] = $seriesNext;
        $result['stringToNextPeriodOrComma'] = $stringToNextPeriodOrComma ?? '';
        $result['editor'] = $editor;
        $result['translator'] = $translator;
        $result['editionNumber'] = $editionNumber ?? null;
        $result['fullEdition'] = $fullEdition ?? null;

        return $result;
    }

    /*
     * Get title from a string that starts with title and then has authors (e.g. editors, in <booktitle> <editor> format)
     */
    public function getTitleAndEditor(string &$remainder, string $language = 'en'): array
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
                    $this->titleDetails = array_merge($this->titleDetails, $nameStringResult['details']);
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