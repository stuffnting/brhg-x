<?php
/**
* Taxonomy index pages such: subject index and history listings
*
* This template is selected by brhg2016_special_urls() in functions/custom_query.php
*
* @package Wordpress
* @subpackage BRHG2016
* @since  BRHG2016 1.0
*/

get_header(); ?>

<section id="content" class="archive-page">

    <?php get_template_part( 'content', 'page-header' );  ?>

    <div class="archive-container archive-item">
        <?php  // 'show_option_all' creates the link to display all articles

        $subjects = get_terms( array(
            'taxonomy'   => get_query_var( 'type_tax' ),
            'hide_empty' => true,
        ) );

        if ( isset( $subjects ) && is_array( $subjects ) ) {
            ?>
            <dl class="taxonomy-list">
            <?php
            foreach ( $subjects as $subject ) {
                echo sprintf("<dt><a href='%s'>%s — (%d)</a></dt>\n<dd>%s</dd>\n",
                    get_term_link( $subject, get_query_var( 'type_tax' )),
                    $subject->name,
                    $subject->count,
                    wp_strip_all_tags( $subject->description )
                );
            }
            ?>
            <dl>
        <?php
        }
        ?>
    </div>
</section>


<?php get_footer(); ?>