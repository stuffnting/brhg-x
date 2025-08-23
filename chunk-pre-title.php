<?php

/**
 * Adds the pre title info to single posts.
 * Includes the section and sub-section.
 * Largely used instead of breadcrumbs.
 *
 * brhg2016_get_item_connected(), brhg2016_get_item_section() and brhg2016_get_item_sub_section 
 * are all in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php
// Single Events; the connected Event Series is used instead of a section
if (get_post_type() === 'events' && brhg2016_get_item_connected('series', false)) { ?>
    <div class="page-header__pre-title page-header__pre-title--series">Event from: <?php brhg2016_get_item_connected('series'); ?></div>
<?php
    // Venues, since at present Venuses do not have an archive page brhg2016_get_item_sub_section() will not return a section name
} elseif (is_singular('venues')) { ?>
    <div class="page-header__pre-title page-header__pre-title--venue">Venue</div>
<?php
} elseif (is_singular('post')) { ?>
    <div class="page-header__pre-title page-header__pre--title"><a href="https://www.brh.org.uk/site/blog">Blog</a></div>
<?php
    // The section and sub-section for all items apart from Events, Venues, Blog Posts and Pages
} elseif (!is_page()) { ?>
    <div class="page-header__pre-title page-header__pre-title--section">
        <?php brhg2016_get_item_section(); ?>
        <?php
        if (brhg2016_get_item_sub_section(false)) { ?>
            -
        <?php
            brhg2016_get_item_sub_section();
        }
        ?>
    </div>
<?php
}
?>