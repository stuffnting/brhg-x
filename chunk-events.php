<?php

/**
 * Adds the event details for Events on archive pages, including the diary, and on single Events
 *
 * brhg2016_get_item_event_date(), brhg2016_get_item_event_time(), brhg2016_get_item_connected(), brhg2016_get_item_meta_singles()
 * are all in functions/utility_functions.php 
 *
 * @package WordPress
 * @subpackage BRHG2016
 * @since  BRHG2016 1.0
 */
?>

<?php
// Choose which details to show on different page types
// Defaults
$show_none = 'no';
$show_date = 'yes';
$show_time = 'yes';
$show_location = 'yes';
$show_venues = 'yes';
$show_price = 'yes';
$show_speakers = 'yes';
$show_series = 'yes';
$show_event_filter = 'yes';

// For the Event Diary page
if (get_query_var('special_url') == 'event-diary') {
    $show_location = 'no';
    $show_price = 'no';
    $show_event_filter = 'no';
// For archive pages
} elseif (is_archive() && ! is_search()) {
    $show_location = 'no';
    $show_price = 'no';
    $show_event_filter = 'no';
    $show_speakers = 'no';
    $show_time = 'no';
// For search pages
} elseif (is_search()) {
    $show_location = 'no';
    $show_venues = 'no';
    $show_price = 'no';
    $show_event_filter = 'no';
    $show_speakers = 'no';
    $show_time = 'no';
// For single Event pages
} elseif (is_singular()) {
    $show_none = 'no';
}

extract(brhg2025_get_details_block_classes());

?>

<?php if (brhg2016_get_item_event_date() && $show_none === 'no' &&  $show_date === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Date: </span>
        <span class="<?php echo $value_class; ?>">
            <?php echo brhg2016_get_item_event_date(); ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_event_time() && $show_none === 'no' &&  $show_time === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Time: </span>
        <span class="<?php echo $value_class; ?>">
            <?php echo brhg2016_get_item_event_time(); ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_meta_singles('location', false) && $show_none === 'no' &&  $show_location === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Location: </span>
        <span class="<?php echo $value_class; ?>">
            <?php echo brhg2016_get_item_meta_singles('location'); ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_connected('venues', false) && $show_none === 'no' &&  $show_venues === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Venue: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_connected('venues'); ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_meta_singles('price', false) && $show_none === 'no' &&  $show_price === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Price: </span>
        <span class="<?php echo $value_class; ?>">
            <?php echo brhg2016_get_item_meta_singles('price'); ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_connected('speakers', false) && $show_none === 'no' &&  $show_speakers === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">With: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_connected('speakers'); ?>
        </span>
    </p>
<?php } ?>

<?php if ($show_none === 'no' &&  $show_series === 'yes') {

    $event_series = brhg2016_get_item_connected('series', false) ?: 'Not in a series';
?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Series: </span>
        <span class="<?php echo $value_class; ?>">
            <?php echo $event_series; ?>
        </span>
    </p>
<?php } ?>

<?php if (brhg2016_get_item_meta_singles('brhg_event_filter', false) === 'other' && $show_none === 'no' &&  $show_event_filter === 'yes') { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Note: </span>
        <span class="<?php echo $value_class; ?>">
            This event was not organised by BRHG.
        </span>
    </p>
<?php }
