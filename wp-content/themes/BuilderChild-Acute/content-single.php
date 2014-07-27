<?php
/**
 * The default template for displaying content
 *
 * @package Builder
 * @subpackage BuilderChild-Acute
 * @since BuilderChild-Acute 1.0.0
 */
?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<div class="entry-header clearfix">
							<h3 class="entry-title">
								<!-- Use this instead? <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3> -->
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							
							<div class="entry-meta">
								<?php printf( __( 'By %s', 'it-l10n-BuilderChild-Acute' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
								<?php printf( __( ' on %s', 'it-l10n-BuilderChild-Acute' ), '<span class="date">' . get_the_date() . '</span>' ); ?>
								<?php edit_post_link( 'Edit', ' <span class="">&middot; ', '</span>' ); ?>
								<?php do_action( 'builder_comments_popup_link', '<span class="comments right">', '</span>', __( '%s Comments', 'it-l10n-BuilderChild-Acute' ), __( 'No', 'it-l10n-BuilderChild-Acute' ), __( '1', 'it-l10n-BuilderChild-Acute' ), __( '%', 'it-l10n-BuilderChild-Acute' ) ); ?>
							</div>
							
						</div>
						
						<!-- post content -->
						<div class="entry-content clearfix">
							<?php the_content( __( 'Read More...', 'it-l10n-BuilderChild-Acute' ) ); ?>
							<?php wp_link_pages(); ?>
						</div>
						
						<!-- categories, tags and comments -->
						<div class="entry-footer clearfix">
							<div class="entry-meta">
								<div class="categories left"><?php printf( __( 'Categories : %s', 'it-l10n-BuilderChild-Acute' ), get_the_category_list( ', ' ) ); ?></div>
								<?php the_tags( '<div class="tags right">' . __( 'Tags : ', 'it-l10n-BuilderChild-Acute' ), ', ', '</div>' ); ?>
							</div>
						</div>
					</div>
					<!-- end .post -->
