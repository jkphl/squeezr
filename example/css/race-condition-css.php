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
 * @copyright	Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
 * @link		http://squeezr.net
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

header('Content-Type: text/css');

$cookie			= empty($_COOKIE['squeezr_racecondition']) ? null : $_COOKIE['squeezr_racecondition'];
$valid			= (empty($_GET['random']) ? false : $_GET['random']) === $cookie;

?>body {
	font-family: Arial, Helvetica, sans-serif;
	line-height: 1.6;
	padding: 0;
}
section {
	margin: 2em;
}
img {
	max-width: 100%;
	border: 1px solid #ccc;
}
p.css-check, pre {
	padding: 1em;
	border: 1px solid #ccc;
	background-color: #eee;
	white-space: pre-wrap;
}
<?php if ($valid): ?>
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