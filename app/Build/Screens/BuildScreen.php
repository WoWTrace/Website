<?php

namespace App\Build\Screens;

use App\Build\Layouts\BuildCompareModalLayout;
use App\Build\Layouts\BuildTableLayout;
use App\Build\Layouts\DetailModalLayout;
use App\Build\Platform;
use App\Common\Screen\Screen;
use App\Models\Build;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BuildScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'Builds';

    /** @inerhitDoc */
    public $description = 'A listing of World of Warcraft related builds from Blizzard Entertainment. (Updated every 10 minutes)';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Request $request): array
    {
        return [
            'build' => Build::query()
                ->filters()
                ->defaultSort('clientBuild', 'desc')
                ->paginate($request->query('pageSize', 25)),
        ];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [
            ModalToggle::make(__('Compare'))
                ->icon('eyeglasses')
                ->modal('buildCompareModal')
                ->modalTitle(__('Build Compare'))
                ->method('buildCompare'),
            DropDown::make(sprintf('Page Size: %u', \request()->query('pageSize', 25)))
                ->type(Color::SECONDARY())
                ->icon('book-open')
                ->list([
                    Button::make('25')->method('selectPageSize')->parameters(['pageSize' => 25]),
                    Button::make('50')->method('selectPageSize')->parameters(['pageSize' => 50]),
                    Button::make('100')->method('selectPageSize')->parameters(['pageSize' => 100]),
                    Button::make('250')->method('selectPageSize')->parameters(['pageSize' => 250]),
                ])
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        return [
            BuildTableLayout::class,
            Layout::modal('detailModal', DetailModalLayout::class)
                ->withoutApplyButton()
                ->size('modal-xl')
                ->async('asyncGetBuild'),
            Layout::modal('buildCompareModal', BuildCompareModalLayout::class)
                ->applyButton('Compare')
                ->closeButton('Close'),
        ];
    }

    public function asyncGetBuild(Build $build): array
    {
        return [
            'buildDetails' => $build,
        ];
    }

    public function buildCompare(Request $request): RedirectResponse
    {
        $request->validate([
            'build.old' => 'int|required',
            'build.new' => 'int|required',
        ]);

        $oldBuildId = $request->input('build.old');
        $newBuildId = $request->input('build.new');

        if ($oldBuildId === $newBuildId) {
            Toast::error('You cannot compare a build to itself.');
            return Redirect::back();
        }

        return Redirect::route(Platform::ROUTE_BUILDS_COMPARE_KEY, [
            'oldBuild' => $oldBuildId,
            'newBuild' => $newBuildId,
        ]);
    }

    public function selectPageSize(Request $request)
    {
        return Redirect::route($request->route()->getName(), $request->query->all());
    }
}
