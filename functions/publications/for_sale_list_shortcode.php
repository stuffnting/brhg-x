<?php

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
