<?php

/**
 * Change the style of the T&C error message
 */
add_filter('wpsc_after_cart_output', 'brhg2025_filter_trolley_form');

function brhg2025_filter_trolley_form($content) {
  snt_dump($content);
  $string = 'style="color: #cc0000; font-size: smaller;"';

  return str_replace($string, '', $content);
}
