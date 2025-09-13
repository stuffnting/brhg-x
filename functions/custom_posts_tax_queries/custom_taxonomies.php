<?php

/**************************************************************************
 * 
 * Taxonomies
 * 
 *************************************************************************/

add_action('init', 'brhg2016_register_my_taxes');


function brhg2016_register_my_taxes() {

  /** article_type **/
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
    "hierarchical"          => 0,
    'meta_box_cb'           => 'post_categories_meta_box', // Make non-hierarchical tax use category checkboxes
    "label"                 => "Article Types",
    "show_ui"               => true,
    "query_var"             => true,
    "rewrite"               => array('slug' => 'article_type', 'with_front' => false),
    "show_admin_column"     => true,
  );
  register_taxonomy("article_type", array("articles"), $args);


  /** book_type **/
  $labels = array(
    'name'                       => 'Book Types',
    'singular_name'              => 'Book Type',
    'menu_name'                  => 'Book Type',
    'all_items'                  => 'All Book Types',
    'parent_item'                => 'Parent Book Type',
    'parent_item_colon'          => 'Parent Book Type:',
    'new_item_name'              => 'New Book Type Name',
    'add_new_item'               => 'Add New Book Type',
    'edit_item'                  => 'Edit Book Type',
    'update_item'                => 'Update Book Type',
    'view_item'                  => 'View Book Type',
    'separate_items_with_commas' => 'Separate book ranges with commas',
    'add_or_remove_items'        => 'Add or remove book types',
    'choose_from_most_used'      => 'Choose from the most used',
    'popular_items'              => 'Popular Book Types',
    'search_items'               => 'Search Book Types',
    'not_found'                  => 'Not Found',
    'no_terms'                   => 'No book types',
    'items_list'                 => 'Book Types list',
    'items_list_navigation'      => 'Book Types list navigation',
  );

  $args = array(
    "labels"            => $labels,
    "hierarchical"      => 0,
    'meta_box_cb'       => 'post_categories_meta_box', // Make non-hierarchical tax use category checkboxes
    "label"             => "Book Types",
    "show_ui"           => true,
    "query_var"         => true,
    "rewrite"           => array('slug' => 'book_type', 'with_front' => false),
    "show_admin_column" => true,
  );

  register_taxonomy("book_type", array("books"), $args);


  /** linting_type **/
  $labels = array(
    'name'                       => 'Radical History Listing Types',
    'singular_name'              => 'Radical History Listing Type',
    'menu_name'                  => 'Radical History Listing Type',
    'all_items'                  => 'All Radical History Listing Types',
    'parent_item'                => 'Parent Radical History Listing Type',
    'parent_item_colon'          => 'Parent Radical History Listing Type:',
    'new_item_name'              => 'New Radical History Listing Type Name',
    'add_new_item'               => 'Add New Radical History Listing Type',
    'edit_item'                  => 'Edit Radical History Listing Type',
    'update_item'                => 'Update Radical History Listing Type',
    'view_item'                  => 'View Radical History Listing Type',
    'separate_items_with_commas' => 'Separate items with commas',
    'add_or_remove_items'        => 'Add or remove Radical History Listing Types',
    'choose_from_most_used'      => 'Choose from the most used',
    'popular_items'              => 'Popular Radical History Listing Types',
    'search_items'               => 'Search Radical History Listing Types',
    'not_found'                  => 'Not Found',
    'no_terms'                   => 'No Radical History Listing Types',
    'items_list'                 => 'Radical History Listing Types list',
    'items_list_navigation'      => 'Radical History Listing Types list navigation',
  );

  $args = array(
    "labels"            => $labels,
    "hierarchical"      => 0,
    'meta_box_cb'       => 'post_categories_meta_box', // Make non-hierarchical tax use category checkboxes
    "label"             => "Radical History Listing Types",
    "show_ui"           => true,
    "query_var"         => true,
    "rewrite"           => array('slug' => 'listing_type', 'with_front' => false),
    "show_admin_column" => true,
  );

  register_taxonomy("listing_type", array("rad_his_listings"), $args);


  /** pub_range **/
  $labels = array(
    'name'                       => 'Publication Ranges',
    'singular_name'              => 'Publication Range',
    'menu_name'                  => 'Publication Range',
    'all_items'                  => 'All Publication Ranges',
    'parent_item'                => 'Parent Publication Range',
    'parent_item_colon'          => 'Parent Publication Range:',
    'new_item_name'              => 'New Publication Range Name',
    'add_new_item'               => 'Add New Publication Range',
    'edit_item'                  => 'Edit Publication Range',
    'update_item'                => 'Update Publication Range',
    'view_item'                  => 'View Publication Range',
    'separate_items_with_commas' => 'Separate items with commas',
    'add_or_remove_items'        => 'Add or remove Publication Ranges',
    'choose_from_most_used'      => 'Choose from the most used',
    'popular_items'              => 'Popular Publication Ranges',
    'search_items'               => 'Search Publication Ranges',
    'not_found'                  => 'Not Found',
    'no_terms'                   => 'No items',
    'items_list'                 => 'Publication Ranges list',
    'items_list_navigation'      => 'Publication Ranges list navigation',
  );

  $args = array(
    "labels"            => $labels,
    "hierarchical"      => false,
    'meta_box_cb'       => 'post_categories_meta_box', // Make non-hierarchical tax use category checkboxes
    "label"             => "Publication Ranges",
    "show_ui"           => true,
    "query_var"         => true,
    "rewrite"           => array('slug' => 'pub_range', 'with_front' => false),
    "show_admin_column" => true,
    "meta_boc_cb"       => false,
    "show_in_quick_edit" => false
  );

  register_taxonomy("pub_range", array("pamphlets"), $args);

  // End cptui_register_my_taxes
}

/**
 * Remove default pub_range meta box form the editor
 */
add_action('admin_menu', 'brhg_2025_remove_pub_range_metabox');

function brhg_2025_remove_pub_range_metabox() {
  $taxonomy = 'pub_range';      // Replace with your taxonomy slug
  $post_type = 'pamphlets';     // Replace with your post type

  // Use 'tagsdiv-' prefix for non-hierarchical taxonomies (tags)
  remove_meta_box('tagsdiv-' . $taxonomy, $post_type, 'side');
}

/**
 * Remove the "Most used" tab form taxonomy meta boxes
 */

add_action('admin_head', 'remove_most_used_category_tab');

function remove_most_used_category_tab() {
  // For standard 'category' taxonomy
  echo '<style>#category-tabs .hide-if-no-js {display: none;}</style>';
  echo '<style>#article_type-tabs .hide-if-no-js {display: none;}</style>';
  echo '<style>#listing_type-tabs .hide-if-no-js {display: none;}</style>';
  echo '<style>#book_type-tabs .hide-if-no-js {display: none;}</style>';
  echo '<style>#pub_range-tabs .hide-if-no-js {display: none;}</style>';
}
