<?php

/**
 * Common configuration (internal hub)
 *
 * @package        squeezr
 * @author        Joschi Kuphal <joschi@kuphal.net>
 * @copyright    Copyright © 2017 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link        http://squeezr.it
 * @github        https://github.com/jkphl/squeezr
 * @twitter        @squeezr
 * @license        https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since        1.0b
 * @version        1.0b
 */

// Require the custom common configuration
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'common.php';

// Require the exception base class
require_once __DIR__.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Exception.php';

/**
 * squeezr cache root
 *
 * All cache files are put into a directory named "cache" under the squeezr main directory.
 *
 * @var string
 */
define('SQUEEZR_CACHEROOT', rtrim(SQUEEZR_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);

/**
 * squeezr plugin directory
 *
 * Plugins for squeezr (like CSS minification providers) are put into a directory named "plugins"
 * under the squeezr main directory.
 *
 * @var string
 */
define('SQUEEZR_PLUGINS', rtrim(SQUEEZR_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);

/**
 * Active breakpoint definition
 *
 * For both engines (images and CSS) squeezr expects a breakpoint value to be submitted via cookie.
 * The .htaccess rewrite rules are in charge of traversing the cookie value into a GET parameter
 * named "breakpoint", which will be picked up here.
 *
 * For the image engine the breakpoint value will be a pixel value (like. e.g. "800px" meaning "The screen
 * of the current device is 800 pixels wide).
 *
 * For the CSS engine the breakpoint value will be a combination of the screen dimensions and the
 * em-to-pixel ratio (like e.g. "1920x1200@16" meaning "The screen has 1920 x 1200 pixels, and 1em equals 16px).
 *
 * @var string
 */
define('SQUEEZR_BREAKPOINT', empty($_GET['breakpoint']) ? null : trim($_GET['breakpoint']));
