<?php

/*
Code responsible for loading required parts of the theme
Written by Chris Jean for iThemes.com
Version 1.12.0

Version History
	1.7.0 - 2012-10-17 - Chris Jean
		Added support for builder-responsive theme feature.
		Added global variable builder_theme_supports_defaults to store theme support defaults.
	1.8.0 - 2012-10-18 - Chris Jean
		Added builder-responsive options: enable-breakpoints and column-min-width.
	1.9.0 - 2012-10-19 - Chris Jean
		Added builder-responsive option: enable-fluid-images
		Added to builder_theme_supports_defaults: builder-full-width-modules and builder-percentage-widths.
	1.10.0 - 2012-12-07 - Chris Jean
		Added to builder_theme_supports_defaults: builder-disable-stylesheet-cache.
	1.11.0 - 2013-01-17 - Chris Jean
		Better support of the loop-standard theme support.
		Added builder-responsive-ready support when theme indicates support for builder-responsive.
	1.11.1 - 2013-01-30 - Chris Jean
		Added a compatibility fix for Jetpack's Carousel.
	1.12.0 - 2013-02-14 - Chris Jean
		Updated require_once calls to require.
		Added loader for lib/gallery-shortcode/init.php when builder-gallery-shortcode is enabled.
*/


do_action( 'builder_start' );

// Set theme-specific global variables
$GLOBALS['wp_theme_name']          = 'Builder';
$GLOBALS['theme_index']            = 'it-builder';
$GLOBALS['theme_menu_var']         = 'ithemes-builder-theme';
$GLOBALS['wp_theme_page_name']     = 'ithemes-builder-theme';
$GLOBALS['builder_start_here_url'] = 'http://ithemes.com/start-here-tuts/?site=' . get_option( 'siteurl' );


require( dirname( dirname( __FILE__ ) ) . '/classes/load.php' );
require( dirname( __FILE__ ) . '/functions.php' );
require( dirname( __FILE__ ) . '/compat.php' );

if ( is_admin() )
	require( dirname( __FILE__ ) . '/admin-functions.php' );


// Set the memory_limit to be at least 64M
// This is to help bypass out of memory errors that happen with WordPress 3.0:
// http://core.trac.wordpress.org/ticket/14889
builder_set_minimum_memory_limit( '64M' );



function it_builder_register_product() {
	do_action( 'it_central_register_product', 'theme', 'Builder', dirname( dirname( dirname( __FILE__ ) ) ) . '/style.css' );
}
add_action( 'init', 'it_builder_register_product' );


function it_builder_load_theme_features() {
	global $wp_version;
	
	$path = builder_template_directory();
	
	
	it_classes_load( 'it-cache.php' );
	
	require( $path . '/lib/import-export/init.php' );
	require( $path . '/lib/widgets/init.php' );
	require( $path . '/lib/theme-settings/init.php' );
	require( $path . '/lib/layout-engine/init.php' );
	require( $path . '/lib/title/init.php' );
	
	
	$file_cache = ( builder_theme_supports( 'builder-file-cache' ) ) ? true : false;
	
	$GLOBALS['builder_cache'] =& new ITCache( 'builder-core', array( 'enable_file_cache' => $file_cache ) );
	$GLOBALS['builder_cache']->add_content_type( 'javascript-footer', 'javascript-footer.js', 'text/javascript', array( 'async_load' => true ) );
	$GLOBALS['builder_cache']->add_content_filter( 'javascript', 'builder_filter_javascript_content' );
	$GLOBALS['builder_cache']->add_content_filter( 'javascript-footer', 'builder_filter_javascript_footer_content' );
	$GLOBALS['builder_cache']->add_content_filter( 'css', 'builder_filter_css_content' );
	
	
	// Add support for builder-3.0 and builder-responsive if the Builder core theme is the active theme
	if ( builder_parent_is_active() ) {
		add_theme_support( 'builder-3.0' );
		
		if ( ! get_theme_support( 'builder-responsive' ) )
			add_theme_support( 'builder-responsive' );
	}
	
	
	if ( builder_theme_supports( 'builder-my-theme-menu' ) )
		require( $path . '/lib/tutorials/init.php' );
	
	// Compatibility check for pre-3.0 automatic-feed-links support
	if ( version_compare( $wp_version, '2.9.7', '<=' ) && builder_theme_supports( 'automatic-feed-links' ) && function_exists( 'automatic_feed_links' ) )
		automatic_feed_links();
	
	if ( builder_theme_supports( 'builder-extensions' ) )
		require( $path . '/lib/extensions/init.php' );
	
	if ( builder_theme_supports( 'builder-admin-bar' ) )
		require( $path . '/lib/admin-bar/init.php' );
	
	if ( builder_theme_supports( 'builder-plugin-features' ) )
		require( $path . '/lib/plugin-features/init.php' );
	
	if ( builder_theme_supports( 'builder-3.0' ) )
		add_theme_support( 'loop-standard' );
	
	if ( builder_theme_supports( 'loop-standard' ) )
		require( $path . '/lib/loop-standard/functions.php' );
	
	if ( builder_theme_supports( 'builder-responsive' ) ) {
		add_theme_support( 'builder-percentage-widths' );
		add_theme_support( 'builder-responsive-ready' );
		
		require( $path . '/lib/responsive/init.php' );
	}
	
	if ( builder_theme_supports( 'builder-gallery-shortcode' ) )
		require( $path . '/lib/gallery-shortcode/init.php' );
	
	
	if ( 'on' == builder_get_theme_setting( 'dashboard_favicon' ) )
		add_action( 'admin_enqueue_scripts', 'builder_add_favicon', 0 );
	
	
	do_action( 'builder_theme_features_loaded' );
}
add_action( 'it_libraries_loaded', 'it_builder_load_theme_features', -10 );


// Now the text widget supports shortcodes
add_filter( 'widget_text', 'do_shortcode' );


add_action( 'admin_print_styles', 'builder_add_global_admin_styles' );
add_action( 'comment_form_comments_closed', 'builder_get_closed_comments_message' );
add_action( 'builder_template_show_not_found', 'builder_template_show_not_found' );
add_action( 'builder_comments_popup_link', 'builder_comments_popup_link', 10, 6 );

add_action( 'builder_add_stylesheets', 'builder_add_reset_stylesheet', -100 );
add_action( 'builder_add_stylesheets', 'builder_add_theme_stylesheet', 0 );
add_action( 'builder_add_stylesheets', 'builder_add_structure_stylesheet', 100 );


add_filter( 'builder_filter_favicon_url', 'builder_filter_favicon_url' );
add_filter( 'it_filter_theme_menu_var', 'it_set_theme_menu_var' );
add_filter( 'it_storage_filter_theme_index', 'it_set_theme_index' );
add_filter( 'it_tutorials_top_menu_icon', 'filter_it_tutorials_top_menu_icon' );
add_filter( 'it_tutorials_filter_url', 'builder_set_start_here_url' );
add_filter( 'admin_body_class', 'builder_filter_admin_body_classes' );
add_filter( 'builder_module_filter_css_prefix', 'builder_module_filter_css_prefix', 0 );
add_filter( 'img_caption_shortcode', 'builder_custom_caption_shortcode', 10, 3 );



$GLOBALS['builder_theme_supports_defaults'] = array(
	'builder-full-width-modules'       => array(),
	'builder-percentage-widths'        => array(),
	'builder-disable-stylesheet-cache' => false,
	'builder-responsive'               => array(
		'enable-fluid-images' => true,
		'enable-breakpoints'  => true,
		'column-min-width'    => '200',
		'enable-auto-margins' => true,
		'tablet-auto-margin'  => '1.5em',
		'mobile-auto-margin'  => '.75em',
		'tablet-width'        => 'layout-width',
		'mobile-width'        => '500px',
	),
);
