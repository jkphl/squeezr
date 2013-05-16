<?php

/**
 * Document root directory
 * 
 * Make sure this path ends with a directory separator (usually a slash "/").
 * 
 * @var string
 */
define('SQUEEZR_DOCROOT', $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);

/**
 * Squeezr root directory
 *
 * @var string
 */
define('SQUEEZR_ROOT', SQUEEZR_DOCROOT.'squeezr');

/**
 * Browser cache lifetime (in seconds)
 *
 * @var int
 */
define('SQUEEZR_CACHE_LIFETIME', 60 * 60 * 24 * 7);