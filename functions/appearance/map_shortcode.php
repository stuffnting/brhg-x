<?php

/**
 * The brhg_map shortcode works in conjunction with the ACF OpenStreetMap field plugin.
 */

add_shortcode('brhg_map', 'brhg2025_brhg_map_shortcode');

function brhg2025_brhg_map_shortcode() {
  return get_field('osm_map');
}
