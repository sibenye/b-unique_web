<?php


function legacy_render_content() {
	
?>
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : // The Loop ?>
			<?php the_post(); ?>
			
			<div <?php post_class(); ?>>
				<!-- title, meta, and date info -->
				<div class="title clearfix">
					<div class="post-title">
						<!-- Use this instead? <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3> -->
						<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</div>
					
					<div class="post-meta">
						<?php printf( __( 'By %s', 'it-l10n-Builder' ), '<span class="meta-author">' . builder_get_author_link() . '</span>' ); ?>
						<?php do_action( 'builder_comments_popup_link', '<span class="meta-comments">&middot; ', '</span>', __( 'Comments %s', 'it-l10n-Builder' ), __( '(0)', 'it-l10n-Builder' ), __( '(1)', 'it-l10n-Builder' ), __( '(%)', 'it-l10n-Builder' ) ); ?>
					</div>
					
					<div class="date">
						<span class="weekday"><?php the_time( 'l' ); ?><span class="weekday-comma">,</span></span>
						<span class="month"><?php the_time( 'F' ); ?></span>
						<span class="day"><?php the_time( 'j' ); ?><span class="day-suffix"><?php the_time( 'S' ); ?></span><span class="day-comma">,</span></span>
						<span class="year"><?php the_time( 'Y' ); ?></span>
					</div>
				</div>
				
				<!-- "Read More" link -->
				<div class="post-content">
					<?php the_content( __( 'Read More&rarr;', 'it-l10n-Builder' ) ); ?>
				</div>
				
				<!-- categories, tags and comments -->
				<div class="meta-bottom clearfix">
					<?php do_action( 'builder_comments_popup_link', '<div class="alignright"><span class="comments">', '</span></div>', __( 'Comments %s', 'it-l10n-Builder' ), __( '(0)', 'it-l10n-Builder' ), __( '(1)', 'it-l10n-Builder' ), __( '(%)', 'it-l10n-Builder' ) ); ?>
					<div class="meta-bottom-left">
						<div class="categories"><?php printf( __( 'Categories : %s', 'it-l10n-Builder' ), get_the_category_list( ', ' ) ); ?></div>
						<?php the_tags( '<div class="tags">' . __( 'Tags : ', 'it-l10n-Builder' ), ', ', '</div>' ); ?>
					</div>
				</div>
			</div>
			<!-- end .post -->
			
			<?php comments_template(); // include comments template ?>
		<?php endwhile; // end of one post ?>
		
		<!-- Previous/Next page navigation -->
		<div class="paging clearfix">
			<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-Builder' ) ); ?></div>
			<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder' ) ); ?></div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}

remove_action( 'builder_layout_engine_render_content', 'render_content' );
add_action( 'builder_layout_engine_render_content', 'legacy_render_content' );
