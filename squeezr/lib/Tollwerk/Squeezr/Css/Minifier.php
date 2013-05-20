<?php

/**
 * Minification provider interface
 * 
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
 * @link		http://squeezr.net
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr\Css;

/**
 * Interface for minifaction providers
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
interface Minifier {
	/**
	 * Minify a CSS text
	 * 
	 * @param string $css			CSS text
	 * @param array $options		Optional options
	 * @return string				Minified CSS text
	 */
	public function minify($css, array $options = array());
}