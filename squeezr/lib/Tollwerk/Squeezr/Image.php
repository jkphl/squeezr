<?php

/**
 * Server side treatment of media queries
 * 
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

// Require the abstract engine base class
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Squeezr.php';

/**
 * Image engine
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
class Image extends \Tollwerk\Squeezr {
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
	
	/************************************************************************************************
	 * PUBLIC METHODS
	 ***********************************************************************************************/
	
	/**
	 * Send the cached and possibly downsampled image
	 * 
	 * @return void
	 */
	public function send() {
		
		// Set some defaults
		$extension						= null;
		$returnImage					= $this->_absoluteImagePath;
		
		// If the GD extension is not available
		if (!extension_loaded('gd') && (!function_exists('dl') || !dl('gd.so'))) {
			$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::GD_UNAVAILABLE_MSG, \Tollwerk\Squeezr\Exception::GD_UNAVAILABLE);
		
		// If the cache directory doesn't exist or is not writable: Error
		} elseif ((!@is_dir($this->_absoluteCacheImageDir) && !@mkdir($this->_absoluteCacheImageDir, 0777, true)) || !@is_writable($this->_absoluteCacheImageDir)) {
			$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY_MSG, \Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY);
		
		// If the squeezr screen cookie is not available or invalid
		} elseif (empty($_COOKIE['squeezr_screen']) || !preg_match("%^(\d+)x(\d+)\@(\d+(?:\.\d+)?)$%", $_COOKIE['squeezr_screen'], $squeezr)) {
			$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE_MSG, \Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE);
		
		// Copy & resample original image
		} else {
			$errors						= array();
			$returnImage				= $this->squeeze($this->_absoluteImagePath, $this->_absoluteCacheImagePath, SQUEEZR_BREAKPOINT, $errors);
			foreach ($errors as $errNo => $errMsg) {
				$this->_addErrorHeader(sprintf($errMsg, $this->_relativeImagePath), $errNo);
			}
		}
		
		// Send the image along with it's HTTP headers
		$extension						= ($extension === null) ? pathinfo($returnImage, PATHINFO_EXTENSION) : $extension;
		$this->_sendFile($returnImage, 'image/'.((strtolower($extension) == 'jpg') ? 'jpeg' : strtolower($extension)), true);
		exit;
	}
	
	/************************************************************************************************
	 * PRIVATE METHODS
	 ***********************************************************************************************/
	
	/**
	 * Constructor
	 *
	 * @param string $image							Image file
	 * @throws \Tollwerk\Squeezr\Exception			If the requested image file doesn't exist
	 */
	protected function __construct($image) {
		$this->_relativeImagePath					= ltrim($image, DIRECTORY_SEPARATOR);
		$this->_absoluteImagePath					= rtrim(SQUEEZR_DOCROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->_relativeImagePath;
		
		$imageInfo									= pathinfo($this->_relativeImagePath);
		$this->_absoluteCacheImagePath				= SQUEEZR_CACHEROOT.$imageInfo['dirname'].DIRECTORY_SEPARATOR.$imageInfo['filename'].'-'.SQUEEZR_BREAKPOINT.'.'.$imageInfo['extension'];
				
		// Check if the requested image file does exist (should always be the case as mod_rewrite does also check this before)
		if (!@is_readable($this->_absoluteImagePath)) {
			throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::INVALID_IMAGE_FILE_MSG, $this->_absoluteImagePath), \Tollwerk\Squeezr\Exception::INVALID_IMAGE_FILE);
		}
		
		$this->_absoluteCacheImageDir				= dirname($this->_absoluteCacheImagePath);
	}
	
	/************************************************************************************************
	 * STATIC METHODS
	 ***********************************************************************************************/
	
	/**
	 * Instanciate an image adaptor
	 *
	 * @param string $image							Image file
	 * @return \Tollwerk\Squeezr\Image				Instance reference
	 * @throws \Tollwerk\Squeezr\Exception			If the requested image cache file is invalid
	 */
	public static function instance($image) {
		return new self($image);
	}
	

	/**
	 * Squeeze a particular source file
	 *
	 * @param string $source			Source file
	 * @param string $target			Target file
	 * @param string $breakpoint		Breakpoint
	 * @param array $errors				Errors
	 * @return string					Result file path
	 */
	public static function squeeze($source, $target, $breakpoint = SQUEEZR_BREAKPOINT, array &$errors = array()) {
	
		// Determine the image dimensions
		list($width, $height)		= getImageSize($source);
	
		// Determine the target width (considering the breakpoint)
		$targetWidth				= intval($breakpoint);
	
		// If the image image has to be downsampled at all
		if ($width > $targetWidth) {
	
			// Prepare target parameters
			$targetHeight			= round($targetWidth * $height / $width);
			$targetImage	        = imagecreatetruecolor($targetWidth, $targetHeight);
			$extension				= strtolower(pathinfo($source, PATHINFO_EXTENSION));
	
			// Create source image
			switch ($extension) {
					
				// PNG files
				case 'png':
					$sourceImage	= @imagecreatefrompng($source);
					imagealphablending($targetImage, false);
					imagesavealpha($targetImage,true);
					$transparent	= imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
					imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
					break;
	
					// GIF files
				case 'gif':
					$sourceImage	= @imagecreatefromgif($source);
					break;
	
					// JPEG files
				default:
					$sourceImage	= @imagecreatefromjpeg($source);
	
					// Enable interlacing for progressive JPEGs
					imageinterlace($targetImage, true);
					break;
			}
	
			// Resize & resample the image
			imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
	
			// Destroy the source file descriptor
			imagedestroy($sourceImage);
	
			// Sharpen image if possible and requested
			if (!!SQUEEZR_IMAGE_SHARPEN && function_exists('imageconvolution')) {
				$intFinal			= $targetWidth * (750.0 / $width);
				$intA     			= 52;
				$intB     			= -0.27810650887573124;
				$intC     			= .00047337278106508946;
				$intRes   			= $intA + $intB * $intFinal + $intC * $intFinal * $intFinal;
				$intSharpness		= max(round($intRes), 0);
				$arrMatrix			= array(
						array(-1, -2, -1),
						array(-2, $intSharpness + 12, -2),
						array(-1, -2, -1)
				);
				imageconvolution($targetImage, $arrMatrix, $intSharpness, 0);
			}
	
			// Save target image
			switch ($extension) {
				case 'png':
					$saved			= ImagePng($targetImage, $target);
					break;
				case 'gif':
					$saved			= ImageGif($targetImage, $target);
					break;
				default:
					$saved			= ImageJpeg($targetImage, $target, min(100, max(1, intval(SQUEEZR_IMAGE_JPEG_QUALITY))));
					break;
			}
	
			// Destroy target image descriptor
			imagedestroy($targetImage);
	
			// If target image could be created: Send it
			if ($saved && @file_exists($target)) {
				$returnImage		= $target;
	
				// Else: Error
			} else {
				$errors[\Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE] = \Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE_MSG;
			}
	
			// Else: No downsampling necessary, try to cache a copy of the original image to avoid subsequent squeeze attempts
		} elseif (!@symlink($source, $target) && (SQUEEZR_IMAGE_COPY_UNDERSIZED ? !@copy($source, $target) : true)) {
			$errors[\Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE] = \Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE_MSG;
	
			// Else: Return target image
		} else {
			$returnImage			= $target;
		}
	
		return $returnImage;
	}
}