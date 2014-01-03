<?php

/**
 * Minification provider for "Minify" package
 * 
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2014 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr\Css\Minifier;

// Require the minification provider interface
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Minifier.php';

// Require the minification provider exception
require_once __DIR__.DIRECTORY_SEPARATOR.'Exception.php';

/**
 * Minify minification provider
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
class Minify implements \Tollwerk\Squeezr\Css\Minifier {
	
	/**
	 * Constructor
	 * 
	 * @throws \Tollwerk\Squeezr\Css\Minifier\Exception		If the minify installation is invalid
	 * @todo joschi											Use Minify_CSS instead of Minify_CSS_Compressor in order to get some options (necessary?)
	 */
	public function __construct() {
		
		// Check if minify is installed properly
		if (!@is_readable(SQUEEZR_PLUGINS.'minify'.DIRECTORY_SEPARATOR.'min'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Minify'.DIRECTORY_SEPARATOR.'CSS'.DIRECTORY_SEPARATOR.'Compressor.php')) {
			throw new \Tollwerk\Squeezr\Css\Minifier\Exception(sprintf(\Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER_MSG, 'minify'), \Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER);
		}
		
		// Include and verify the minify compressor class
		require_once SQUEEZR_PLUGINS.'minify'.DIRECTORY_SEPARATOR.'min'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Minify'.DIRECTORY_SEPARATOR.'CSS'.DIRECTORY_SEPARATOR.'Compressor.php';
		if (!@class_exists('\\Minify_CSS_Compressor', false)) {
			throw new \Tollwerk\Squeezr\Css\Minifier\Exception(sprintf(\Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER_MSG, 'minify'), \Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER);
		}
	}
	/**
	 * Minify a CSS text
	 *
	 * @param string $css			CSS text
	 * @param array $options		Optional options
	 * @return string				Minified CSS text
	 */
	public function minify($css, array $options = array()) {
		return \Minify_CSS_Compressor::process($css, $options);
	}
}