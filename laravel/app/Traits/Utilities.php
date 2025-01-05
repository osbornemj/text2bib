<?php
namespace App\Traits;

use stdClass;

use Str;

use PhpSpellcheck\Spellchecker\Aspell;

trait Utilities
{
    // Codes are ended by } EXCEPT \em, \it, and \sl, which have to be ended by something like \normalfont.  Code
    // that gets italic text handles only the cases in which } ends italics.
    // \enquote{ is not a signal of italics, but it is easiest to classify it thus.
    var $italicCodes = [
        "\\textit{", 
        "\\textit {", 
        "\\textsl{", 
        "\\textsl {", 
        "\\textsc{", 
        "\\textsc {", 
        "\\emph{", 
        "\\emph {", 
        "{\\em ", 
        "\\em ", 
        "{\\it ", 
        "\\it ", 
        "{\\sl ", 
        "\\sl ", 
        "\\enquote{"
    ];

    var $boldCodes = [
        "\\textbf{", 
        "{\\bf ", 
        "{\\bfseries "
    ];

    var $articleRegExp = 'art(icle|\.) (id |no\.? ?)?[0-9]*';

    var $yearRegExp = '(18|19|20)[0-9]{2}';

    var $forthcomingRegExp = '[Ff]orthcoming( at| in)?|[Ii]n [Pp]ress|[Aa]ccepted( at)?|[Tt]o [Aa]ppear [Ii]n|à paraître';
    var $endForthcomingRegExp = '( |\()([Ff]orthcoming|[Ii]n [Pp]ress|[Aa]ccepted|[Tt]o [Aa]ppear|à paraître)\.?\)?$';
    var $startForthcomingRegExp = '^\(?forthcoming( at| in)?\)?|^in press|^accepted( at)?|^to appear in|^à paraître';

    // (°|º) cannot be replaced by [°º].  Don't know why.
    // Note that "Issues" cannot be followed by "in" --- because "issues in" could be part of journal name
    var $numberRegExp = '[Nn][Oo]s? ?\.?:? ?|[Nn]úm\.:? ?|[Nn]umbers? ?|[Nn] ?\. |№\.? ?|[Nn]\.? ?(°|º) ?|[Ii]ssues?:? ?(?! in)|Issue no. ?|Iss: |Heft ';

    var $editionRegExp = '(?P<fullEdition>((?P<edition>(1st|first|2nd|second|3rd|third|[4-9]th|[1-9][0-9]th|fourth|fifth|sixth|seventh|[12][0-9]{3}|revised) (rev\.|revised )?)(ed\.|edition|vydání|édition|edición|edição|editie))|[1-9] ?ed\.)';

    var $workingPaperRegExp = '(preprint|arXiv preprint|bioRxiv|working paper|texto para discussão|discussion paper|'
        . 'technical report|tech\. report|report no\.|'
        . 'research paper|mimeo|unpublished paper|unpublished manuscript|manuscript|'
        . 'under review|submitted|in preparation)';
    // Working paper number can contain letters and dashes, but must contain at least one digit
    // (otherwise word following "manuscript" will be matched, for example)
    var $workingPaperNumberRegExp = ' (\\\\#|number|no\.?)? ?(?=.*[0-9])([a-zA-Z0-9\-]+),?';

    var $proceedingsRegExp = '(^proceedings of |proceedings of the (.*) (conference|congress)|conference|conferencia| symposium | meeting |congress of the | world congress|congreso|^proc\.| workshop|^actas del | scientific assembly of the )';
    var $proceedingsExceptions = '^Proceedings of the American Mathematical Society|^Proceedings of the VLDB Endowment|^Proceedings of the AMS|^Proceedings of the National Academy|^Proc\.? Natl?\.? Acad|^Proc\.? Amer\.? Math|^Proc\.? National Acad|^Proceedings of the \p{L}+ (\p{L}+ )?Society|^Proc\.? R\.? Soc\.?|^Proc\.? Roy\.? Soc\.? A|^Proc\.? Roy\.? Soc\.?|^Proc\. Camb\. Phil\. Soc\.|^Proceedings of the International Association of Hydrological Sciences|^Proc\.? IEEE(?! [a-zA-Z])|^Proceedings of the IEEE (?!(International )?(Conference|Congress))|^Proceedings of the IRE|^Proc\.? Inst\.? Mech\.? Eng\.?|^Proceedings of the American Academy|^Proceedings of the American Catholic|^Carnegie-Rochester conference';

    var $detailLines;
    
    public function isInitials(string $word): bool
    {
        $patterns = [
            '\p{Lu}\.?\.?',             // A or A. or A..
            '\p{Lu}\.\p{Lu}\.',         // A.B.
            '\p{Lu}\p{Lu}',             // AB
            '\p{Lu}\.\p{Lu}\.\p{Lu}\.', // A.B.C.
            '\p{Lu}\p{Lu}\p{Lu}',       // ABC
            '\p{Lu}\.?-\p{Lu}\.',       // A.-B. or A-B.
        ];

        $case = 0;
        foreach ($patterns as $i => $pattern) {
            if (preg_match('/^' . $pattern . '$/u', $word)) {
                $case = $i+1;
                $this->verbose("isInitials case " . $case);
                break;
            }    
        }

        return $case > 0;
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
            $this->setField($item, $fieldName, (isset($item->$fieldName) ? $item->$fieldName . ' ' : '') . $string, $id); 
        }
    }

    private function verbose(string|array $arg): void
    {
        $this->detailLines[] = $arg;
    }

    /**
     * isEd: determine if string is 'Eds.' or 'Editors' (or with parens or brackets) or
     * singular version of one of these, with possible trailig . or ,
     * @param $string string
     * @return 0 if no match, 1 if singular form is matched, 2 if plural form is matched
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
  
    private function isAnd(string $string, $language = 'en'): bool
    {
        // 'with' is allowed to cover lists of authors like Smith, J. with Jones, A.
        //return mb_strtolower($string) == $this->phrases[$language]['and'] || in_array($string, $this->andWords) || $string == 'with';
        return in_array(mb_strtolower($string), $this->andWords) || $string == 'with';
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

    /*
     * Determine whether $word is in the dictionary and not in the list of names in the dictionary
     */
    private function inDict(string $word, array $dictionaryNames, bool $lowercaseOnly = true): bool
    {
        $aspell = Aspell::create();
        // strtolower to exclude proper names (e.g. Federico is in dictionary)
        $inDict = 0 == iterator_count($aspell->check($lowercaseOnly ? strtolower($word) : $word));
        $notName = ! in_array($word, $dictionaryNames);
        return $inDict && $notName;
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

        /**
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
                    } elseif (
                        // if pattern is [a-z]`[a-z] then ` is not an opening quote, but an accent in Arabic
                        ! isset($chars[$i-1])
                        ||
                        (
                            $chars[$i-1] != '\\' 
                            &&
                            isset($chars[$i+1])
                            &&
                            ! (in_array($chars[$i-1], range('a', 'z')) && in_array($chars[$i+1], range('a', 'z')))
                        )
                        ) {
                        $level++;
                        if ($begin) {
                            $quotedText .= $char;
                        } else {
                            $begin = '`';
                        }
                    } else {
                        if (in_array($chars[$i-1], range('a', 'z')) && isset($chars[$i+1]) && $chars[$i+1] == 's') {
                            $char = "'";
                        }
                        if ($begin) {
                            $quotedText .= $char;
                        } else {
                            $beforeQuote .= $char;
                        }
                    }
                } elseif ($char == "'") {
                    // ''
                    if (($i == 0 || $chars[$i-1] != '\\') && isset($chars[$i+1]) && $chars[$i+1] == "'") {
                        if (isset($chars[$i+2]) && $chars[$i+2] == "'") {
                            // '''
                            if ($begin == "''" || $begin == "``") {
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
                            $level--;
                            if ($level == 0) {
                                $end = true;
                                $skip = 1;
                            } else {
                                $quotedText .= $char;
                                $quotedText .= $chars[$i+1];
                                $skip = 1;
                            }
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
                    // (so followed by a space or punctuation)
                    } elseif (
                            ! isset($chars[$i+1]) 
                            ||
                            (
                                $chars[$i+1] != "'" 
                                &&
                                ! preg_match('/^[\p{L}\\\]$/u', $chars[$i+1])
                                &&
                                (
                                    ! isset($chars[$i+2])
                                    ||
                                    // e.g. "[elephant]s' s[ize]"
                                    ! preg_match('/^s\' \p{Ll}$/u', $chars[$i-1] . $chars[$i] . $chars[$i+1] . $chars[$i+2])
                                )
                            )
                        ) {
                        if ($begin == "'" || $begin == "`") {
                            $level--;
                            $end = $level ? false : true;
                            if (! $end) {
                                $quotedText .= $char;
                            }
                        } elseif ($begin && $level == 1) {
                            // ' cannot end a nested quote because $level == 1
                            $quotedText .= $char;
                        } elseif ($begin) {
                            // ' might end a nested quote if that quote started with ' or `.  To be sure
                            // it would be necessary to keep track of the starting characters of all the
                            // nested quotes.
                            $level--;
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

        $style = $matchedText ? $style : 'none';

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
            if ($startPos !== false && (($start && $startPos == 0) || ! $start)) {
                return true;
            }
        }
        return false;
    }

    private function removeFontStyle(string $string, string $style): string
    {
        if ($style == 'italics') {
            $codes = $this->italicCodes;
        } elseif ($style == 'bold') {
            $codes = $this->boldCodes;
        }
        foreach ($codes as $code) {
            $string = str_replace($code, '', $string);
        }

        $string = str_replace('}', '', $string);

        return $string;
    }

    private function isAddressPublisher(string $string, bool $start = true, bool $finish = true, bool $allowYear = true): bool
    {
        $returner = false;
        $begin = $start ? '/^' : '/';
        $end = $finish ? '$/u' : '/u';

        $addressPublisherRegExp = '(?P<address>[\p{L},. ]{0,25}): ?(?P<publisher>[\p{L}&\-.,\' ]{0,50})';

        if ($allowYear) {
            // permit "?" after year (e.g. "[2023?]").
            $match = preg_match($begin . '\(?' . $addressPublisherRegExp . '(, [(\[]?(?P<year>' . $this->yearRegExp . ')\??[)\]]?)?\)?' . $end, $string, $matches);
        } else {
            $match = preg_match($begin . '\(?' . $addressPublisherRegExp . '\)?' . $end, $string, $matches);
        }

        if ($match) {
            $returner = true;
            $addressPublisherRegExp = $matches['address'] . ' ' . $matches['publisher'];
            $words = explode(' ', $addressPublisherRegExp);
            foreach ($words as $word) {
                if (substr($word, -1) == '.') {
                    if (! in_array($word, ['St.', 'Inc.']) && ! preg_match('/^[A-Z]\.$/', $word)) {
                        $returner = false;
                        break;
                    }
                }
            }
        }

        return $returner;
    }

    /*
     * Split an array of words into sentences.  Each period that does not follow a single uc letter 
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

    /*
     * Return the substring of $string before all of the characters in $chars
     */
    private function stringBefore(string $string, array $chars): string
    {
        $pos = strlen($string);
        foreach ($chars as $char) {
            $p = strpos($string, $char);
            if ($p !== false) {
                $pos = min($pos, $p);
            }
        }

        $substring = substr($string, 0, $pos);

        return $substring;
    }

    /* 
     * Does $string start with US State abbreviation, possibly preceded by ', '?
     */
    private function getUsState(string $string): string|bool
    {
        if (preg_match('/^(,? ?[A-Z]\.? ?[A-Z]\.?)[,.: ]/', $string, $matches)) {
            return $matches[1];
        }
        
        return false;
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

    /*
     * Truncate $string at first '%' that is not preceded by '\'.  Return true if truncated, false if not.
     */
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

    // Report whether $string is the start of the name of the proceedings of a conference
    private function isProceedings(string $string): bool
    {
        $isProceedings = false;

        foreach ($this->italicCodes as $code) {
            $string = Str::replaceStart($code, '', $string);
        }

        if (preg_match('/' . $this->proceedingsRegExp . '/i', $string)
                && ! preg_match('/' . $this->proceedingsExceptions . '/iu', $string)) {
            $isProceedings = true;
        }

        return $isProceedings;
    }

}
