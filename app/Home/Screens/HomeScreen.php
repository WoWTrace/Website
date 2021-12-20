<?php

namespace App\Home\Screens;

use App\Home\Layouts\ProductEntries;
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
        return [
            ProductEntries::TARGET => ProductEntries::getContent(),
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
        return [
            OrchidLayout::columns([
                ProductEntries::class,
                OrchidLayout::columns([])
            ])
        ];
    }
}
