<?php

/**
 * Add an options page for the shop settings. 
 * 
 *  @since brhg2025a 
 */
add_action('admin_menu', 'brhg2025_publications_add_options_page', 2);

function brhg2025_publications_add_options_page() {
  // The top level page, which is not used
  add_menu_page(
    'Shop Admin',                  // Page title
    'Shop Admin',                  // Menu title
    'manage_options',              // Capability
    'shop-settings',               // Menu slug
    false,                         // Callback function
    'dashicons-money-alt',         // Icon
    80                             // Position
  );

  // Submenu page that queries and deletes personal details in publication orders
  add_submenu_page(
    'shop-settings',                             // Parent slug
    'Publication Orders GDPR',                   // Page title
    'Publication Orders GDPR',                   // Menu title
    'manage_options',                            // Capability
    'publication-orders-gdpr',                   // Menu slug
    'brhg2025_orders_gdpr_render_options_page',  // Callback function in publication_orders_gdpr.php 
  );

  // Submenu page for tallying publication sales
  add_submenu_page(
    'shop-settings',                             // Parent slug
    'Sales Stats',                               // Page title
    'Sales Stats',                               // Menu title
    'manage_options',                            // Capability
    'publication-sales-count',                   // Menu slug
    'brhg2025_sales_count_render_options_page',  // Callback function in publications_sales_count.php
  );

  // Move the Cart Orders submenu pages, and remove the top-level Cart Orders page
  remove_submenu_page('edit.php?post_type=wpsc_cart_orders', 'edit.php?post_type=wpsc_cart_orders');
  remove_submenu_page('edit.php?post_type=wpsc_cart_orders', 'post-new.php?post_type=wpsc_cart_orders');

  remove_menu_page('edit.php?post_type=wpsc_cart_orders');

  // Re-add under new parent
  add_submenu_page(
    'shop-settings',                        // New parent slug
    'Cart Orders',                          // Page title
    'Cart Orders',                          // Menu title
    'manage_options',                       // Capability
    'edit.php?post_type=wpsc_cart_orders'   // Menu slug (same as original)
  );

  add_submenu_page(
    'shop-settings',                            // New parent slug
    'Add Orders',                               // Page title
    'Add Orders',                               // Menu title
    'manage_options',                           // Capability
    'post-new.php?post_type=wpsc_cart_orders'   // Menu slug (same as original)
  );
}

/**
 * acf_form_head() is an ACF function which adds stuff to HTML head so that the form can function. 
 * Runs of current_screen because this is the first hook that can identify which admin screen is current.
 */
add_action('current_screen', 'BRHG2025_acf_form_head');

function BRHG2025_acf_form_head($screen) {
  $add_acf_forms_admin_pages = array(
    'shop-admin_page_publication-orders-gdpr',
    'shop-admin_page_publication-sales-count'
  );

  if (in_array($screen->base, $add_acf_forms_admin_pages)) {
    add_action('admin_enqueue_scripts', 'acf_form_head', 0);
  }
}
