<?php
/**
 *	Template for displaying the Loop's footer
 *	Found on index and archive pages.
 *
 *	@package 		Builder
 *	@subpackage		BuilderChild-Acute
 *	@since			1.0.0
*/
?>
			<div class="loop-footer">
				<?php if ( function_exists( 'builder_child_pagination' ) && ( builder_get_theme_setting( 'remove_pagination' ) != true ) ) : ?>
					<!-- previous/next page navigation with pagination -->
					<div class="loop-utility clearfix pagination">
						<div class="paging">
							<?php builder_child_pagination(); ?>
						</div>
						<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-BuilderChild-Acute' ) ); ?></div>
						<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-BuilderChild-Acute' ) ); ?></div>
					</div>
				<?php else : ?>
					<!-- previous/next page navigation -->
					<div class="loop-utility clearfix">
						<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-BuilderChild-Acute' ) ); ?></div>
						<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-BuilderChild-Acute' ) ); ?></div>
					</div>
				<?php endif; ?>
			</div>