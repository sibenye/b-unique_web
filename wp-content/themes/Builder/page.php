<?php

function render_content() {
	
?> 
<table style="margin-top: 0;">
<tbody style="margin-top: 0;">
<tr>
<td style="background-color: #ffffff; border-color: #ffffff; padding-top: 0; padding-bottom: 0;" rowspan="2"><a href="http://test.bubaonline.com/wp-content/uploads/2013/06/Logo1a.jpg"><img class="alignnone size-full wp-image-25" style="border-color: #ffffff;" alt="Logo1a" src="http://test.bubaonline.com/wp-content/uploads/2013/06/Logo1a.jpg" width="295" height="288" /></a></td>
<td style="background-color: #ffffff; border-color: #ffffff; padding-top: 0; padding-bottom: 0;"><a href="http://test.bubaonline.com/wp-content/uploads/2013/06/headimage.png"><img class="alignnone size-full wp-image-28" style="border-color: #ffffff;" alt="headimage" src="http://test.bubaonline.com/wp-content/uploads/2013/06/headimage.png" width="450" height="140" /></a></td>
</tr>
<tr>
<td style="background-color: #ffffff; border-color: #ffffff; padding-top: 0; padding-bottom: 0;"><a href="http://test.bubaonline.com/wp-content/uploads/2013/06/adimage1.png"><img class="alignnone size-full wp-image-27" style="border-color: #ffffff;" alt="adimage1" src="http://test.bubaonline.com/wp-content/uploads/2013/06/adimage1.png" width="450" height="140" /></a></td>
</tr>
</tbody>
</table>

<div class="content-title"><?php the_title(); ?></div>
<div class="content-center">
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : // The Loop ?>
					<?php the_post(); ?>
					
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<!-- <div class="entry-header clearfix">
								<h1 class="entry-title"><?php //the_title(); ?></h1>
						</div> -->
						
						<!-- post content -->
						<div class="entry-content clearfix">
							<?php the_content(); ?>
						</div>
						</div>
						<div class="entry-footer clearfix">
							<?php wp_link_pages( array( 'before' => '<p class="entry-utility"><strong>' . __( 'Pages:', 'it-l10n-Builder' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
							<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder' ), '<p class="entry-utility edit-entry-link">', '</p>' ); ?>
						</div>
					</div>
					<!-- end .post -->
					
					<?php comments_template(); // include comments template ?>
				<?php endwhile; // end of one post ?>
			</div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
