<?php

namespace App\Traits;

trait StringCleaners
{
    public $burmeseNumerals = [
        '0' => "\xE1\x81\x80",
        '1' => "\xE1\x81\x81",
        '2' => "\xE1\x81\x82",
        '3' => "\xE1\x81\x83",
        '4' => "\xE1\x81\x84",
        '5' => "\xE1\x81\x85",
        '6' => "\xE1\x81\x86",
        '7' => "\xE1\x81\x87",
        '8' => "\xE1\x81\x88",
        '9' => "\xE1\x81\x89",
    ];

    private function digitsToBurmeseNumerals(string $string): string
    {
        $string = str_replace(array_keys($this->burmeseNumerals), $this->burmeseNumerals, $string);

        return $string;
    }

    private function burmeseNumeralsToDigits(string $string): string
    {
        $string = str_replace($this->burmeseNumerals, array_keys($this->burmeseNumerals), $string);

        return $string;
    }

    private function cleanText(string $string): string
    {
        $replacements = [
            '\\newblock' => '',
            '\\newpage' => '',
            // Replace each tab with a space
            "\t" => ' ',
            '\\textquotedblleft ' => '``',
            '\\textquotedblleft{}' => '``',
            '\\textquotedblright ' => "''",
            '\\textquotedblright' => "''",
            '\\textquoteright ' => "'",
            '\\textendash ' => '--',
            '\\textendash{}' => '--',
            '\\textemdash ' => '---',
            '\\textemdash{}' => '---',
            '•' => '',
            // Replace non-breaking space with regular space
            "\xC2\xA0" => ' ',
            // Replace thin space with regular space
            "\xE2\x80\x89" => ' ',
            // Replace punctuation space with regular space
            "\xE2\x80\x88" => ' ',
            // Remove left-to-right mark
            "\xE2\x80\x8E" => '',
            // Remove zero width joiner
            "\xE2\x80\x8D" => '',
            // Remove zero width nonjoiner
            "\xE2\x80\x8C" => '',
            // Replace zero-width non-breaking space with regular space
            // Change is now made when file is uploaded
            // "\xEF\xBB\xBF" => " ",
            // http://www.utf8-chartable.de/unicode-utf8-table.pl (change "go to other block" to see various parts)
            "\xE2\x80\x90" => '-',
            "\xE2\x80\x91" => '-',
            "\xE2\x80\x93" => '--',
            "\xE2\x80\x94" => '---',
            "\xC2\xAD" => '',  // soft hyphen (shown on https://www.utf8-chartable.de/unicode-utf8-table.pl?start=128&number=128)
            // ‘ and ’
            "\xE2\x80\x98" => '`',
            "\xE2\x80\x99" => "'",
            "\xE2\x80\x9C" => '``',
            // ”
            "\xE2\x80\x9D" => "''",
            // „
            "\xE2\x80\x9E" => '``',
            "\xE2\x80\x9F" => "''",
            "\xEF\xAC\x80" => 'ff',
            "\xEF\xAC\x81" => 'fi',
            "\xEF\xAC\x82" => 'fl',
            "\xEF\xAC\x83" => 'ffi',
            "\xEF\xAC\x84" => 'ffl',
            // French guillemets
            "\xC2\xAB" => '``',
            "\xC2\xBB" => "''",
            // ‚ [single low quotation mark, but here translated to comma, which it looks like]
            // If it is ever used as an opening quote, may need to make translation depend on
            // whether it is preceded or followed by a space
            "\xE2\x80\x9A" => ',',
            "\xEF\xBC\x81" => '!',
            "\xEF\xBC\x82" => '"',
            "\xEF\xBC\x83" => '#',
            "\xEF\xBC\x84" => '$',
            "\xEF\xBC\x85" => '%',
            "\xEF\xBC\x86" => '&',
            "\xEF\xBC\x87" => "'",
            "\xEF\xBC\x88" => '(',
            "\xEF\xBC\x89" => ')',
            "\xEF\xBC\x8A" => '*',
            "\xEF\xBC\x8B" => '+',
            "\xEF\xBC\x8C" => ', ',
            "\xEF\xBC\x8D" => '-',
            "\xEF\xBC\x8E" => '.',
            "\xEF\xBC\x8F" => '/',
            "\xEF\xBC\x9A" => ':',
            "\xEF\xBC\x9B" => ';',
            "\xEF\xBC\x9F" => '?',
            "\xEF\xBC\x3B" => '[',
            "\xEF\xBC\x3D" => ']',
            "\xEF\xBD\x80" => '`',
            "\xEF\xBC\xBB" => '[',
            "\xEF\xBC\xBD" => ']',
            "\xEF\xBC\xBE" => '^',
            '&nbsp;' => ' ',
            '\\ ' => ' ',
            '\\textbf{ }' => ' ',
            '\\textbf{\\ }' => ' ',
            '\\textit{ }' => ' ',
            '\\textit{\\ }' => ' ',
            // Remove copyright symbol
            '©' => '',
        ];

        $string = str_replace(array_keys($replacements), $replacements, $string);

        // Replace ~ with space if not preceded by \ or / or : (as it will be in a URL; actualy #:~: in URL)
        $string = preg_replace('/([^\/\\\:])~/', '$1 ', $string);
        $string = str_replace('\\/', '', $string);

        // Fix errors like 'x{\em ' by adding space after the x [might not be error?]
        $string = preg_replace('/([^ ])(\{\\\[A-Za-z]{2,8} )/', '$1 $2', $string);

        // Remove spaces before commas (assumed to be errors)
        $string = str_replace(' ,', ',', $string);

        // Delete ^Z and any trailing space (^Z is at end of last entry of DOS file)
        $string = rtrim($string, " \032");
        $string = ltrim($string, ' ');

        return $string;
    }

    /*
     * Replace every substring of multiple spaces with a single space.
     */
    private function regularizeSpaces(string $string): string
    {
        // Using \h (horizontal white space) seems to mess up utf-8.
        // return preg_replace('%\h+%', ' ', $string);
        return preg_replace('% +%', ' ', $string);
    }

    private function utf8ToTeX(string $string): string
    {
        $replacements = [
            // C3
            "\xC3\x80" => "{\`A}",
            "\xC3\x81" => "{\\'A}",
            "\xC3\x82" => "{\^A}",
            "\xC3\x83" => "{\~A}",
            "\xC3\x84" => '{\\"A}',
            "\xC3\x85" => "{\AA}",
            "\xC3\x86" => "{\AE}",
            "\xC3\x87" => "{\c{C}}",
            "\xC3\x88" => "{\`E}",
            "\xC3\x89" => "{\\'E}",
            "\xC3\x8A" => "{\^E}",
            "\xC3\x8B" => '{\\"E}',
            "\xC3\x8C" => "{\`I}",
            "\xC3\x8D" => "{\\'I}",
            "\xC3\x8E" => "{\^I}",
            "\xC3\x8F" => '{\\"I}',
            "\xC3\x90" => "{\DH}",
            "\xC3\x91" => "{\~N}",
            "\xC3\x92" => "{\`O}",
            "\xC3\x93" => "{\\'O}",
            "\xC3\x94" => "{\^O}",
            "\xC3\x95" => "{\~O}",
            "\xC3\x96" => '{\\"O}',
            "\xC3\x98" => "{\O}",
            "\xC3\x99" => "{\`U}",
            "\xC3\x9A" => "{\\'U}",
            "\xC3\x9B" => "{\^U}",
            "\xC3\x9C" => '{\\"U}',
            "\xC3\x9D" => "{\\'Y}",
            "\xC3\x9E" => "{\Thorn}",
            "\xC3\x9F" => "{\ss}",
            "\xC3\xA0" => "{\`a}",
            "\xC3\xA1" => "{\\'a}",
            "\xC3\xA2" => "{\^a}",
            "\xC3\xA3" => "{\=a}",
            "\xC3\xA4" => '{\\"a}',
            "\xC3\xA5" => "{\aa}",
            "\xC3\xA6" => "{\ae}",
            "\xC3\xA7" => "\c{c}",
            "\xC3\xA8" => "{\`e}",
            "\xC3\xA9" => "{\\'e}",
            "\xC3\xAA" => "{\^e}",
            "\xC3\xAB" => '{\\"e}',
            "\xC3\xAC" => "{\`\i}",
            "\xC3\xAD" => "{\\'\i}",
            "\xC3\xAE" => "{\^\i}",
            "\xC3\xAF" => "{\\\"\i}",
            "\xC3\xB0" => "{\dh}",
            "\xC3\xB1" => "{\~n}",
            "\xC3\xB2" => "{\`o}",
            "\xC3\xB3" => "{\\'o}",
            "\xC3\xB4" => "{\^o}",
            "\xC3\xB5" => "{\=o}",
            "\xC3\xB6" => '{\\"o}',
            "\xC3\xB8" => "{\o}",
            "\xC3\xB9" => "{\`u}",
            "\xC3\xBA" => "{\\'u}",
            "\xC3\xBB" => "{\^u}",
            "\xC3\xBC" => '{\\"u}',
            "\xC3\xBD" => "{\\'y}",
            "\xC3\xBE" => '{\\thorn}',
            "\xC3\xBF" => '{\\"y}',
            // C4
            "\xC4\x80" => "{\=A}",
            "\xC4\x81" => "{\=a}",
            "\xC4\x82" => "{\u{A}}",
            "\xC4\x83" => "{\u{a}}",
            "\xC4\x84" => "{\k{A}}",
            "\xC4\x85" => "{\k{a}}",
            "\xC4\x86" => "{\\'C}",
            "\xC4\x87" => "{\\'c}",
            "\xC4\x88" => "{\^C}",
            "\xC4\x89" => "{\^c}",
            "\xC4\x8A" => "{\.C}",
            "\xC4\x8B" => "{\.c}",
            "\xC4\x8C" => '\\v{C}',
            "\xC4\x8D" => '\\v{c}',
            "\xC4\x8E" => '\\v{D}',
            // "\xC4\x90" => "{}",
            // "\xC4\x91" => "{}",
            "\xC4\x92" => "{\=E}",
            "\xC4\x93" => "{\=e}",
            "\xC4\x94" => "{\u{E}}",
            "\xC4\x95" => "{\u{e}}",
            "\xC4\x96" => "{\.E}",
            "\xC4\x97" => "{\.e}",
            "\xC4\x98" => "{\k{E}}",
            "\xC4\x99" => "{\k{e}}",
            "\xC4\x9A" => '\\v{E}',
            "\xC4\x9B" => '\\v{e}',
            "\xC4\x9C" => "{\^G}",
            "\xC4\x9D" => "{\^g}",
            // "\xC4\x9E" => "{\u{G}}",
            // "\xC4\x9F" => "{\u{g}}",
            "\xC4\xA0" => "{\.G}",
            "\xC4\xA1" => "{\.g}",
            "\xC4\xA2" => "{\k{G}}",
            // "\xC4\xA3" => "",
            "\xC4\xA4" => "{\^H}",
            "\xC4\xA5" => "{\^h}",
            // "\xC4\xA6" => "{}",
            // "\xC4\xA7" => "{}",
            "\xC4\xA8" => "{\~I}",
            "\xC4\xA9" => "{\=\i}",
            "\xC4\xAA" => "{\=I}",
            "\xC4\xAB" => "{\=\i}",
            // "\xC4\xAC" => "{\u{I}}",
            // "\xC4\xAD" => "{\u{\i}}",
            "\xC4\xAE" => "{\k{I}}",
            "\xC4\xAF" => "{\k{i}}",
            "\xC4\xB0" => "{\.I}",
            "\xC4\xB1" => "{\i}",
            // "\xC4\xb2" => "",
            // "\xC4\xb3" => "",
            "\xC4\xB4" => "{\^J}",
            "\xC4\xB5" => "{\^\j}",
            // "\xC4\xb6" => "{}",
            // "\xC4\xb7" => "{}",
            // "\xC4\xb8" => "{\~I}",
            "\xC4\xB9" => "{\'L}",
            "\xC4\xBA" => "{\'l}",
            // "\xC4\xbB" => "",
            // "\xC4\xbC" => "",
            // "\xC4\xbD" => "",
            // "\xC4\xbE" => "",
            // "\xC4\xbF" => "",
            // C5
            "\xC5\xA0" => '\\v{S}',
            "\xC5\xA1" => '\\v{s}',
        ];

        $string = str_replace(array_keys($replacements), $replacements, $string);

        return $string;
    }
}
