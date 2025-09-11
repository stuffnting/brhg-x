<?php

/**
 * The outer contents for thumb-only lists. Used by Event Series and Pamphlets
 *
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>
<?php get_template_part('content', 'page-header');  ?>
<section id="thumb-only-list-page" class="thumb-only-list">

  <?php // $pub_sets set in archive-pamphlets.php i.e. are we on the Pamphlet Archive page, or Publications Collections page

  if (get_query_var('post_type', false) === 'pamphlets') {

    // Cycle through the publication ranges: pamphleteer, republications and books
    foreach ($pub_sets as $set) :

      if (get_query_var('special_url', false)) {
        // Collection
        $title = $set['publication_collection_title'] ?? '';
        $description = $set['publication_collection_description'] ?? '';
        $set_id = $set['publication_collection_title'] ?? ''; // Compares titles
      } else {
        // Range
        $title = $set->name;
        $description = $set->description;
        $set_id = $set->term_id; // Compares IDs
      }

      // First the publication range/collection title & description 
  ?>
      <header class="thumb-only-list__header">
        <h2 class="thumb-only-list__title"><?php echo $title; ?></h2>
        <p class="thumb-only-list__description"><?php echo $description; ?></p>
      </header>

      <div class="thumb-only-listing__wrapper">

        <?php /* Start the Loop for the pamphlets */
        while (have_posts()) : the_post();

          if (get_query_var('special_url', false) === 'publication-collections') {
            // Collections: Is post in the current collection?
            $test = in_array($post->ID, $set['items']) ? true : false;
          } else {
            // Ranges: does the post's pub_range match the current range
            $current_item_range = get_the_terms($post->ID, 'pub_range');
            $test = !empty($current_item_range) && $set_id == $current_item_range[0]->term_id;
          }

          if ($test) {
            get_template_part('loop', 'thumbs-only');
          }

        endwhile;
        rewind_posts(); ?>
      </div> <!-- thumb-only-listing-wrapper -->
    <?php endforeach;
  } else {

    // i.e. we are on the Events Series Archive page 
    ?>
    <div class="thumb-only-listing__wrapper">
      <?php /* Start the Loop */

      while (have_posts()) : the_post();
        get_template_part('loop', 'thumbs-only');
      endwhile; ?>
    </div>
  <?php } ?>

</section> <!-- thumb-only-list -->