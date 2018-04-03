<?php

namespace HumanDirect\Imagine;

use HumanDirect\Imagine\Theme\AdaptiveThemeInterface;
use HumanDirect\Imagine\Theme\ThemeInterface;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Class Canvas.
 */
class Canvas implements CanvasInterface, ImageManagerAwareInterface, RequestAwareInterface
{
    use ImageManagerAwareTrait, RequestAwareTrait;

    public const MEDIA_FB_SHARED_IMAGE = 'facebook_shared_image';
    public const MEDIA_FB_SHARED_LINK = 'facebook_shared_link';
    public const MEDIA_TW_SHARED_IMAGE = 'twitter_shared_image';
    public const MEDIA_TW_SHARED_LINK = 'twitter_shared_link';
    public const MEDIA_LI_SHARED_IMAGE = 'linkedin_shared_image';
    public const MEDIA_LI_SHARED_LINK = 'linkedin_shared_link';
    public const MEDIA_DEFAULT_SIZE = self::MEDIA_LI_SHARED_LINK;

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
     * @var bool
     */
    private $initialized = false;

    /**
     * @var bool
     */
    private $built = false;

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
    public function create(): CanvasInterface
    {
        return $this->doCreate();
    }

    /**
     * Apply theme.
     *
     * @param ThemeInterface $theme
     *
     * @return CanvasInterface
     */
    public function applyTheme(ThemeInterface $theme): CanvasInterface
    {
        if ($theme instanceof AdaptiveThemeInterface) {
            $theme = $theme->decide(
                $this->backgroundImage,
                $this->getWidth(),
                $this->getHeight()
            );
        }

        $this->image = $theme->apply(
            $this->image,
            $this->getWidth(),
            $this->getHeight()
        );

        //header('X-Theme: ' . $theme->getName());

        return $this;
    }

    /**
     * Draw final canvas.
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    public function draw(): Image
    {
        return $this->doDraw();
    }

    /**
     * Get canvas width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->built ? $this->width : $this->initWidth;
    }

    /**
     * Get canvas height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->built ? $this->height : $this->initHeight;
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
     * @return CanvasInterface
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function doCreate(): CanvasInterface
    {
        $this->initialize();
        $this->build();

        return $this;
    }

    /**
     * Finalize canvas.
     *
     * @return Image
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function doDraw(): Image
    {
        if ($this->initWidth !== $this->width || $this->initHeight !== $this->height) {
            $this->image->resize($this->width, $this->height);
        }

        $debug = (bool) $this->request->get('debug', false);
        if ($debug) {
            $w = $this->image->getWidth();
            $h = $this->image->getHeight();
            $midX = (int)floor($w/2);
            $midH = (int)floor($h/2);
            $sizeFontSize = 20;

            $this->image->rectangle($midX-55, $midH-45, $midX+55, $midH+25, function (AbstractShape $draw) {
                $draw->border(2, 'rgba(255, 255, 255, 0.8)');
                $draw->background('rgba(255, 255, 255, 0.5)');
            });

            $sizeCallback = function (AbstractFont $font) use ($sizeFontSize) {
                $dir = __DIR__;
                $rootPath = \dirname($dir, 3) . '/';

                $font->file(sprintf('%s/fonts/%s', $rootPath, 'SourceSansPro-Bold.otf'));
                $font->size($sizeFontSize);
                $font->color('#000000');
                $font->align('center');
                $font->valign('top');
            };
            $this->image->text(sprintf('%s X %s', $w, $h), $midX, $midH-30, $sizeCallback);
            $this->image->text(sprintf('%s:%s', round($w/$h, 2), 1), $midX, $midH-10, $sizeCallback);
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
            [self::MEDIA_TW_SHARED_IMAGE, 1024, 512],
            [self::MEDIA_TW_SHARED_LINK, 520, 254],
            [self::MEDIA_LI_SHARED_IMAGE, 520, 320],
            [self::MEDIA_LI_SHARED_LINK, 1200, 627]
        ]);

        foreach ($mediaTypes as $mediaType) {
            $this->addSupportedMediaType($mediaType);
        }

        $this->initialized = true;
    }

    /**
     * Build the canvas.
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function build(): void
    {
        // init dimensions are the largest possible
        [$this->initWidth, $this->initHeight] = $this->getMediaType(self::MEDIA_DEFAULT_SIZE)->getDimensions();

        // final dimensions of the image
        [$this->width, $this->height] = $this->getMediaType($this->mediaType)->getDimensions();

        $this->image = $this->manager
            ->make($this->backgroundImage->getUrl())
            ->fit($this->initWidth, $this->initHeight)
            ->crop($this->initWidth, $this->initHeight);

        $this->built = true;
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
