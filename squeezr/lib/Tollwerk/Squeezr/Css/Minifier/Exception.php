<?php

/**
 * Minification provider exception
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

namespace Tollwerk\Squeezr\Css\Minifier;

require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'Exception.php';

/**
 * Minification provider exception
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
class Exception extends \Tollwerk\Squeezr\Exception {
	/**
	 * Invalid minification provider
	 *
	 * @var int
	 */
	const INVALID_MINIFICATION_PROVIDER = 100;
	/**
	 * Invalid minification provider
	 *
	 * @var string
	 */
	const INVALID_MINIFICATION_PROVIDER_MSG = 'Invalid minification provider "%s" - please check the installation';
}