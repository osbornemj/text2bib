<?php
namespace App\Traits;

use stdClass;

use PhpSpellcheck\Spellchecker\Aspell;

use App\Models\City;
use App\Models\DictionaryName;

trait Utilities
{
    var $phrases = [
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
            'edited by' => 'editoval'
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

    // Codes are ended by } EXCEPT \em, \it, and \sl, which have to be ended by something like \normalfont.  Code
    // that gets italic text handles only the cases in which } ends italics.
    // \enquote{ is not a signal of italics, but it is easiest to classify it thus.
    var $italicCodes = [
        "\\textit{", 
        "\\textsl{", 
        "\\textsc{", 
        "\\emph{", 
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

    // The script will identify strings as cities and publishers even if they are not in these arrays---but the
    // presence of a string in one of the arrays helps when the elements of the reference are not styled in any way.
    private function getCities(): array
    {
        $cities = City::where('distinctive', 1)
            ->where('checked', 1)
            ->orderByRaw('CHAR_LENGTH(name) DESC')
            ->pluck('name')
            ->toArray();

        return $cities;
    }

    private function getDictionaryNames()
    {
        // Words that are in dictionary but are names
        return DictionaryName::all()->pluck('word')->toArray();
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
        return mb_strtolower($string) == $this->phrases[$language]['and'] || in_array($string, $this->andWords) || $string == 'with';
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
    private function inDict(string $word, bool $lowercaseOnly = true): bool
    {
        $aspell = Aspell::create();
        // strtolower to exclude proper names (e.g. Federico is in dictionary)
        $inDict = 0 == iterator_count($aspell->check($lowercaseOnly ? strtolower($word) : $word));
        $notName = ! in_array($word, $this->getDictionaryNames());
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
                      } elseif (
                          // if pattern is [a-z]`s, assume the ` is a typo for '
                          ! isset($chars[$i-1])
                          ||
                          ($chars[$i-1] != '\\' && ! (in_array($chars[$i-1], range('a', 'z')) && $chars[$i+1] == 's'))
                         ) {
                          $level++;
                          if ($begin) {
                              $quotedText .= $char;
                          } else {
                              $begin = '`';
                          }
                      } else {
                          if (in_array($chars[$i-1], range('a', 'z')) && $chars[$i+1] == 's') {
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

    // Does $string start with US State abbreviation, possibly preceded by ', '?
    private function getUsState(string $string): string|bool
    {
        if (preg_match('/^(,? ?[A-Z]\.? ?[A-Z]\.?)[,.: ]/', $string, $matches)) {
            return $matches[1];
        }
        
        return false;
    }

}
