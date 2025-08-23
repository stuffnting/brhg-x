<?php

/**
 * Get 6 related items for each of the elements in the passes array.
 * 
 * For publications related to a publication post, the publication collections are used.
 * If there are less than 6 similar pamphlets, the number is made up with a tag & subject query,
 * and if needed a random query.
 * 
 * For the other related types, 6 items are found with a tag & subject query,
 * and if needed a random query.
 * 
 * @since 2024
 * 
 * @param array $related_types an array of post types in the form: 'Post Type Title' => 'post_type_name'.
 * @return array An array of WP_Query objects containing the 6 related posts: 'post_type_name' => WP_Query obj
 */
function brhg2024_get_related($related_types = array()) {

  if (! get_the_ID() || empty($related_types)) {
    return false;
  }

  $post_type = get_post_type() ?? '';

  if (
    is_admin()
    || ! is_singular()
    || empty($post_type)
  ) {
    return false;
  }

  // Array to store the 3 sets of related posts
  $related = array();

  foreach ($related_types as $related_type) {
    // Arrays to store the different queries
    $collections_query = array();
    $tag_sub_query = array();
    $random_query = array();

    // A counter to make sure we get 6 related items.
    $cumulative_query_count = 0;

    // Publications use collections to find other related publications
    if ($post_type === 'pamphlets' && $related_type === 'pamphlets') {

      /*
      * A transient is used to store similar publication IDs from the collections.
      * The transient is set in /functions/publication_controls.php
       * using the acf/load_value/key=field_65e1beca34d66 filter
      */
      $collections = get_transient('brhg_publication_collections');

      if (
        array_key_exists(get_the_ID(), $collections['collections'])
        && ! empty($collections['collections'][get_the_ID()])
      ) {

        $similar_in_collections = $collections['collections'][get_the_ID()]['similar'];

        $args = array(
          'orderby' => 'rand',
          'posts_per_page' => 6,
          'post_type' => 'pamphlets',
          'post__in' => $similar_in_collections,
          'post__not_in' => array(get_the_ID()),
          'meta_query' => array(
            array(
              'key' => '_thumbnail_id',
              'compare' => 'EXISTS' // Must have featured image
            ),
          )
        );

        $collections_query = get_posts($args);

        $cumulative_query_count += count($collections_query);
      }
    }

    // Have we got 6 related items yet? No, move to tag & subject query.
    if ($cumulative_query_count < 6) {
      $extra_related_tag_sub = 6 - $cumulative_query_count;
      $tag_sub_query = brhg2024_get_related_query($related_type, $extra_related_tag_sub);

      $cumulative_query_count += count($tag_sub_query);
    }

    // Have we got 6 related items yet? No, move to random query.
    if ($cumulative_query_count < 6) {
      $extra_related_random = 6 - $cumulative_query_count;
      $random_query = brhg2024_get_related_query($related_type,  $extra_related_random, false);

      $cumulative_query_count += count($random_query);
    }

    // Merge the results from the 3 queries. The $posts parameter for empty queries is set to an empty array, see above.
    $related_six = array_merge($collections_query, $tag_sub_query, $random_query);

    $related[$related_type] = $related_six;
  }

  return $related;
}

/**
 * Perform the tag & subject, and the random queries when finding related items.
 * 
 * Used by brhg2024_get_related() above.
 * 
 * @param string $post_type The post type for related items.
 * @param int $number The number of items requested.
 * @param bool $include_tax Whether to include the tax_query.
 * @return object A WP_Query object containing the found related items.
 */
function brhg2024_get_related_query($post_type, $number, $include_tax = true) {
  $subjects = wp_list_pluck(get_the_category(), 'term_id');
  $tags = wp_list_pluck(get_the_tags(), 'term_id');

  $tax_query = array(
    'relation' => 'OR',
    array(
      'taxonomy'  => 'category',
      'field'     => 'term_id',
      'terms'     => $subjects,
      'operator'  => 'IN'
    ),
    array(
      'taxonomy'  => 'tags',
      'field'     => 'term_id',
      'terms'     => $tags,
      'operator'  => 'IN',
    ),
  );

  $args = array(
    'orderby'         => 'rand',
    'numberposts'  => $number,
    'fields'          => 'all',
    'post_type'       => $post_type,
    'post__not_in'    => array(get_the_ID()),
    'tax_query'       => $include_tax ? $tax_query : array(),
    'meta_query'      => array(
      array(
        'key'     => '_thumbnail_id',
        'compare' => 'EXISTS'  // Must have featured image
      ),
    )
  );

  return get_posts($args);
}
