//(function(w){
	if (navigator.cookieEnabled) {
		var squeezr					= 'squeezr',
		path						= ';path=/',
		doc							= document,
		winWidth					= w.innerWidth,
		screenWidth					= screen.width,
		screenHeight				= screen.height;

		/**
		 * Extract the Squeezr cookie values
		 * 
		 * @return {Object}				Squeezr cookie values
		 */
		function getSqueezrCookies() {
			for (var index = 0, cookieParts = doc.cookie.split(';'), cookiePattern = /^\ssqueezr\.([^=]+)=(.*?)\s*$/, cookies = {}, cookie; index < cookieParts.length; ++index) {
				if (cookie = cookieParts[index].match(cookiePattern)) {
					cookies[cookie[1]]	= cookie[2];
				}
			}
			return cookies;
		}
		
		/**
		 * Measure the window width in em
		 * 
		 * @return {Number}				Window width in em
		 */
		function measureWindowEmWidth(precision, f) {
			precision				= Math.max(parseFloat(precision || 1, 10), .01);
			var docElement			= doc.documentElement,
			createBlock				= function() {
										var block					= doc.createElement('div'),
										blockStyles					= {'width': '1px', 'height': '1px', 'display': 'inline-block'};
										for (var property in blockStyles) {
											block.style[property]	= blockStyles[property];
										}
										return block;
									},
			blockRow				= doc.createElement('div'),
			block					= blockRow.appendChild(createBlock());
			blockRow.appendChild(createBlock());
			docElement.appendChild(blockRow);
			var blockHeight			= blockRow.clientHeight,
			ems						= Math.floor(winWidth / blockHeight),
			delta					= ems / 2,
			iteration				= 0,
			iterations				= [ems];
			
			// Iteratetively resize the measurement block until the breakpoint is found (taking the given precision into account)
			while ((iteration++ < 1000) && ((Math.abs(delta) > precision) || (blockRow.clientHeight > blockHeight))) {
				ems					+= delta;
				block.style.width	= ems + 'em';
				delta				/= ((blockRow.clientHeight > blockHeight) ? 1 : -1) * ((delta > 0) ? -2 : 2);
				iterations.push(ems);
			}
			
			// Remove the measurement elements
			docElement.removeChild(blockRow);
//			console.log(iteration + ' iterations: ' + iterations.join(', '));
			
			return ems;
		}
		
		/**
		 * Extract the image breakpoints
		 * 
		 * @param {String} str		Serialized breakpoints list
		 * @return {Array}			Ordered breakpoints list
		 */
		function getBreakpoints(str, emf) {
			
			// Split the breakpoint list and run through all breakpoints
			for (var index = 0, rawBreakpoints = (str || '').split(','), breakpointPattern = /(\d+(?:\.\d+)?)(px)?/i, breakpoints = [], match; index < rawBreakpoints.length; ++index) {
				
				// If this is a valid breakpoint definition (may have a "px" unit)
				if (match = rawBreakpoints[index].match(breakpointPattern)) {
					breakpoints.push(parseFloat(match[1], 10));
				}
			}
			
			// Sort the breakpoints in ascending order
			return breakpoints.sort(function(a, b){ return a - b; });
		}
		
		/**
		 * Return the device pixel ratio
		 * 
		 * @return {Number}			Device pixel ratio
		 */
		function getDevicePixelRatio() {
			return ('devicePixelRatio' in w) ? w.devicePixelRatio : ((('deviceXDPI' in w) && ('logicalXDPI' in w)) ? (w.deviceXDPI / w.logicalXDPI) : 1);
		}
		
		/**
		 * Round a floating point value to a certain precision
		 * 
		 * @param {Number}			Floating point value
		 * @param {Number}			Precision
		 * @param {Number}			Rounded floating point value
		 */
		function roundToPrecision(em, precision) {
			return precision * Math.round(10 * em / precision) / 10;
		}
		
		// Run through all document JavaScripts and find the Squeezr script (must reference a script file named "squeezr.js" or carry an ID attribute with value "squeezr")
		for (var index = 0, scripts = doc.getElementsByTagName('script'); index < scripts.length; ++index) {
			if (scripts[index].id == squeezr) {
				var pixelRatio			= getDevicePixelRatio(),
				imgBreakpoint			= '-';
				
				// Set the general screen size cookie
				doc.cookie				= squeezr + '.screen=' + screenWidth + 'x' + screenHeight + '@' + pixelRatio + path;
				
				// Set the image engine cookie (if enabled)
				if (!scripts[index].getAttribute('data-disable-images')) {
					var breakpoints		= getBreakpoints(scripts[index].getAttribute('data-breakpoints-images')),
					maximum				= Math.max(screenWidth, screenHeight),
					nextBreakpoint		= null;
					do {
						if (maximum > (nextBreakpoint = breakpoints.pop())) {
							break;
						}
						imgBreakpoint	= (nextBreakpoint * pixelRatio) + 'px';
					} while (breakpoints.length);
				}
				doc.cookie				= squeezr + '.images=' + imgBreakpoint + path;
				
				// Determine the Squeezr cookie values
				var cookies				= getSqueezrCookies(),
				cssBreakpoint			= cookies.css || '-';
				if ((!('css' in cookies) || !cookies.css || (cookies.css == '-')) && !scripts[index].getAttribute('data-disable-css')) {
					var emRatio			= winWidth / measureWindowEmWidth(parseFloat(scripts[index].getAttribute('data-em-precision') || .5, 10) / 100);
					cssBreakpoint		= screenWidth + 'x' + screenHeight + '@' + (Math.round(emRatio * 10) / 10);
				}
				doc.cookie				= squeezr + '.css=' + cssBreakpoint + path;

				break;
			}
		}
	}
//})(this);