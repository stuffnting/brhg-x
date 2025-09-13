<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage BRHG2016
 * @since 1.0
 */

get_header(); ?>

<section id="content" class="single-page error-404 not-found container">
    <?php get_template_part('content', 'page-header');  ?>

    <div class="entry-content">
        <p class="error-4o4__p"><?php _e('You can\'t always get what you want.', 'brhg2016'); ?></p>

        <?php get_search_form(); ?>
    </div>

    <aside id="tag-404-cloud" class="tag-index">
        <?php brhg2016_tag_cloud(45); ?>
    </aside>

</section>


<?php get_footer();
