<?php
/**
 *	Making Modules Full Width
 *	Additional Information
 *
 *
*/

// Moving classes and width from outer wrapper to inner wrapper
function it_set_full_width_container( $width ) { 
    remove_filter( 'builder_get_container_width', 'it_set_full_width_container' );
    
    return ''; 
}
add_filter( 'builder_get_container_width', 'it_set_full_width_container' );

function it_set_full_width_module( $fields ) { 
    global $it_original_module_width;
    
    $it_original_module_width = ''; 
    
    foreach ( (array) $fields['attributes']['style'] as $index => $value ) { 
        if ( preg_match( '/^(width:.+)/i', $value, $matches ) ) { 
            $it_original_module_width = $matches[1];
            unset( $fields['attributes']['style'][$index] );
        }
        if ( preg_match( '/^overflow:/', $value ) ) { 
            unset( $fields['attributes']['style'][$index] );
            $fields['attributes']['style'][] = 'overflow:visible;';
        }
    }
    add_filter( 'builder_module_filter_inner_wrapper_attributes', 'it_constrain_full_width_module_inner_wrapper' );
    
    return $fields;
}
add_filter( 'builder_module_filter_outer_wrapper_attributes', 'it_set_full_width_module' );

function it_constrain_full_width_module_inner_wrapper( $fields ) { 
    global $it_original_module_width;
    
    remove_filter( 'builder_module_filter_inner_wrapper_attributes', 'it_constrain_full_width_module_inner_wrapper' );
    
    $fields['attributes']['style'][] = $it_original_module_width;
    
    $it_original_module_width = ''; 
    
    return $fields;
}