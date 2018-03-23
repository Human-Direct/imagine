<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use HumanDirect\Imagine\Canvas;
use HumanDirect\Imagine\Imagine;
use HumanDirect\Imagine\UnsplashImage;

require_once 'vendor/autoload.php';

$unsplashImage = UnsplashImage::createFromSource(UnsplashImage::SOURCE_RANDOM);

$imagine = new Imagine();
$image = $imagine->create($unsplashImage, Canvas::MEDIA_FB_HIGHLIGHTED_IMAGE, 'default');

echo $image->response('jpg', 100);