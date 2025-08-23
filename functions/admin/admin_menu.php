<?php

/**
 * Controls main, left-hand, admin menu
 *
 * @package Wordpress
 * @subpackage BRHG2024
 * @since 2024
 */

// Alter admin menu entries
add_action('admin_menu', 'brhg2024_admin_menu', 999);

function brhg2024_admin_menu() {
    global $menu;
    global $submenu;

    /**
     * Remove menu items
     */

    // Remove from all
    remove_menu_page('link-manager.php');


    // Hide menu items from all users except Randy ID 6
    if (is_user_logged_in()) {
        $user = wp_get_current_user();

        if (defined('WP_SITE_ADMIN') && WP_SITE_ADMIN !== $user->ID) {
            remove_submenu_page('index.php', 'simple_history_page'); // index.php is the Dashboard
            remove_menu_page('plugins.php'); // Plugins
            remove_menu_page('themes.php'); // Appearance
            remove_menu_page('tools.php'); // Tools
            remove_menu_page('options-general.php'); // Settings
            remove_menu_page('edit.php?post_type=acf-field-group'); // AFC
            remove_menu_page('wspsc-menu-main'); // Cart settings
            remove_menu_page('edit.php?post_type=cookielawinfo');
            remove_menu_page('publication-orders-gdpr'); // GDPR, sales stats and wspsc cart orders
            remove_menu_page('wpseo_dashboard'); // Yoast
            remove_menu_page('wp-fail2ban-menu');
            remove_menu_page('postman'); // Post SMTP
            remove_menu_page('Wordfence');
            remove_menu_page('simple_history_admin_menu_page');
            remove_menu_page('options-general.php?page=updraftplus'); // UpDraft
        }
    }

    /**
     *  Change Post to Blog in menu
     */
    $menu[5][0] = 'Blog';
    $submenu['edit.php'][5][0] = 'Blog Posts';
    $submenu['edit.php'][10][0] = 'Add Blog Post';
    $submenu['edit.php'][16][0] = 'Tags';

    /**
     * Deal with shop-settings, a mixture of custom, wspsc and ACF options pages.
     * 
     */

    // This options page is added by ACF, and somehow gets the wrong name.
    $submenu['shop-settings'][0][0] = "Shop Settings";

    // Merge the current submenu for shop-settings, with the WPSC submenu items (orders post-type and add order)
    //$submenu['shop-settings'] = array_merge($submenu['shop-settings'], $submenu['edit.php?post_type=wpsc_cart_orders']);

    /**
     * Add extra separators, making sure they are not replacing array indexes that already exist.
     * They will be reorder with the menu_order filter below.
     */
    $first_key = key($menu);

    $menu[$first_key - 1] = array(
        0 => '',
        1 => 'read',
        2 => 'separator3',
        3 => '',
        4 => 'wp-menu-separator'
    );

    $menu[$first_key - 2] = array(
        0 => '',
        1 => 'read',
        2 => 'separator4',
        3 => '',
        4 => 'wp-menu-separator'
    );

    $menu[$first_key - 3] = array(
        0 => '',
        1 => 'read',
        2 => 'separator4',
        3 => '',
        4 => 'wp-menu-separator'
    );
}

// Order admin menu items
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'brhg2024_order_admin_menu', 1002, 1);

function brhg2024_order_admin_menu($menu_order) {

    if (!$menu_order) {
        return true;
    }

    return array(
        'index.php', // Dashboard
        'simple_history_admin_menu_page',
        'separator1',
        'edit.php', // Posts
        'edit.php?post_type=events',
        'edit.php?post_type=event_series',
        'edit.php?post_type=articles',
        'edit.php?post_type=pamphlets',
        'edit.php?post_type=books',
        'edit.php?post_type=rad_his_listings',
        'edit.php?post_type=project',
        'edit.php?post_type=contributors',
        'edit.php?post_type=venues',
        'edit.php?post_type=page',
        'separator2',
        'edit-comments.php',
        'brhg-details', // AFC Options Page
        'front-page', // AFC Options Page
        'upload.php', // Media
        'shop-settings',
        'wpcf7', // 'Contact' Contact Form 7
        'brhg-help',
        'separator3',
        'themes.php', // Appearance
        'plugins.php',
        'users.php',
        'tools.php',
        'options-general.php', //Settings
        'separator4',
        'edit.php?post_type=acf-field-group', // Advanced Custom Fields
        'wspsc-menu-main', // WP Simple Shopping Cart settings
        'wpseo_dashboard', // Yoast
        'postman', // Post SMTP
        'Wordfence',
        'edit.php?post_type=cookielawinfo',
        'options-general.php?page=updraftplus',
        'wp-fail2ban-menu',
        'separator-last',
    );
}
