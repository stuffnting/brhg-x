<?php

/**
 * Run the event_list_wrapper early to avoid wpautop p tag hell.
 * 
 * *** Note *** wpautop() and shortcode_unautop() run at priority 10, 
 * but shortcode normally run at 11.
 * 
 * Method from embed shortcode. 
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-embed.php#L62
 * 
 * @param string $content The content.
 * @return string The content with the wrapper shortcode processed, but the nested shortcodes intact.
 */
add_filter('the_content', 'brhg2025_run_event_list_wrapper_early', 7);

function brhg2025_run_event_list_wrapper_early($content) {

    global $shortcode_tags;

    // Back up current registered shortcodes and clear them all out.
    $orig_shortcode_tags = $shortcode_tags;
    remove_all_shortcodes();

    // Add event_list_wrapper shortcode
    add_shortcode('event_list_wrapper', 'brhg2024_make_event_programme');

    // Do the shortcode (only the [event_list_wrapper][/event_list_wrapper] one is registered).
    $content = do_shortcode($content, true);

    // Put the original shortcodes back.
    $shortcode_tags = $orig_shortcode_tags;

    return $content;
}

/**
 * A wrapper for all events list tables.
 * 
 * *** !!! NOTE: When this shortcode runs is controlled from publication_the_content_filter !!! ***
 * 
 * @param array     $atts       The attributes from the shortcode.
 *                  title       The title for the events list wrapper, e.g. 'Programme 2025.
 * @param string    $content    The content of the shortcode. This will include the shortcodes 
 *                              for the individual tables.
 * @return string   The contents of the shortcode, including the nested shortcodes, wrapped in the outer HTML.
 */

function brhg2024_make_event_programme($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'title'     => 'Programme',
        ),
        $atts,
        'event_list_wrapper'
    );

    $content = brhg2024_change_headers_in_content($content, 'h3', 'event-prog__prog-sub-title');

    // Only return a populated string if the shortcode has contents.
    if (isset($content)) {
        $new_content = sprintf(
            "<section id='full-programme' class='event-prog'>\n
            <h2 class='event-prog__title'>%s</h2>\n
            %s\n
            </section>\n",
            $atts['title'],
            $content,
        );

        return $new_content;
    }

    return '';
}

function brhg2024_change_headers_in_content($html = '', $new_tag = 'h3', $class = '') {
    // Define a pattern to match all heading tags
    $pattern = '/<h[1-6](.*?)>(.*?)<\/h[1-6]>/i';

    // Define the replacement pattern
    $replacement = "<$new_tag class='$class'>$2</$new_tag>";

    // Perform the replacement
    $new_html = preg_replace($pattern, $replacement, $html);

    return $new_html;
}


/**
 * [event_list] shortcode for event table on event series page.
 *
 * @param    array   $atts an array of attributes passed from the shortcode
 *                   The short code atts are:
 *                   posts - a comma separated list of event post IDs used to override the events connected to 
 *                   the event series, default is false.
 *                   subset - the subset of the events series, used to break up the event table into smaller chunks.
 *                   title - the h3 title to add before the subset table is added to the page.
 *                   theme - if the event series has themes, these can be added for each event.
 *                   venue - the event venue.
 *                   location - the location within the venue.
 *                   with - the speakers. Default is to show.
 *                   date - Show or hide the date column. Default is to show.
 *                   time - Show or hide the time column. Default is to show.
 *
 * @return   string  The events programme as a table with some additional markup
 */
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
            'theme'     => false,
            'venue'     => false,
            'location'  => false,
            'with'      => true,
            'time'      => true,
            'date'      => true
        ),
        $atts,
        'event_list'
    );

    $atts['theme'] = filter_var($atts['theme'], FILTER_VALIDATE_BOOLEAN);
    $atts['venue'] = filter_var($atts['venue'], FILTER_VALIDATE_BOOLEAN);
    $atts['location'] = filter_var($atts['location'], FILTER_VALIDATE_BOOLEAN);
    $atts['with'] = filter_var($atts['with'], FILTER_VALIDATE_BOOLEAN);
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
            if ($atts['with']) {
                p2p_type('speaker_to_event')->each_connected($connected, array(), 'speakers');
            }
            if ($atts['venue']) {
                p2p_type('venue_to_events')->each_connected($connected, array(), 'venues');
            }
        endif;

        // Loop through the posts and create a table row for each one
        $header_rows = '';
        $list_rows = '';
        // Keep track of how many optional columns are used (from date, time, and location)
        $col_count = 0;

        while ($connected->have_posts()) : $connected->the_post();
            // From here $post refers to the current event in $connected

            /**
             * Loop through all connected events. Let an event through if it matches the subset from the shortcode.
             * If there is no subset in the shortcode, let all events through: i.e. list all connected events.
             */
            if ($atts['subset'] === false || $atts['subset'] == get_post_meta($post->ID, 'subset', true)) {

                $list_rows .= "<tr class='event-list__event'>\n";

                if ($atts['date']) {
                    $date = brhg2016_get_item_event_date(false);
                    $date = str_replace(' to ', ' —<br>', $date);
                    $list_rows .=  "<td class='event-list__cell event-list__cell--date'>$date</td>\n";
                }

                if ($atts['time']) {
                    $time = brhg2016_get_item_event_time(true);
                    $list_rows .= "<td class='event-list__cell event-list__cell--time'>$time</td>\n";
                }

                $link = get_the_permalink();
                $title = get_the_title();
                $sub_title = brhg2016_get_item_meta_singles('sub_title', false)
                    ? ': ' . brhg2016_get_item_meta_singles('sub_title', false)
                    : '';

                $list_rows .= "<td class='event-list__cell event-list__cell--title'>\n
                        <a href='$link'>$title$sub_title</a>\n
                    </td>\n";

                if ($atts['theme']) {
                    $theme = brhg2016_get_item_meta_singles('event_theme', false);
                    $list_rows .= "<td class='event-list__cell event-list__cell--theme'>$theme</td>\n";
                }

                if ($atts['with']) {
                    $speakers = brhg2016_get_item_connected('speakers', false);
                    $list_rows .= "<td class='event-list__cell event-list__cell--with'>$speakers</td>\n";
                }

                if ($atts['venue']) {
                    $venue = brhg2016_get_item_connected('venues', false);
                    $list_rows .= "<td class='event-list__cell event-list__cell--venue'>$venue</td>\n";
                }

                if ($atts['location']) {
                    $location = brhg2016_get_item_meta_singles('location', false);
                    $list_rows .=  "<td class='event-list__cell event-list__cell--location'>$location</td>\n";
                }

                $list_rows .= "</tr>\n";
            }

        endwhile;

        // Make the table header cells
        if ($atts['date']) {
            $header_rows .= "<th class='event-list__head event-list__head--date'>Date</th>\n";
            $col_count++;
        }

        if ($atts['time']) {
            $header_rows .= "<th class='event-list__head event-list__head--time'>Time</th>\n";
            $col_count++;
        }

        $header_rows .= "<th class='event-list__head event-list__head--title'>Title</th>\n";
        $col_count++;

        if ($atts['theme']) {
            $header_rows .= "<th class='event-list__head event-list__head--theme'>Theme</th>\n";
            $col_count++;
        }

        if ($atts['with']) {
            $header_rows .= "<th class='event-list__head event-list__head--with'>With</th>\n";
            $col_count++;
        }

        if ($atts['location']) {
            $header_rows .= "<th class='event-list__head event-list__head--location'>Location</th>\n";
            $col_count++;
        }

        if ($atts['venue']) {
            $header_rows .= "<th class='event-list__head event-list__head--venue'>venue</th>\n";
            $col_count++;
        }

        // Build the table
        $list_output = sprintf(
            "<div class='event-list event-list--cols-%s'>\n
                %s\n
                <p class='event-list__scroll'>(Drag left/right)</p>\n
                <div class='event-list__table-wrap'>\n
                    <table class='event-list__table'>\n
                        <thead>\n
                            <tr>\n
                                %s\n
                            </tr>\n
                        </thead>\n
                            %s\n
                    </table>\n
                </div>\n
            </div>\n",
            $col_count,
            ($atts['title'])
                ? "<h3 class='event-list__title'>{$atts['title']}</h3>\n"
                : '',
            $header_rows,
            $list_rows
        );

    endif;

    // Reset $post to the Event Series so that other shortcodes will work.
    wp_reset_postdata();

    return $list_output;
}
