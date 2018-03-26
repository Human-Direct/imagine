<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\BackgroundImageInterface;

/**
 * Class AdaptiveThemeInterface.
 */
interface AdaptiveThemeInterface extends ThemeInterface
{
    /**
     * Decide what theme to use.
     *
     * @param BackgroundImageInterface $bgImage
     * @param int                      $canvasWidth
     * @param int                      $canvasHeight
     *
     * @return PositionAwareThemeInterface
     */
    public function decide(BackgroundImageInterface $bgImage, int $canvasWidth, int $canvasHeight): PositionAwareThemeInterface;
}
