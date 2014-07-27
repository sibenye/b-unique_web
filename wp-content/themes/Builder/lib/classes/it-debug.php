<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2012-06-15 - Chris Jean
		Forked from ITUtility version 1.4.2
	1.0.1 - 2012-12-03 - Chris Jean
		Fixed bug with 0's being printed as empty strings in _inspect_dive.
*/


if ( ! class_exists( 'ITDebug' ) ) {
	class ITDebug {
		function print_r( $data, $args = array() ) {
			if ( is_string( $args ) )
				$args = array( 'description' => $args );
			else if ( is_bool( $args ) )
				$args = array( 'expand_objects' => $args );
			else if ( is_numeric( $args ) )
				$args = array( 'max_depth' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			
			$default_args = array(
				'description'    => '',
				'expand_objects' => true,
				'max_depth'      => 10,
			);
			$args = array_merge( $default_args, $args );
			
			if ( ! empty( $args['description'] ) )
				$args['description'] .= "\n";
			
			
			echo "<pre style='color:black;background:white;padding:15px;font-family:\"Courier New\",Courier,monospace;font-size:12px;white-space:pre-wrap;text-align:left;max-width:100%;'>";
			echo $args['description'];
			ITDebug::inspect( $data, $args );
			echo "</pre>\n";
		}
		
		function get_backtrace_description( $backtrace, $backtrace_args = array() ) {
			$default_backtrace_args = array(
				'remove_abspath' => true,
			);
			$backtrace_args = array_merge( $default_backtrace_args, $backtrace_args );
			
			
			extract( $backtrace );
			
			
			$args = ITDebug::_flatten_backtrace_description_args( $args );
			
			if ( ( true == $backtrace_args['remove_abspath'] ) && isset( $file ) )
				$file = preg_replace( '/^' . preg_quote( ABSPATH, '/' ) . '/', '', $file );
			
			
			if ( isset( $class ) && isset( $type ) && isset( $function ) && isset( $args ) ) {
				return "<strong>$class$type$function(</strong>$args<strong>)</strong>";
			}
			else if ( isset( $function ) && isset( $args ) ) {
				if ( isset( $file ) && isset( $line ) ) {
					return "<strong>$function(</strong>$args<strong>)</strong>  on line $line of $file";
				}
				
				return "<strong>$function(</strong>$args<strong>)</strong>";
			}
			
			
			return 'STring!';
		}
		
		function _flatten_backtrace_description_args( $args, $max_depth = 2, $depth = 0 ) {
			if ( is_string( $args ) )
				return "'$args'";
			if ( is_int( $args ) )
				return "(int) $args";
			if ( is_float( $args ) )
				return "(float) $args";
			if ( is_bool( $args ) )
				return '(bool) ' . ( $args ? 'true' : 'false' );
			if ( is_object( $args ) )
				return '(object) ' . get_class( $args );
			if ( ! is_array( $args ) )
				return '[unknown]';
			
			if ( $depth == $max_depth ) {
				if ( empty( $args ) )
					return 'array()';
				else
					return 'array( ... )';
			}
			
			
			$flat_args = array();
			
			foreach ( $args as $arg )
				$flat_args[] = ITDebug::_flatten_backtrace_description_args( $arg, $max_depth, $depth + 1 );
			
			$args = implode( ', ', $flat_args );
			
			if ( ! empty( $args ) )
				$args = " $args ";
			
			if ( 0 == $depth )
				return $args;
			
			return "array($args)";
		}
		
		function get_backtrace( $args = array() ) {
			if ( is_bool( $args ) )
				$args = array( 'expand_objects' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$default_args = array(
				'expand_objects' => false,
				'limit'          => 0,
				'offset'         => 0,
				'type'           => 'array',  // 'array' or 'string'
			);
			$args = array_merge( $default_args, $args );
			
			
			$backtrace = debug_backtrace();
			unset( $backtrace[0] );
			
			if ( $args['offset'] > 0 )
				$backtrace = array_slice( $backtrace, $args['offset'] );
			
			if ( $args['limit'] > 0 )
				$backtrace = array_slice( $backtrace, 0, $args['limit'] );
			
			$backtrace = array_values( $backtrace );
			
			
			if ( 'string' == $args['type'] ) {
				$string_backtrace = '';
				
				foreach ( $backtrace as $trace )
					$string_backtrace .= ITDebug::get_backtrace_description( $trace, $args ) . "\n";
				
				$backtrace = $string_backtrace;
			}
			
			
			return $backtrace;
		}
		
		function backtrace( $args = array() ) {
			if ( is_string( $args ) )
				$args = array( 'description' => $args );
			else if ( is_bool( $args ) )
				$args = array( 'expand_objects' => $args );
			else if ( is_numeric( $args ) )
				$args = array( 'max_depth' => $args );
			else if ( ! is_array( $args ) )
				$args = array();
			
			
			$default_args = array(
				'description'    => '',
				'expand_objects' => false,
				'max_depth'      => 3,
				'type'           => '',
			);
			$args = array_merge( $default_args, $args );
			
			
			if ( isset( $args['offset'] ) )
				$args['offset']++;
			else
				$args['offset'] = 1;
			
			$backtrace = ITDebug::get_backtrace( $args );
			
			if ( 'string' == $args['type'] )
				echo $backtrace;
			else
				ITDebug::print_r( $backtrace, $args['description'], $args['expand_objects'], $args['max_depth'] + 1 );
		}
		
		function inspect( $data, $args = array() ) {
			it_classes_load( 'it-utility.php' );
			
			
			// Create a deep copy so that variables aren't needlessly manipulated.
			$data = unserialize( serialize( $data ) );
			
			
			$default_args = array(
				'expand_objects' => false,
				'max_depth'      => 2,
				'echo'           => true,
			);
			$args = array_merge( $default_args, $args );
			
			if ( $args['max_depth'] < 1 )
				$args['max_depth'] = 100;
			
			
			$retval = ITDebug::_inspect_dive( $data, $args['expand_objects'], $args['max_depth'] );
			
			
			if ( $args['echo'] )
				echo $retval;
			
			return $retval;
		}
		
		function _inspect_dive( $data, $expand_objects, $max_depth, $depth = 0, $show_array_header = true ) {
			$pad = ITUtility::pad( $depth, '    ' );
			
			if ( is_string( $data ) ) {
				if ( '' === $data )
					return "<strong>[empty string]</strong>";
				else
					return htmlspecialchars( $data );
			}
			
			if ( is_bool( $data ) )
				return ( $data ) ? '<strong>true</strong>' : '<strong>false</strong>';
			
			if ( is_null( $data ) )
				return '<strong>null</strong>';
			
			if ( is_object( $data ) ) {
				$class_name = get_class( $data );
				$retval = "<strong>Object</strong> $class_name";
				
				if ( ! $expand_objects || ( $depth == $max_depth ) )
					return $retval;
				
				$vars = get_object_vars( $data );
				
				if ( empty( $vars ) )
					$vars = '';
				else
					$vars = ITDebug::_inspect_dive( $vars, $expand_objects, $max_depth, $depth, false );
				
				$retval .= "$vars";
				
				return $retval;
			}
			
			if ( is_array( $data ) ) {
				$retval = ( $show_array_header ) ? '<strong>Array</strong>' : '';
				
				if ( empty( $data ) )
					return "$retval()";
				if ( $depth == $max_depth )
					return "$retval( ... )";
				
				$max = 0;
				
				foreach ( array_keys( $data ) as $index ) {
					if ( strlen( $index ) > $max )
						$max = strlen( $index );
				}
				
				foreach ( $data as $index => $val )
					$retval .= "\n$pad" . sprintf( "%-{$max}s", $index ) . "  <strong>=&gt;</strong> " . ITDebug::_inspect_dive( $val, $expand_objects, $max_depth, $depth + 1 );
				
				return $retval;
			}
			
			return '<strong>[' . gettype( $data ) . ']</strong> ' . $data;
		}
	}
}
