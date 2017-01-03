<?php

/**
 * Cache cleaning
 *
 * @package squeezr
 * @author Joschi Kuphal <joschi@kuphal.net>
 * @copyright Copyright © 2017 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link http://squeezr.it
 * @github https://github.com/jkphl/squeezr
 * @twitter @squeezr
 * @license https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since 1.0b
 * @version 1.0b
 */

namespace Tollwerk\Squeezr;

/**
 * Cache cleaner engine
 *
 * @package        squeezr
 * @author Joschi Kuphal <joschi@kuphal.net>
 * @since 1.0b
 * @version 1.0b
 */
class Cleaner
{
    /**
     * Base path
     *
     * @var string
     */
    protected $_base = null;

    /************************************************************************************************
     * PUBLIC METHODS
     ***********************************************************************************************/

    /**
     * Recursively clean the cache directory given as base path
     *
     * @return void
     */
    public function clean()
    {
        $this->_cleanDirectory();
    }

    /************************************************************************************************
     * PRIVATE METHODS
     ***********************************************************************************************/

    /**
     * Constructor
     *
     * @param string $base Base path
     */
    private function __construct($base)
    {
        $this->_base = $base;
    }

    /**
     * Recursively clean a subdirectory
     *
     * @param string $path Subdirectory path
     * @return int                                        Number of contained resources
     */
    private function _cleanDirectory($path = '')
    {
        $resources = scandir($this->_base.$path);
        $resourceCount = count($resources);
        $links =
        $files = array();

        // Run through the directory contents
        foreach ($resources as $resource) {
            $absResource = $this->_base.$path.DIRECTORY_SEPARATOR.$resource;
            $origResource = SQUEEZR_DOCROOT.$path.DIRECTORY_SEPARATOR.$resource;

            // Ignore symbolic links to current and parent directory
            if (@is_dir($absResource) && (($resource == '.') || ($resource == '..'))) {
                --$resourceCount;
                continue;
            }

            // If it's a directory ...
            if (@is_dir($absResource)) {

                // Clean recursively and remove it alltogether in case it remains empty
                if (!@is_dir($origResource) || !$this->_cleanDirectory(ltrim($path.DIRECTORY_SEPARATOR.$resource,
                        DIRECTORY_SEPARATOR))
                ) {
                    @exec('rm -R '.escapeshellarg($absResource));
                    --$resourceCount;
                }

                // Else if it's a symbolic link: register it
            } elseif (@is_link($absResource)) {
                $links[] = $resource;

                // Else if it's a regular file: register it
            } elseif (@is_file($absResource)) {
                $files[] = $resource;

                // Else: No idea ... delete it!
            } else {
                if (@unlink($absResource)) {
                    --$resourceCount;
                }
            }
        }

        // Check & clean regular files
        foreach ($files as $file) {
            $absResource = $this->_base.$path.DIRECTORY_SEPARATOR.$file;

            // Proceed based on file extension
            switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {

                // CSS file
                case 'css':

                    // If it's a breakpoint CSS file
                    if (preg_match("%^(.+)\-\d+\.css$%i", $file, $breakpointCssFile)) {
                        $origResource = SQUEEZR_DOCROOT.$path.DIRECTORY_SEPARATOR.$breakpointCssFile[1].'.css';

                        // Else: No idea ... delete it!
                    } else {
                        if (@unlink($absResource)) {
                            --$resourceCount;
                        }
                        continue 2;
                    }
                    break;

                // PHP cache file
                case 'php':

                    // If it's a PHP cache file
                    if (preg_match("%^(.+)\-squeezr\.css\.php$%i", $file, $phpCacheFile)) {
                        $origResource = SQUEEZR_DOCROOT.$path.DIRECTORY_SEPARATOR.$phpCacheFile[1].'.css';

                        // Else: No idea ... delete it!
                    } else {
                        if (@unlink($absResource)) {
                            --$resourceCount;
                        }
                        continue 2;
                    }
                    break;

                // Image files
                case 'png':
                case 'jpg':
                case 'jpeg':
                case 'gif':
                    $origResource = SQUEEZR_DOCROOT.$path.DIRECTORY_SEPARATOR.$file;
                    break;

                // Other extensions: Ignore
                default:
                    continue 2;
                    break;
            }

            // Remove the cache file if the original file doesn't exist anymore or is younger
            if (!@is_file($origResource) || (@filemtime($origResource) > @filemtime($absResource))) {
                if (@unlink($absResource)) {
                    --$resourceCount;
                }
            }
        }

        // Check & clean symbolic links
        foreach ($links as $link) {
            $absResource = $this->_base.$path.DIRECTORY_SEPARATOR.$link;
            $origResource = @readlink($absResource);

            // Delete the symbolic link if the original file doesn't exist anymore
            if ((!$origResource || !@is_file($origResource)) && @unlink($absResource)) {
                --$resourceCount;
            }
        }

        return $resourceCount;
    }

    /************************************************************************************************
     * STATIC METHODS
     ***********************************************************************************************/

    /**
     * Instanciate a cache cleaner
     *
     * @param string $base Base path
     * @return \Tollwerk\Squeezr\Cleaner                Instance reference
     */
    public static function instance($base)
    {
        return new self($base);
    }
}
