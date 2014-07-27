<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-01-18 - Chris Jean
		Forked from the 1.0.1 version of generator.php to retain legacy features.
*/


class BuilderStyleManagerGenerator {
	var $_var = 'builder_style_manager';
	var $_base_path = 'builder-style-manager';
	
	
	function delete_old_stylesheets() {
		if ( ! isset( $this->_options ) )
			$this->_storage =& new ITStorage( $this->_var, true );
		else
			$this->_storage->clear_cache();
		
		$this->_options = $this->_storage->load();
		
		
		if ( ( $uploads = wp_upload_dir() ) && ( false === $uploads['error'] ) ) {
			$files = glob( "{$uploads['basedir']}/builder-style-*.css" );
			
			if ( ! is_array( $files ) )
				return;
			
			sort( $files );
			
			$ids = array();
			
			foreach ( (array) $files as $file ) {
				if ( preg_match( '/builder-style-([a-z0-9]{13,30})\.css$/', $file, $matches ) ) {
					$found_id = null;
					
					foreach ( (array) $ids as $id => $id_files ) {
						if ( preg_match( "/^$id/", $matches[1] ) ) {
							$found_id = $id;
							break;
						}
					}
					
					if ( ! is_null( $found_id ) )
						$ids[$found_id][] = $file;
					else
						$ids[$matches[1]] = array( $file );
				}
			}
			
			foreach ( (array) $ids as $id => $files ) {
				if ( isset( $this->_options['styles'][$id] ) )
					$this->generate_stylesheet( $id );
				
				foreach ( (array) $files as $file )
					@unlink( $file );
			}
		}
	}
	
	function delete_stylesheet( $id ) {
		if ( ! isset( $this->_options ) )
			$this->_storage =& new ITStorage( $this->_var, true );
		else
			$this->_storage->clear_cache();
		
		$this->_options = $this->_storage->load();
		
		
		if ( isset( $this->_options['files'][$id]['file'] ) && file_exists( $this->_options['files'][$id]['file'] ) )
			unlink( $this->_options['files'][$id]['file'] );
		
		unset( $this->_options['files'][$id] );
		
		$this->_storage->save( $this->_options );
	}
	
	function generate_stylesheet( $id ) {
		it_classes_load( 'it-file-utility.php' );
		
		if ( ! isset( $this->_options ) )
			$this->_storage =& new ITStorage( $this->_var, true );
		else
			$this->_storage->clear_cache();
		
		$this->_options = $this->_storage->load();
		
		
		if ( ! isset( $this->_options['styles'][$id] ) )
			return new WP_Error( 'invalid_id', __( 'Unable to find the requested Builder Style id', 'it-l10n-builder-style-manager' ) );
		
		
		$stylesheet = "/* Standardize child theme styling */\n";
		$stylesheet .= file_get_contents( dirname( __FILE__ ) . '/css/normalize-legacy-styling.css' );
		
		$stylesheet .= "\n\n\n/* Style Manager generated css */\n";
		$stylesheet .= $this->_options['styles'][$id]['builder_css_preview'];
		
		if ( ! empty( $this->_options['styles'][$id]['builder_custom_css'] ) ) {
			$stylesheet .= "\n\n\n/* Style Manager custom css */\n";
			$stylesheet .= $this->_options['styles'][$id]['builder_custom_css'];
		}
		
		$name = $this->_get_stylesheet_name( $this->_options['styles'][$id]['name'] );
		$version = ( ! empty( $this->_options['files'][$id]['version'] ) ) ? $this->_options['files'][$id]['version'] : 0;
		
		
		$wrote_file = false;
		
		$file = ITFileUtility::get_writable_file( "{$this->_base_path}/$name.css" );
		
		if ( ! is_wp_error( $file ) ) {
			$result = ITFileUtility::write( $file, $stylesheet );
			
			$wrote_file = true;
		}
		
		
		unset( $this->_options['files'][$id] );
		
		if ( true === $wrote_file ) {
			$this->_options['files'][$id]['file'] = $file;
			$this->_options['files'][$id]['url'] = ITFileUtility::get_url_from_file( $file );
			$this->_options['files'][$id]['version'] = $version + 1;
		}
		else {
			$this->_options['files'][$id]['stylesheet'] = $stylesheet;
		}
		
		$this->_storage->save( $this->_options );
		
		
		return $this->_options['files'][$id];
	}
	
	function _get_stylesheet_name( $name ) {
		$name = strtolower( $name );
		$name = str_replace( ' ', '-', $name );
		$name = preg_replace( '/[^a-z0-9\-_]+/', '', $name );
		
		return $name;
	}
}
