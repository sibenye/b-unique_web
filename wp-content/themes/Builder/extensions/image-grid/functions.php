<?php

if ( is_admin() )
	return;

if ( ! class_exists( 'BuilderExtensionImageGrid' ) ) {
	class BuilderExtensionImageGrid {
		
		function BuilderExtensionImageGrid() {
			
			// Include the file for setting the image sizes
			require_once( dirname( __FILE__ ) . '/lib/image-size.php' );
			
			// Helpers
			it_classes_load( 'it-file-utility.php' );
			$this->_base_url = ITFileUtility::get_url_from_file( dirname( __FILE__ ) );
			
			// Print necessary scripts and styles.
			add_action( 'wp_enqueue_scripts', array( &$this, 'do_enqueues' ) );
			
			// Calling only if not on a singular
			if ( ! is_singular() ) {
				add_action( 'builder_layout_engine_render', array( &$this, 'change_render_content' ), 0 );
			}
		}
		
		function do_enqueues() {
			wp_enqueue_script( 'it_colorbox', "$this->_base_url/js/jquery.colorbox-min.js", array( 'jquery' ) );
			wp_enqueue_script( 'builder_feature_image_colorbox_reference', "$this->_base_url/js/colorbox-reference.js", array( 'it_colorbox' ) );
			
			wp_enqueue_style( 'colorbox-1', "$this->_base_url/css/colorbox-1.min.css" );
		}
		
		function extension_render_content() {
			global $post, $wp_query;
			
			$args = array(
				'ignore_sticky_posts' => true,
				'posts_per_page'      => 9,
				'meta_key'            => '_thumbnail_id',
				'paged'               => get_query_var( 'paged' ),
			);
			
			$args = wp_parse_args( $args, $wp_query->query );
			
			query_posts( $args ); // Query only posts with a feature image set.
			
			?>
			<?php if ( have_posts() ) : ?>
				<div class="loop">
					<div class="loop-content">
						<?php while ( have_posts() ) : the_post(); // the loop ?>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php $galleryurl = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' ); ?>
								<div class="grid_wrapper entry-content">
									<div class="inner">
										<p class="slide_box">
											<a href="<?php echo $galleryurl[0]; ?>" title="<?php the_title(); ?>" rel="gallery-images" class="gallery-image"><img src="<?php echo $this->_base_url ?>/images/zoom.png" alt="Zoom Feature Image" /></a>
											<a href="<?php the_permalink(); ?>"  class="permalink"><img src="<?php echo $this->_base_url ?>/images/more.png" alt="Read This Article" /><?php printf( __( '<span>Read: %s</span>', 'it-l10n-Builder' ), '<strong>' . get_the_title() . '</strong>' ); ?></a>
										</p>
										<?php the_post_thumbnail( 'it-gallery-thumb', array( 'class' => 'it-gallery-thumb' ) ); ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endwhile; // end of one post ?>
					</div>
					
					<!-- Previous/Next page navigation -->
					<div class="loop-footer">
						<div class="loop-utility clearfix">
							<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page' , 'it-l10n-Builder' ) ); ?></div>
							<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder' ) ); ?></div>
						</div>
					</div>
				</div>
			<?php else : // do not delete ?>
				<?php do_action( 'builder_template_show_not_found' ); ?>
			<?php endif; // do not delete ?>
		<?php
		
		}
		
		function change_render_content() {
			remove_action( 'builder_layout_engine_render_content', 'render_content' );
			add_action( 'builder_layout_engine_render_content', array( &$this, 'extension_render_content' ) );
		}
	
	} // end class 
	
	$BuilderExtensionImageGrid = new BuilderExtensionImageGrid();
}
