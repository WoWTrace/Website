<?php

declare(strict_types=1);

namespace App\Build;

use App\Build\Screens\BuildScreen;
use App\Build\Screens\BuildCompareScreen;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Tabuna\Breadcrumbs\Trail;

final class Platform
{
    public const ROUTE_BUILDS_OVERVIEW_SLUG = 'builds';
    public const ROUTE_BUILDS_OVERVIEW_KEY = 'platform.builds';
    public const ROUTE_BUILDS_COMPARE_SLUG = 'builds/compare';
    public const ROUTE_BUILDS_COMPARE_KEY = "platform.builds.compare";

    public static function boot(Dashboard $dashboard)
    {
        $permissions = ItemPermission::group('Builds')
            ->addPermission('builds.api', 'API access');

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        return [
            Menu::make('Builds')
                ->icon('server')
                ->route(self::ROUTE_BUILDS_OVERVIEW_KEY)
        ];
    }

    public static function registerScreens(): void
    {
        Route::screen(self::ROUTE_BUILDS_OVERVIEW_SLUG, BuildScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_BUILDS_OVERVIEW_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Builds'), route(self::ROUTE_BUILDS_OVERVIEW_KEY));
            });

        Route::screen(self::ROUTE_BUILDS_OVERVIEW_SLUG, BuildCompareScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_BUILDS_COMPARE_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Builds'), route(self::ROUTE_BUILDS_OVERVIEW_KEY))
                    ->push(__('Compare'), route(self::ROUTE_BUILDS_COMPARE_KEY));
            });
    }
}
