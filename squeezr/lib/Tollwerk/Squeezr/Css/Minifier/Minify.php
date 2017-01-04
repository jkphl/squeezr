<?php

/**
 * Minification provider for "Minify" package
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

namespace Tollwerk\Squeezr\Css\Minifier;

/**
 * Minify minification provider
 *
 * @package        squeezr
 * @author        Joschi Kuphal <joschi@kuphal.net>
 * @since        1.0b
 * @version        1.0b
 */
class Minify implements \Tollwerk\Squeezr\Css\Minifier
{
    /**
     * Minify a CSS text
     *
     * @param string $css CSS text
     * @param array $options Optional options
     * @return string                Minified CSS text
     */
    public function minify($css, array $options = array())
    {
        return \Minify_CSS_Compressor::process($css, $options);
    }
}
