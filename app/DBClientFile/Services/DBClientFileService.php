<?php
declare(strict_types=1);

namespace App\DBClientFile\Services;

use App\Common\Services\TactService;
use App\DBClientFile\Support\DBClientFile;
use App\Models\Build;
use Erorus\DB2\Reader;
use Illuminate\Support\Facades\Storage;

final class DBClientFileService
{
    public function __construct(private TactService $tactService)
    {
        //
    }

    public function open(DBClientFile $dbClientFile, Build $build, string $locale = 'enUS'): ?Reader
    {
        if (!$this->download($dbClientFile, $build, $locale)) {
            return null;
        }

        return new Reader($this->getStoragePath($dbClientFile, $build, $locale));
    }

    public function download(DBClientFile $dbClientFile, Build $build, string $locale = 'enUS'): bool
    {
        $storagePath = $this->getStoragePath($dbClientFile, $build, $locale);

        return $this->tactService->downloadFileByNameOrIdWithBuild((string)$dbClientFile->getValue(), $storagePath, $build, $locale);
    }

    public function getStoragePath(DBClientFile $dbClientFile, Build $build, string $locale = 'enUS'): string
    {
        $downloadFolder = Storage::path(sprintf('dbclientfile/%s/%s/%s', $build->productKey, sprintf('%s.%u', $build->patch, $build->clientBuild), $locale));

        if (!is_dir($downloadFolder)) {
            mkdir($downloadFolder, 0777, true);
        }

        return sprintf('%s/%s.db2', $downloadFolder, $dbClientFile->getKey());
    }
}