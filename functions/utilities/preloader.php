<?php

/**
 * Preload
 */

add_filter('wp_preload_resources', 'brhg2024_preload');

function brhg2024_preload($resources) {
  if (! is_array($resources)) {
    $resources = array();
  }

  // Fonts
  $fonts = array(
    //'essays1743-bold-webfont.woff2',
    //'essays1743-italic-webfont.woff',
    'essays1743-webfont.woff2',
    'notosans-regular-webfont.woff2',
    'notosans-bold-webfont.woff2',
    'notosans-italic-webfont.woff2',
    'notosans-bolditalic-webfont.woff2'
  );

  foreach ($fonts as $font) {
    $resources[] = array(
      'href'          => get_template_directory_uri() . '/fonts/' . $font,
      'as'            => 'font',
      'type'          => 'font/woff2',
      'crossorigin'   => '' // needed for fonts even though same origin
    );
  }

  // Images
  $main_bg_images = array(
    'angel.svg',
    'skeleton.svg',
    'scroll-left.svg',
    'scroll-middle.svg',
    'scroll-right.svg',
    'wavy-line.svg',
    'header-oxo.svg',
    'search.svg',
    'hamburger.svg',
    'menu-bottom-line.svg',
    'cherub-left.svg',
    'cherub-middle.svg',
    'cherub-right.svg',
    //'q-white.svg',
    //'q.svg',
    //'brhg-missing.svg',
    'trolley.svg'
  );

  $fp_bg_images = array(
    'headline-frame-top-scroll.svg',
    'headline-frame-top.svg',
    'headline-frame-middle.svg',
    'headline-frame-bottom.svg',
    'headline-frame-bottom-scroll.svg',
    'slider-frame-top.svg',
    'slider-frame-bottom.svg',
    'slider-frame-middle.svg',
  );

  if (is_front_page()) {
    $bg_images = array_merge($main_bg_images, $fp_bg_images);
  } else {
    $bg_images = $main_bg_images;
  }

  // Mobiles use a png file for front page logo
  if (is_front_page()) {
    $fp_logo = brhg2024_check_phone()  ? 'full-logo-small-2024.png' : 'full-logo-small-2024.svg';
    // Logo is LCP, and added to the front of array
    array_unshift($bg_images, $fp_logo);
  }

  foreach ($bg_images as $image) {
    $resources[] = array(
      'href'          => get_template_directory_uri() . '/images/' . $image,
      'as'            => 'image',
      'type'          => 'image/svg+xml'
    );
  }

  return $resources;
}
