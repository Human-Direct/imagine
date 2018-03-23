<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use HumanDirect\Imagine\UnsplashImage;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;

require_once 'vendor/autoload.php';

$unsplashImage = UnsplashImage::createFromSource(UnsplashImage::SOURCE_RANDOM);
$imageUrl = $unsplashImage->getUrl();

[$initW, $initH]  = getMediaDimensions('facebook_highlighted_image');
[$w, $h] = getMediaDimensions('facebook_highlighted_image');

$startW = $w;
$startH = $h;
$needsResize = false;

if ($initW !== $w || $initH !== $h) {
    $w = $initW;
    $h = $initH;
    $needsResize = true;
}

$manager = new ImageManager(['driver' => 'imagick']);
$image = $manager
    ->make($imageUrl)
    ->crop($w, $h);
//->insert($imageUrl, 'center');

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

$logoInfo = getLogoInfo('horizontal');

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
$image->text(utf8_wordwrap('Front-End JavaScript Developer/Team Lead', 25), $textPadLeft, $titlePadTop, function (AbstractFont $font) use ($titleSize) {
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
$image->text(utf8_wordwrap(text_truncate($description)), $textPadLeft, $descPadTop, function (AbstractFont $font) use ($descSize) {
    $font->file('fonts/SourceSansPro-Regular.otf');
    $font->size($descSize);
    $font->color('#000000');
    $font->align('left');
    $font->valign('top');
});

// consultant data
$avatarInfo = getImageInfo('ana-small.jpg');

$avatarW = 120;
$avatarH = 120;
$avatarPadLeft = (int)floor(($rectW - $avatarW) / 2);
$avatarPadTop = $blueBottomRightY + 20;

$avatar = $manager
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

// needs resizing after image is composed
if ($needsResize) {
    $image->resize($startW, $startH);
}

// send HTTP header and output image data
echo $image->response('jpg', 100);

function utf8_wordwrap($string, $width = 45, $break = "\n", $cut = false)
{
    if ($cut) {
        // Match anything 1 to $width chars long followed by whitespace or EOS,
        // otherwise match anything $width chars long
        $pattern = '/(.{1,' . $width . '})(?:\s|$)|(.{' . $width . '})/uS';
        $replace = '$1$2' . $break;
    } else {
        // Anchor the beginning of the pattern with a lookahead
        // to avoid crazy backtracking when words are longer than $width
        $pattern = '/(?=\s)(.{1,' . $width . '})(?:\s|$)/uS';
        $replace = '$1' . $break;
    }

    return preg_replace($pattern, $replace, $string);
}

function text_truncate($string, $desiredlength = 300)
{
    $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
    $parts_count = count($parts);

    $length = 0;
    $last_part = 0;
    $truncated = false;
    for (; $last_part < $parts_count; ++$last_part) {
        $length += strlen($parts[$last_part]);
        if ($length > $desiredlength) {
            $truncated = true;
            break;
        }
    }

    return implode(array_slice($parts, 0, $last_part)) . ($truncated ? ' ...' : '');
}

function getLogoInfo($type)
{
    $logo = 'hd-watermark-300w.png';
    if ('horizontal' === $type) {
        $logo = 'hd-horizontal-watermark-300w.png';
    }

    return getImageInfo($logo);
}

function getImageInfo($imagePath)
{
    $image = realpath(sprintf('images/%s', $imagePath));

    [$width, $height, $type] = getimagesize($image);

    return [
        'path' => $image,
        'width' => $width,
        'height' => $height,
        'type' => $type,
    ];
}

/**
 * @see https://postcron.com/en/blog/infographics-social-media-image-sizes/
 *
 * @param string $type
 *
 * @return array
 * @throws Exception
 */
function getMediaDimensions(string $type)
{
    switch ($type) {
        case 'facebook_shared_image':
            return [1200, 630];
        case 'facebook_shared_link':
            return [1200, 627];
        case 'facebook_highlighted_image':
            return [1200, 717];
        case 'twitter_shared_image':
            return [1024, 512];
        case 'twitter_shared_link':
            return [520, 254];
        case 'linkedin_shared_image':
            return [520, 320];
        case 'linkedin_shared_link':
            return [520, 272];
    }

    throw new \Exception('Unsupported media type.');
}