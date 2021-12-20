<?php

declare(strict_types=1);

namespace App\Home\Layouts;

use App\Models\ListFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Layouts\Chart;

class ChartUsedExtension extends Chart
{
    /** @var string */
    public const TARGET = 'chartUsedExtension';

    /** @inerhitDoc */
    protected $title = 'Used file extension';

    /** @inerhitDoc */
    protected $height = 650;

    /** @inerhitDoc */
    protected $type = 'pie';

    /** @inerhitDoc */
    protected $target = self::TARGET;

    public static function getContent(bool $rebuildCache = false): array
    {
        if (!$rebuildCache && Cache::has(self::TARGET)) {
            return Cache::get(self::TARGET);
        }

        $extensionCount = ListFile::query()
            ->groupBy('extension')
            ->select(['extension', DB::raw('COUNT(*) as `count`')])
            ->whereNotIn('extension', ['meta', 'pd4', 'pm4', 'h2o', 'col', 'png', 'dat'])
            ->orderByDesc('count')
            ->get(['extension', 'count'])
            ->mapWithKeys(static function ($data) {
                return [$data->extension => $data->count];
            })
            ->toArray();

        $metricsData = [
            [
                'labels' => array_keys($extensionCount),
                'values' => array_values($extensionCount),
            ]
        ];

        Cache::set(self::TARGET, $metricsData, 60 * 60 * 24);

        return $metricsData;
    }
}
