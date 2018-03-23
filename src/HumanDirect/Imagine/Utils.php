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
     * @param string $type
     *
     * @return array
     */
    public static function getLogoInfo(string $type): array
    {
        $logo = 'hd-watermark-300w.png';
        if ('horizontal' === $type) {
            $logo = 'hd-horizontal-watermark-300w.png';
        }

        return self::getImageInfo($logo);
    }

    /**
     * @param string $imagePath
     *
     * @return array
     */
    public static function getImageInfo(string $imagePath): array
    {
        $image = realpath(sprintf('images/%s', $imagePath));

        [$width, $height, $type] = getimagesize($image);

        return [
            'path' => $image,
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
    public static function wordwrap(string $string, int $width = 45, string $break = "\n")
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
}
