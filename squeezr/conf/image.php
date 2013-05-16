<?php

/**
 * JPEG quality
 *
 * @var int
*/
define('SQUEEZR_JPEG_QUALITY', 80);

/**
 * Sharpen downsampled images
 *
 * @var boolean
*/
define('SQUEEZR_SHARPEN', true);

/**
 * Copy undersized images
 * 
 * Enabling this feature will produce real image copies if the original image is smaller than
 * a specific breakpoint (and thus doesn't need to be downsampled) and the system doesn't support
 * symlinks. Please be aware that this can result in a much higher disk space requirement.
 * 
 * @var boolean
 */
define('SQUEEZR_CACHE_UNDERSIZED', true);