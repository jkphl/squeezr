<?php

/**
 * Image engine configuration
 * 
 * The following configuration options affect the squeezr image engine only.
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
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
define('SQUEEZR_IMAGE_SHARPEN', true);

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