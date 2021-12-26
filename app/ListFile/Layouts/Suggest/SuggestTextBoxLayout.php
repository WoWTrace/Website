<?php

declare(strict_types=1);

namespace App\ListFile\Layouts\Suggest;


use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;
use function __;

class SuggestTextBoxLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            TextArea::make('suggestions')
                ->rows(35)
                ->required()
                ->title(__('ListFile entries'))
                ->placeholder("1;interface/cinematics/logo_800.avi\n21;interface/cinematics/logo_1024.avi\n22;interface/cinematics/wow_intro_1024.avi"),
        ];
    }
}
