<?php

/**
 * Cookie race condition test page – Dynamic CSS 
 * 
 * This file belongs to the cookie race condition test case and dynamically outputs some CSS
 * to demonstrate the problem. Please call examples/race-condition.php in your browser to
 * see it in action.  
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2017 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

header('Content-Type: text/css');

$cookie			= empty($_COOKIE['squeezr_racecondition']) ? null : $_COOKIE['squeezr_racecondition'];
$valid			= (empty($_GET['random']) ? false : $_GET['random']) === $cookie;

echo file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'example.css')."\n";

if ($valid): ?>
p.success {
	background-color: #cfc;
}
p.fail {
	display: none;
}
<?php else: ?>
p.success {
	display: none;
}
p.fail {
	background-color: #fcc;
}
<?php endif; ?>