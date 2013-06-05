<!DOCTYPE html>
<!--

squeezr example page for 8-bit PNG image

@package		squeezr
@author			Joschi Kuphal <joschi@kuphal.net>
@copyright		Copyright © 2013 Joschi Kuphal http://joschi.kuphal.net
@link			http://squeezr.it
@github			https://github.com/jkphl/squeezr
@twitter		@squeezr
@license		http://creativecommons.org/licenses/by/3.0/ Creative Commons Attribution 3.0 Unported License
@since			1.0b2
@version		1.0b

--><?php

$images			= array(
	1			=> array('TrueColor',								'truecolor.jpg'), 				// JPEG
	2			=> array('256 colors',								'palette.gif'),					// GIF
	3			=> array('256 colors + index transparency',			'palette-index.gif'),			// GIF
	4			=> array('256 colors (8-bit)',						'palette-8bit.png'),			// PNG
	5			=> array('256 colors + index transparency (8-bit)',	'palette-index-8bit.png'),		// PNG
	6			=> array('256 colors + alpha transparency (8-bit)',	'palette-alpha-8bit.png'),		// PNG
	7			=> array('Greyscale (8-bit)',						'greyscale-8bit.png'),			// PNG
	8			=> array('Greyscale + index transparency (8-bit)',	'greyscale-index-8bit.png'),	// PNG
	9			=> array('Greyscale + alpha transparency (16-bit)',	'greyscale-alpha-16bit.png'),	// PNG
	10			=> array('TrueColor (24-bit)',						'truecolor-24bit.png'),			// PNG
	11			=> array('TrueColor + alpha transparency (32-bit)', 'truecolor-alpha-32bit.png'),	// PNG
);
$headings		= array(
	1			=> 'JPEG',
	2			=> 'GIF',
	4			=> 'PNG',
);

?><html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1" />
		<title>squeezr | 8-bit PNG images with alpha transparency</title>
		<script type="text/javascript" id="squeezr" data-breakpoints-images="760,960,1024,1440,2000">(function(a){function h(){for(var f,a=0,b=d.cookie.split(";"),c=/^\ssqueezr\.([^=]+)=(.*?)\s*$/,e={};b.length>a;++a)(f=b[a].match(c))&&(e[f[1]]=f[2]);return e}function i(a){a=Math.max(parseFloat(a||1,10),.01);var c=d.documentElement,f=function(){var a=d.createElement("div"),b={width:"1px",height:"1px",display:"inline-block"};for(var c in b)a.style[c]=b[c];return a},g=d.createElement("div"),h=g.appendChild(f());g.appendChild(f()),c.appendChild(g);for(var i=g.clientHeight,j=Math.floor(e/i),k=j/2,l=0,m=[j];1e3>l++&&(Math.abs(k)>a||g.clientHeight>i);)j+=k,h.style.width=j+"em",k/=(g.clientHeight>i?1:-1)*(k>0?-2:2),m.push(j);return c.removeChild(g),j}function j(a){for(var g,c=0,d=(a||"").split(","),e=/(\d+(?:\.\d+)?)(px)?/i,f=[];d.length>c;++c)(g=d[c].match(e))&&f.push(parseFloat(g[1],10));return f.sort(function(a,b){return a-b})}function k(){return"devicePixelRatio"in a?a.devicePixelRatio:"deviceXDPI"in a&&"logicalXDPI"in a?a.deviceXDPI/a.logicalXDPI:1}if(navigator.cookieEnabled)for(var b="squeezr",c=";path=/",d=document,e=a.innerWidth,f=screen.width,g=screen.height,m=0,n=d.getElementsByTagName("script");n.length>m;++m)if(n[m].id==b){var o=k(),p="-";if(d.cookie=b+".screen="+f+"x"+g+"@"+o+c,!n[m].getAttribute("data-disable-images")){var q=j(n[m].getAttribute("data-breakpoints-images")),r=Math.max(f,g),s=null;do{if(r>(s=q.pop()))break;p=s*o+"px"}while(q.length)}d.cookie=b+".images="+p+c;var t=h(),u=t.css||"-";if(!("css"in t&&t.css&&"-"!=t.css||n[m].getAttribute("data-disable-css"))){var v=e/i(parseFloat(n[m].getAttribute("data-em-precision")||.5,10)/100);u=f+"x"+g+"@"+Math.round(10*v)/10}d.cookie=b+".css="+u+c;break}})(this);</script>
		<link rel="stylesheet" type="text/css" href="css/example.css" media="all" />
	</head>
	<body id="body">
		<section>
			<h1>squeezr | image types &amp; formats</h1>
			<p>Use this page to see squeezr's work on different image types &amp; formats. The original images (left side) are all 2300 x 2300 pixels, the downscaled ones (right side) are <script type="text/javascript">var breakpoint = parseInt(document.cookie.split('squeezr.images=')[1].split('px;').shift()); document.write(breakpoint + ' x ' + breakpoint);</script> pixels (resulting from your screen dimensions). Please click on the images to see them in their original size (will open new windows). Click on the link at the bottom to change the page's background color and see the transparency effects.</p>
			<form method="get">Display: <select name="type" onchange="this.form.submit();"><option value="0">--- All images types &amp; formats ---</option><?php
				$selected			= empty($_GET['type']) ? 0 : intval($_GET['type']);
				$optgroup			= '';
				foreach($images as $index => $image):
					if (array_key_exists($index, $headings)):
						if ($optgroup): ?></optgroup><?php endif;
						$optgroup	= $headings[$index];
						?><optgroup label="<?php echo htmlspecialchars($headings[$index]); ?>"><?php
					endif;
					?><option value="<?php echo $index; ?>"<?php if($selected == $index) echo ' selected="selected"'; ?>><?php echo htmlspecialchars($optgroup).' - '.htmlspecialchars($image[0]); ?></option><?php
				endforeach;
				if ($optgroup): ?></optgroup><?php endif;
			?></select></form>
			<?php
				if ($selected) {
					$images			= array($selected => $images[$selected]);
				}
				foreach($images as $index => $image):
					if (array_key_exists($index, $headings)):
						?><h2><?php echo htmlspecialchars($headings[$index]); ?></h2><?php
					endif;
				?><h4><?php echo $image[0]; ?></h4>
				<div class="images">
					<a href="img/colors/<?php echo $image[1]; ?>?squeezr=0" target="_blank"><img src="img/colors/<?php echo $image[1]; ?>?squeezr=0"/></a>
					<a href="img/colors/<?php echo $image[1]; ?>" target="_blank"><img src="img/colors/<?php echo $image[1]; ?>"/></a>
				</div><?php
				endforeach;
			?><p><a href="8bit-transparent.html">Reload this page</a> | <a href="#" onclick="document.getElementById('body').style.backgroundColor = 'rgb(' + Math.round(Math.random() * 255) + ',' + Math.round(Math.random() * 255) + ',' + Math.round(Math.random() * 255) + ')';return false;">Change background color</a></p>
		</section>
	</body>
</html>