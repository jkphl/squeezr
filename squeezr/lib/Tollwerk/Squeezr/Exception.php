<?php

/**
 * Error messages
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

namespace Tollwerk\Squeezr;

/**
 * squeezr exception
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
class Exception extends \Exception {
	/**
	 * Invalid target cache directory 
	 * 
	 * @var int
	 */
	const INVALID_TARGET_CACHE_DIRECTORY = 0;
	/**
	 * Invalid target cache directory
	 *
	 * @var string
	 */
	const INVALID_TARGET_CACHE_DIRECTORY_MSG = 'squeezr cache directory non-existent and could not be created';
	/**
	 * Missing metrics cookie
	 * 
	 * @var int
	 */
	const MISSING_METRICS_COOKIE = 1;
	/**
	 * Missing metrics cookie
	 *
	 * @var string
	 */
	const MISSING_METRICS_COOKIE_MSG = 'squeezr metrics cookie missing or invalid';
	/**
	 * Failed writing cache file
	 *
	 * @var int
	 */
	const FAILED_WRITING_CACHE_FILE = 2;
	/**
	 * Failed writing cache file
	 *
	 * @var string
	 */
	const FAILED_WRITING_CACHE_FILE_STR = 'Failed writing cache file "%s"';

	/**
	 * Invalid CSS file
	 *
	 * @var int
	 */
	const INVALID_CSS_FILE = 102;
	/**
	 * Invalid CSS file
	 *
	 * @var string
	 */
	const INVALID_CSS_FILE_MSG = 'Invalid css file "%s"';
	/**
	 * Unbalanced block comment
	 * 
	 * @var int
	 */
	const UNBALANCED_BLOCK_COMMENT = 103;
	/**
	 * Unbalanced block comment
	 *
	 * @var string
	 */
	const UNBALANCED_BLOCK_COMMENT_STR = 'Unbalanced block comment starting at line %s';
	/**
	 * Invalid @-rule identifier
	 *
	 * @var int
	 */
	const INVALID_AT_RULE_IDENTIFIER = 104;
	/**
	 * Invalid @-rule identifier
	 *
	 * @var string
	 */
	const INVALID_AT_RULE_IDENTIFIER_STR = 'Invalid @-rule identifier at line %s';
	/**
	 * Invalid declaraction block
	 *
	 * @var int
	 */
	const INVALID_DECLARATION_BLOCK = 105;
	/**
	 * Invalid declaraction block
	 *
	 * @var string
	 */
	const INVALID_DECLARATION_BLOCK_STR = 'Invalid declaration block at line %s';
	/**
	 * Unbalanced declaraction block
	 *
	 * @var int
	 */
	const UNBALANCED_DECLARATION_BLOCK = 106;
	/**
	 * Unbalanced declaraction block
	 *
	 * @var string
	 */
	const UNBALANCED_DECLARATION_BLOCK_STR = 'Unbalanced declaration block at line %s';
	/**
	 * Invalid image file
	 *
	 * @var int
	 */
	const INVALID_IMAGE_FILE = 200;
	/**
	 * Invalid image file
	 *
	 * @var string
	 */
	const INVALID_IMAGE_FILE_MSG = 'Invalid image file "%s"';
	/**
	 * No GD library available
	 *
	 * @var int
	 */
	const GD_UNAVAILABLE = 201;
	/**
	 * No GD library available
	 *
	 * @var string
	 */
	const GD_UNAVAILABLE_MSG = 'squeezr needs the GD library to be available';
	/**
	 * Breakpoint copy cache failed
	 * 
	 * @var int
	 */
	const FAILED_COPY_CACHE = 202;
	/**
	 * Breakpoint copy cache failed
	 * 
	 * @var string
	 */
	const FAILED_COPY_CACHE_MSG = 'Failed to cache the original image as breakpoint cache copy: "%s"';
	/**
	 * Breakpoint downsample cache failed
	 *
	 * @var int
	 */
	const FAILED_DOWNSAMPLE_CACHE = 203;
	/**
	 * Breakpoint downsample cache failed
	 *
	 * @var string
	 */
	const FAILED_DOWNSAMPLE_CACHE_MSG = 'Failed to cache a downsampled image as breakpoint cache copy: "%s"';
}