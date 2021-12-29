<?php
declare(strict_types=1);

namespace App\DBClientFile\Services;

use App\Common\Services\TactService;
use App\DBClientFile\Support\DBClientFile;
use Erorus\DB2\Reader;
use Illuminate\Support\Facades\Storage;

final class DBClientFileService
{
    public function __construct(private TactService $tactService)
    {
        //
    }

    public function open(DBClientFile $dbClientFile, string $product, string $locale = 'enUS', string $region = TactService::DEFAULT_REGION): ?Reader
    {
        if (!$this->download($dbClientFile, $product, $locale, $region)) {
            return null;
        }

        $versionConfig    = $this->tactService->getVersionConfig($product, $region);
        $dbClientFilePath = Storage::path(sprintf('tactCache/dbclientfile/%s/%s/%s/%s.db2', $product, $versionConfig->getVersion(), $locale, $dbClientFile->getKey()));

        return new Reader($dbClientFilePath);
    }

    public function download(DBClientFile $dbClientFile, string $product, string $locale = 'enUS', string $region = TactService::DEFAULT_REGION): bool
    {
        $versionConfig  = $this->tactService->getVersionConfig($product, $region);
        $downloadFolder = Storage::path(sprintf('tactCache/dbclientfile/%s/%s/%s', $product, $versionConfig->getVersion(), $locale));

        if (!is_dir($downloadFolder)) {
            mkdir($downloadFolder, 0777, true);
        }

        return $this->tactService->downloadFileByNameOrId((string)$dbClientFile->getValue(), sprintf('%s/%s.db2', $downloadFolder, $dbClientFile->getKey()), $product, $region, $locale);
    }
}