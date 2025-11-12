<?php

/**
 * Compact trolley link with number of items
 */

function brhg2025_number_items_trolley_link() {
  // Add the number of items in the trolly.
  $num_items = 0;

  if (class_exists('WPSC_Cart')) {
    $wspsc_cart = WPSC_Cart::get_instance();
    $num_items = $wspsc_cart->get_total_cart_qty();
  }

  if ($num_items > 0) {
    $trolley_url = get_option('cart_checkout_page_url');
    return "<div id='floating-trolley' class='floating-trolley'>\n
            <a href='{$trolley_url}' class='cart-number-items'>Shopping Trolley <span class='trolley-items'>{$num_items}</span></a>\n
            </div>";
  } else {
    return '';
  }
}

/**
 * Adds the buy box, where to buy links, covers and reviews to a single publication page content.
 */

function brhg2024_add_pamphlet_content($content) {

  global $post;

  // brhg2024_wp_simple_cart_shortcode() is in functions/publication_controls.php. Returns false in pamphlet is not for sale.
  $buy_book_box = brhg2024_wp_simple_cart_buy_button_shortcode($post->ID);

  // Where to buy
  $where_to_buy_link = get_field('where_to_buy_page_link', 'options');
  $where_buy_html = sprintf(
    "<aside class='pubs-buy-where' aria-label='Where to buy'>\n
      %s\n
      <p><a href='%s' class='pub-buy-where__link'>Find out where to buy all our publications.</a></p>\n
    </aside>\n",
    $buy_book_box ? "<p><a href='#buy-book' class='pub-buy-where__link'>Buy this publication from us now.</a></p>" : '',
    $where_to_buy_link
  );

  // Covers
  $covers = array('front', 'back');
  $covers_html = '';

  foreach ($covers as $cover) {
    $cover_field = "pam_{$cover}_cover";
    $cover_url = wp_get_attachment_image_url(get_field($cover_field), 'full');

    if (! $cover_url) {
      continue;
    }
    $img_args = array('class' => 'pub-covers__img', 'alt' => $cover . ' cover');
    $cover_img = wp_get_attachment_image(get_field($cover_field), 'big_thumb', false, $img_args);

    $covers_html .= sprintf(
      "<a href='%s' class='pub-covers__link'>%s</a>",
      esc_url($cover_url),
      $cover_img,
    );
  }

  // Covers
  $pamphlet_covers_html = sprintf(
    "<div class='pub-covers'>
      %s
    </div>\n",
    $covers_html
  );

  //reviews
  $reviews_html = brhg2024_pamphlet_reviews_html($post->ID);

  return $where_buy_html . $pamphlet_covers_html . $content . $reviews_html . $buy_book_box;
}

/**
 * Get a formatted list of reviews for a pamphlet
 */

function brhg2024_pamphlet_reviews_html($pamphlet_id) {
  // Reviews
  if (have_rows('pamphlet_reviews', $pamphlet_id)):

    $review_rows = "";

    // Loop through rows.
    while (have_rows('pamphlet_reviews', $pamphlet_id)) : the_row();

      // Get the meta values
      $review_text = get_sub_field('pamphlet_review_text');

      // Sometimes the last review is an empty row
      if (empty($review_text)) {
        continue;
      }

      $split_review_text = get_sub_field('pamphlet_review_split');
      $review_source = get_sub_field('pamphlet_review_source');
      $review_link_type = get_sub_field('pamphlet_review_link_type');

      // Get the source link
      if ($review_link_type !== 'none') {
        $review_link = get_sub_field('pamphlet_review_link');
        $cite_link = get_sub_field('pamphlet_review_link_cite');

        if ($review_link_type === 'text') {
          $review_link_text = get_sub_field('pamphlet_review_link_text');
        }
      };

      /**
       * Ready the review blockquote content.
       */

      // Allow only a few tags in review text.
      $allowed_tags = array('p', 'a', 'i', 'b', 'em', 'strong');
      $review_text_processed = strip_tags($review_text, $allowed_tags);

      // Is the review large? Use details-summary
      if ($split_review_text === true) {
        // Match the p elements in the review
        $re = '/(<p>.+<\/p>)/U';
        $matches = array();
        $found = preg_match_all($re, $review_text_processed, $matches, PREG_PATTERN_ORDER, 0);

        if ($found && is_array($matches[0])) {
          // array_shift() removes 1st para for summary. p tags not allowed in summary element, strip them.
          $review_text_summary = preg_replace('#</?p[^>]*>#', '', array_shift($matches[0]));
          //$review_text_summary .= ' <span class="review-hellip">[&hellip;]</span>';

          $review_text_processed = sprintf(
            "<details name='review' class='pub-reviews__details'>\n
              <summary class='pub-reviews__summary'>%s</summary>\n
              %s\n
            </details>\n",
            $review_text_summary ?? '',
            implode($matches[0])
          );
        } else {
          $review_text_processed = '';
        }
      } else {
        $review_text_processed = $review_text;
      }

      // Format the review blockquote
      $blockquote_html = sprintf(
        "<blockquote%s class='class='pub-reviews__text'>%s</blockquote>\n",
        $review_link_type !== 'none' && $cite_link ? " cite='$review_link'" : '',
        $review_text_processed ?? ''
      );

      // Format the source and source link
      $source_html = sprintf(
        "<p class='pub-reviews__source'>%s%s%s</p>",
        $review_link_type === 'source' ? "<a href='$review_link' target='_blank' class='class='pub-reviews__source-link'>" : '',
        $review_source ?? '',
        $review_link_type === 'source' ? "</a>" : ''
      );

      if ($review_link_type === 'text') {
        $link_html = sprintf(
          "<p class='pub-reviews__link-wrap'>
            <a href='%s' target='_blank' class='pub-reviews__link'>%s</a>
          <p>\n",
          $review_link ?? '',
          $review_link_text ?? '',
        );
      } else {
        $link_html = '';
      }

      $review_rows .= "
      <div class='pub-reviews__item'>" . $blockquote_html . $source_html . $link_html . "</div>";

    // End loop.
    endwhile;

    $reviews_html = sprintf(
      "<aside id='reviews' class='pub-reviews__wrap' aria-label='Reviews'>\n%s%s\n</aside>",
      "<p class='pub-reviews__title'>Reviews</P>",
      $review_rows
    );

  // No value.
  else : $reviews_html = "";
  // Do something...
  endif;

  return $reviews_html;
}

/***************************************************************************************************************
 *
 * Buy book box including wp_cart_button from WP Simple Shopping Cart shortcode
 *
 ***************************************************************************************************************/

/**
 * Build the buy-book box for a single publication page, including the WP Simple Shopping Cart shortcode.
 *
 * Called from brhg2016_content_filter() in functions/utility_functions.php.
 * The shortcode is added by the WP Simple Shopping Cart plugin.
 *
 * @param int $post_id The ID if the single publication page being processed.
 * @return string The HTML, including the WP Simple Shopping Cart shortcode, for the buy-book box.
 */
function brhg2024_wp_simple_cart_buy_button_shortcode($passed_post_id = 0) {

  if (empty($passed_post_id)) {
    return 'WP Simple Cart Shortcode ERROR: No post ID passed.';
  }

  // Check the shop is open
  if (!get_field('shop_open', 'options')) {
    return false;
  }

  /**
   * We need to get the pricing details from the Publication Prices options page
   *
   * First get the repeater with all the publication ranges (and their publications)
   */
  $pub_range_repeater_array = get_field('publication_range_repeater', 'options');

  /**
   * Convert the AFC publication_range_repeater array to be a list of publications sorted by post ID.
   * 2nd parameter specifies that the range repeater is from get_field(), not an ACF filter.
   */

  // brhg2024_get_id_keyed_array_from_pub_range_repeater() in ./publication_control.php
  $pub_range_repeater_by_id = brhg2024_get_id_keyed_array_from_pub_range_repeater($pub_range_repeater_array, false);

  // The list of publications sorted by post ID could not be made
  if (empty($pub_range_repeater_by_id) || !is_array($pub_range_repeater_by_id)) {
    return 'WP Simple Cart Shortcode ERROR: No pub_range repeater.';
  }

  /**
   * Get the entry for the current pamphlet from the list of publications sorted by post ID.
   * Keys are [range] [number_in_range] [pub_id] [price] [shipping] [for_sale] [notes]
   */
  $publication = $pub_range_repeater_by_id[$passed_post_id];

  // Something went wrong - double check the post ID.
  if ($publication['pub_id'] !== $passed_post_id) {
    return false;
  };

  // Is the item is for sale?
  if (!$publication['for_sale']) {
    return;
  }

  // If price is not set or 0, don't allow the pamphlet to be sold.
  if (empty($publication['price']) || $publication['price'] == 0) {
    return false;
  }

  // Generate the buy button. Get the wp_cart_button shortcode output

  // Changing the $name format will break the publication sales stats!
  $name = '#' . $publication['number_in_range'] . ' ' . get_the_title($publication['pub_id']);
  $price = $publication['price'];
  $shipping = empty($publication['shipping']) ? 0 : $publication['shipping'];
  $thumb = get_the_post_thumbnail_url($publication['pub_id'], 'tiny_thumbs');

  $button_args = array(
    'name' => $name,
    'price' => $price,
    'shipping' => $shipping,
    'thumbnail' => $thumb,
  );

  // wpsc_cart_button_handler() is in wp_shopping_cart_shortcodes.php
  $shortcode = wpsc_cart_button_handler($button_args);

  // Make the HTML for the buy-book box, including the shortcode
  $html_with_shortcode = sprintf(
    "<aside id='buy-book' class='buy' aria-name='Buy publication'>\n
        <p class='buy__title'>Buy this publication</p>\n
        <p class='buy__post-warning'><strong>* If you want books posting outside the UK <a href='https://www.brh.org.uk/site/contact-us/'>please email</a> first to check postal rates.</strong></p>
        <p>%s</p>\n
        <p>£%s %s</p>\n
        <p>%s</p>\n
        <div class='buy__paypal-img'>\n
          <img class='paypal-img' src='https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png' alt='PayPal Acceptance Mark' width='200' border='0' />
        </div>\n
      </aside\n",
    get_the_title($publication['pub_id']),
    $publication['price'],
    $publication['shipping'] == 0 ? 'inc P&amp;P within the UK' : " +  {$publication['shipping']} P&amp;P",
    $shortcode,
  );

  return $html_with_shortcode;
}

/**
 * Filter the [always_show_wp_shopping_cart] shortcode which is used on the checkout page.
 */
add_filter('do_shortcode_tag', 'brhg2024_filter_wp_simple_cart_trolly_shortcode', 10, 2);

function brhg2024_filter_wp_simple_cart_trolly_shortcode($output, $tag) {

  if ($tag != 'always_show_wp_shopping_cart') {
    return $output;
  }

  $new_output = '';

  // IS the trolley empty or not?
  $searchString = '<form action="https://www.paypal.com/cgi-bin/webscr"';
  $test_position = strpos($output, $searchString);

  if ($test_position != false) {

    // Add a class to the table
    $old_table_tag = "<table";
    $new_table_tag = '<table class="brhg-trolley" ';

    $new_output .= str_replace($old_table_tag, $new_table_tag, $output);

    // Change the checkout button
    $old_checkout_button = plugins_url('wordpress-simple-paypal-shopping-cart/images/paypal_checkout_EN.png');
    $new_checkout_button = get_theme_file_uri('images/checkout.svg');

    $new_output = str_replace($old_checkout_button, $new_checkout_button, $new_output);

    // Find where to add the delivery warning
    $position = strpos($new_output, $searchString) - strlen($searchString);
    $contact_url = get_site_url(null, '/contact-us/');


    $delivery_warning = "<div class='delivery-address-warning'>
      <p>*** Delivery Address ***</p>
      <ul>
        <li>Check your delivery address in PayPal when you checkout (<a href='#delivery-address'>see below</a>).</li>
        <li>If you want delivery outside the UK <a href='$contact_url'>please email</a> first to check postal rates.</li>
      </ul>
    </div>";

    $position += strlen($searchString);
    $new_output = substr_replace($new_output, $delivery_warning, $position, 0);

    // Manual checkout form
    $manual_checkout_form_required = array(
      "First Name" => "First Name*",
      "Email" => "Email*",
      "Email* Checkout" => "Email Checkout", // Protect the Email in the button
      "Shipping Address" => "Posting Address (to post outside the UK, <a href='$contact_url'>email first</a> to check postage)",
      "Street Address" => "Street Address*",
      "City" => "City/Town*",
      "State" => "County/Region*",
      "Postal Code" => "Post Code*"
    );

    foreach ($manual_checkout_form_required as $before => $after) {
      $new_output = str_replace($before, $after, $new_output);
    }

    $manual_checkout_search_string = '<div class="wpsc-manual-checkout-section">';
    $insert_or_position = strpos($new_output, $manual_checkout_search_string);
    $or = '<div class="wpsc-manual-checkout-section-or">OR</div>';
    $new_output = substr_replace($new_output, $or, $insert_or_position, 0);
  } else {
    // Trolley is empty.
    $new_output = "<div class='empty-trolley-wrap'>{$output}</div>";
  }

  //snt_dump($new_output);

  return $new_output;
}
