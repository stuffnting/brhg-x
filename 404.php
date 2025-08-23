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

    <div class="single-item-content">
        <div class="page-content">
            <p><?php _e('The thing that you are looking for is not here.', 'brhg2016'); ?></p>

            <?php get_search_form(); ?>
        </div>
        <aside id="tag-404-cloud" class="tag-index">
            <?php brhg2016_tag_cloud(45); ?>
        </aside>
    </div>

</section>


<?php get_footer();
