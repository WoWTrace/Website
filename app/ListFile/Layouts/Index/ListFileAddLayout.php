<?php

declare(strict_types=1);

namespace App\ListFile\Layouts\Index;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class ListFileAddLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('listfile.id')
                ->type('number')
                ->min(1)
                ->max(2147483647)
                ->required()
                ->title(__('FD ID'))
                ->placeholder('1'),

            Input::make('listfile.path')
                ->type('text')
                ->min(1)
                ->max(265)
                ->required()
                ->title(__('Path'))
                ->placeholder('interface/cinematics/logo_800.avi'),
        ];
    }
}
