<?php
/*
Plugin Name:  Bookshop
Plugin URI:   
Description:  Version with rewrites
Version:      
Author:       
Author URI:   
License:      GPL2
License URI:  
Text Domain:  
Domain Path:  
*/

/**
 * Make content for the bookshop transaction URL that was processed with Special URLs.
 * This script needs the download.php file to be present in the download folder.
 *
 * This is a callback function for the the_posts filter which runs after the query has been made
 *
 * @param array $posts An array of WP_Post objects found by WP_Query
 * @param WP_Query $query The WP_Query object passed by reference
 * 
 * @return array $posts  An array of WP_Post objects, either as passed and unaltered, passed and altered or completely fabricated
 */

// Where are you book files?
// ABSPATH is the absolute path on the server to the wordpress installation


add_filter('posts_results', function ($posts, $query) {
    // Check that this is the page's main query and that this is from a bookshop sale URL
    if (
        $query->is_main_query()
        //&& isset( $query->query_vars['special_url'] ) ?  $query->query_vars['special_url'] : NULL  === 'bookshop'  
        && isset($query->query_vars['special_url'])
        && $query->query_vars['special_url'] === 'bookshop'
        && isset($query->query_vars['transaction_id'])
    ) {


        /*
         * Add the path to the bookshop downloades here
         *
         * ABSPATH is the path to the Wordpress install directory
         */

        $book_files_dir = ABSPATH . 'downloads/bookshop/';

        /*
         * These are the file types that will be listed
         *
         * The order is the order in which they will be listed
         */
        $book_file_types = array(
            'epub' => 'For eReaders or Apps that are not Kindle.',
            'mobi' => 'Sidedload to a Kindle or Kindle app via email.',
            'azw3' => 'Sidedload to a Kindle or Kindle app via USB.',
            'kfx'  => 'Sidedload to a Kindle or Kindle app via USB with hyphanation.'
        );

        /*
         * This is the id of the page that contains the download instructions
         *
         * Must be an integer
        */
        $instructions_page_id = 9428;






        // get the transaction details
        $trans_details = get_post_meta($posts[0]->ID, 'wpsc_cart_items', true);

        if (!empty($trans_details)) {
            // Order the multidimensional array of items in the transaction using natural 
            //sort on the item_number element in each top level element 
            usort($trans_details, function ($a, $b) {
                return strnatcasecmp($a['item_number'], $b['item_number']);
            });

            // Process each item in the transaction
            foreach ($trans_details as $key => $trans) {

                if (empty($trans['file_url'])) continue; // This is a physical book so bail this loop

                // Get the book file names, there will be several for each book: EPUB, MOBI etc.
                $files = scandir($book_files_dir . $trans['item_number']);

                // Remove . and ..
                array_splice($files, 0, 2);

                if (!empty($files)) {

                    $book_files = array();
                    // Process each book file into an array
                    foreach ($files as $key2 => $file) {

                        $book_files[$key2]['file_name'] = $file;

                        // Get the file extension
                        $pattern = '#[.][a-zA-Z0-9]*$#';
                        preg_match($pattern, $file, $file_type);

                        // Add the file type to the array
                        $book_files[$key2]['file_type'] = str_replace('.', '', $file_type[0]);
                    }

                    $trans_details[$key]['book_files'] = $book_files;
                }
            }

            // Construct the HTML
            $html_out = "<p><strong>Please see the notes below for a fuller description of the which file you need and an explanation of 'sideloading'.</strong></p>\n";

            foreach ($trans_details as $book) {
                // A title for each book - physical and eBook
                $html_out .= "<h3 clas='ebook-title'>{$book['name']}</h3>\n";

                if (isset($book['file_url'])) {

                    // This is an eBook - several file links for each
                    // $book_file_types is the order in which to add then

                    foreach ($book_file_types as $book_type => $type_blurb) {

                        foreach ($book['book_files'] as $files) {

                            if ($files['file_type'] == $book_type) {
                                $html_out .= sprintf(
                                    "<p class='brhg-ebook-type'><a class='brhg-ebook-link' href=%s>%s</a>\n</p><p class='brhg-ebook-type-blurb'>%s</p>\n",
                                    //http://www.example.com/downloads/bookshop/download.php?file=filename
                                    site_url() . '/downloads/bookshop/download.php?file=' . urlencode($book['item_number'] . '/' . $files['file_name']),
                                    esc_html($files['file_name']),
                                    $type_blurb
                                );
                            } else {
                                continue;
                            }
                        }
                    }
                } else {

                    // This is a physical book order - no file links
                    $html_out .= "<p>Physical book. No Downlod file.</p>\n";
                }
            }

            // If there is more than 1 post in the $posts parameter passed to this callback function something has gone wrong 
            if (count($posts) == 1) {
                // Just incase sanitize the HTML we have added
                $allowed = array(
                    'strong'  => array(),
                    'a' => array(
                        'title' => array(),
                        'href' => array(),
                        'class' => array(),
                        'id' => array()
                    ),
                    'h3' => array(
                        'class' => array(),
                        'id' => array()
                    )
                );

                $html_out = wp_kses($html_out, $allowed);

                if (is_integer($instructions_page_id)) {
                    $instructions = get_post($instructions_page_id);
                    $instructions_out = apply_filters('the_content', $instructions->post_content);
                    $html_out .= $instructions_out;
                }

                // Set the post_content of the post that was passed to use from the query        

                $posts[0]->post_title = 'Order ' . $query->query_vars['transaction_id'];
                $posts[0]->post_content = $html_out;
            }
        }
    }

    // Return the $posts array to the the_posts filter
    return $posts;
}, 10, 2);


/**
 * Filter the buyer's notification email
 * The wspsc_buyer_notification_email_body filter is in wordpress-simple-paypal-shopping-cart
 * paypal.php line 273 
 *
 * @param string $body The body of the email
 * 
 * @return string $processed The modified email body
 */
add_filter('wspsc_buyer_notification_email_body', function ($body, $ipn, $items) {

    // If there is an eBook in the order, there will be a download link
    // We don't want it
    $re = '@(Download Link.*)@';
    $processed = preg_replace($re, '', $body, -1, $done);

    // If there was no download link only physical books were bought
    // So we don't want the eBook link
    if ($done == 0) {
        $re2 = '@(^Please follow[\s\S]+/[A-Z0-9]{17})@m';
        $processed = preg_replace($re2, '', $body);
    }

    return $processed;
}, 10, 3);
