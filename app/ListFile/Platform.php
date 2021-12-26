<?php

declare(strict_types=1);

namespace App\ListFile;

use App\ListFile\Screens\ListFileScreen;
use App\ListFile\Screens\SuggestListFileScreen;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Tabuna\Breadcrumbs\Trail;

final class Platform
{
    public const ROUTE_LISTFILE_OVERVIEW_SLUG = 'listfile';
    public const ROUTE_LISTFILE_OVERVIEW_KEY = 'platform.listfile';
    public const ROUTE_LISTFILE_SUGGEST_SLUG = 'listfile/suggest';
    public const ROUTE_LISTFILE_SUGGEST_KEY = 'platform.listfile.suggest';

    public static function boot(Dashboard $dashboard)
    {
        $permissions = ItemPermission::group('ListFile')
            ->addPermission('listfile.suggest', 'Suggest Entries')
            ->addPermission('listfile.add', 'Add Entries')
            ->addPermission('listfile.delete', 'Delete Entries')
            ->addPermission('listfile.api', 'API access');

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        return [
            Menu::make('Files')
                ->icon('list')
                ->route(self::ROUTE_LISTFILE_OVERVIEW_KEY)
        ];
    }

    public static function registerScreens(): void
    {
        Route::screen(self::ROUTE_LISTFILE_OVERVIEW_SLUG, ListFileScreen::class)
            ->name(self::ROUTE_LISTFILE_OVERVIEW_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY));
            });

        Route::screen(self::ROUTE_LISTFILE_SUGGEST_SLUG, SuggestListFileScreen::class)
            ->name(self::ROUTE_LISTFILE_SUGGEST_KEY)
            ->middleware(['permission:listfile.suggest'])
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY))
                    ->push(__('Suggest'), route(self::ROUTE_LISTFILE_SUGGEST_KEY));
            });
    }
}
