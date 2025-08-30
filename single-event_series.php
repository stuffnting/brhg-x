<?php
/*
*
* 
*
*
*/

get_header();
?>


<?php
if (have_posts()) :
    while (have_posts()) : the_post(); ?>

        <article id="single-event-series" class="single-event-series">

            <?php
            /*
                * Page header
                *
                */
            get_template_part('content', 'page-header');

            ?>
            <div class='entry-content'>
                <?php
                // The programme table is added by function/shortcodes.php
                // This function in functions/utility_functions.php
                brhg2016_content_filter();
                ?>
            </div>

    <?php
    // End the loop.
    endwhile;

// If no content, include the "No posts found" template.
else :
    get_template_part('content', 'none');

endif; ?>
        </article>

        <?php get_footer(); ?>