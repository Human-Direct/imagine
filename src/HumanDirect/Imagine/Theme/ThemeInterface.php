<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Canvas;
use Intervention\Image\Image;

/**
 * Class ThemeInterface.
 */
interface ThemeInterface
{
    /**
     * Apply theme to canvas.
     *
     * @param Canvas $canvas
     *
     * @return Image
     */
    public function apply(Canvas $canvas): Image;

    /**
     * @param string $themeName
     *
     * @return bool
     */
    public function hasName(string $themeName): bool;
}
