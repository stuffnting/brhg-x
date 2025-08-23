<?php
/**
* The template for displaying the bolg list/news page
*
*
* @package Wordpress
* @subpackage BRHG2016
* @since BRHG2016 1.0
*
*/
?>

<?php get_header(); ?>

<section id="content" class="archive-page">

    <?php echo is_archive(); ?>
    <?php get_template_part( 'content', 'page-header' );  ?>

        <?php if ( have_posts() ) : ?>

            <div class="archive-container">

                <?php
                // Start the loop.
                while ( have_posts() ) : the_post();

                    get_template_part( 'loop', 'archive' ); 

                // End the loop.
                endwhile; ?>

                <div class="archive-pagination ">
                    <?php brhg2016_archive_pagination(); ?>
                </div>

            </div>
        <?php
        // If no content, include the "No posts found" template.
        else :
            get_template_part( 'content', 'none' );
        endif;
        ?>
</section>

<?php get_footer(); ?>