<?php

declare(strict_types=1);

namespace App\Roles;

use App\Roles\Screens\RoleEditScreen;
use App\Roles\Screens\RoleListScreen;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Tabuna\Breadcrumbs\Trail;

final class Platform
{
    public static function boot(Dashboard $dashboard)
    {
        $permissions = ItemPermission::group(__('System'))
            ->addPermission('platform.systems.roles', __('Roles'));

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        return
            [
                Menu::make(__('Roles'))
                    ->icon('lock')
                    ->route('platform.systems.roles')
                    ->permission('platform.systems.roles'),
            ];
    }

    public static function registerScreens(): void
    {
        // Platform > System > Roles > Role
        Route::screen('roles/{roles}/edit', RoleEditScreen::class)
            ->name('platform.systems.roles.edit')
            ->breadcrumbs(function (Trail $trail, $role) {
                return $trail
                    ->parent('platform.systems.roles')
                    ->push(__('Role'), route('platform.systems.roles.edit', $role));
            });

        // Platform > System > Roles > Create
        Route::screen('roles/create', RoleEditScreen::class)
            ->name('platform.systems.roles.create')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.systems.roles')
                    ->push(__('Create'), route('platform.systems.roles.create'));
            });

        // Platform > System > Roles
        Route::screen('roles', RoleListScreen::class)
            ->name('platform.systems.roles')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Roles'), route('platform.systems.roles'));
            });
    }
}
