<?php
/**
 *	Template for displaying blog posts at home.
 *
 *  @package 		Builder
 *  @subpackage 	BuilderChild-Acute
 *  @since			Version 1.0.0
*/

function render_content() {
	
?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php get_template_part( 'content', get_post_format() ); ?>

					<?php comments_template(); // include comments template ?>

				<?php endwhile; // end of one post ?>
			</div>
			
			<?php get_template_part( 'loop', 'footer' ); // include loop-footer template ?>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
