<?php

namespace Tollwerk;

/**
 * Abstract Squeezr base class containing some common functionality
 * 
 * @author joschi
 *
 */
abstract class Squeezr {
	/**
	 * HTTP headers
	 *
	 * @var array
	 */
	private $_headers = array();
	
	/************************************************************************************************
	 * PRIVATE METHODS
	 ***********************************************************************************************/
	
	/**
	 * Add an HTTP error header
	 *
	 * @param string $error				Error message
	 * @param int $errorNumber			Error number
	 * @return void
	 */
	private function _addErrorHeader($error, $errorNumber) {
		$this->_headers['X-Squeezr-Error']			= $error;
		$this->_headers['X-Squeezr-Errno']			= $errorNumber;
	}
	
	/**
	 * Send a file along with it's appropriate headers
	 *
	 * @param string $src			File path
	 * @param string $mime			MIME type
	 * @param boolean $cache		Optional: The client browser should cache the file
	 * @return void
	 */
	protected function _sendFile($src, $mime, $cache = true) {
		
		// Custom headers
		foreach ($this->_headers as $header => $value) {
			header($header.': '.$value);
		}
	
		// Send basic headers
		header('Content-Type: '.$mime);
		header('Content-Length: '.filesize($src));
	
		// If the client browser should cache the image
		if ($cache) {
			header('Cache-Control: private, max-age='.SQUEEZR_CACHE_LIFETIME);
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + SQUEEZR_CACHE_LIFETIME).' GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($src)).' GMT');
			header('ETag: '.$this->_calculateFileETag($src));
				
			// Else
		} else {
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() - SQUEEZR_CACHE_LIFETIME).' GMT');
		}
	
		readfile($src);
		exit;
	}
	
	/**
	 * Calculate the ETag of a file
	 *
	 * @param string $src			File path
	 * @return string				File ETag
	 */
	private function _calculateFileETag($src) {
		$fileStats					= stat($src);
		$fileFulltime				= @exec('ls --full-time '.escapeshellarg($src));
		$fileMtime					= str_pad(preg_match("%\d+\:\d+\:\d+\.(\d+)%", $fileFulltime, $fileMtime) ? substr($fileStats['mtime'].$fileMtime[1], 0, 16) : $fileStats['mtime'], 16, '0');
		return sprintf('"%x-%x-%s"', $fileStats['ino'], $fileStats['size'], base_convert($fileMtime, 10, 16));
	}
}