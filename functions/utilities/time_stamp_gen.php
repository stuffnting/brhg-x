<?php

/**
 * Generates the timestamps each time an Event or Event Series is saved.
 *
 * NOTE: Events are listed in the Event Diary until they have finished.
 * If no end date is provided it is assumed that the Event finishes on the same day it began.
 * If no end time is provided it is assumed that the event is 2 hours long.
 * These assumed times and dates will not be displayed on Event pages, 
 * if no end dates and times are provided only the start times and dates are displayed.
 *
 * These functions are only called when saving Events or Event Series from the edit screen. 
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */


/**
 * Generates the timestamp for events when they are posted or updated.
 *
 * @param int        $post_ID the ID of the event being saved, passed by the action.
 * @param object     $post the post (event) being saved, passed the action.
 * @param $update    $update Whether this is an existing post being updated or not, passed by the action.
 *
 *
 */
function brhg2016_save_event_time($post_ID, $post, $update) {

    if (!isset($_POST['action'])) {
        return;
    }

    if ($_POST['action'] != 'editpost' || $_POST['post_type'] != 'events') {
        return;
    }

    // Verify if this is an auto save routine. If it is our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_ID;
    }

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_ID)) {
            return $post_ID;
        }
    } else {
        if (!current_user_can('edit_post', $post_ID)) {
            return $post_ID;
        }
    }

    # This value has to be provided for the event to save.
    $start_date = ($_POST['acf']['field_4f7c24905eb88']) ? $_POST['acf']['field_4f7c24905eb88'] : ' ';
    $start_time = ($_POST['acf']['field_4f7c24906a886']) ? $_POST['acf']['field_4f7c24906a886'] : ' ';

    # If there is no end date provided assume that the event finishes on the same day it starts.
    $end_date = ($_POST['acf']['field_4f7c2490755ac']) ? $_POST['acf']['field_4f7c2490755ac'] : $start_date;
    # End time.
    $end_time = ($_POST['acf']['field_4f7c249080396']) ? $_POST['acf']['field_4f7c249080396'] : '';

    # Generate the timestamps

    # Start Time
    $start_time_stamp = strtotime($start_date . ' ' .  $start_time);

    # End timestamp
    if (empty($end_time) && $end_date === $start_date) {
        # If there is no end date or end time assume that the events finishes 2 hours after it starts (+7200)		
        $end_time_stamp = $start_time_stamp + 7200;
    } elseif (empty($end_time) && $end_date !== $start_date) {
        # If there is an end date but no end time, assume event ends at the end of the end date
        $end_time_stamp = strtotime($end_date . ' ' .  '23:59');
    } elseif (!empty($end_time) && $end_date) {
        #End date and end time are both set - end date is set to start date (ends same day) if end date is not set - see above
        $end_time_stamp = strtotime($end_date . ' ' .  $end_time);
    } else {
        # Something has gone wrong
        $end_time_stamp = '';
    }

    # Save the timestamp values
    delete_post_meta($post->ID, 'end_time_stamp');
    delete_post_meta($post->ID, 'start_time_stamp');

    if (isset($start_time_stamp)) {
        update_post_meta($post->ID, 'start_time_stamp', $start_time_stamp);
    }

    if (isset($end_time_stamp)) {
        update_post_meta($post->ID, 'end_time_stamp', $end_time_stamp);
    }
}

add_action('save_post_events', 'brhg2016_save_event_time', 10, 3);

/**
 * Generates the timestamp for event series when they are posted or updated.
 *
 * @param int        $post_ID the ID of the event being saved, passed by the action.
 * @param object     $post the post (event) being saved, passed the action.
 * @param $update    $update Whether this is an existing post being updated or not, passed by the action.
 *
 *
 */
function brhg20166_save_series_time($post_ID, $post, $update) {

    if (! array_key_exists('action', $_POST) || $_POST['action'] != 'editpost' || $_POST['post_type'] != 'event_series') {
        return;
    }

    // Verify if this is an auto save routine. If it is our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_ID;
    }

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_ID)) {
            return $post_ID;
        }
    } else {
        if (!current_user_can('edit_post', $post_ID)) {
            return $post_ID;
        }
    }

    # This must be set for the event series to save.
    $start_date = ($_POST['acf']['field_4f7c8c3106dfb']) ? $_POST['acf']['field_4f7c8c3106dfb'] : '$start_date';
    # If this is not set use the assume it is a 1 day event series (e.g. a bookfair) and make the end date the same as the start date.
    $end_date = ($_POST['acf']['field_4f7c8c310f6fa']) ? $_POST['acf']['field_4f7c8c310f6fa'] : $start_date;

    # Generate the timestamp values
    $series_start_date_stamp = strtotime($start_date);
    $series_end_date_stamp = strtotime($end_date);

    # Save the timestamp values
    delete_post_meta($post->ID, 'series_start_date_stamp');
    delete_post_meta($post->ID, 'series_end_date_stamp');

    update_post_meta($post->ID, 'series_start_date_stamp', $series_start_date_stamp);
    update_post_meta($post->ID, 'series_end_date_stamp', $series_end_date_stamp);
}

add_action('save_post_event_series', 'brhg20166_save_series_time', 10, 3);
