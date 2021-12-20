<?php

namespace App\Home\Screens;

use App\Home\Layouts\ChartListFileEntriesLastSvenDays;
use App\Home\Layouts\ChartUsedExtension;
use App\Home\Layouts\ListLastAddedListFileEntries;
use App\Home\Layouts\MetricsListFileNewToday;
use Auth;
use Orchid\Screen\Action;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout as OrchidLayout;
use Throwable;

class HomeScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'WoWData.tools';

    /** @inerhitDoc */
    public $description = 'WoWData.tools shows data from World of Warcraft in a clear form and should help with data mining. To use this website you need a basic understanding of the World of Warcraft structure.';

    /** @inerhitDoc */
    public function query(): array
    {
        return [];
        return [
            MetricsListFileNewToday::TARGET          => MetricsListFileNewToday::getContent(),
            ChartListFileEntriesLastSvenDays::TARGET => ChartListFileEntriesLastSvenDays::getContent(),
            ListLastAddedListFileEntries::TARGET     => ListLastAddedListFileEntries::getContent(),
            ChartUsedExtension::TARGET               => ChartUsedExtension::getContent(),
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return string[]|Layout[]
     *
     * @throws Throwable
     *
     */
    public function layout(): array
    {
        return [];

        return [
            MetricsListFileNewToday::class,
            ChartListFileEntriesLastSvenDays::class,
            OrchidLayout::columns([
                ListLastAddedListFileEntries::class,
                ChartUsedExtension::class,
            ])
        ];
    }
}
