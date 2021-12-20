<?php

declare(strict_types=1);

namespace App\Common;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Builds
        $schedule->command('build:crawl')->everyTenMinutes();

        // Clean
        $schedule->command('tact:clean-cache')->daily()->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load([
            __DIR__ . '/Commands',
            __DIR__ . '/../Home/Commands',
            __DIR__ . '/../ListFile/Commands',
            __DIR__ . '/../Build/Commands',
        ]);
    }
}