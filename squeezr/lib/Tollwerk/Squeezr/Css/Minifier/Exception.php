<?php

/**
 * Minification provider exception
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
 * Minification provider exception
 *
 * @package        squeezr
 * @author        Joschi Kuphal <joschi@kuphal.net>
 * @since        1.0b
 * @version        1.0b
 */
class Exception extends \Tollwerk\Squeezr\Exception
{
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
