<?php

// Require the custom common configuration
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'common.php';

// Require the exception base class
require_once __DIR__.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Exception.php';

/**
 * Squeezr cache root
 *
 * @var string
 */
define('SQUEEZR_CACHEROOT', rtrim(SQUEEZR_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);

/**
 * Squeezr plugin directory
 *
 * @var string
 */
define('SQUEEZR_PLUGINS', rtrim(SQUEEZR_ROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);

/**
 * Current breakpoint
 *
 * @var int
 */
define('SQUEEZR_BREAKPOINT', empty($_GET['breakpoint']) ? null : trim($_GET['breakpoint']));