<?php
/*
*
* Custom post types
*
* Includes custom callback functions for admin pages
*
*/

add_action('init', 'brhg2016_register_my_cpts');

function brhg2016_register_my_cpts() {
    /** venues **/
    $labels = array(
        'name'                  => 'Venues',
        'singular_name'         => 'Venue',
        'menu_name'             => 'Venues',
        'name_admin_bar'        => 'Venue',
        'archives'              => 'Venue Archives',
        'attributes'            => 'Venue Attributes',
        'parent_item_colon'     => 'Parent Venue:',
        'all_items'             => 'All Venues',
        'add_new_item'          => 'Add New Venue',
        'add_new'               => 'Add New',
        'new_item'              => 'New Venue',
        'edit_item'             => 'Edit Venue',
        'update_item'           => 'Update Venue',
        'view_item'             => 'View Venue',
        'view_items'            => 'View Venues',
        'search_items'          => 'Search Venue',
        'insert_into_item'      => 'Insert into venue',
        'uploaded_to_this_item' => 'Uploaded to this venue',
        'items_list'            => 'Venues list',
        'items_list_navigation' => 'Venues list navigation',
        'filter_items_list'     => 'Filter venue list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "Details of event venues",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => false,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => true,
        "query_var"             => true,
        "supports"              => array("title", "editor", "custom-fields", "revisions", "thumbnail", "author"),
    );

    register_post_type("venues", $args);

    /** event_series **/
    $labels = array(
        'name'                  => 'Event Series',
        'singular_name'         => 'Events Series',
        'menu_name'             => 'Event Series',
        'name_admin_bar'        => 'Events Series',
        'archives'              => 'Event Series Archives',
        'attributes'            => 'Event Series Attributes',
        'parent_item_colon'     => 'Parent Event Series:',
        'all_items'             => 'All Event Series',
        'add_new_item'          => 'Add New Event Series',
        'add_new'               => 'Add New',
        'new_item'              => 'New Event Series',
        'edit_item'             => 'Edit Event Series',
        'update_item'           => 'Update Event Series',
        'view_item'             => 'View Event Series',
        'view_items'            => 'View Event Series',
        'search_items'          => 'Search Event Series',
        'insert_into_item'      => 'Insert into event series',
        'uploaded_to_this_item' => 'Uploaded to this event series',
        'items_list'            => 'Event Series list',
        'items_list_navigation' => 'Event Series list navigation',
        'filter_items_list'     => 'Filter event series list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "Details for a group of events or an event series.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "event-series", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "revisions", "thumbnail", "author", "page-attributes"),
    );

    register_post_type("event_series", $args);

    /** articles **/
    $labels = array(
        'name'                  => 'Articles',
        'singular_name'         => 'Article',
        'menu_name'             => 'Articles',
        'name_admin_bar'        => 'Article',
        'archives'              => 'Article Archives',
        'attributes'            => 'Article Attributes',
        'parent_item_colon'     => 'Parent Article:',
        'all_items'             => 'All Articles',
        'add_new_item'          => 'Add New Article',
        'add_new'               => 'Add New',
        'new_item'              => 'New Article',
        'edit_item'             => 'Edit Article',
        'update_item'           => 'Update Article',
        'view_item'             => 'View Article',
        'view_items'            => 'View Articles',
        'search_items'          => 'Search Article',
        'insert_into_item'      => 'Insert into article',
        'uploaded_to_this_item' => 'Uploaded to this article',
        'items_list'            => 'Articles list',
        'items_list_navigation' => 'Articles list navigation',
        'filter_items_list'     => 'Filter articles list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "Articles, galleries and videos etc.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "articles", "with_front" => true),
        "query_var"             => true,
        "supports" => array("title", "editor", "excerpt", "trackbacks", "comments", "revisions", "thumbnail", "author", "page-attributes"),
        "taxonomies" => array("category", "post_tag")
    );

    register_post_type("articles", $args);


    /** rad_his_listings **/
    $labels = array(
        'name'                  => 'Radical History Listings',
        'singular_name'         => 'Radical History Listing',
        'menu_name'             => 'Radical History Listings',
        'name_admin_bar'        => 'Radical History Listing',
        'archives'              => 'Radical History Listing Archives',
        'attributes'            => 'Radical History Listing Attributes',
        'parent_item_colon'     => 'Parent Listing',
        'all_items'             => 'All Listing',
        'add_new_item'          => 'Add New Listing',
        'add_new'               => 'Add New',
        'new_item'              => 'New Listing',
        'edit_item'             => 'Edit Listing',
        'update_item'           => 'Update Listing',
        'view_item'             => 'View Listing',
        'view_items'            => 'View Listings',
        'search_items'          => 'Search Radical History Listing',
        'insert_into_item'      => 'Insert into listing',
        'uploaded_to_this_item' => 'Uploaded to this linsting',
        'items_list'            => 'Listings list',
        'items_list_navigation' => 'Listings list navigation',
        'filter_items_list'     => 'Filter listings list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "A collection of links to things out side of this site that may be of interest.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => false,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "brhg-listings", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "revisions", "thumbnail", "author"),
        "taxonomies"            => array("category", "post_tag")
    );

    register_post_type("rad_his_listings", $args);

    /** Events **/
    $labels = array(
        'name'                  => 'Events',
        'singular_name'         => 'Event',
        'menu_name'             => 'Events',
        'name_admin_bar'        => 'Event',
        'archives'              => 'Event Archives',
        'attributes'            => 'Event Attributes',
        'parent_item_colon'     => 'Parent Event:',
        'all_items'             => 'All Events',
        'add_new_item'          => 'Add New Event',
        'add_new'               => 'Add New Event',
        'new_item'              => 'New Event',
        'edit_item'             => 'Edit Event',
        'update_item'           => 'Update Event',
        'view_item'             => 'View Event',
        'view_items'            => 'View Events',
        'search_items'          => 'Search Event',
        'insert_into_item'      => 'Insert into Event',
        'uploaded_to_this_item' => 'Uploaded to this Event',
        'items_list'            => 'Events list',
        'items_list_navigation' => 'Events list navigation',
        'filter_items_list'     => 'Filter events list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "Bristol Radical History Group and other peoples events.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "events", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "comments", "thumbnail", "author"),
        "taxonomies"            => array("category", "post_tag"),
        'show_in_rest'          => true
    );

    register_post_type("events", $args);

    /** pamphlets **/
    $labels = array(
        'name'                  => 'Publications',
        'singular_name'         => 'Publication',
        'menu_name'             => 'Publications',
        'name_admin_bar'        => 'Publication',
        'archives'              => 'Publication Archives',
        'attributes'            => 'Publication Attributes',
        'parent_item_colon'     => 'Parent Publication:',
        'all_items'             => 'All Publications',
        'add_new_item'          => 'Add New Publication',
        'add_new'               => 'Add New',
        'new_item'              => 'New Publication',
        'edit_item'             => 'Edit Publication',
        'update_item'           => 'Update Publication',
        'view_item'             => 'View Publication',
        'view_items'            => 'View Publications',
        'search_items'          => 'Search Publication',
        'insert_into_item'      => 'Insert into publication',
        'uploaded_to_this_item' => 'Uploaded to this publication',
        'items_list'            => 'Publications list',
        'items_list_navigation' => 'Publications list navigation',
        'filter_items_list'     => 'Filter publications list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "List BRHG Radical Pamphleteer range.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "pamphleteer", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "comments", "revisions", "thumbnail", "author"),
        "taxonomies"            => array("category", "post_tag"),
        'show_in_rest'          => true
    );

    register_post_type("pamphlets", $args);

    /** books **/
    $labels = array(
        'name'                  => 'Book Reviews',
        'singular_name'         => 'Book Review',
        'menu_name'             => 'Book Reviews',
        'name_admin_bar'        => 'Book Review',
        'archives'              => 'Book Review Archives',
        'attributes'            => 'Book Review Attributes',
        'parent_item_colon'     => 'Parent Book Review:',
        'all_items'             => 'All Book Reviews',
        'add_new_item'          => 'Add New Book Review',
        'add_new'               => 'Add New',
        'new_item'              => 'New Book Review',
        'edit_item'             => 'Edit Book Review',
        'update_item'           => 'Update Book Review',
        'view_item'             => 'View Book Review',
        'view_items'            => 'View Book Reviews',
        'search_items'          => 'Search Book Review',
        'insert_into_item'      => 'Insert into book review',
        'uploaded_to_this_item' => 'Uploaded to this book review',
        'items_list'            => 'Book Reviews list',
        'items_list_navigation' => 'Book Reviews list navigation',
        'filter_items_list'     => 'Filter book reviews list',
    );

    $args = array(
        "labels"                => $labels,
        "description"           => "",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "book-reviews", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "comments", "revisions", "thumbnail", "author"),
        "taxonomies"            => array("category", "post_tag")
    );

    register_post_type("books", $args);

    /** project **/
    $labels = array(
        'name'                  => 'Project',
        'singular_name'         => 'Post Project',
        'menu_name'             => 'Project',
        'name_admin_bar'        => 'Post Project',
        'archives'              => 'Project Archives',
        'attributes'            => 'Project Attributes',
        'parent_item_colon'     => 'Parent Project:',
        'all_items'             => 'All Projects',
        'add_new_item'          => 'Add New Project',
        'add_new'               => 'Add New',
        'new_item'              => 'New Project',
        'edit_item'             => 'Edit Project',
        'update_item'           => 'Update Project',
        'view_item'             => 'View Project',
        'view_items'            => 'View Projects',
        'search_items'          => 'Search Project',
        'insert_into_item'      => 'Insert into project',
        'uploaded_to_this_item' => 'Uploaded to this project',
        'items_list'            => 'Projects list',
        'items_list_navigation' => 'Projects list navigation',
        'filter_items_list'     => 'Filter projects list',
    );

    $args = array(
        "labels" => $labels,
        "description"           => "Use for combining different content types into projects.",
        "public"                => true,
        "show_ui"               => true,
        "has_archive"           => true,
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => array("slug" => "project", "with_front" => true),
        "query_var"             => true,
        "supports"              => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "comments", "revisions", "thumbnail", "author", "page-attributes"),
        "taxonomies"            => array("category", "post_tag")
    );

    register_post_type("project", $args);

    /** contributors **/
    $labels = array(
        'name'                  => 'Contributors',
        'singular_name'         => 'Contributor',
        'menu_name'             => 'Contributors',
        'name_admin_bar'        => 'Contributor',
        'archives'              => 'Contributor Archives',
        'attributes'            => 'Contributor Attributes',
        'parent_item_colon'     => 'Parent Contributor:',
        'all_items'             => 'All Contributors',
        'add_new_item'          => 'Add New Contributor',
        'add_new'               => 'Add New',
        'new_item'              => 'New Contributor',
        'edit_item'             => 'Edit Contributor',
        'update_item'           => 'Update Contributor',
        'view_item'             => 'View Contributor',
        'view_items'            => 'View Contributors',
        'search_items'          => 'Search Contributor',
        'insert_into_item'      => 'Insert into contributor',
        'uploaded_to_this_item' => 'Uploaded to this contributor',
        'items_list'            => 'Contributors list',
        'items_list_navigation' => 'Contributors list navigation',
        'filter_items_list'     => 'Filter contributors list',
    );

    $args = array(
        "labels" => $labels,
        "description"           => "The biographies of the contributors to BRHG events.",
        "public"                => true,
        "show_ui"               => true,
        // Contributors archive redirects to contrib_alpha taxonomy page
        "has_archive"           => true,
        "rewrite"               => array("slug" => "contributors", "with_front" => true),
        "show_in_menu"          => true,
        "exclude_from_search"   => false,
        "capability_type"       => "post",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "supports" => array("title", "editor", "excerpt", "trackbacks", "custom-fields", "revisions", "thumbnail", "author", "page-attributes"),
    );

    register_post_type("contributors", $args);

    // End of cptui_register_my_cpts()
}

/**
 * Remove page attribute meta box from hierarchical custom post types and pages
 */
add_action('init', 'brhg2024_remove_post_type_attribute_support');
function brhg2024_remove_post_type_attribute_support() {
    remove_post_type_support('page', 'page-attributes');
    remove_post_type_support('articles', 'page-attributes');
    remove_post_type_support('event_series', 'page-attributes');
    remove_post_type_support('project', 'page-attributes');
    remove_post_type_support('contributors', 'page-attributes');
}

/**
 * Add custom post types to RSS feed
 */
add_filter('request', 'brhg2016_rss_add_custom_post_types');

function brhg2016_rss_add_custom_post_types($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = array('post', 'events', 'books', 'articles', 'pamphlets', 'rad_his_listings');
    return $qv;
}
