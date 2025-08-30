<?php

/**
 * Controls for:
 * - Publications prices page
 * - Publication Collections page 
 * 
 * @since 2024
 */

// The ACF field for the publication number on the post edit page.
$pub_num_field = 'field_4f834448a7039';

/**
 * Path to the CSV file
 * 
 * Character set: Unicode (UTF-8)
 * Separator: comma
 * 
 * Folder: 0755
 * File: 0644
 */
$csv_file_path =  ABSPATH . 'downloads/publication-prices/brhg-publications-price-list.csv';




/************************************************************************************************************************************
 *
 * Short codes
 *
 * for-sale-list
 * brhg-publications-price-list
 * 
 *************************************************************************************************************************************/


/**
 * Shortcode that generates the 'for sale on this website' list on the where-to-buy-brhg-publications page.
 * 
 * @return string HTML with the publication range titles and the list of available publications.
 */

add_shortcode('for-sale-list', 'brhg2024_make_for_sale_list');

function brhg2024_make_for_sale_list() {
  $shop_closed_message = __('<p>We are not currently selling any pamphlets from this website.</p>');

  // Check shop is open.
  if (!get_field('shop_open', 'options')) {
    return $shop_closed_message;
  }

  $list = '';

  // Outer publication range repeater
  if (have_rows('publication_range_repeater', 'options')):

    // Loop through range rows.
    while (have_rows('publication_range_repeater', 'options')) : the_row();

      $range_obj = get_term(get_sub_field('publication_range_name'), 'pub_range');

      $list_items = '';

      // Inner publication repeater
      if (have_rows('publication_range_item_repeater')):

        // loop through the publication rows
        while (have_rows('publication_range_item_repeater')) : the_row();

          // Current publication not for sale
          if (!get_sub_field('for_sale')) {
            continue;
          }


          $list_items .= sprintf(
            "<li><a href='%s'>#%s %s</a></li>",
            get_the_permalink(get_sub_field('publication')),
            get_sub_field('range_number'),
            get_the_title(get_sub_field('publication'))
          );

        endwhile; // End loop publication repeater

      endif; // End if publication repeater        

      // Nothing for sale in the current range
      if (empty($list_items)) {
        continue;
      }

      $list .= "<p class='pub-for-sale-range-title'>{$range_obj->name}</p>\n<ul class='pub-for-sale-range-list q-list'>$list_items</ul>\n";

    endwhile; // End loop range repeater

  endif; // End if range repeater

  // Shop is open, but nothing for sale
  if (empty($list)) {
    return $shop_closed_message;
  }

  return $list;
}

/**
 * Shortcode that generates the BRHG publication prices list.
 */

add_shortcode('brhg-publications-price-list', 'brhg2024_make_publications_price_list');

function brhg2024_make_publications_price_list() {

  $range_array = get_transient("brhg_publications_price_list");

  //  If the transient does not exist, does not have a value, or has expired, then get_transient will return false.
  if (!$range_array) {
    return;
  }

  $list_html = '';

  $table_headers = array(
    array('header' => '#', 'tooltip' => 'Publication number'),
    array('header' => 'Title', 'tooltip' => 'Publication title'),
    array('header' => 'ISBN', 'tooltip' => 'International Standard Book Number'),
    array('header' => 'Edition', 'tooltip' => 'Details of the current edition'),
    array('header' => 'PP', 'tooltip' => 'Number of printed pages in the publication'),
    array('header' => 'PI', 'tooltip' => 'Number of printed images&sol;illustrations in the publication'),
    array('header' => 'Format', 'tooltip' => 'Publication&apos;s physical format'),
    array('header' => 'RRP', 'tooltip' => 'Recommended Retail Price'),
    array('header' => 'Notes', 'tooltip' => 'Further details'),
  );

  $table_headers_html = '';

  foreach ($table_headers as $header) {
    $table_headers_html .= "<th title='{$header['tooltip']}'>{$header['header']}</th>\n";
  }

  $table_headers_html = "<thead>\n<tr>\n{$table_headers_html}\n</tr></thead>\n";

  foreach ($range_array as $range) {
    $heading_html = "<h2 class='pub-price-list-range-title'>{$range['publication_range_name']}</h2>\n";
    $pub_rows = '';

    foreach ($range['publications_in_range'] as $publication) {
      extract($publication, EXTR_OVERWRITE);

      $pub_rows .= sprintf(
        "<tr>
          <td class='ppl-pub-number'>%s</td>\n
          <td class='ppl-title'><a href='%s'>%s</a></td>\n
          <td class='ppl-isbn'>%s</td>\n
          <td class='ppl-edition'>%s</td>\n
          <td class='ppl-pages'>%s</td>\n
          <td class='ppl-images'>%s</td>\n
          <td class='ppl-format'>%s</td>\n
          <td class='ppl-price'>£%s</td>\n
          <td class='ppl-notes'>%s</td>\n
        </tr>\n",
        $publication_number,
        $url,
        brhg2016_trim_things($title, 60),
        $isbn,
        str_replace(array('Edition', 'Revised'), array('Ed', 'Rev'), $edition),
        $pages,
        $images,
        $format,
        $price,
        $notes
      );
    }

    $list_html .= $heading_html . "<div class='pub-price-list-range'><table class='table table-striped pub-price-list-table'>" . $table_headers_html  . $pub_rows . "</table></div>";
  }

  return "<div class='pub-price-list-wrapper'>{$list_html}</div>";
}

/*********************************************************************************************************************************
 *
 * ACF Filters
 *  
 ********************************************************************************************************************************/

/**
 * Autofill Publication *Collections* options page values.
 * 
 * The page has two nested repeaters: the outer one, for each collection; 
 * and, the inner one for items within the collection.
 */
add_filter('acf/load_value/key=field_65e1bf1134d67', 'brhg2024_publication_collection_autofill', 10, 3);

function brhg2024_publication_collection_autofill($value, $post_id, $field) {

  global $pub_num_field;

  // Each row within the repeater has an array as its value.
  if (empty($value) || !is_array($value)) {
    return $value;
  }

  $new_repeater_array = array();

  // Each row within the repeater has an array as its value.
  foreach ($value as $key => $row) {
    $post_id = $row['field_65e1c08434d69'];

    // Keep the value if this field as the publication ID.
    $new_repeater_array[$key]['field_65e1c08434d69'] = $post_id;

    // Set the featured image field
    $new_repeater_array[$key]['field_65e243221c174'] = get_post_thumbnail_id($post_id);

    // Only one pup_range per publication is possible. However, an array is returned.
    $pub_range = get_the_terms($post_id, 'pub_range');

    // Check a pub_range was found, and set it in the range field.
    $new_repeater_array[$key]['field_65e2496e3110b'] = is_array($pub_range) && !empty($pub_range[0]->name)
      ? $pub_range[0]->name
      :  '';
    // Set the publication number field
    $new_repeater_array[$key]['field_65e1c0f134d6a'] = get_field($pub_num_field, $post_id);
  }

  return $new_repeater_array;
}

/**
 * Make a transient from the collections repeater, used by 'related publications'.
 * 
 * The array set as the transient has the structure:
 * ['collections']
 *    [publication_id] => array(
 *        ['collections'] => array of the names of collections containing this publication
 *        ['similar'] => array of ids of all other publications in the same collections as this publication
 * ['all publications'] => array of all publication post IDs
 * ['not_in_collection'] => array of all publication IDs not in any collection
 * ); 
 */
add_filter('acf/load_value/key=field_65e1beca34d66', 'brhg2024_publication_collection_transient', 10, 3);

function brhg2024_publication_collection_transient($value, $post_id, $field) {

  if (! is_array($value)) {
    return $value;
  }

  $all_IDs_in_collections = array();

  foreach ($value as $collection) {

    if (empty($collection['field_65e1bf1134d67'])) {
      continue;
    }

    // Make array of all publication IDs in this collection. field_65e1bf1134d67 is the collection's repeater. field_65e1c08434d69 is publication ID.
    $collection_IDs = wp_list_pluck($collection['field_65e1bf1134d67'], 'field_65e1c08434d69');

    foreach ($collection_IDs as $id) {

      // ID exists as  a key in $all_IDs_in_collections. 
      if (array_key_exists($id, $all_IDs_in_collections)) {

        // Add current collection name 
        $all_IDs_in_collections[$id]['collections'][] = $collection['field_65e1bfcc34d68'];
        // Add other publication IDs in this collection. Make sure array only contains unique values.
        $all_IDs_in_collections[$id]['similar'] = array_unique(
          array_merge($all_IDs_in_collections[$id]['similar'], $collection_IDs)
        );

        // ID does not exist as a key in $all_IDs_in_collections. Create a new item.
      } else {
        $all_IDs_in_collections[$id]['collections'] = array($collection['field_65e1bfcc34d68']);
        $all_IDs_in_collections[$id]['similar'] = $collection_IDs;
      }

      // Sort similar, don't reindex.
      arsort($all_IDs_in_collections[$id]['similar'], SORT_NUMERIC);
    }
  }

  // Get an array of all publication post IDs
  $all_pub_IDs_args = array(
    'numberposts' => -1,
    'post_type' => 'pamphlets',
    'fields' => 'ids'
  );


  $all_pub_IDs = get_posts($all_pub_IDs_args);
  $pub_IDs_in_all_collections = array_keys($all_IDs_in_collections);
  $pub_IDS_not_in_any_collection = array_diff($all_pub_IDs, $pub_IDs_in_all_collections);
  krsort($all_IDs_in_collections, SORT_NUMERIC);

  $transient_array = array(
    'collections'       => $all_IDs_in_collections,
    'all_publications'  => $all_pub_IDs,
    'not_in_collections' => $pub_IDS_not_in_any_collection
  );

  set_transient('brhg_publication_collections', $transient_array, 0);

  return $value;
}

/***************************************************************************************************************************
 *
 * Make the tables on the Publication Prices options page 
 *  
 **************************************************************************************************************************/

/**
 * Construct the publication price table array. 
 * 
 * brhg2024_publication_populate_repeater() is called when:
 * 
 * 1) The Publication Prices options page loads/updates.
 * 2) get_field( 'field_65e495e90ec48', 'options' ) is called in brhg2024_acf_validate_publications_save_post(), below.
 *    This happens when a publication post type is published or updated. 
 * 3) If get_field( 'field_65e495e90ec48', 'options' ) is called from a template file. 
 * 
 */
add_filter('acf/load_value/key=field_65e495e90ec48', 'brhg2024_publication_populate_repeater', 10, 3);

function brhg2024_publication_populate_repeater($value, $post_id, $field) {

  $value_by_pub_id = brhg2024_get_id_keyed_array_from_pub_range_repeater($value);

  //  Get all the terms in the pub_range taxonomy
  $pub_range_args = array(
    'orderby'      => 'id',
    'order'        => 'DESC',
    'hide_empty'  => true,
  );

  $pub_ranges = get_terms('pub_range', $pub_range_args);

  $populated_repeater = array();
  $pubs_in_ranges = array();

  // Loop through each pub-range
  foreach ($pub_ranges as $range) {

    // Get all the publications for the current range.
    $pubs_in_range_args = array(
      'fields'          => 'ids',
      'posts_per_page'  => -1,
      'post_type'       => 'pamphlets',
      'post_status'     => 'publish',
      'meta_key'        => 'pamphlet_number',
      'orderby'         => 'meta_value_num',
      'order'           => 'ASC',
      'tax_query'       => array(
        array(
          'taxonomy'  => 'pub_range',
          'field'     => 'term_id',
          'terms'     => $range->term_id,
        ),
      ),
    );

    $query_publications_in_range = new WP_Query($pubs_in_range_args);

    $row_arrays = array();
    $pubs_in_this_range = array();

    // Loop through the publications and create a ACF repeater field for each one.
    foreach ($query_publications_in_range->posts as $publication_id) {

      // Get the stored pub data from the resorted $value
      $options_publication_array = $value_by_pub_id[$publication_id] ?? array();

      $publication_number =  (int) get_post_meta($publication_id, 'pamphlet_number', true);

      /**
       * Populate the row and make list if posts in each range.
       * Always add the publication ID and publication number.
       * For the other values, tests to see if they exist in the row, if there is a value, use it.
       */
      $row_arrays[] = array(
        'field_65e496670ec4b_field_65d1eefa88310' => $publication_number,
        'field_65e496670ec4b_field_65d1128d336b6' => $publication_id,
        'field_65e496670ec4b_field_65d1be5089086' => !empty($options_publication_array)
          ? $options_publication_array['price']
          : number_format(0, 2, '.', ''),
        'field_65e496670ec4b_field_65d1bf95db70d' => !empty($options_publication_array)
          ? $options_publication_array['shipping']
          : number_format(0, 2, '.', ''),
        'field_65e496670ec4b_field_65d22591b31c3' => !empty($options_publication_array)
          ? $options_publication_array['for_sale']
          : 0,
        'field_65e496670ec4b_field_65e493189ece7' => !empty($options_publication_array)
          ? $options_publication_array['notes']
          : ''
      );

      $pubs_in_this_range[$publication_id] = $publication_number;
    }

    /**
     * Populate the outer repeater with the range and publication rows.
     */
    $populated_repeater[] = array(
      'field_65e49702714b2' => $range->term_id,
      'field_65e4962c0ec4a' => $row_arrays
    );

    $pubs_in_ranges[$range->term_id]  = $pubs_in_this_range;
  }

  // Update the options, including any changes since the options page was last updates.
  update_field('field_65e495e90ec48', $populated_repeater, 'options');

  // Store the list of pubs in each range
  set_transient('brhg_pubs_in_range', $pubs_in_ranges);

  return $populated_repeater;
}


/**
 * Format the price and shipping in the publications table
 */
add_filter('acf/load_value/key=field_65e496670ec4b_field_65d1be5089086', 'brhg2024_format_prices', 10, 3);
add_filter('acf/load_value/key=field_65e496670ec4b_field_65d1bf95db70d', 'brhg2024_format_prices', 10, 3);

function brhg2024_format_prices($value, $post_id, $field) {

  if ($field['_name'] == 'price' && ($value < 0.01 || empty($value))) {
    return number_format(0, 2, '.', '');
  }

  return number_format($value, 2, '.', '');
}

/**
 * Stop the Publication select from finding aby results.
 */
add_filter('acf/fields/post_object/query/key=field_65d1128d336b6', 'brhg2024_alter_pamphlet_query', 10, 3);

function brhg2024_alter_pamphlet_query($args, $field, $post_id) {

  // Empty array will return home page results, i.e. last 10 posts, so use array(0).
  $new_args['post__in'] = array(0);

  return $new_args;
}

/**
 * Stop the Publication Range select from finding any results on the publication prices options page.
 */
add_filter('acf/fields/taxonomy/query/key=field_65e49702714b2', 'brhg2024_alter_pub_range_query', 10, 3);

function brhg2024_alter_pub_range_query($args, $field, $post_id) {

  // Non-existing taxonomy
  $new_args['taxonomy'] = 'fish';

  return $new_args;
}

/**
 * Validate the pub_range and publication number on the edit publication page.
 */

add_action('acf/validate_save_post', 'brhg2024_acf_validate_publications_save_post');

function brhg2024_acf_validate_publications_save_post() {

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'pamphlets') {
    return;
  }

  // Get the range from the post's select input
  $input_range = (int) $_POST['tax_input']['pub_range'][0];

  /**
   * If there is no pub_range selected, or, if the pub_range has been deleted, stop the post being published by forcing draft status.
   * 
   * @see {@link https://wordpress.stackexchange.com/questions/42013/prevent-post-from-being-published-if-custom-fields-not-filled}
   */
  $error = false;

  if (empty($input_range) || !term_exists($input_range, 'pub_range')) {
    if (empty($input_range)) {
      $missing_message = 'Missing Publication Range';
    } elseif (!term_exists($input_range, 'pub_range')) {
      $missing_message = 'Publication Range no longe in taxonomy';
    } else {
      $missing_message = 'Unknown error.';
    }

    $error = new WP_Error('ERROR', $missing_message);

    // The error is stored in transient data.
    set_transient("brhg_missing_pub_range_errors_{$_POST['post_ID']}", $error);

    // unhook this function to prevent indefinite loop
    remove_action('acf/validate_save_post', 'brhg2024_acf_validate_publications_save_post');

    // update the post to change post status to draft
    wp_update_post(array('ID' => $_POST['post_ID'], 'post_status' => 'draft'));

    // re-hook this function again
    add_action('acf/validate_save_post', 'brhg2024_acf_validate_publications_save_post');

    return;
  }

  /**
   * No pub_range errors here!!!!!
   */

  // Reaching here there is no longer an error, so if the transient data exists, delete it.
  delete_transient("brhg_missing_pub_range_errors_{$_POST['post_ID']}");

  /**
   * This triggers brhg2024_publication_populate_repeater().
   */
  $range_repeater_value = get_field('publication_range_repeater', 'options');

  /**
   * Check that the publication number does not exist in the range
   * 
   * brhg_pubs_in_range[ $input_range ] is an array of [ post ID ] => number in range
   */
  if (get_transient('brhg_pubs_in_range')) {
    $pubs_in_ranges = get_transient('brhg_pubs_in_range');
    $pub_number = (int) $_POST['acf']['field_4f834448a7039'];

    // Find the array keys, who's value is $pub_number, and put the post IDs in new array.
    $pub_number_in_range = is_array($pubs_in_ranges)
      ? array_keys($pubs_in_ranges[$input_range], $pub_number)
      : array();

    // If the pub_number is 0 (the default), or $pub_number appears more than once in the range.
    if (empty($pub_number) || $pub_number === 0 || (count($pub_number_in_range) > 1)) {

      $number_message = $pub_number === 0
        ? 'ERROR: Publication number is 0.'
        : 'ERROR: Publication number already exists in this Publication Range.';

      $error = new WP_Error('ERROR', $number_message);

      set_transient("brhg_pub_number_error_{$_POST['post_ID']}", $error);
    } else {
      // No error, if error transient exists, delete it.
      delete_transient("brhg_pub_number_error_{$_POST['post_ID']}");
    }
  }

  return;
}


// Remove Post Published, and Post Updated messages when there is a pub_range error.
add_filter('post_updated_messages', 'brhg2024_pub_range_error_message_control', 99);

function brhg2024_pub_range_error_message_control($messages) {
  if (! array_key_exists('post', $_GET)) {
    return;
  }

  $post_id = $_GET['post'];

  if (get_transient("brhg_missing_pub_range_errors_{$post_id}")) {
    return array();
  }

  return $messages;
};

// Add the pub_range error message if there is an error.
add_action('admin_notices', 'brhg2024_publication_errors');

function brhg2024_publication_errors() {
  $post_id = array_key_exists('post', $_GET) ? $_GET['post'] : null;

  if (empty($post_id)) {
    return;
  }

  if ($error = get_transient("brhg_missing_pub_range_errors_{$post_id}")) { ?>
    <div class="error">
      <p><?php echo $error->get_error_message(); ?></p>
    </div><?php
        }

        if ($error = get_transient("brhg_pub_number_error_{$post_id}")) { ?>
    <div class="error">
      <p><?php echo $error->get_error_message(); ?></p>
    </div><?php
        }
      }

      /**
       * Convert the AFC publication_range_repeater array to be a list of publications sorted by post ID.
       * 
       * There are two scenarios where this might be used: an AFC filter, a call to get_field().
       * In the former, the publication_range_repeater array will use ACF field keys, e.g. field_65e4962c0ec4a. 
       * In the latter, it will use ACF names, e.g. range_number.
       * 
       * @param array $range_repeater The AFC publication_range_repeater array. Default empty array.
       * @param boolean $use_field_key Whether the array uses AFC field keys, or not. Default true.
       * 
       * @return array A list of publications sorted by post ID, this the post ID as the keys.
       */
      function brhg2024_get_id_keyed_array_from_pub_range_repeater($range_repeater = array(), $use_field_key = true) {


        if (empty($range_repeater) || !is_array($range_repeater)) {
          return false;
        }

        // From an ACF filter $range_repeater will use field keys, from get_filed() $range_repeater uses filed names.
        $pub_repeater_key = $use_field_key ? 'field_65e4962c0ec4a' : 'publication_range_item_repeater';
        $range_id_key = $use_field_key ? 'field_65e49702714b2' : 'publication_range_name'; // Misnamed, should be 'publication_range_id';
        $pub_id_key = $use_field_key ? 'field_65e496670ec4b_field_65d1128d336b6' : 'publication';
        $number_in_range_key = $use_field_key ? 'field_65e496670ec4b_field_65d1eefa88310' : 'range_number';
        $price_key = $use_field_key ? 'field_65e496670ec4b_field_65d1be5089086' : 'price';
        $shipping_key = $use_field_key ? 'field_65e496670ec4b_field_65d1bf95db70d' : 'shipping';
        $for_sale_key = $use_field_key ? 'field_65e496670ec4b_field_65d22591b31c3' : 'for_sale';
        $notes_key = $use_field_key ? 'field_65e496670ec4b_field_65e493189ece7' : 'notes';

        // Resort $range_repeater into an array pub_id => original_array
        $range_repeater_by_pub_id = array();

        foreach ($range_repeater as $range) {

          if (!is_array($range[$pub_repeater_key])) {
            continue;
          }

          $range_id = $range[$range_id_key];

          foreach ($range[$pub_repeater_key] as $pub) {
            $range_repeater_by_pub_id[$pub[$pub_id_key]] = array(
              'range'           => $range_id,
              'number_in_range' => $pub[$number_in_range_key],
              'pub_id'          => $pub[$pub_id_key],
              'price'           => $pub[$price_key],
              'shipping'        => $pub[$shipping_key],
              'for_sale'        => $pub[$for_sale_key],
              'notes'           => $pub[$notes_key]
            );
          }
        }

        return $range_repeater_by_pub_id;
      }

      /************************************************************************************************************************************
       * Generate the CSV price list and the transient used to by the brhg-publications-price-list shortcode (see above).
       * 
       * When the Publication Prices options page is updated, or a pamphlet post is updated, this function generates 
       * an array which saved as a wp transient. The transient is used by the shortcode above to make the publication price list. 
       * A CSV file containing the price list is also generated.
       * 
       **********************************************************************************************************************************/
      add_action('acf/options_page/save', 'brhg2024_acf_options_page_call_price_list', 10, 2);
      add_action('wp_after_insert_post', 'brhg2024_save_publication_call_price_list', 10, 2);


      /**
       * This function filters the acf/options_page/save hook, because it will fire for all ACF options pages.
       *
       * @param string $page The page being saved. This will always be the ACF default of `options`.
       * @param string $slug The slug of the options page being saved.
       */
      function brhg2024_acf_options_page_call_price_list($page, $slug) {
        if ($slug !== 'publications-prices') {
          return;
        }
        brhg2024_construct_publications_price_list();
      }

      /**
       * This function filters the wp_after_insert_post hook, because it will fire for all post types.
       * The save_post_{post_type} can't be used because it fires before the post metadata has been updated. 
       * wp_after_insert_post fires once a post, its terms and meta data has been saved.
       *
       * @param int $post_id The ID of the post that has just saved.
       * @param object $post_obj The WP_Post object for the post that just saved.
       */
      function brhg2024_save_publication_call_price_list($post_id, $post_obj) {
        if ($post_obj->post_type === 'pamphlets') {
          brhg2024_construct_publications_price_list();
        }
      }


      function brhg2024_construct_publications_price_list() {

        // Set at top of file.
        global $csv_file_path;

        $range_array = get_field('publication_range_repeater', 'options');

        if (!is_array($range_array)) {
          return;
        }

        $new_list = array();

        /**
         * Initiate CSV file.
         * 
         */
        $csv_checked_file = @file_exists($csv_file_path)
          ? $csv_file_path
          : false;

        if ($csv_checked_file) {
          $price_list_csv_file = fopen($csv_checked_file, "w");
          $csv_headers = array('Range', '#', 'Title', 'ISBN', 'Edition', 'Pages', 'Images', 'Format', 'Price', 'Notes', 'URL');

          // Write the headers as the fist row in the CSV file.
          fputcsv($price_list_csv_file, $csv_headers);
        }

        // Construct an nested array, first by publication range, then by publications in that range
        foreach ($range_array as $range) {

          // pub_range is a taxonomy
          $range_term = get_term($range['publication_range_name'], 'pub_range');

          // The range abbreviation name is taxonomy term metadata added via ACF
          $range_prefix = get_term_meta($range['publication_range_name'], 'range_name_abbreviation', true);

          // AFC field is wrongly named publication_range_name, since it stores the ID, not the name.
          $range_details = array(
            'publication_range_id' => $range['publication_range_name'],
            'publication_range_name' =>  $range_term->name,
            'publication_range_prefix' => $range_prefix
          );

          // Array to collect the publications within a range.
          $range_publications = array();

          // Publications in range
          foreach ($range['publication_range_item_repeater'] as $pub_key => $publication) {

            $item = array(
              'range_number'        => $publication['range_number'],
              'publication_number'  => "{$range_details['publication_range_prefix']}-{$publication['range_number']}",
              'wp_post_id'          => $publication['publication'],
              'title'               => get_the_title($publication['publication']),
              'title_decoded'       => html_entity_decode(
                get_the_title($publication['publication']),
                ENT_QUOTES
              ), // For CSV title only, decode HTML entities.
              'isbn'                => get_post_meta($publication['publication'], 'isbn', true),
              'edition'             => get_post_meta($publication['publication'], 'edition', true),
              'pages'               => get_post_meta($publication['publication'], 'number_of_pages', true),
              'images'               => get_post_meta($publication['publication'], 'number_of_images', true),
              'format'              => get_post_meta($publication['publication'], 'format', true),
              'price'                 => $publication['price'],
              'price_string'          => "£{$publication['price']}",
              'brhg_website_for_sale' => $publication['for_sale'],
              'notes'                 => $publication['notes'],
              'url'                 => get_the_permalink($publication['publication'])
            );

            /**
             * Process the publication item as a row in the CSV file.
             */
            if ($csv_checked_file) {

              // Add the publication range name to the first column for first item in range only.
              $first_col = $pub_key === 0 ? $range_term->name : '';
              array_unshift($item, $first_col);

              // Remove array elements that do not correspond to a CSV column.
              $csv_remove = array(
                'range_number'          => '',
                'wp_post_id'            => '',
                'title'                 => '',
                'price'                 => '',
                'brhg_website_for_sale' => ''
              );

              // Write row to CSV
              fputcsv($price_list_csv_file, array_diff_key($item, $csv_remove));
            }

            // Remove title_decoded, used for CSV, but unsafe for database.
            unset($item['title_decoded']);

            // Add in the slug
            $item['post_slug'] = get_post_field('post_name', $publication['publication']);

            // Add row to pub_range for transient
            $range_publications[$publication['range_number']] = $item;
          } // Item repeater

          // Add pub_range to publication list array for transient
          $new_list[$range_term->slug] = $range_details + array('publications_in_range' => $range_publications);
        } // Range repeater

        // Write the array containing the full price list to db transient.
        set_transient('brhg_publications_price_list', $new_list);

        /**
         * Close the CSV file.
         */
        if ($csv_checked_file) {
          fclose($price_list_csv_file);
        }
      }
