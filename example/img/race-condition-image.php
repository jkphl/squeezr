<?php

/**
 * Cookie race condition test page – Dynamic image
 *
 * This file belongs to the cookie race condition test case and dynamically outputs a JPEG
 * to demonstrate the problem. Please call examples/race-condition.php in your browser to
 * see it in action.
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
 * @link		http://squeezr.net
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

header('Content-Type: image/jpeg');

$cookie			= empty($_COOKIE['squeezr_racecondition']) ? null : $_COOKIE['squeezr_racecondition'];
$valid			= (empty($_GET['random']) ? false : $_GET['random']) === $cookie;

$image			= imagecreatetruecolor(300, 200);
$picture		= imagecreatefromjpeg(__DIR__.DIRECTORY_SEPARATOR.'example.jpg');
$textColorBg	= imagecolorallocate($image, 0, 0, 0);
$textColorFg	= imagecolorallocate($image, 255, 255, 255);
$bgColor 		= $valid ? imagecolorallocate($image, 204, 255, 204) : imagecolorallocate($image, 255, 204, 204);
$text			= $valid ? 'Congratulations! As for the image request, your browser did set the cookie fast enough!' : 'Sorry, this was not fast enough! The image request didn\'t carry the correct cookie value ...';
$lines			= wordwrap($text, floor(280 / imagefontwidth(3)), "\n", true);
$lines			= explode("\n", $lines);


imagecopyresampled($image, $picture, 0, 0, 0, 0, 300, 200, 1500, 1000);
imagefilter($image, IMG_FILTER_GRAYSCALE);
$valid ? imagefilter($image, IMG_FILTER_COLORIZE, 0, 51, 0) : imagefilter($image, IMG_FILTER_COLORIZE, 51, 0, 0);
foreach ($lines as $line => $text) imagestring($image, 3, 11, 11 + 20 * $line, $text, $textColorBg);
foreach ($lines as $line => $text) imagestring($image, 3, 10, 10 + 20 * $line, $text, $textColorFg);
imagejpeg($image);
imagedestroy($image);