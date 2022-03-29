<?php

namespace App\Build\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BuildCompareLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'buildCompare';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('action', __('Action')),

            TD::make('fileID', __('File ID')),

            TD::make('fileName', __('File Name')),

            TD::make('fileType', __('File Type')),
        ];
    }
}