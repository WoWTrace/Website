<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Jobs\ProcessRoot;
use App\Models\Build;
use App\Models\ListFile;
use Illuminate\Database\Seeder;

class ListFileSeeder extends Seeder
{
    public function run(): void
    {
        Build::whereRaw( '`id` NOT IN (SELECT `buildId` FROM `listfile_version` GROUP BY `buildId`)')
            ->orderBy("clientBuild")
            ->each(static function(Build $build) {
            ProcessRoot::dispatch($build);
        });
    }
}
