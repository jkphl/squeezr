'use strict';

/* jshint -W117 */

var system = require('system');

if (system.args.length !== 4) {
    console.error('Usage: load-size.phantom.js url width height');
    phantom.exit();
}

var width = system.args[2];
var height = system.args[3];
var page = require('webpage').create();
var initializer = new Function('(function () { window.screen = ' + JSON.stringify({
        width: width,
        height: width
    }) + '; })();');
page.viewportSize = { width: width, height: height };
page.clipRect = { top: 0, left: 0, width: width, height: height };
page.zoomFactor = 1;
page.onInitialized = function () {
    page.evaluate(initializer);
};
page.clearMemoryCache();
page.open(system.args[1], function (status) {
    if (status === 'success') {
        page.render('./tmp/' + width + 'x' + height + '.png');
    } else {
        console.log('fail');
    }
    page.close();
    phantom.exit();
});
