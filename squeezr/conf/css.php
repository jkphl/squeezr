<?php

/**
 * Pixel-Em-Ratio
 * 
 * @var float
 */
define('SQUEEZR_CSS_EM_PX', 16.0);

/**
 * CSS minification provider
 * 
 * Enter the class name of the CSS minification provider you want to use. The class must be found
 * inside a file with the same name (plus .php extension) lying in the folder Tollwerk\Squeezr\Css\Minifier,
 * and it must implement the interface Tollwerk\Squeezr\Css\Minifier.
 * 
 * Current valid options are:
 * 
 * - Minify
 * - Cssmin
 * 
 * If you do not want to use CSS minification provide NULL here. 
 * 
 * @var string
 */
define('SQUEEZR_CSS_MINIFICATION_PROVIDER', 'minify');