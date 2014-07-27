<?php
/*
 * Let's create our own post navigation with pagination.
 * Used on index and archive
 *
*/

if ( ! function_exists( 'builder_child_pagination' ) ) {
	function builder_child_pagination( $pages = '', $range = 2 ) {  
		$showitems = ( $range * 2 ) + 1;  
		
		global $paged;
		if( empty( $paged ) ) $paged = 1;
		
		if($pages == '')
		{
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if( ! $pages ) {
			    $pages = 1;
			}
		}

		if(1 != $pages) {
			
			// provide the ability to jump to the first page.
			if( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) echo "<a href='" . get_pagenum_link(1) . "' title='Jump to the first page'>&larr;</a>\n";

			// start the paging
			for ( $i = 1; $i <= $pages; $i++ ) {
				if ( 1 != $pages && ( ! ( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems )) {
					echo ( $paged == $i ) ? "<span class='current'>" . $i . "</span>\n" : "<a href='" . get_pagenum_link( $i ) . "' class='inactive' title='Page " . $i . "' >" . $i . "</a>\n";
				}
			}

			// provide the ability to jump to the last page.
			if ( $paged < $pages-1 && $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='" . get_pagenum_link( $pages ) . "' title=\"Jump to the last page\">&rarr;</a>\n";
		}
	}
}