<?php

declare(strict_types=1);

namespace App\Jobs;

use App\DBClientFile\Services\DBClientFileService;
use App\DBClientFile\Support\DBClientFile;
use App\ListFile\Services\ListFileService;
use App\Models\Build;
use App\Models\ListFile;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDBClientFile implements ShouldQueue
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

    public function handle(DBClientFileService $dbClientFileService, ListFileService $listFileService): void
    {
        ini_set('memory_limit', '1G');

        $generatedListFile = array_replace(
            $this->processMap($dbClientFileService, $listFileService),
            $this->processManifestInterfaceData($dbClientFileService, $listFileService),
            $this->processManifestInterfaceTOCData($dbClientFileService, $listFileService),
        );

        foreach (array_chunk($generatedListFile, self::QUERY_BUFFER_SIZE, true) as $chunk) {
            $queryBuffer = [];
            foreach ($chunk as $fileId => $path) {
                $queryBuffer[] = [
                    'id'       => $fileId,
                    'path'     => $path,
                    'type'     => pathinfo($path, PATHINFO_EXTENSION),
                    'verified' => true
                ];
            }

            ListFile::upsert($queryBuffer, ['id'], ['path', 'type', 'verified']);
        }
    }

    /**
     * @param DBClientFileService $dbClientFileService
     * @return array<int, string>
     * @throws Exception
     */
    private function processMap(DBClientFileService $dbClientFileService, ListFileService $listFileService): array
    {
        $generatedListFile = [];
        $db2               = $dbClientFileService->open(DBClientFile::Map(), $this->build->productKey);
        $db2->fetchColumnNames();

        foreach ($db2->getIds() as $id) {
            $row = $db2->getRecord($id);

            if (empty($row['Directory']))
                break;

            if (!empty($row['ZmpFileDataID'])) {
                $generatedListFile[(int)$row['ZmpFileDataID']] = $listFileService->pathClean(sprintf('interface/worldmap/%s.zmp', $row['Directory']));
            }

            if (!empty($row['WdtFileDataID'])) {
                $generatedListFile[(int)$row['WdtFileDataID']] = $listFileService->pathClean(sprintf('world/maps/azeroth/%s.wdt', $row['Directory']));
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
        $db2               = $dbClientFileService->open(DBClientFile::ManifestInterfaceData(), $this->build->productKey);
        $db2->fetchColumnNames();

        foreach ($db2->getIds() as $id) {
            $row = $db2->getRecord($id);

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
        $db2               = $dbClientFileService->open(DBClientFile::ManifestInterfaceTOCData(), $this->build->productKey);
        $db2->fetchColumnNames();

        foreach ($db2->getIds() as $id) {
            $row = $db2->getRecord($id);

            if (empty($row['FilePath']))
                break;

            $pathParts = explode("\\", trim($row['FilePath'], '\\'));

            $generatedListFile[$id] = $listFileService->pathClean(sprintf('%s%s.toc', $row['FilePath'], end($pathParts)));
        }

        return $generatedListFile;
    }
}
