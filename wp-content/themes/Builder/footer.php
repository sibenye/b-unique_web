<?php


function render_footer() {
	$builder_link = '<a href="http://ithemes.com/purchase/builder-theme/" title="iThemes Builder">iThemes Builder</a>';
	$ithemes_link = '<a href="http://ithemes.com/" title="iThemes WordPress Themes">iThemes</a>';
	$wordpress_link = '<a href="http://wordpress.org">WordPress</a>';
	$slybase_link = '<a href="http://slybase.com" target="_blank">Slybase Services</a>';
	$admin_link = '<a href="http://bubba-online.com/wp-admin">Office Use</a>';
	
	/*$footer_credit = sprintf( __( '%1$s by %2$s<br />Powered by %3$s', 'it-l10n-Builder' ), $builder_link, $ithemes_link, $wordpress_link );*/

	$footer_credit = sprintf(__('%1$s <br/>Developed By %2$s', 'it-l10n-Builder'), $admin_link, $slybase_link);
	$footer_credit = apply_filters( 'builder_footer_credit', $footer_credit );
	
?>
	<div class="alignleft">
		<strong><?php bloginfo( 'name' ); ?></strong><br />
		<?php printf( __( 'Copyright &copy; %s All Rights Reserved', 'it-l10n-Builder' ), date( 'Y' ) ); ?>
	</div>
	<div class="alignright">
		<?php echo $footer_credit; ?>
	</div>
	<?php wp_footer(); ?>
<?php
	
}

add_action( 'builder_layout_engine_render_footer', 'render_footer' );