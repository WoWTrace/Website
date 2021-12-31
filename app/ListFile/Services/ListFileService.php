<?php

namespace App\ListFile\Services;

use App\Models\User;

final class ListFileService
{
    public const MAX_ROWS_PER_REQUEST = 10000;
    public const PATH_REGEX = '/(\d+;[a-z0-9\/\\\\.\-_ \(\)]+$)/im';

    // Cache blizzard user id
    public static int $blizzardUserId = 0;

    public static function getBlizzardUserId()
    {
        if (!empty(self::$blizzardUserId)) {
            return self::$blizzardUserId;
        }

        if ($user = User::firstWhere('email', config('listfile.sourceUserEmail'))) {
            return self::$blizzardUserId = (int)$user->id;
        }

        return 0;
    }

    public function pathClean(string $path): string
    {
        // lower path
        $path = strtolower($path);

        // backslash
        $path = str_replace('\\', '/', $path);

        // trim
        return trim($path, " \t\n\r\0\x0B/");
    }

    public function listFileStringToArray(string $listfile): array
    {
        $listFileArray = [];
        $suggestions = explode("\n", trim(str_replace(['\\', "\r\n"], ['/', "\n"], $listfile)));

        foreach ($suggestions as $suggestion) {
            list($fileId, $path) = str_getcsv($suggestion, ';');
            $listFileArray[(int)$fileId] = $this->pathClean($path);
        }

        return $listFileArray;
    }
}