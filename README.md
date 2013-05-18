squeezr
=======
is basically just another take on improving browsing experience across the ever-growing
multitude of web enabled devices. It can help you preserve your visitor's bandwidth by
**shrinking your website's images and CSS files** to fit the device-specific limitations.

*squeezr* can easily be applied to any website that meets the [requirements](#requirements)
– be it a collection of plain HTML files or rendered by a full-blown CMS – and does not require
any remarkable change to your source code.
As a means of [responsive web design](http://www.abookapart.com/products/responsive-web-design)
it is to be used in combination with [fluid image](http://unstoppablerobotninja.com/entry/fluid-images/) techniques.

Main objectives
---------------

*	*squeezr* aims to be a **modern solution** and doesn't care much about being specifically compatible
	with outdated server environments (see [requirements](#requirements)).
*	It was the overall goal to leverage the **best possible performance** in every stage of processing.
	Some effort has been made to deliberately choose and combine the techniques involved in *squeezr*.
	
What squeezr does
-----------------

Currently *squeezr* consists of two "engines" that can be used independently of each other:

*	The **image engine** automatically resizes images so that they don't exceed your
	visitor's screen size. The resulting image variants get cached to disk for optimized follow-up
	request performance.
	
	*squeezr's* image engine is heavily inspired and influenced by
	[Matt Wilcox' Adaptive Images](http://adaptive-images.com/). At the same time, *squeezr* tries
	to overcome some drawbacks of Matt's approach.
	
*	The **CSS engine** creates and caches device-specific CSS file variants by
	stripping out irrelevant CSS3 media query sections on the server side before delivering them
	to the client. Optionally also CSS minification can be applied as well (using external libraries
	like e.g. [Minify](https://github.com/mrclay/minify)), potentially reducing CSS file size even
	further.

You can find a complete description of *squeezr's* functions and configuration options at
[http://squeezr.net](http://squeezr.net). 

Requirements
------------

*	On the client side:
	*	JavaScript support
	*	Cookie support
*	On the server side:
	*	Apache 2.2+ with mod_rewrite
	*	PHP 5.3+
	*	GD (Image engine only; mostly standard with PHP)

Legal
-----
*squeezr* by Joschi Kuphal is licensed under a [Creative Commons Attribution 3.0 Unported
License](http://creativecommons.org/licenses/by/3.0/).