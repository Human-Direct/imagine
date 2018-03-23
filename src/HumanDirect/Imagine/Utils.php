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
}
