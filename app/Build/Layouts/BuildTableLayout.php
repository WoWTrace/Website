<?php

namespace App\Build\Layouts;

use App\Common\Screen\Actions\Span;
use App\ListFile\Services\ListFileService;
use App\Models\Build;
use App\Models\ListFile;
use App\Models\User;
use Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class BuildTableLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'build';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('patch', __('Patch'))
                ->cantHide()
                ->width('100px')
                ->filter(Input::make()),

            TD::make('build', __('Build'))
                ->sort()
                ->width('110px')
                ->filter(Input::make()->type('number')),

            TD::make('branch', __('Branch'))
                ->alignLeft()
                ->render(function (Build $build) {
                    return sprintf(
                        '<b class="badge %s">%s</b>',
                        $build->product()->first()->badgeType,
                        $build->product()->first()->badgeText
                    );
                }),

            TD::make('buildConfig', __('Build Config')),
            TD::make('patchConfig', __('Patch Config')),
            TD::make('cdnConfig', __('CDN Config')),
            TD::make('created_at', __('Detected at (CEST)'))
                ->sort()
                ->render(function (Build $build) {
                    return $build->created_at->format('Y-m-d H:i:s');
                }),

            TD::make('view', '')
                ->cantHide()
                ->render(function (Build $build) {
                    return ModalToggle::make('')
                        ->icon('magnifier')
                        ->modal('detailModal')
                        ->modalTitle(__('Build Information'))
                        ->asyncParameters($build->id);
                }),
        ];
    }
}
