<?php

function brhg2025_get_font_page_about_featured() { // Logo file
  if (brhg2024_check_phone()) {
    $logo_url = esc_url(get_theme_file_uri('images/full-logo-small-2024.png'));
  } else {
    $logo_url = esc_url(get_theme_file_uri('images/full-logo-small-2024.svg'));
  }

  // About section
  $about_section = get_field('about_brhg_section', 'options');

  // Set up the four featured items    
  $raw_featured_order_array = get_field('fp_order_featured_items', 'options');

  $processed_featured_order_array = array();

  // $item will be in the for "Featured Item A". Make an array of A, B, C and D, in the order they are on the options page.
  foreach ($raw_featured_order_array as $item) {
    $processed_featured_order_array[] = substr($item['fp_item_position'], -1);
  }

  $featured_items = array();

  // Use $processed_featured_order_array to construct an array of featured items 
  foreach ($processed_featured_order_array as $key => $item) {
    // The featured items are 1 to 4, not 0 to 3.
    $key++;

    $featured_image_id = get_field("fp_featured_item_{$item}_image", 'option')['ID'];
    $featured_image_tag = wp_get_attachment_image($featured_image_id, 'big_thumb', false, array('alt' => ''));

    $featured_items["fi_{$key}"] = array(
      'id' => $key,
      'image_tag' => $featured_image_tag,
      'title' => get_field("fp_featured_item_{$item}_title", 'option'),
      'text' => get_field("fp_featured_item_{$item}_text", 'option'),
      'link' => esc_url(get_field("fp_featured_item_{$item}_link", 'option')),
      'button' => get_field("fp_featured_item_{$item}_button_text", 'option'),
    );
  }

  return array(
    'logo_url' => $logo_url,
    'about_section' => $about_section,
    'featured_items' => $featured_items
  );
}
