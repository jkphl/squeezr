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
 * CSS minification
 *
 * Please specify if you want the CSS output to be minified. There's no reason to disable CSS
 * minification other than development purposes.
 *
 * @var boolean
 */
define('SQUEEZR_CSS_MINIFY', true);
