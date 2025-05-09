<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Output;

class NormalizeOutputSources extends Command
{
    protected $signature = 'normalize:output-sources';

    protected $description = 'Normalize space characters in the source field of outputs table';

    public function handle(): int
    {
        $processed = 0;
        $updated = 0;

        Output::cursor()->each(function ($output) use (&$processed, &$updated) {
            $processed++;

            $original = $output->source;
            $normalized = $this->normalizeSpaces($original);

            if ($original !== $normalized) {
                $output->forceFill(['source' => $normalized])->updateQuietly();
                $updated++;
            }

            if ($processed % 10000 === 0) {
                $this->info("Processed $processed rows, updated $updated rows.");
            }
        });

        $this->info("Done. Total processed: $processed. Total normalized: $updated.");

        return Command::SUCCESS;
    }

    private function normalizeSpaces(string $text): string
    {
        $whitespaceChars = [
            "\u{00A0}", "\u{2000}", "\u{2001}", "\u{2002}", "\u{2003}",
            "\u{2004}", "\u{2005}", "\u{2006}", "\u{2007}", "\u{2008}",
            "\u{2009}", "\u{200A}", "\u{202F}", "\u{205F}", "\u{3000}", "\u{200B}",
        ];

        return str_replace($whitespaceChars, ' ', $text);
    }
}

