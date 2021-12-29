<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Build;
use App\Models\ListFile;
use App\Models\ListFileVersion;
use Erorus\CASC\Cache;
use Erorus\CASC\Manifest\Root;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRoot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    public function __construct(private Build $build)
    {
        //
    }

    public function handle(Cache $cache): void
    {
        ini_set('memory_limit', '4G');
        $root = $this->getRoot($cache);

        $queryList = [];
        $now = now();
        $buildId = $this->build->id;
        $clientBuild = $this->build->clientBuild;

        foreach ($root->all() as $fileId => $rootEntry) {
            $queryList[] = [
                'listfile'        => [
                    'id'         => $fileId,
                    'lookup'     => $rootEntry['nameHash'] ?? null,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ],
                'listfileVersion' => [
                    'id'          => $fileId,
                    'contentHash' => $rootEntry['contentHash'] ?? null,
                    'encrypted'   => $rootEntry['encrypted'] ?? false,
                    'buildId'     => $buildId,
                    'clientBuild' => $clientBuild,
                    'created_at'  => $now->toDateTimeString(),
                    'updated_at'  => $now->toDateTimeString(),
                ]
            ];
        }

        $listFileQuery = ListFile::query();
        $listFileVersionQuery = ListFileVersion::query();

        foreach (array_chunk($queryList, self::QUERY_BUFFER_SIZE, true) as $chunk) {
            $listFileQuery->insertOrIgnore(array_column($chunk, 'listfile'));
            $listFileVersionQuery->insertOrIgnore(array_column($chunk, 'listfileVersion'));
        }

        unset($queryList);

        ProcessDBClientFile::dispatch($this->build);
    }

    private function getRoot(Cache $cache): Root
    {
        $versionConfig = new HTTPVersionConfig($cache, $this->build->productKey, 'eu');

        return new Root(
            $cache,
            $versionConfig->getServers(),
            $versionConfig->getCDNPath(),
            $this->build->rootCdnHash
        );
    }
}
