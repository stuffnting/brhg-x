<?php

/**
 * This shortcode replaces the Select Footnotes for WordPress plugin
 * by Charles Johnson.
 * 
 * @since 2024
 * 
 */

add_shortcode('ref', 'brhg2024_footnote_shortcode');

// This global stores the references as the occur through an article
$GLOBALS['brhg_ref'] = array();

function brhg2024_footnote_shortcode($atts, $content = null) {

  global $post;
  global $brhg_ref;

  // Add the footnote text from the shortcode content
  $brhg_ref[] = array('footnote' => $content);

  // Force array to be 1 indexed, not 0 indexed.
  if (isset($brhg_ref[0])) {
    $brhg_ref[1] = $brhg_ref[0];
    unset($brhg_ref[0]);
  }

  $ref_number = count($brhg_ref);

  // Add the reference and footnote IDs to use in the links
  $brhg_ref[$ref_number]['footnote_ID'] = $post->post_name . '-n-' . $ref_number;
  $brhg_ref[$ref_number]['ref_ID'] = 'to-' . $brhg_ref[$ref_number]['footnote_ID'];

  // Return the footnote number and link into the article content.
  return sprintf(
    "<span style='white-space: nowrap;' class='footnote-ref'><sup><a href='#%s' id='%s'>[ %s ]</a></sup></span>",
    $brhg_ref[$ref_number]['footnote_ID'],
    $brhg_ref[$ref_number]['ref_ID'],
    $ref_number
  );
}

/**
 * Add the footnotes by the [ref] shortcode.
 */
add_filter('the_content', 'brhg2024_add_footnotes', 1000);

function brhg2024_add_footnotes($content) {
  global $brhg_ref;

  // If there are no references, bail
  if (! isset($brhg_ref) || count($brhg_ref) === 0) {
    return $content;
  }

  $footnote_list = '';

  // Make the footnote list items from the global array
  foreach ($brhg_ref as $key => $footnote) {
    $footnote_list .= sprintf(
      "<li id='%s'>
        <sup><a href='#%s' class='ref-back-link-num'>[ %s ]</a></sup> 
        %s <a href='#%2\$s' class='ref-back-link-text'>[back&hellip;]</a>
      </li>",
      $footnote['footnote_ID'],
      $footnote['ref_ID'],
      $key,
      $footnote['footnote'],
    );
  }

  // Return post content with the formatted footnote list section added
  return "$content
    <div class='footnotes'>\n
      <div class='footnote-title'>Notes</div>\n
      <ul class='footnote-list'>$footnote_list</ul>
    </div>";
}
