<?php

/**
 * CSS engine configuration
 *
 * The following configuration options affect the squeezr CSS engine only.
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

/**
 * Activate the CSS engine
 *
 * Set this to FALSE if you want to disable the CSS engine TEMPORARILY. For disabling it permanently
 * you should better remove the corresponding rewrite rules in the .htaccess files, as there will be
 * no proper browser cache handling otherwise. This is for development purposes only.
 *
 * @var boolean
 */
define('SQUEEZR_CSS', true);

/**
 * CSS minification provider
 *
 * Enter the class name of the CSS minification provider you want to use. The class must be found
 * inside a file with the same name (UpperCamelCased plus .php extension) located in the folder
 * Tollwerk\Squeezr\Css\Minifier, and it must implement the interface Tollwerk\Squeezr\Css\Minifier.
 *
 * Currently only "Minify" as a minification provider is implemented, and squeezr comes with a
 * copy of Minify preinstalled (you can find it under SQUEEZR_ROOT/plugins/minify). Minify is a
 * great project of it's own, please visit it at it's homepage or at it's GitHub repository:
 *
 * @link        https://code.google.com/p/minify/
 * @github        https://github.com/mrclay/minify
 *
 * If you do not want to use CSS minification provide NULL here.
 *
 * @var string
 */
define('SQUEEZR_CSS_MINIFICATION_PROVIDER', 'Minify');
