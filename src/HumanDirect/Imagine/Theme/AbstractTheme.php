<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\ImageManagerAwareInterface;
use HumanDirect\Imagine\ImageManagerAwareTrait;
use HumanDirect\Imagine\Utils;
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
}
