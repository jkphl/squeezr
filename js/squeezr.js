//(function(){

	/**
	 * Extract the Squeezr cookie values
	 * 
	 * @return {Object}			Squeezr cookie values
	 */
	function getSqueezrCookies() {
		for (var index = 0, cookieParts = document.cookie.split(';'), cookiePattern = /^\ssqueezr\.([^=]+)=(.*?)\s*$/, cookies = {}, cookie; index < cookieParts.length; ++index) {
			if (cookie = cookieParts[index].match(cookiePattern)) {
				cookies[cookie[1]] = cookie[2];
			}
		}
		return cookies;
	}
	
	// Determine the Squeezr cookie values
	var cookies					= getSqueezrCookies();
	
	
	function emw(p, f) {
		p						= Math.max(parseFloat(p || 1, 10), .1);
		var de					= document.documentElement,
		cb						= function(c) {
									var b				= document.createElement('div'),
									s					= {'width': '1px', 'height': '1em', 'backgroundColor': c, 'display': 'inline-block'};
									for (var p in s) {
										b.style[p]		= s[p];
									}
									return b;
								},
		r						= document.createElement('div'),
		m						= r.appendChild(cb('red')),
		b						= r.appendChild(cb('blue'));
		de.appendChild(r);
		var h					= r.clientHeight,
		w						= window.innerWidth, // de.clientWidth
		ems						= Math.floor(w / h),
		d						= ems / 2,
		i						= 0,
		res						= [ems];
		
		while ((i++ < 1000) && ((Math.abs(d) > p) || (r.clientHeight > h))) {
			ems					+= d;
			m.style.width		= ems + 'em';
			d					/= ((r.clientHeight > h) ? 1 : -1) * ((d > 0) ? -2 : 2);
			res.push(ems);
		}
		
		de.removeChild(r);
//		console.log(i + ' iterations: ' + res.join(', '));
		return ems;
	}
	function bps(str, emf) {
		for (var b = 0, bs = (str || '').split(','), bl = bs.length, d = /(\d+(?:\.\d+)?)(em|px)?/i, br = [], m, em, emg = false, v; b < bl; ++b) {
			if (m = bs[b].match(d)) {
				em				= ((m[2] || '').toLowerCase() == 'em');
				emg				= emg || em;
				if (em && !emf) {
					continue;
				}
				v				= parseFloat(m[1], 10);
				br.push([Math.round(v * ((em ? emf : 0) || 1)), v, em ? 'em' : 'px']);
			}
		}
		br.sort(function(a, b){ return a[0] - b[0]; });
		return br;
	}
	function sc(cn, s, empx, dar) {
		var br					= bps(s, empx),
		d						= Math.max(screen.width, screen.height),
		b						= '-',
		n						= null;
		do {
			if (d > (n = br.pop())[0]) break;
			b					= (n[1] * (dar || 1)) + n[2];
		} while (br.length);
		document.cookie		= 'squeezr.' + cn + '=' + b + ';path=/';
	}
	
	/**
	 * Return the device pixel ratio
	 * 
	 * @return {Number}			Device pixel ratio
	 */
	function getDevicePixelRatio() {
		return ('devicePixelRatio' in window) ? window.devicePixelRatio : ((('deviceXDPI' in window) && ('deviceXDPI' in logicalXDPI)) ? (window.deviceXDPI / window.logicalXDPI) : 1);
	}
	
	// Run through all document JavaScripts and find the Squeezr script (must reference a script file named "squeezr.js" or carry an ID attribute with value "squeezr")
	for (var index = 0, scripts = document.getElementsByTagName('script'), scriptSrcPattern = /squeezr.js$/; index < scripts.length; ++index) {
		if ((scripts[index].id == 'squeezr') || scriptSrcPattern.test(scripts[index].src)) {
			var pixelRatio		= getDevicePixelRatio();
			
			// Set the general screen size cookie
			document.cookie		= 'squeezr.screen=' + screen.width + 'x' + screen.height + '@' + pixelRatio + ';path=/';
			
			// Set the image engine cookie
			sc('images', scripts[index].getAttribute('data-breakpoints-images'), 0, pixelRatio);
			
			// Set the CSS engine cookie
//			sc('css', scripts[index].getAttribute('data-horizontal-breakpoints-css'), window.innerWidth / emw(.05));
			break;
		}
	}
//})();