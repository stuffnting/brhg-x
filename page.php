<?php

/**
 * Single pages, single Venues and single Projects
 *
 * Single venues are directed here from single-venues.php
 * Single projects are redirected here from single-project.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

get_header(); ?>

<?php get_template_part('content', 'page-header');  ?>

<div class="entry-content">

    <?php
    // Display the Venue meta details; address etc.
    if ('venues' === get_post_type()) {
        get_template_part('chunk', 'venue');
    }
    ?>

    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            brhg2016_content_filter();
        endwhile;

    // If no content, include the "No posts found" template.
    else :
        get_template_part('content', 'none');
    endif;
    ?>

    <?php
    # Add the stuff connected to the project
    if (is_singular('project')) {
        get_template_part('content', 'single-project');
        # Add the Search Page search form
    } elseif (is_page('search-page')) {
        get_template_part('content', 'search-page');
    } elseif (is_page('about-us')) {
    ?>
        <img
            id="about-brhg-logo"
            class="no-border"
            src="<?php echo get_stylesheet_directory_uri() ?>/images/bob.png"
            srcset="<?php echo get_stylesheet_directory_uri() ?>/images/full-logo-small.svg"
            alt="BRHG Logo"
            loading="eager" />
    <?php
    }
    ?>

</div>

<?php get_footer();
