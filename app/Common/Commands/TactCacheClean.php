<?php

namespace App\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TactCacheClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tact:cache-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean tact cache folder';

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
        $this->info('Clean tact folder...');
        Storage::deleteDirectory('tactCache');
        $this->info('Done');

        return Command::SUCCESS;
    }
}
