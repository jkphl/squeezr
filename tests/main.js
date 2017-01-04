'use strict';

const phantomjs = require('phantomjs-prebuilt').path;
const execFile = require('child_process').execFile;
const path = require('path');
const glob = require('glob');
const should = require('should');
const fs = require('fs');
const sizeOf = require('image-size');

// Read the environment variables
require('dotenv').config();

// Determine the PhantomJS parameters
const phantomScript = path.resolve(__dirname, 'fixture/load-size.phantom.js');
const testUrl = process.env.TEST_DOMAIN + '/example/index.html';
const cacheDir = path.resolve(path.dirname(__dirname), 'squeezr/cache/example');
const explDir = path.join(path.dirname(__dirname), 'example');
const cssDir = path.join(cacheDir, 'css');
const imgDir = path.join(cacheDir, 'img');

/**
 * Load the example page with PhantomJS and a particular screen size
 *
 * @param {Number} width Screen width
 * @param {Number} height Screen height
 * @param {Function} cb Callback
 */
function loadSize(width, height, cb) {
    const errorStr = `PhantomJS couldn't load the test URL "${testUrl}" for width = ${width} / height = ${height}`;
    execFile(phantomjs, [phantomScript, testUrl, width, height], function (err, stdout, stderr) {
        if (err) {
            cb(err);
        } else if (stdout.length > 0) {
            cb((stdout.toString().trim() === 'success') ? null : new Error(errorStr));
        } else if (stderr.length > 0) {
            cb(new Error(stderr.toString().trim()));
        } else {
            cb(null);
        }
    });
}

/**
 * List all files in a directory
 *
 * @param {String} dir Directory path
 * @return {Array} Files
 */
function listDir(dir) {
    return glob.sync(path.resolve(dir, '*'), { nodir: true }).map((f) => path.basename(f)).sort();
}

/**
 * Return whether a directory exists
 *
 * @param {String} dir Directory name
 * @return {Boolean} Directory exists
 */
function dirExists(dir) {
    try {
        fs.lstatSync(dir).isDirectory();
    } catch (e) {
        return false;
    }
}

/**
 * Return whether a path is a symbolic link to a particular target
 *
 * @param {String} src Source path
 * @param {String} Optional: target Target
 * @return {Boolean} Path is link (to target)
 */
function isLink(src, target) {
    try {
        var stat = fs.lstatSync(src);
        return stat.isSymbolicLink() && (!target || (fs.readlinkSync(src) === target));
    } catch (e) {
        return false;
    }
}

describe('squeezr', function () {
    describe('with breakpoints "760,960,1024,1440,2000"', function () {
        var screenSizes = [
            { width: 3840, height: 2160 }, // 4k
            { width: 1920, height: 1080 }, // Full HD
            { width: 1080, height: 1920 }, // HTC One
            { width: 768, height: 1280 }, // Google Nexus 4
            { width: 640, height: 960 }, // iPhone 4
        ];
        this.timeout(5000);

        it(`works for ${screenSizes[0].width} × ${screenSizes[0].height} px`, (done) => {
            loadSize(screenSizes[0].width, screenSizes[0].height, (error) => {
                should(error).not.ok;
                should(dirExists(cssDir)).is.true;
                should(listDir(cssDir)).deepEqual(['example-0.css', 'example-3840x3840@16.css', 'example-squeezr.css.php'].sort());
                should(isLink(path.join(cssDir, 'example-3840x3840@16.css'), path.join(cssDir, 'example-0.css'))).is.true;
                should(dirExists(path.join(cacheDir, 'img'))).is.false;
                done();
            });
        });

        it(`works for ${screenSizes[1].width} × ${screenSizes[1].height} px`, (done) => {
            loadSize(screenSizes[1].width, screenSizes[1].height, (error) => {
                should(error).not.ok;
                should(listDir(cssDir)).deepEqual([
                    'example-0.css',
                    'example-3840x3840@16.css',
                    'example-1920x1920@16.css',
                    'example-squeezr.css.php'
                ].sort());
                should(isLink(path.join(cssDir, 'example-1920x1920@16.css'), path.join(cssDir, 'example-0.css'))).is.true;
                should(dirExists(imgDir)).is.true;
                should(listDir(imgDir)).deepEqual(['example-2000px.jpg']);
                should(isLink(path.join(imgDir, 'example-2000px.jpg'), path.join(explDir, 'img/example.jpg'))).is.true;
                done();
            });
        });

        it(`works for ${screenSizes[2].width} × ${screenSizes[2].height} px`, (done) => {
            loadSize(screenSizes[2].width, screenSizes[2].height, (error) => {
                should(error).not.ok;
                should(listDir(cssDir)).deepEqual([
                    'example-0.css',
                    'example-3840x3840@16.css',
                    'example-1920x1920@16.css',
                    'example-1080x1080@16.css',
                    'example-squeezr.css.php'
                ].sort());
                should(isLink(path.join(cssDir, 'example-1080x1080@16.css'), path.join(cssDir, 'example-0.css'))).is.true;
                should(listDir(imgDir)).deepEqual([
                    'example-1440px.jpg',
                    'example-2000px.jpg'
                ].sort());
                should(isLink(path.join(imgDir, 'example-1440px.jpg'))).is.false;

                var dimensions = sizeOf(path.join(imgDir, 'example-1440px.jpg'));
                should(dimensions).has.property('width', 1440);
                done();
            });
        });

        it(`works for ${screenSizes[3].width} × ${screenSizes[3].height} px`, (done) => {
            loadSize(screenSizes[3].width, screenSizes[3].height, (error) => {
                should(error).not.ok;
                should(listDir(cssDir)).deepEqual([
                    'example-0.css',
                    'example-3840x3840@16.css',
                    'example-1920x1920@16.css',
                    'example-1080x1080@16.css',
                    'example-768x768@16.css',
                    'example-squeezr.css.php'
                ].sort());
                should(isLink(path.join(cssDir, 'example-768x768@16.css'), path.join(cssDir, 'example-0.css'))).is.true;
                should(listDir(imgDir)).deepEqual([
                    'example-960px.jpg',
                    'example-1440px.jpg',
                    'example-2000px.jpg'
                ].sort());
                should(isLink(path.join(imgDir, 'example-960px.jpg'))).is.false;

                var dimensions = sizeOf(path.join(imgDir, 'example-960px.jpg'));
                should(dimensions).has.property('width', 960);
                done();
            });
        });

        it(`works for ${screenSizes[4].width} × ${screenSizes[4].height} px`, (done) => {
            loadSize(screenSizes[4].width, screenSizes[4].height, (error) => {
                should(error).not.ok;
                should(listDir(cssDir)).deepEqual([
                    'example-0.css',
                    'example-3840x3840@16.css',
                    'example-1920x1920@16.css',
                    'example-1080x1080@16.css',
                    'example-768x768@16.css',
                    'example-640x640@16.css',
                    'example-squeezr.css.php'
                ].sort());
                should(isLink(path.join(cssDir, 'example-640x640@16.css'), path.join(cssDir, 'example-0.css'))).is.true;
                should(listDir(imgDir)).deepEqual([
                    'example-760px.jpg',
                    'example-960px.jpg',
                    'example-1440px.jpg',
                    'example-2000px.jpg'
                ].sort());
                should(isLink(path.join(imgDir, 'example-760px.jpg'))).is.false;

                var dimensions = sizeOf(path.join(imgDir, 'example-760px.jpg'));
                should(dimensions).has.property('width', 760);
                done();
            });
        });
    });
});
