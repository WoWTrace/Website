<?php

declare(strict_types=1);

namespace App\Home\Layouts;

use App\ListFile\Services\ListFileService;
use App\Models\ListFile;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Layouts\Metric;

class MetricsListFileNewToday extends Metric
{
    /** @var string */
    public const TARGET = 'metricsListFileNewToday';

    /** @inerhitDoc */
    protected $target = self::TARGET;

    /** @inerhitDoc */
    protected $labels;

    public function __construct()
    {
        $this->labels = [
            __('Added Custom ListFile Entries Today'),
            __('Added Blizzard ListFile Entries Today'),
        ];
    }

    public static function getContent(): array
    {
        $todayDate = (new DateTimeImmutable())->format('Y-m-d');

        $customAddedCount = ListFile::query()
            ->groupByRaw('DATE(`created_at`)')
            ->select([DB::raw('DATE(`created_at`) as `date`'), DB::raw('COUNT(*) as `count`')])
            ->whereDate('created_at', $todayDate)
            ->where('user_id', '!=', ListFileService::getBlizzardUserId())
            ->first(['date', 'count']);

        $blizzardAddedCount = ListFile::query()
            ->groupByRaw('DATE(`created_at`)')
            ->select([DB::raw('DATE(`created_at`) as `date`'), DB::raw('COUNT(*) as `count`')])
            ->whereDate('created_at', $todayDate)
            ->where('user_id', ListFileService::getBlizzardUserId())
            ->first(['date', 'count']);

        return [
            ['keyValue' => number_format($customAddedCount->count ?? 0, 0, ',', '.')],
            ['keyValue' => number_format($blizzardAddedCount->count ?? 0, 0, ',', '.')],
        ];
    }
}
