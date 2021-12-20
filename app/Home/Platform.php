<?php

declare(strict_types=1);

namespace App\Home;

use App\Home\Screens\HomeScreen;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Screen\Actions\Menu;

final class Platform
{
    public const ROUTE_HOME_OVERVIEW_SLUG = '/home';
    public const ROUTE_HOME_OVERVIEW_KEY  = 'platform.home';

    public static function registerMainMenu(): array
    {
        return
            [
                Menu::make('Home')
                    ->icon('home')
                    ->route(self::ROUTE_HOME_OVERVIEW_KEY)
            ];
    }

    public static function registerScreens(): void
    {
        Route::screen(self::ROUTE_HOME_OVERVIEW_SLUG, HomeScreen::class)
            ->name(self::ROUTE_HOME_OVERVIEW_KEY);
    }
}
