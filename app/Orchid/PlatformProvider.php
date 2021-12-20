<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Build;
use App\Home;
use App\ListFile;
use App\Roles;
use App\Users;
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
        parent::boot($dashboard);
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
