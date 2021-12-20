<?php

namespace App\Build\Screens;

use App\Build\Layouts\BuildTableLayout;
use App\Build\Layouts\DetailModalLayout;
use App\Models\Build;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

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
    public function query(): array
    {
        return [
            'build' => Build::query()
                ->filters()
                ->defaultSort('build', 'desc')
                ->paginate()
        ];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [];
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
        ];
    }

    public function asyncGetBuild(Build $build): array
    {
        return [
            'buildDetails' => $build,
        ];
    }
}
