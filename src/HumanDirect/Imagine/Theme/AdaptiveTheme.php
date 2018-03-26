<?php

namespace HumanDirect\Imagine\Theme;

use Google\Cloud\Vision\VisionClient;
use HumanDirect\Imagine\BackgroundImageInterface;
use HumanDirect\Imagine\ImageManagerAwareInterface;
use HumanDirect\Imagine\ImagineException;
use HumanDirect\Imagine\Utils;
use Intervention\Image\Image;

/**
 * Class AdaptiveTheme.
 */
class AdaptiveTheme extends AbstractTheme implements AdaptiveThemeInterface
{
    /**
     * @var PositionAwareThemeInterface[]
     */
    private $supportedThemes = [];

    /**
     * @var PositionAwareThemeInterface
     */
    private $theme;

    /**
     * Decide what theme to use.
     *
     * @param BackgroundImageInterface $bgImage
     * @param int                      $canvasWidth
     * @param int                      $canvasHeight
     *
     * @return PositionAwareThemeInterface
     */
    public function decide(BackgroundImageInterface $bgImage, int $canvasWidth, int $canvasHeight): PositionAwareThemeInterface
    {
        $this->loadThemes();

        $position = $this->getSuggestedPosition($bgImage, $canvasWidth, $canvasHeight);

        $this->theme = $this->getTheme($position);
        if ($this->theme instanceof ImageManagerAwareInterface) {
            $this->theme->setImageManager($this->manager);
        }

        return $this->theme;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return sprintf('%s-%s', parent::getName(), $this->theme->getName());
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

            if (!$isTheme || $current->isDir() || $iterator->hasChildren()) {
                return false;
            }

            $r = new \ReflectionClass($themeNS . $className);

            return ($r->isInstantiable()
                && $r->isSubclassOf(PositionAwareThemeInterface::class)
                && !$r->isSubclassOf(AdaptiveThemeInterface::class)
            );
        });

        foreach ($files as $file) {
            $className = $themeNS . str_replace('.php', '', $file->getFilename());
            $this->supportedThemes[] = new $className();
        }
    }

    /**
     * @param string $suggestedPosition
     *
     * @return PositionAwareThemeInterface
     *
     * @throws \HumanDirect\Imagine\ImagineException
     */
    private function getTheme(string $suggestedPosition): PositionAwareThemeInterface
    {
        if (!$this->supportedThemes) {
            throw new ImagineException('No supported themes.');
        }

        foreach ($this->supportedThemes as $theme) {
            if ($suggestedPosition === $theme->getPosition()) {
                return $theme;
            }
        }

        return $this->supportedThemes[0];
    }

    /**
     * @param BackgroundImageInterface $bgImage
     * @param int                      $canvasWidth
     * @param int                      $canvasHeight
     *
     * @return string|null
     */
    private function getSuggestedPosition(BackgroundImageInterface $bgImage, int $canvasWidth, int $canvasHeight): ?string
    {
        // By default set position to a random supported one
        $positions = PositionAwareThemeInterface::SUPPORTED_POSITIONS;
        $position = $positions[array_rand($positions)];

        $projectId = 'human-direct';
        $vision = new VisionClient(['projectId' => $projectId]);

        $image = $vision->image(file_get_contents($bgImage->getUrl()), ['FACE_DETECTION']);
        $result = $vision->annotate($image);

        $annotations = $result->info();
        if ($annotations && $annotations['faceAnnotations']) {
            $leftSupLimit = (int)floor($canvasWidth * 0.33);
            $rightInfLimit = (int)floor($canvasWidth * 0.66);
            $facialCoords = [
                PositionAwareThemeInterface::POSITION_LEFT => [],
                PositionAwareThemeInterface::POSITION_CENTER => [],
                PositionAwareThemeInterface::POSITION_RIGHT => [],
            ];

            foreach ($annotations['faceAnnotations'] as $annotation) {
                if (isset($annotation['boundingPoly'])) {
                    $verticies = $annotation['boundingPoly']['vertices'];
                    $xTL = $verticies[0]['x'] ?? 0;
                    $yTL = $verticies[0]['y'] ?? 0;
                    $xBR = $verticies[2]['x'] ?? 0;
                    $yBR = $verticies[2]['y'] ?? 0;

                    [$centerX, $centerY] = Utils::centroid([
                        [$xTL, $yTL],
                        [$xBR, $yBR],
                    ]);

                    $faceCoords = [$centerX, $centerY];
                    if ($centerX > $leftSupLimit && $centerX < $rightInfLimit) {
                        $facialCoords[PositionAwareThemeInterface::POSITION_CENTER][] = $faceCoords;
                    } else if ($centerX <= $leftSupLimit) {
                        $facialCoords[PositionAwareThemeInterface::POSITION_LEFT][] = $faceCoords;
                    } else if ($centerX >= $rightInfLimit) {
                        $facialCoords[PositionAwareThemeInterface::POSITION_RIGHT][] = $faceCoords;
                    }
                }
            }

            $leftFaces = \count($facialCoords[PositionAwareThemeInterface::POSITION_LEFT]);
            //$centerFaces = \count($facialCoords[PositionAwareThemeInterface::POSITION_CENTER]);
            $rightFaces = \count($facialCoords[PositionAwareThemeInterface::POSITION_RIGHT]);

            if ($leftFaces > $rightFaces) {
                return PositionAwareThemeInterface::POSITION_RIGHT;
            }

            // TODO: in case numbers are equal, get the 2 most extreme points on either side and favour the most far from edges to be revealed (box position at the opposite end)
//            if ($leftFaces === $rightFaces) {
//
//            }

            return PositionAwareThemeInterface::POSITION_LEFT;
        }

        return $position;
    }
}
