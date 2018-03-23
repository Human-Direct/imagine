<?php

namespace HumanDirect\Imagine;

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Class Canvas.
 */
class Canvas implements CanvasInterface, ImageManagerAwareInterface
{
    use ImageManagerAwareTrait;

    public const MEDIA_FB_SHARED_IMAGE = 'facebook_shared_image';
    public const MEDIA_FB_SHARED_LINK = 'facebook_shared_link';
    public const MEDIA_FB_HIGHLIGHTED_IMAGE = 'facebook_highlighted_image';
    public const MEDIA_TW_SHARED_IMAGE = 'twitter_shared_image';
    public const MEDIA_TW_SHARED_LINK = 'twitter_shared_link';
    public const MEDIA_LI_SHARED_IMAGE = 'linkedin_shared_image';
    public const MEDIA_LI_SHARED_LINK = 'linkedin_shared_link';

    /**
     * @var BackgroundImageInterface
     */
    private $backgroundImage;

    /**
     * @var string
     */
    private $mediaType;

    /**
     * @var MediaType[]
     */
    private $supportedMediaTypes = [];

    /**
     * @var int
     */
    private $initWidth;

    /**
     * @var int
     */
    private $initHeight;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var Image
     */
    private $image;

    /**
     * Canvas constructor.
     *
     * @param BackgroundImageInterface $bgImage
     * @param string                   $mediaType
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function __construct(BackgroundImageInterface $bgImage, string $mediaType)
    {
        $this->backgroundImage = $bgImage;
        $this->mediaType = $mediaType;
    }

    /**
     * Create canvas containing background image.
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function create(): Image
    {
        return $this->doCreate();
    }

    /**
     * Get canvas width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->initWidth;
    }

    /**
     * Get canvas height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->initHeight;
    }

    /**
     * Add media type.
     *
     * @param MediaType $mediaType
     */
    protected function addSupportedMediaType(MediaType $mediaType): void
    {
        $this->supportedMediaTypes[] = $mediaType;
    }

    /**
     * Handle creation of canvas.
     *
     * @return Image
     *
     * @throws \HumanDirect\Imagine\ImagineException
     *
     * TODO: cropping should happen at the end, after theme is applied
     */
    private function doCreate(): Image
    {
        $this->initialize();
        $this->build();

        if ($this->initWidth !== $this->width || $this->initHeight !== $this->height) {
            $this->image->resize($this->width, $this->height, function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $this->image;
    }

    /**
     * Initialize the canvas.
     *
     * @see https://postcron.com/en/blog/infographics-social-media-image-sizes/
     */
    private function initialize(): void
    {
        $mediaTypes = MediaTypeFactory::createMultiple([
            [self::MEDIA_FB_SHARED_IMAGE, 1200, 630],
            [self::MEDIA_FB_SHARED_LINK, 1200, 630],
            [self::MEDIA_FB_HIGHLIGHTED_IMAGE, 1200, 717],
            [self::MEDIA_TW_SHARED_IMAGE, 1024, 512],
            [self::MEDIA_TW_SHARED_LINK, 520, 254],
            [self::MEDIA_LI_SHARED_IMAGE, 520, 320],
            [self::MEDIA_LI_SHARED_LINK, 520, 272]
        ]);

        foreach ($mediaTypes as $mediaType) {
            $this->addSupportedMediaType($mediaType);
        }
    }

    /**
     * Build the canvas.
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function build(): void
    {
        // init dimensions are the largest possible
        [$this->initWidth, $this->initHeight] = $this->getMediaType(self::MEDIA_FB_HIGHLIGHTED_IMAGE)->getDimensions();

        // final dimensions of the image
        [$this->width, $this->height] = $this->getMediaType($this->mediaType)->getDimensions();

        $this->image = $this->manager
            ->make($this->backgroundImage->getUrl())
            ->crop($this->initWidth, $this->initHeight);
    }

    /**
     * @param string $type
     *
     * @return MediaType
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function getMediaType(string $type): MediaType
    {
        foreach($this->supportedMediaTypes as $mediaType) {
            if ($mediaType->hasType($type)) {
                return $mediaType;
            }
        }

        throw new ImagineException('Unsupported media type.');
    }
}
