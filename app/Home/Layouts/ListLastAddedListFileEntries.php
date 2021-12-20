<?php

namespace App\Home\Layouts;

use App\Common\Screen\Actions\Span;
use App\ListFile\Services\ListFileService;
use App\Models\ListFile;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class ListLastAddedListFileEntries extends Table
{

    /** @var string */
    public const TARGET = 'listLastAddedListFileEntries';

    /** @inerhitDoc */
    protected $target = self::TARGET;

    public static function getContent(): array
    {
        $rows = [];
        ListFile::query()
            ->limit(13)
            ->orderByDesc('created_at')
            ->get()
            ->each(static function (ListFile $listFile) use (&$rows) {
                $rows[] = $listFile;
            });

        return $rows;
    }

    /** @inerhitDoc */
    protected function columns(): array
    {
        return [
            TD::make('id', __('FD ID'))
                ->alignLeft()
                ->render(function (ListFile $listFile) {
                    return Span::make($listFile->id)
                        ->icon(($listFile->user_id !== ListFileService::getBlizzardUserId()) ? 'pencil' : null)
                        ->onclick(sprintf('navigator.clipboard.writeText(%u)', $listFile->id));
                }),

            TD::make('path', __('Filename')),
        ];
    }
}
