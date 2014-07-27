<?php

/*
Written by Chris Jean for iThemes.com
Version 1.3.1

Version History
	1.0.0 - 2011-09-07 - Chris Jean
		Created from defaults.php version 1.2.0
	1.1.0 - 2011-12-09 - Chris Jean
		Added Gallery Shortcode options
	1.2.0 - 2013-01-10 - Chris Jean
		Removed the SEO tab.
	1.3.0 - 2013-02-15 - Chris Jean
		Moved code to run off of the it_libraries_loaded action.
		Added conditional loading of the Gallery Shortcode settings.
	1.3.1 - 2013-02-18 - Chris Jean
		Moved builder_add_settings_tab to outside of the function to prevent the tab from appearing after "Import / Export".
*/


builder_add_settings_tab( __( 'Basic', 'it-l10n-Builder' ), 'basic', 'ITThemeSettingsTabBasic', dirname( __FILE__ ) . '/tab-basic.php' );


function it_builder_configure_editor_features() {
	builder_add_settings_editor_box( __( 'Menu Builder', 'it-l10n-Builder' ), null, array( 'var' => 'menu_builder', '_builtin' => true ) );
	builder_add_settings_editor_box( __( 'Analytics and JavaScript Code', 'it-l10n-Builder' ), null, array( 'var' => 'analytics', '_builtin' => true ) );
	builder_add_settings_editor_box( __( 'Favicon', 'it-l10n-Builder' ), null, array( 'var' => 'favicon', '_builtin' => true ) );
	builder_add_settings_editor_box( __( 'Identify Widget Areas', 'it-l10n-Builder' ), null, array( 'var' => 'widgets', '_builtin' => true ) );
	builder_add_settings_editor_box( __( 'Comments', 'it-l10n-Builder' ), null, array( 'var' => 'comments', '_builtin' => true ) );
	
	if ( builder_theme_supports( 'builder-gallery-shortcode' ) )
		builder_add_settings_editor_box( __( 'Gallery Shortcode', 'it-l10n-Builder' ), null, array( 'var' => 'gallery_shortcode', '_builtin' => true ) );
	
	builder_add_settings_editor_box( __( 'Theme Features', 'it-l10n-Builder' ), null, array( 'var' => 'theme_features', '_builtin' => true, 'priority' => 'low' ) );
}
add_action( 'it_libraries_loaded', 'it_builder_configure_editor_features' );
