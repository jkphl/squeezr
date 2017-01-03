<?php

/**
 * Image engine configuration
 * 
 * The following configuration options affect the squeezr image engine only.
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2017 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since		1.0b
 * @version		1.0b
 */

/**
 * Activate the iamge engine
 *
 * Set this to FALSE if you want to disable the image engine TEMPORARILY. For disabling it permanently
 * you should better remove the corresponding rewrite rules in the .htaccess files, as there will be
 * no proper browser cache handling otherwise. This is for development purposes only.
 *
 * @var boolean
 */
define('SQUEEZR_IMAGE', true);

/**
 * JPEG quality
 * 
 * Please provide the quality downscaled JPEG images should have (between 1 and 100; defaults to 80).
 *
 * @var int
*/
define('SQUEEZR_IMAGE_JPEG_QUALITY', 80);

/**
 * Sharpen downsampled images
 * 
 * Please define if downsampled images should be sharpened (defaults to TRUE)
 *
 * @var boolean
*/
define('SQUEEZR_IMAGE_SHARPEN', false);

/**
 * Force image sharpening
 * 
 * In some situations image sharpening is suspended by default, e.g. when downscaling 8-bit PNG images, as
 * sharpening seriously affects image quality in these cases. Activate this option to force
 * sharpening anyway.
 *
 * @var boolean
*/
define('SQUEEZR_IMAGE_FORCE_SHARPEN', false);

/**
 * Copy undersized images
 * 
 * Enabling this feature will produce real copies of your original images in case they are smaller
 * than a specific breakpoint (and thus don't need to be downsampled) and your system doesn't support
 * symlinks. Please be aware that this will lead to significantly higher disk space requirements, but
 * it will save some processing power (defaults to FALSE). 
 * 
 * @var boolean
 */
define('SQUEEZR_IMAGE_COPY_UNDERSIZED', false);

/**
 * 8-bit PNG quantizer
 *
 * When downscaling 8-bit PNG images, the resulting images have to be re-quantized. squeezr comes with
 * some internal quantizing logic (based on GD), but the results aren't that good. If available, you
 * should use an external quantizer like "pngquant" or "pngnq". The following quantizer options are
 * available:
 * 
 * - FALSE: Disable re-quantizing altogether (potentially resulting in huge PNG files)
 * - "internal", NULL or empty string: Internal quantizer (GD based)
 * - "pngquant": pngquant command line quantizer (must be available on the system)
 * - "pngnq": pngnq command line quantizer (must be available on the system)
 * 
 * @var string
 */
define('SQUEEZR_IMAGE_PNG_QUANTIZER', 'internal');

/**
 * External 8-bit PNG quantizer speed
 * 
 * If an external quantizer is used (like "pngquant" or "pngnq"), you can control the quality the
 * resulting PNG files will have. Higher quality means longer processing time. Provider an integer
 * value between 1 and 10 here, with 1 meaning highest quality / slowest processing and 10 meaning
 * poorest quality, but fastest processing.  
 * 
 * @var int
 */
define('SQUEEZR_IMAGE_PNG_QUANTIZER_SPEED', 5);