<?php

declare(strict_types=1);

namespace App\Common;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use jdavidbakr\LaravelCacheGarbageCollector\LaravelCacheGarbageCollector;

class Kernel extends ConsoleKernel
{
    /** @inerhitDoc  */
    protected $commands = [
        LaravelCacheGarbageCollector::class
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Builds
        $schedule->command('build:crawl')->everyTenMinutes();

        // Cleanup expired laravel cache
        $schedule->command('cache:gc')->everyTwoHours();
    }

    protected function commands(): void
    {
        $this->load([
            __DIR__ . '/Commands',
            __DIR__ . '/../Home/Commands',
            __DIR__ . '/../ListFile/Commands',
            __DIR__ . '/../Build/Commands',
            __DIR__ . '/../DBClientFile/Commands',
        ]);
    }
}