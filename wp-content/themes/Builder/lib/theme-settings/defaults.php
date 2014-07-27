<?php

/*
Written by Chris Jean for iThemes.com
Version 1.6.0

Version History
	1.4.0 - 2011-10-19 - Chris Jean
		Added builder-header-flush theme feature
	1.5.0 - 2011-12-09 - Chris Jean
		Added gallery_default_render_method default value
	1.5.1 - 2011-12-20 - Chris Jean
		Changed the default include_cats to just a simple empty array
		Added default blank value for gosquared_enable
	1.6.0 - 2013-02-15 - Chris Jean
		Added theme feature support for Title Tag.
		Added theme feature support for Gallery Shortcode Customizations.
*/


function builder_theme_settings_set_defaults( $defaults ) {
	global $builder_theme_feature_options;
	
	$new_defaults = array(
		'include_pages'                 => array( 'home' ),
		'include_cats'                  => array(),
		'javascript_code_header'        => '',
		'javascript_code_footer'        => '',
		'identify_widget_areas'         => 'admin',
		'identify_widget_areas_method'  => 'empty',
		'enable_comments_page'          => '',
		'enable_comments_post'          => '1',
		'comments_disabled_message'     => 'none',
		'tag_as_keyword'                => 'yes',
		'cat_index'                     => 'no',
		'google_analytics_enable'       => '',
		'woopra_enable'                 => '',
		'gosquared_enable'              => '',
		'gallery_default_render_method' => 'columns',
	);
	
	foreach ( (array) $builder_theme_feature_options as $features ) {
		foreach ( (array) $features as $feature => $details )
			$new_defaults["theme_supports_$feature"] = $details['default_enabled'];
	}
	
	$defaults = ITUtility::merge_defaults( $defaults, $new_defaults );
	$defaults = apply_filters( 'builder_filter_theme_settings_defaults', $defaults );
	
	// Legacy
	$defaults = apply_filters( 'builder_filter_default_settings', $defaults );
	
	return $defaults;
}
add_filter( 'it_storage_get_defaults_builder-theme-settings', 'builder_theme_settings_set_defaults', 0 );


builder_add_theme_feature_option( 'builder-widget-duplicate-sidebar', __( 'Duplicate Sidebar Widget', 'it-l10n-Builder' ), __( 'The Duplicate Sidebar Widget allows for easy duplication of another sidebar\'s widgets.', 'it-l10n-Builder' ), 0 );
builder_add_theme_feature_option( 'builder-plugin-features', __( 'Plugin Features', 'it-l10n-Builder' ), __( 'Builder can provide custom coding, styling, and JavaScript to enhance specific plugins running alongside Builder. All of these enhancements can be removed by disabling this option.', 'it-l10n-Builder' ) );
builder_add_theme_feature_option( 'builder-extensions', __( 'Extensions', 'it-l10n-Builder' ), __( 'Builder\'s Extensions are like mini-themes that can be applied to Layouts or Views. This feature can be disabled if Extensions are not used so that Extensions are hidden from the interface.', 'it-l10n-Builder' ) );
builder_add_theme_feature_option( 'builder-admin-bar', __( 'Admin Bar Modifications', 'it-l10n-Builder' ), __( 'WordPress\' Admin Bar provides links to easily manage your site. Builder can add a "Builder" entry to the Admin Bar to give quick and easy access to Builder management features from the front-end of your site. Disabling this feature will prevent these modifications from being added.', 'it-l10n-Builder' ) );
builder_add_theme_feature_option( 'builder-title-tag', __( 'Title Tag', 'it-l10n-Builder' ), __( 'Builder can generate a full title tag that includes the name of the of the site. This is typically recommended for SEO purposes. However, this feature can sometimes conflict with an SEO plugin that also tries to add such modifications to the title. You can disable this feature to allow an SEO plugin to have full control over the title. Note that this feature may automatically be disabled when known-conflicting plugins are running (currently WordPress SEO and All in One SEO Pack).', 'it-l10n-Builder' ) );
builder_add_theme_feature_option( 'builder-gallery-shortcode', __( 'Gallery Shortcode Customizations', 'it-l10n-Builder' ), __( 'Builder has built-in gallery shortcode (<code>[gallery]</code>) customizations that allows the gallery to be more flexible. This results in the gallery automatically adjusting to various widths found in Layouts and to be responsive in responsive child themes. These customizations can create conflicts with plugins that also modify the gallery shortcode output. To avoid such conflicts, this feature can be disabled. Note that disabling this feature may require restyling the gallery design.', 'it-l10n-Builder' ) );
builder_add_theme_feature_option( 'builder-header-flush', __( 'Header Flush', 'it-l10n-Builder' ), __( '<strong>Advanced Setting:</strong> Builder flushes (sends) the page header for improved performance (<a href="http://www.stevesouders.com/blog/2009/05/18/flushing-the-document-early/">details</a>). This could cause problems with some plugins that attempt to do page redirects after Builder starts to render the site. Typically, the problems show up as <strong><code>Warning: Cannot modify header information - headers already sent</code></strong> and the redirects fail to work. If you are having this problem, disabling this feature may fix your issue.', 'it-l10n-Builder' ), 20 );
