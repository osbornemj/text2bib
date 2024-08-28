<?php
namespace App\Traits;

trait AuthorPatterns
{
    /* AUTHOR PATTERNS
    * 
    * If end2 = end3 = null:
    * name1 end1 (name2 end1)*
    * 
    * If end3 = null:
    * name1 end1 (name2 end1)* name2 end2
    * 
    * Otherwise:
    * name1 end1 (name2 end1)* name2 end2 name2 end3
    * 
    * name2 == name1 unless explicitly specified, so if name2 is not explicitly specified, pattern is
    * (name1 end1)* name1 end2 name1 end3
    * 
    * Precisely:
    * $name1 = $r['name1'];
    * $name2 = $r['name2'] ?? $name1;
    * 
    * $regExp = '%^(?P<firstAuthor>' . $name1 . ')' . $r['end1']; 
    * if ($r['end2']) {
    *      $regExp .= '(?P<middleAuthors>(' . $name2 . $r['end1'] . ')*)';
    *      $regExp .= '(?P<penultimateAuthor>' . $name2 . ')' . $r['end2'];
    * }
    * if ($r['end3']) {
    *      $regExp .= '(?P<lastAuthor>' . $name2 . ')' . $r['end3'];
    * }
    * $regExp .= '(?P<remainder>.*)%u';
    * 
    * (Notice that first name can have different format from that of following names, to accommodate author strings like
    * Smith, A., B. Jones, and C. Gonzalez.)
    * 
    * 'initials' => true means treat string of u.c. letters as initials if length is at most 4 (rather than default 2)
    */
    private function authorPatterns(): array
    {
        $vonNameRegExp = '(';
        foreach ($this->vonNames as $i => $vonName) {
            $vonNameRegExp .= ($i ? '|' : '') . $vonName;
        }
        $vonNameRegExp .= ')';

        $andRegExp = '(';
        foreach ($this->andWords as $i => $andWord) {
            if ($andWord == '\&') {
                $andWord = '\\\&';
            } elseif ($andWord == '$\&$') {
                $andWord = '\$\\\&\$';
            }
            $andRegExp .= ($i ? '|' : '') . $andWord;
        }
        $andRegExp .= ')';

        // Last name has to start with uppercase letter
        //$lastNameRegExp = '(' . $vonNameRegExp . ' )?\p{Lu}[\p{L}\-\']+';
        // Following allows double-barelled last name with space; doesn't produce any errors in Examples, and seems OK
        // as long as none of the patterns end with simply a space.
        $lastNameRegExp = '(' . $vonNameRegExp . ' )?(' . $vonNameRegExp . ' )?\p{Lu}[\p{L}\-\']+( \p{Lu}[\p{L}\-\']+)?';
        // Other name (string before first space) has to start with uppercase letter and include at least one lowercase letter
        $otherNameRegExp = '(?=[^ ]*\p{Ll})\p{Lu}[\p{L}\-\']+';
        // Uppercase name
        $ucNameRegExp = '\p{Lu}+( \p{Lu}+)?';
        $initialRegExp = '(\p{Lu}\.?|\p{Lu}\.?-\p{Lu}\.?)';
        $initialPeriodRegExp = '(\p{Lu}\.|\p{Lu}\.-\p{Lu}\.)';

        // Spaces between initials are added before an item is processed, so "A.B." doesn't need to be matched
        $initialsLastName = '(' . $initialRegExp . ' ){1,3}' . $lastNameRegExp;
        $lastNameInitials = $lastNameRegExp . ',?( ' . $initialRegExp . '){1,4}';
        $lastNameInitialsPeriod = $lastNameRegExp . ',?( ' . $initialPeriodRegExp . '){1,4}';
        $firstNameInitialsLastName = $otherNameRegExp . ' (' . $initialRegExp . ' )?' . $lastNameRegExp;
        $lastNameFirstNameInitials = $lastNameRegExp . ', ' . $otherNameRegExp . '( ' . $initialRegExp . ')?';

        $notJr = '(?!(';
        foreach ($this->nameSuffixes as $i => $nameSuffix) {
            $notJr .= ($i ? '|' : '') . $nameSuffix;
        };
        $notJr .= '))';

        $notAnd = '(?!' . $andRegExp . ')';

        // When used for Editors, ending with colon can be a problem if it is used in address: publisher
        // Having '(' as a terminator is in appropriate for an entry like 
        // "Douglas Hedley, The Iconic Imagination (New York: Bloomsbury, 2016)."
        // because "The Iconic Imagination" gets interpreted as a second author.
        //$commaYear = ',? (?=[(\[`"\'\d])';
        $commaYear = ',? (?=(([(\]](\d|ed|tran))|[`"\'\d]))';
        // Requirement of lowercase after the first word is to avoid terminating an author string like the following one
        // too early (after Hamilton SA):
        // George JN, Raskob GE, Shah SR, Rizvi MA, Hamilton SA, Osborne S and Vondracek T:
        $commaYearOrBareWords = '(' . $commaYear . '|,? (?=(\p{Lu}[\p{L}\-]*( [\p{Ll}\-]+){3})))';
        //$commaYearOrBareWords = '(' . $commaYear . '|, (?=(\p{Lu}[\p{L}\-]*(( [\p{Ll}\-]+){3}|( [\p{L}\-]+){4}))))';
        $colonOrCommaYear = '(: |' . $commaYear . ')';
        $colonOrCommaYearOrBareWords = '(: |' . $commaYearOrBareWords . ')';
        $periodOrColonOrCommaYear = '(\. |: |' . $commaYear . ')';
        $periodOrColonOrCommaYearOrBareWords = '(\. |: |; |' . $commaYearOrBareWords . ')';
        $periodOrColonOrCommaYearOrCommaNotJr = '(\. |: |; |' . $commaYearOrBareWords . '|, ' . $notJr . ')';
        $periodNotAndOrColonOrCommaYear = '(\.,? ' . $notAnd . '|: |' . $commaYear . ')';
        $periodNotAndOrCommaYear = '(\. ' . $notAnd . '|' . $commaYear . ')';
        // 'and' includes 'et', so $notAnd covers 'et al' also
        $periodOrColonOrCommaYearOrCommaNotInitialNotAnd = '(\. |: |' . $commaYear . '|, (?!' . $initialRegExp . ' )' .  $notAnd . ')';

        $authorRegExps = [

            /////////////////////////
            // THREE OR MORE NAMES //
            /////////////////////////

            // 0. Smith,? AB, Jones,? CD, Gonzalez,? JD(: |\. | followed by <paren> or <quote> or <digit>)
            [
                'name1' => $lastNameRegExp . ',? \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ', ' . $notAnd, 
                'end3' => $periodOrColonOrCommaYear,
//                'end3' => '(: |\. | (?=[\(\[`"\'\d]))', 
                'initials' => true
            ],
            // 1. Smith,? (A.|AB), Jones,? (C.|CD), Gonzalez,? (J.|JD)(: |\. | followed by <paren> or <quote> or <digit> or 4 bare words)
            [
                'name1' => $lastNameRegExp . ',? (\p{Lu}\.|\p{Lu}{2,3})', 
                'end1' => ', ', 
                'end2' => ', ' . $notAnd, 
                'end3' => $periodOrColonOrCommaYearOrBareWords, 
                'initials' => true
            ],
            // 2. Smith AB, Jones CD,? and Gonzalez JD(: |\. |, | followed by <paren> or <quote> or <digit>)
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ',? ' . $andRegExp . ' ', 
                //'end3' => '(: |\. |, | (?=[\(\[`"\'\d]))',
                'end3' => $periodOrColonOrCommaYearOrCommaNotJr,  
                'initials' => true
            ],
            // 3. Smith AB, Jones CD, Gonzalez JD, et al.
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ', ', 
                'end2' => ', ' . $notAnd, 
                'end3' => ',? et\.? al\.?', 
                'initials' => true,
                'etal' => true,
            ],
            // 4. Smith A B, Jones C D, Gonzalez J D, et al.
            [
                'name1' => $lastNameInitials, 
                'end1' => ', ', 
                'end2' => ', ' . $notAnd, 
                'end3' => ',? et\.? al\.?', 
                'initials' => true,
                'etal' => true,
            ],
            // 5. Smith, A. B.[,;] Jones, C. D.; Gonzalez, J. D.[.,] <not initial>
            // Exclusions in end3 are to prevent the first part of a list of authors matching
            // when the rest does not because of a typo in the punctuation.
            [
                'name1' => $lastNameInitials, 
                'end1' => '[;,] ', 
                'end2' => '; ' . $notAnd, 
                'end3' => '[,.] (?!(\p{Lu}\.|;|' . $lastNameInitials . '[; ]))', 
            ],
            // 6. Smith, A. B.[,;] Jones, C. D.[,;] Gonzalez, J. D.:
            [
                'name1' => $lastNameInitials, 
                'end1' => '[;,] ', 
                'end2' => '[;,] ' . $notAnd, 
                'end3' => ': ', 
            ],
            // 7. Smith,? A. B., Jones,? C. D., and Gonzalez,? J. D.(, |\. | )
            // Space is allowed at end only if preceded by period, to avoid matching last author name as
            // Gonzalez, J. A.
            // for the string ... Gonzalez, J. A theory of ...
            [
                'name1' => $lastNameInitials, 
                'end1' => ', ' . $notAnd, 
                'end2' => ',? ' . $andRegExp . ' ', 
                'end3' => '(, |\. |(?<=\.) )', 
            ],
            // 8. A. B. Smith, C. D. Jones,? and J. D. Gonzalez[\., ]
            [
                'name1' => $initialsLastName, 
                'end1' => ', ' . $notAnd, 
                'end2' => ',? ' . $andRegExp . ' ', 
                //'end3' => '(\. |,? ' . $notJr . ')',
                'end3' => $periodOrColonOrCommaYearOrCommaNotJr, 
            ],
            // 9. (A. B. Smith, )*C. D. Jones(\.|:|, <year>) 
            [
                'name1' => $initialsLastName, 
                'end1' => '[,;] ' . $notAnd, 
                //'end2' => $periodOrColonOrCommaYearOrBareWords, 
                'end2' => $periodOrColonOrCommaYearOrCommaNotInitialNotAnd, 
                'end3' => null, 
            ],
            // 10. Smith, A. B., C. D. Jones,? and J. D. Gonzalez[\., ]
            [
                'name1' => $lastNameInitials, 
                'name2' => $initialsLastName, 
                'end1' => ', ' . $notAnd, 
                'end2' => ',? ' . $andRegExp . ' ', 
                //'end3' => '(\. |; |, ' . $notJr . '| \()', // can't be simply space, because then if last author has two-word last name, only first is included
                'end3' => $periodOrColonOrCommaYearOrCommaNotJr, 
            ],
            // 11. Jane (A. )?Smith, Susan (B. )?Jones, Hilda (C. )?Gonzalez. 
            [
                'name1' => $firstNameInitialsLastName, 
                'end1' => '[,;] ' . $notAnd, 
                'end2' => '[,;] ' . $notAnd, 
                'end3' => $periodOrColonOrCommaYearOrBareWords, 
            ],
            // 12. SMITH Jane, JONES Susan, GONZALEZ Hilda.
            [
                'name1' => $ucNameRegExp . ',? ' . $otherNameRegExp, 
                'end1' => '[,;] ' . $notAnd, 
                'end2' => '[,;] ' . $notAnd, 
                'end3' => $periodOrColonOrCommaYearOrBareWords, 
            ],
            // 13. Jane (A. )?Smith, Susan (B. )?Jones,? and Hilda (C. )?Gonzalez[.,;] 
            [
                'name1' => $firstNameInitialsLastName, 
                'end1' => ', ' . $notAnd, 
                'end2' => ',? ' . $andRegExp . ' ',
                //'end3' => '[\.,;] ' . $notJr, 
                'end3' => $periodOrColonOrCommaYearOrCommaNotJr, 
            ],
            // 14. Smith, Jane( J\.?)?, Susan( K\.?)? Jones,? and Jill( L\.?)? Gonzalez[,.] 
            [
                'name1' => $lastNameFirstNameInitials,
                'name2' => $firstNameInitialsLastName,
                'end1' => ', ' . $notAnd, 
                'end2' => ',? ' . $andRegExp . ' ',
                //'end3' => '[\.,;]? ' . $notJr,
                'end3' => $periodOrColonOrCommaYearOrCommaNotJr, 
            ],
            // 15. (Smith, J. A.(, |/))*Jones, A. B.,? <followed by>[\(|\[|`|\'|"|\d]
            // Allow authors to be separated by '/' rather than ', '.  (Could be allowed for other patterns too.)
            [
                'name1' => $lastNameInitials, 
                'end1' => '(, |\/)', 
                'end2' => $commaYear, 
                'end3' => null, 
            ],
            // 16. Smith, Jane( J\.?)?[,;] Jones, Susan( K\.?)?[,;] Gonzalez, Jill( L\.?)? (year) 
            [
                'name1' => $lastNameFirstNameInitials,
                'end1' => '[,;] ' . $notAnd, 
                'end2' => '[,;] ' . $notAnd,
                'end3' => $periodNotAndOrCommaYear,
            ],

            ///////////////
            // TWO NAMES //
            ///////////////
            
            // 17. Smith AB, Jones CD[:\.]
            [
                'name1' => $lastNameRegExp . ',? \p{Lu}{1,3}',
                'end1' => ', ', 
                //'end2' => '(: |\. ' . $notAnd . ')', 
                'end2' => $periodNotAndOrColonOrCommaYear,
                'end3' => null, 
                'initials' => true
            ],
            // 18. Smith A. B.[,;] Jones C. D.:
            [
                'name1' => $lastNameInitials,
                'end1' => '[,;] ', 
                //'end2' => '(: | (?=([\(\[`"\'\d])))', 
                'end2' => $colonOrCommaYear,
                'end3' => null, 
            ],
            // 19. Smith A. B.[,;] Jones C. D.(: | followed by <paren> or <quote> or <digit> or 4 bare words (no punctuation)
            [
                'name1' => $lastNameInitialsPeriod,
                'end1' => '[,;] ', 
                //'end2' => '(: | (?=([\(\[`"\'\d]|([\p{L}\-]+ ){4})))', 
                'end2' => $colonOrCommaYearOrBareWords,
                'end3' => null, 
            ],
            // 20. Smith AB and Gonzalez JD(: |\. |, | followed by <paren> or <quote> or <digit>)
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{1,3}', 
                'end1' => ' ' . $andRegExp . ' ', 
                //'end2' => '(: |\. |, | (?=[\(\[`"\'\d]))', 
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr,
                'end3' => null, 
                'initials' => true
            ],
            // 21. A. B. Smith and C. D. Jones[\., ]
            [
                'name1' => $initialsLastName, 
                'end1' => ',? ' . $andRegExp . ' ', 
                //'end2' => '(\. |,? ' . $notJr . ')', 
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr, 
                'end3' => null, 
            ],
            // 22. Smith, J\.? and Jones, A
            // end2 cannot be period, because then 'Smith, Jane and Maria J. Gonzalez' would be terminated at the J,
            [
                'name1' => $lastNameInitials, 
                'end1' => ',? ' . $andRegExp . ' ', 
                //'end2' => '(:|\.(?!-)|,)? ', 
                'end2' => $colonOrCommaYearOrBareWords,
                'end3' => null, 
            ],
            // 23. Smith, J\.? and A\.? Jones[:\.,]
            [
                'name1' => $lastNameInitials, 
                'name2' => $initialsLastName, 
                'end1' => ',? ' . $andRegExp . ' ', 
                //'end2' => '(: |\. |,? ' . $notJr . ')', 
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr, 
                'end3' => null, 
            ],
            // 24. Smith, Jane( J\.?)? and Susan( K\.?)? Jones[,.] 
            [
                'name1' => $lastNameFirstNameInitials,
                'name2' => $firstNameInitialsLastName,
                'end1' => ',? ' . $andRegExp . ' ',
                //'end2' => '(: |\. |,? ' . $notJr . ')',
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr, 
                'end3' => null,
            ],
            // 25. Jane (A. )?Smith[,;] Susan (B. )?Jones(period or colon or comma year)
            [
                'name1' => $firstNameInitialsLastName, 
                'end1' => '[,;] ' . $notAnd, 
                'end2' => $periodOrColonOrCommaYearOrBareWords, 
                'end3' => null, 
            ],
            // 26. Jane (A. )?Smith and Susan (B. )?Jones[,.] 
            [
                'name1' => $firstNameInitialsLastName, 
                'end1' => ',? ' . $andRegExp . ' ',
                //'end2' => '[\.,] ' . $notJr, 
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr, 
                'end3' => null, 
            ],
            // 27. Smith, Jane( J\.?)? and Jones, Susan( K\.?)?[,.] 
            [
                'name1' => $lastNameFirstNameInitials,
                'end1' => ',? ' . $andRegExp . ' ',
                //'end2' => '[\.,] ' . $notJr,
                'end2' => $periodOrColonOrCommaYearOrCommaNotJr, 
                'end3' => null,
            ],
            
            //////////////
            // ONE NAME //
            //////////////
            
            // 28. Smith AB(period not and or colon or comma year)
            // [must be at least two initials, otherwise could be start of name --- e.g. Smith A. Jones]
            [
                'name1' => $lastNameRegExp . ' \p{Lu}{2,3}',
                'end1' => $periodNotAndOrColonOrCommaYear,
                'end2' => null,
                'end3' => null,
                'initials' => true
            ],
            // 29. Smith J. A.(colon or comma year)
            [
                'name1' => $lastNameInitials, 
                //'end1' => $colonOrCommaYear, 
                'end1' => $colonOrCommaYearOrBareWords, 
                'end2' => null, 
                'end3' => null, 
            ],
            // 30. Smith, Jane( J)?(period or colon or comma year or bare words)
            // (Note that need to exclude
            // Jane Smith, Susan A. Jones, Elizabeth Gonzalez, ...
            // with "Jane Smith" being interpreted as a last name.)
            // If colon is included in end1, editor extracted from 'Jane Smith, Leiden: Brill' is
            // Jane Smith, Leiden
            // (However, this case should be dealt with by extracing the publication info before getting the editor.)
            [
                'name1' => $lastNameRegExp . ', ' . $otherNameRegExp . '( \p{Lu}\.?)?( \p{Lu}\.?)?',
                'end1' => '((?<!\p{Lu})\. ' . $notAnd . '(?!\p{Lu}\.)' . '|' . $commaYearOrBareWords . ')',
                //'end1' => $periodNotAndOrCommaYear, 
                'end2' => null, 
                'end3' => null, 
            ],
            // 31. J. A. Smith(period or colon or comma year)
            [
                'name1' => $initialsLastName, 
                'end1' => $periodOrColonOrCommaYearOrBareWords, 
                'end2' => null, 
                'end3' => null, 
            ],
            // 32. Jane A. Smith(period or colon or comma year)
            [
                'name1' => $firstNameInitialsLastName, 
                'end1' => $periodOrColonOrCommaYearOrBareWords, 
                'end2' => null, 
                'end3' => null, 
            ],

            /////////////////////
            // ONE NAME et al. //
            /////////////////////

            // 33. J. A. Smith et.? al.? OR Jane A. Smith,? et.? al.?
            [
                'name1' => '(' . $initialsLastName . '|' . $firstNameInitialsLastName . ')', 
                'end1' => ',? et\.? al\.?', 
                'end2' => null, 
                'end3' => null, 
                'etal' => true,
            ],
            // 34. Smith,? A.,? et al. OR Smith, Jane A.,? et.? al.?
            [
                'name1' => '(' . $lastNameInitials . '|' . $lastNameRegExp . ', ' . $otherNameRegExp . '( \p{Lu})?)',
                'end1' => ',? et\.? al\.?',
                'end2' => null,
                'end3' => null,
                'etal' => true,
            ],
        ];

        return $authorRegExps;
    }
}
