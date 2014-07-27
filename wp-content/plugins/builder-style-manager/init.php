<?php

/*
Plugin Name: Builder Style Manager - BETA
Plugin URI: http://ithemes.com/
Description: Provides basic style management for Builder
Version: 0.6.4
Author: iThemes
Author URI: http://ithemes.com/
*/


if ( 'builder' != strtolower( get_template() ) ) {
	function it_builder_style_manager_invalid_theme_notice() {
		echo "<div class='error'><p>The Builder Style Manager plugin requires that Builder or a Builder child theme is active in order for it to function.</p></div>";
	}
	add_action( 'admin_notices', 'it_builder_style_manager_invalid_theme_notice' );
	
	return;
}

require_once( dirname( __FILE__ ) . '/lib/classes/load.php' );


function it_builder_style_manager_init() {
	$path = dirname( __FILE__ );
	
	if ( function_exists( 'builder_theme_supports' ) && ( builder_theme_supports( 'builder-responsive-ready' ) || builder_theme_supports( 'builder-responsive' ) || builder_theme_supports( 'builder-percentage-widths' ) ) )
		$type = '';
	else
		$type = '-legacy';
	
	if ( is_admin() )
		require_once( $path . "/editor$type.php" );
	else
		require_once( $path . "/public$type.php" );
}
add_action( 'builder_theme_features_loaded', 'it_builder_style_manager_init' );
