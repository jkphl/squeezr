<?php

namespace Tollwerk\Squeezr\Css;

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