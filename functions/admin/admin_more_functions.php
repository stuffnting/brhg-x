<?php

/**
 * Hide selected screen options from all users
 */
add_filter('hidden_meta_boxes', 'brhg2024_hide_selected_screen_options', 99, 2);

function brhg2024_hide_selected_screen_options($hidden, $screen) {
    $new_hidden = array(
        'wpseo_meta',
        'trackbacksdiv',
        'commentstatusdiv',
        'slugdiv',
        'authordiv',
        'revisionsdiv',
        'commentsdiv',
        'et_monarch_settings',
        'et_monarch_sharing_stats'
    );

    return $new_hidden;
}

/**
 * Hide screen option button from everyone except Randy
 */
//add_filter('screen_options_show_screen', 'brhg2024_hide_screen_options');

function brhg2024_hide_screen_options() {

    if (wp_get_current_user()->ID == WP_SITE_ADMIN) {
        return true;
    }

    return false;
}

/**
 * Add extra classes to the body tag of admin pages to aid jQuery.
 * 
 */
add_filter('admin_body_class', 'brhg2024_add_admin_body_classes', 50);

function brhg2024_add_admin_body_classes($classes) {

    // Collapse all meta boxes on single post edit pages
    $post_type_collapse_metaboxes = array(
        'articles',
        'pamphlets',
        'books',
        'event_series',
        'events',
        'post',
        'rad_his_listings',
        'project',
        'contributors'
    );

    $screen = get_current_screen();

    $post_type = $screen->post_type;
    $screen_type = $screen->base;

    if (in_array($post_type, $post_type_collapse_metaboxes) && $screen_type === 'post') {
        return $classes . " brhg-collapse-meta";
    }

    return $classes;
}


// Force all users meta boxes to be in the same order as super admin's
add_action('current_screen', 'brhg2024_meta_box_order');

function brhg2024_meta_box_order() {

    // Guard against WP_SITE_ADMIN not existing and keep linter happy
    if (!defined('WP_SITE_ADMIN')) {
        define('WP_SITE_ADMIN', false);
        return;
    }

    $screen = get_current_screen();

    // Check if this is not post edit screen
    if ($screen->base !== 'post' || empty($screen->post_type)) {
        return;
    }

    $user = wp_get_current_user();

    // Check the user is not the super admin, but can edit posts
    if (
        $user->ID != WP_SITE_ADMIN && current_user_can('edit_posts')
    ) {

        // Get the super admin's user meta data for meta-box-order_{$current_post_type}
        $user_meta_post_type = get_user_meta(
            WP_SITE_ADMIN,
            "meta-box-order_{$screen->post_type}",
            true
        );

        // Set the equivalent meta data for the user to be the same as the super admin's
        update_user_meta(
            $user->ID,
            "meta-box-order_{$screen->post_type}",
            $user_meta_post_type
        );
    }
}

/**
 * Only allow the cloned ACF field "BRHG Event Filter" in the group "Filter Options" for events post-type.
 */
add_filter('acf/prepare_field', 'brhg2024_control_filter_metabox');

function brhg2024_control_filter_metabox($field) {

    $screen = get_current_screen();

    // Is this the event filter field?
    if (empty($field['key']) || $field['key'] !== 'field_65e073a58a7f7_field_4f83418dac671') {
        return $field;
    }

    // Is this a single event edit screen?
    if ($screen->base == 'post' && $screen->post_type === 'events') {
        return $field;
    }

    // For every other admin page, don't add the event filter.
    return;
}
