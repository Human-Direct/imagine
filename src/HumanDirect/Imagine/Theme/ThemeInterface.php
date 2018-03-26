<?php

namespace HumanDirect\Imagine\Theme;

use Intervention\Image\Image;

/**
 * Class ThemeInterface.
 */
interface ThemeInterface
{
    public const RANDOM_THEME_NAME = 'random';

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
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $themeName
     *
     * @return bool
     */
    public function hasName(string $themeName): bool;

    /**
     * Does theme support randomization?
     *
     * @return bool
     */
    public function supportsRandomization(): bool;
}
