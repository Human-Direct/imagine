<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;

/**
 * Class DefaultTheme.
 */
class DefaultTheme extends AbstractTheme implements PositionAwareThemeInterface
{
    protected $superiorBoxBgColor = 'rgba(255, 255, 255, 0.8)';
    protected $inferiorBoxBgColor = 'rgba(27, 179, 219, 0.8)'; // brand light blue
    protected $titleTextColor = 'rgb(43, 57, 132)';
    protected $jdTextColor = '#000000';
    protected $avatarImgBorderColor = 'rgb(43, 57, 132)'; // brand dark blue
    protected $avatarTextColor = '#ffffff';

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
        $jobDescription = $this->request->get('jobDescription');

        $avatarInput = $this->request->get('avatar');
        $avatarImageUrl = $avatarInput['image'] ?? null;
        $avatarName = $avatarInput['name'] ?? null;
        $avatarContact = $avatarInput['contact'] ?? null;

        $showAvatar = (bool) $this->request->get('showAvatar', true);
        $usesAvatar = ($showAvatar && $avatarInput && $avatarImageUrl && $avatarName && $avatarContact);

        $rectW = (int)ceil($w / 3);
        $rectH = $usesAvatar ? (int)floor($h * 0.6) : $h;
        $image->rectangle(0, 0, $rectW, $rectH, function (AbstractShape $draw) {
            $draw->background($this->superiorBoxBgColor);
        });

        $logoPath = realpath('images/hd-horizontal-watermark-300w.png');
        $logoInfo = Utils::getImageInfo($logoPath);

        $padLeft = 30;
        $padTop = 30;

        $logoPadLeft = $padLeft;
        $logoPadTop = $padTop;
        $image->insert($logoInfo['path'], 'top-left', $logoPadLeft, $logoPadTop);

        $textPadLeft = $padLeft;
        $titlePadTop = $logoPadTop + $logoInfo['height'] + $padTop * 1.5;
        $titleSize = 24;

        if (null !== $jobTitle) {
            $titleCallback = function (AbstractFont $font) use ($titleSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($titleSize);
                $font->color($this->titleTextColor);
                $font->align('left');
                $font->valign('middle');
            };
            $image->text(Utils::wordwrap(Utils::truncate($jobTitle, 58), 25), $textPadLeft, $titlePadTop, $titleCallback);

            if ($debug) {
                $this->drawControlRectangle($image, $textPadLeft, $titlePadTop, $rectW - 30, $titlePadTop + $titleSize * 2);
            }
        }

        $descSize = 20;
        $descPadTop = $titlePadTop + $titleSize * 2 + $padTop * 1.5;

        if (null !== $jobDescription) {
            $jdCallback = function (AbstractFont $font) use ($descSize) {
                $font->file('fonts/SourceSansPro-Regular.otf');
                $font->size($descSize);
                $font->color($this->jdTextColor);
                $font->align('left');
                $font->valign('bottom');
            };
            $jdTextLimit = $usesAvatar ? 225 : 650;
            $image->text(Utils::wordwrap(Utils::truncate($jobDescription, $jdTextLimit), 32), $textPadLeft, $descPadTop, $jdCallback);

            if ($debug) {
                $this->drawControlRectangle($image, $textPadLeft, $descPadTop, $rectW - 30, $rectH - 30);
            }
        }

        if ($usesAvatar) {
            $blueTopLeftX = 0;
            $blueTopLeftY = $h;
            $blueBottomRightX = $rectW;
            $blueBottomRightY = abs((int)floor($h * 1.6) - $h);
            $image->rectangle($blueTopLeftX, $blueTopLeftY, $blueBottomRightX, $blueBottomRightY, function (AbstractShape $draw) {
                $draw->background($this->inferiorBoxBgColor);
            });

            $avatarW = 120;
            $avatarH = 120;

            $nameTextSize = 20;
            $contactTextSize = 18;

            $avatarPadLeft = (int)floor(($rectW - $avatarW) / 2);
            $avatarPadTop = $blueBottomRightY + 20;
            $textPadLeft = $avatarPadLeft + (int)floor($avatarW / 2);
            $namePadTop = $avatarPadTop + $avatarH + 20;
            $contactPadTop = $namePadTop + $nameTextSize + 20;

            $avatarInfo = Utils::getImageInfo($avatarImageUrl);
            $avatar = $this->manager
                ->make($avatarInfo['path'])
                ->resize($avatarW, $avatarH);

            $image->insert($avatar, 'top-left', $avatarPadLeft, $avatarPadTop);
            $image->rectangle($avatarPadLeft, $avatarPadTop, $avatarPadLeft + $avatarW, $avatarPadTop + $avatarH, function (AbstractShape $draw) {
                $draw->border(2, $this->avatarImgBorderColor);
            });

            $image->text($avatarName, $textPadLeft, $namePadTop, function (AbstractFont $font) use ($nameTextSize) {
                $font->file('fonts/SourceSansPro-Bold.otf');
                $font->size($nameTextSize);
                $font->color($this->avatarTextColor);
                $font->align('center');
                $font->valign('top');
            });

            $email = $avatarContact['email'] ?? null;
            if ($email) {
                $image->text($email, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('center');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $phone = $avatarContact['phone'] ?? null;
            if ($phone) {
                $image->text($phone, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('center');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $skype = $avatarContact['skype'] ?? null;
            if ($skype) {
                $image->text('Skype: '.$skype, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file('fonts/SourceSansPro-Regular.otf');
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('center');
                    $font->valign('top');
                });
            }
        }

        return $image;
    }

    /**
     * @inheritdoc
     */
    public function supportsRandomization(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return PositionAwareThemeInterface::POSITION_LEFT;
    }
}
