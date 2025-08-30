<?php

/**
 * Adds the post title meta to single posts and single items on archive pages.
 * Includes sub-title, book author, pamphlet author, article author and event-series dates.
 *
 * brhg2016_get_item_connected() and brhg2016_get_item_meta_singles() are both in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php
$subtitle_tag = (is_home() || is_archive() || is_search()) ? 'h3' : 'h2';
$class_base = (is_home() || is_archive() || is_search())
    ? "archive-item-header"
    : "page-header";

// Subtitle
if (brhg2016_get_item_meta_singles('sub_title', false)) {

    echo "<{$subtitle_tag} class='{$class_base}__sub-title'>";
    brhg2016_get_item_meta_singles('sub_title');
    echo "</{$subtitle_tag}>";
}

// Event series - dates
if (get_post_type() === 'event_series' && brhg2016_event_series_dates(false)) {

    echo "<{$subtitle_tag} class='{$class_base}__sub-title'>";
    brhg2016_event_series_dates();
    echo "</{$subtitle_tag}>";
}

// Events diary - current event series
if (get_query_var('special_url') === 'event-diary') {
?>
    <div class="<?php echo $class_base; ?>__after-title">
        <?php get_template_part('chunk', 'events'); ?>
    </div>
<?php
}

// Author
switch (get_post_type()) {
    case 'books':
        $author = brhg2016_get_item_meta_singles('author', false) ?: false;
        break;
    case 'pamphlets':
        $author = brhg2016_get_item_connected('author', false) ?: false;
        break;
    case 'articles':
        $author = brhg2016_get_item_connected('article_author', false) ?: false;
        break;
    case 'post':
        $author = brhg2016_get_item_connected('post_contri_author', false) ?: get_the_author();
        break;
    default:
        $author = false;
        break;
}

if ($author) {
?>
    <div class="<?php echo $class_base; ?>__after-title">
        By <?php echo $author; ?>
    </div>
<?php
}
