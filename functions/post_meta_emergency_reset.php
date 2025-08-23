<?php

/**
 * Emergency reset
 *
 */

//add_action('admin_init', 'brhg2016_reset_event_series_meta');

/**
 * This function can be used, with the action above
 * to reset a meta value on all instances of a post type.
 */
function brhg2016_meta_emergency_reset() {

    $post_type = 'event_series';
    $meta_to_reset = 'event_series_in_slider';

    $args = array (
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'fields' => 'ids'
        );

    $reset_query = new WP_Query( $args );

    foreach ( $reset_query->posts as $id ) {

        delete_post_meta( $id, $meta_to_reset );
        
    }
}