<?php

/*
Basic functions used by the responsive feature of Builder.
Written by Chris Jean for iThemes.com
Version 1.2.0

Version History
	1.0.0 - 2012-10-09 - Chris Jean
		Initial version
	1.1.0 - 2012-10-12 - Chris Jean
		Commented out responsive.css output in order to focus on generated stylesheets.
	1.2.0 - 2012-10-18 - Chris Jean
		Added builder_add_responsive_stylesheets function.
*/


function builder_add_fitvids_scripts() {
	$base_url = ITUtility::get_url_from_file( dirname( __FILE__ ) );
	
	wp_register_script( 'fitvids', "$base_url/js/jquery.fitvids-max-width-modification.js", array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'builder-init-fitvids', "$base_url/js/init-fitvids.js", array( 'fitvids' ), '1.0', true );
}

function builder_add_responsive_viewport_meta() {
	echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
}

function builder_add_responsive_stylesheets() {
	$directory = get_stylesheet_directory();
	$url_base = get_stylesheet_directory_uri();
	
	$files = array(
		'style-responsive.css' => 'tablet-width',
		'style-tablet.css'     => array(
			'mobile-width',
			'tablet-width',
		),
		'style-mobile.css'     => 'mobile-width',
	);
	
	$stylesheets = array();
	
	foreach ( array_keys( $files ) as $file ) {
		if ( file_exists( "$directory/$file" ) )
			$stylesheets[] = $file;
	}
	
	if ( empty( $stylesheets ) )
		return;
	
	$size_widths = array(
		'tablet-width' => builder_theme_supports( 'builder-responsive', 'tablet-width' ),
		'mobile-width' => builder_theme_supports( 'builder-responsive', 'mobile-width' ),
		'layout-width' => apply_filters( 'builder_get_layout_width', '' ),
	);
	
	foreach ( $stylesheets as $stylesheet ) {
		$widths = $files[$stylesheet];
		
		if ( is_array( $widths ) ) {
			$min_width = $widths[0];
			$max_width = $widths[1];
		}
		else {
			$min_width = '';
			$max_width = $widths;
		}
		
		if ( ! empty( $min_width ) && isset( $size_widths[$min_width] ) )
			$min_width = $size_widths[$min_width];
		if ( ! empty( $min_width ) && isset( $size_widths[$min_width] ) )
			$min_width = $size_widths[$min_width];
		
		if ( is_numeric( $min_width ) )
			$min_width .= 'px';
		
		if ( ! empty( $size_widths[$max_width] ) )
			$max_width = $size_widths[$max_width];
		if ( ! empty( $size_widths[$max_width] ) )
			$max_width = $size_widths[$max_width];
		
		if ( is_numeric( $max_width ) )
			$max_width .= 'px';
		
		
		if ( empty( $min_width ) )
			echo "<link rel=\"stylesheet\" href=\"$url_base/$stylesheet\" type=\"text/css\" media=\"only screen and (max-width: $max_width)\" />\n";
		else
			echo "<link rel=\"stylesheet\" href=\"$url_base/$stylesheet\" type=\"text/css\" media=\"only screen and (min-width: $min_width) and (max-width: $max_width)\" />\n";
	}
}
