<?php

namespace Tollwerk\Squeezr;

/**
 * Server side image adaptor
 * 
 * @author joschi
 *
 */
class Cleaner {
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
	public function clean() {
		if (!$this->_cleanDirectory()) {
			@exec('rm -R '.escapeshellarg($this->_base));
		}
	}
	
	/************************************************************************************************
	 * PRIVATE METHODS
	 ***********************************************************************************************/
	
	/**
	 * Constructor
	 *
	 * @param string $base								Base path
	 */
	private function __construct($base) {
		$this->_base		= $base;
	}
	
	/**
	 * Recursively clean a subdirectory
	 * 
	 * @param string $path								Subdirectory path
	 * @return int										Number of contained resources
	 */
	private function _cleanDirectory($path = '') {
		$resources			= scandir($this->_base.$path);
		$resourceCount		= count($resources);
		
		// Run through the directory contents
		foreach ($resources as $resource) {
			$absResource	= $this->_base.$path.DIRECTORY_SEPARATOR.$resource;
			$origResource	= SQUEEZR_DOCROOT.$path.DIRECTORY_SEPARATOR.$resource;
			
			// Ignore symbolic links to current and parent directory
			if (@is_dir($absResource) && (($resource == '.') || ($resource == '..'))) {
				--$resourceCount;
				continue;
			}
			
			// Recursively run clean all subdirectories
			if (@is_dir($absResource)) {
				if (!@is_dir($origResource) || !$this->_cleanDirectory($path.DIRECTORY_SEPARATOR.$resource)) {
					@exec('rm -R '.escapeshellarg($absResource));
					--$resourceCount;
				}
				
			// If it's a symlink: Check existence of linked target (must be a file)
			} elseif (@is_link($absResource)) {
				if (!@is_file($origResource)) {
					@unlink($absResource);
					--$resourceCount;
				}
				
			// If it's a file: compare modification dates
			} elseif (@is_file($absResource)) {
				if (!@is_file($origResource) || (@filemtime($origResource) > @filemtime($absResource))) {
					@unlink($absResource);
					--$resourceCount;
				}
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
	 * @param string $base								Base path
	 * @return \Tollwerk\Squeezr\Cleaner				Instance reference
	 */
	public static function instance($base) {
		return new self($base);
	}
}