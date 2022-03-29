<?php

declare(strict_types=1);

namespace App\Build\Layouts;

use App\Models\Build;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class BuildCompareModalLayout extends Rows
{
    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function fields(): array
    {
        return [
            Select::make('build.old')
                ->title(__('Old Build'))
                ->help(__('Select the old build to compare against.'))
                ->required()
                ->fromModel(Build::class, 'name', 'id'),
                
            Select::make('build.new')
                ->title(__('New Build'))
                ->help(__('Select the new build to compare against.'))
                ->required()
                ->fromModel(Build::class, 'name', 'id'),
        ];
    }
}