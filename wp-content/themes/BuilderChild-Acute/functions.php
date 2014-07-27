<?php
/**
 * Core functions and functionality for BuilderChild-Acute
 *
 * @package Builder
 * @subpackage BuilderChild-Acute
 */

// require the files within the lib folder
require_once( dirname( __FILE__ ) . '/lib/admin/settings.php' );
require_once( dirname( __FILE__ ) . '/lib/structure/alternate-modules.php' );
require_once( dirname( __FILE__ ) . '/lib/structure/pagination.php' );

// Add Builder 3.0 Support
add_theme_support( 'builder-3.0' );
add_theme_support( 'builder-responsive' );
add_theme_support( 'builder-full-width-modules' );

// add theme support for post formats
add_theme_support( 'post-formats', array( 'gallery' ) );

