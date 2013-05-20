<?php

/**
 * Main squeezr processing hub
 * 
 * This is the main script called whenever a file has to be processed by squeezr. Based on the
 * GET parameters given (which have been introduced by the .htaccess file responsible for the
 * rewrite rules) it will decide which squeezr engine to use.
 * 
 * Furthermore, this script is also in charge for cleaning the squeezr file cache.
 * Whenever called without parameters it will start a full cache cleaning cycle. You could
 * e.g. implement a call to this script into your favourite CMS, so that the cache is refreshed
 * whenever you alter any of your images ... 
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

// If a squeezr engine has been requested and a file to process is given
if (!empty($_GET['engine']) && !empty($_GET['source'])) {
	switch ($_GET['engine']) {

		// CSS engine
		case 'css':
			
			// Include the CSS engine configuration
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'css.php';

			// If the CSS engine hasn't been disabled temporarily
			if (SQUEEZR_CSS) {
			
				// Include the CSS engine itself
				require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Css.php';
				
				// Squeeze, cache and send the CSS file
				\Tollwerk\Squeezr\Css::instance($_GET['source'])->send();
				
			// Else: Don't care about caching and deliver the original file
			} else {
				readfile(SQUEEZR_DOCROOT.$_GET['source']);
			}
			
			break;
		
		// Image engine
		case 'image':
				
			// Include the image engine configuration
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'image.php';
			
			// If the CSS engine hasn't been disabled temporarily
			if (SQUEEZR_IMAGE) {
			
				// Include the image engine itself
				require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Image.php';
				
				// Squeeze, cache and send the image file
				\Tollwerk\Squeezr\Image::instance($_GET['source'])->send();

			// Else: Don't care about caching and deliver the original file
			} else {
				readfile(SQUEEZR_DOCROOT.$_GET['source']);
			}
			break;
	}
	
	exit;
	
// Else: Cache cleaning / Garbage collection
} else {
	
	// Include the common configuration
	require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'common.php';
	
	// Include the cache cleaner engine
	require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Cleaner.php';
	
	// Clean the cache root directory
	\Tollwerk\Squeezr\Cleaner::instance(SQUEEZR_CACHEROOT)->clean();
	
	// Respond with an empty content
	header('HTTP/1.1 204 No Content');
	exit;
}