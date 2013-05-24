<?php

/**
 * CSS engine configuration (internal hub including the common configuration)
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

// Require the global common configuration
require_once __DIR__.DIRECTORY_SEPARATOR.'common.php';

// Require the custom CSS engine configuration
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'css.php';