<?php

namespace Tollwerk\Squeezr\Css\Minifier;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Minifier.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'Exception.php';

class Minify implements \Tollwerk\Squeezr\Css\Minifier {
	
	/**
	 * Constructor
	 * 
	 * @throws \Tollwerk\Squeezr\Css\Minifier\Exception		If the minify installation is invalid
	 */
	public function __construct() {
		
		// TODO joschi: Umstellen auf Minify_CSS, dann stehen Optionen zur Verfügung
		
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