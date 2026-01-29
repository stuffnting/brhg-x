<?php

/**
 * Return an array of event option field keys
 */
function brhg2026_event_option_fields() {
  return array(
    'field_4f83418dac671', // BRHG event?
    'field_68daae9b582b7', // Misc event?
    'field_68d1b5e494975' // Use featured image?
  );
}

/**
 * Add filters
 */
foreach (brhg2026_event_option_fields() as $field) {
  add_filter('acf/prepare_field/key=' . $field, 'brhg2026_post_options_filter');
}

/**
 * Hide the event option fields if the post type is not events
 */
function brhg2026_post_options_filter($field) {

  $screen = get_current_screen();
  $fields_array = brhg2026_event_option_fields();

  // Hide the event option fields on post types that are not events
  if ($screen?->post_type !== 'events' && in_array($field['key'], $fields_array)) {
    return false; // Hides the field
  }

  return $field;
}
