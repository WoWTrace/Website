<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Build\Helper\BuildProcessor;
use App\DBClientFile\Services\DBClientFileService;
use App\DBClientFile\Support\DBClientFile;
use App\ListFile\Services\ListFileService;
use App\Models\Build;
use App\Models\ListFile;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDBClientFile implements ShouldQueue, ShouldBeUnique
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

    public function __construct(private Build $build)
    {
        //
    }

    public function handle(DBClientFileService $dbClientFileService, ListFileService $listFileService): void
    {
        if ($this->alreadyProcessed()) {
            return;
        }

        ini_set('memory_limit', '1G');

        $generatedListFile = array_replace(
            $this->processMap($dbClientFileService, $listFileService),
            $this->processManifestInterfaceData($dbClientFileService, $listFileService),
            $this->processManifestInterfaceTOCData($dbClientFileService, $listFileService),
        );

        $now = now()->toDateTimeString();

        foreach (array_chunk($generatedListFile, self::QUERY_BUFFER_SIZE, true) as $chunk) {
            $queryBuffer = [];
            foreach ($chunk as $fileId => $path) {
                $queryBuffer[] = [
                    'id'            => $fileId,
                    'path'          => $path,
                    'type'          => pathinfo($path, PATHINFO_EXTENSION),
                    'verified'      => true,
                    'pathDiscovery' => $now,
                ];
            }

            ListFile::upsert($queryBuffer, ['path'], ['path', 'type', 'verified', 'pathDiscovery']);
        }

        $this->markAsProcessed();
    }

    /**
     * @param DBClientFileService $dbClientFileService
     * @return array<int, string>
     * @throws Exception
     */
    private function processMap(DBClientFileService $dbClientFileService, ListFileService $listFileService): array
    {
        $generatedListFile = [];
        $dbClientFile      = $dbClientFileService->open(DBClientFile::Map(), $this->build);
        if (empty($dbClientFile)) {
            return $generatedListFile;
        }

        $dbClientFile->fetchColumnNames();

        foreach ($dbClientFile->getIds() as $id) {
            $row = $dbClientFile->getRecord($id);

            if (empty($row['Directory']))
                break;

            if (!empty($row['ZmpFileDataID'])) {
                $generatedListFile[(int)$row['ZmpFileDataID']] = $listFileService->pathClean(sprintf('interface/worldmap/%s.zmp', $row['Directory']));
            }

            if (!empty($row['WdtFileDataID'])) {
                $generatedListFile[(int)$row['WdtFileDataID']] = $listFileService->pathClean(sprintf('world/maps/%s/%s.wdt', $row['Directory'], $row['Directory']));
            }
        }

        return $generatedListFile;
    }

    /**
     * @param DBClientFileService $dbClientFileService
     * @return array<int, string>
     * @throws Exception
     */
    private function processManifestInterfaceData(DBClientFileService $dbClientFileService, ListFileService $listFileService): array
    {
        $generatedListFile = [];
        $dbClientFile      = $dbClientFileService->open(DBClientFile::ManifestInterfaceData(), $this->build);
        if (empty($dbClientFile)) {
            return $generatedListFile;
        }

        $dbClientFile->fetchColumnNames();

        foreach ($dbClientFile->getIds() as $id) {
            $row = $dbClientFile->getRecord($id);

            if (empty($row['FilePath']) || empty($row['FileName']))
                break;

            $generatedListFile[$id] = $listFileService->pathClean($row['FilePath'] . $row['FileName']);
        }

        return $generatedListFile;
    }

    /**
     * @param DBClientFileService $dbClientFileService
     * @return array<int, string>
     * @throws Exception
     */
    private function processManifestInterfaceTOCData(DBClientFileService $dbClientFileService, ListFileService $listFileService): array
    {
        $generatedListFile = [];
        $dbClientFile      = $dbClientFileService->open(DBClientFile::ManifestInterfaceTOCData(), $this->build);
        if (empty($dbClientFile)) {
            return $generatedListFile;
        }

        $dbClientFile->fetchColumnNames();

        foreach ($dbClientFile->getIds() as $id) {
            $row = $dbClientFile->getRecord($id);

            if (empty($row['FilePath']))
                break;

            $pathParts = explode("\\", trim($row['FilePath'], '\\'));

            $generatedListFile[$id] = $listFileService->pathClean(sprintf('%s%s.toc', $row['FilePath'], end($pathParts)));
        }

        return $generatedListFile;
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
