<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;

/**
 * Class MinimalBlackBoxBottomTheme.
 */
class MinimalBlackBoxBottomTheme extends AbstractTheme
{
    protected $boxBgColor = 'rgba(0, 0, 0, 0.5)';
    protected $titleTextColor = '#ffffff';

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
        $image->rectangle(0, $rectY1, $rectW, $h, function (AbstractShape $draw) {
            $draw->background($this->boxBgColor);
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
            $titleCallback = function (AbstractFont $font) use ($titleSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($titleSize);
                $font->color($this->titleTextColor);
                $font->align('right');
                $font->valign('top');
            };
            $image->text(Utils::wordwrap(Utils::truncate($jobTitle, 40, ''), 35), $titlePadLeft, $titlePadTop, $titleCallback);

            if ($debug) {
                $this->drawControlRectangle(
                    $image,
                    $logoPadLeft + $logoInfo['width'] + $padLeft,
                    $titlePadTop,
                    $rectW - 30,
                    $titlePadTop + $titleSize,
                    '255, 255, 255'
                );
            }
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
