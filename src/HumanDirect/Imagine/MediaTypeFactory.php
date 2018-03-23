<?php

namespace HumanDirect\Imagine;

/**
 * Class MediaType.
 */
class MediaTypeFactory
{
    /**
     * Create media type.
     *
     * @param string $name
     * @param int    $width
     * @param int    $height
     *
     * @return MediaType
     */
    public static function create(string $name, int $width, int $height): MediaType
    {
        return new MediaType($name, $width, $height);
    }

    /**
     * Create multiple media types.
     *
     * @param array $types
     *
     * @return MediaType[]
     */
    public static function createMultiple(array $types): array
    {
        $created = [];
        foreach ($types as $type) {
            [$name, $width, $height] = $type;
            $created[] = self::create($name, $width, $height);
        }

        return $created;
    }
}
