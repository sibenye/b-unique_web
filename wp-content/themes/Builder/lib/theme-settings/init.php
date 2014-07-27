<?php

/*
Written by Chris Jean for iThemes.com
Version 1.2.1

Version History
	1.0.0 - 2010-12-15
		Release ready
	1.0.1 - 2011-02-22
		Set theme-settings data version to 1.0 using builder_set_data_version
		Check support for builder-my-theme-menu in order to load editor.php
		Add data source for BuilderDataSourceThemeSettings
		Added call to builder_theme_settings_loaded action
	1.1.0 - 2011-10-06 - Chris Jean
		Added loader for editor-features.php
	1.1.1 - 2011-10-19 - Chris Jean
		Removed references to TEMPLATEPATH
	1.2.0 - 2011-12-20 - Chris Jean
		Added builder_theme_settings_pre_settings_load just above builder_load_theme_settings()
	1.2.1 - 2013-02-15 - Chris Jean
		Removed unused SEO code.
*/


builder_set_data_version( 'theme-settings', '1.0' );


require_once( dirname( __FILE__ ) . '/functions.php' );
require_once( dirname( __FILE__ ) . '/defaults.php' );

if ( is_admin() ) {
	if ( current_theme_supports( 'builder-my-theme-menu' ) ) {
		require_once( dirname( __FILE__ ) . '/editor-features.php' );
		require_once( dirname( __FILE__ ) . '/editor.php' );
	}
	
	builder_add_import_export_data_source( 'BuilderDataSourceThemeSettings', dirname( __FILE__ ) . '/class.builder-data-source-theme-settings.php' );
}

function builder_theme_settings_upgrade() {
	require_once( dirname( __FILE__ ) . '/upgrade.php' );
}
add_action( 'it_storage_do_upgrade_builder-theme-settings', 'builder_theme_settings_upgrade' );

function builder_theme_settings_load_javascript_cache_generators() {
	require_once( dirname( __FILE__ ) . '/generators/analytics.php' );
}
add_action( 'it_file_cache_prefilter_builder-core_javascript', 'builder_theme_settings_load_javascript_cache_generators' );


add_action( 'wp_head', 'builder_render_javascript_header_cache' );
add_action( 'wp_head', 'builder_render_css_cache' );
add_action( 'builder_layout_engine_render_container', 'builder_render_javascript_footer_cache', 20 );

add_action( 'wp_head', 'builder_render_header_tracking_code' );
add_action( 'builder_layout_engine_render_container', 'builder_render_footer_tracking_code', 20 );


do_action( 'builder_theme_settings_pre_settings_load' );

builder_load_theme_settings( true );

do_action( 'builder_theme_settings_loaded' );
