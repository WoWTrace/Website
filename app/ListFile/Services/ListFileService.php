<?php

namespace App\ListFile\Services;

use App\Models\User;

final class ListFileService
{
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
}