<?php

namespace HumanDirect\Imagine;

/**
 * Class MediaType.
 */
class MediaType
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string
     */
    private $type;

    /**
     * MediaType constructor.
     *
     * @param string $type
     * @param int    $width
     * @param int    $height
     */
    public function __construct(string $type, int $width, int $height)
    {
        $this->type = $type;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get Width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get Height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Checks if instance is of supplied type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasType(string $type): bool
    {
        return $type === $this->getType();
    }

    /**
     * Get dimensions [int W, int H].
     *
     * @return array
     */
    public function getDimensions(): array
    {
        return [
            $this->width,
            $this->height,
        ];
    }
}
