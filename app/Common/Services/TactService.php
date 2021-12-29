<?php


namespace App\Common\Services;


use App\Common\Exceptions\TactException;
use Erorus\CASC\BLTE;
use Erorus\CASC\Cache;
use Erorus\CASC\Config;
use Erorus\CASC\DataSource\TACT;
use Erorus\CASC\Encoding;
use Erorus\CASC\Encoding\ContentMap;
use Erorus\CASC\HTTP;
use Erorus\CASC\Manifest;
use Erorus\CASC\Manifest\Root;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Exception;
use Throwable;

final class TactService
{
    public const DEFAULT_REGION       = 'eu';
    public const DEFAULT_TACT_KEY_URL = 'https://raw.githubusercontent.com/wowdev/TACTKeys/master/WoW.txt';

    /** @var array<string, HTTPVersionConfig> */
    private static array $versionConfigs = [];

    /** @var array<string, Encoding> */
    private static array $encodings = [];

    /** @var array<string, TACT> */
    private static array $tact = [];

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

    public function getEncodingContentMap(string $contentHash, string $product, string $region = self::DEFAULT_REGION): ?ContentMap
    {
        $encoding = $this->getEncoding($product, $region);
        return $encoding->getContentMap(hex2bin($contentHash));
    }

    public function getEncoding(string $product, string $region = self::DEFAULT_REGION): Encoding
    {
        $cacheKey = sprintf('%s-%s', $product, $region);
        if (!empty(self::$encodings[$cacheKey])) {
            return self::$encodings[$cacheKey];
        }

        $versionConfig = $this->getVersionConfig($product, $region);
        $buildConfig   = $this->getBuildConfig($product, $region);

        try {
            $encoding = new Encoding(
                $this->cache,
                $versionConfig->getServers(),
                $versionConfig->getCDNPath(),
                $buildConfig->encoding[1],
                true
            );
        } catch (Throwable $throwable) {
            throw new TactException(sprintf("Failed to download encoding for product %s\n%s", $product, $throwable->getMessage()));
        }

        return self::$encodings[$cacheKey] = $encoding;
    }

    public function getVersionConfig(string $product, string $region = self::DEFAULT_REGION): HTTPVersionConfig
    {
        $cacheKey = sprintf('%s-%s', $product, $region);
        if (!empty(self::$versionConfigs[$cacheKey])) {
            return self::$versionConfigs[$cacheKey];
        }

        $versionConfig = new HTTPVersionConfig($this->cache, $product, $region);

        $version         = $versionConfig->getVersion();
        $buildConfigHash = $versionConfig->getBuildConfig();
        $cdnConfigHash   = $versionConfig->getCDNConfig();

        if (empty($version) || empty($buildConfigHash) || empty($cdnConfigHash)) {
            throw new TactException(sprintf('Versions file product %s is empty', $product));
        }

        return self::$versionConfigs[$cacheKey] = $versionConfig;
    }

    public function getBuildConfig(string $product, string $region = self::DEFAULT_REGION): Config
    {
        $versionConfig = $this->getVersionConfig($product, $region);
        $buildConfig   = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $versionConfig->getBuildConfig());

        if (empty($buildConfig->getByKey('encoding'))) {
            throw new TactException(sprintf('Skip build %s in product %s because it is encrypted', $versionConfig->getBuildConfig(), $product));
        }

        return $buildConfig;
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

    public function getContentHash(string $nameOrId, string $product, string $region = self::DEFAULT_REGION, ?string $locale = null): ?string
    {
        /** @var Manifest[] $nameSources */
        $nameSources = [
            $this->getRoot($product, $region)
        ];

        $contentHash = null;
        foreach ($nameSources as $nameSource) {
            if ($contentHash = $nameSource->getContentHash($nameOrId, $locale)) {
                break;
            }
        }

        return $contentHash;
    }

    private function getRoot(string $product, string $region = self::DEFAULT_REGION): Root
    {
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

        return $root;
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
                    if ($dataSource->extractFile($location, $destPath, $contentHash)) {
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
        $cacheKey = sprintf('%s-%s', $product, $region);
        if (!empty(self::$tact[$cacheKey])) {
            return self::$tact[$cacheKey];
        }

        $versionConfig = $this->getVersionConfig($product, $region);
        $cdnConfig     = $this->getCdnConfig($product, $region);
        $this->downloadTactKeys();

        return self::$tact[$cacheKey] = new TACT(
            $this->cache,
            $versionConfig->getServers(),
            $versionConfig->getCDNPath(),
            $cdnConfig->archives
        );
    }

    public function getCdnConfig(string $product, string $region = self::DEFAULT_REGION): Config
    {
        $versionConfig = $this->getVersionConfig($product, $region);
        $cdnConfig     = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $versionConfig->getCDNConfig());

        if (empty($cdnConfig->getByKey('archives'))) {
            throw new TactException(sprintf('Skip build %s in product %s because it is encrypted', $versionConfig->getBuildConfig(), $product));
        }

        return $cdnConfig;
    }

    private function downloadTactKeys(): int
    {
        $keys = [];
        try {
            $list = HTTP::get(env('TACT_KEY_SOURCE_URL', self::DEFAULT_TACT_KEY_URL));
        } catch (Exception $e) {
            echo $e->getMessage(), "\n";

            return 0;
        }

        $lines = explode("\n", $list);
        foreach ($lines as $line) {
            if (preg_match('/([0-9A-F]{16})\s+([0-9A-F]{32})/i', $line, $match)) {
                $keys[strrev(hex2bin($match[1]))] = hex2bin($match[2]);
            }
        }

        if ($keys) {
            BLTE::loadEncryptionKeys($keys);
        }

        return count($keys);
    }
}