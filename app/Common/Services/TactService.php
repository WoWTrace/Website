<?php


namespace App\Common\Services;


use App\Common\Exceptions\TactException;
use App\Models\Build;
use Carbon\CarbonInterface;
use Erorus\CASC\BLTE;
use Erorus\CASC\Cache;
use Erorus\CASC\Config;
use Erorus\CASC\DataSource\TACT;
use Erorus\CASC\Encoding;
use Erorus\CASC\Encoding\ContentMap;
use Erorus\CASC\HTTP;
use Erorus\CASC\Manifest;
use Erorus\CASC\Manifest\Install;
use Erorus\CASC\Manifest\Root;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Exception;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Throwable;

final class TactService
{
    public const DEFAULT_REGION       = 'eu';
    public const DEFAULT_TACT_KEY_URL = 'https://raw.githubusercontent.com/wowdev/TACTKeys/master/WoW.txt';

    /** @var array<string, mixed> */
    private static array $instanceCache = [];

    public function __construct(private Cache $cache)
    {
        //
    }

    public function getCdnHashFromContentHash(string $contentHash, string $product, string $region = self::DEFAULT_REGION): ?string
    {
        $encodingEntry = $this->getEncodingContentMap($contentHash, $product, $region);

        if (!$encodingEntry) {
            return null;
        }

        return bin2hex($encodingEntry->getEncodedHashes()[0]);
    }

    public function getEncodingContentMap(string $contentHash, string $product, string $region = self::DEFAULT_REGION, bool $preloadEncoding = false): ?ContentMap
    {
        $encoding = $this->getEncoding($product, $region, $preloadEncoding);
        return $encoding->getContentMap(hex2bin($contentHash));
    }

    public function getEncoding(string $product, string $region = self::DEFAULT_REGION, bool $preloadEncoding = false): Encoding
    {
        $cacheKey = sprintf('tact-encoding-%s-%s%s', $product, $region, $preloadEncoding ? '-preloaded' : '');
        if (!empty(self::$instanceCache[$cacheKey])) {
            return self::$instanceCache[$cacheKey];
        }

        $versionConfig = $this->getVersionConfig($product, $region);
        $buildConfig   = $this->getBuildConfig($product, $region);

        try {
            $encoding = new Encoding(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $buildConfig->encoding[1],
                true,
                $preloadEncoding
            );
        } catch (Throwable $throwable) {
            throw new TactException(sprintf("Failed to download encoding for product %s\n%s", $product, $throwable->getMessage()));
        }

        return self::$instanceCache[$cacheKey] = $encoding;
    }

    public function getVersionConfig(string $product, string $region = self::DEFAULT_REGION): HTTPVersionConfig
    {
        return LaravelCache::remember(sprintf('tact-versionConfig-%s-%s', $product, $region), 2 * CarbonInterface::SECONDS_PER_MINUTE, function () use ($product, $region): HTTPVersionConfig {
            $versionConfig = new HTTPVersionConfig($this->cache, $product, $region);

            $version         = $versionConfig->getVersion();
            $buildConfigHash = $versionConfig->getBuildConfig();
            $cdnConfigHash   = $versionConfig->getCDNConfig();

            if (empty($version) || empty($buildConfigHash) || empty($cdnConfigHash)) {
                throw new TactException(sprintf('Versions file product %s is empty', $product));
            }

            return $versionConfig;
        });
    }

    public function getBuildConfig(string $product, string $region = self::DEFAULT_REGION): Config
    {
        return LaravelCache::remember(sprintf('tact-buildconfig-%s-%s', $product, $region), 60 * CarbonInterface::SECONDS_PER_MINUTE, function () use ($product, $region): Config {
            $versionConfig = $this->getVersionConfig($product, $region);
            $buildConfig   = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $versionConfig->getBuildConfig());

            if (empty($buildConfig->getByKey('encoding'))) {
                throw new TactException(sprintf('Skip build %s in product %s because it is encrypted', $versionConfig->getBuildConfig(), $product));
            }

            return $buildConfig;
        });
    }

    public function getEncodingContentMapWithBuild(string $contentHash, Build $build, bool $preloadEncoding = false): ?ContentMap
    {
        $encoding = $this->getEncodingByBuild($build, $preloadEncoding);
        return $encoding->getContentMap(hex2bin($contentHash));
    }

    public function getEncodingByBuild(Build $build, bool $preloadEncoding = false): Encoding
    {
        $cacheKey = sprintf('tact-encoding-%s%s', $build->encodingCdnHash, $preloadEncoding ? '-preloaded' : '');
        if (!empty(self::$instanceCache[$cacheKey])) {
            return self::$instanceCache[$cacheKey];
        }

        $versionConfig = $this->getVersionConfig($build->productKey);

        try {
            $encoding = new Encoding(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $build->encodingCdnHash,
                true,
                $preloadEncoding
            );
        } catch (Throwable $throwable) {
            throw new TactException(sprintf("Failed to download encoding for product %s with build config key %s\n%s", $build->productKey, $build->buildConfig, $throwable->getMessage()));
        }

        return self::$instanceCache[$cacheKey] = $encoding;
    }

    public function downloadFileByNameOrId(string $nameOrId, string $destPath, string $product, string $region = self::DEFAULT_REGION, ?string $locale = null): bool
    {
        $contentHash = $this->getContentHash($nameOrId, $product, $region, $locale);

        if (is_null($contentHash)) {
            return false;
        }

        $contentHash = bin2hex($contentHash);

        if (file_exists($destPath) && md5_file($destPath) === $contentHash) {
            return true;
        }

        return $this->downloadFileByContentHash($contentHash, $destPath, $product, $region);
    }

    public function downloadFileByNameOrIdWithBuild(string $nameOrId, string $destPath, Build $build, ?string $locale = null): bool
    {
        $contentHash = $this->getContentHashWithBuild($nameOrId, $build);

        if (is_null($contentHash)) {
            return false;
        }

        $contentHash = bin2hex($contentHash);

        if (file_exists($destPath) && md5_file($destPath) === $contentHash) {
            return true;
        }

        return $this->downloadFileByContentHashWithBuild($contentHash, $destPath, $build);
    }

    public function getContentHash(string $nameOrId, string $product, string $region = self::DEFAULT_REGION, ?string $locale = null): ?string
    {
        /** @var Manifest[] $nameSources */
        $nameSources = [
            $this->getInstall($product, $region),
            $this->getRoot($product, $region),
        ];

        $this->downloadTactKeys();

        $contentHash = null;
        foreach ($nameSources as $nameSource) {
            if ($contentHash = $nameSource->getContentHash($nameOrId, $locale)) {
                break;
            }
        }

        return $contentHash;
    }

    public function getContentHashWithBuild(string $nameOrId, Build $build, ?string $locale = null): ?string
    {
        /** @var Manifest[] $nameSources */
        $nameSources = [
            $this->getInstallByBuild($build),
            $this->getRootByBuild($build),
        ];

        $this->downloadTactKeys();

        $contentHash = null;
        foreach ($nameSources as $nameSource) {
            if ($contentHash = $nameSource->getContentHash($nameOrId, $locale)) {
                break;
            }
        }

        return $contentHash;
    }

    private function getInstall(string $product, string $region = self::DEFAULT_REGION): Install
    {
        return LaravelCache::remember(sprintf('tact-install-%s-%s', $product, $region), 60 * CarbonInterface::SECONDS_PER_MINUTE, function () use ($product, $region): Install {
            $versionConfig = $this->getVersionConfig($product, $region);
            $buildConfig   = $this->getBuildConfig($product, $region);

            if (empty($buildConfig->getByKey('install')[1])) {
                throw new TactException(sprintf("Cant get install cdn key for product %s", $product));
            }

            try {
                $install = new Install(
                    $this->cache,
                    $versionConfig->getServers(),
                    $versionConfig->getCDNPath(),
                    $buildConfig->getByKey('install')[1]
                );
            } catch (Throwable $throwable) {
                throw new TactException(sprintf("Failed to download root for product %s\n%s", $product, $throwable->getMessage()));
            }

            return $install;
        });
    }

    private function getInstallByBuild(Build $build): Install
    {
        return LaravelCache::remember(sprintf('tact-install-%s', $build->installCdnHash), 60 * CarbonInterface::SECONDS_PER_MINUTE, function () use ($build): Install {
            $versionConfig = $this->getVersionConfig($build->productKey);


            try {
                $install = new Install(
                    $this->cache,
                    $versionConfig->getServers(),
                    $versionConfig->getCDNPath(),
                    $build->installCdnHash
                );
            } catch (Throwable $throwable) {
                throw new TactException(sprintf("Failed to download root for product %s with build config key %s\n%s", $build->productKey, $build->buildConfig, $throwable->getMessage()));
            }

            return $install;
        });
    }

    private function getRoot(string $product, string $region = self::DEFAULT_REGION): Root
    {
        $cacheKey = sprintf('tact-root-%s-%s', $product, $region);
        if (!empty(self::$instanceCache[$cacheKey])) {
            return self::$instanceCache[$cacheKey];
        }

        $versionConfig       = $this->getVersionConfig($product, $region);
        $buildConfig         = $this->getBuildConfig($product, $region);
        $rootEncodingMapping = $this->getEncodingContentMap($buildConfig->getByKey('root')[0], $product, $region);

        if (!$rootEncodingMapping) {
            throw new TactException(sprintf("Cant get root cdn key for product %s", $product));
        }

        try {
            $root = new Root(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                bin2hex($rootEncodingMapping->getEncodedHashes()[0])
            );
        } catch (Throwable $throwable) {
            throw new TactException(sprintf("Failed to download root for product %s\n%s", $product, $throwable->getMessage()));
        }

        return self::$instanceCache[$cacheKey] = $root;
    }

    private function downloadTactKeys(): int
    {
        $keys = LaravelCache::remember('tact-encryptionKeys', 60 * CarbonInterface::SECONDS_PER_MINUTE, static function (): array {
            $keys = [];

            try {
                $list = HTTP::get(env('TACT_KEY_SOURCE_URL', self::DEFAULT_TACT_KEY_URL));
            } catch (Exception $e) {
                echo $e->getMessage(), "\n";

                return [];
            }

            $lines = explode("\n", $list);
            foreach ($lines as $line) {
                if (preg_match('/([0-9A-F]{16})\s+([0-9A-F]{32})/i', $line, $match)) {
                    $keys[strrev(hex2bin($match[1]))] = hex2bin($match[2]);
                }
            }

            return $keys;
        });

        if ($keys) {
            BLTE::loadEncryptionKeys($keys);
        }

        return count($keys);
    }

    private function downloadFileByContentHash(string $contentHash, string $destPath, string $product, string $region = self::DEFAULT_REGION): bool
    {
        $contentMap = $this->getEncodingContentMap($contentHash, $product, $region);
        if (!$contentMap) {
            return false;
        }

        $dataSource = $this->getTact($product, $region);

        foreach ($contentMap->getEncodedHashes() as $hash) {
            try {
                if ($location = $dataSource->findHashInIndexes($hash)) {
                    if ($dataSource->extractFile($location, $destPath)) {
                        return true;
                    }
                }
            } catch (Exception) {
                //
            }
        }

        return false;
    }

    private function downloadFileByContentHashWithBuild(string $contentHash, string $destPath, Build $build): bool
    {
        $contentMap = $this->getEncodingContentMapWithBuild($contentHash, $build);
        if (!$contentMap) {
            return false;
        }

        $dataSource = $this->getTact($build->productKey);

        foreach ($contentMap->getEncodedHashes() as $hash) {
            try {
                if ($location = $dataSource->findHashInIndexes($hash)) {
                    if ($dataSource->extractFile($location, $destPath)) {
                        return true;
                    }
                }
            } catch (Exception) {
                //
            }
        }

        return false;
    }

    public function getTact(string $product, string $region = self::DEFAULT_REGION): TACT
    {
        $versionConfig = $this->getVersionConfig($product, $region);
        $cdnConfig     = $this->getCdnConfig($product, $region);
        $this->downloadTactKeys();

        return new TACT(
            $this->cache,
            $versionConfig->getServers(),
            $versionConfig->getCDNPath(),
            $cdnConfig->archives
        );
    }

    public function getCdnConfig(string $product, string $region = self::DEFAULT_REGION): Config
    {
        return LaravelCache::remember(sprintf('tact-cdnconfig-%s-%s', $product, $region), 60 * CarbonInterface::SECONDS_PER_MINUTE, function () use ($product, $region): Config {
            $versionConfig = $this->getVersionConfig($product, $region);
            $cdnConfig     = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $versionConfig->getCDNConfig());

            if (empty($cdnConfig->getByKey('archives'))) {
                throw new TactException(sprintf('Skip build %s in product %s because it is encrypted', $versionConfig->getBuildConfig(), $product));
            }

            return $cdnConfig;
        });
    }

    public function getRootByBuild(Build $build): Root
    {
        $cacheKey = sprintf('tact-root-%s', $build->rootCdnHash);
        if (!empty(self::$instanceCache[$cacheKey])) {
            return self::$instanceCache[$cacheKey];
        }

        $versionConfig = $this->getVersionConfig($build->productKey);

        try {
            $root = new Root(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $build->rootCdnHash
            );
        } catch (Throwable $throwable) {
            throw new TactException(sprintf("Failed to download root for product %s with build config key %s\n%s", $build->productKey, $build->buildConfig, $throwable->getMessage()));
        }

        return self::$instanceCache[$cacheKey] = $root;
    }
}