<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(BroadcastManager $broadcastManager): void
    {
        $broadcastManager->routes();

        require $this->app->basePath('routes/channels.php');
    }
}
