<?php

/**
 * Gets the header for all single posts, archive pages and pages
 *
 * brhg2016_get_page_title() is in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

?>

<header id="page-title-header" class="page-header" aria-label="Main content header">
    <div class="page-header__title-wrap">

        <?php
        // Pre-title info such as section, sub-section and event series
        if (is_singular()) {
            get_template_part('chunk', 'pre-title');
        }
        ?>

        <h1 class="page-header__title">
            <?php brhg2016_get_page_title(); ?>
        </h1>
        <?php
        // Post-title meta for single pages including, sub-titles, authors (articles, pamphlets and books) and Event Series Dates
        if (is_singular()) {
            get_template_part('chunk', 'post-title-meta');
        }
        ?>

    </div>

    <?php
    // Archive and search page header; includes archive intro and search form. 
    if (is_archive() || is_search()) { ?>
        <div class="page-header__archive-intro">
            <?php get_template_part('chunk', 'archive-header');
            if (is_search()) {

                //get_template_part('searchform');
                //get_template_part('chunk', 'search-filter');
                get_template_part('content', 'search-page');
            ?>
                <button type="button" id="search-filter-form-btn" class="search-form__btn">Refine Search</button>
        </div>
    <?php }
        }
        // Current & Forthcoming Event Series
        if (get_query_var('special_url') === 'event-diary' || is_post_type_archive('events') || is_post_type_archive('event_series')) { ?>
    <div class="current-series">
        <?php get_template_part('chunk', 'current-series'); ?>
    </div>
<?php } ?>

</header>
<hr class="page-header__pale-rule">