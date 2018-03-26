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
     * AbstractTheme constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
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
     * @param Image $image
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     */
    protected function drawControlRectangle(Image $image, int $x1, int $y1, int $x2, int $y2): void
    {
        $border = 2;
        $image->rectangle($x1, $y1, $x2, $y2, function (AbstractShape $draw) use ($border) {
            $draw->border($border, 'rgba(0, 0, 0, 0.5)');
            $draw->background('rgba(0, 0, 0, 0.3)');
        });
    }
}
