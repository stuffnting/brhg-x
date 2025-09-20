<?php

/**
 * Archive pages that aren't Event Series or Pamphlets
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

get_header(); ?>

<?php get_template_part('content', 'page-header');  ?>

<?php
$archive_type = false;


// Archive type
if (is_tax('contrib_alpha')) {
    $archive_type = "contributor";
} elseif (get_query_var('special_url') === 'tag-index') {
    $archive_type = "tag-index";
} elseif (get_query_var('special_url') === 'radical-history-listings') {
    $archive_type = "radical-history-listings";
} elseif (get_query_var('special_url') === 'subject-index') {
    $archive_type = "radical-history-listings";
} else {
    $archive_type = "other";
}

$pagination = "";

/**
 * Pagination
 */
if ($archive_type === 'contributor') {
    $pagination = brhg2016_make_conrib_alpha_list();
} elseif ($archive_type === 'other') {
    $pagination = brhg2016_archive_pagination();
}

/**
 * Tag Index
 */
if ($archive_type === 'tag-index') {
?>
    <section id="tag-index-tag-cloud" class="tag-index" aria-label="Tag index">
        <?php brhg2016_tag_cloud(0); ?>
    </section>

    <?php
    /**
     * Subject Index and Rad His Index
     */
} elseif ($archive_type === "radical-history-listings" || $archive_type === "subject-index") {
    // Subject archive

    $subjects = get_terms(array(
        'taxonomy'   => get_query_var('type_tax'),
        'hide_empty' => true,
    ));

    if (isset($subjects) && is_array($subjects)) {
    ?>
        <section class="archive-content">
            <dl class="archive-content__desc-list">
                <?php
                foreach ($subjects as $subject) {
                    echo sprintf(
                        "<dt class='archive-content__desc-term'>
                            <a href='%s' class='archive-content__desc-term-link'>%s — (%d)</a>
                        </dt>\n
                        <dd class='archive-content__desc-def'>%s
                        </dd>\n",
                        get_term_link($subject, get_query_var('type_tax')),
                        $subject->name,
                        $subject->count,
                        wp_strip_all_tags($subject->description)
                    );
                }
                ?>
                <dl>
        </section>
    <?php
    }
    /**
     * Other archive pages
     */
} else {
    if (!empty($pagination)) {
    ?>
        <div class="archive-p8n__wrap archive-p8n__wrap--top">
            <?php echo $pagination; ?>
        </div>
    <?php } ?>

    <section class="archive-content">
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
    </section>

<?php } ?>

<?php if (!empty($pagination)) { ?>
    <div class="archive-p8n__wrap archive-p8n__wrap--bottom">
        <?php echo $pagination; ?>
    </div>
<?php } ?>

<?php
get_footer();
