<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-01-18 - Chris Jean
		Forked from public.php version 1.0.2 to preserve legacy features.
*/


class BuilderStyleManagerPublic {
	var $_var = 'builder_style_manager';
	
	function BuilderStyleManagerPublic() {
		if ( current_user_can( 'switch_themes' ) && ! empty( $_GET['builder-render-no-styles'] ) && ! empty( $_GET['preview'] ) ) {
			if ( function_exists( 'show_admin_bar' ) )
				show_admin_bar( false );
			
			add_action( 'template_redirect', array( &$this, 'handle_preview' ) );
		}
		else {
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'builder_layout_engine_identified_layout', array( &$this, 'select_style' ) );
		}
	}
	
	function init() {
		$this->_storage =& new ITStorage( $this->_var, true );
		$this->_options = $this->_storage->load();
	}
	
	function handle_preview() {
		
		it_classes_load( 'it-file-utility.php' );
		
		$css_url = ITFileUtility::get_url_from_file( dirname( __FILE__ ) . '/css/normalize-legacy-styling.css' );
		
		wp_enqueue_style( "{$this->_var}-normalize-styling", $css_url );
		
		
		ob_start( array( &$this, 'filter_links' ) );
	}
	
	function filter_links( $content ) {
		return preg_replace_callback( "|(<a.*?href=([\"']))(.*?)([\"'].*?>)|", array( &$this, 'filter_links_callback' ), $content );
	}
	
	function filter_links_callback( $matches ) {
		if ( strpos($matches[4], 'onclick') !== false )
			$matches[4] = preg_replace('#onclick=([\'"]).*?(?<!\\\)\\1#i', '', $matches[4]);
		if (
			( false !== strpos( $matches[3], '/wp-admin/' ) )
		||
			( ( false !== strpos( $matches[3], '://' ) ) && ( 0 !== strpos($matches[3], get_option('home')) ) )
		||
			( false !== strpos( $matches[3], '/feed/' ) )
		||
			( false !== strpos( $matches[3], '/trackback/' ) )
		)
			return $matches[1] . "#$matches[2] onclick=$matches[2]return false;" . $matches[4];
		
		$link = add_query_arg( array( 'preview' => 1, 'builder-render-no-styles' => 1 ), $matches[3] );
		if ( 0 === strpos( $link, 'preview=1' ) )
			$link = "?$link";
		return $matches[1] . esc_attr( $link ) . $matches[4];
	}
	
	function select_style( $layout_id ) {
		if ( isset( $this->_options['layouts'][$layout_id] ) ) {
			$style_id = $this->_options['layouts'][$layout_id];
			
			if ( isset( $this->_options['styles'][$style_id] ) )
				$this->_style_id = $style_id;
		}
		if ( ! isset( $this->_style_id) && isset( $this->_options['global'] ) ) {
			$style_id = $this->_options['global'];
			
			if ( isset( $this->_options['styles'][$style_id] ) )
				$this->_style_id = $style_id;
		}
		
		if ( isset( $this->_style_id ) ) {
			if ( isset( $this->_options['files'][$this->_style_id] ) )
				$this->_stylesheet_data = $this->_options['files'][$this->_style_id];
			
			if ( empty( $this->_stylesheet_data['url'] ) && ! defined( 'BUILDER_STYLE_MANAGER_NO_CSS_FILE' ) ) {
				require_once( dirname( __FILE__ ) . '/generator-legacy.php' );
				
				$generator = new BuilderStyleManagerGenerator();
				$this->_stylesheet_data = $generator->generate_stylesheet( $this->_style_id );
			}
			
			add_action( 'wp_print_styles', array( &$this, 'render_stylesheet' ) );
		}
	}
	
	function render_stylesheet() {
		if ( defined( 'BUILDER_STYLE_MANAGER_NO_CSS_FILE' ) ) {
			unset( $this->_stylesheet_data['url'] );
			
			if ( empty( $this->_stylesheet_data['stylesheet'] ) && ! empty( $this->_stylesheet_data['file'] ) )
				$this->_stylesheet_data['stylesheet'] = file_get_contents( $this->_stylesheet_data['file'] );
		}
		
		if ( ! empty( $this->_stylesheet_data['url'] ) ) {
			it_classes_load( 'it-utility.php' );
			
			$url = ITUtility::fix_url( $this->_stylesheet_data['url'] );
			
			
			$stylesheet_data = $this->_options['files'][$this->_style_id];
			
			$version = "{$this->_stylesheet_data['version']}.css";
			
			if ( defined( 'BUILDER_STYLE_MANAGER_NO_CSS_VERSION' ) )
				$version = null;
			
			wp_enqueue_style( "it-builder-style-{$this->_style_id}", $url, array(), $version );
		}
		else if ( ! empty( $this->_stylesheet_data['stylesheet'] ) )
			echo "<style type='text/css'>\n{$this->_stylesheet_data['stylesheet']}\n</style>\n";
	}
}

new BuilderStyleManagerPublic();
