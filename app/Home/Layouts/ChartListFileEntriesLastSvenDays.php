<?php

declare(strict_types=1);

namespace App\Home\Layouts;

use App\ListFile\Services\ListFileService;
use App\Models\ListFile;
use DateTime;
use DateTimeImmutable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Orchid\Screen\Layouts\Chart;
use Orchid\Screen\Repository;

class ChartListFileEntriesLastSvenDays extends Chart
{
    /** @var string */
    public const TARGET = 'chartListFileEntriesAdded';

    /** @inerhitDoc */
    protected $title;

    /** @inerhitDoc */
    protected $target = self::TARGET;

    /**
     * Configuring line.
     *
     * @var array
     */
    protected $lineOptions = [
        'spline'     => 1,
        'regionFill' => 1,
        'hideDots'   => 0,
        'hideLine'   => 0,
        'heatline'   => 0,
        'dotSize'    => 3,
    ];

    public function __construct()
    {
        $this->title = __('Added ListFile entries');
    }

    public static function getContent(bool $rebuildCache = false): array
    {
        $cacheKey = sprintf('%s-%s', self::TARGET, (new DateTimeImmutable())->format('y-m-d'));
        if (!$rebuildCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $lastSevenDaysCustomFiles = [];
        $lastSevenDaysBlizzardFiles = [];
        $now = new DateTime();

        for ($day = 1; $day <= 7; $day++) {
            $now->modify('-1 days');
            $lastSevenDaysCustomFiles[$now->format('Y-m-d')] = 0;
            $lastSevenDaysBlizzardFiles[$now->format('Y-m-d')] = 0;
        }

        $lastSevenDaysCustomFiles = array_reverse($lastSevenDaysCustomFiles);
        $lastSevenDaysBlizzardFiles = array_reverse($lastSevenDaysBlizzardFiles);


        ListFile::query()
            ->groupByRaw('DATE(`created_at`)')
            ->select([DB::raw('DATE(`created_at`) as `date`'), DB::raw('COUNT(*) as `count`')])
            ->whereDate('created_at', '>=', current(array_keys($lastSevenDaysCustomFiles)))
            ->where('user_id', '!=', ListFileService::getBlizzardUserId())
            ->get()
            ->each(static function ($data) use (&$lastSevenDaysCustomFiles) {
                $lastSevenDaysCustomFiles[$data->date] = $data->count;
            });

        ListFile::query()
            ->groupByRaw('DATE(`created_at`)')
            ->select([DB::raw('DATE(`created_at`) as `date`'), DB::raw('COUNT(*) as `count`')])
            ->whereDate('created_at', '>=', current(array_keys($lastSevenDaysBlizzardFiles)))
            ->where('user_id', ListFileService::getBlizzardUserId())
            ->get()
            ->each(static function ($data) use (&$lastSevenDaysBlizzardFiles) {
                $lastSevenDaysBlizzardFiles[$data->date] = $data->count;
            });


        $chartData = [
            [
                'name'   => __('Custom'),
                'values' => array_values($lastSevenDaysCustomFiles),
                'labels' => array_keys($lastSevenDaysCustomFiles),
            ],
            [
                'name'   => __('Blizzard'),
                'values' => array_values($lastSevenDaysBlizzardFiles),
                'labels' => array_keys($lastSevenDaysBlizzardFiles),
            ]
        ];

        Cache::set($cacheKey, $chartData, 3600);

        return $chartData;
    }

    /**
     * @param Repository $repository
     *
     * @return Factory|View
     */
    public function build(Repository $repository)
    {
        $this->query = $repository;

        if (!$this->isSee()) {
            return;
        }

        $labels = collect($repository->getContent($this->target))
            ->map(function ($item) {
                return $item['labels'] ?? [];
            })
            ->flatten()
            ->unique()
            ->values()
            ->toJson(JSON_NUMERIC_CHECK);

        return view($this->template, [
            'title'            => __($this->title),
            'slug'             => Str::slug($this->title),
            'type'             => $this->type,
            'height'           => $this->height,
            'labels'           => $labels,
            'export'           => $this->export,
            'data'             => json_encode($repository->getContent($this->target), JSON_NUMERIC_CHECK),
            'colors'           => json_encode($this->colors),
            'maxSlices'        => json_encode($this->maxSlices),
            'valuesOverPoints' => json_encode($this->valuesOverPoints),
            'axisOptions'      => json_encode($this->axisOptions),
            'barOptions'       => json_encode($this->barOptions),
            'lineOptions'      => json_encode($this->lineOptions),
            'markers'          => json_encode($this->markers()),
        ]);
    }
}
