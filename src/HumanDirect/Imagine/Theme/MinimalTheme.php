<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\BackgroundImageInterface;
use HumanDirect\Imagine\ImageManagerAwareInterface;
use HumanDirect\Imagine\ImagineException;
use HumanDirect\Imagine\RequestAwareInterface;
use Intervention\Image\Image;

/**
 * Class MinimalTheme.
 */
class MinimalTheme extends AbstractTheme implements AdaptiveThemeInterface
{
    /**
     * @var PositionAwareThemeInterface[]
     */
    private $supportedThemes = [];

    /**
     * @var ThemeInterface
     */
    private $theme;

    /**
     * Decide what theme to use.
     *
     * @param BackgroundImageInterface $bgImage
     * @param int                      $canvasWidth
     * @param int                      $canvasHeight
     *
     * @return ThemeInterface
     */
    public function decide(BackgroundImageInterface $bgImage, int $canvasWidth, int $canvasHeight): ThemeInterface
    {
        $this->loadThemes();

        $this->theme = $this->getTheme();
        if ($this->theme instanceof ImageManagerAwareInterface) {
            $this->theme->setImageManager($this->manager);
        }
        if ($this->theme instanceof RequestAwareInterface) {
            $this->theme->setRequest($this->request);
        }

        return $this->theme;
    }

    /**
     * Apply theme.
     *
     * @param Image $image
     * @param int   $width
     * @param int   $height
     *
     * @return Image
     */
    public function apply(Image $image, int $width, int $height): Image
    {
        return $this->theme->apply($image, $width, $height);
    }

    /**
     * @inheritdoc
     */
    public function supportsRandomization(): bool
    {
        return true;
    }

    /**
     * Load themes.
     */
    private function loadThemes(): void
    {
        $themeNS = '\\HumanDirect\\Imagine\\Theme\\';
        $directory = new \RecursiveDirectoryIterator(__DIR__, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($themeNS) {
            $className = str_replace('.php', '', $current->getFilename());
            $isTheme = 'Theme' === substr($className, -5);
            $isMinimal = 0 === strpos($className, 'Minimal');

            if (!$isTheme || !$isMinimal || $current->isDir() || $iterator->hasChildren()) {
                return false;
            }

            $r = new \ReflectionClass($themeNS . $className);

            return ($r->isInstantiable() && !$r->isSubclassOf(AdaptiveThemeInterface::class));
        });

        foreach ($files as $file) {
            $className = $themeNS . str_replace('.php', '', $file->getFilename());
            $this->supportedThemes[] = new $className();
        }
    }

    /**
     * Returns a random minimal theme
     *
     * @return ThemeInterface
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function getTheme(): ThemeInterface
    {
        if (!$this->supportedThemes) {
            throw new ImagineException('No supported themes.');
        }

        return $this->supportedThemes[array_rand($this->supportedThemes)];
    }
}
