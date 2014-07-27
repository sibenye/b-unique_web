<?php
/**
 *	Template for displaying single posts and post types (unless otherwise stated).
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
					
					<?php get_template_part( 'content', 'single' ); ?>

					<?php comments_template(); // include comments template ?>

				<?php endwhile; // end of one post ?>
			</div>
			
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php next_post_link( '%link', '&larr; Newer' ); ?></div>
					<div class="alignright"><?php previous_post_link( '%link', 'Older &rarr;' ); ?></div>
				</div>
			</div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
