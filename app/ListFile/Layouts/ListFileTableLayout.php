<?php

namespace App\ListFile\Layouts;

use App\Common\Screen\Actions\Span;
use App\Common\Screen\TDWithColor;
use App\Models\ListFile;
use App\Models\ListFileVersion;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class ListFileTableLayout extends Table
{
    public const COLOR_EMPTY = 'rgba(255, 88, 88, 0.315)';
    public const COLOR_UNVERIFIED = 'rgba(103, 58, 183, 0.315)';

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
                ->width(110)
                ->cantHide()
                ->sort()
                ->alignLeft()
                ->filter(Input::make()->type('number')),

            TDWithColor::make('path', __('Filename'))
                ->sort()
                ->filter(Input::make())
                ->alignLeft()
                ->renderColor(static function (ListFile $listFile, TDWithColor $td) {
                    if (empty($listFile->path)) {
                        $td->backgroundColor(self::COLOR_EMPTY);
                    } elseif (!$listFile->verified) {
                        $td->backgroundColor(self::COLOR_UNVERIFIED);
                    }
                }),

            TD::make('versions', __('Versions'))
                ->width(200)
                ->sort()
                ->render(static function (ListFile $listFile) {
                    if ($listFile->versions->isEmpty()) {
                        return 'No versions available';
                    }

                    if ($listFile->versions->count() === 1) {
                        /** @var ListFileVersion $listFileVersion */
                        $listFileVersion = $listFile->versions->first();

                        $versionBuild = $listFileVersion->build;

                        $span = Span::make(sprintf(
                            '%s.%u (%s)',
                            $versionBuild->patch,
                            $versionBuild->clientBuild,
                            $versionBuild->product->badgeText
                        ));

                        if ($listFileVersion->encrypted) {
                            return $span->icon('lock');
                        }

                        return $span;
                    }

                    $versionList = $listFile->versions->map(static function (ListFileVersion $listFileVersion): Span {
                        $versionBuild = $listFileVersion->build;

                        $span = Span::make(sprintf(
                            '%s.%u (%s)',
                            $versionBuild->patch,
                            $versionBuild->clientBuild,
                            $versionBuild->product->badgeText
                        ));

                        if ($listFileVersion->encrypted) {
                            return $span->icon('lock');
                        }

                        return $span;
                    });

                    return DropDown::make(sprintf(__(sprintf('%u Versions', $listFile->versions->count()))))
                        ->list($versionList->toArray());
                }),


            TDWithColor::make('type', __('Type'))
                ->width(120)
                ->sort()
                ->filter(Input::make())
                ->alignRight(),
        ];
    }
}
