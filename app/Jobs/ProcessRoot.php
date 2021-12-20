<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Build;
use App\Models\ListFile;
use Erorus\CASC\Cache;
use Erorus\CASC\Manifest\Root;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        $now         = now();

        echo "Start Collected Data " . " memory: " . $this->convert(memory_get_usage()) . "\n";
        foreach ($root->all() as $fileId => $rootEntry) {
            $queryList[] = [
                'id'          => $fileId,
                'lookup'      => $rootEntry['nameHash'] ?? null,
                'contentHash' => $rootEntry['contentHash'] ?? null,
                'rootCdnHash' => $this->build->rootCdnHash,
                'encrypted'   => $rootEntry['encrypted'] ?? false,
                'created_at'  => $now->toDateTimeString(),
                'updated_at'  => $now->toDateTimeString(),
            ];
        }

        $i = 1;
        $listFileQuery = ListFile::query();

        foreach (array_chunk($queryList, self::QUERY_BUFFER_SIZE, true) as $chunk) {
            $listFileQuery->insertOrIgnore($chunk);
            echo "Chunk done " . self::QUERY_BUFFER_SIZE * $i++ . " memory: " . $this->convert(memory_get_usage()) . "\n";
        }

        unset($queryList);
    }

    private function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    private function getRoot(Cache $cache): ?Root
    {
        $product       = (string)$this->build->product;
        $versionConfig = new HTTPVersionConfig($cache, $product, 'eu');

        try {
            return new Root(
                $cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $this->build->rootCdnHash
            );
        } catch (Throwable $throwable) {
            $a = 1;
            return null;
        }
    }
}
