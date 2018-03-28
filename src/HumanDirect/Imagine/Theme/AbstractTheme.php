<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\ImageManagerAwareInterface;
use HumanDirect\Imagine\ImageManagerAwareTrait;
use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractTheme.
 */
abstract class AbstractTheme implements ThemeInterface, ImageManagerAwareInterface
{
    use ImageManagerAwareTrait;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * AbstractTheme constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();

        $dir = __DIR__; // workaround for xDebug, __DIR__ is overwritten during a debug session!
        $this->rootPath = \dirname($dir, 4) . '/';
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        $shortName = (new \ReflectionClass($this))->getShortName();
        $className = substr($shortName, 0, -5);

        return Utils::camelToKebab($className);
    }

    /**
     * @inheritdoc
     */
    public function hasName(string $themeName): bool
    {
        return $this->getName() === $themeName;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    protected function getImagePath(string $image): string
    {
        return sprintf('%s/images/%s', $this->rootPath, $image);
    }

    /**
     * @param string $font
     *
     * @return string
     */
    protected function getFontPath(string $font): string
    {
        return sprintf('%s/fonts/%s', $this->rootPath, $font);
    }

    /**
     * @param Image  $image
     * @param int    $x1
     * @param int    $y1
     * @param int    $x2
     * @param int    $y2
     * @param string $rgbColor
     */
    protected function drawControlRectangle(Image $image, int $x1, int $y1, int $x2, int $y2, string $rgbColor = '0, 0, 0'): void
    {
        $border = 2;
        $image->rectangle($x1, $y1, $x2, $y2, function (AbstractShape $draw) use ($border, $rgbColor) {
            $draw->border($border, 'rgba('.$rgbColor.', 0.5)');
            $draw->background('rgba('.$rgbColor.', 0.3)');
        });
    }
}
