<?php

declare(strict_types=1);

namespace App\Build;

use App\Build\Screens\BuildScreen;
use App\ListFile\Screens\ListFileScreen;
use Erorus\CASC\BLTE;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Tabuna\Breadcrumbs\Trail;

final class Platform
{
    public const ROUTE_BUILDS_OVERVIEW_SLUG = 'builds';
    public const ROUTE_BUILDS_OVERVIEW_KEY  = 'platform.builds';

    public static function boot(Dashboard $dashboard)
    {
        $permissions = ItemPermission::group('Builds')
            ->addPermission('builds.api', 'API access');

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        return
            [
                Menu::make('Builds')
                    ->icon('server')
                    ->route(self::ROUTE_BUILDS_OVERVIEW_KEY)
            ];
    }

    public static function registerScreens(): void
    {
        Route::screen(self::ROUTE_BUILDS_OVERVIEW_SLUG, BuildScreen::class)
            ->name(self::ROUTE_BUILDS_OVERVIEW_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Builds'), route(self::ROUTE_BUILDS_OVERVIEW_KEY));
            });
    }
}
