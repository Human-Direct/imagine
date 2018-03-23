<?php

namespace HumanDirect\Imagine;

use Intervention\Image\Image;

/**
 * Interface CanvasInterface.
 */
interface CanvasInterface
{
    /**
     * Create canvas containing background image.
     */
    public function create(): Image;

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
