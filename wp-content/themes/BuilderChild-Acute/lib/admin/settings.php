<?php
/**
 *	Builder Child Settings
 *	Adds additional setting for this Builder child theme.
 *
 *	@package 		Builder
 *	@subpackage		BuilderChild-Acute
 *	@since			1.0.0
*/

if ( !function_exists( 'builder_child_settings' ) ) {
	function builder_child_settings( $form ) { 
?>
			<p><?php _e( 'In addition to the default Builder settings, Acute give you additional settings. Included in the settings is the ability to disable the pagination for this child theme. You have complete control over this setting below.', 'it-l10n-BuilderChild-Acute' ); ?></p>
			<hr>
			<p><?php _e( 'Builder Child Acute comes with paginaiton built in. If you are using a plugin and do not wish to use the pagination within Acute you can disable it below.', 'it-l10n-BuilderChild-Acute' ); ?></p>
			<p><?php $form->add_check_box( 'remove_pagination' ); ?> <span><?php _e( 'Check to remove pagination inside the loop utility.', 'it-l10n-BuilderChild-Acute' ); ?></span></p>
<?php
	}
}

if ( ! function_exists( 'builder_child_libraries_loaded' ) ) {
	function builder_child_libraries_loaded() {
		if ( function_exists( 'builder_add_settings_editor_box' ) ) {
			builder_add_settings_editor_box( __( 'Builder Child Acute Settings', 'it-l10n-BuilderChild-Acute' ), 'builder_child_settings' );
		}
	}
	add_action( 'it_libraries_loaded', 'builder_child_libraries_loaded' );
}

if ( !function_exists( 'builder_child_settings_defaults' ) ) {
	function builder_child_settings_defaults( $defaults ) {
		$defaults['remove_pagination'] = false;
		return $defaults;
	}
	add_filter( 'builder_filter_default_settings', 'builder_child_settings_defaults' );
}

