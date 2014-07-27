<?php
if ( ! function_exists( 'it_builder_loaded' ) ) { 
	function it_builder_loaded() {

		builder_register_module_style( 'widget-bar', 'Gradient Background', 'builder-module-widget-bar-gradient' );
		builder_register_module_style( 'widget-bar', 'Light Background', 'builder-module-widget-bar-light' );
		builder_register_module_style( 'widget-bar', 'No Background', 'builder-module-widget-bar-no-background' );

	}   
	add_action( 'it_libraries_loaded', 'it_builder_loaded' );
}
?>