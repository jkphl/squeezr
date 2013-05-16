<?php

// trigger_error(var_export($_COOKIE, true));
// trigger_error(var_export($_SERVER, true));
// trigger_error(var_export($_GET, true));

// If a squeezr engine has been requested
if (!empty($_GET['engine']) && !empty($_GET['source'])) {
	switch ($_GET['engine']) {

		// CSS engine
		case 'css':
			
			// Include the CSS engine configuration
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'css.php';

			// Include the CSS engine
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Css.php';
			
			// Squeeze, cache and send the CSS file
			\Tollwerk\Squeezr\Css::instance($_GET['source'])->send();
			
			break;
		
		// Image engine
		case 'image':
				
			// Include the image engine configuration
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'image.php';
			
			// Include the image engine
			require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Image.php';
			
			// Squeeze, cache and send the image file
			\Tollwerk\Squeezr\Image::instance($_GET['source'])->send();
				
			break;
	}
	
// Else: Garbage collection
} else {
	
	// Include the common configuration
	require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'common.php';
	
	// Include the CSS engine
	require_once __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Tollwerk'.DIRECTORY_SEPARATOR.'Squeezr'.DIRECTORY_SEPARATOR.'Cleaner.php';
	
	// Run through all breakpoint directories
	foreach (scandir(SQUEEZR_CACHEROOT) as $breakpointRoot) {
		if (@is_dir(SQUEEZR_CACHEROOT.$breakpointRoot) && ($breakpointRoot != '.') && ($breakpointRoot != '..')) {
			\Tollwerk\Squeezr\Cleaner::instance(SQUEEZR_CACHEROOT.$breakpointRoot)->clean();
		}
	}
}