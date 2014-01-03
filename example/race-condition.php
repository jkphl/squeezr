<?php

/**
 * Cookie race condition test page
 * 
 * Please call this page in your browser. You will be able to check if your browser suffers
 * from a well known cookie race condition which squeezr is affected of. The browser view
 * containes further details on this.
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2014 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

// Create random hash to append to CSS and image requests
$random	= md5(microtime(true).rand());

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1" />
		<title>squeezr | cookie race condition test</title>
		<script type="text/javascript">document.cookie = 'squeezr.racecondition=<?php echo $random; ?>;path=/';</script>
		<link rel="stylesheet" type="text/css" href="css/race-condition-css.php?random=<?php echo $random; ?>" media="all" />
	</head>
	<body>
		<section>
			<h1>squeezr | cookie race condition test</h1>
			<p>This page contains an inline piece of JavaScript in it's <code>&lt;head&gt;</code> section, which sets a cookie with a random value on each reload. This time the value is:</p>
			<pre><?php echo $random; ?></pre>
			<p>Furthermore, there's one CSS and one image embedded into this page. Both of them <em>should</em> receive the cookie value. However, not any browser does set the cookie fast enough, or at least not in a reliable fashion, which is a known problem ("race condition"; you might read <a href="http://blog.cloudfour.com/responsive-imgs/" target="_blank">here</a> or <a href="http://blog.yoav.ws/2011/09/Preloaders-cookies-and-race-conditions" target="_blank">here</a> for further information).</p>
			<p>These are the results for your current browser (I encourage you to try this also with other clients, and be sure to hit reload a few times in order to spot if it's consistent or not!):</p>
			<h3>CSS request</h3>
			<p class="css-check success">Congratulations! As for the CSS request, your browser did set the cookie fast enough!</p>
			<p class="css-check fail">Sorry, this was not fast enough! The CSS request didn't carry the correct cookie value ...</p>
			<h3>Image request</h3>
			<p><img src="img/race-condition-image.php?random=<?php echo $random; ?>"/></p>
		</section>
	</body>
</html>