<?php

// Suggested by copilot.
// Doesn't quite work: the str_replace seems not subtle enough
$filePath = 'app/Services/Converter.php';
$fileContent = file_get_contents($filePath);

$pattern = '/\$this->setField\(\$item, [^;]+, \'setField \d+[a-z]?\'\);/';
$matches = [];
preg_match_all($pattern, $fileContent, $matches);

$counter = 1;
foreach ($matches[0] as $match) {
    $newMatch = preg_replace('/setField \d+[a-z]?/', 'setField ' . $counter, $match);
    $fileContent = str_replace($match, $newMatch, $fileContent);
    $counter++;
}

file_put_contents($filePath, $fileContent);

echo "Updated setField calls with sequential numbers.\n";