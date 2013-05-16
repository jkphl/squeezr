<?php

namespace Tollwerk\Squeezr;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Squeezr.php';

/**
 * Server side image adaptor
 * 
 * @author joschi
 *
 */
class Image extends \Tollwerk\Squeezr {
	/**
	 * Requested image file (relative path to document root)
	 *
	 * @var string
	 */
	private $_relativeImagePath = null;
	/**
	 * Requested image file (absolute path)
	 *
	 * @var string
	 */
	private $_absoluteImagePath = null;
	/**
	 * Cached image file (absolute path)
	 *
	 * @var string
	 */
	private $_absoluteCacheImagePath = null;
	/**
	 * Parent directory of cached image file (absolute path)
	 * 
	 * @var string
	 */
	private $_absoluteCacheImageDir = null;
	
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
		
		// If the Squeezr screen cookie is not available or invalid
		} elseif (empty($_COOKIE['squeezr_screen']) || !preg_match("%^(\d+)x(\d+)\@(\d+(?:\.\d+)?)$%", $_COOKIE['squeezr_screen'], $squeezr)) {
			$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE_MSG, \Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE);
		
		// Copy & resample original image
		} else {
		
			// Determine the image dimensions
			list($width, $height)		= getImageSize($this->_absoluteImagePath);
		
			// Determine the target width (considering the breakpoint)
			$targetWidth				= intval(SQUEEZR_BREAKPOINT);
			
			// If the image image has to be downsampled at all
			if ($width > $targetWidth) {
		
				// Prepare target parameters
				$targetHeight			= round($targetWidth * $height / $width);
				$targetImage	        = ImageCreateTrueColor($targetWidth, $targetHeight);
				$extension				= strtolower(pathinfo($this->_absoluteImagePath, PATHINFO_EXTENSION));
		
				// Create source image
				switch ($extension) {
					case 'png':
						$sourceImage	= @ImageCreateFromPng($this->_absoluteImagePath);
						imagealphablending($targetImage, false);
						imagesavealpha($targetImage,true);
						$transparent	= imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
						imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
						break;
					case 'gif':
						$sourceImage	= @ImageCreateFromGif($this->_absoluteImagePath);
						break;
					default:
						$sourceImage	= @ImageCreateFromJpeg($this->_absoluteImagePath);
		
						// Enable interlacing for progressive JPEGs
						ImageInterlace($targetImage, true);
						break;
				}
		
				// Resize & resample the image
				ImageCopyResampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
		
				// Destroy the source file descriptor
				ImageDestroy($sourceImage);
		
				// Sharpen image if possible and requested
				if (!!SQUEEZR_SHARPEN && function_exists('imageconvolution')) {
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
						$saved			= ImagePng($targetImage, $this->_absoluteCacheImagePath);
						break;
					case 'gif':
						$saved			= ImageGif($targetImage, $this->_absoluteCacheImagePath);
						break;
					default:
						$saved			= ImageJpeg($targetImage, $this->_absoluteCacheImagePath, min(100, max(1, intval(SQUEEZR_JPEG_QUALITY))));
						break;
				}
		
				// Destroy target image descriptor
				ImageDestroy($targetImage);
		
				// If target image could be created: Send it
				if ($saved && @file_exists($this->_absoluteCacheImagePath)) {
					$returnImage		= $this->_absoluteCacheImagePath;
						
				// Else: Error
				} else {
					$this->_addErrorHeader(sprintf(\Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE_MSG, $this->_relativeImagePath), \Tollwerk\Squeezr\Exception::FAILED_DOWNSAMPLE_CACHE);
				}
		
			// Else: No downsampling necessary, try to cache a copy of the original image to avoid subsequent squeeze attempts
			} elseif (!@symlink($this->_absoluteImagePath, $this->_absoluteCacheImagePath) && (SQUEEZR_CACHE_UNDERSIZED ? !@copy($this->_absoluteImagePath, $this->_absoluteCacheImagePath) : true)) {
				$this->_addErrorHeader(sprintf(\Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE_MSG, $this->_relativeImagePath), \Tollwerk\Squeezr\Exception::FAILED_COPY_CACHE);
		
			// Else: Return target image
			} else {
				$returnImage			= $this->_absoluteCacheImagePath;
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
	 * @param string $image										Image file
	 * @throws \Tollwerk\Squeezr\Exception				If the requested image file doesn't exist
	 */
	private function __construct($image) {
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
	 * @param string $image										Image file
	 * @return \Tollwerk\Squeezr\Image					Instance reference
	 * @throws \Tollwerk\Squeezr\Exception				If the requested image cache file is invalid
	 */
	public static function instance($image) {
		return new self($image);
	}
}