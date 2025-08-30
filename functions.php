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
// Custom Post Types and Taxonomies - includes brhg2016_pub_range_meta_cb() dropdown list for edit screens
require_once('functions/custom_posts_tax_queries/custom_post_tax.php');
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
    // Help Page
    require_once('functions/admin/brhg2016_help.php');
    // Simply Show IDs
    require_once('functions/admin/simply-show-ids.php');
    // Extra admin page columns and Quick Edit - includes in_slider Meta Box and pub_range dropdown list
    require_once('functions/admin/admin_columns.php');
    // Controls the left-hand admin menu
    require_once('functions/admin/admin_menu.php');
    // Controls the left-hand admin menu
    require_once('functions/admin/admin_more_functions.php');
    // Shop settings: Delete personal data, sales stats
    require_once('functions/publications/publications_admin_pages.php');
    require_once('functions/publications/publication_orders_gdpr.php');
    require_once('functions/publications/publications_sales_count.php');
}

/**
 * Not in Admin (front only)
 */
if (! is_admin()) {
    // Shortcode for the event-series event-list
    require_once('functions/appearance/event_list_shortcode.php');
    // Short codes for displaying the contact details from the BRHG Details options page
    require_once('functions/appearance/contact_details_shortcodes.php');
    // Short codes for the footnotes
    require_once('functions/appearance/footnotes_shortcode.php');
    // Donate button shortcode
    require_once('functions/appearance/donate_button_shortcode.php');
    // Schema
    require_once('functions/utilities/schema.php');
    // Inline scripts
    require_once('functions/utilities/inline_scripts.php');
    // Content filters for the single.php template file
    require_once('functions/appearance/the_content_filter.php');
    // Content filter for single publications page
    require_once('functions/publications/publication_the_content_filter.php');
    //Comments and comment form
    require_once('functions/appearance/comment_form.php');
    // Front page about and featured sections
    require_once('functions/appearance/front_page_about_featured.php');
}

// Max theme embed width
//If you change this see images in brhg2016_setup()
if (! isset($content_width)) {
    $content_width = 780;
}

/**
 *
 * Setup function
 *
 */
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

    /*
   * Add custom post types to RSS feed
   */
    function brhfeed_request($qv) {
        if (isset($qv['feed']) && !isset($qv['post_type']))
            $qv['post_type'] = array('post', 'events', 'books', 'articles', 'pamphlets', 'rad_his_listings');
        return $qv;
    }
    add_filter('request', 'brhfeed_request');

    /*
    * images
    */
    add_image_size('big_thumb', 332, 470, true); //for pamphleteer page and single listing and event listings
    add_image_size('listing_thumbnails', 100, 150, false);  //thumbnailsfor listing pages (not event series)
    add_image_size('2_or_3_across_crop', 270, 200, true);  //2 accross page width for 2 column, or 3 accross for full width page cropped for height
    add_image_size('2_or_3_across_no_crop', 270, 9999, false);  //2 accross page width for 2 column, or 3 accross for full width page not cropped for height
    add_image_size('medium_thumbs', 150, 150, false);
    add_image_size('tiny_thumbs', 50, 80, true); // for home page recent books, articles and admin list thumbnails 

    /*
    * Menus
    */
    register_nav_menus(array(
        'scroll_info_menu' => __('Scroll Info Menu', 'brhg2016'),
        'main_content_menu' => __('Main Content Menu', 'brhg2016'),
        'mobile_menu' => __('Mobile Menu', 'brhg2016'),
        'footer_menu' => __('Footer Menu', 'brhg2016'),
    ));
}
add_action('after_setup_theme', 'brhg2016_setup');

/**
 *
 * Enqueue
 *
 */

// Get rid of Google maps API wank styling added by the google maps shortcode plugin
if (function_exists('gmaps_header')) {
    remove_action('wp_head', 'gmaps_header');
}

if (function_exists('MultipleMarkerMap_header')) {
    remove_action('wp_head', 'MultipleMarkerMap_header');
}

//add stylesheets and scripts
function brhg2016_theme_style_scripts() {
    //add jquery
    wp_enqueue_script('jquery');
    //add the style sheet
    wp_enqueue_style(
        'main-style',
        get_stylesheet_uri(),
        array(),
        filemtime(get_template_directory() . '/style.css')
    );

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

add_action('wp_enqueue_scripts', 'brhg2016_theme_style_scripts');

// Stop jQuery Migrate loading by removing dependency from jQuery
function BRHG2024_dequeue_jquery_migrate($scripts) {
    if (! is_admin() && ! empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        );
    }
}

add_action('wp_default_scripts', 'BRHG2024_dequeue_jquery_migrate');

/**
 * Kill Gutenberg
 */

remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

add_action('wp_enqueue_scripts', function () {
    // https://github.com/WordPress/gutenberg/issues/36834
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');

    // https://stackoverflow.com/a/74341697/278272
    wp_dequeue_style('classic-theme-styles');

    // Or, go deep: https://fullsiteediting.com/lessons/how-to-remove-default-block-styles
});

add_filter('should_load_separate_core_block_assets', '__return_true');

// Dequeue unwanted stuff
function brhg_remove_unwanted_css() {
    // Cookie banner, now  in main css
    wp_dequeue_style('cookie-law-info-gdpr');
    wp_dequeue_style('cookie-law-info');
    wp_dequeue_style('wpsc-style');
}

add_action('wp_enqueue_scripts', 'brhg_remove_unwanted_css', 999);

// Add admin stylesheets and scripts
function brhg2016_admin_theme_style_scripts() {
    /*     wp_enqueue_style(
        'brhg2016-admin-theme',
        get_template_directory_uri() . '/css/brhg2016-admin.css',
        false,
        filemtime(get_template_directory() . '/css/brhg2016-admin.css')
    ); */
    // Contains the quick edit js
    wp_enqueue_script(
        'admin-extra-script',
        get_template_directory_uri() . '/js/admin-extra.js',
        array('jquery'),
        filemtime(get_template_directory() . '/js/admin-extra.js'),
        true
    );
}

add_action('admin_enqueue_scripts', 'brhg2016_admin_theme_style_scripts');
add_action('login_enqueue_scripts', 'brhg2016_admin_theme_style_scripts');

/**
 * Preload
 */
//add_filter('wp_preload_resources', 'brhg2024_preload');

function brhg2024_preload($resources) {
    if (! is_array($resources)) {
        $resources = array();
    }

    // Fonts
    $fonts = array(
        //'essays1743-bold-webfont.woff2',
        //'essays1743-italic-webfont.woff',
        'essays1743-webfont.woff2',
        'notosans-regular-webfont.woff2',
        'notosans-bold-webfont.woff2',
        'notosans-italic-webfont.woff2',
        'notosans-bolditalic-webfont.woff2'
    );

    foreach ($fonts as $font) {
        $resources[] = array(
            'href'          => get_template_directory_uri() . '/fonts/' . $font,
            'as'            => 'font',
            'type'          => 'font/woff2',
            'crossorigin'   => '' // needed for fonts even though same origin
        );
    }

    // Images
    $main_bg_images = array(
        'angel.svg',
        'skeleton.svg',
        'scroll-left.svg',
        'scroll-middle.svg',
        'scroll-right.svg',
        'wavy-line.svg',
        'header-oxo.svg',
        'search.svg',
        'hamburger.svg',
        'menu-bottom-line.svg',
        'cherub-left.svg',
        'cherub-middle.svg',
        'cherub-right.svg',
        'q-white.svg',
        //'q.svg',
        //'brhg-missing.svg',
        'trolley.svg'
    );

    $fp_bg_images = array(
        'headline-frame-top-scroll.svg',
        'headline-frame-top.svg',
        'headline-frame-middle.svg',
        'headline-frame-bottom.svg',
        'headline-frame-bottom-scroll.svg',
        'slider-frame-top.svg',
        'slider-frame-bottom.svg',
        'slider-frame-middle.svg',
    );

    if (is_front_page()) {
        $bg_images = array_merge($main_bg_images, $fp_bg_images);
    } else {
        $bg_images = $main_bg_images;
    }

    // Mobiles use a png file for front page logo
    if (is_front_page()) {
        $fp_logo = brhg2024_check_phone()  ? 'full-logo-small-2024.png' : 'full-logo-small-2024.svg';
        // Logo is LCP, and added to the front of array
        array_unshift($bg_images, $fp_logo);
    }

    foreach ($bg_images as $image) {
        $resources[] = array(
            'href'          => get_template_directory_uri() . '/images/' . $image,
            'as'            => 'image',
            'type'          => 'image/svg+xml'
        );
    }

    return $resources;
}

// Favicon
function brhg2016_favicon() {
    echo '<link rel="Shortcut Icon" type="image/x-icon" href="' . get_stylesheet_directory_uri() . '/images/favicon.ico" />';
}
add_action('admin_head', 'brhg2016_favicon');
add_action('login_head', 'brhg2016_favicon');
add_action('wp_head', 'brhg2016_favicon');

/*
* Extras
*
*/

// Remove version
function brhg2016_remove_version() {
    return '';
}

add_filter('the_generator', 'brhg2016_remove_version');

// Add custom image sizes to media uploader
function brhg2016_insert_custom_image_sizes($sizes) {
    global $_wp_additional_image_sizes;

    if (empty($_wp_additional_image_sizes)) {
        return $sizes;
    }
    foreach ($_wp_additional_image_sizes as $id => $data) {
        if (!isset($sizes[$id])) {
            $sizes[$id] = ucfirst(str_replace('-', ' ', $id));
        }
    }
    return $sizes;
}

add_filter('image_size_names_choose', 'brhg2016_insert_custom_image_sizes');

// Add a bs class to make image size responsive
// This only works for new images added via tinyMCE
// functions/utility_functions.php/brhg2016_content_filter() takes care of legacy images
function brhg2016_image_tag($class, $id, $align, $size) {

    $class = $class . ' img-responsive';

    return $class;
}

add_filter('get_image_tag_class', 'brhg2016_image_tag', 0, 4);


add_filter('wp_get_attachment_image_attributes', function ($attr) {
    $attr['class'] .= ' img-responsive';
    return $attr;
});

// Kill Yoast SEO dropdown filter on admin pages

add_action('admin_init', 'wpse151723_remove_yoast_seo_posts_filter', 20);

function wpse151723_remove_yoast_seo_posts_filter() {
    global $wpseo_metabox;

    if ($wpseo_metabox) {
        remove_action('restrict_manage_posts', array($wpseo_metabox, 'posts_filter_dropdown'));
    }
}

// Kill Yoast Schema

add_filter('wpseo_json_ld_output', '__return_false');


// Wrap embed iframe
function brhg_wrap_embed_with_div($html, $url, $attr) {
    return '<div class="iframe-container">' . $html . '</div>';
}

add_filter('embed_oembed_html', 'brhg_wrap_embed_with_div', 10, 3);

// Kill emojis

add_action('init', 'my_disable_emojis');

function my_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove from TinyMCE - function below
    add_filter('tiny_mce_plugins', 'my_disable_emojis_tinymce');
}

function my_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

// Kill application password and xmlrpc

add_filter('wp_is_application_passwords_available', '__return_false');
add_filter('xmlrpc_enabled', '__return_false');
