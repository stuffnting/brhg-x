<?php

/**
 * The template file for all single post except for: Pages, Events, Projects and Venues
 *
 * brhg2016_add_single_after_contents() is in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since  BRHG2016 1.0
 *
 */
?>

<?php get_header(); ?>



<?php
# Include everything in the loop so that the connected post-2-post items will be added to $wp_query
if (have_posts()) :
    while (have_posts()) : the_post();
        /**
         * add_filter( 'the_content', '' )
         * To add book and pamphlet details to the bottom of the contents
         */
        // brhg2016_add_single_after_contents();
?>

        <article id="content">

            <?php
            /*
            * Page header
            */
            get_template_part('content', 'page-header');

            ?>
            <div id="single-item-container" class="single-item__wrap">
                <section id="single-item-block-wrapper" class="details-block--single" aria-label="Article details">
                    <?php

                    # If this is an event/book/pamphlet there will be two blocks in the info box
                    $extra_class = 'details-block__block--1up';

                    /**
                     * Add details block
                     *
                     * First add the details of the event/book/pamphlet
                     */
                    if (get_post_type() === 'events' || get_post_type() === 'books' || get_post_type() === 'pamphlets') {
                        $extra_class = "details-block__block--2up";
                    ?>
                        <div class="details-block__block details-block__block--1up">
                            <div class="details-block__heading">
                                <?php echo get_post_type_object(get_post_type())->labels->singular_name ?> Details
                            </div>
                            <div class="details-block__details">
                                <?php get_template_part('chunk', get_post_type()); ?>
                            </div>
                        </div>
                    <?php
                    }

                    /**
                     * Now add the item details - Section, Subjects, Tags, Post Date etc
                     */
                    ?>
                    <div class="details-block__block <?php echo $extra_class ?>">
                        <div class="details-block__heading">Page Details</div>
                        <div class="details-block__details">
                            <?php get_template_part('chunk', 'item-details'); ?>
                        </div>
                    </div>
                    <?php if (post_type_supports(get_post_type(), 'comments')) { ?>
                        <div class="details-block__comments-link ">
                            <?php get_template_part('chunk', 'comment-link'); ?>
                        </div>
                    <?php
                    }
                    ?>
                </section>
                <div class="entry-content">
                    <section aria-label="Article main content">
                        <?php

                        /**
                         * Content
                         * This function in functions/the_content_filter.php
                         * Where to buy link added to the top of content for pamphlets pages
                         * Footnotes added by this function
                         */
                        brhg2016_content_filter()
                        ?>
                    </section>
                    <?php
                    /*
                    * Comments
                    */
                    if (post_type_supports(get_post_type(), 'comments')  && comments_open()) { ?>

                        <section class="comments__wrap" aria-label="Comments">
                            <?php comments_template(); ?>
                        </section>
                    <?php
                    }
                    ?>
                </div><!-- single-item-main-content -->
            </div><!-- single-item-container -->
        </article>

        <?php
        /*
                * Related
                */
        get_template_part('content', 'related-posts');
        ?>
<?php
    // End the loop.
    endwhile;

// If no content, include the "No posts found" template.
else :
    the_content('content', 'none');
endif;
?>



<?php get_footer();
