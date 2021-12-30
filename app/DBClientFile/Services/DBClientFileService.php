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

        return new Reader($this->getStoragePath($dbClientFile, $product, $locale, $region));
    }

    public function download(DBClientFile $dbClientFile, string $product, string $locale = 'enUS', string $region = TactService::DEFAULT_REGION): bool
    {
        $storagePath = $this->getStoragePath($dbClientFile, $product, $locale, $region);

        return $this->tactService->downloadFileByNameOrId((string)$dbClientFile->getValue(), $storagePath, $product, $region, $locale);
    }

    public function getStoragePath(DBClientFile $dbClientFile, string $product, string $locale = 'enUS', string $region = TactService::DEFAULT_REGION): string
    {
        $versionConfig  = $this->tactService->getVersionConfig($product, $region);
        $downloadFolder = Storage::path(sprintf('dbclientfile/%s/%s/%s', $product, $versionConfig->getVersion(), $locale));

        if (!is_dir($downloadFolder)) {
            mkdir($downloadFolder, 0777, true);
        }

        return sprintf('%s/%s.db2', $downloadFolder, $dbClientFile->getKey());
    }
}