<?php

namespace App\Home\Commands;

use App\Home\Layouts;
use Illuminate\Console\Command;

class HomeChartCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'home:chart:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup chart and metrics cache';

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
        $this->info('Warmup chart and metric caches...');
        $startTime = time();

        Layouts\ChartListFileEntriesLastSvenDays::getContent(true);
        Layouts\ChartUsedExtension::getContent(true);

        $this->info(sprintf('Warmup done in %u seconds!', time() - $startTime));

        return Command::SUCCESS;
    }
}
