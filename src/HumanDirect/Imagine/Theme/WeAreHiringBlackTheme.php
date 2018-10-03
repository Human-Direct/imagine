<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;

/**
 * Class WeAreHiringBlackTheme.
 */
class WeAreHiringBlackTheme extends AbstractTheme
{
    protected $boxBgColor = 'rgba(0, 0, 0, 0.7)';
    protected $titleColor = '#ffffff';
    protected $primaryColor = '#ffffff';
    protected $secondaryColor = 'rgba(255, 255, 255, 0.3)';

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
        $w = $width;
        $h = $height;

        $jobTitle = $this->request->get('jobTitle');
        $location = $this->request->get('location');
        $jobCode = $this->request->get('jobCode');

        // boxBgColor overlay
        $rectW = $w;
        $rectH = $h;
        $image->rectangle(0, 0, $rectW, $rectH, function (AbstractShape $draw) {
            $draw->background($this->boxBgColor);
        });

        $padLeft = 50;
        $padRight = $padLeft;
        $wereHiringSize = 60;
        $titleSize = 60;
        $subtitleSize = 25;
        $padCenterTop = 20;

        $logoPath = $this->getImagePath('hd-horizontal-watermark-300w.png');
        $logoInfo = Utils::getImageInfo($logoPath);
        $logoHeight = $logoInfo['height'];
        $sepH = 5;
        $sepY1 = abs($rectH - $logoHeight*2);
        $sepY2 = $sepY1 - $sepH;

        if (null !== $jobTitle) {
            // we're hiring text
            $wereHiringCallback = function (AbstractFont $font) use ($wereHiringSize) {
                $font->file($this->getFontPath('OpenSans-Bold.ttf'));
                $font->size($wereHiringSize);
                $font->color($this->titleColor);
                $font->align('center');
                $font->valign('top');
            };
            $image->text('We\'re Hiring', floor($rectW/2), floor($rectH/2)-($titleSize+floor($wereHiringSize*1.2))-$padCenterTop, $wereHiringCallback);

            // job title
            $titleCallback = function (AbstractFont $font) use ($titleSize) {
                $font->file($this->getFontPath('SourceSansPro-Light.otf'));
                $font->size($titleSize);
                $font->color($this->titleColor);
                $font->align('center');
                $font->valign('top');
            };
            $titlePadTop = floor($rectH/2)-$titleSize-$padCenterTop;

            // inserts character where string is to be split into new line (keeping words intact)
            $titleLength = \count($jobTitle);
            $maxCharLimit = 28;
            $titleCharLimit = ($titleLength > $maxCharLimit) ? ceil($titleLength/2) : $maxCharLimit;
            $splittedJobTitle = wordwrap($jobTitle, $titleCharLimit, '\n');
            // create array of lines
            $titleLines = explode('\n', $splittedJobTitle);

            foreach ($titleLines as $titleLine) {
                $image->text($titleLine, floor($rectW/2), $titlePadTop, $titleCallback);
                $titlePadTop = $titlePadTop + $titleSize + 2; // shift top position down
            }

            if (null !== $location || null !== $jobCode) {
                $subtitleCallback = function (AbstractFont $font) use ($subtitleSize) {
                    $font->file($this->getFontPath('SourceSansPro-Bold.otf'));
                    $font->size($subtitleSize);
                    $font->color($this->primaryColor);
                    $font->align('center');
                    $font->valign('top');
                };
                $subtitle = implode(' / ', array_filter([$jobCode, $location]));
                $image->text($subtitle, floor($rectW/2), $sepY1-($subtitleSize*2), $subtitleCallback);
            }
        }

        // footer separator line
        $image->rectangle($padLeft, $sepY1, $w-$padRight, $sepY2, function (AbstractShape $draw) {
            $draw->background($this->secondaryColor);
        });

        $whiteLogo = $this->manager
            ->make($logoInfo['path'])
            ->greyscale()
            ->brightness(100)
            ->contrast(100);

        // insert logo onto image
        $image->insert($whiteLogo, 'bottom-center', 0, floor($logoInfo['height']/2));

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function supportsRandomization(): bool
    {
        return true;
    }
}
