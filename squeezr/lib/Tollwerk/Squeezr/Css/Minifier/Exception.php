<?php

namespace Tollwerk\Squeezr\Css\Minifier;

/**
 * Server side media query proxy
 * 
 * @author joschi
 *
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