<?php

/**
 * This file contains all of the layout function used in the theme
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 *
 * $GLOBALS['schema'] initially declared in functions.php
 */

/**
 * Makes an excerpt with a custom length from the_content().
 * The excerpt is trimmed to the nearest whole word.
 * 
 * @param int        $characters the desired number of characters
 * @param string     $end_string the string to add to the end of the excerpt
 *
 * @return string    $content the trimmed content
 */
function brhg2016_custom_excerpt($characters = 200, $end_string = '[&hellip;]') {
    $content = get_the_content();
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);

    // Strip out any urls e.g. youtube urls to be embeded
    $content = brhg2016_strip_urls($content);

    // remove multiple white spaces, including breaking characters ** including &nbsp; using u modifier **
    $content = preg_replace('~\s{2,}~u', ' ', $content);
    // remove white space from start and finish
    $content = trim($content);

    // Trim the excerpt
    $content = brhg2016_trim_things($content, $characters, $end_string);

    return $content;
}

/**
 * Trims a post title to a custom length.
 * The title is trimmed to the nearest whole word.  
 * 
 * @param int        $characters the desired number of characters
 * @param string     $end_string the string to add to the end of the excerpt
 *
 * @return string    $title the trimmed content
 */
function brhg2016_custom_title($characters = 50, $end_string = '[&hellip;]') {
    $title = get_the_title();

    if ($characters <= 0) {
        return $title;
    }

    $title  = brhg2016_trim_things($title, $characters, $end_string);

    return $title;
}

/**
 * Trims a string to a number of characters and then to the nearest whole word.  
 * Used by brhg2016_custom_excerpt() and brhg2016_custom_title()
 * 
 * @param int        $characters the desired number of characters
 * @param string     $end_string the string to add to the end of the excerpt, e.g. [...]
 *
 * @return string    $out the trimmed excerpt
 */
function brhg2016_trim_things($content, $characters, $end_string = '[&hellip;]') {
    if (mb_strlen($content) > $characters) {
        // trim to the required length
        $trimmed = mb_substr($content, 0, $characters + 1, 'UTF-8');
        // trim to the nearest whole word i.e. to the last occurrence of a space
        $trimmed = mb_strrchr($trimmed, ' ', true,  'UTF-8');
        $out = "$trimmed $end_string";
    } else {
        $out = $content;
    }
    return $out;
}

/**
 * Strips any urls out of the content.  
 * Used by brhg2016_custom_excerpt()
 * 
 * @param string     $content the content from which to remove the urls
 * 
 * @return string    $out the trimmed excerpt
 */
function brhg2016_strip_urls($content) {
    $urls = brhg2016_find_urls($content);

    // Strip the urls out
    if (!empty($urls)) {
        foreach ($urls as $url) {
            $content = str_replace($url, '', $content);
        }
    }
    return $content;
}

/** 
 * Finds urls in passed content
 * Used by brhg2016_strip_urls
 *
 * @param string     $content the passed content
 *
 * @return array     $urls an array of the urls found in $content
 */
function brhg2016_find_urls($content) {
    // WP function that returns an array with the urls contained in the content
    $urls = wp_extract_urls($content);
    // Fix the fact that wp_extract_urls() sometimes returns bits of dates
    if (!empty($urls)) {
        foreach ($urls as $key => $url) {
            if (substr($url, 0, 4) !== "http") {
                unset($urls[$key]);
            }
        }
    }

    return $urls;
}

/**
 * Finds unlinked URLs on their own line in content, extracts the domain name, and filters against video site names.
 * 
 * @return array The filtered URLs that.
 */
function brhg2024_find_video_urls() {
    global $post;

    if (!$post) {
        return false;
    }

    // In the content, find urls on their own lines. No sub-patterns captured.
    // See WP autoembed() in class-wp-embed.php.
    $video_urls = preg_match_all(
        '|^(?:\s*)(?:https?://[^\s<>"]+)(?:\s*)$|im',
        get_the_content($post),
        $matches
    );

    // Set up the filter of allowed video sites. 'youtu' is for 'youtu.be' URLs
    $filter = array('youtube', 'youtu', 'vimeo', 'dailymotion');
    $filtered_urls = array();

    // No video urls found. No sub-patterns captured, all URLs will be in $matches[0].
    if ($video_urls == 0 || empty($matches[0])) {
        return false;
    }

    foreach ($matches[0] as $url) {
        // Isolate the domain name. Will return example.com, or subdomain.example.com
        preg_match_all(
            '/^(?:http(?:s)?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n?]+)/im',
            $url,
            $result,
            PREG_SET_ORDER
        );

        if (empty($result[0])) {
            continue;
        }

        // Test domain name against filter
        foreach ($filter as $site) {
            if (! empty($result[0][1]) && strpos($result[0][1], $site) !== false) {
                $filtered_urls[] = $url;
            }
        }
    }

    if ($video_urls === 1 && !empty($filtered_urls)) {
        return $filtered_urls;
    } else {
        return false;
    }
}

/**
 * Gets the embed video from posts for archive pages
 *
 * @param int        $number the max number of videos to get 
 *
 * @return array|bool   An array of video embeds or false if there are not. Each item in the array is an iframe.
 *
 */
function brhg2016_get_vids(int $number = 1) {
    $content = get_the_content();

    // Get all video urls from the content
    $video_urls = brhg2024_find_video_urls($content);

    $embed_list = array();

    if ($video_urls) {
        // If possible get the embed codes
        foreach ($video_urls as $url) {
            $embed = wp_oembed_get($url, '');
            // If the url produces an embed put it in the array
            if ($embed) {
                $embed_list[] = $embed;
            }
        }
    }

    if (!empty($embed_list)) {
        snt_dump($embed_list);
        // Echo the embedded videos
        $output = array();
        for ($n = 1; $n <= $number; $n++) {
            $output[] = $embed_list[$n - 1];
        }

        return $output;
    } else {
        // Return 0 to show no videos were found
        return 0;
    }
}

/**
 * Get page titles
 * Custom Query vars are set in functions/custom_query.php
 *
 */
function brhg2016_get_page_title() {
    global $post;

    if (is_archive() || is_search() || is_home() || get_query_var('special_url') === 'radical-history-listings') {
        global $wp_query;

        $main_title = '';
        $sub_title = '';

        $main_title = get_query_var('page_title', '');


        if (is_tax() || is_category() || is_tag()) {
            $sub_title = ': ' . $wp_query->get_queried_object()->name;
        }

        echo $main_title . $sub_title;
    } elseif (is_singular()) {
        $title_out = single_post_title('', false);
        echo $title_out;
        $GLOBALS['schema'][$post->ID]['page_title'] = $title_out;
    } elseif (is_404()) {
        $main_title = "Big Fat 404";

        echo $main_title;
    }
}


/**
 * Get archive page intros
 * Custom Query vars are set in functions/custom_query.php
 *
 */
function brhg2016_get_intro() {
    $intro_id = get_query_var('intro_text', 'Not Set');

    if ($intro_id != 'Not Set') {
        $intro_text = get_post(intval($intro_id));
        $intro_text = apply_filters('the_content', $intro_text->post_content);
        echo $intro_text;
    }
}


/**
 * Get archive page intro text edit link
 *
 */
function brhg2016_intro_test_edit_link() {
    $link = get_edit_post_link(get_query_var('intro_text', 'Not Set'));
    echo ($link == 'none') ? '#' : $link;
}


/**
 * Construct the archive filter with BS classes
 *
 * @param string     $list make the filter as a dropdown button or a string of links
 * @param string     $seperator the seperator to use if the filter is a string of links
 *
 */
function brhg2016_archive_filter($list = 'button', $separator = ' * ') {
    // Create a dropdown button or a string of links?
    $list = ($list === 'button') ? 'list' : '';

    // Get the taxonomy used to create the filter, query var type_tax set in functions/custom_query.php
    $type_tax = get_query_var('type_tax', '');

    // If this archive page has no filter, bail
    if (!$type_tax) {
        return 0;
    }

    $name = get_query_var('page_title', '');

    // 'show_option_all' creates the link to display all articles
    $args = array(
        'taxonomy'          => $type_tax,
        'title_li'          => '',
        'orderby'           => 'count',
        'order'             => 'DESC',
        'echo'              => 0,
        'show_option_all'   => 'All ' . $name,
        'style'             => $list,
    );

    if ($type_tax === 'category') {
        $args['orderby']    = 'name';
        $args['order']      = 'ASC';
    }

    // Get the links, if 'style' => 'list' this is a list without <ul> or <ol> tags
    $filter = wp_list_categories($args);


    // Correct the 'All' link which wp_list_categories() gives the home page url
    switch ($type_tax) {
        case 'category':
            $new_url = site_url('subject-index');
            $pattern = "%" . site_url() . "%";
            $filter = preg_replace($pattern, $new_url, $filter, 1);
            break;

        case 'listing_type':
            $new_url = site_url('radical-history-listings');
            $pattern = "%" . site_url() . "%";
            $filter = preg_replace($pattern, $new_url, $filter, 1);
            break;
    }

    if ($list === 'list' && $filter) {
        $filter = "<nav class='archive-filter' aria-label='Filter by type'>\n
                        <button type='button' class='archive-filter__btn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>\n
                            Filter by Type <span class='caret'></span>\n
                        </button>\n 
                        <ul class='archive-filter__dropdown'>$filter</ul>\n 
                    </nav>";
    } else {
        $filter = str_replace('<br />', '', $filter);
        $filter = preg_replace('~(</a>\s*<a)~', '</a>' . $separator . '<a', $filter);
    }

    echo $filter;
}

/**
 * The classnames for the brhg2016_make_conrib_alpha_list() and brhg2016_archive_pagination()
 */
function brhg2025_pagination_classes() {
    $base_class = "archive-p8n";
    return array(
        "base_class"            => $base_class,
        "wrap_class"            => $base_class . "__wrap",
        "ul_class"              => $base_class . "__ul",
        "li_class"              => $base_class . "__li",
        "li_current_class"      => $base_class . "__li--current",
        "li_dots_class"         => $base_class . "__li--dots",
        "li_next_class"         => $base_class . "__li--next",
        "li_prev_class"         => $base_class . "__li--prev",
        "link_class"            => $base_class . "__link",
        "no_link_class"         => "no-link",
    );
}

/**
 * Makes the alphabet list for the contributors
 * 
 * Uses the transient data stored from contrib_alphabet.php
 *
 */
function brhg2016_make_conrib_alpha_list() {
    $alpha_list = "";

    extract(brhg2025_pagination_classes());

    foreach (range('a', 'z') as $letter) :
        $link =  (is_string(get_term_link($letter, 'contrib_alpha'))) ? get_term_link($letter, 'contrib_alpha') : null;
        $current_query_var = ($letter == get_query_var('contrib_alpha')) ? true : false;

        $alpha_list .= sprintf(
            "<li class='{$li_class}%1\$s'><%2\$s class='%3\$s'>$letter</%4\$s></li>\n",
            ($current_query_var) ? " {$li_current_class}" : "",
            (isset($link) && !$current_query_var) ? "a href='$link'" : "span",
            (isset($link) && !$current_query_var) ? "$link_class" : "$no_link_class",
            (isset($link) && !$current_query_var) ? "a" : "span"
        );
    endforeach;
    // Finish the list
    $alpha_list_output = "
        <nav class='{$base_class}__nav {$base_class}__nav--alpha' aria-label='Contributors alphabetical pagination'>\n
            <ul class='$ul_class'>\n
                $alpha_list
            </ul>\n
        </nav>\n";

    return $alpha_list_output;
}

/**
 * Pagination for archive pages
 * With BS classes
 *
 */
function brhg2016_archive_pagination($args = '') {
    $comments = false;

    if (is_singular()) {
        $comments = true;
        $pagination = paginate_comments_links(array('type' => 'array'));
    } else {
        $defaults = array(
            'prev_text'          => '&laquo;',
            'next_text'          => '&raquo;',
            'before_page_number' => '<span class="sr-only">' . __('Page', 'brhg2016') . ' </span>',
            'type'               => 'array',
            'mid_size'           => '4',
            'screen_reader_text' => 'Archive page navigation'
        );

        $args = wp_parse_args($args, $defaults);
        $pagination = paginate_links($args);
    }

    if (!is_array($pagination)) {
        return;
    }

    extract(brhg2025_pagination_classes());

    $pagination_list_items = '';

    foreach ($pagination as $page) {
        $li = '';
        if (str_contains($page, "aria-current")) {
            $li .= "<li class='$li_class {$li_current_class}'>" . substr_replace($page, $no_link_class, 33, 20) . "</li>\n";
        } elseif (str_contains($page, "dots")) {
            $li .= "<li class='$li_class {$li_dots_class}'>" . substr_replace($page, $no_link_class, 13, 17) . "</li>\n";
        } elseif (str_contains($page, "next")) {
            $li_new = "<li class='$li_class {$li_next_class}'>" . substr_replace($page, $link_class, 10, 17) . "</li>\n";
            $li .= str_replace("Next &raquo;", '&raquo;', $li_new);
        } elseif (str_contains($page, "prev")) {
            $li_new = "<li class='$li_class {$li_prev_class}'>" . substr_replace($page, $link_class, 10, 17) . "</li>\n";
            $li .= str_replace("&laquo; Previous", '&laquo;', $li_new);
        } else {
            $li .= "<li class='$li_class'>" . substr_replace($page, $link_class, 10, 12) . "</li>\n";
        }

        $pagination_list_items .= $li;
    }

    $pagination_html = sprintf(
        "<nav class='%s' aria-label='Archive pagination'>
            %s
            <ul class='%s'>
               %s
            </ul>
        </nav>",
        $base_class . "__nav",
        $comments ? "<div class='{$base_class}__nav-title'>More comments</div>" : '',
        $ul_class,
        $pagination_list_items
    );

    return $pagination_html;
}


/**
 * Gets the Archive page thumbs
 * For events it will get the featured for the event series or make a 'missing thumb'
 * for other post types it will get the featured image  or make a 'missing thumb'
 * 
 * @param string     $size the image size to use for the thumb
 * @param boolean    $echo if true the thumbnail will be 
 *
 * @return boolean   With $echo = false: true is returned if a featured image exists, 
 *                   false is returned if 'missing thumb' will be echoed with $echo set to true
 */
function brhg2016_archive_thumb($size = 'big_thumb', $echo = true) {
    global $post;
    $thumb_img_class = "archive-item-content__thumb-img";

    //make events use the thumbnail from the event series
    if (get_post_type() == 'events') :
        $connected_series = '';

        //get the ID of the connected Event Series    
        if (isset($post->series[0])) {
            $connected_series = $post->series[0]->ID;
        } elseif (is_singular('project')) {
            $connected_series ??= p2p_type('events_to_series')->get_connected($post->ID)->posts[0]->ID;
        } else {
            $connected_series = 'no series';
        }

        // Not a BRHG event
        if (get_post_meta($post->ID, 'brhg_event_filter', true) == 'other'):

            if ($echo === true) {

                brhg2016_archive_missing_thumb('Not A BRHG Event');
            } else {

                return 'text';
            }

        // if it is a brhg event but there is no connected event series
        elseif (get_post_meta($post->ID, 'brhg_event_filter', true) == 'brhg' && $connected_series == 'no series') :

            if ($echo === true) {
                brhg2016_archive_missing_thumb('Not In An Event Series');
            } else {

                return 'text';
            }

        else:

            // if there is a connected Event Series but it has no featured image
            if (!has_post_thumbnail($connected_series)) :

                if ($echo === true) {
                    brhg2016_archive_missing_thumb(get_the_title($connected_series));
                } else {
                    return 'text';
                }

            // if there is a connected Event Series and it has a featured image
            else:

                $thumb_attr = array(
                    'class' => $thumb_img_class,
                );

                if ($echo === true) {
                    echo get_the_post_thumbnail($connected_series, $size, $thumb_attr);
                } else {
                    return 'image';
                }

            endif;
        endif;
    else:
        // otherwise if not an event get featured image thumb

        if (!has_post_thumbnail()) {

            if ($echo === true) {

                //brhg2016_archive_missing_thumb(get_the_title());
            } else {

                return false;
            }
        } else {

            $thumb_attr = array(

                'class' => $thumb_img_class,

            );

            if ($echo === true) {

                echo the_post_thumbnail($size, $thumb_attr);
            } else {

                return 'image';
            }
        }
    endif;
}


/**
 * Archive page missing thumbs
 *
 * Used by brhg2016_archive_missing_thumb()
 *
 * @param string $text The text to over lay on the blank thumb
 */
function brhg2016_archive_missing_thumb($text) {

    $link_tag_open = "";
    $link_tag_close = "";

    /*     // Make missing thumb a link for pamphlet and event series archive pages.
    if (is_post_type_archive('pamphlets') || is_post_type_archive('event_series')) {
        $link_tag_open = sprintf(
            "<a href ='%s' title='%s' class='archive-item-missing-thumb-link'>",
            get_permalink(),
            the_title_attribute(array('echo' => false))
        );
        $link_tag_close = "</a>";
    }

    $missing_thumb = sprintf(
        "%s\n
                                    <img src='%s' width='212' height='300' class='archive-item-thumb' alt='transparent fiddle' />\n
                                    <span class='archive-item-missing-thumb-text'>%s</span>\n
                                %s\n",
        $link_tag_open,
        get_stylesheet_directory_uri() . '/images/transparent-212x300.png',
        $text,
        $link_tag_close
    ); */

    $missing_thumb_replacement_text = "<div class='archive-item-content__missing-thumb-text'>$text</div>";

    echo  $missing_thumb_replacement_text;
}


/**
 * On archive pages sends the $post global to brhg2016_item_data() to get the details
 * These details include the 'page_tile' which is used as the subject
 * and the 'type_tax' which is the taxonomy used to find the sub-section on archive pages
 * brhg2016_item_data() is in custom_query.php
 * Used by brhg2016_get_item_section() and brhg2016_get_item_sub_section()
 *
 * @global object    $post WP_Post object
 *
 * @return array     $details the details for the archive page item in question
 */
function brhg2016_get_item_details() {
    global $post;

    $details = brhg2016_item_data($post);

    return $details;
}


/**
 * Gets the section and section link for the item details list
 * Used on single posts and for each item on an archive page
 * 
 * @param boolean    $echo whether to echo the section and link or return true/false
 *
 * @return boolean   With $echo = false: true is returned if a section is found, false if there is no section
 */
function brhg2016_get_item_section($echo = true) {
    global $post;

    $details = brhg2016_get_item_details();

    if (array_key_exists('page_title', $details)) {
        $section = sprintf(
            "%s%s%s",
            // Remember Contributors archive redirects to contrib_alpha taxonomy page
            ($details['page_title'] === 'Venues') ? '' : "<a href='" . get_post_type_archive_link(get_post_type()) . "'>",
            $details['page_title'],
            ($details['page_title'] === 'Venues') ? '' : "</a>"
        );

        $GLOBALS['schema'][$post->ID]['section'] = wp_strip_all_tags($section, true);

        if ($echo === true) {
            echo $section;
        } else {
            return $section;
        }
    }
    return false;
}

/**
 * Gets the sub-section and link for the item details
 * Used on single posts and for each item on an archive page
 *
 * @param boolean    $echo whether to echo the section and link or return true/false
 * @global object    $post WP_Post object
 *
 * @return boolean   With $echo = false: true is returned if a sub-section is found, false if there is no sub-section
 */
function brhg2016_get_item_sub_section($echo = true) {
    global $post;

    $details = brhg2016_get_item_details();

    if (array_key_exists('type_tax', $details)) {
        $sub_section = get_the_term_list($post->ID, $details['type_tax'], '', ', ', '');

        $GLOBALS['schema'][$post->ID]['sub_section'] = wp_strip_all_tags($sub_section, true);

        if ($echo === true) {
            echo $sub_section;
        } else {
            return $sub_section;
        }
    } else {
        return false;
    }
}

/**
 * Gets the project and link for an item
 *
 * @param boolean    $echo whether to echo the section and link or return true/false
 * @global object    $post WP_Post object
 *
 * @return boolean   With $echo = false: true is returned if a project is found, false if there is no project
 */
function brhg2016_get_item_project($echo = true) {
    global $post;

    $projects = get_post_meta($post->ID, 'brhg2016_project');

    if (is_array($projects) && !empty($projects)) {
        foreach ($projects as $key => $project) {
            if ($project == 0) {
                continue;
            }

            $project_out = sprintf(
                "<a href='%s'>%s</a>%s",
                get_permalink($project),
                get_the_title($project),
                ($key + 1 < count($projects)) ? ', ' : ''
            );

            $GLOBALS['schema'][$post->ID]['project'][$project] = wp_strip_all_tags($project_out, true);

            if ($echo === true) {
                echo $project_out;
            } else {
                return $project_out;
            }
        }
    }
    return false;
}


/**
 * Gets the Event Series dates
 *
 * Remember the time stamps are made when the post is saved
 *
 * @param boolean    $echo whether to echo the section and link or return true/false
 *
 * @return boolean   With $echo = false: true is returned if there are event-series dates, 
 *                   false if there are no time or the item is not an event series
 */
function brhg2016_event_series_dates($echo = true, $connected = '', $event_id = '') {
    if (get_post_type() !== 'event_series' && get_post_type() !== 'events') {
        return false;
    }

    global $post;

    $series = (get_post_type() === 'event_series') ? $post : $connected;

    $event_series_date = array(
        // The timestamps are generated when an Event Series is saved
        'series_start_date_stamp' => ($series_start_time_stamp = get_post_meta($series->ID, 'series_start_date_stamp', true)),
        'series_end_date_stamp' => ($series_end_time_stamp = get_post_meta($series->ID, 'series_end_date_stamp', true)),
        // The raw date value from the Event Series is used as a Double Check below
        'series_start_time' => get_post_meta($series->ID, 'series_start_date', true),
        'series_end_time' => get_post_meta($series->ID, 'series_end_date', true),

        'series_start_human_date' => ($series_start_time_stamp) ? date('l jS F', $series_start_time_stamp) : '',
        'series_end_human_date' => ($series_end_time_stamp) ? date('l jS F', $series_end_time_stamp)  :  '',

        'series_start_human_year' => ($series_start_time_stamp) ? date('Y', $series_start_time_stamp) : '',
        'series_end_human_year' => ($series_end_time_stamp) ? date('Y', $series_end_time_stamp)  :  '',

        'series_start_datetime' => ($series_start_time_stamp) ? date("c", $series_start_time_stamp) : '',
        'series_end_datetime' => ($series_end_time_stamp) ? date("c", $series_end_time_stamp)  :  ''
    );

    if (!$event_series_date['series_start_date_stamp']) {
        return false;
    }

    $GLOBALS['schema'][$event_id]['series_start_datetime'] = $event_series_date['series_start_datetime'];
    $GLOBALS['schema'][$event_id]['series_end_datetime'] = (empty($event_series_date['series_end_datetime'])) ? '' : $event_series_date['series_end_datetime'];

    # We can bail here if this is an Event because we only need the schema.
    if (get_post_type() === 'events') {
        return;
    }

    if ($echo === true) {
        # Check that the date has not been deleted form the event series without deleting the timestamp
        if (!empty($event_series_date['series_end_time'])) {
            # Format the end date
            $end = sprintf(
                " to <time datetime='%s'>%s, %s</time>",
                $event_series_date['series_end_datetime'],
                $event_series_date['series_end_human_date'],
                $event_series_date['series_end_human_year']
            );
        } else {
            $end = null;
        }

        # Format the start date
        printf(
            "<time datetime='%s'>%s%s</time>%s",
            $event_series_date['series_start_datetime'],
            $event_series_date['series_start_human_date'],
            ($event_series_date['series_start_human_year'] ===  $event_series_date['series_end_human_year']
                && $event_series_date['series_start_datetime'] !== $event_series_date['series_end_datetime']) ?
                '' : ', ' . $event_series_date['series_start_human_year'],
            (empty($end)) ? '' : $end
        );
    } else {
        return true;
    }
}


/**
 * Retrieves the single Event dates and times
 * 
 * Remember the time stamps are made when the post is saved
 *
 * Includes the original meta box entries as well as the time stamps
 *
 * @param int        $id the id of the event
 * @global object    $post WP_Post object 
 * @global array     $event_time_date all the date and time data for the event.
 *                   Made global so that it is only generated once even though the
 *                   function is called twice per event: once to check the times exist
 *                   and once to return the data.
 * 
 * @return array     $event_time-date an array with all the event date and time data
 */
function brhg2016_get_item_timestamps($id) {
    if (get_post_type($id) !== 'events' || is_admin()) {
        return;
    }

    global $post;

    global $wp_query;

    // Make sure $event_time_date is only set once per archive item, despite it being called twice.
    // Once to check if a value is returned and second to return a value.
    global $event_time_date;

    if (!isset($event_time_date) || $event_time_date['post_id'] !== $id) {
        $event_time_date = array(
            'post_id' => $post->ID,
            // Timestamp data generated when an Event is saved
            'start_time_stamp'  => (int)($start_time_stamp = get_post_meta($post->ID, 'start_time_stamp', true)),
            'end_time_stamp'    => (int)($end_time_stamp = get_post_meta($post->ID, 'end_time_stamp', true)),

            // The raw date value from the Event Series is used as a Double Check below
            // Each Event has to have a start date
            'start_time'  => get_post_meta($post->ID, 'start_time', true),
            'end_date'  => get_post_meta($post->ID, 'end_date', true),
            'end_time'  => get_post_meta($post->ID, 'end_time', true),

            'start_datetime' => ($start_time_stamp) ? date("c", $start_time_stamp) : '',
            'end_datetime' => ($end_time_stamp) ? date("c", $end_time_stamp) : '',

            'start_human_time' => ($start_time_stamp) ? date('g:ia', $start_time_stamp) : '',
            'end_human_time' => ($end_time_stamp) ? date('g:ia', $end_time_stamp) : '',

            // For full day and month use D jS M
            'start_human_date' => ($start_time_stamp) ? date('D jS M', $start_time_stamp) : '',
            'end_human_date' => ($end_time_stamp) ?  date('D jS M', $end_time_stamp) : ''
        );

        $GLOBALS['schema'][$post->ID]['event_start_datetime'] = $event_time_date['start_datetime'];
        $GLOBALS['schema'][$post->ID]['event_end_datetime'] = (empty($event_time_date['end_time'])) ? '' : $event_time_date['end_datetime'];

        # Force brhg2016_event_series_dates() to run for Events
        # as it is only called for Event Series but we need the series datetime for the schema superEvent.
        # But don't do this on single Event Series pages.
        if (get_query_var('post_type') !== 'event_series') {
            foreach ($post->series as $series) {
                brhg2016_event_series_dates(false, $series, $post->ID);
            }
        }
    }

    return $event_time_date;
}

/**
 * sets the classes for single and archive page item detail blocks.
 * 
 * This needs to be in a function because the variables are shared across multiple
 * template levels and they are set here once.
 * 
 * @return array The classes to use.
 */

function brhg2025_get_details_block_classes() {

    // Used for single items and archive items
    $classes = array(
        "key_class" => "details-block__key",
        "value_class" => "details-block__value"
    );

    return $classes;
}

/**
 * Generated a full datetime for use in schema.org markup.
 * Includes the start date and start time of an event.
 *
 * @param string     $time which datetime to get the 'start' or 'end'.
 * @param boolean    $echo echo or return.
 *
 * @return boolean   If false if no date is found, or if $echo = false returns true if a date is found.
 */
function brhg2016_get_schema_datetime($time = 'start', $echo = true) {

    global $post;

    $datetime = brhg2016_get_item_timestamps($post->ID);

    if ($time === 'start' && $datetime[$time . '_time_stamp']) {
        $datetime = date('c', $datetime[$time . '_time_stamp']);
    } else {
        return false;
    }

    if ($echo === true) {
        echo $datetime;
    } elseif ($echo === false) {
        return true;
    }
}


/**
 * Formats the start and finish time for events.
 * 
 * Use original entries to double check that the original value in the metabox 
 * has not been deleted without the timestamp being deleted as well.
 * 
 * @param  boolean   $start_time_only When true, only the formatted start time is returned.
 *
 * @global object    $post WP_Post object.
 *
 * @return string    A formatted string with the start and finish times.
 */
function brhg2016_get_item_event_time($start_time_only = false) {
    global $post;

    $event_times = brhg2016_get_item_timestamps($post->ID);

    // If brhg2016_get_item_timestamps() returns nothing
    // Note it will return something if a start date is set, even if no start time is set
    if (!isset($event_times) || empty($event_times['start_time_stamp'])) {
        return false;
    }

    // If there is a start date, but no start time.
    if (empty($event_times['start_time'])) {
        return false;
    }

    // Format the start time
    if ($event_times['start_time']) {
        $start_time = sprintf(
            "<time datetime='%s'>%s</time>",
            $event_times['start_datetime'],
            $event_times['start_human_time']
        );
    }

    // Format the end time
    if ($event_times['end_time']) {
        $end_time = sprintf(
            "<time datetime='%s'>%s</time>",
            $event_times['end_datetime'],
            $event_times['end_human_time']
        );
    }

    // Format the both together
    return sprintf(
        '%1$s %2$s %3$s %2$s %4$s',
        (isset($start_time)) ? $start_time : '',
        (is_singular('event_series') && isset($end_time) && $start_time_only === false) ? "<br class='event-time-date-break'>" :  '',
        (isset($end_time) && $start_time_only === false) ? " to " : '',
        (isset($end_time) && $start_time_only === false) ? $end_time : ''
    );
}

/**
 * Formats the event dates. 
 *
 * Use original entries to double check that the original value in the metabox 
 * has not been deleted without the timestamp being deleted as well.
 *
 * @param boolean    $year Include the year in the date output?
 * @global object    $WP_Post object
 *
 * @return string    The formatted event dates
 */
function brhg2016_get_item_event_date($year = true) {
    global $post;

    $event_times = brhg2016_get_item_timestamps($post->ID);

    if (!isset($event_times) || empty($event_times)) {
        return;
    }

    /*
    * Start date and year.
    * 
    * Each Event has to have a start date and so it is not checked.
    */
    $start_date = sprintf(
        "<time datetime='%s'>%s</time>",
        $event_times['start_datetime'],
        $event_times['start_human_date']
    );

    $start_year = ($event_times['start_time_stamp']) ? date('Y', $event_times['start_time_stamp']) : '';

    /*
    * End date and year.
    * 
    * The Event might not have an end date or year
    */
    if ($event_times['end_date']) {
        $end_date = sprintf(
            "<time datetime='%s'>%s</time>",
            $event_times['end_datetime'],
            $event_times['end_human_date']
        );

        $end_year = date('Y', $event_times['end_time_stamp']);
    } else {
        $end_year = 'none';
        $end_date = 'none';
    }

    /*
    * Compare the start and end years.
    * 
    *
    */
    if (isset($end_date)) {
        // If the start date = end date assume normal single day event and null the end date and year
        if ($event_times['start_human_date'] === $event_times['end_human_date']) {
            $end_date = 'none';
            $end_year = 'none';
        }

        // For multi-day events which end in the same year they begin, null the end year
        if ($start_year === $end_year) {
            $end_year = 'none';
        }

        // If the end time is in the early hours (before 6am) of the day after they begin assume it is an 'all nighter'
        // Treat this as a single day event and null the end date
        // 86400 sec in a day and 6 hours = 21600 secs. Also % gives modulus (remainder)
        if (($event_times['end_time_stamp'] - $event_times['start_time_stamp']) < 86400 && ($event_times['end_time_stamp'] % 86400) < 21600) {
            $end_date = 'none';
            $end_year = 'none';
        }
    }

    /*
    * Format the Event date.
    * 
    * @param $year dictates if the years are included in the returned date
    */
    if ($year === true) {
        return sprintf(
            "%s%s%s%s",
            (isset($start_date)) ? $start_date : '',
            ($end_year === 'none') ?  '' : ', ' . $start_year,
            ($end_date === 'none') ? '' : ' to ' . $end_date,
            ($end_year === 'none') ?  ', ' . $start_year : ', ' . $end_year
        );
    } else {
        return sprintf(
            "%s%s",
            (isset($start_date)) ? $start_date : '',
            ($end_date === 'none') ? '' : ' to ' . $end_date
        );
    }
}

/**
 * Returns single meta values for posts for each key received in the $args.
 *
 * @param array/string   $args can accept an array of meta value keys to get several different meta values or and a string to get a single meta key value.
 * @param boolean        $echo echo or return
 * @param int            $post_id the ID of the post to use, if null the current $post->ID will be used.
 * @global               WP_Post object.
 *
 * @return array/string  If $args is an array and array is returned, if $args is a string a string is returned.
 */
function brhg2016_get_item_meta_singles($args, $echo = true, $post_id = null) {
    global $post;

    // If a $post_id is passed use it, otherwise use the global $Post->ID
    $post_id = ($post_id) ? $post_id : $post->ID;

    $items = array();

    // If @param $args is a string make an array. If it is an array, keep an array.
    if (is_string($args)) {
        $items[0] = $args;
    } elseif (is_array($args)) {
        $items = $args;
    }

    // Get the meta data
    if (isset($items)) {
        $meta = array();
        foreach ($items as $item) {
            //$meta[$item] = $sub_title = get_post_meta( $post_id, $item, true );
            $meta[$item] = get_post_meta($post_id, $item, true);
            $GLOBALS['schema'][$post->ID][$item . '_meta'] = $meta[$item];
        }

        // If there is only one value of meta data return a string
        if (count($meta) == 1) {
            $meta = implode('', $meta);
        }

        // Echo or return
        if ($echo === true) {
            echo $meta;
        } else {
            return $meta;
        }
    }
}


/**
 * Get a single type of connected item e.g. Speakers connected to an Event
 *
 * Remember that the $post->connected method is added by the post-2-post plugin
 *
 * @param string     $connected - the connected item
 * @param boolean    $echo echo or return.
 * @param boolean    $link whether to include a link tag
 * @param boolean    $postcode include the postcode after a venue name?
 *
 * @return string    If $echo = false a list of the connected item is returned with links,
 *                   if there are no connected items false is retuned.
 */
function brhg2016_get_item_connected($connected, $echo = true, $link = true, $postcode = true) {
    global $post;

    if (!isset($connected)) {
        return false;
    }

    $connected_items = $post->$connected;

    if (empty($connected_items)) {
        $GLOBALS['schema'][$post->ID][$connected . '_connected'] = array();
        return false;
    }

    $out = '';

    foreach ($connected_items as $key => $item) {
        if ($link === true) {
            $out .= sprintf(
                "<a href='%s'>%s%s</a>%s%s",
                get_permalink($item->ID),
                $item->post_title,
                ($connected === 'venues' && $postcode === true) ? ', ' . brhg2016_get_item_connected_details($item, 'venue_postcode') : '',
                ($key + 1 < count($connected_items)) ? ', ' : '',
                // Add a <br> tag after each speaker in the Event Series Programme Table
                (is_singular('event_series') && $key + 1 < count($connected_items)) ? '<br>' : ''
            );

            $GLOBALS['schema'][$post->ID][$connected . '_connected'][$item->ID] = $item->post_title;
            # Add venue details to Schema for event venues
            if ($connected === 'venues') {
                $meta_required = array('venue_postcode', 'venue_address1', 'venue_address2', 'venue_address3', 'venue_city', 'venue_email', 'venue_phone', 'venue_website');

                foreach ($meta_required as $meta) {
                    brhg2016_get_item_connected_details($connected_items[0], $meta);
                }
            }
        } else {
            $out .= sprintf(
                "%s%s",
                $item->post_title,
                ($key + 1 < count($connected_items)) ? ', ' : ''
            );
        }
    }

    if ($echo === true) {
        echo $out;
    } else {
        return $out;
    }
}

/**
 * Get the requested meta details from a single item connected to another item
 * e.g. the postcode (stored as meta) of a venue connected to an event.
 *
 * @param object     $item the connected item, e.g. a venue connected to an event.
 *                   Will be a property of $post object
 * @param string     $meta the meta data required for the connected item
 *
 * @return string/boolean    The meta data or false if  there isn't any
 */
function brhg2016_get_item_connected_details($item, $meta) {

    if (!isset($item)) {
        return false;
    }

    $meta_out =  brhg2016_get_item_meta_singles($meta, false, $item->ID);

    if (isset($meta_out) && $meta_out != '') {
        return $meta_out;
    } else {
        return false;
    }
}

/**
 * Get meta data about a venue for an single Event page
 *
 * @param string     $meta the meta data required
 * @param boolean    $echo echo or return.
 *
 * @return boolean   True if there is a meta value and $echo === false.
 *                   False if there is no venue or no $meta is set
 */
function brhg2016_get_item_connected_venue_details($meta, $echo = true) {

    global $post;

    if (!isset($post->venues[0]) || !isset($meta)) {
        return false;
    }

    // Only use the first venue of there is more than one
    $meta_out = brhg2016_get_item_connected_details($post->venues[0], $meta);

    if ($echo = true) {
        echo $meta_out;
    } else {
        return true;
    }
}


/**
 * Formats the 'post' date for an item.
 *
 */
function brhgh2016_post_date() {
    global $post;

    printf(
        "<span class='published'><time class='value' datetime='%s'>%s</time></span>",
        get_the_date('c'),
        get_the_date('d/m/Y')
    );

    $GLOBALS['schema'][$post->ID]['posted'] = get_the_date('c');
}


/**
 * Formats the 'modified' date for an item.
 *
 */
function brhgh2016_post_modified() {
    global $post;

    echo sprintf(
        "<span class='updated'><time class='value' datetime='%s'>%s</time></span>",
        get_the_modified_date('c'),
        get_the_modified_date('d/m/Y')
    );

    $GLOBALS['schema'][$post->ID]['modified'] = get_the_date('c');
}


/**
 * Add the book cover to after the content of a single Book item.
 * Runs off the the_content filter added by brhg2016_add_single_after_contents() above.
 *
 * @param string     $contents the contents passed by the the_content filter.
 *
 * @return string    The cover images appended to $content.
 */
function brhg2016_add_book_covers($contents) {
    global $post;

    $image = '';

    if (has_post_thumbnail()) {
        $attr = array('alt' => the_title_attribute(array('echo' => false)) . ' Cover');
        $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

        $GLOBALS['schema'][$post->ID]['book_cover_src'] = $src;

        $images = sprintf(
            "<p class='single-covers'><span class='single-book-covers'><a href='%s' title='%s'>%s</a></span></p>",
            $src[0],
            the_title_attribute(array('echo' => false)) . ' Cover',
            get_the_post_thumbnail('', 'big_thumb', $attr)
        );
    }
    return $contents . $images;
}

/**
 * Encode emails addresses so that they are all in ASCII code.
 *
 * @param string $e The emails address
 * 
 * @return string The encoded email address.
 */
function brhg2016_encode_email($e = '') {
    $output = '';

    for ($i = 0; $i < strlen($e); $i++) {
        // In php ord() returns ASCII value of character
        $output .= '&#' . ord($e[$i]) . ';';
    }

    if (!empty($output)) {
        return $output;
    }

    return;
}

/**
 * Tag cloud
 *
 * @param int    $n the number of tags to return. For all, pass 0
 */
function brhg2016_tag_cloud($n = 45) {

    // Change classname on links
    add_filter('wp_tag_cloud', function ($tag_index) {
        return str_replace('tag-cloud-link', 'tag-index__link', $tag_index);
    });

    $tag_args = array(
        'smallest'                  => 16,
        'largest'                   => 34,
        'unit'                      => 'px',
        'number'                    => $n,
        'format'                    => 'flat',
        'separator'                 => "\n",
        'orderby'                   => 'name',
        'order'                     => 'ASC',
        'exclude'                   => null,
        'include'                   => null,
        'topic_count_text_callback' => 'default_topic_count_text',
        'link'                      => 'view',
        'taxonomy'                  => 'post_tag',
        'echo'                      => true,
        'child_of'                  => null, // see Note!
    );

    echo wp_tag_cloud($tag_args);
}

/**
 *
 *
 *
 */
function brhg2016_get_Url() {
    $url  = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['SERVER_NAME'];
    $url .= in_array($_SERVER['SERVER_PORT'], array('80', '443')) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}


/**
 *  Check phone and other small devices
 * 
 * @return boolean Whether a phone device is detected
 */

function brhg2024_check_phone() {
    if (!isset($_SERVER['HTTP_USER_AGENT']))
        return false;

    $matched = false;

    $matched = (
        strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false
        || (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            && strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false)
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false // some kindles
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
    );

    return $matched;
}

/**
 * Gets the main contact email address from the BRHG Details options page.
 */

function brhg2024_get_main_email($encode = false) {

    $email = get_field('brhg_contact_email_address', 'options');

    if (empty($email)) {

        return false;
    }

    return $encode ? brhg2016_encode_email($email) : $email;
}

/**
 * Gets the main shop help email address from the BRHG Details options page.
 */

function brhg2024_get_shop_help_email($encode = false) {

    $email = get_field('shop_help_email_address', 'options');

    if (empty($email)) {

        return false;
    }

    return $encode ? brhg2016_encode_email($email) : $email;
}
