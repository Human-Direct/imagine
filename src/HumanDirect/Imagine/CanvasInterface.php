<?php

namespace HumanDirect\Imagine;

use HumanDirect\Imagine\Theme\ThemeInterface;
use Intervention\Image\Image;

/**
 * Interface CanvasInterface.
 */
interface CanvasInterface
{
    /**
     * Create canvas containing background image.
     */
    public function create(): CanvasInterface;

    /**
     * Apply theme.
     *
     * @param ThemeInterface $theme
     *
     * @return CanvasInterface
     */
    public function applyTheme(ThemeInterface $theme): CanvasInterface;

    /**
     * Draw canvas.
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function draw(): Image;

    /**
     * Get canvas width.
     *
     * @return int
     */
    public function getWidth(): int;

    /**
     * Get canvas height.
     *
     * @return int
     */
    public function getHeight(): int;
}
