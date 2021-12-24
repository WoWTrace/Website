<?php

namespace App\ListFile\Commands;

use App\Models\ListFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ListFileCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listfile:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate listfile cache with custom data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Generate listfile cache with custom data...');

        $startTime = time();
        $listFileHandler = fopen(Storage::path(config('listfile.cachePath')), 'wb');
        ListFile::query()->orderBy('id')->each(static function ($entry) use ($listFileHandler) {
            fwrite($listFileHandler, sprintf("%s;%s\n", $entry->id, $entry->path));
        });

        // Trim last newline
        $stat = fstat($listFileHandler);
        ftruncate($listFileHandler, $stat['size'] - 1);

        fclose($listFileHandler);
        $this->info(sprintf('Listfile cache generated in %u seconds!', time() - $startTime));

        return Command::SUCCESS;
    }
}
