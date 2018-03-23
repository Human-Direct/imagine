<?php

namespace HumanDirect\Imagine;
use HumanDirect\Imagine\Theme\DefaultTheme;
use HumanDirect\Imagine\Theme\ThemeInterface;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Class Imagine.
 */
class Imagine
{
    /**
     * @var Canvas
     */
    private $canvas;

    /**
     * @var ImageManager
     */
    private $manager;

    /**
     * @var ThemeInterface[]
     */
    private $supportedThemes;

    /**
     * Imagine constructor.
     */
    public function __construct()
    {
        $this->manager = new ImageManager(['driver' => 'imagick']);

        $this->loadThemes();
    }

    /**
     * Create image.
     *
     * @param BackgroundImageInterface $bgImage
     * @param string                   $mediaType
     * @param string                   $themeName
     *
     * @return Image
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function create(BackgroundImageInterface $bgImage, string $mediaType, string $themeName): Image
    {
        $this->canvas = new Canvas($bgImage, $mediaType);
        if ($this->canvas instanceof ImageManagerAwareInterface) {
            $this->canvas->setImageManager($this->manager);
        }

        return $this->getTheme($themeName)->apply($this->canvas);
    }

    /**
     * Load themes.
     *
     * TODO: Scan themes directory for classes and load all of them.
     */
    private function loadThemes(): void
    {
        $this->supportedThemes = [
            new DefaultTheme()
        ];
    }

    /**
     * @param string $themeName
     *
     * @return ThemeInterface
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function getTheme(string $themeName): ThemeInterface
    {
        foreach($this->supportedThemes as $theme) {
            if ($theme->hasName($themeName)) {
                if ($theme instanceof ImageManagerAwareInterface) {
                    $theme->setImageManager($this->manager);
                }

                return $theme;
            }
        }

        throw new ImagineException('Unsupported theme.');
    }
}
