<?php

/**
 * Server side treatment of media queries
 *
 * @package        squeezr
 * @author        Joschi Kuphal <joschi@kuphal.net>
 * @copyright    Copyright © 2017 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link        http://squeezr.it
 * @github        https://github.com/jkphl/squeezr
 * @twitter        @squeezr
 * @license        https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since        1.0b
 * @version        1.0b
 */

namespace Tollwerk\Squeezr;

// Require the abstract engine base class
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Squeezr.php';

/**
 * Image engine
 *
 * @package        squeezr
 * @author        Joschi Kuphal <joschi@kuphal.net>
 * @since        1.0b
 * @version        1.0b
 */
class Image extends \Tollwerk\Squeezr
{
    /**
     * Requested image file (relative path to document root)
     *
     * @var string
     */
    protected $_relativeImagePath = null;
    /**
     * Requested image file (absolute path)
     *
     * @var string
     */
    protected $_absoluteImagePath = null;
    /**
     * Cached image file (absolute path)
     *
     * @var string
     */
    protected $_absoluteCacheImagePath = null;
    /**
     * Parent directory of cached image file (absolute path)
     *
     * @var string
     */
    protected $_absoluteCacheImageDir = null;
    /**
     * Valid quantizers
     *
     * @var array
     */
    protected static $_quantizers = array(
        self::QUANTIZER_INTERNAL => false,
        self::QUANTIZER_PNGQUANT => '`which pngquant` --force --transbug --ext ".png" --speed %s %s',
        self::QUANTIZER_PNGNQ => '`which pngnq` -f -e ".png" -s %s %s',
    );
    /**
     * Internal quantizer (GD based)
     *
     * @var string
     */
    const QUANTIZER_INTERNAL = 'internal';
    /**
     * pngquant quantizer
     *
     * @var string
     */
    const QUANTIZER_PNGQUANT = 'pngquant';
    /**
     * pngnq quantizer
     *
     * @var string
     */
    const QUANTIZER_PNGNQ = 'pngnq';


    /************************************************************************************************
     * PUBLIC METHODS
     ***********************************************************************************************/

    /**
     * Send the cached and possibly downsampled image
     *
     * @return void
     */
    public function send()
    {

        // Set some defaults
        $extension = null;
        $returnImage = $this->_absoluteImagePath;

        // If the GD extension is not available
        if (!extension_loaded('gd') && (!function_exists('dl') || !dl('gd.so'))) {
            $this->_addErrorHeader(\Tollwerk\Squeezr\Exception::GD_UNAVAILABLE_MSG,
                \Tollwerk\Squeezr\Exception::GD_UNAVAILABLE);

            // If the cache directory doesn't exist or is not writable: Error
        } elseif ((!@is_dir($this->_absoluteCacheImageDir) && !@mkdir($this->_absoluteCacheImageDir, 0777,
                    true)) || !@is_writable($this->_absoluteCacheImageDir)
        ) {
            $this->_addErrorHeader(\Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY_MSG,
                \Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY);

            // If the squeezr screen cookie is not available or invalid
        } elseif (empty($_COOKIE['squeezr_screen']) || !preg_match("%^(\d+)x(\d+)\@(\d+(?:\.\d+)?)$%",
                $_COOKIE['squeezr_screen'], $squeezr)
        ) {
            $this->_addErrorHeader(\Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE_MSG,
                \Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE);

            // Copy & resample original image
        } else {
            $errors = array();
            $returnImage = $this->squeeze($this->_absoluteImagePath, $this->_absoluteCacheImagePath, SQUEEZR_BREAKPOINT,
                $errors);
            foreach ($errors as $errNo => $errMsg) {
                $this->_addErrorHeader(sprintf($errMsg, $this->_relativeImagePath), $errNo);
            }
        }

        // Send the image along with it's HTTP headers
        $extension = ($extension === null) ? pathinfo($returnImage, PATHINFO_EXTENSION) : $extension;
        $this->_sendFile($returnImage, 'image/'.((strtolower($extension) == 'jpg') ? 'jpeg' : strtolower($extension)),
            true);
        exit;
    }

    /************************************************************************************************
     * PRIVATE METHODS
     ***********************************************************************************************/

    /**
     * Constructor
     *
     * @param string $image Image file
     * @throws \Tollwerk\Squeezr\Exception            If the requested image file doesn't exist
     */
    protected function __construct($image)
    {
        $this->_relativeImagePath = ltrim($image, DIRECTORY_SEPARATOR);
        $this->_absoluteImagePath = rtrim(SQUEEZR_DOCROOT,
                DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->_relativeImagePath;

        $imageInfo = pathinfo($this->_relativeImagePath);
        $this->_absoluteCacheImagePath = SQUEEZR_CACHEROOT.$imageInfo['dirname'].DIRECTORY_SEPARATOR.$imageInfo['filename'].'-'.SQUEEZR_BREAKPOINT.'.'.$imageInfo['extension'];

        // Check if the requested image file does exist (should always be the case as mod_rewrite does also check this before)
        if (!@is_readable($this->_absoluteImagePath)) {
            throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::INVALID_IMAGE_FILE_MSG,
                $this->_absoluteImagePath), \Tollwerk\Squeezr\Exception::INVALID_IMAGE_FILE);
        }

        $this->_absoluteCacheImageDir = dirname($this->_absoluteCacheImagePath);
    }

    /************************************************************************************************
     * STATIC METHODS
     ***********************************************************************************************/

    /**
     * Instanciate an image adaptor
     *
     * @param string $image Image file
     * @return \Tollwerk\Squeezr\Image                Instance reference
     * @throws \Tollwerk\Squeezr\Exception            If the requested image cache file is invalid
     */
    public static function instance($image)
    {
        return new self($image);
    }


    /**
     * Squeeze a particular source file
     *
     * @param string $source Source file
     * @param string $target Target file
     * @param string $breakpoint Breakpoint
     * @param array $errors Errors
     * @return string                    Result file path
     * @link                            http://www.idux.com/2011/02/27/what-are-index-and-alpha-transparency/
     */
    public static function squeeze($source, $target, $breakpoint = SQUEEZR_BREAKPOINT, array &$errors = array())
    {
        $returnImage = null;

        // Determine the image dimensions
        list($width, $height, $type) = getImageSize($source);

        // Determine the target width (considering the breakpoint)
        $targetWidth = intval($breakpoint);

        // If the image image has to be downsampled at all
        if ($width > $targetWidth) {

            // Prepare basic target parameters
            $targetHeight = round($targetWidth * $height / $width);

            switch ($type) {
                case IMAGETYPE_PNG:
                    $saved = self::_downscalePng($source, $width, $height, $target, $targetWidth, $targetHeight);
                    break;
                case IMAGETYPE_JPEG:
                    $saved = self::_downscaleJpeg($source, $width, $height, $target, $targetWidth, $targetHeight);
                    break;
                case IMAGETYPE_GIF:
                    $saved = self::_downscaleGif($source, $width, $height, $target, $targetWidth, $targetHeight);
                    break;
                default:
                    $saved = false;
            }

            // If target image could be created: Send it
            if ($saved && @file_exists($target)) {
                $returnImage = $target;

                // Else: Error
            } else {
                $errors[\Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE] = \Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE_MSG;
            }

            // Else: No downsampling necessary, try to cache a copy of the original image to avoid subsequent squeeze attempts
        } elseif (!@symlink($source, $target) && (SQUEEZR_IMAGE_COPY_UNDERSIZED ? !@copy($source, $target) : true)) {
            $errors[\Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE] = \Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE_MSG;

            // Else: Return target image
        } else {
            $returnImage = $target;
        }

        return $returnImage;
    }

    /**
     * Downscale a PNG image
     *
     * @param string $source Source image path
     * @param int $width Source image width
     * @param int $height Source image height
     * @param string $target Target image path
     * @param int $targetWidth Target image width
     * @param int $targetHeight Target image height
     * @return boolean                    Downscaled image has been saved
     * @link                            http://en.wikipedia.org/wiki/Portable_Network_Graphics#Color_depth
     * @link                            http://perplexed.co.uk/1814_png_optimization_with_gd_library.htm
     */
    protected static function _downscalePng($source, $width, $height, $target, $targetWidth, $targetHeight)
    {
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Determine active quantizer
        if (SQUEEZR_IMAGE_PNG_QUANTIZER === false) {
            $quantizer = false;
        } else {
            $quantizer = strtolower(trim(SQUEEZR_IMAGE_PNG_QUANTIZER));
            $quantizer = ($quantizer && array_key_exists($quantizer,
                    self::$_quantizers)) ? $quantizer : self::QUANTIZER_INTERNAL;
        }

        /**
         * Determine the PNG type
         *
         * 0 - Grayscale
         * 2 - RGB
         * 3 - RGB with palette (= indexed)
         * 4 - Grayscale + alpha
         * 6 - RGB + alpha
         */
        $sourceType = ord(@file_get_contents($source, false, null, 25, 1));
        $sourceImage = @imagecreatefrompng($source);
        $sourceIndexed = !!($sourceType & 1);
        $sourceAlpha = !!($sourceType & 4);
        $sourceTransparentIndex = imagecolortransparent($sourceImage);
        $sourceIndexTransparency = $sourceTransparentIndex >= 0;
        $sourceTransparentColor = $sourceIndexTransparency ? imagecolorsforindex($sourceImage,
            $sourceTransparentIndex) : null;
        $sourceColors = imagecolorstotal($sourceImage);

        // Determine if the resulting image should be quantized
        $quantize = $quantizer && ($sourceIndexed || (($sourceColors > 0) && ($sourceColors <= 256)));

        // Support transparency on the target image if necessary
        if ($sourceIndexTransparency || $sourceAlpha) {
            self::_enableTranparency($targetImage, $sourceTransparentColor);
        }

        // If the resulting image should be quantized
        if ($quantize) {

            // If an external quantizer is available: Convert the source image to a TrueColor before downsampling
            if ($quantize && ($quantizer != self::QUANTIZER_INTERNAL)) {
                $tmpSourceImage = imagecreatetruecolor($width, $height);

                // Enable transparency if necessary (index or alpha channel)
                if ($sourceIndexTransparency || $sourceAlpha) {
                    self::_enableTranparency($tmpSourceImage, $sourceTransparentColor);
                }

                imagecopy($tmpSourceImage, $sourceImage, 0, 0, 0, 0, $width, $height);
                imagedestroy($sourceImage);
                $sourceImage = $tmpSourceImage;
                unset($tmpSourceImage);

                // Else: Use internal quantizer (convert to palette before downsampling)
            } elseif ($sourceIndexTransparency || $sourceAlpha) {
                imagetruecolortopalette($targetImage, true, $sourceColors);
            }
        }

// 		trigger_error(var_export(array(
// 			'type'						=> $sourceType,
// 			'indexed'					=> $sourceIndexed,
// 			'indextransp'				=> $sourceIndexTransparency,
// 			'alpha'						=> $sourceAlpha,
// 			'transindex'				=> $sourceTransparentIndex,
// 			'colors'					=> $sourceColors,
// 			'quantize'					=> $quantize,
// 			'quantizer'					=> $quantizer
// 		), true));

        // Resize & resample the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Sharpen image if possible and requested
        if (!$quantize || SQUEEZR_IMAGE_FORCE_SHARPEN) {
            self::_sharpenImage($targetImage, $width, $targetWidth);
        }

        // If the image should be quantized internally
        if ($quantize && ($quantizer == self::QUANTIZER_INTERNAL)) {
            imagetruecolortopalette($targetImage, true, $sourceColors);
        }

        // Save the image
        $saved = imagepng($targetImage, $target);

        // Destroy the source image descriptor
        imagedestroy($sourceImage);

        // Destroy the target image descriptor
        imagedestroy($targetImage);

        // If the image should be quantized using an external tool
        if ($saved && $quantize && ($quantizer != self::QUANTIZER_INTERNAL)) {
            $cmd = sprintf(self::$_quantizers[$quantizer], max(1, min(10, intval(SQUEEZR_IMAGE_PNG_QUANTIZER_SPEED))),
                escapeshellarg($target));
            @exec($cmd);
        }

        return $saved;
    }

    /**
     * Downscale a JPEG image
     *
     * @param string $source Source image path
     * @param int $width Source image width
     * @param int $height Source image height
     * @param string $target Target image path
     * @param int $targetWidth Target image width
     * @param int $targetHeight Target image height
     * @return boolean                    Downscaled image has been saved
     */
    protected static function _downscaleJpeg($source, $width, $height, $target, $targetWidth, $targetHeight)
    {
        $sourceImage = @imagecreatefromjpeg($source);
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Enable interlacing for progressive JPEGs
        imageinterlace($targetImage, true);

        // Resize, resample and sharpen the image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        self::_sharpenImage($targetImage, $width, $targetWidth);

        // Save the target JPEG image
        $saved = imagejpeg($targetImage, $target, min(100, max(1, intval(SQUEEZR_IMAGE_JPEG_QUALITY))));

        // Destroy the source image descriptor
        imagedestroy($sourceImage);

        // Destroy the target image descriptor
        imagedestroy($targetImage);

        return $saved;
    }

    /**
     * Downscale a GIF image
     *
     * @param string $source Source image path
     * @param int $width Source image width
     * @param int $height Source image height
     * @param string $target Target image path
     * @param int $targetWidth Target image width
     * @param int $targetHeight Target image height
     * @return boolean                    Downscaled image has been saved
     */
    protected static function _downscaleGif($source, $width, $height, $target, $targetWidth, $targetHeight)
    {
        $sourceImage = @imagecreatefromgif($source);
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Determine the transparent color
        $sourceTransparentIndex = imagecolortransparent($sourceImage);
        $sourceTransparentColor = ($sourceTransparentIndex >= 0) ? imagecolorsforindex($sourceImage,
            $sourceTransparentIndex) : null;

        // Allocate transparency for the target image if needed
        if ($sourceTransparentColor !== null) {
            $targetTransparentColor = imagecolorallocate($targetImage, $sourceTransparentColor['red'],
                $sourceTransparentColor['green'], $sourceTransparentColor['blue']);
            $targetTransparentIndex = imagecolortransparent($targetImage, $targetTransparentColor);
            imageFill($targetImage, 0, 0, $targetTransparentIndex);
        }

        // Resize & resample the image (no sharpening)
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Save the target GIF image
        $saved = imagegif($targetImage, $target);

        // Destroy the source image descriptor
        imagedestroy($sourceImage);

        // Destroy the target image descriptor
        imagedestroy($targetImage);

        return $saved;
    }

    /**
     * Sharpen an image
     *
     * @param resource $image Image resource
     * @param int $width Original image width
     * @param int $targetWidth Downsampled image width
     * @return void
     */
    protected static function _sharpenImage($image, $width, $targetWidth)
    {

        // Sharpen image if possible and requested
        if (!!SQUEEZR_IMAGE_SHARPEN && function_exists('imageconvolution')) {
            $intFinal = $targetWidth * (750.0 / $width);
            $intA = 52;
            $intB = -0.27810650887573124;
            $intC = .00047337278106508946;
            $intRes = $intA + $intB * $intFinal + $intC * $intFinal * $intFinal;
            $intSharpness = max(round($intRes), 0);
            $arrMatrix = array(
                array(-1, -2, -1),
                array(-2, $intSharpness + 12, -2),
                array(-1, -2, -1)
            );
            imageconvolution($image, $arrMatrix, $intSharpness, 0);
        }
    }

    /**
     * Enable transparency on an image resource
     *
     * @param resource $image Image resource
     * @param array $transparentColor Transparent color
     * @return void
     */
    protected static function _enableTranparency($image, array $transparentColor = null)
    {
        if ($transparentColor === null) {
            $transparentColor = array('red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 127);
        }
        $targetTransparent = imagecolorallocatealpha($image, $transparentColor['red'], $transparentColor['green'],
            $transparentColor['blue'], $transparentColor['alpha']);
        imagecolortransparent($image, $targetTransparent);
        imageFill($image, 0, 0, $targetTransparent);
        imagealphablending($image, false);
        imagesavealpha($image, true);
    }
}