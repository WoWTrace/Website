<?php

namespace App\ListFile\Commands;

use App\Models\ListFile;
use App\Models\Product;
use Erorus\CASC\Manifest\Root;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ListFileImportRoot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listfile:import:root {rootCdnKey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import listfile entries from root file';

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
        $this->info('Import from Root file... ' . $this->argument('rootCdnKey'));


        /*
        $root = $this->getRoot($versionConfig, $rootCdnHash, $product);
        if (!$root) {
            return;
        }

        $va = $root->all();
*/
        return Command::SUCCESS;
    }

    private function getRoot(HTTPVersionConfig $versionConfig, string $rootCdnHash, Product $product): ?Root
    {
        $this->info('Download Root...', 'v');

        try {
            return new Root(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $rootCdnHash
            );
        } catch (Throwable $throwable) {
            $this->error(sprintf('Failed to download root for product %s\n%s', $product->product, $throwable->getMessage()));
            return null;
        }
    }
}
