<?php

namespace HumanDirect\Imagine;

/**
 * Class Utils
 *
 * @package HumanDirect\Imagine
 */
class Utils
{
    /**
     * @param string $url
     *
     * @return string
     */
    public static function getRedirectTarget(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = curl_exec($ch);
        curl_close($ch);

        // Check if there's a Location: header (redirect)
        if (preg_match('/^Location: (.+)$/im', $headers, $matches)) {
            return trim($matches[1]);
        }

        return $url;
    }

    /**
     * @param string $imagePath
     *
     * @return array
     */
    public static function getImageInfo(string $imagePath): array
    {
        [$width, $height, $type] = getimagesize($imagePath);

        return [
            'path' => $imagePath,
            'width' => $width,
            'height' => $height,
            'type' => $type,
        ];
    }

    /**
     * @param        $string
     * @param int    $width
     * @param string $break
     *
     * @return null|string|string[]
     */
    public static function wordwrap(string $string, int $width = 40, string $break = "\n")
    {
        // Anchor the beginning of the pattern with a lookahead
        // to avoid crazy backtracking when words are longer than $width
        $pattern = '/(?=\s)(.{1,' . $width . '})(?:\s|$)/uS';
        $replace = '$1' . $break;

        return preg_replace($pattern, $replace, $string);
    }

    /**
     * @param string $string
     * @param int    $desiredLength
     *
     * @return string
     */
    public static function truncate(string $string, int $desiredLength = 300): string
    {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = \count($parts);

        $length = 0;
        $last_part = 0;
        $truncated = false;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += \strlen($parts[$last_part]);
            if ($length > $desiredLength) {
                $truncated = true;
                break;
            }
        }

        return implode(\array_slice($parts, 0, $last_part)) . ($truncated ? ' ...' : '');
    }

    /**
     * Get center coordinated of a shape.
     *
     * @see http://webdevzoom.com/get-center-of-polygon-triangle-and-area-using-javascript-and-php/
     *
     * @param array $coord
     *
     * @return mixed
     */
    public static function centroid(array $coord)
    {
        $centroid = array_reduce($coord, function ($x, $y) use ($coord) {
            $len = \count($coord);

            return [$x[0] + $y[0] / $len, $x[1] + $y[1] / $len];
        }, [0, 0]);

        return $centroid;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public static function camelToKebab(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }
}
