<?php

namespace HumanDirect\Imagine\Theme;

/**
 * Class PositionAwareThemeInterface.
 */
interface PositionAwareThemeInterface extends ThemeInterface
{
    public const POSITION_LEFT = 'left';
    public const POSITION_CENTER = 'center';
    public const POSITION_RIGHT = 'right';
    public const SUPPORTED_POSITIONS = [
        self::POSITION_LEFT,
        self::POSITION_CENTER,
        self::POSITION_RIGHT,
    ];

    /**
     * @return string
     */
    public function getPosition(): string;
}
