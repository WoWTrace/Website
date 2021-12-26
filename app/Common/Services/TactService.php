<?php


namespace App\Common\Services;


use App\Common\Exceptions\TactException;
use Erorus\CASC\Cache;
use Erorus\CASC\Config;
use Erorus\CASC\Encoding;
use Erorus\CASC\Encoding\ContentMap;
use Erorus\CASC\VersionConfig\HTTP as HTTPVersionConfig;
use Throwable;

final class TactService
{
    public const DEFAULT_REGION = 'eu';

    /** @var array<string, HTTPVersionConfig> */
    private static array $versionConfigs = [];

    /** @var array<string, Encoding> */
    private static array $encodings = [];

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

    public function getBuildConfig(string $product, string $region = self::DEFAULT_REGION)
    {
        $versionConfig = $this->getVersionConfig($product, $region);
        $buildConfig   = new Config($this->cache, $versionConfig->getServers(), $versionConfig->getCDNPath(), $versionConfig->getBuildConfig());

        if (empty($buildConfig->getByKey('encoding'))) {
            throw new TactException(sprintf('Skip build %s in product %s because it is encrypted', $versionConfig->getBuildConfig(), $product));
        }

        return $buildConfig;
    }
}