<?php

namespace HumanDirect\Imagine\Theme;

use HumanDirect\Imagine\Utils;
use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;

/**
 * Class BlueBoxRightTheme.
 */
class BlueBoxRightTheme extends AbstractTheme implements PositionAwareThemeInterface
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
        $rectH = $usesAvatar ? (int)floor($h * 0.75) : $h;
        $rectLeft = $w - $rectW;
        $image->rectangle($rectLeft, 0, $w, $rectH, function (AbstractShape $draw) {
            $draw->background($this->superiorBoxBgColor);
        });

        $logoPath = $this->getImagePath('hd-horizontal-watermark-300w.png');
        $logoInfo = Utils::getImageInfo($logoPath);

        $padLeft = 30;
        $padTop = 30;

        $logoPadLeft = $rectLeft + $padLeft;
        $logoPadTop = $padTop;
        $image->insert($logoInfo['path'], 'top-left', $logoPadLeft, $logoPadTop);

        $textPadLeft = $rectLeft + $padLeft;
        $titlePadTop = $logoPadTop + $logoInfo['height'] + $padTop * 1.5;
        $titleSize = 24;

        if (null !== $jobTitle) {
            $titleCallback = function (AbstractFont $font) use ($titleSize) {
                $font->file($this->getFontPath('SourceSansPro-Bold.otf'));
                $font->size($titleSize);
                $font->color($this->titleTextColor);
                $font->align('left');
                $font->valign('middle');
            };
            $image->text(Utils::wordwrap(Utils::truncate($jobTitle, 58), 25), $textPadLeft, $titlePadTop, $titleCallback);

            if ($debug) {
                $this->drawControlRectangle($image, $textPadLeft, $titlePadTop, $w - 30, $titlePadTop + $titleSize * 2);
            }
        }

        $descSize = 20;
        $descPadTop = $titlePadTop + $titleSize * 2 + $padTop;

        if (null !== $jobDescription) {
            $jdTextLimit = $usesAvatar ? 345 : 580;
            $jdCallback = function (AbstractFont $font) use ($descSize) {
                $font->file($this->getFontPath('SourceSansPro-Regular.otf'));
                $font->size($descSize);
                $font->color($this->jdTextColor);
                $font->align('left');
                $font->valign('bottom');
            };
            $image->text(Utils::wordwrap(Utils::truncate($jobDescription, $jdTextLimit), 32), $textPadLeft, $descPadTop, $jdCallback);

            if ($debug) {
                $this->drawControlRectangle($image, $textPadLeft, $descPadTop, $w - 30, $rectH - 30);
            }
        }

        if ($usesAvatar) {
            $blueTopLeftX = $rectLeft;
            $blueTopLeftY = $h;
            $blueBottomRightX = $w;
            $blueBottomRightY = abs((int)floor($h * 1.75) - $h);
            $image->rectangle($blueTopLeftX, $blueTopLeftY, $blueBottomRightX, $blueBottomRightY, function (AbstractShape $draw) {
                $draw->background($this->inferiorBoxBgColor);
            });

            $avatarW = 100;
            $avatarH = 100;
            $avatarPadLeft = $rectLeft + $padLeft;
            $avatarPadTop = $blueBottomRightY + 30;

            $nameTextSize = 20;
            $contactTextSize = 18;

            $textPadLeft = $avatarPadLeft + $avatarW + 15;
            $namePadTop = $avatarPadTop;
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
                $font->file($this->getFontPath('SourceSansPro-Bold.otf'));
                $font->size($nameTextSize);
                $font->color($this->avatarTextColor);
                $font->align('left');
                $font->valign('top');
            });

            $email = $avatarContact['email'] ?? null;
            if ($email) {
                $image->text($email, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file($this->getFontPath('SourceSansPro-Regular.otf'));
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('left');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $phone = $avatarContact['phone'] ?? null;
            if ($phone) {
                $image->text($phone, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file($this->getFontPath('SourceSansPro-Regular.otf'));
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('left');
                    $font->valign('top');
                });
                $contactPadTop = $contactPadTop + $contactTextSize + 5;
            }

            $skype = $avatarContact['skype'] ?? null;
            if ($skype) {
                $image->text('Skype: '.$skype, $textPadLeft, $contactPadTop, function (AbstractFont $font) use ($contactTextSize) {
                    $font->file($this->getFontPath('SourceSansPro-Regular.otf'));
                    $font->size($contactTextSize);
                    $font->color($this->avatarTextColor);
                    $font->align('left');
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
        return PositionAwareThemeInterface::POSITION_RIGHT;
    }
}
