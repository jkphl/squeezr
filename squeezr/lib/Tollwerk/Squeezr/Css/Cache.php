<?php

namespace Tollwerk\Squeezr\Css {

	/**
	 * Cached CSS file
	 */
	class Cache_FILEHASH {
		/**
		 * Directory of extracted CSS media query breakpoints / conditions
		 * 
		 * @var array
		 */
		private $_breakpointIndex = 'BREAKPOINTS';
		/**
		 * Tokenized CSS blocks of this stylesheet
		 * 
		 * @var array
		 */
		private $_blocks = 'BLOCKS';
		/**
		 * Currently matching breakpoints
		 * 
		 * @var int
		 */
		private $_breakpoints = null;
		/**
		 * Breakpoint types
		 *
		 * @var array
		 */
		private static $_breakpointTypes = array(
			self::CONDITION_WIDTH			=> 'width',
			self::CONDITION_HEIGHT			=> 'height',
			self::CONDITION_RESOLUTION		=> 'resolution',
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
		 * Serialization of this CSS file for a specific request configuration
		 * 
		 * @return string				Serialized CSS file
		 */
		public function __toString() {
			$breakpoints										= $this->getMatchingBreakpoints();
			$css												= '';
			foreach ($this->_blocks as $block) {
				$includeBlock									= ($block[0] === null);
				if (is_array($block[0])) {
					foreach ($block[0] as $conditionAlternative) {
						if (($breakpoints & $conditionAlternative) == $conditionAlternative) {
							$includeBlock						= true;
							break;
						}
					}
				}
				if ($includeBlock) {
					$css										.= $block[1];
				}
			}
			
			return $css;
		}
		
		/**
		 * Determine the currently matching breakpoints (bitmask)
		 * 
		 * @return int					Matching breakpoints
		 */
		public function getMatchingBreakpoints() {
			if ($this->_breakpoints === null) {
				$capabilities										= array(
					self::CONDITION_WIDTH							=> null,
					self::CONDITION_HEIGHT							=> null,
					self::CONDITION_RESOLUTION						=> 1,
				);
				$pxPerEm											= 16;
					
				// Capabilities detection
				if (!empty($_COOKIE['squeezr_css']) && preg_match("%^(\d+)x(\d+)\@(\d+(?:\.\d+)?)%", $_COOKIE['squeezr_css'], $cssCapabilities)) {
					$capabilities[self::CONDITION_WIDTH]			= intval($cssCapabilities[1]);
					$capabilities[self::CONDITION_HEIGHT]			= intval($cssCapabilities[2]);
					$pxPerEm										= floatval($cssCapabilities[3]);
				}
					
				$this->_breakpoints									= 0;
				$breakpointTypes									= array_flip(self::$_breakpointTypes);
				foreach($this->_breakpointIndex as $index => $breakpoint) {
					$breakpointType									= $breakpointTypes[$breakpoint[0]];
					if (($capabilities[$breakpointType] !== null) && count($breakpoint[1])) {
							
						// Run through all breakpoint conditions
						foreach ($breakpoint[1] as $unit => $value) {
							$value									*= ($unit == 'em') ? $pxPerEm : 1;
							if ($capabilities[$breakpointType] < $value) {
								continue 2;
							}
						}
							
						$this->_breakpoints							|= pow(2, $index);
					}
				}
			}
			
			return $this->_breakpoints;
		}
		
		/************************************************************************************************
		 * STATIC METHODS
		 ***********************************************************************************************/
		
		/**
		 * Create an instance of this cached CSS file
		 * 
		 * @return \Tollwerk\Squeezr\Css\Cache		Cache file instance
		 */
		public static function instance() {
			return new self();
		}
	}
	
	return new Cache_FILEHASH();
}