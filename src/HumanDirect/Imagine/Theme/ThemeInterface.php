<?php

namespace HumanDirect\Imagine\Theme;

use Intervention\Image\Image;

/**
 * Class ThemeInterface.
 */
interface ThemeInterface
{
    /**
     * Apply theme.
     *
     * @param Image $image
     * @param int   $width
     * @param int   $height
     *
     * @return Image
     */
    public function apply(Image $image, int $width, int $height): Image;

    /**
     * @param string $themeName
     *
     * @return bool
     */
    public function hasName(string $themeName): bool;
}
