/*
JS Stylesheet Library
Created by Chris Jean
Licensed under GPL v2

Version 1.0.1

Change Notes:
	1.0.0 - 2011-05-16 - Chris Jean
		Complete rewrite to have better browser support and failover modes
	1.0.1 - 2011-05-17 - Chris Jean
		Fixed a bug in the declaration parsing that broke url designations
*/


/*
function inspect( obj, maxLevels, level ) {
	var str = '', type, msg, property;
	
	// Start Input Validations
	// Don't touch, we start iterating at level zero
	if ( 'undefined' === typeof( level ) )
		level = 0;
	
	// At least you want to show the first level
	if ( 'undefined' === typeof( maxLevels ) )
		maxLevels = 1;
	if ( maxLevels < 1 )
		return '<font color="red">Error: Levels number must be > 0</font>';
	
	// We start with a non null object
	if ( null === obj )
		return '<font color="red">Error: Object <b>NULL</b></font>';
	// End Input Validations
	
	if ( 'string' === typeof( obj ) ) {
		str += obj;
	}
	else {
		// Each Iteration must be indented
		str += '<ul>';
		
		// Start iterations for all objects in obj
		for ( property in obj ) {
			try {
				// Show "property" and "type property"
				type = typeof( obj[property] );
				
				var value = '';
				
				if ( null === obj[property] )
					value = ': <b>[NULL]</b>';
				else if ( ( 'object' !== typeof( obj[property] ) ) && ( 'function' !== typeof( obj[property] ) ) )
					value = ': <b>' + obj[property] + '</b>';
				
				str += '<li>(' + type + ') ' + property + value + '</li>';
				
				// We keep iterating if this property is an Object, non null
				// and we are inside the required number of levels
				if ( ( 'object' === typeof( obj[property] ) ) && ( null !== obj[property] ) && ( ( level + 1 ) < maxLevels ) )
					str += inspect( obj[property], maxLevels, level + 1 );
			}
			catch( error ) {
				// Is there some properties in obj we can't access? Print it red.
				if ( 'string' === typeof( error ) )
					msg = error;
				else if ( error.message )
					msg = error.message;
				else if ( error.description )
					msg = error.description;
				else
					msg = 'Unknown';
				
				str += '<li><font color="red">(Error) ' + property + ': ' + msg + '</font></li>';
			}
		}
		
		// Close indent
		str += '</ul>';
	}
	
	return str;
}

var print_r = function( obj, print_output, padding ) {
	if ( 'undefined' === typeof( print_output ) )
		print_output = true;
	if ( 'undefined' === typeof( padding ) )
		padding = '';
	
//	console.log( obj );
	var output = inspect( obj, 4 );
	
	if ( true === print_output )
		jQuery('body').append( '<pre>' + output + '</pre>' );
	else
		return output;
}

if ( 'undefined' === typeof( console ) )
	var console = {};
if ( ! console.log )
	console.log = print_r;*/


function JavaScriptStylesheets( args, action ) {
	if ( ! document.styleSheets )
		return false;
	
	
	
	this.property_dom_names      = {};
	this.property_standard_names = {};
	this.converted_rgb_values    = {};
	this.short_property_cache    = {};
	
	this.args   = ( 'undefined' !== typeof( args ) ) ? args : {};
	this.action = ( 'undefined' !== typeof( action ) ) ? action : 'load';
	
	
	this.is_ie      = false;
	this.is_ie_lt_9 = false;
	
	if ( document.createElement('span').applyElement ) {
		this.is_ie = true;
		
		if ( 'undefined' === typeof( document.getElementsByClassName ) )
			this.is_ie_lt_9 = true;
	}
	
	
	
	this.init = function() {
		if ( 'find' === this.action )
			return this._find_stylesheet();
		else
			return this._load_stylesheet();
	}
	
	this._find_stylesheet = function() {
		args = this.args;
		
		for ( var i = 0; i < document.styleSheets.length; i++ ) {
			if ( false === this._hrefs_match( args.href, document.styleSheets[i].href ) )
				continue;
			if ( ( 'undefined' !== typeof( args.title ) ) && ( args.title !== document.styleSheets[i].title ) )
				continue;
			if ( ( 'undefined' !== typeof( args.rel ) ) && ( args.rel !== document.styleSheets[i].rel ) )
				continue;
			if ( ( 'undefined' !== typeof( args.media ) ) && ( args.media !== document.styleSheets[i].media ) )
				continue;
			if ( ( 'undefined' !== typeof( args.type ) ) && ( args.type !== document.styleSheets[i].type ) )
				continue;
			if ( ( 'undefined' !== typeof( args.disabled ) ) && ( args.disabled !== document.styleSheets[i].disabled ) )
				continue;
			
			this.stylesheet = document.styleSheets[i];
			this._find_rules();
			
			break;
		}
		
		if ( 'undefined' === typeof( this.stylesheet ) )
			return false;
		
		return true;
	}
	
	this._load_stylesheet = function() {
		args = this.args;
		
		this.document = document;
		
		if ( 'undefined' !== typeof( args.document ) ) {
			this.document = args.document;
			delete args.document;
		}
		
		var new_style_node;
		
		if ( 'undefined' !== typeof( args.href ) ) {
			new_style_node = this.document.createElement( 'link' );
			new_style_node.href = args.href;
			
			this.type = 'link';
		}
		else {
			new_style_node = this.document.createElement( 'style' );
			
			this.type = 'style';
		}
		
		new_style_node.type = 'text/css';
		
		if ( 'undefined' !== typeof( args.title ) )
			new_style_node.title = args.title;
		if ( 'undefined' !== typeof( args.rel ) )
			new_style_node.rel = args.rel;
		if ( 'undefined' !== typeof( args.media ) )
			new_style_node.media = args.media;
		
		if ( ( 'undefined' !== typeof( args.href ) ) && ( 'undefined' === typeof( args.rel ) ) )
			new_style_node.rel = 'stylesheet';
		
		
		var content = '';
		
		if ( 'undefined' !== typeof( args.content ) ) {
			content = args.content;
			delete args.content;
		}
		
		this.stylesheet_node = this.document.getElementsByTagName( 'head' )[0].appendChild( new_style_node );
		
		this.stylesheet = this.document.styleSheets[this.document.styleSheets.length - 1];
		this._find_rules();
		
		if ( '' !== content )
			this.set_rules( content );
		
		return true;
	}
	
	this._hrefs_match = function( href1, href2 ) {
		if ( ( 'undefined' === typeof( href1 ) ) || ( 'undefined' === typeof( href2 ) ) )
			return false;
		
		var url_parts = document.location.href.match( /^(.+:\/\/[^\/]+)([^\?]*)/ );
		var url_root = url_parts[1];
		var url_path = url_parts[2];
		
		if ( '/' !== url_path.substr( url_path.length - 1 ) )
			url_path += '/';
		
		if ( 0 === href1.indexOf( '/' ) )
			href1 = url_root + href1;
		else if (-1 === href1.indexOf( '://' ) )
			href1 = url_root + url_path + href1;
		
		if ( 0 === href2.indexOf( '/' ) )
			href2 = url_root + href1;
		else if ( -1 === href2.indexOf( '://' ) )
			href2 = url_root + url_path + href2;
		
		if ( href1 === href2 )
			return true;
		return false;
	}
	
	this._find_rules = function() {
		if ( 'undefined' === typeof( this.stylesheet ) )
			return false;
		
		if ( 'undefined' !== typeof( this.stylesheet.cssRules ) )
			this.rules = this.stylesheet.cssRules;
		else if ( 'undefined' !== typeof( this.stylesheet.rules ) )
			this.rules = this.stylesheet.rules;
		
		if ( 'undefined' === typeof( this.rules ) )
			return false;
		return true;
	}
	
	this._get_style_from_declarations = function( declarations ) {
		var style = '';
		
		for ( property in declarations )
			style += property + ':' + declarations[property] + '; ';
		
		return style;
	}
	
	this._get_rules_obj_from_string = function( rules_string ) {
		var rules = {};
		
		var rule_matches = rules_string.match( /\s*[^{;]+\s*{\s*[^{}]+\s*}?/g );
		
		if ( ( 'undefined' === typeof( rule_matches ) ) || ( null === rule_matches ) || ( -1 === rule_matches ) )
			return rules;
		
		for ( var i = 0; i < rule_matches.length; i++ ) {
			var rule_parts = rule_matches[i].match( /\s*([^{;]+)\s*{\s*([^{}]+)\s*}?/ );
			
			if ( 'undefined' === typeof( rules[rule_parts[1]] ) )
				rules[rule_parts[1]] = rule_parts[2];
			else
				rules[rule_parts[1]] += rule_parts[2];
		}
		
		return rules;
	}
	
	this._get_property_dom_name = function( css_property ) {
		if ( 'undefined' !== typeof( this.property_dom_names[css_property] ) )
			return this.property_dom_names[css_property];
		
		var property_parts = css_property.split( '-' );
		
		var property = property_parts.shift();
		
		while ( property_parts.length > 0 ) {
			var part = property_parts.shift();
			part = part.charAt( 0 ).toUpperCase() + part.substr( 1 );
			
			property += part;
		}
		
		this.property_dom_names[css_property] = property;
		
		return property;
	}
	
	this._get_property_standard_name = function( css_property ) {
		if ( 'undefined' !== typeof( this.property_standard_names[css_property] ) )
			return this.property_standard_names[css_property];
		
		var property = css_property;
		
		if ( 'padding-right-value' === css_property )
			property = 'padding-right';
		else if ( 'padding-left-value' === css_property )
			property = 'padding-left';
		else if ( 'margin-right-value' === css_property )
			property = 'margin-right';
		else if ( 'margin-left-value' === css_property )
			property = 'margin-left';
		
		this.property_standard_names[css_property] = property;
		
		return property;
	}
	
	this._delete_rule_at_index = function( index ) {
		if ( this.stylesheet.deleteRule )
			this.stylesheet.deleteRule( index );
		else
			this.stylesheet.removeRule( index );
	}
	
	this._get_stylesheet_rules = function( stylesheet ) {
		if ( stylesheet.cssRules )
			return stylesheet.cssRules;
		return stylesheet.rules;
	}
	
	this._get_stylesheet_rules_object = function( stylesheet ) {
		var raw_rules = this._get_stylesheet_rules( stylesheet );
		
		var declarations = {};
		var selectors = [];
		
		for ( var i = 0; i < raw_rules.length; i++ ) {
			declarations[raw_rules[i].selectorText] = this._get_rule_declarations_object( raw_rules[i] );
			selectors.push( raw_rules[i].selectorText );
		}
		
		selectors.sort();
		
		var rules = {};
		
		for ( var i = 0; i < selectors.length; i++ )
			rules[selectors[i]] = declarations[selectors[i]];
		
		return rules;
	}
	
	this._get_rule_declarations_object = function( rule_or_node ) {
		var declarations = {};
		
		var style_obj;
		
		if ( 'undefined' !== typeof( rule_or_node.style.cssText ) ) {
			var raw_declarations = rule_or_node.style.cssText.split( /\s*;\s*/ );
			
			var matches;
			
			for ( var i = 0; i < raw_declarations.length; i++ ) {
				matches = raw_declarations[i].match( /^\s*([^:]+)\s*:\s*(.+)\s*$/ );
				
				if ( matches )
					declarations[matches[1].toLowerCase()] = matches[2];
			}
		}
		else {
			if ( rule_or_node.style )
				style_obj = rule_or_node.style;
			else
				style_obj = rule_or_node;
			
			var properties = [];
			for ( var i = 0; i < style_obj.length; i++ )
				properties.push( style_obj[i] );
			properties.sort();
			
			for ( var i = 0; i < properties.length; i++ ) {
				var property = this._get_property_standard_name( properties[i] );
				
				if ( 'undefined' !== typeof( style_obj[property] ) )
					declarations[property] = style_obj[property];
				else
					declarations[property] = style_obj[this._get_property_dom_name( property )];
			}
		}
		
		return declarations;
	}
	
	
	this.get_rule_index = function( selector ) {
		if ( 'undefined' === typeof( selector ) )
			return false;
		
		if ( ! this.rules )
			this._find_rules();
		if ( ! this.rules )
			return false;
		
		if ( 'undefined' !== typeof( this.rules[selector] ) )
			return selector;
		
		for ( var i = 0; i < this.rules.length; i++ ) {
			if ( this.rules[i].selectorText.toLowerCase() == selector.toLowerCase() )
				return i;
		}
		
		return false;
	}
	
	this.get_rule = function( selector ) {
		if ( 'undefined' === typeof( selector ) )
			return false;
		
		var index = this.get_rule_index( selector );
		
		if ( ( false === index ) || ( 'undefined' === typeof( this.rules[index] ) ) )
			return false;
		
		return this.rules[index];
	}
	
	this.add_rule = function( selector, declarations ) {
		this.update_rule( selector, declarations );
	}
	
	this.update_rule = function( selectors_raw, declarations ) {
		if ( ( 'undefined' === typeof( this.rules ) ) || ( 'undefined' === typeof( selectors_raw ) ) )
			return false;
		if ( 'undefined' === typeof( declarations ) )
			declarations = {};
		
		var selectors = selectors_raw.split( ',' );
		var rules = [];
		
		for ( var i = 0; i < selectors.length; i++ ) {
			var selector = selectors[i];
			
			if ( 'undefined' === typeof( selector ) )
				continue;
			
			var rule = this.get_rule( selector );
			
			try {
				if ( false === rule ) {
					rule = this._add_rule( selector, declarations );
					
					if ( false === rule )
						throw new Error( 'unable to add rule (' + selector + ')' );
				}
				else {
					for ( property in declarations ) {
						if ( null === declarations[property] ) {
							rule = this._remove_property( rule, property );
							
							if ( false === rule )
								throw new Error( 'unable to remove property (' + rule + ', ' + property + ')' );
						}
						else {
							rule = this._update_property( rule, property, declarations[property] );
							
							if ( false === rule )
								throw new Error( 'unable to update property (' + rule + ', ' + property + ', ' + declarations[property] + ')' );
						}
					}
				}
				
				rules.push( rule );
			}
			catch( error ) {
/*				if ( error.message )
					alert( error.message );
				else
					alert( error );*/
			}
		}
		
		return rules;
	}
	
	this._add_rule = function( selector, declarations ) {
		// Firefox 4, Chromium 12.0.742.30 (84361), Opera 11.10, IE 9, Safari 5.0.5 (Windows) (7533.21.1)
		
		try {
			var string_declarations = ( 'string' === typeof( declarations ) ) ? declarations : this._get_style_from_declarations( declarations );
			var rule_index = this.rules.length;
			
			this.stylesheet.insertRule( selector + ' {' + string_declarations + '}', rule_index );
			
			return this.rules[rule_index];
		}
		catch( error ) {}
		
		
		this._add_rule = this._add_rule_fallback;
		
		return this._add_rule( selector, declarations );
	}
	
	this._add_rule_fallback = function( selector, declarations ) {
		// IE 8, IE 7, IE 6
		
		try {
			var string_declarations = ( 'string' === typeof( declarations ) ) ? declarations : this._get_style_from_declarations( declarations );
			var rule_index = this.rules.length;
			
			this.stylesheet.addRule( selector, string_declarations, rule_index );
			
			return this.rules[rule_index];
		}
		catch( error ) {}
		
		return false;
	}
	
	this._update_property = function( rule, property, value, important ) {
		// Firefox 4, Chromium 12.0.742.30 (84361), Opera 11.10, Safari 5.0.5 (Windows) (7533.21.1)
		
		important = ( important && ( null !== value ) ) ? 'important' : '';
		
		try {
			rule.style.setProperty( property, value, important );
			
			return rule;
		}
		catch( error ) {}
		
		
		this._update_property = this._update_property_fallback;
		
		return this._update_property( rule, property, value, important );
	}
	
	this._update_property_ie = function( rule, property, value, important ) {
		// IE 9
		
		important = ( important && ( null !== value ) ) ? 'important' : '';
		
		try {
			// The function needs to be called twice in order to force IE to refresh the screen. The first
			// call needs to set a value of null. If it is only called once or if null is not passed, IE
			// will not refresh the screen.
			
			rule.style.setProperty( property, null, important );
			rule.style.setProperty( property, value, important );
			
			return rule;
		}
		catch( error ) {}
		
		
		this._update_property = this._update_property_ie_lt_9;
		
		return this._update_property( rule, property, value, important );
	}
	
	this._update_property_ie_lt_9 = function( rule, property, value, important ) {
		// IE 8, IE 7, IE 6
		
		important = ( important && ( null !== value ) ) ? 'important' : '';
		
		try {
			// IE 8 and below require the short version of a property name backgroundColor rather than
			// background-color in order for the DOM to be properly updated. If this is not done, then
			// only the first change will apply.
			
			rule.style.setAttribute( this.get_short_property( property ), value, null );
			
			return rule;
		}
		catch( error ) {}
		
		
		this._update_property = this._update_property_fallback;
		
		return this._update_property( rule, property, value, important );
	}
	
	this._update_property_fallback = function( rule, property, value, important ) {
		// Not used. Exists as a reasonable fallback.
		
		important = ( important && ( null !== value ) ) ? 'important' : '';
		
		try {
			rule.style.setAttribute( property, value, null );
			
			return rule;
		}
		catch( error ) {}
		
		
		return false;
	}
	
	this._remove_property = function( rule, property ) {
		// Firefox 4, Chromium 12.0.742.30 (84361), Safari 5.0.5 (Windows) (7533.21.1), IE 9, Opera 11.10
		
		try {
			rule.style.removeProperty( property );
			
			return rule;
		}
		catch( error ) {}
		
		
		this._remove_property = this._remove_property_fallback;
		
		return this._remove_property( rule, property );
	}
	
	this._remove_property_ie_lt_9 = function( rule, property ) {
		// IE 8, IE 7, IE 6
		
		try {
			rule.style.setAttribute( property, null, null );
			
			if ( rule.style.cssText ) {
				var css_text_test = new RegExp( '(?=[^\w\-]*)' + property + ':[^;]*;?', 'i' );
				
				while ( css_text_test.test( rule.style.cssText ) ) {
					rule.style.cssText = rule.style.cssText.replace( css_text_test, '' );
				}
			}
			
			return rule;
		}
		catch( error ) {}
		
		
		this._remove_property = this._remove_property_fallback;
		
		return this._remove_property( rule, property );
	}
	
	this._remove_property_fallback = function( rule, property ) {
		// Not used currently. Exists as a possible fallback.
		
		try {
			rule.style.setAttribute( property, null, null );
			
			return rule;
		}
		catch( error ) {}
		
		
		return false;
	}
	
	
	
	// Some browsers must have specific functions applied since they don't failover to the
	// fallbacks correctly. The following section assigns these specific functions.
	
	if ( this.is_ie ) {
		this._update_property = this._update_property_ie;
		
		if ( this.is_ie_lt_9 )
			this._remove_property = this._remove_property_ie_lt_9;
	}
	
	
	
	this.get_short_property = function( property ) {
		if ( 'undefined' === typeof( this.short_property_cache ) )
			this.short_property_cache = new Object();
		
		if ( 'undefined' !== typeof( this.short_property_cache[property] ) )
			return this.short_property_cache[property];
		
		var property_parts = property.split( '-' );
		
		this.short_property_cache[property] = property_parts[0];
		
		for ( var i = 1; i < property_parts.length; i++ )
			this.short_property_cache[property] += property_parts[i].charAt( 0 ).toUpperCase() + property_parts[i].slice( 1 );
		
		return this.short_property_cache[property];
	}
	
	this.delete_all_rules = function() {
		while ( this.rules.length > 0 )
			this._delete_rule_at_index( 0 );
	}
	
	this.delete_rule = function( selector ) {
		var index = this.get_rule_index( selector );
		
		if ( false === index )
			return false;
		
		this._delete_rule_at_index( index );
		
		return true;
	}
	
	this.delete_rule_property = function( selector, property ) {
		declarations = new Object;
		declarations[property] = null;
		
		this.update_rule( selector, declarations );
	}
	
	this._convert_rgb_to_hex = function( rgb ) {
		if ( 'undefined' !== typeof( this.converted_rgb_values[rgb] ) )
			return rgb.replace( /\brgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)/, this.converted_rgb_values[rgb], rgb );
		
		var digits = /rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/.exec( rgb );
		
		var red   = parseInt( digits[1] );
		var green = parseInt( digits[2] );
		var blue  = parseInt( digits[3] );
		
		var hex_raw = blue | ( green << 8 ) | ( red << 16 );
		
		hex = hex_raw.toString( 16 ).toUpperCase();
		
		while ( hex.length < 6 )
			hex = '0' + hex;
		
		this.converted_rgb_values[rgb] = '#' + hex;
		
		return rgb.replace( /\brgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)/, this.converted_rgb_values[rgb], rgb );
	}
	
	this.get_stylesheet_text = function() {
		var rules = this._get_stylesheet_rules_object( this.stylesheet );
		
		var stylesheet = '';
		
		for( selector in rules ) {
			var properties = this._get_selector_text( rules[selector] );
			
			if ( '' === properties )
				continue;
			
			var matches = selector.match( /(^|\s)([a-zA-Z]*[A-Z]+[a-zA-Z]*)\b/g );
			
			if ( null !== matches ) {
				for ( var i = 0; i < matches.length; i++ ) {
					var regex = new RegExp( '(^|\\s)' + matches[i].replace( /^\s+/, '' ) + '\\b' );
					selector = selector.replace( regex, matches[i].toLowerCase() );
				}
			}
			
			if ( '' !== stylesheet )
				stylesheet += "\n";
			stylesheet += selector + " {\n" + properties + '}';
		}
		
		return stylesheet;
	}
	
	this._get_selector_text = function( properties ) {
		var output = '';
		var rgb_regex = /\brgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)/;
		
		for( property in properties ) {
			var value = properties[property];
			
			if ( ( 'undefined' === typeof( value ) ) || ( '' === value ) )
				continue;
			
			if ( rgb_regex.test( value ) )
				value = this._convert_rgb_to_hex( value );
			
			output += "\t" + property + ": " + value + ";\n";
		}
		
		return output;
	}
	
	this.get_computed_style = function( node ) {
		if ( window.getComputedStyle )
			return window.getComputedStyle( node, '' );
		return node.currentStyle;
	}
	
	this.set_rules = function( new_style_rules, delete_rules ) {
		if ( 'undefined' === typeof( delete_rules ) )
			delete_rules = true;
		if ( true === delete_rules )
			this.delete_all_rules();
		
		if ( 'string' === typeof( new_style_rules ) )
			new_style_rules = this._get_rules_obj_from_string( new_style_rules );
		
		for ( selector in new_style_rules )
			this.update_rule( selector, new_style_rules[selector] );
	}
	
	
	this.init();
	
	return true;
}
