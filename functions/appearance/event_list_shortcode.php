<?php

/**
 * The event-list shortcode.
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

/**
 * [event_list] shortcode for event table on event series page.
 *
 * @param    array   $atts an array of attributes passed from the shortcode
 *                   The short code atts are:
 *                      posts - a comma separated list of event post IDs used to override the events connected to 
 *                          the event series, default is false.
 *                      subset - the subset of the events series, used to break up the event table into smaller chunks.
 *                      title - the h3 title to add before the subset table is added to the page.
 *                      venue - the event venue.
 *                      location - the location within the venue.
 *                      date - Show or hide the date column. Default is to show.
 *                      time - Show or hide the time column. Default is to show.
 *
 * @return   string  The events programme as a table with some additional markup
 */

add_shortcode('event_list_wrapper', 'brhg2024_make_event_programme');

function brhg2024_make_event_programme($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'title'     => 'Programme',
        ),
        $atts,
        'event_list_wrapper'
    );

    if (isset($content)) {
        $new_content = sprintf(
            "<div class='event-programme-wrapper' id='full-programme'><h2 class='event-list-wrap-title'>%s</h2>%s</div>",
            $atts['title'],
            brhg2024_change_headers($content, 'h3', 'event-series-programme-date'),
        );
        return do_shortcode($new_content);
    }

    return '';
}

function brhg2024_change_headers($html = '', $new_tag = 'h3', $class = '') {
    // Define a pattern to match all heading tags
    $pattern = '/<h[1-6](.*?)>(.*?)<\/h[1-6]>/i';

    // Define the replacement pattern
    $replacement = "<$new_tag class='$class'>$2</$new_tag>";

    // Perform the replacement
    $new_html = preg_replace($pattern, $replacement, $html);

    return $new_html;
}

add_shortcode('event_list', 'brhg2016_make_event_list');

function brhg2016_make_event_list($atts) {
    // Here $post refers to the Event Series, who's page the shortcode is generating the programme table
    global $post;

    // Check the atts passed by the shortcode against defaults
    $atts = shortcode_atts(
        array(
            'posts'     => false,
            'subset'    => false,
            'title'     => false,
            'venue'     => false,
            'location'  => false,
            'time'      => true,
            'date'      => true
        ),
        $atts,
        'event_list'
    );

    $atts['venue'] = filter_var($atts['venue'], FILTER_VALIDATE_BOOLEAN);
    $atts['location'] = filter_var($atts['location'], FILTER_VALIDATE_BOOLEAN);
    $atts['date'] = filter_var($atts['date'], FILTER_VALIDATE_BOOLEAN);
    $atts['time'] = filter_var($atts['time'], FILTER_VALIDATE_BOOLEAN);

    // Has a 'posts' attribute been passed by the shortcode?
    if ($atts['posts']) {
        // Turn the comma separated string into an array of post ID integers
        $atts['posts'] = array_map('intval', explode(',', $atts['posts']));

        // Check that the new array contains only ints
        if ($atts['posts'] === array_filter($atts['posts'], 'is_int')) {
            // Set $event_list to the array new array of post IDs
            $event_list = $atts['posts'];
        }
    }

    // If there was no posts attribute passed by the shortcode, or the passed attribute could not be converted to an array of ints
    if (!isset($event_list)) {
        // Make an array containing the events connected to the event series. 
        // $post->series is added by the posts-to-posts plugin
        $event_list = array();
        foreach ($post->series as $event) {
            $event_list[] = $event->ID;
        }
    }

    // If there are no events in the event series yet, return nothing
    if (empty($event_list)) {
        return;
    }

    // Use this array to do a query to find the events connected to the event series
    $query_events_args = array(
        'post_type'           => 'events',
        'posts_per_page'      => -1,
        'meta_key'            => 'start_time_stamp',
        'orderby'             => 'meta_value_num',
        'order'               => 'ASC',
        'ignore_sticky_posts' => true,
        'post__in'            => $event_list,
    );

    $connected = new WP_Query($query_events_args);

    // If there are connected events, 
    if ($connected->have_posts()) :

        // Connect the speakers to each event in the $connected object
        if (function_exists('p2p_type')):
            p2p_type('speaker_to_event')->each_connected($connected, array(), 'speakers');
            p2p_type('venue_to_events')->each_connected($connected, array(), 'venues');
        endif;

        // Loop through the posts and create a table row for each one
        $header_rows = '';
        $list_rows = '';
        // Keep track of how many optional columns are used (from date, time, and location)
        $count_optional_cols = 0;

        while ($connected->have_posts()) : $connected->the_post();
            // From here $post refers to the current event in $connected

            if ($atts['subset'] === false || $atts['subset'] == get_post_meta($post->ID, 'subset', true)) {

                $list_rows .= "<tr class='event_programme_table_row'\n>";

                if ($atts['date']) {
                    $date = brhg2016_get_item_event_date(false);
                    $list_rows .=  "<td class='event-programme-table-date'>$date</td>\n";
                }

                if ($atts['time']) {
                    $time = brhg2016_get_item_event_time(true);
                    $list_rows .=  "<td class='event-programme-table-time'>$time</td>\n";
                }

                $link = get_the_permalink();
                $title = get_the_title();
                $sub_title = brhg2016_get_item_meta_singles('sub_title', false)
                    ? ': ' . brhg2016_get_item_meta_singles('sub_title', false)
                    : '';

                $list_rows .= "<td class='event-programme-table-title'><a href='$link'>$title$sub_title</a></td>\n";

                $speakers = brhg2016_get_item_connected('speakers', false);
                $list_rows .= "<td class='event-programme-table-speakers'>$speakers</td>\n";

                if ($atts['venue']) {
                    $venue = brhg2016_get_item_connected('venues', false);
                    $list_rows .= "<td class='event-programme-table-venue'>$venue</td>\n";
                }

                if ($atts['location']) {
                    $location = brhg2016_get_item_meta_singles('location', false, $post->ID) ? brhg2016_get_item_meta_singles('location', false) : "";
                    $list_rows .=  "<td class='event-programme-table-location'>$location</td>\n";
                }

                $list_rows .= "</tr>\n";
            }

        endwhile;

        // Make the table header cells
        if ($atts['date']) {
            $header_rows .= "<th class='event-programme-table-date'>Date</th>\n";
            $count_optional_cols++;
        }

        if ($atts['time']) {
            $header_rows .= "<th class='event-programme-table-time'>Time</th>\n";
            $count_optional_cols++;
        }

        $header_rows .= "<th class='event-programme-table-title'>Title</th>\n";
        $header_rows .= "<th class='event-programme-table-speakers'>With</th>\n";

        if ($atts['venue']) {
            $header_rows .= "<th class='event-programme-table-venue'>venue</th>\n";
            $count_optional_cols++;
        }

        if ($atts['location']) {
            $header_rows .= "<th class='event-programme-table-location'>Location</th>\n";
            $count_optional_cols++;
        }

        // Build the table
        $list_output = sprintf(
            "<div class='event-list-wrapper optional-cols-%s'>\n
                %s
                <div class='event-list'>\n
                    <table class='table table-striped event-programme-table'>\n
                        <thead>\n
                            <tr>\n
                                %s \n 
                            </tr>\n
                        </thead>\n
                            %s\n
                    </table>\n
                </div>
            </div>\n",
            $count_optional_cols,
            ($atts['title']) ? "<h3 class='event-programme-title'>{$atts['title']} <span class='event-programme-scroll'>(drag left/right)</span>:</h3>\n" : '',
            $header_rows,
            $list_rows
        );

    endif;

    // Reset $post to the Event Series so that other shortcodes will work.
    wp_reset_postdata();

    return $list_output;
}
