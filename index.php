<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use Intervention\Image\ImageManager;

require_once 'vendor/autoload.php';

$featuredImageUrl = 'https://source.unsplash.com/featured?internet,office,computer';
$imageUrl = get_redirect_target($featuredImageUrl);

[$w, $h] = getMediaDimensions('facebook_highlighted_image');

$manager = new ImageManager(['driver' => 'gd']);
$image = $manager
    ->make($imageUrl)
    ->crop($w, $h);
$image->insert($imageUrl, 'center');
$image->insert('hd-watermark-300w.png', 'center');
//$image->rectangle(0, 0, $w, $h, function ($draw) {
//    $draw->background('rgba(0, 0, 0, 0.2)');
//});

// create a new image resource
//$resource = $image->canvas($w, $h, '#ffffff');

// send HTTP header and output image data
//echo $resource->response('jpg', 100);
header('Content-Type: image/jpg');
echo $image->encode('jpg', 100);

/**
 * @see https://postcron.com/en/blog/infographics-social-media-image-sizes/
 *
 * @param string $type
 *
 * @return array
 * @throws Exception
 */
function getMediaDimensions(string $type) {
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

    throw new \Exception('Unsupported media type');
}


// FOLLOW A SINGLE REDIRECT:
// This makes a single request and reads the "Location" header to determine the
// destination. It doesn't check if that location is valid or not.
function get_redirect_target($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = curl_exec($ch);
    curl_close($ch);

    // Check if there's a Location: header (redirect)
    if (preg_match('/^Location: (.+)$/im', $headers, $matches)) {
        return trim($matches[1]);
    }
    // If not, there was no redirect so return the original URL
    // (Alternatively change this to return false)
    return $url;
}

// FOLLOW ALL REDIRECTS:
// This makes multiple requests, following each redirect until it reaches the
// final destination.
function get_redirect_final_target($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect
    curl_exec($ch);
    $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($target) {
        return $target;
    }

    return false;
}