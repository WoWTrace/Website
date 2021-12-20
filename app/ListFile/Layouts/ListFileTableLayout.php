<?php

namespace App\ListFile\Layouts;

use App\Common\Screen\Actions\Span;
use App\ListFile\Services\ListFileService;
use App\Models\ListFile;
use Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class ListFileTableLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'listfile';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id', __('FD ID'))
                ->cantHide()
                ->sort()
                ->alignLeft()
                ->filter(Input::make()->type('number'))
                ->render(function (ListFile $listFile) {
                    return Span::make($listFile->id)
                        ->icon(($listFile->user_id !== ListFileService::getBlizzardUserId()) ? 'pencil' : null)
                        ->onclick(sprintf('navigator.clipboard.writeText(%u)', $listFile->id));
                }),

            TD::make('path', __('Filename'))
                ->sort()
                ->filter(Input::make()),

            TD::make('type', __('Type'))
                ->sort()
                ->width('100px')
                ->alignCenter(),

            TD::make(__('Actions'))
                ->cantHide()
                ->alignCenter()
                ->width('100px')
                ->canSee(Auth::user()?->hasAccess('listfile.delete') ?? false)
                ->render(function (ListFile $listFile) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(sprintf(__('Are you sure you want to delete FD ID: <b>%u</b> from ListFile?'), $listFile->id))
                                ->method('remove', [
                                    'id' => $listFile->id,
                                ]),
                        ]);
                }),
        ];
    }
}
