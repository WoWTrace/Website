<?php

declare(strict_types=1);

namespace App\Roles\Screens;

use App\Roles\Layouts\RoleListLayout;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use function __;
use function route;

class RoleListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Manage roles';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Access rights';

    /**
     * @var string
     */
    public $permission = 'platform.systems.roles';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'roles' => Role::filters()->defaultSort('id', 'desc')->paginate(),
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->href(route('platform.systems.roles.create')),
        ];
    }

    /**
     * Views.
     *
     * @return string[]|Layout[]
     */
    public function layout(): array
    {
        return [
            RoleListLayout::class,
        ];
    }
}
