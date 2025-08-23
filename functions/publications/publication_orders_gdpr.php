<?php

/**
 * Search for and delete personal data.
 * 
 * The options page is registered in publication_admin_pages.php.
 * AFC Form Head is also added in that file.
 * 
 * @since brhg2025a 
 */

/**
 * Options page callback to render a page containing the ACF form.
 * The form needs ACF Form Head which is added in publication_admin_pages.php.
 */
function brhg2025_orders_gdpr_render_options_page() {
  echo '<div class="wrap">';
  echo '<h1>Process Sales GDPR</h1>';

  // Show ACF fields using acf_form() if needed
  if (function_exists('acf_form')) {
    acf_form([
      'id' => 'acf-order-gdpr-query',
      'post_id' => 'options',
      'field_groups' => ['group_6873ad673325e'],
      'form' => true,
      'return' => admin_url('admin.php?page=publication-orders-gdpr#sales-count'),
      'submit_value' => __("Submit", 'brhg2025'),
    ]);
  }

  echo (string) brhg2025_orders_gdpr_results();

  echo '</div>';
}


/**
 * Layout the results of the query and processing of personal data. 
 * 
 * @return string $orders_processed_html_out
 */
function brhg2025_orders_gdpr_results() {
  $last_query = get_transient('last-orders-gdpr-query');
  $last_processed = get_transient('last-orders-gdpr-processed') ?? array();

  $were_ids_to_process_found = isset($last_query['query_range_orders_to_process_ids']) &&
    is_array($last_query['query_range_orders_to_process_ids']) &&
    !empty($last_query['query_range_orders_to_process_ids'])
    ? true
    : false;

  $ids_to_process = $were_ids_to_process_found
    ? implode(', ', $last_query['query_range_orders_to_process_ids'])
    : '';

  $orders_processed_html_out = '<h2 style="margin-top: 100px">Last Query</h2>';

  $total_orders =   wp_count_posts('wpsc_cart_orders');

  if (!$last_query) {
    $orders_processed_html_out .=  "<p>No data yet. Do a new query.</p>";
  } else {
    $orders_processed_html_out .= sprintf(
      "\n<p>Query date: %s</p>\n
      <p><strong>*** %s ***</strong></p>\n
      <p>Total number of orders: %s</p>\n
      <p>Orders found in query range (excluding last %s): %s</p>\n
      <p>Orders found after query range: %s</p>\n
      <p>Number of unprocessed orders in query range: %s</p>\n
      <p>Date of 1st unprocessed order: %s</p>\n
      <p>Date of last unprocessed order: %s</p>\n
      <p>Unprocessed orders IDS: %s</p>\n",
      $last_query['date'] ?? 'Not recorded',
      (isset($last_query['last_query_processed']) && (bool) $last_query['last_query_processed'] === true)
        ? 'This query was processed'
        : (($last_query['query_range_orders_to_process'] == 0)
          ? 'No orders to process'
          : 'This query was not processed'),
      $total_orders->publish ?? 'None',
      $last_query['weeks_to_keep'] ?? 'None recorded',
      $last_query['query_range_orders_total_orders'] ?? 0,
      $total_orders->publish - $last_query['query_range_orders_total_orders'],
      $last_query['query_range_orders_to_process'] ?? 0,
      $were_ids_to_process_found ? get_the_date('Y-m-d', end($last_query['query_range_orders_to_process_ids'])) : '-',
      $were_ids_to_process_found ? get_the_date('Y-m-d', $last_query['query_range_orders_to_process_ids'][0]) : '-',
      $were_ids_to_process_found ? $ids_to_process : 'None'
    );
  }

  $orders_processed_html_out .= '<h2 style="margin-top: 30px">Last Orders Processed</h2>';
  $orders_processed_html_out .= '<p>Including last processed query.</p>';

  if (!$last_query) {
    $orders_processed_html_out .= "<p>Process query first.</p>";
  } else {
    $orders_processed_html_out .= sprintf(
      "\n<p>Last orders processed on: %s</p>\n
    <p>Number of orders processed to date: %s</p>\n
    <p>Number of orders processed last time: %s</p>\n
    <p>Last order processed: ID %s, ordered on %s</p>\n",
      empty($last_processed['date_last_processed']) ? 'Unknown' : $last_processed['date_last_processed'],
      $last_processed['total_number_processed_orders'] ?? 0,
      $last_processed['number_processed_last_time'] ?? 'unknown',
      $last_processed['last_order_processed_id'] ?? 0,
      empty($last_processed['last_order_processed_id'])
        ? 'Unknown'
        : get_the_date('Y-m-d', $last_processed['last_order_processed_id'])
    );
  }

  return $orders_processed_html_out;
}

/**
 * Do stuff when the form is submitted.
 * 
 * Queries the data range, deletes metadata when required and sets
 * transient data.
 * 
 * acf/save_posts runs before, or after the $_POST data is saved.
 * *** NOTE *** Priority 5 means this runs before the form data is saved.
 * 
 * @param array $form Data from the submitted form
 */
add_action('acf/save_post', 'brhg2025_orders_gdpr_process_orders', 5);

function brhg2025_orders_gdpr_process_orders($post_id) {

  // Check we are on an options page
  if ($post_id !== 'options') {
    return;
  }

  // Check the options page contains the form
  if (
    !isset($_POST['acf']) || !isset($_POST['acf']['field_6873b73a7fe98'])
  ) {
    return;
  }

  $form_data_raw = $_POST['acf']['field_6873b73a7fe98'];

  $form_data = array(
    'order_gdpr_weeks_keep' => $form_data_raw['field_6874c0625132d'],
    'process_gdpr_details' => $form_data_raw['field_6873b806f83a5']
  );

  if (
    !$form_data ||
    !isset($form_data['order_gdpr_weeks_keep']) ||
    !isset($form_data['process_gdpr_details'])
  ) {
    return;
  }

  $end_date = new DateTime();
  // Modify end date e.g. "-3 weeks"
  $modify_string = "-{$form_data['order_gdpr_weeks_keep']} weeks";
  $end_date->modify($modify_string);

  // Query the orders in date range: all orders before order_gdpr_weeks_keep
  $args_orders = array(
    'post_type' => 'wpsc_cart_orders',
    'date_query' => array(
      array(
        'before' => $end_date->format('Y-m-d'),
        'inclusive' => true,
      ),
    ),
    'numberposts' => -1,
    'fields' => 'ids'
  );

  $orders_in_query_range = get_posts($args_orders);

  // Query all orders processed to date
  $args_processed_orders = [
    'post_type'     => 'wpsc_cart_orders',
    'meta_query'    => [
      [
        'key'     => 'order_gdpr_processed',
        'compare' => 'EXISTS',
      ],
    ],
    'posts_per_page' => -1,
    'fields' => 'ids',
  ];

  $all_processed_orders = get_posts($args_processed_orders);

  // All orders that are unprocessed in the query date range
  $orders_to_process = array_diff($orders_in_query_range, $all_processed_orders);

  // all orders found are processed, even if they have previously been processed, to make sure there are none left stranded
  if (!empty($orders_in_query_range)) {
    if ((bool) $form_data['process_gdpr_details']) {
      // Order meta fields to delete
      $fields_to_delete = array(
        'wpsc_first_name',
        'wpsc_last_name',
        'wpsc_email_address',
        'wpsc_ipaddress',
        'wpsc_address',
        'wpspsc_phone',
        'wpsc_buyer_email_sent'
      );

      foreach ($orders_to_process as $order_id) {
        foreach ($fields_to_delete as $delete_field) {
          // Delete personal details meta
          update_post_meta($order_id, $delete_field, '');
        }
        // Mark as processed
        update_post_meta($order_id, 'order_gdpr_processed', true);
      }

      // Reset process_gdpr_details for safety
      $_POST['acf']['field_6873b73a7fe98']['field_6873b806f83a5'] = 0;
    }
  }

  // Transient data
  $last_query_trans_data = array(
    'date' =>  date('Y-m-d H:i:s'),
    'last_query_processed' => (bool) $form_data['process_gdpr_details'] === true ? true : false,
    'weeks_to_keep' => isset($form_data['order_gdpr_weeks_keep'])
      ? "{$form_data['order_gdpr_weeks_keep']} weeks"
      : 'Unknown',
    'query_range_orders_total_orders' => is_array($orders_in_query_range) ? count($orders_in_query_range)  : 0,
    'query_range_orders_to_process' => is_array($orders_to_process) ? count($orders_to_process) : 0,
    'query_range_orders_to_process_ids' => is_array($orders_to_process) &&
      !empty($orders_to_process)
      ? $orders_to_process
      : array()
  );


  if ((bool) $form_data['process_gdpr_details']) {

    // If orders were processed:
    $all_processed_orders_updated = $orders_in_query_range;

    $processed_orders_trans_data = array(
      'date_last_processed' => date('Y-m-d H:i:s'),
      'total_number_processed_orders' => is_array($all_processed_orders_updated) ? count($all_processed_orders_updated) : 0,
      'number_processed_last_time' => is_array($orders_to_process) ? count($orders_to_process) : 0,
      'last_order_processed_id' => is_array($all_processed_orders_updated) ? $all_processed_orders_updated[0] : 'Unknown',
    );

    set_transient('last-orders-gdpr-processed', $processed_orders_trans_data);
  }

  set_transient('last-orders-gdpr-query', $last_query_trans_data);

  /**
   * EMERGENCY!
   */
  //delete_transient('last-orders-gdpr-processed');
  //delete_transient('last-orders-gdpr-query');
}
