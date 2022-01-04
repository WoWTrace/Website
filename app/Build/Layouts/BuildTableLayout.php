<?php

namespace App\Build\Layouts;

use App\Common\Screen\TDExtended;
use App\Models\Build;
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
            TDExtended::make('patch', __('Patch'))
                ->cantHide()
                ->minWidth(75)
                ->filter(Input::make()),

            TDExtended::make('clientBuild', __('Build'))
                ->sort()
                ->minWidth(90)
                ->filter(Input::make()->type('number'))
                ->render(static function (Build $build): string {
                    if ($build->clientBuild  % 10000 == 0) {
                        return sprintf('%u <span class="lead">&#x1f389;</span>', $build->clientBuild);
                    }

                    return (string)$build->clientBuild;
                }),

            TD::make('branch', __('Branch'))
                ->alignLeft()
                ->render(function (Build $build) {
                    return sprintf(
                        '<b class="badge %s">%s</b> %s',
                        $build->product->badgeType,
                        $build->product->badgeText,
                        $build->custom ? '<b class="badge bg-warning" alt="Build configuration for this build was is custom generated.">&#x270f;</b>' : ''
                    );
                }),

            TD::make('buildConfig', __('Build Config')),
            TD::make('patchConfig', __('Patch Config')),
            TD::make('cdnConfig', __('CDN Config')),
            TD::make('compiledAt', __('Compiled at (PT)'))
                ->sort()
                ->render(function (Build $build) {
                    if (empty($build->compiledAt)) {
                        return '';
                    }

                    return $build->compiledAt->format('Y-m-d H:i:s');
                }),
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
