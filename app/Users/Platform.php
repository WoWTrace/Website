<?php

declare(strict_types=1);

namespace App\Users;

use App\Users\Screens\UserEditScreen;
use App\Users\Screens\UserListScreen;
use App\Users\Screens\UserProfileScreen;
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
            ->addPermission('platform.systems.users', __('Users'));

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        return
            [
                Menu::make(__('Users'))
                    ->icon('user')
                    ->route('platform.systems.users')
                    ->permission('platform.systems.users')
                    ->title(__('Access rights')),
            ];
    }

    public static function registerProfileMenu(): array
    {
        return
            [
                Menu::make('Profile')
                    ->route('platform.profile')
                    ->icon('user'),
            ];
    }

    public static function registerScreens(): void
    {
        // Platform > Profile
        Route::screen('profile', UserProfileScreen::class)
            ->name('platform.profile')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Profile'), route('platform.profile'));
            });

        // Platform > System > Users
        Route::screen('users/{user}/edit', UserEditScreen::class)
            ->name('platform.systems.users.edit')
            ->breadcrumbs(function (Trail $trail, $user) {
                return $trail
                    ->parent('platform.systems.users')
                    ->push(__('User'), route('platform.systems.users.edit', $user));
            });

        // Platform > System > Users > Create
        Route::screen('users/create', UserEditScreen::class)
            ->name('platform.systems.users.create')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.systems.users')
                    ->push(__('Create'), route('platform.systems.users.create'));
            });

        // Platform > System > Users > User
        Route::screen('users', UserListScreen::class)
            ->name('platform.systems.users')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('Users'), route('platform.systems.users'));
            });

    }
}
