<?php

namespace App\ListFile\Commands;

use App\Common\Services\DownloadService;
use App\Models\ListFile;
use App\Models\User;
use Illuminate\Console\Command;
use function config;

class ListFileDownload extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listfile:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download current listfile from Github';

    /** @var DownloadService $downloadService */
    private $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var string $sourceUserEmail */
        $sourceUserEmail = config('listfile.sourceUserEmail');

        /** @var User $listFileUser */
        $listFileUser = User::query()->where('email', $sourceUserEmail)->first();

        if (!$listFileUser) {
            $this->error(sprintf('Source User not found by E-Mail "%s"', $sourceUserEmail));
            return Command::FAILURE;
        }

        /** @var string $sourceUrl */
        $sourceUrl = config('listfile.sourceUrl');

        /** @var string $sourceSeparator */
        $sourceSeparator = config('listfile.sourceSeparator');

        /** @var int $queryBufferSize */
        $queryBufferSize = config('listfile.importQueryBufferSize');

        /** @var array<string, string|int> $queryBuffer */
        $queryBuffer = [];

        $startTime = time();
        $this->info('Import soruce listfile from github...');
        $importedEntries = $this->downloadService->readRemoteCSV($sourceUrl, $sourceSeparator, function (array $line) use (&$queryBuffer, $queryBufferSize, $listFileUser) {
            if (count($line) != 2) {
                return;
            }

            if (count($queryBuffer) >= $queryBufferSize) {
                $this->saveQueryBuffer($queryBuffer);
            }

            $path          = trim($line[1]);
            $queryBuffer[] = [
                'id'        => trim($line[0]),
                'path'      => $path,
                'extension' => pathinfo($path, PATHINFO_EXTENSION),
                'user_id'   => $listFileUser->id,
            ];
        });

        $this->saveQueryBuffer($queryBuffer);
        $this->info(sprintf('%u entries imported in %u seconds!', $importedEntries, time() - $startTime));

        return Command::SUCCESS;
    }

    private function saveQueryBuffer(array &$queryBuffer)
    {
        ListFile::query()->insertOrIgnore($queryBuffer);
        $queryBuffer = [];
    }
}
