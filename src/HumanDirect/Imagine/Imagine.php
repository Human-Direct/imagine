<?php

namespace HumanDirect\Imagine;

use HumanDirect\Imagine\Theme\ThemeInterface;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Imagine.
 */
class Imagine implements RequestAwareInterface
{
    use RequestAwareTrait;

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
     * @var ThemeInterface[]
     */
    private $randomizableThemes;

    /**
     * Imagine constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->setRequest($request);
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
        if ($this->canvas instanceof RequestAwareInterface) {
            $this->canvas->setRequest($this->request);
        }

        $theme = $this->getTheme($themeName);
        if ($theme instanceof ImageManagerAwareInterface) {
            $theme->setImageManager($this->manager);
        }
        if ($theme instanceof RequestAwareInterface) {
            $theme->setRequest($this->request);
        }

        return $this->canvas
            ->create()
            ->applyTheme($theme)
            ->draw();
    }

    /**
     * Load themes.
     */
    private function loadThemes(): void
    {
        $themeNS = '\\HumanDirect\\Imagine\\Theme\\';
        $directory = new \RecursiveDirectoryIterator(
            __DIR__ . '/Theme',
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $files = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($themeNS) {
            $className = str_replace('.php', '', $current->getFilename());
            $isTheme = 'Theme' === substr($className, -5);

            if (!$isTheme || $current->isDir() || $iterator->hasChildren()) {
                return false;
            }

            $r = new \ReflectionClass($themeNS . $className);

            return $r->isInstantiable();
        });

        foreach ($files as $file) {
            $className = $themeNS . str_replace('.php', '', $file->getFilename());
            /** @var ThemeInterface $object */
            $object = new $className($this->request);
            $this->supportedThemes[] = $object;

            if ($object->supportsRandomization()) {
                $this->randomizableThemes[] = $object;
            }
        }
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
        if (ThemeInterface::RANDOM_THEME_NAME === $themeName) {
            return $this->randomizableThemes[array_rand($this->randomizableThemes)];
        }

        foreach ($this->supportedThemes as $theme) {
            if ($theme->hasName($themeName)) {
                return $theme;
            }
        }

        throw new ImagineException('Unsupported theme.');
    }
}
