<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\RouteRegistrar;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(
            function (RouteRegistrar $webRouter, RouteRegistrar $apiRouter, Application $application) {
                $webRouter
                    ->as('web.')
                    ->middleware('web')
                    ->group($application->basePath('routes/web.php'));

                $apiRouter
                    ->prefix('api')
                    ->middleware('api')
                    ->group($application->basePath('routes/api.v1.php'));
            }
        );
    }
}
