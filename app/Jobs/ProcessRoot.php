<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Build\Helper\BuildProcessor;
use App\Common\Services\TactService;
use App\Models\Build;
use App\Models\ListFile;
use App\Models\ListFileBuilds;
use App\Models\ListFileVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessRoot implements ShouldQueue, ShouldBeUnique
{
    use BuildProcessor, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const QUERY_BUFFER_SIZE = 7000;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1800;

    public function __construct(private Build $build, private $force = false)
    {
        //
    }

    public function handle(TactService $tactService): void
    {
        if (!$this->force && $this->alreadyProcessed()) {
            return;
        }

        ini_set('memory_limit', '4G');
        $root = $tactService->getRootByBuild($this->build);

        $queryList   = [];
        $now         = now();
        $buildId     = $this->build->id;
        $clientBuild = $this->build->clientBuild;

        $listFileQuery        = ListFile::query();
        $listFileVersionQuery = ListFileVersion::query();
        $listFileBuildsQuery  = ListFileBuilds::query();

        foreach ($root->all() as $fileId => $rootEntry) {
            $encodingMap = $tactService->getEncodingContentMapWithBuild($rootEntry['contentHash'], $this->build, true);

            $queryList[] = [
                'listfile'        => [
                    'id'         => $fileId,
                    'lookup'     => $rootEntry['nameHash'] ?? null,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ],
                'listfileVersion' => [
                    'id'           => $fileId,
                    'contentHash'  => $rootEntry['contentHash'],
                    'encrypted'    => $rootEntry['encrypted'] ?? false,
                    'fileSize'     => $encodingMap?->getFileSize() ?? null,
                    'firstBuildId' => $buildId,
                    'clientBuild'  => $clientBuild,
                    'created_at'   => $now->toDateTimeString(),
                    'updated_at'   => $now->toDateTimeString(),
                ],
                'listfileBuilds'  => [
                    'id'      => $fileId,
                    'buildId' => $buildId,
                ],
            ];

            if (count($queryList) >= self::QUERY_BUFFER_SIZE) {
                $this->saveQueryBuffer($listFileQuery, $listFileVersionQuery, $listFileBuildsQuery, $queryList);
            }
        }

        $this->saveQueryBuffer($listFileQuery, $listFileVersionQuery, $listFileBuildsQuery, $queryList);
        $this->markAsProcessed();

        //ProcessDBClientFile::dispatch($this->build);
    }

    private function saveQueryBuffer(Builder $listFileQuery, Builder $listFileVersionQuery, Builder $listFileBuildsQuery, array &$queryBuffer)
    {
        $listFileQuery->upsert(
            array_column($queryBuffer, 'listfile'),
            'id',
            ['lookup' => DB::raw('IF(values(`lookup`) IS NOT NULL, values(`lookup`), `lookup`)')]
        );
        $listFileVersionQuery->insertOrIgnore(array_column($queryBuffer, 'listfileVersion'));
        $listFileBuildsQuery->insertOrIgnore(array_column($queryBuffer, 'listfileBuilds'));

        $queryBuffer = [];
    }

    /**
     * The unique ID of the job.
     *
     * @return string|int
     */
    public function uniqueId()
    {
        return $this->build->id;
    }

}
