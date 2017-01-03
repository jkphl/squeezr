<?php

/**
 * CSS engine configuration (internal hub including the common configuration)
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

// Require the global common configuration
require_once __DIR__.DIRECTORY_SEPARATOR.'common.php';

// Require the custom CSS engine configuration
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'css.php';
