<?php

namespace HumanDirect\Imagine;

/**
 * Class UnsplashImage
 *
 * @package HumanDirect\Imagine
 */
class UnsplashImage
{
    public const SOURCE_PUBLIC = 'featured';
    public const SOURCE_PRIVATE_COLLECTION = 'private_collection';
    public const SOURCE_RANDOM = 'random';

    public const SUPPORTED_SOURCES = [
        self::SOURCE_PRIVATE_COLLECTION,
        self::SOURCE_PUBLIC,
        self::SOURCE_RANDOM,
    ];
    private const SOURCE_URLS = [
        self::SOURCE_PUBLIC => 'https://source.unsplash.com/featured?internet,office,computer',
        self::SOURCE_PRIVATE_COLLECTION => 'https://source.unsplash.com/collection/1896279',
    ];

    private $source;

    /**
     * UnsplashImage constructor.
     *
     * @param string $source
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function __construct(string $source = self::SOURCE_PUBLIC)
    {
        if (!\in_array($source, self::SUPPORTED_SOURCES, true)) {
            throw new ImagineException(sprintf('Source not supported. Use one of: %s', implode(self::SUPPORTED_SOURCES)));
        }

        $this->source = $source;
    }

    /**
     * @param string $source
     *
     * @return UnsplashImage
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public static function createFromSource(string $source): UnsplashImage
    {
        return new static($source);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $sourceUrl = $this->getSourceUrl();

        return Utils::getRedirectTarget($sourceUrl);
    }

    /**
     * @return string
     */
    private function getSourceUrl(): string
    {
        $source = $this->source;
        if (self::SOURCE_RANDOM === $source) {
            $key = array_rand(array_keys(self::SOURCE_URLS));
            $source = self::SUPPORTED_SOURCES[$key];
        }

        return self::SOURCE_URLS[$source];
    }
}
