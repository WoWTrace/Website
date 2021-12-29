<?php
declare(strict_types=1);

namespace App\DBClientFile\Commands;

use App\DBClientFile\Services\DBClientFileService;
use App\DBClientFile\Support\DBClientFile;
use App\Jobs\ProcessDBClientFile;
use App\Models\Build;
use Illuminate\Console\Command;

final class DBClientFileDownload extends Command
{
    /** @inerhitDoc */
    protected $signature = 'dbclientfile:download {dbClientFileSelect=all} {product=wow} {locale=enUS} {region=eu}';

    /** @inerhitDoc */
    protected $description = 'Download DBClientFiles';

    private DBClientFileService $dbClientFileService;

    /** @inerhitDoc */
    public function handle(DBClientFileService $dbClientFileService): int
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->error('This command only works under linux!');
            return Command::FAILURE;
        }

        ProcessDBClientFile::dispatch(Build::firstWhere('id', 1));

        $this->dbClientFileService = $dbClientFileService;
        $dbClientFileSelect        = $this->argument('dbClientFileSelect');
        $product                   = $this->argument('product');
        $locale                    = $this->argument('locale');
        $region                    = $this->argument('region');

        // All
        if ($dbClientFileSelect === 'all') {
            foreach (DBClientFile::values() as $dbClientFile) {
                if (!$this->download($dbClientFile, $product, $locale, $region)) {
                    $this->error('Failed to download');
                }
            }

            return Command::SUCCESS;
        }

        // By FileId
        if (DBClientFile::isValid($dbClientFileSelect)) {
            $dbClientFile = DBClientFile::from($dbClientFileSelect);
            if (!$this->download($dbClientFile, $product, $locale, $region)) {
                $this->error('Failed to download');
            }

            return Command::SUCCESS;
        }

        // By Name
        if (DBClientFile::isValidKey($dbClientFileSelect)) {
            $dbClientFile = DBClientFile::$dbClientFileSelect();
            if (!$this->download($dbClientFile, $product, $locale, $region)) {
                $this->error('Failed to download');
            }

            return Command::SUCCESS;
        }

        $this->error('Invalid selected DBClientFile');
        return Command::FAILURE;
    }

    private function download(DBClientFile $dbClientFile, string $product, string $locale, string $region): bool
    {
        $this->info(sprintf('Download %s.db2...', $dbClientFile->getKey()));

        return $this->dbClientFileService->download($dbClientFile, $product, $locale, $region);
    }
}