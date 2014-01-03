<?php

/**
 * Server side treatment of media queries
 * 
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @copyright	Copyright © 2014 Joschi Kuphal <joschi@kuphal.net>, http://jkphl.is
 * @link		http://squeezr.it
 * @github		https://github.com/jkphl/squeezr
 * @twitter		@squeezr
 * @license		https://github.com/jkphl/squeezr/blob/master/LICENSE.txt MIT License
 * @since		1.0b
 * @version		1.0b
 */

namespace Tollwerk\Squeezr;

// Require the abstract engine base class
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Squeezr.php';

/**
 * CSS engine / media query proxy
 *
 * @package		squeezr
 * @author		Joschi Kuphal <joschi@kuphal.net>
 * @since		1.0b
 * @version		1.0b
 */
class Css extends \Tollwerk\Squeezr {
	/**
	 * Requested CSS file (relative path to document root)
	 *
	 * @var string
	 */
	protected $_relativeCssPath = null;
	/**
	 * Requested CSS file (absolute path)
	 * 
	 * @var string
	 */
	protected $_absoluteCssPath = null;
	/**
	 * Base path for all cache files
	 * 
	 * @var string
	 */
	protected $_absoluteCachePathBase = null;
	/**
	 * Cached CSS file (absolute path)
	 *
	 * @var string
	 */
	protected $_absoluteCacheCssPath = null;
	/**
	 * Cached CSS file (absolute path)
	 *
	 * @var string
	 */
	protected $_absoluteCachePhpPath = null;
	/**
	 * Parent directory of cached CSS file (absolute path)
	 *
	 * @var string
	 */
	protected $_absoluteCacheCssDir = null;
	/**
	 * Media Query condition catalog
	 * 
	 * @var array
	 */
	protected $_breakpointCatalog = array(
		self::CONDITION_WIDTH			=> array(),
		self::CONDITION_HEIGHT			=> array(),
		self::CONDITION_RESOLUTION		=> array(),
	);
	/**
	 * Media Query condition index
	 *
	 * @var array
	 */
	protected $_breakpointIndex = array();
	/**
	 * CSS Blocks
	 * 
	 * @var array
	 */
	protected $_blocks = array();
	/**
	 * Line lengths
	 * 
	 * @var array
	 */
	protected $_lines = array();
	/**
	 * Minification provider
	 * 
	 * @var \Tollwerk\Squeezr\Css\Minifier
	 */
	protected $_minifier = null;
	/**
	 * Breakpoint types
	 *
	 * @var array
	 */
	protected static $_breakpointTypes = array(
		self::CONDITION_WIDTH			=> 'width',
		self::CONDITION_HEIGHT			=> 'height',
		self::CONDITION_RESOLUTION		=> 'resolution',
	);
	/**
	 * Import at-rule patterns
	 * 
	 * @var array
	 */
	protected static $_importPatterns = array(
		'url('							=> '%^(url\((\042|\047)?([^\)]+)(?(2)\\2)\))\s+(.+)$%',
		'"'								=> '%^(\042([^\042]+)\042)\s+(.+)$%',
		"'"								=> '%^(\047([^\047]+)\047)\s+(.+)$%',
	);
	/**
	 * Width condition
	 *
	 * @var unknown
	*/
	const CONDITION_WIDTH = 1;
	/**
	 * Height condition
	 *
	 * @var unknown
	 */
	const CONDITION_HEIGHT = 2;
	/**
	 * Resolution condition
	 *
	 * @var unknown
	 */
	const CONDITION_RESOLUTION = 4;
	
	/************************************************************************************************
	 * PUBLIC METHODS
	 ***********************************************************************************************/
	
	/**
	 * Parse, squeeze and send the CSS file to the client
	 * 
	 * @param boolean $cache			Cache a copy of the parsed CSS stylesheet
	 * @return void
	 */
	public function send($cache = true) {
		$returnCssFile					= $this->_absoluteCssPath;
		$returnCss						= '';
		
		// Try to process this CSS file
		try {
		
			// If the cache directory doesn't exist or is not writable: Error
			if ((!@is_dir($this->_absoluteCacheCssDir) && !@mkdir($this->_absoluteCacheCssDir, 0777, true)) || !@is_writable($this->_absoluteCacheCssDir)) {
				$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY_MSG, \Tollwerk\Squeezr\Exception::INVALID_TARGET_CACHE_DIRECTORY);
			
			// If the squeezr screen cookie is not available or invalid
			} elseif (empty($_COOKIE['squeezr_css']) || !preg_match("%^(\d+)x(\d+)\@(\d+(?:\.\d+)?)$%", $_COOKIE['squeezr_css'], $squeezr)) {
				$this->_addErrorHeader(\Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE_MSG, \Tollwerk\Squeezr\Exception::MISSING_METRICS_COOKIE);
			
			// Else: Compile and send the breakpoint specific CSS
			} else {
			
				// If there's already a PHP cache file of the requested CSS
				if (@is_file($this->_absoluteCachePhpPath)) {
					$cacheInstance			= include $this->_absoluteCachePhpPath;
				
				// Else: Create a PHP cache file of the requested CSS
				} else {
				
					// Compile the cacheable CSS file PHP class
					$cacheClassCode		= trim($this->_compileCacheClassCode());
				
					// If the file should be cached: Write the PHP cache class file to disk
					if ($cache && !@file_put_contents($this->_absoluteCachePhpPath, $cacheClassCode)) {
						$this->_addErrorHeader(sprintf(\Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE_STR, $this->_absoluteCachePhpPath), \Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE);
							
						// Disable caching alltogether
						$cache				= false;
					}
				
					// Instanciate the cache class
					$cacheInstance			= eval(substr($cacheClassCode, 5));
				}
					
				// If the PHP cache class could be instanciated ...
				if (is_object($cacheInstance)) {
					$returnCss				= strval($cacheInstance);
				
					// Render the breakpoint specific CSS (if not available yet) and create a request specific symlink
					$breakpointsCachePath	= $this->_absoluteCachePathBase.$cacheInstance->getMatchingBreakpoints().'.css';
				
					// If caching is enabled ...
					if ($cache) {
				
						// If the breakpoint specific CSS cannot be created: Caching error
						if (!@is_file($breakpointsCachePath) && !@file_put_contents($breakpointsCachePath, $returnCss)) {
							$this->_addErrorHeader(sprintf(\Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE_STR, $breakpointsCachePath), \Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE);
				
						// Else if the breakpoint specific CSS cannot be symlinked: Caching error
						} elseif (!@symlink($breakpointsCachePath, $this->_absoluteCacheCssPath)) {
							$this->_addErrorHeader(sprintf(\Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE_STR, $this->_absoluteCacheCssPath), \Tollwerk\Squeezr\Exception::FAILED_WRITING_CACHE_FILE);
				
						// Else: Return the cached file
						} else {
							$returnCssFile	= $this->_absoluteCacheCssPath;
						}
					}
				}
			}
			
		// On errors
		} catch (\Tollwerk\Squeezr\Exception $e) {
			$this->_addErrorHeader($e->getMessage(), $e->getCode());
			$returnCssFile					= $this->_absoluteCssPath;
		}
		
		// If the CSS has been cached to a file
		if ($returnCssFile) {
			$this->_sendFile($returnCssFile, 'text/css', true);
			exit;
			
		// Else: Just send the CSS code along with some appropriate headers
		} else {
			header('Content-Type: text/css');
			header('Content-Length: '.strlen($returnCss));
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() - SQUEEZR_CACHE_LIFETIME).' GMT');
			
			die($returnCss);
		}
	}
	
	/************************************************************************************************
	 * PRIVATE METHODS
	 ***********************************************************************************************/
	
	/**
	 * Constructor
	 * 
	 * @param string $css							Requested CSS file (relative to document root)
	 * @throws \Tollwerk\Squeezr\Exception			If the requested CSS file doesn't exist
	 */
	protected function __construct($css) {
		$this->_relativeCssPath						= ltrim($css, DIRECTORY_SEPARATOR);
		$this->_absoluteCssPath						= rtrim(SQUEEZR_DOCROOT, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->_relativeCssPath;
		
		$cssInfo									= pathinfo($this->_relativeCssPath);
		$this->_absoluteCachePathBase				= SQUEEZR_CACHEROOT.$cssInfo['dirname'].DIRECTORY_SEPARATOR.$cssInfo['filename'].'-';
		$this->_absoluteCacheCssPath				= $this->_absoluteCachePathBase.SQUEEZR_BREAKPOINT.'.css';
		$this->_absoluteCachePhpPath				= $this->_absoluteCachePathBase.'squeezr.css.php';
		
		// Check if the requested CSS file does exist
		if (!@is_readable($this->_absoluteCssPath)) {
			throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::INVALID_CSS_FILE_MSG, $this->_absoluteCssPath), \Tollwerk\Squeezr\Exception::INVALID_CSS_FILE);
		}
		
		$this->_absoluteCacheCssDir					= dirname($this->_absoluteCacheCssPath);
	}
	
	/**
	 * Parse and analyze the requested CSS file
	 * 
	 * @throws \Tollwerk\Squeezr\Exception			On any parser error
	 * @todo joschi									Implement media query support for @import rules
	 */
	protected function _parse() {
		$origCss									= '';
		$length										= 0;
		foreach (@file($this->_absoluteCssPath) as $line) {
			$origCss								.= $line;
			$length									+= strlen($line);
			$this->_lines[]							= $length;
		}
		$css										= $origCss;
		$peek										= 0;
		$block										= null;
		
		// Consume all CSS data (blockwise)
		while ($peek < $length) {
			
			// Consume whitespace
			if (preg_match("%^(\s+)%isSu", $css, $whitespace)) {
				$block								.= $whitespace[0];
				$peek								+= strlen($whitespace[0]);
				
			// Consume Block comment
			} else if (!strncmp('/*', $css, 2)) {
				$commentEnd							= strpos($css, '*/', 2);
				
				// If no comment end is found: Error
				if ($commentEnd === false) {
					throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::UNBALANCED_BLOCK_COMMENT_STR, $this->_peekToLine($peek)), \Tollwerk\Squeezr\Exception::UNBALANCED_BLOCK_COMMENT);
				}
				
				$commentLength						= $commentEnd + 2;
				$block								.= substr($css, 0, $commentLength);
				$peek								+= $commentLength;
			
			// Consume @-rule
			} elseif (!strncmp('@', $css, 1)) {

				// Determine the rule identifier
				if (!preg_match("%^\@([\w\-]+)%", $css, $atRule)) {
					throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::INVALID_AT_RULE_IDENTIFIER_STR, $this->_peekToLine($peek)), \Tollwerk\Squeezr\Exception::INVALID_AT_RULE_IDENTIFIER);
				}
				
				// Consume @-rule by identifier				
				switch($atRule[1]) {
					
					// @media rule
					case 'media':
						$declarationBlockStart		= 0;
						$declarationBlock			= $this->_consumeDeclarationBlock($css, $peek, strlen($atRule[1]) + 1, $declarationBlockStart);
						$peek						+= strlen($declarationBlock);
						$breakpoints				= $this->_breakpoints(substr($css, 6, $declarationBlockStart - 6));
						
						// If the @media query is squeezable
						if (is_array($breakpoints)) {
							
							// If there's already block data to be registered
							if (strlen($block)) {
								$this->_registerBlock($block);
							}
							
							$this->_registerBlock($declarationBlock, $breakpoints);
							
						// Else: Not a squeezable @media query, register as regular block
						} else {
							$block					.= $declarationBlock;
						}
						
						break;
						
					// @import rule: May contain media query, should be implemented
					case 'import':
						
						// Find the next line break or semicolon
						if (preg_match("%[\r\n\;]%", $css, $delimiter, PREG_OFFSET_CAPTURE, strlen($atRule[1]))) {
							$declarationBlock		= substr($css, 0, $delimiter[0][1] + 1);
								
						// Else: this must be the end of the data
						} else {
							$declarationBlock		= $css;
						}
						
						$peek						+= strlen($declarationBlock);
						
						// Examine the import rule
						$import						= trim(substr($declarationBlock, 7), ' ;');
						foreach (self::$_importPatterns as $start => $pattern) {
							if (!strncmp($start, $import, strlen($start)) && preg_match($pattern, $import, $importMediaQuery)) {
								$breakpoints		= $this->_breakpoints($importMediaQuery[4]);
								
								// If the @import rule has a squeezable media query part
								if (is_array($breakpoints)) {
										
									// If there's already block data to be registered
									if (strlen($block)) {
										$this->_registerBlock($block);
									}
										
									$this->_registerBlock($declarationBlock, $breakpoints);
									break 2;
								}
							}
						}
						
						$block						.= $declarationBlock;
						break;
						
					// Single line @-rules (except @import)
					case 'charset';
					case 'namespace':
						
						// Find the next line break or semicolon
						if (preg_match("%[\r\n\;]%", $css, $delimiter, PREG_OFFSET_CAPTURE, strlen($atRule[1]))) {
							$block					.=
							$declarationBlock		= substr($css, 0, $delimiter[0][1] + 1);
							$peek					+= strlen($declarationBlock);
							
						// Else: this must be the end of the data
						} else {
							$block					.= $css;
							$peek					+= strlen($css);
						}
					
						break;
					
					// Block @-rules (e.g. *-keyframes)
					case 'page':
					case 'font-face':
					default:
						$block						.=
						$declarationBlock			= $this->_consumeDeclarationBlock($css, $peek, strlen($atRule[1]) + 1);
						$peek						+= strlen($declarationBlock);
						break;
				}
			
			// Consume declaration blocks
			} else {
				$block								.=
				$declarationBlock					= $this->_consumeDeclarationBlock($css, $peek);
				$peek								+= strlen($declarationBlock);
			}
			
			$css									= substr($origCss, $peek);
		}
		
		// Register the last block (if any)
		if (strlen($block)) {
			$this->_registerBlock($block);
		}
	}
	
	/**
	 * Register block data
	 * 
	 * @param string $block							Block data
	 * @param array $breakpoints					Breakpoints
	 */
	protected function _registerBlock(&$block, array $breakpoints = null) {
		$this->_blocks[]							= array($breakpoints, $this->_minify($block));
		$block										= '';
	}
	
	/**
	 * Detect and consume a declaration block within the given CSS text
	 * 
	 * @param string $css							CSS text
	 * @param int $peek								Current peek (for line number detection in case of an error)
	 * @param int $offset							Optional: Offset position
	 * @param int $declarationBlockStart			Set by reference: Declaration block start position within the CSS text
	 * @return string								Declaration block (starting at the beginning of the CSS text)
	 * @throws \Tollwerk\Squeezr\Exception			If there is no declaration block starting or if it's unbalanced
	 */
	protected function _consumeDeclarationBlock($css, $peek, $offset = 0, &$declarationBlockStart = 0) {
		$declarationBlockStart				= strpos($css, '{', $offset);
		if ($declarationBlockStart === false) {
			throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::INVALID_DECLARATION_BLOCK_STR, $this->_peekToLine($peek)), \Tollwerk\Squeezr\Exception::INVALID_DECLARATION_BLOCK);
		}
		
		$declarationBlockBalance			= 1;
		$declarationBlockPosition			= $declarationBlockStart + 1;
		while($declarationBlockBalance > 0) {
			
			// If either a block start or end delimiter is found
			if (preg_match("%[\{\}]%", $css, $declarationBlockDelimiter, PREG_OFFSET_CAPTURE, $declarationBlockPosition)) {
				$declarationBlockBalance	+= ($declarationBlockDelimiter[0][0] == '{') ? 1 : -1;
				$declarationBlockPosition	= $declarationBlockDelimiter[0][1] + 1;
				
			// Else: error
			} else {
				throw new \Tollwerk\Squeezr\Exception(sprintf(\Tollwerk\Squeezr\Exception::UNBALANCED_DECLARATION_BLOCK_STR, $this->_peekToLine($peek)), \Tollwerk\Squeezr\Exception::UNBALANCED_DECLARATION_BLOCK);
			}
		}
		
		return substr($css, 0, $declarationBlockPosition);
	}
	
	/**
	 * Extract the relevant breakpoints out of a @media query
	 * 
	 * @param string $mediaQuery					@media query
	 * @return array|null							Breakpoint alternatives
	 * @todo joschi									Implement support for "resolution" media queries
	 */
	protected function _breakpoints($mediaQuery) {
		$breakpointAlternatives						= array();
		$mediaQuery									= strtolower($mediaQuery);
		
		// Run through all media query alternatives
		foreach (explode(',', $mediaQuery) as $mediaQueryAlternative) {
			$breakpoints							= 0;
			$minConditions							= array(
				self::CONDITION_WIDTH				=> array(),
				self::CONDITION_HEIGHT				=> array(),
			);
			
			// Detect a minimum width condition
			if (preg_match_all("%((?:(?:min|max)\-)?(device\-)?width)\s*\:\s*(\-?\d+(?:\.\d+)?)([acdehimnoprtvwxz]+)%i", $mediaQueryAlternative, $widthConditions, PREG_SET_ORDER)) {
				foreach ($widthConditions as $widthCondition) {
					$minWidth						=& $minConditions[self::CONDITION_WIDTH];
					$minWidth[$widthCondition[4]]	= empty($minWidth[$widthCondition[4]]) ? floatval($widthCondition[3]) : min(floatval($widthCondition[3]), $minWidth[$widthCondition[4]]);
					unset($minWidth);
				}
			}
			
			// Detect a minimum height condition
			if (preg_match_all("%((?:(?:min|max)\-)?(device\-)?height)\s*\:\s*(\-?\d+(?:\.\d+)?)([acdehimnoprtvwxz]+)%i", $mediaQueryAlternative, $heightConditions, PREG_SET_ORDER)) {
				foreach ($heightConditions as $heightCondition) {
					$minHeight						=& $minConditions[self::CONDITION_HEIGHT];
					$minHeight[$heightCondition[4]]	= empty($minHeight[$heightCondition[4]]) ? floatval($heightCondition[3]) : min(floatval($heightCondition[3]), $minHeight[$heightCondition[4]]);
					unset($minHeight);
				}
			}
			
			// Run through the registered conditions and register them (if any)
			foreach ($minConditions as $type => $typeConditions) {
				if (count($typeConditions)) {
					$breakpoints					|= pow(2, $this->_breakpointIndex($type, $typeConditions));
				}
			}
			
			$breakpointAlternatives[]				= $breakpoints;
		}
		
		return count($breakpointAlternatives) ? $breakpointAlternatives : null;
	}
	
	/**
	 * Determine / create and return a breakpoint index entry
	 * 
	 * @param int $type								Breakpoint type
	 * @param array $condition						Condition values
	 * @return int									Breakpoint index
	 */
	protected function _breakpointIndex($type, array $condition) {
		
		// Normalize condition values
		ksort($condition);
		$serializedCondition						= array();
		foreach ($condition as $unit => $value) {
			$serializedCondition[]					= $value.$unit;
		}
		$serializedCondition						= implode('/', $serializedCondition);

		// If a breakpoint at this width has not yet been registered
		if (!array_key_exists($serializedCondition, $this->_breakpointCatalog[$type])) {
			$this->_breakpointCatalog[$type][$serializedCondition]	= count($this->_breakpointIndex);
			$this->_breakpointIndex[$this->_breakpointCatalog[$type][$serializedCondition]] = array(self::$_breakpointTypes[$type], $condition);
		}
		
		return $this->_breakpointCatalog[$type][$serializedCondition];
	}
	
	/**
	 * Find the line number corresponding with a peek position within the CSS file
	 * 
	 * @param int $peek								Peek position
	 * @return int									Line number
	 */
	protected function _peekToLine($peek) {
		foreach ($this->_lines as $line => $length) {
			if ($length >= $peek) {
				break;
			}
		}
		return $line;
	}
	
	/**
	 * Minifiy a CSS block (in case a minification provider has been registered)
	 * 
	 * @param string $css							CSS block
	 * @return string								Minified CSS block
	 */
	protected function _minify($css) {
		return ($this->_minifier instanceof \Tollwerk\Squeezr\Css\Minifier) ? $this->_minifier->minify($css) : $css;
	}
	
	/**
	 * Compile a PHP cache class representing the loaded CSS stylesheet
	 * 
	 * @return string										PHP class code
	 * @throws \Tollwerk\Squeezr\Css\Minifier\Exception		If an invalid minification provider has been requested
	 */
	protected function _compileCacheClassCode() {
		
		// Register a minification provider (if any)
		if (SQUEEZR_CSS_MINIFICATION_PROVIDER) {
			$minificationProvider					= ucfirst(strtolower(SQUEEZR_CSS_MINIFICATION_PROVIDER));
				
			// If the minification provider class file doesn't exist: error
			if (!@is_readable(__DIR__.DIRECTORY_SEPARATOR.'Css'.DIRECTORY_SEPARATOR.'Minifier'.DIRECTORY_SEPARATOR.$minificationProvider.'.php')) {
				require_once __DIR__.DIRECTORY_SEPARATOR.'Css'.DIRECTORY_SEPARATOR.'Minifier'.DIRECTORY_SEPARATOR.'Exception.php';
				throw new \Tollwerk\Squeezr\Css\Minifier\Exception(sprintf(\Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER_MSG, SQUEEZR_CSS_MINIFICATION_PROVIDER), \Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER);
			}
				
			// Require and verify the minification provider
			require_once __DIR__.DIRECTORY_SEPARATOR.'Css'.DIRECTORY_SEPARATOR.'Minifier.php';
			require_once __DIR__.DIRECTORY_SEPARATOR.'Css'.DIRECTORY_SEPARATOR.'Minifier'.DIRECTORY_SEPARATOR.$minificationProvider.'.php';
			$minificationProvider					= '\\Tollwerk\\Squeezr\\Css\\Minifier\\'.$minificationProvider;
			if (!@class_exists($minificationProvider, false) || !is_subclass_of($minificationProvider, '\\Tollwerk\\Squeezr\\Css\\Minifier')) {
				throw new \Tollwerk\Squeezr\Css\Minifier\Exception(sprintf(\Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER_MSG, SQUEEZR_CSS_MINIFICATION_PROVIDER), \Tollwerk\Squeezr\Css\Minifier\Exception::INVALID_MINIFICATION_PROVIDER);
			}
				
			$this->_minifier						= new $minificationProvider();
		}
			
		// Parse the CSS file and extract breakpoint and CSS block info
		$this->_parse();
		
		// Compile a corresponding PHP cache class
		$cacheClassCode								= strtr(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Css'.DIRECTORY_SEPARATOR.'Cache.php'), array(
			'FILEHASH'								=> md5($this->_relativeCssPath),
			"'BREAKPOINTS'"							=> var_export($this->_breakpointIndex, true),
			"'BLOCKS'"								=> var_export($this->_blocks, true),
		));
		
		// Return the PHP cache class code
		return $cacheClassCode;
	}
	
	/************************************************************************************************
	 * STATIC METHODS
	 ***********************************************************************************************/

	/**
	 * Instanciate a CSS proxy
	 *  
	 * @param string $css							CSS file
	 * @return \Tollwerk\Squeezr\Css				Instance reference
	 */
	public static function instance($css) {
		return new self($css); 
	}
}