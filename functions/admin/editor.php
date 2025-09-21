<?php

/**
 *  Prevent default TintMCE styles loading
 */
add_filter('mce_css', 'brhg2025_kill_tinymce_styles', 15);

function brhg2025_kill_tinymce_styles($styles) {
  return '';
}

/**
 * Add custom styles
 */
add_filter('mce_css', 'brhg2025_add_timymce_styles', 20);

function brhg2025_add_timymce_styles($styles) {

  $custom_stylesheet = get_template_directory_uri() . '/css/brhg-editor-styles.css';

  // If other styles are already enqueued, append yours
  if (!empty($styles)) {
    $styles .= ',' . $custom_stylesheet;
  } else {
    $styles = $custom_stylesheet;
  }

  return $styles;
}

/**
 * Tame TinyMCE styles
 */
add_filter('tiny_mce_before_init', 'brhg2025_tame_tinymce_styles');

function brhg2025_tame_tinymce_styles($init_array) {


  // Insert the custom formats into the TinyMCE settings
  //$init_array['style_formats'] = json_encode( $style_formats );

  $formats = json_encode([
    'alignleft'     => ['selector' => 'p,h1,h2,h3,h4,h5,h6', 'classes' => 'alignleft'],
    'aligncenter'   => ['selector' => 'p,h1,h2,h3,h4,h5,h6', 'classes' => 'aligncenter'],
    'alignright'    => ['selector' => 'p,h1,h2,h3,h4,h5,h6', 'classes' => 'alignright'],
  ]);

  $init_array['formats'] = $formats;
  $init_array['width'] = '790px'; // or '100%' for responsive

  return $init_array;
}

/**
 *  Add custom image sizes to media uploader
 */
add_filter('image_size_names_choose', 'brhg2016_insert_custom_image_sizes');

function brhg2016_insert_custom_image_sizes($sizes) {
  global $_wp_additional_image_sizes;

  if (empty($_wp_additional_image_sizes)) {
    return $sizes;
  }
  foreach ($_wp_additional_image_sizes as $id => $data) {
    if (!isset($sizes[$id])) {
      $sizes[$id] = sprintf(
        "%s %s",
        $id,
        $data['crop'] ? '(crop)' : '(no crop)'
      );
    }
  }

  return $sizes;
}

/**
 * Alow class attributes in the default gallery shortcode
 */
add_filter('post_gallery', 'brhg2025_add_class_to_gallery_wrapper', 10, 3);

function brhg2025_add_class_to_gallery_wrapper($html, $attr, $instance) {

  if (!empty($attr['class'])) {

    $class = $attr['class'];
    unset($attr['class']);

    // Filter the gallery opening div tag containing the class attribute
    // ($style) use ($class) -> make $class accessible in the anonymous function's scope
    add_filter('gallery_style', function ($style) use ($class) {
      // Add extra style
      return str_replace("class='gallery ", "class='gallery $class ", $style);
    });

    // Generate new gallery shortcode output
    $html = gallery_shortcode($attr);
  }

  return $html;
}
