<?php

namespace HumanDirect\Imagine;

use Intervention\Image\ImageManager;

/**
 * Class ImageManagerAwareInterface.
 */
interface ImageManagerAwareInterface
{
    /**
     * @param ImageManager $manager
     */
    public function setImageManager(ImageManager $manager): void;
}
