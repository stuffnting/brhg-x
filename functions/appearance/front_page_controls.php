<?php

/**
 * Auto fill the filed in the Front Page Settings, Order Featured items group
 */

add_filter('acf/load_value/key=field_65db8d67d7819', 'brhg2024_featured_items_positions', 10, 3);

function brhg2024_featured_items_positions($repeater_array, $post_id, $field) {

  if (!empty($repeater_array)) {
    return $repeater_array;
  }

  $new_array = array();

  $new_array[] = array(
    'field_65db9872b75e1' => "Featured Item A"
  );

  $new_array[] = array(
    'field_65db9872b75e1' => "Featured Item B"
  );

  $new_array[] = array(
    'field_65db9872b75e1' => "Featured Item C"
  );

  $new_array[] = array(
    'field_65db9872b75e1' => "Featured Item D"
  );

  return $new_array;
}

/**
 * Control viability of Friend section
 */

add_filter('acf/prepare_field/key=field_65f56f53feab9', 'brhg2024_hide_fp_friends');

function brhg2024_hide_fp_friends($field) {

  $user = wp_get_current_user();

  if (WP_SITE_ADMIN != $user->ID) {
    return false;
  }

  return $field;
}

/**
 * A loop that returns the HTML of the friends' logos. Used by the friends-logo-set shortcode below,
 * and the front-page.php template files. 
 */
function brhg2024_front_page_friends_section_repeater_loop($friends_repeater_loop = array()) {

  if (empty($friends_repeater_loop)) {
    return;
  }

  $html_out = '';

  foreach ($friends_repeater_loop as $friend) {
    $html_out .=  sprintf(
      '<div class="fp-friends__item">' . "\n" .
        '<a href="%1$s" title="%2$s" class="fp-friends__item-link" target="_blank">' .
        '<img src="%3$s" class="fp-friends__item-img" alr="Logo for %2$s">' .
        '</a>' . "\n" .
        '</div>' . "\n",
      esc_url($friend['fp_friends_link']),
      $friend['fp_friends_name'],
      esc_url($friend['fp_friends_logo']['sizes']['medium_large']),
    );
  }

  return $html_out;
}

/**
 * A shortcode to add the font page friends logo set to other pages.
 */
add_shortcode('friends-logo-set', 'brhg2024_friends_logo_set');

function brhg2024_friends_logo_set($atts) {
  extract(shortcode_atts(array(
    'wide' => false,
  ), $atts));

  $friends_fields = get_field('fp_friends', 'options');

  if (empty($friends_fields)) {
    return;
  }

  // Returns the friend items, each wrapped in .fp-friends__item
  $repeater_html = brhg2024_front_page_friends_section_repeater_loop($friends_fields['fp_friends_repeater']);

  return sprintf(
    "%s<div class='fp-friends__items-wrap'>%s</div>\n%s",
    $wide ? "<div class='fp-friends__items-wide'>" : '',
    $repeater_html,
    $wide ? "</div>" : ''
  );
}
