<?php

/**
 * Common configuration
 * 
 * Configuration options in this file affect all squeezr engines (images and CSS).
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
 * Document root directory
 * 
 * Provide the absolute path of your website's root directory here and make sure
 * this path ends with a directory separator (usually a slash "/"). This constant
 * defaults to
 * 
 *		$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR
 * 
 * and in most cases there should be no need to change this.
 * 
 * @var string
 */
define('SQUEEZR_DOCROOT', $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);

/**
 * squeezr root directory
 *
 * By default, all squeezr files are located in a subdirectory named "squeezr" on
 * the top level of your website. Please remember to adapt the rewrite rules in the
 * .htaccess files as well in case you need to change this for whatever reason.
 * 
 * @var string
 */
define('SQUEEZR_ROOT', SQUEEZR_DOCROOT.'squeezr'.DIRECTORY_SEPARATOR);

/**
 * Browser cache lifetime (in seconds)
 * 
 * By default, browsers are supposed to cache any files delivered by squeezr for
 * 1 week (60 * 60 * 24 * 7 = 604800 seconds). You may change this if you like.
 *
 * @var int
 */
define('SQUEEZR_CACHE_LIFETIME', 604800);