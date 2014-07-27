/*
Builder Style Manager main editor JS code
Written by Chris Jean
Licensed under GPL v2

Version 1.0.0

Change Notes:
	1.0.0 - 2011-05-16 - Chris Jean
		Near complete rewrite
		Added Custom CSS code and handlers
		Updated code to work with new stylesheet-lib code
*/


var builder_style_manager_stylesheet = {};
var builder_style_manager_custom_css_stylesheet = {};

var builder_group_selectors = new Array();
var builder_style_manager_loaded = false;
var builder_preview_updates = new Array();
var builder_intialized_color_pickers = new Array();
var builder_style_manager_custom_css_update_timeout = false;


if( ! window.console ) {
	window.console = new function() {
		this.log = function(str) {};
		this.dir = function(str) {};
	};
}


jQuery(document).ready(
	function() {
		if ( null !== document.getElementById( 'builder_style_manager-style-preview' ) )
			builder_init_style_editor();
	}
);


function builder_refresh_style_preview() {
	jQuery(".styling-option").each(
		function() {
			if ( '' != jQuery(this).val() ) {
				builder_update_style_preview( this, true );
			}
		}
	);
	
	builder_update_css_preview();
}

function builder_update_style_preview(node, batch_update) {
	batch_update = ( 'undefined' !== typeof( batch_update ) ) ? batch_update : false;
	
	parsed_node = builder_parse_node( node );
	
	if ( false === parsed_node )
		return;
	
	if ( ( '' == parsed_node['value'] ) ) {
		if ( true === builder_style_manager_loaded )
			builder_style_manager_stylesheet.delete_rule_property( parsed_node['selector'], parsed_node['property'] );
	}
	else {
		var data = {};
		data[parsed_node['property']] = parsed_node['value'];
		
		builder_style_manager_stylesheet.update_rule( parsed_node['selector'], data );
	}
	
	builder_update_syncs( parsed_node['group'], parsed_node['property'] );
	
	if( false === batch_update )
		builder_update_css_preview();
}

function builder_update_css_preview() {
	jQuery('#builder_css_preview').val( builder_style_manager_stylesheet.get_stylesheet_text() );
}

function builder_parse_node(node) {
	if ( 'undefined' === typeof( jQuery(node).attr( 'name' ) ) )
		return false;
	
	
	var matches = jQuery(node).attr( 'name' ).match( /^([^-]+)-(.+)/ );
	
	var node_data = [];
	
	node_data['name'] = jQuery(node).attr( 'name' );
	node_data['group'] = matches[1];
	node_data['property'] = matches[2];
	node_data['value'] = jQuery(node).val();
	
	if ( 'undefined' !== typeof( builder_group_selectors[node_data['group']] ) ) {
		node_data['selector'] = builder_group_selectors[node_data['group']];
	}
	else {
		node_data['selector'] = jQuery('#' + node_data['group'] + '-selector').val();
		builder_group_selectors[node_data['group']] = node_data['selector'];
	}
	
	return node_data;
}

function builder_update_syncs( group, property ) {
	var syncs_data = jQuery('#' + group + '-' + property + '-sync').val();
	
	if( 'undefined' === typeof( syncs_data ) )
		return false;
	
	
	var syncs = syncs_data.split( ',' );
	var val = jQuery('#' + group + '-' + property).val();
	
	for ( count = 0; count < syncs.length; count++ ) {
		var id = syncs[count];
		var cur_val = val;
		
		var matches = id.match( /^(.+)---(.+)/ );
		
		if ( ( null != matches ) && ( 'undefined' !== typeof( matches ) ) ) {
			id = matches[1];
			
			if ( '' !== cur_val ) {
				var val_matches = val.match( /(\d+)(.+)/ );
				
				cur_val = val_matches[1] * matches[2];
				cur_val += val_matches[2];
			}
		}
		
		jQuery('#' + id).val( cur_val );
		builder_update_style_preview( jQuery('#' + id).get( 0 ), true );
	}
	
	builder_update_css_preview();
}

function builder_update_background_option( node ) {
	var matches = jQuery(node).attr( 'name' ).match( /^(.+)-option$/ );
	
	var main_name = matches[1];
	var value = jQuery(node).val();
	
	if ( 'image' === value ) {
		if ( true === builder_style_manager_loaded )
			jQuery("#" + main_name).val( jQuery("#" + main_name + "-backup").val() );
		
		jQuery("." + main_name + "-upload-link").show();
	}
	else {
		if ( ( '' != jQuery("#" + main_name).val() ) && ( 'none' != jQuery("#" + main_name).val() ) )
			jQuery("#" + main_name + "-backup").val( jQuery("#" + main_name).val() );
		jQuery("#" + main_name).val( value );
		
		jQuery("." + main_name + "-upload-link").hide();
	}
	
	builder_update_style_preview( jQuery("#" + main_name).get( 0 ) );
}

function builder_enable_color_picker( node ) {
	if ( true === builder_intialized_color_pickers[jQuery(node).attr("name")] )
		return;
	
	jQuery(node).unbind( 'focus' );
	
	jQuery(node).ColorPicker(
		{
			onChange: function( color, el ) {
				jQuery(el).val( color );
				builder_update_style_preview( el );
			},
			onBeforeShow: function () {
				color = ( '' !== this.value ) ? this.value : '#9EDCF0';
				jQuery(this).ColorPickerSetColor( color );
			}
		}
	).bind('keyup',
		function() {
			jQuery(this).ColorPickerSetColor( this.value );
		}
	);
	
	builder_intialized_color_pickers[jQuery(node).attr( 'name' )] = true;
}

function builder_load_preview() {
	var url_input = jQuery( '#builder_preview_url' );
	var url = url_input.val();
	var iframe = '<iframe src="' + url + '" name="style-preview" id="style-preview" frameborder="0"></iframe>';
	
	jQuery(url_input).replaceWith( iframe );
	
	builder_iframe_resize();
	jQuery('#style-preview').load( builder_init_preview_handlers );
}

var builder_init_preview_handlers = function() {
	builder_style_manager_stylesheet = new JavaScriptStylesheets( {'document': document.getElementById('style-preview').contentWindow.document} );
	
	if ( null !== document.getElementById( 'builder_style_manager-style-custom-css' ) )
		builder_init_custom_css();
	
	builder_refresh_style_preview();
	
	if ( true === builder_style_manager_stylesheet.is_ie_lt_9 ) {
		var message = jQuery( '#alert-message-container' ).html();
		jQuery( '#alert-message-container' ).html( message + "\n<div class='error'><p>Note: This version of Internet Explorer is not compatible with all the features of Style Manager. For best results, use a newer version of Internet Explorer or another browser.</p></div>" );
	}
	
	jQuery('.styling-option').keyup(
		function() {
			builder_update_style_preview( this );
		}
	);
	jQuery('.styling-option').change(
		function() {
			builder_update_style_preview( this );
		}
	);
	
	jQuery('.background-image-option').change(
		function() {
			builder_update_background_option( this );
		}
	).each(
		function() {
			builder_update_background_option( this );
		}
	);
	
	builder_style_manager_loaded = true;
}

function builder_init_style_editor() {
	builder_load_preview();
	
	jQuery("[name$='-color']").focus(
		function() {
			builder_enable_color_picker( this );
		}
	);
	
	jQuery('.show-hide-wrapper a').click(
		function() {
			var target = jQuery(this).parent().siblings( '.show-hide-target' );
			
			if ( 'none' === jQuery(target).css( 'display' ) ) {
				jQuery(target).show();
				jQuery(this).html( '(Hide Options)' );
			}
			else {
				jQuery(target).hide();
				jQuery(this).html( '(Show Options)' );
			}
			
			return false;
		}
	);
	
	jQuery('#posts-filter').submit(
		function() {
			jQuery('#posts-filter :input').each(
				function() {
					if ( '' == jQuery(this).val() ) {
						jQuery(this).remove();
					}
				}
			);
		}
	);
	
	jQuery("input[type='submit']:disabled").removeAttr( 'disabled' );
}

function builder_init_custom_css() {
	builder_style_manager_custom_css_stylesheet = new JavaScriptStylesheets( {'document': document.getElementById('style-preview').contentWindow.document, 'content': jQuery( '#builder_custom_css' ).val()} );
	
	jQuery( '#builder_custom_css' ).keydown( builder_custom_css_keydown );
	
	jQuery( '#builder_custom_css' ).blur(
		function( e ) {
			if ( this.lastKey && 9 == this.lastKey )
				this.focus();
		}
	);
}

var builder_custom_css_keydown = function( e ) {
	if ( 9 == e.keyCode )
		builder_custom_css_handle_tab( e );
	
	if ( false !== builder_style_manager_custom_css_update_timeout )
		clearTimeout( builder_style_manager_custom_css_update_timeout );
	
	builder_style_manager_custom_css_update_timeout = setTimeout( builder_custom_css_update, 500 );
}

var builder_custom_css_update = function() {
	builder_style_manager_custom_css_update_timeout = false;
	builder_style_manager_custom_css_stylesheet.set_rules( jQuery( '#builder_custom_css' ).val() );
}

function builder_custom_css_handle_tab( e ) {
	// Tab handler code originally from WordPress 3.2 trunk
	var el = e.target, selStart = el.selectionStart, selEnd = el.selectionEnd, val = el.value, scroll, sel;
	var shift = ( 'undefined' !== typeof( e.shiftKey ) ) ? e.shiftKey : false;
	
	try {
		this.lastKey = 9; // not a standard DOM property, lastKey is to help stop Opera tab event.  See blur handler below.
	} catch(err) {}
	
	if ( document.selection ) {
		el.focus();
		sel = document.selection.createRange();
		sel.text = '\t';
	} else if ( selStart >= 0 ) {
		scroll = this.scrollTop;
		el.value = val.substring(0, selStart).concat('\t', val.substring(selEnd) );
		el.selectionStart = el.selectionEnd = selStart + 1;
		this.scrollTop = scroll;
	}
	
	if ( e.stopPropagation )
		e.stopPropagation();
	if ( e.preventDefault )
		e.preventDefault();
}

function builder_get_array_values( name ) {
	var values = jQuery("input[name='" + name + "[]']").map(
		function() {
			return jQuery(this).val()
		}
	).get();
	
	return values;
}
