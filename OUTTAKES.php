<?php

// squeezr/conf/image.php

/**
 * Alpha transparency threshold
 *
 * This value determines as to which opacity a image pixel is considered transparent. Enter a floating
 * point value between 0.0 and 1.0 here. 0.0 means "zero tolerance", so only pixels that are truly completely
 * transparent are considered as such. 1 means "every pixel is transparent" (which doesn't make sense
 * of course). A reasonable value could be e.g. 0.1;
 *
 * @var string
 */
define('SQUEEZR_IMAGE_TRANSPARENCY_THRESHOLD', 0.1);

// squeezr/lib/Tollwerk/Squeezr/Image.php:Image::_downscalePng

// If the original image had alpha transparency: Determine if the resampled image also has transparent pixels
if ($sourceAlpha){
	$transparencyThreshold		= round(127 * max(0, min(1, floatval(SQUEEZR_IMAGE_TRANSPARENCY_THRESHOLD))));
	trigger_error($transparencyThreshold);
	$stepX						= min(max(floor($targetWidth / 50), 1), 10);
	$stepY						= min(max(floor($targetHeight / 50), 1), 10);
	$quantize					= true;
	for ($column = 0; $column < $targetWidth; $column += $stepX) {
		for ($row = 0; $row < $targetHeight; $row += $stepY) {
			$color				= imagecolorsforindex($targetImage, imagecolorat($targetImage, $column, $row));
			if ($color['alpha'] > $transparencyThreshold) {
				$quantize		= false;
				break 2;
			}
		}
	}
}

/****************************************************************************/

?>