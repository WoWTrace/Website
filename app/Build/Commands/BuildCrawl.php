<?php

namespace App\Build\Commands;

use App\Models\Build;
use App\Models\Product;
use Carbon\Carbon;
use Erorus\CASC\Cache;
use Erorus\CASC\Config;
use Erorus\CASC\Encoding;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Illuminate\Console\Command;
use Throwable;

class BuildCrawl extends Command
{
    /** @inerhitDoc */
    protected $signature = 'build:crawl {region=eu}';

    /** @inerhitDoc */
    protected $description = 'Crawl available builds';

    private Cache $cache;

    /** @inerhitDoc */
    public function __construct()
    {
        parent::__construct();
    }

    /** @inerhitDoc */
    public function handle(Cache $cache): int
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->error('This command only works under linux!');
            //return Command::FAILURE;
        }

        $this->cache = $cache;

        Product::all()->each([$this, 'crawl']);

        return Command::SUCCESS;
    }

    public function crawl(Product $product): void
    {

        ini_set('memory_limit', '2G');

        $this->info(sprintf('Crawl product %s ("%s")...', $product->name, $product->product));

        $versionConfig = new HTTPVersionConfig($this->cache, $product->product, $this->argument('region'));

        $version         = $versionConfig->getVersion();
        $buildConfigHash = $versionConfig->getBuildConfig();
        $cdnConfigHash   = $versionConfig->getCDNConfig();

        if (empty($version) || empty($buildConfigHash) || empty($cdnConfigHash) ) {
            $this->info(sprintf('Skip product %s because versions file is empty', $buildConfigHash, $product->product), 'v');
            return;
        }

        $versionParts = explode('.', $version);

        if (count($versionParts) != 4) {
            $this->error(sprintf('Invalid version detected %s in product %s', $version, $product->product));
            return;
        }

        // Update last detected version
        if ($product->lastBuildConfig !== $buildConfigHash) {
            $product->lastVersion = $version;
            $product->lastBuildConfig = $buildConfigHash;
            $product->detected    = Carbon::now();
            $product->update();
        }

        if (Build::firstWhere('buildConfig', $buildConfigHash) !== null) {
            $this->info(sprintf('Build %s in product %s already exists', $buildConfigHash, $product->product), 'v');
            return;
        }

        try {
            $buildConfig = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $buildConfigHash);
            //$cdnConfig   = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $cdnConfigHash);

        } catch (Throwable $throwable) {
            $this->error(sprintf('Failed to download build and cdn config for product %s\n%s', $product->product, $throwable->getMessage()));
            return;
        }

        if (empty($buildConfig->getByKey('encoding'))) {
            $this->info(sprintf('Skip build %s in product %s because it is encrypted ', $buildConfigHash, $product->product), 'v');
            return;
        }

        $encoding = $this->getEncoding($versionConfig, $buildConfig, $product);
        if (!$encoding) {
            return;
        }

        $rootCdnHash = $this->getCdnHashFromContentHash($buildConfig->getByKey('root')[0], $encoding, $product);
        if (!$rootCdnHash) {
            return;
        }

        Build::query()->insert([
            'buildConfig'         => $buildConfigHash,
            'cdnConfig'           => $cdnConfigHash,
            'patchConfig'         => $buildConfig->getByKey('patch-config')[0] ?? null,
            'productConfig'       => $versionConfig->getProductConfig(),
            'product'             => $product->product,
            'expansion'           => $versionParts[0],
            'major'               => $versionParts[1],
            'minor'               => $versionParts[2],
            'build'               => (int)$versionParts[3],
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
            'created_at'          => $product->detected,
            'updated_at'          => $product->detected,
        ]);
    }

    private function getEncoding(HTTPVersionConfig $versionConfig, Config $buildConfig, Product $product): ?Encoding
    {
        $this->info('Download Encoding...', 'v');
        try {
            return new Encoding(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $buildConfig->encoding[1],
                true
            );
        } catch (Throwable $throwable) {
            $this->error(sprintf("Failed to download encoding for product %s\n%s", $product->product, $throwable->getMessage()));
            return null;
        }
    }

    private function getCdnHashFromContentHash(string $contentHash, Encoding $encoding, Product $product): ?string
    {
        $encodingEntry = $encoding->getContentMap(hex2bin($contentHash));

        if (!$encodingEntry) {
            $this->error(sprintf('Cant find content Hash %s in Encoding table for product %s', $contentHash, $product->product));
            return null;
        }

        return bin2hex($encodingEntry->getEncodedHashes()[0]);
    }
}
