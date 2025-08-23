<?php

/**
 *
 * Archive pages that aren't Event Series or Pamphlets, also Tax Index
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

get_header(); ?>

<?php get_template_part('content', 'page-header');  ?>

<?php
$archive_type = false;

if (is_tax('contrib_alpha')) {
    $archive_type = "contributor";
} elseif (get_query_var('special_url') === 'tag-index') {
    $archive_type = "tag-index";
} else {
    $archive_type = "other";
}

// Pagination
if ($archive_type === 'contributor') {
    brhg2016_make_conrib_alpha_list();
} elseif ($archive_type === 'other') {
    brhg2016_archive_pagination();
}

if ($archive_type === 'tag-index') {
?>
    <section id="tag-index-tag-cloud" class="tag-index" aria-label="Tag index">
        <?php brhg2016_tag_cloud(0); ?>
    </section>

<?php
}
?>

<?php if ($archive_type !== 'tag-index') : ?>
    <div class="archive-content">
        <?php
        if (have_posts()) :

            while (have_posts()) : the_post();

                get_template_part('loop', 'archive');

            // End the loop.

            endwhile;

        // If no content, include the "No posts found" template.
        else :
            get_template_part('content', 'none');

        endif;
        ?>
    </div>

<?php endif; // !tag-index


// Pagination
if ($archive_type === 'other') {
    brhg2016_archive_pagination();
} elseif (is_tax('contrib_alpha')) {
    brhg2016_make_conrib_alpha_list();
}


get_footer();
