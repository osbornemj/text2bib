<?php
namespace App\Services;

use Str;

use App\Services\RegularExpressions;

class EditionParser
{
    private RegularExpressions $regExps;

    public function __construct()
    {
        $this->regExps = new RegularExpressions;
    }

    public function extractEdition(string &$string, bool $start = false, bool $end = false): array|null
    {
        $beforePattern = $start ? '' : '(?P<before>.*)';
        $afterPattern = $end ? '' : '(?P<after>.*)';

        $editionRegExp = '/^' . $beforePattern . '(\(' . $this->regExps->editionRegExp . '\)|' . $this->regExps->editionRegExp . '[.,]?)' . $afterPattern . '$/iJu';

        if ($string && preg_match($editionRegExp, $string, $matches)) {

            for ($i = 1; $i <= 10; $i++) {
                if ($matches['n' . $i]) {
                    $edition = $matches['n' . $i];
                    $editionNumber = $i;
                    break;
                }
            }

            return [
                'edition' => $edition,
                'editionNumber' => $editionNumber,
                'before' => $matches['before'] ?? '',
                'after' => $matches['after'] ?? ''
            ];
        }

        return null;

    }

   
}