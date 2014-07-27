/*
	Based off of code by Nathan Smith of SonSpring.com.
	http://sonspring.com/journal/jquery-iframe-sizing
	
	Thanks Nathan.
	
	Version 1.1.0
*/

var builder_iframe_resize = function() {
	var max_height = 500;
	
	var style_preview_original_width = 0;
	var style_preview = jQuery('#style-preview');
	var style_preview_postbox = jQuery('#builder_style_manager-style-preview');
	
	// Set specific variable to represent all iframe tags.
	var iFrames = document.getElementsByTagName( 'iframe' );
	
	// Resize height
	var iResize = function() {
		// Iterate through all iframes in the page.
		for (var i = 0, j = iFrames.length; i < j; i++) {
			var new_height = iFrames[i].contentWindow.document.body.offsetHeight;
			
			if ( new_height > 0 ) {
				if ( new_height > max_height )
					new_height = max_height;
				
				// Set inline style to equal the body height of the iframed content.
				iFrames[i].style.height = new_height + 'px';
				
				var e = iFrames[i];
				setTimeout( function() { resize_width(e); }, 100 );
			}
		}
	}
	
	// Resize preview width
	var resize_width = function() {
		var iframe_container_width = style_preview.parent().width();
		style_preview.css( 'width', iframe_container_width + 'px' );
		
		if ( (0 == style_preview_original_width ) || ( 'undefined' == typeof style_preview_original_width ) ) {
			var width = style_preview.contents().find( '.builder-module' ).first().width();
			
			if ( width > 0 )
				style_preview_original_width = width;
		}
		
		if ( (iframe_container_width - style_preview_original_width ) < 100 )
			style_preview.contents().find( 'body' ).css( 'width', ( style_preview_original_width + 100 ) + 'px' );
		else
			style_preview.contents().find( 'body' ).css( 'width', '100%' );
	}
	
	// Check if browser is Safari or Opera.
	if ( jQuery.browser.safari || jQuery.browser.opera ) {
		// Start timer when loaded.
		jQuery('iframe').load(
			function() {
				setTimeout( iResize, 0 );
			}
		);
	}
	else {
		// For other good browsers.
		style_preview.load(
			function() {
				var new_height = this.contentWindow.document.body.offsetHeight;
				
				if ( new_height > 0 ) {
					if ( new_height > max_height )
						new_height = max_height;
					
					// Set inline style to equal the body height of the iframed content.
					this.style.height = new_height + 'px';
					
					setTimeout(function() { resize_width(); }, 100);
				}
			}
		);
	}
	
	jQuery(window).resize( resize_width );
	jQuery('h3.hndle, h3.hndle span, .handlediv', style_preview_postbox).click( resize_width );
	jQuery('#adminmenu .separator, #collapse-menu').click( resize_width );
};


jQuery(document).ready(
	function() {
		builder_iframe_resize();
	}
);
