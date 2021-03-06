<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use HumanDirect\Imagine\Canvas;
use HumanDirect\Imagine\Imagine;
use HumanDirect\Imagine\Theme\ThemeInterface;
use HumanDirect\Imagine\UnsplashImage;
use Symfony\Component\HttpFoundation\Request;

require_once 'vendor/autoload.php';

$request = Request::createFromGlobals();
$theme = $request->get('theme', ThemeInterface::RANDOM_THEME_NAME);
$mediaType = $request->get('size', Canvas::MEDIA_DEFAULT_SIZE);

$unsplashImage = UnsplashImage::createFromSource(UnsplashImage::SOURCE_RANDOM);
$imagine = new Imagine($request);
$image = $imagine->create($unsplashImage, $mediaType, $theme);

header('X-Original-Image-URL: ' . $unsplashImage->getUrl());
echo $image->response('jpg', 100);