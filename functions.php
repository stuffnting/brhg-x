<?php

/**
 * The functions.php file for the BRHG2023 theme
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 *
 */

/**
 * Front and Admin
 */
// Custom Post Types 
require_once('functions/custom_posts_tax_queries/custom_post_types.php');
// Custom Taxonomies - includes brhg2016_pub_range_meta_cb() dropdown list for edit screens
require_once('functions/custom_posts_tax_queries/custom_taxonomies.php');
// Contributor Alphabet
require_once('functions/custom_posts_tax_queries/contrib_alphabet.php');
/* Utility functions*/
require_once('functions/utilities/utility_functions.php');
// Project meta-box for edit pages
require_once('functions/custom_posts_tax_queries/project_meta_box.php');
// Event time stamp generator
require_once('functions/utilities/time_stamp_gen.php');
// post-2-posts
require_once('functions/custom_posts_tax_queries/post-2-posts.php');
// Custom queries
require_once('functions/custom_posts_tax_queries/custom_query.php');
// Creates code for the bookshop download page and changes the contents of the sales email
require_once('functions/publications/bookshop.php');
// AFC Options page for pricing pamphlets, including shortcode
require_once('functions/publications/publication_controls.php');
// Related items. Replacement for Microkid plugin.
require_once('functions/custom_posts_tax_queries/related_items.php');
// AFC Options page for front page featured items
require_once('functions/appearance/front_page_controls.php');

/**
 * Admin only
 */
if (is_admin()) {
    // Simply Show IDs
    require_once('functions/admin/simply_show_ids.php');
    // Extra admin page columns and Quick Edit - includes in_slider Meta Box and pub_range dropdown list
    require_once('functions/admin/admin_columns.php');
    // Controls the left-hand admin menu
    require_once('functions/admin/admin_menu.php');
    // Controls the left-hand admin menu
    require_once('functions/admin/admin_more_functions.php');
    // Stuff for the editor
    require_once('functions/admin/editor.php');
    // Shop settings: Delete personal data, sales stats
    require_once('functions/publications/publications_admin_pages.php');
    require_once('functions/publications/publication_orders_gdpr.php');
    require_once('functions/publications/publications_sales_count.php');
    // Help Page
    require_once('functions/admin/brhg2016_help.php');
    // Filter ACF post options fields, e.g. Is BRHG event?
    require_once('functions/custom_posts_tax_queries/afc_filter_for_posts.php');
}

/**
 * Not in Admin (front only)
 */
if (! is_admin()) {
    // Preloader
    require_once('functions/utilities/preloader.php');
    // Schema
    require_once('functions/utilities/schema.php');
    // Inline scripts
    require_once('functions/utilities/inline_scripts.php');
    // Content filters for the single.php template file
    require_once('functions/appearance/the_content.php');
    // Social media share buttons for posts
    require_once('functions/appearance/social_media_share.php');
    // Content filter for single publications page
    require_once('functions/publications/publication_the_content_and_trolley.php');
    //Comments and comment form
    require_once('functions/appearance/comment_form.php');
    // Front page about and featured sections
    require_once('functions/appearance/front_page_about_featured.php');
    // Shopping Trolley filters
    require_once('functions/publications/trolley.php');
    // Shortcode for the event-series event-list
    require_once('functions/appearance/event_list_shortcode.php');
    // Short codes for displaying the contact details from the BRHG Details options page
    require_once('functions/appearance/contact_details_shortcodes.php');
    // Short codes for the footnotes
    require_once('functions/appearance/footnotes_shortcode.php');
    // Shortcode for the publication price list
    require_once('functions/publications/publication_price_list_shortcode.php');
    // Shortcode for the for sale list on the where to buy page
    require_once('functions/publications/for_sale_list_shortcode.php');
    // Donate button shortcode
    require_once('functions/appearance/donate_button_shortcode.php');
    // ACF OpenStreetMap shortcode
    require_once('functions/appearance/map_shortcode.php');
}

/**
 * Max theme embed width
 * 
 * If you change this see images in brhg2016_setup()
 */
if (! isset($content_width)) {
    $content_width = 780;
}

/**
 * Setup function
 */
add_action('after_setup_theme', 'brhg2016_setup');

function brhg2016_setup() {
    load_theme_textdomain('brhg2016', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption'
    ));

    /**
     * Images
     */
    add_image_size('big_thumb', 332, 470, true); // for pamphleteer page and single listing and event listings
    add_image_size('listing_thumbnails', 100, 150, false);  // thumbnails for listing pages (not event series)
    add_image_size('2_or_3_across_crop', 270, 200, true);  // 2 across page width for 2 column, or 3 across for full width page cropped for height
    add_image_size('2_or_3_across_no_crop', 270, 9999, false);  // 2 across page width for 2 column, or 3 across for full width page not cropped for height
    add_image_size('medium_thumbs', 150, 150, false);
    add_image_size('tiny_thumbs', 50, 80, true); // for home page recent books, articles and admin list thumbnails 

    /**
     * Menus
     */
    register_nav_menus(array(
        'scroll_info_menu' => __('Scroll Info Menu', 'brhg2016'),
        'main_content_menu' => __('Main Content Menu', 'brhg2016'),
        'mobile_menu' => __('Mobile Menu', 'brhg2016'),
        'footer_menu' => __('Footer Menu', 'brhg2016'),
    ));
}

/**
 * Enqueue
 */
add_action('wp_enqueue_scripts', 'brhg2016_theme_style_scripts');

function brhg2016_theme_style_scripts() {
    //add jquery
    wp_enqueue_script('jquery');

    //add the mainstyle sheet
    wp_enqueue_style(
        'main-style',
        get_theme_file_uri('css/style.css'),
        array(),
        filemtime(get_template_directory() . '/css/style.css')
    );

    // Front page styles and needed for friends page
    if (is_front_page() || is_page('friends')) {
        wp_enqueue_style(
            'front-page-style',
            get_theme_file_uri('css/front-page-style.css'),
            array('main-style'),
            filemtime(get_template_directory() . '/css/front-page-style.css')
        );
    }

    // Tiny Slider for font page
    if (is_front_page()) {
        wp_enqueue_script(
            'tiny-slider',
            'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js',
            array('jquery'),
            false,
            array(
                'in_footer' => true
            )
        );
    }

    //Threaded comment & form validate
    if (is_singular() && comments_open() && post_type_supports(get_post_type(), 'comments')) {
        if (get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}

/**
 *  Stop jQuery Migrate loading by removing dependency from jQuery
 */
add_action('wp_default_scripts', 'brhg2024_dequeue_jquery_migrate');

function brhg2024_dequeue_jquery_migrate($scripts) {
    if (! is_admin() && ! empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        );
    }
}

/**
 * Add admin stylesheets and scripts
 */
add_action('admin_enqueue_scripts', 'brhg2016_admin_theme_style_scripts');
add_action('login_enqueue_scripts', 'brhg2016_admin_theme_style_scripts');

function brhg2016_admin_theme_style_scripts() {
    wp_enqueue_style(
        'brhg2025-admin-theme',
        get_template_directory_uri() . '/css/brhg-admin-styles.css',
        false,
        filemtime(get_template_directory() . '/css/brhg-admin-styles.css')
    );
    // Contains the quick edit js
    wp_enqueue_script(
        'admin-extra-script',
        get_template_directory_uri() . '/js/admin-extra.js',
        array('jquery'),
        filemtime(get_template_directory() . '/js/admin-extra.js'),
        true
    );
}

/**
 * Favicon
 */
add_action('admin_head', 'brhg2016_favicon');
add_action('login_head', 'brhg2016_favicon');
add_action('wp_head', 'brhg2016_favicon');

function brhg2016_favicon() {
    echo '<link rel="Shortcut Icon" type="image/x-icon" href="' . get_stylesheet_directory_uri() . '/images/favicon.ico" />';
}

/**
 * Kill Stuff
 */

/**
 *  Dequeue unwanted stuff
 */
add_action('wp_enqueue_scripts', 'brhg_remove_unwanted_css', 999);

function brhg_remove_unwanted_css() {
    wp_dequeue_style('wpsc-style');
    wp_dequeue_style('contact-form-7');
}

/**
 * Kill Gutenberg
 */
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

add_action('wp_enqueue_scripts', 'brhg2025_dequeue_gutenberg_scripts');

function brhg2025_dequeue_gutenberg_scripts() {
    // https://github.com/WordPress/gutenberg/issues/36834
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');

    // https://stackoverflow.com/a/74341697/278272
    wp_dequeue_style('classic-theme-styles');

    // Or, go deep: https://fullsiteediting.com/lessons/how-to-remove-default-block-styles
}

add_filter('should_load_separate_core_block_assets', '__return_true');

/**
 * Get rid of Google maps API wank styling added by the google maps shortcode plugin
 */
if (function_exists('gmaps_header')) {
    remove_action('wp_head', 'gmaps_header');
}

if (function_exists('MultipleMarkerMap_header')) {
    remove_action('wp_head', 'MultipleMarkerMap_header');
}

/**
 * Remove version
 */
add_filter('the_generator', 'brhg2016_remove_version');

function brhg2016_remove_version() {
    return '';
}

/**
 * Kill Yoast SEO dropdown filter on admin pages
 */
add_action('admin_init', 'wpse151723_remove_yoast_seo_posts_filter', 20);

function wpse151723_remove_yoast_seo_posts_filter() {
    global $wpseo_metabox;

    if ($wpseo_metabox) {
        remove_action('restrict_manage_posts', array($wpseo_metabox, 'posts_filter_dropdown'));
    }
}

/**
 *  Kill Yoast Schema
 */
add_filter('wpseo_json_ld_output', '__return_false');

/**
 * Kill eemojis
 */
add_action('init', 'brhg2025_disable_emojis');

function brhg2025_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove from TinyMCE - function below
    add_filter('tiny_mce_plugins', 'brhg2025_disable_emojis_tinymce');
}

function brhg2025_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

/**
 * Kill application password and xmlrpc
 */
add_filter('wp_is_application_passwords_available', '__return_false');
add_filter('xmlrpc_enabled', '__return_false');



/**
 * *** For Live Link only ***
 */

/* add_filter('wp_get_attachment_url', function ($url, $post_id) {
    return preg_replace('/^http:/', 'https:', $url);
}, 10, 2);

add_filter('wp_calculate_image_srcset', function ($sources) {
    foreach ($sources as &$source) {
        $source['url'] = preg_replace('/^http:/', 'https:', $source['url']);
    }
    return $sources;
});

add_filter('wp_get_attachment_image_src', function ($image) {
    if (is_array($image) && isset($image[0])) {
        $image[0] = preg_replace('/^http:/', 'https:', $image[0]);
    }
    return $image;
});

add_filter('the_content', function ($content) {
    return str_replace('http:', 'https:', $content);
}); */

// Add a bs class to make image size responsive
// This only works for new images added via tinyMCE
// functions/utility_functions.php/brhg2016_content_filter() takes care of legacy images
/* add_filter('get_image_tag_class', 'brhg2016_image_tag', 0, 4);

function brhg2016_image_tag($class, $id, $align, $size) {

    $class = $class . ' img-responsive';

    return $class;
}

add_filter('wp_get_attachment_image_attributes', function ($attr) {
    $attr['class'] .= ' img-responsive';
    return $attr;
}); */
