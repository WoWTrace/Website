<?php

namespace App\ListFile\Layouts\Index;

use App\Common\Screen\Actions\Span;
use App\Common\Screen\TDExtended;
use App\Models\ListFile;
use App\Models\ListFileVersion;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class ListFileTableLayout extends Table
{
    public const COLOR_EMPTY = 'rgba(220, 63, 66, 0.35)';
    public const COLOR_UNVERIFIED = 'rgba(165, 126, 248, 0.315)';

    /** @inerhitDoc */
    protected $target = 'listfile';

    /** @inerhitDoc */
    protected function columns(): array
    {
        return [
            TD::make('id', __('FD ID'))
                ->width(110)
                ->cantHide()
                ->sort()
                ->alignLeft()
                ->filter(Input::make()->type('number')),

            TDExtended::make('path', __('Filename'))
                ->sort()
                ->filter(Input::make())
                ->alignLeft()
                ->renderColor(static function (ListFile $listFile, TDExtended $td) {
                    if (empty($listFile->path)) {
                        $td->backgroundColor(self::COLOR_EMPTY);
                    } elseif (!$listFile->verified) {
                        $td->backgroundColor(self::COLOR_UNVERIFIED);
                    }
                }),


            TDExtended::make('lookup', __('Lookup'))
                ->width(145)
                ->sort()
                ->defaultHidden()
                ->filter(Input::make()),

            TD::make('versions', __('Versions'))
                ->width(200)
                ->sort()
                ->render(static function (ListFile $listFile) {
                    $versions = $listFile->versions;

                    if ($versions->isEmpty()) {
                        return 'No versions available';
                    }

                    if ($versions->count() === 1) {
                        /** @var ListFileVersion $listFileVersion */
                        $listFileVersion = $listFile->versions->first();

                        $firstBuildVersion = $listFileVersion->firstBuild;

                        $span = Span::make(sprintf(
                            '%s.%u (%s)',
                            $firstBuildVersion->patch,
                            $firstBuildVersion->clientBuild,
                            $firstBuildVersion->product->badgeText
                        ));

                        if ($listFileVersion->encrypted) {
                            return $span->icon('lock');
                        }

                        return $span;
                    }

                    $versionList = $versions->map(static function ($listFileVersion): Span {
                        $versionBuild = $listFileVersion->firstBuild;

                        $span = Span::make(sprintf(
                            '%s.%u (%s)',
                            $versionBuild->patch,
                            $versionBuild->clientBuild,
                            $versionBuild->product->badgeText
                        ));

                        if ($listFileVersion->first()->encrypted) {
                            return $span->icon('lock');
                        }

                        return $span;
                    });

                    return DropDown::make(sprintf(__(sprintf('%u Versions', $versions->count()))))
                        ->list($versionList->toArray());
                }),


            TDExtended::make('type', __('Type'))
                ->width(120)
                ->sort()
                ->filter(Input::make())
                ->alignRight(),
        ];
    }
}
