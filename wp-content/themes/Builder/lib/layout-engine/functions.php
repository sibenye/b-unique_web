<?php

/*
Written by Chris Jean for iThemes.com
Version 1.4.0

Version History
	1.2.0 - 2011-07-05 - Chris Jean
		Added function builder_get_default_layouts
	1.2.1 - 2011-07-12 - Chris Jean
		Updated the serialize/unserialize process to use base64
			encoding/decoding to avoid system-specific read errors
	1.2.2 - 2011-10-06 - Chris Jean
		Moved most functions to lib/builder-core/functions.php.
	1.3.0 - 2012-10-09 - Chris Jean
		Added the builder_get_pixel_widths and builder_get_percent_widths functions.
	1.4.0 - 2012-10-12 - Chris Jean
		Improved the builder_get_percent_widths function.
*/


function builder_get_default_layouts( $layouts ) {
//	file_put_contents( dirname( __FILE__ ) . '/default-layouts.txt', base64_encode( serialize( $layouts ) ) );
	
	if ( ! empty( $layouts ) && is_array( $layouts ) && isset( $layouts['default'] ) )
		return $layouts;
	if ( ! is_array( $layouts ) )
		$layouts = array();
	
	$layouts = array();
	
	$defaults = unserialize( base64_decode( file_get_contents( dirname( __FILE__ ) . '/default-layouts.txt' ) ) );
	
	
	include_once( dirname( __FILE__ ) . '/upgrade-storage.php' );
	$data = apply_filters( 'it_storage_upgrade_layout_settings', array( 'data' => $defaults ) );
	$defaults = $data['data'];
	
	require_once( dirname( __FILE__ ) . '/layout-settings-guid-randomizer.php' );
	$defaults = BuilderLayoutSettingsGUIDRandomizer::randomize_guids( $defaults );
	
	return ITUtility::merge_defaults( $layouts, $defaults );
}

function builder_get_pixel_widths( $widths, $full_pixel_width ) {
	$total_width = array_sum( $widths );
	
	if ( $total_width >= 110 ) {
		$widths[count( $widths ) - 1] += $full_pixel_width - $total_width;
		
		return $widths;
	}
	
	
	$pixel_widths = array();
	$remaining_width = $full_pixel_width;
	
	foreach ( $widths as $index => $width ) {
		if ( ( $index + 1 ) == count( $widths ) )
			$width = $remaining_width;
		else
			$width = ceil( ( $width / 100 ) * $full_pixel_width );
		
		$pixel_widths[] = $width;
		$remaining_width -= $width;
	}
	
	return $pixel_widths;
}

function builder_get_percent_widths( $widths ) {
	$total_width = array_sum( $widths );
	
	if ( $total_width < 110 ) {
		$widths[count( $widths ) - 1] += 100 - $total_width;
		
		return $widths;
	}
	
	
	$percent_widths = array();
	$remaining_width = 100;
	
	$count = 1;
	
	foreach ( $widths as $index => $width ) {
		if ( $count == count( $widths ) )
			$width = $remaining_width;
		else
			$width = intval( $width / $total_width * 100000 ) / 1000;
		
		$percent_widths[$index] = $width;
		$remaining_width -= $width;
		
		$count++;
	}
	
	return $percent_widths;
}
