<?php

/**
 * A form which tallies publication sales.
 * 
 * The options page is added in publications_admin_pages.php
 * 
 *  @since brhg2025a 
 */


/**
 * Add the ACF form and other buttons. 
 * ACF Forme Head is required and added in publications_admin_pages.php
 */
function brhg2025_sales_count_render_options_page() {
  echo '<div class="wrap">';
  echo '<h1>Sales Stats</h1>';

  echo '<h1 id="sales-count" style="margin-top: 100px">Sales count</h1>';

  if (function_exists('acf_form')) {
    acf_form([
      'id' => 'acf-count-publication-sales',
      'post_id' => 'options',
      'field_groups' => ['group_68766706ddd7d'],
      'form' => true,
      'return' => admin_url('admin.php?page=publication-sales-count&sales_data_generated=true'),
      'submit_value' => __("Generate Sale Data", 'brhg2025'),
    ]);
  }

  // Only show results if they have just been generated
  if ($_GET['sales_data_generated'] ?? false) {
?>
    <div style="margin-top: 20px">
      <form method="post" action="<?php echo admin_url('admin-post.php?sales_data_generated=true'); ?>">
        <input type="hidden" name="action" value="generate_sales_stats_csv">
        <?php wp_nonce_field('generate_sales_stats_csv', 'generate_sales_stats_csv_nonce'); ?>
        <input type="submit" value="Sales Per Publication CSV File" id="sales-stats-csv-button" class="button">
        <label for="sales-stats-csv-button" style="margin-left: 10px">(Comma separated, Unicode (UTF-8))</label>
      </form>
    </div>
    <div style="margin-top: 20px">
      <form method="post" action="<?php echo admin_url('admin-post.php?sales_data_generated=true'); ?>">
        <input type="hidden" name="action" value="generate_sales_records_csv">
        <?php wp_nonce_field('generate_sales_records_csv', 'generate_sales_records_csv_nonce'); ?>
        <input type="submit" value="Sales Records CSV File" id="sales-records-csv-button" class="button">
        <label for="sales-records-csv-button" style="margin-left: 10px">(Comma separated, Unicode (UTF-8))</label>
      </form>
    </div>
    <div style="margin-top: 20px">
      <p><a href="<?php echo admin_url('admin.php?page=publication-sales-count'); ?>" class="button">Clear Data</a></p>
    </div>
<?php
    echo (string) brhg2025_sales_stats_results();
  }

  echo '</div>';
}

/**
 * Layout the results of the query.
 * 
 * @return string $sales_count_html
 */
function brhg2025_sales_stats_results() {

  $publication_sales_count_data = get_transient('publication_sales_count_data');

  // Guard against transient not set when page loads.
  if (is_array($publication_sales_count_data)) {
    extract($publication_sales_count_data);
  } else {
    return '';
  }

  $sales_count_html = '<h2>Sales stats results</h2>';

  /**
   * $total_sales from sales count.
   * $total_sales_check from raw trolley data
   */
  $sales_count_html .= sprintf(
    "\n<p>Sales data from %s to %s</p>\n
    <p>Total taken: £%.2f</p>\n
    <p>Total number sold: %s (individual items)</p>\n
    <p>Total number of transactions: %s (number of order posts)</p>\n
    <p>Number of different titles sold: %s</p>\n",
    $start_date,
    $end_date,
    $total_taken,
    $total_sales,
    $total_transactions,
    $number_titles_sold
  );

  $sales_count_table_html = "\n<tr>\n
                        <th>Pub. Range</th>\n
                        <th>#</th>\n
                        <th>Slug</th>\n
                        <th>Title</th>\n
                        <th>Sub-title</th>\n
                        <th>Author</th>\n
                        <th>Sold</th>\n
                        </tr>\n";

  foreach ($sales_data as $publication) {
    extract($publication);

    $sales_count_table_html .= sprintf(
      "\n<tr>\n
        <td>%s</td>\n
        <td>%s</td>\n
        <td>%s</td>\n
        <td><a href='%s'>%s</a></td>\n
        <td>%s</td>\n
        <td>%s</td>\n
        <td>%s</td>\n
      </tr>\n",
      $pub_range,
      $pub_number,
      $slug,
      $permalink,
      $title,
      $sub_title,
      $author,
      $sales_count
    );
  }

  $sales_count_html .= "<table class='widefat striped'>{$sales_count_table_html}</table>";

  return $sales_count_html;
}

/**
 * Validate the ACF form when a query is submitted.
 */
add_filter('acf/validate_value/key=field_68766ff420be0', 'brhg2025_sales_stats_validate_form', 10, 4);

function brhg2025_sales_stats_validate_form($valid, $value, $field, $input) {

  $dropdown = $value['field_687667b414151'];
  $start_date_picker = $value['field_68766ef19eb33'] ?? '';
  $end_date_picker = $value['field_68766fd320bdf'] ?? '';
  $now = date("Y-m-d");

  if ($dropdown !== 'custom') {
    return $valid;
  }

  // Should not happen because it is a required field
  if (empty($start_date_picker)) {
    return __('Start date must be set!', 'brhg2025');
  }

  // compare dates in format yyyy-mm-dd
  $start_date = DateTime::createFromFormat('Ymd', $start_date_picker)->format('Y-m-d');
  $end_date = empty($end_date_picker)
    ? $now
    : DateTime::createFromFormat('Ymd', $end_date_picker)->format('Y-m-d');

  if ($end_date == $now && $start_date > $now) {
    return __('Start date must not be in the future!.', 'brhg2025');
  }

  if ($start_date > $end_date || $start_date > $now) {
    return __('Start date must be before end date!', 'brhg2025');
  }

  if ($end_date > $now) {
    return __('End date must not be in the future!.', 'brhg2025');
  }

  return $valid;
}


/**
 * Do the query when the form is submitted.
 * 
 * the acf/save_post action hook is the most stable for this.
 */
add_action('acf/save_post', 'brhg2025_generate_sales_stats_and_records', 5);

function brhg2025_generate_sales_stats_and_records($post_id) {

  // Check we are on an options page
  if ($post_id !== 'options') {
    return;
  }

  // Check the options page contains the form
  if (
    !isset($_POST['acf']) ||
    !isset($_POST['acf']['field_68766ff420be0']) ||
    !isset($_POST['acf']['field_68766ff420be0']['field_687667b414151'])
  ) {
    return;
  }

  // Values from the form
  $dropdown = $_POST['acf']['field_68766ff420be0']['field_687667b414151'] ?? '';
  $start_picker = $_POST['acf']['field_68766ff420be0']['field_68766ef19eb33'] ?? '';
  $end_picker = $_POST['acf']['field_68766ff420be0']['field_68766fd320bdf'] ?? '';

  // Set the dates for the WP query
  $date_now_obj = new DateTime();
  $start_date = '';
  $end_date = $date_now_obj->format('Y-m-d');

  switch ($dropdown) {
    case "all":
      $start_date = '2015-01-01'; // First sales in 2015
      $end_date = $date_now_obj->format('Y-m-d');
      break;
    case "custom":
      $start_date = DateTime::createFromFormat('Ymd', $start_picker)->format('Y-m-d');
      $end_date = $end_picker
        ? DateTime::createFromFormat('Ymd', $end_picker)->format('Y-m-d')
        : $end_date;
      break;
    default:
      $end_date = $date_now_obj->format('Y-m-d'); // Has to be 1st because ->modify() mutates
      $start_date = $date_now_obj->modify($dropdown)->format('Y-m-d');
      break;
  }

  // Get all the orders between the dates specified
  $args = array(
    'post_type' => 'wpsc_cart_orders',
    'date_query' => array(
      array(
        'after' => $start_date,
        'before' => $end_date,
        'inclusive' => true,
      ),
    ),
    'numberposts' => -1,
  );

  $orders = get_posts($args);

  // Work through the orders
  $sales_count_array_by_publication = array();
  $total_money_taken = 0;
  $sales_records = array();

  foreach ($orders as $order) {

    // Use wpsc_cart_items postmeta. $trolley will contain an array, one element per item ordered.
    $trolley = get_post_meta($order->ID, 'wpsc_cart_items', true);

    // Get order details for the sales record transient & total taken
    $order_total = get_post_meta($order->ID, 'wpsc_total_amount', true);
    $order_items = get_post_meta($order->ID, 'wpspsc_items_ordered', true);

    // Get the total from the sale
    $total_money_taken = (float) $order_total + (float) $total_money_taken;

    $post_date = new DateTime($order->post_date);

    // This is for the sales records transient
    $sales_records[] = array(
      'date'  =>  $post_date->format('Y-m-d'),
      'total' => number_format($order_total, 2),
      'items' => rtrim(preg_replace('/^[\r\n]/sm', '', $order_items))
    );

    /**
     * Extract the quantity and publications 'post_name' (slug) for each item in the trolley.
     * 
     * The WPSC_Cart_Item class properties are protected, therefore, var_export() is used to 
     * turn the object into a string.
     * 
     * preg_replace is used on the tring to remove all white space including returns.
     * Then preg_match_all() is used to extract the details of each item in the oder trolley.
     */
    $trolley_string = preg_replace('/\s+/', '', var_export($trolley, true));
    $re = '/quantity.*?(\d+).*?pamphleteer\/([^\'\/]*)/m';

    /**
     *  Using PREG_SET_ORDER each element in $trolley_match is an item in the trolley, with:
     * * [0] The whole pattern match.
     * * [1] The quantity of the item ordered.
     * * [2] The items slug.
     */
    preg_match_all($re, $trolley_string, $trolley_items, PREG_SET_ORDER, 0);

    // If the slug exists in the sales array, tally the sale. If not, add the slug as a new key, and tally 1 sale.
    foreach ($trolley_items as $item) {
      if (array_key_exists($item[2], $sales_count_array_by_publication)) {
        $sales_count_array_by_publication[$item[2]] += $item[1];
      } else {
        $sales_count_array_by_publication[$item[2]] = $item[1];
      }
    }
  }

  // Sort by number sold array descending
  arsort($sales_count_array_by_publication);

  // Get details of all publications
  $args = array(
    'post_type' => 'pamphlets',
    'numberposts' => -1,
    'post_status' => 'post_status'
  );

  $all_publications = get_posts($args);

  // Match the slug in the sales count array with the publication post.
  $sales_count_array_all_data = array();

  foreach ($sales_count_array_by_publication as $slug => $sales_count) {
    foreach ($all_publications as $publication) {

      // Match slug to publication post_name 
      if ($slug === $publication->post_name) {

        // Get the postmeta to get the publication number
        $post_meta = get_post_meta($publication->ID);

        // Get authors from p2p connection
        $connected_authors = p2p_get_connections('speakers_to_pamphlets', array(
          'to' => $publication->ID
        ));

        $authors_string = '';

        foreach ($connected_authors as $key => $author) {
          $connector = count($connected_authors) == $key + 1 ? '' : ', ';
          $authors_string .= get_the_title($author->p2p_from) . $connector;
        }

        $sales_count_array_all_data[] = array(
          'ID'          => $publication->ID, // From pub post obj
          'slug'        => $publication->post_name, // From pub post obj
          'title'       => $publication->post_title, // From pub post obj
          'sub_title'   => implode($post_meta['sub_title']), // From postmeta. Returns array of a element, implode to string.
          'author'      => $authors_string, // From p2p connection
          'pub_range'   => wp_get_post_terms($publication->ID, 'pub_range')[0]->name, // From pub_range taxonomy
          'pub_number'  => implode($post_meta['pamphlet_number']), // From postmeta. Returns array of a element, implode to string.
          'sales_count' => $sales_count, // From $sales_count_array_by_publication
          'permalink'   => get_permalink($publication->ID) // From pub post obj
        );
        break;
      }
    }
  }

  $final_sales_data = array(
    'sales_data'          => $sales_count_array_all_data,
    'total_taken'         => $total_money_taken,
    'total_sales'         => array_sum($sales_count_array_by_publication),
    'total_transactions'   => count($orders),
    'start_date'          => $start_date,
    'end_date'            => $end_date,
    'number_titles_sold'  => count($sales_count_array_by_publication)
  );

  set_transient('publication_sales_count_data', $final_sales_data, 0);
  set_transient('publication_sales_records_data', $sales_records, 0);


  /**
   * EMERGENCY
   */
  //delete_transient('publication_sales_count_data');
  //delete_transient('publication_sales_records_data');
}


/**
 * Generate the sales stats CSV file.
 * 
 * Fires off the admin_post_{$action} hook, which is triggered by the form button.
 * 
 * This function must exit.
 */
add_action('admin_post_generate_sales_stats_csv', 'brhg2025_generate_sales_stats_csv');

function brhg2025_generate_sales_stats_csv() {
  // nonce set in form 
  if (check_admin_referer('generate_sales_stats_csv', 'generate_sales_stats_csv_nonce') !== 1) {
    exit;
  };

  $trans_data = get_transient('publication_sales_count_data');

  // If there is no transient data, go back to the stats page.
  if (!is_array($trans_data) || empty($trans_data)) {
    wp_redirect(admin_url('admin.php?page=publication-sales-count'));
    exit;
  }

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="brhg-sales-data.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  $output = fopen('php://output', 'w');

  extract($trans_data);

  $summary_rows = array(
    ['BRHG Publication Sales Data'],
    [],
    ['Data starts from', $start_date],
    ['Data ends on', $end_date],
    ['Total items sold', $total_sales],
    ['Total transactions', $total_transactions],
    ['Number of separate titles sold', $number_titles_sold],
    ['Total take (£)', $total_taken],
    []
  );

  foreach ($summary_rows as $row) {
    fputcsv($output, $row);
  }

  $header_row = array('Range', '#', 'Slug', 'Tile', 'Sub-title', 'Author',  'Number sold', 'URL');
  fputcsv($output, $header_row);

  foreach ($sales_data as $row_raw) {
    extract($row_raw);
    $row = array($pub_range, $pub_number, $slug, $title, $sub_title, $author, $sales_count, $permalink);
    fputcsv($output, $row);
  }
  fclose($output);
  exit;
}


/**
 * Generate the sales records CSV file.
 * 
 * Fires off the admin_post_{$action} hook, which is triggered by the form button.
 * 
 * This function must exit.
 */
add_action('admin_post_generate_sales_records_csv', 'brhg2025_generate_sales_records_csv');

function brhg2025_generate_sales_records_csv() {
  // Nonce is set in the form
  if (check_admin_referer('generate_sales_records_csv', 'generate_sales_records_csv_nonce') !== 1) {
    exit;
  };

  $trans_data = get_transient('publication_sales_records_data');

  // If there is no transient data, go back to the stats page.
  if (!is_array($trans_data) || empty($trans_data)) {
    wp_redirect(admin_url('admin.php?page=publication-sales-count'));
    exit;
  }

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="brhg-sales-records.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  $output = fopen('php://output', 'w');

  extract($trans_data);

  $header_row = array('Date', 'Amount', 'Details');
  fputcsv($output, $header_row);

  foreach ($trans_data as $row_raw) {
    extract($row_raw);
    $row = array($date, $total, $items);
    fputcsv($output, $row);
  }
  fclose($output);
  exit;
}


/**
 * Clear up the transient data.
 */
add_action('admin_init', 'brhg2025_sales_data_clear_transient');

function brhg2025_sales_data_clear_transient() {
  if (empty($_GET['sales_data_generated'])) {
    delete_transient('publication_sales_count_data');
    delete_transient('publication_sales_records_data');
  }
}
