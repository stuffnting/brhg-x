<?php

/**
 * Change the style of the T&C error message
 */
add_filter('wpsc_after_cart_output', 'brhg2025_filter_trolley_form');

function brhg2025_filter_trolley_form($content) {
  $string = 'style="color: #cc0000; font-size: smaller;"';

  return str_replace($string, '', $content);
}

/**
 * Filter the trolley image
 */

add_filter('wspsc_cart_icon_image_src', 'brhg2025_filter_trolley_image');

function brhg2025_filter_trolley_image($image) {

  $new_image = get_theme_file_uri('images/trolley.svg');;

  return $new_image;
}
