<?php

namespace App\Common\Services;

final class DownloadService
{

    public function readRemoteCSV(string $url, string $separator, callable $processLineCallback): int
    {
        $file = fopen($url, 'r');

        $importedEntries = 0;
        while (($line = fgetcsv($file, 0, $separator)) !== false) {
            $processLineCallback($line);
            $importedEntries++;
        }

        return $importedEntries;
    }
}
