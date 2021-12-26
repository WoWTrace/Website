<?php

namespace App\Build\Commands;

use App\Common\Services\TactService;
use App\Jobs\ProcessRoot;
use App\Models\Build;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class BuildCrawl extends Command
{
    /** @inerhitDoc */
    protected $signature = 'build:crawl {region=eu}';

    /** @inerhitDoc */
    protected $description = 'Crawl available builds';

    private TactService $tactService;

    /** @inerhitDoc */
    public function __construct()
    {
        parent::__construct();
    }

    /** @inerhitDoc */
    public function handle(TactService $tactService): int
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->error('This command only works under linux!');
            //return Command::FAILURE;
        }

        $this->tactService = $tactService;

        Product::all()->each([$this, 'crawl']);

        return Command::SUCCESS;
    }

    public function crawl(Product $product): void
    {
        ini_set('memory_limit', '1G');

        $this->info(sprintf('Crawl product %s ("%s")...', $product->name, $product->product));
        $region = $this->argument('region');

        try {
            $versionConfig   = $this->tactService->getVersionConfig($product->product, $region);
            $version         = $versionConfig->getVersion();
            $buildConfigHash = $versionConfig->getBuildConfig();
            $cdnConfigHash   = $versionConfig->getCDNConfig();

            $versionParts = explode('.', $version);
            if (count($versionParts) != 4) {
                $this->error(sprintf('Invalid version detected %s in product %s', $version, $product->product));
                return;
            }

            // Update last detected version
            if ($product->lastBuildConfig !== $buildConfigHash) {
                $product->lastVersion     = $version;
                $product->lastBuildConfig = $buildConfigHash;
                $product->detected        = Carbon::now();
                $product->update();
            }

            if (Build::firstWhere('buildConfig', $buildConfigHash) !== null) {
                $this->info(sprintf('Build %s in product %s already exists', $buildConfigHash, $product->product), 'v');
                return;
            }

            $buildConfig = $this->tactService->getBuildConfig($product->product, $region);
            $rootCdnHash = $this->tactService->getCdnHashFromContentHash($buildConfig->getByKey('root')[0], $product->product, $region);

            if (!$rootCdnHash) {
                return;
            }

            /** @var Build $build */
            $build = Build::query()->create([
                'buildConfig'         => $buildConfigHash,
                'cdnConfig'           => $cdnConfigHash,
                'patchConfig'         => $buildConfig->getByKey('patch-config')[0] ?? null,
                'productConfig'       => $versionConfig->getProductConfig(),
                'productKey'          => $product->product,
                'expansion'           => $versionParts[0],
                'major'               => $versionParts[1],
                'minor'               => $versionParts[2],
                'clientBuild'         => (int)$versionParts[3],
                'name'                => $buildConfig->getByKey('build-name')[0],
                'encodingContentHash' => $buildConfig->getByKey('encoding')[0],
                'encodingCdnHash'     => $buildConfig->getByKey('encoding')[1],
                'rootContentHash'     => $buildConfig->getByKey('root')[0],
                'rootCdnHash'         => $rootCdnHash,
                'installContentHash'  => $buildConfig->getByKey('install')[0],
                'installCdnHash'      => $buildConfig->getByKey('install')[1],
                'downloadContentHash' => $buildConfig->getByKey('download')[0],
                'downloadCdnHash'     => $buildConfig->getByKey('download')[1],
                'sizeContentHash'     => $buildConfig->getByKey('size')[0],
                'sizeCdnHash'         => $buildConfig->getByKey('size')[1],
            ]);

            ProcessRoot::dispatch($build);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
