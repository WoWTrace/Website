<?php

declare(strict_types=1);

namespace App\Users\Screens;

use App\Users\Layouts\UserEditLayout;
use App\Users\Layouts\UserFiltersLayout;
use App\Users\Layouts\UserListLayout;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use function __;

class UserListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'User';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All registered users';

    /**
     * @var string
     */
    public $permission = 'platform.systems.users';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'users' => User::with('roles')
                ->filters()
                ->filtersApplySelection(UserFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
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
                ->route('platform.systems.users.create'),
        ];
    }

    /**
     * Views.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            UserFiltersLayout::class,
            UserListLayout::class,

            Layout::modal('oneAsyncModal', UserEditLayout::class)
                ->async('asyncGetUser'),
        ];
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function asyncGetUser(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    /**
     * @param User $user
     * @param Request $request
     */
    public function saveUser(User $user, Request $request): void
    {
        $request->validate([
            'user.email' => 'required|unique:users,email,' . $user->id,
        ]);

        $user->fill($request->input('user'))
            ->save();

        Toast::info(__('User was saved.'));
    }

    /**
     * @param Request $request
     */
    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))
            ->delete();

        Toast::info(__('User was removed'));
    }
}
