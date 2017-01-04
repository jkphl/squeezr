squeezr [![Build Status](https://travis-ci.org/jkphl/squeezr.svg?branch=master)](https://travis-ci.org/jkphl/squeezr)
=====================================================================================================================
is basically just another take on improving browsing experience across the ever-growing
multitude of web enabled devices. It can help you preserve your visitor's bandwidth by
**shrinking your website's images and CSS files** to fit the device-specific limitations.

*squeezr* can easily be applied to any website that meets the [requirements](#requirements) – be it a collection of plain HTML files or rendered by a full-blown CMS – and does not require any remarkable change to your source code. As a means of [responsive web design](http://www.abookapart.com/products/responsive-web-design) it is to be used in combination with [fluid image](http://unstoppablerobotninja.com/entry/fluid-images/) techniques.

What squeezr does
-----------------

Currently *squeezr* consists of two engines that can be used independently of each other:

* The **image engine** automatically resizes images so that they don't exceed your visitor's screen size. The resulting image variants get cached to disk for optimized follow-up request performance.

    *squeezr's* image engine is heavily inspired and influenced by	[Matt Wilcox' Adaptive Images](http://adaptive-images.com/). At the same time, *squeezr* tries	to overcome some drawbacks of Matt's approach.

* The **CSS engine** creates and caches device-specific CSS file variants by stripping out irrelevant CSS3 media query sections on the server side before delivering them to the client. Optionally also CSS minification can be applied (using [Minify](https://github.com/mrclay/minify)), potentially reducing CSS file size even further.

You can find a complete description of *squeezr's* functions and configuration options at [http://squeezr.it](http://squeezr.it).

Installation
---------------

1. Get the latest version by cloning this repository or downloading and unpacking an archive from the [*squeezr* website](http://squeezr.it).
2. Open a console, switch to *squeezr*'s root directory and let [Composer](https://getcomposer.org) pull in all required dependencies:
    ```bash
    composer install
    ```
3. Customize the 3 configuration files (common / global settings file and one for each of the engines) in the `/squeezr/conf/*` directory to your needs.
4. Upload the `/squeezr` directory to your website root and make sure the webserver has write privileges for the subdirectory `/squeezr/cache`.
5. Upload the included `.htaccess` file to your website root **only in case you don't already have a file with this name**. Otherwise, thouroughly incorporate the contained rewrite rules into your existing `.htaccess`.
6. Include the client JavaScript into your HTML pages. Please see the [*squeezr* website](http://squeezr.it#client) for details on this. That's all – you're done now and *squeezr* should be up and running.

Please visit the [*squeezr* website](http://squeezr.it) for further instructions and configuration options.

Requirements
------------

* On the client side:
	* JavaScript support
	* Cookie support
* On the server side:
	* Apache 2.2+ with mod_rewrite
	* PHP 5.3+
	* GD (Image engine only; mostly standard with PHP)

Legal
-----
Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / [@jkphl](https://twitter.com/jkphl)

As of version 1.0.3, *squeezr* is licensed under the terms of the [MIT license](LICENSE.txt). Before that, a [Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/) applied.
