<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;

/**
 * Class GradientTitleBottomTheme.
 */
class GradientTitleBottomTheme extends AbstractTheme
{
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

        $debug = (bool) $this->request->get('debug', false);
        $jobTitle = $this->request->get('jobTitle');

        $rectW = $w;
        $rectH = (int)floor($h * 0.15);
        $rectY1 = abs($rectH - $h);
        $image->rectangle(0, $rectY1, $rectW, $h, function ($draw) {
            $draw->background('rgba(0, 0, 0, 0.5)');
        });

        $logoPath = realpath('images/hd-horizontal-watermark-300w.png');
        $logoInfo = Utils::getImageInfo($logoPath);

        $titleSize = 40;
        $padLeft = 30;
        $padTop = $rectY1 + ($rectH / 2) - ($titleSize / 2);

        $logoPadLeft = $padLeft;
        $logoPadTop = (int)floor($rectY1 + (($rectH - $logoInfo['height']) / 2));

        $whiteLogo = $this->manager
            ->make($logoInfo['path'])
            ->greyscale()
            ->brightness(100)
            ->contrast(100);

        $image->insert($whiteLogo, 'top-left', $logoPadLeft, $logoPadTop);

        $titlePadLeft = $rectW - $padLeft;
        $titlePadTop = $padTop;

        if (null !== $jobTitle) {
            $image->text(Utils::wordwrap(Utils::truncate($jobTitle, 40, ''), 35), $titlePadLeft, $titlePadTop, function (AbstractFont $font) use ($titleSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($titleSize);
                $font->color('#ffffff');
                $font->align('right');
                $font->valign('top');
            });
        }

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
