<?php

namespace HumanDirect\Imagine;

use Intervention\Image\ImageManager;

/**
 * Class ImageManagerAwareTrait.
 */
trait ImageManagerAwareTrait
{
    /**
     * @var ImageManager
     */
    private $manager;

    /**
     * @param ImageManager $manager
     */
    public function setImageManager(ImageManager $manager): void
    {
        $this->manager = $manager;
    }
}
