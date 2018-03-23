<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Canvas;
use HumanDirect\Imagine\ImageManagerAwareInterface;
use HumanDirect\Imagine\ImageManagerAwareTrait;
use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;

/**
 * Class DefaultTheme.
 */
class DefaultTheme implements ThemeInterface, ImageManagerAwareInterface
{
    use ImageManagerAwareTrait;

    /**
     * @inheritdoc
     */
    public function apply(Canvas $canvas): Image
    {
        $image = $canvas->create();
        $w = $canvas->getWidth();
        $h = $canvas->getHeight();

        $rectW = $w / 3;
        $rectH = (int)floor($h * 1.6);
        $image->rectangle(0, 0, $rectW, $rectH, function ($draw) {
            //$draw->background('rgba(27, 179, 219, 0.5)'); // light blue
            $draw->background('rgba(255, 255, 255, 0.8)');
        });

        $blueTopLeftX = 0;
        $blueTopLeftY = $h;
        $blueBottomRightX = $rectW;
        $blueBottomRightY = abs($rectH - $h);
        $image->rectangle($blueTopLeftX, $blueTopLeftY, $blueBottomRightX, $blueBottomRightY, function ($draw) {
            $draw->background('rgba(27, 179, 219, 0.8)'); // light blue
        });

        $logoInfo = Utils::getLogoInfo('horizontal');

// calculate logo to center of rectangle
//$centerPadLogo = ($rectW - $logoInfo['width']) / 2;

        $padLeft = 30;
        $padTop = 30;

        $logoPadLeft = $padLeft;
        $logoPadTop = $padTop;
        $image->insert($logoInfo['path'], 'top-left', $logoPadLeft, $logoPadTop);

// Add title
        $textPadLeft = $padLeft;
        $titlePadTop = $padTop * 3 + $logoInfo['height'] / 2;
        $titleSize = 24;
        $image->text(Utils::wordwrap('Front-End JavaScript Developer/Team Lead', 25), $textPadLeft, $titlePadTop, function (AbstractFont $font) use ($titleSize) {
            $font->file('fonts/SourceSansPro-Bold.otf');
            $font->size($titleSize);
            $font->color('rgb(43, 57, 132)');
            $font->align('left');
            $font->valign('top');
        });

        $description = 'How does it sound to develop the most innovative SD-WAN solution on the market?
This Full Stack position encompasses the use of the latest frontend technologies (Angular 4, Node.js) with technical challenges concerning cloud centric, high scalability and performance.';

        $descSize = 16;
//$descPadTop = $titlePadTop + $titleSize + $padTop;
        $descPadTop = $titlePadTop - 10;
        $image->text(Utils::wordwrap(Utils::truncate($description)), $textPadLeft, $descPadTop, function (AbstractFont $font) use ($descSize) {
            $font->file('fonts/SourceSansPro-Regular.otf');
            $font->size($descSize);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
        });

// consultant data
        $avatarInfo = Utils::getImageInfo('ana-small.jpg');

        $avatarW = 120;
        $avatarH = 120;
        $avatarPadLeft = (int)floor(($rectW - $avatarW) / 2);
        $avatarPadTop = $blueBottomRightY + 20;

        $avatar = $this->manager
            ->make($avatarInfo['path'])
            ->resize($avatarW, $avatarH);
        $image->insert($avatar, 'top-left', $avatarPadLeft, $avatarPadTop);
        $image->rectangle($avatarPadLeft, $avatarPadTop, $avatarPadLeft + $avatarW, $avatarPadTop + $avatarH, function ($draw) {
            $draw->border(2, 'rgb(43, 57, 132)');
        });

        $nameSize = 20;
        $textPadLeft = $avatarPadLeft + (int)floor($avatarW / 2);
        $namePadTop = $avatarPadTop + $avatarH + 20;
        $image->text('Ana Sandu', $textPadLeft, $namePadTop, function (AbstractFont $font) use ($nameSize) {
            $font->file('fonts/SourceSansPro-Bold.otf');
            $font->size($nameSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        $infoSize = 18;
        $emailPadTop = $namePadTop + $nameSize + 20;
        $image->text('ana.sandu@humandirect.eu', $textPadLeft, $emailPadTop, function (AbstractFont $font) use ($infoSize) {
            $font->file('fonts/SourceSansPro-Regular.otf');
            $font->size($infoSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });
        $phonePadTop = $emailPadTop + $infoSize + 5;
        $image->text('+40723370850', $textPadLeft, $phonePadTop, function (AbstractFont $font) use ($infoSize) {
            $font->file('fonts/SourceSansPro-Regular.otf');
            $font->size($infoSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });
        $skypePadTop = $phonePadTop + $infoSize + 5;
        $image->text('Skype: ana.sandu_2', $textPadLeft, $skypePadTop, function (AbstractFont $font) use ($infoSize) {
            $font->file('fonts/SourceSansPro-Regular.otf');
            $font->size($infoSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function hasName(string $themeName): bool
    {
        return 'default' === $themeName;
    }
}
