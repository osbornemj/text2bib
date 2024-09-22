<?php
namespace App\Traits;

trait StringExtractors
{
    use StringCleaners;

    /*
     * Remove all matches for $regExp (regular expression without delimiters), case insensitive, from $string
     * and return resulting string (unaltered if there are no matches).
     */
    private function findAndRemove(string $string, string $regExp, int $limit = -1): string
    {
        return preg_replace('%' . $regExp . '%i', '', $string, $limit);
    }

    /*
     * removeAndReturn METHOD SHOULD BE USED INSTEAD
     * Find first match for $regExp (regular expression without delimiters), case insensitive, in $string,
     * return groups in match (components of $result)
     * and remove entire match for $regExp from $string.
     * If no match, return false (and do not alter $string).
     */
    private function findRemoveAndReturn(string &$string, string $regExp, bool $caseInsensitive = true): false|string|array
    {
        $matched = preg_match(
            '%' . $regExp . '%u' . ($caseInsensitive ? 'i' : ''),
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
     * Find first match for $regExp (regular expression without delimiters), case insensitive, in $string,
     * return matches for each string with name in $names, and remove entire match for $regExp from $string.
     * If no match, return false (and do not alter $string).
     */
    private function removeAndReturn(string &$string, string $regExp, array $names, string $position = 'first', bool $caseInsensitive = true): false|string|array
    {

        $regExp = '%^(?P<before>.*' . ($position == 'first' ? '?' : '') . ')' . $regExp . '(?P<after>.*?)$%u' . ($caseInsensitive ? 'i' : '');

        $matched = preg_match($regExp, $string, $matches);

        if (! $matched) {
            return false;
        }

        $result['before'] = $matches['before'] ?? '';
        $result['after'] = $matches['after'] ?? '';
        foreach ($names as $name) {
            $result[$name] = $matches[$name] ?? '';
        }
        $string = ($matches['before'] ?? '') . ' ' . ($matches['after'] ?? '');
        $string = $this->regularizeSpaces(trim($string));

        return $result;
    }

    /*
     * If $reportLabel is false: 
     *   For $string that matches <label><content>, remove match for <label> and <content> and return match for <content>,
     *   where <label> and <content> are regular expressions (without delimiters).  Matching is case-insensitive.
     *   If no matches, return false.
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

        if (! $matched) {
            return false;
        }

        $content = trim($matches['content'][0], ' .,;');
        $string = substr($string, 0, $matches['label'][1]) . substr($string, $matches['content'][1] + strlen($matches['content'][0]), strlen($string));
        $string = $this->regularizeSpaces(trim($string, ' .,'));

        $returner = $reportLabel ? ['label' => trim($matches['label'][0]), 'content' => $content] : $content;

        return $returner;
    }


}
