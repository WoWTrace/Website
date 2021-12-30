<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Build;
use App\Home;
use App\ListFile;
use App\Roles;
use App\Users;
use Illuminate\Support\Facades\View;
use Orchid\Platform\Dashboard;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        View::composer('dashboard', function () use ($dashboard) {
            foreach ($this->registerMainMenu() as $element) {
                $dashboard->registerMenuElement(Dashboard::MENU_MAIN, $element);
            }

            foreach ($this->registerProfileMenu() as $element) {
                $dashboard->registerMenuElement(Dashboard::MENU_PROFILE, $element);
            }
        });

        foreach ($this->registerPermissions() as $permission) {
            $dashboard->registerPermissions($permission);
        }

        $dashboard->registerSearch($this->registerSearchModels());

        ListFile\Platform::boot($dashboard);
        Build\Platform::boot($dashboard);
        Users\Platform::boot($dashboard);
        Roles\Platform::boot($dashboard);
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return array_merge(
            Home\Platform::registerMainMenu(),
            ListFile\Platform::registerMainMenu(),
            Build\Platform::registerMainMenu(),
            Users\Platform::registerMainMenu(),
            Roles\Platform::registerMainMenu()
        );
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return array_merge(
            Users\Platform::registerProfileMenu(),
        );
    }
}
