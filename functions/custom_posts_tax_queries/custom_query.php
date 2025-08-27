<?php

/**
 * Alter the main queries as needed and provide information for template files
 * Also queries for second loops etc.
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 *
 */

/**
 * Callback function for query_vars filter
 * Set query vars for Title and Intro Text etc
 *
 * @param $vars array passed by the query_vars filter
 *
 * @return $vars array The custom query vars
 */
function brhg2016_query_vars($vars) {
    $vars[] = "intro_text";
    $vars[] = "page_title";
    $vars[] = "special_url";
    $vars[] = "sent_from";
    return $vars;
}

add_filter('query_vars', 'brhg2016_query_vars');


/* Front End Queries
**************************************************************************************************************************************/

/**
 * Callback function for request filter
 * Alters the default queries for front end only (not Admin pages)
 *
 * @param $request The request vars
 *
 * @return $request The altered request vars
 */
function brhg2016_alter_the_query($request) {
    /**
     * Redirects for neater URLs
     * Default for wp_redirect() is 302
     *
     */

    # Redirect brh.org.uk/schools-course-material/
    if ($_SERVER['REQUEST_URI'] == '/school-course-material/') {
        wp_redirect('https://www.brh.org.uk/site/article_type/material-for-schools/');
        exit();
    }

    /**
     * Other 301 redirects
     */

    if ($_SERVER['REQUEST_URI'] === '/site/articles/congratulations-barbados/') {
        wp_redirect('https://www.brh.org.uk/site/2021/12/congratulations-barbados/');
        exit();
    }

    /*
     * Deal with all other URLs that are not to be redirected
     *
     */
    if (array_key_exists('pagename', $request) && !is_admin()) {
        /*
        *  Is this a Page? If so $request['pagename'] will exist and be set to the slug of the page.
        *  If this is not a Page send $request to brhg2016_special_urls() to see if it is a 'special url'
        *  i.e. a fake Page url that is used to deliver custom contents, 
        *  note that the urls do not actually correspond to a Wordpress Page.
        *  If this is a 'special url' Page then brhg2016_special_urls() will replace the original $request,
        *  if this is a real Page brhg2016_special_urls() returns the original $request.
        *  In either case return $request and skip the rest of this function.
        */
        $slugs = brhg2016_get_slug();

        if (!empty($slugs)) {
            $return_request = brhg2016_special_urls($request, $slugs);
        }
    } elseif (!is_admin()) {
        /*
        *  If this is not an Admin page process $request.
        *  Admin page queries are altered by brhg2016_admin_queries(), see below
        */

        // This is for the search filters from search result archive pages and the Search Page itself
        if (array_key_exists('sent_from', $request)) {
            if (isset($request['category_name']) && is_array($request['category_name'])) {
                //$request['category_name'] = (count($request['category_name']) > 1) ? implode(',', $request['category_name']) : $request['category_name'][0];
                $request['category_name'] = implode(',', $request['category_name']);
                if (empty($request['category_name'])) {
                    unset($request['category_name']);
                }
            }

            if (empty($request['post_type'])) {
                unset($request['post_type']);
            }
        }

        /*
        *  Make a dummy query object using no query_vars and parse $request
        *  This object is now empty apart from the query and conditionals
        */
        $dummy_query = new WP_Query();
        $dummy_query->parse_query($request);

        /*
        *  Redirects
        *  Contributors archive page redirects to contrib_alpha taxonomy page so that they are paginated alphabetically.
        *  Check that ?s is not set to stop the search filter messing up if searching for contributors
        */
        if ($dummy_query->is_post_type_archive('contributors') && array_key_exists('s', $request)) {
            wp_redirect(get_term_link('a', 'contrib_alpha'), 301);
            exit;
        }

        /*
        *  Modify the request using brhg2016_request_data_list() via brhg2016_request_data()
        */
        $request_mod = brhg2016_request_data($dummy_query);

        /*
        *  Add the modified request to the original one so that anything that was not modified remains.
        *  Remember that with array_merge: "If the input arrays have the same string keys, 
        *  then the later value for that key will overwrite the previous one."
        *  So, $request_mod values will overwrite $request values for the same key.
        */
        if (is_array($request_mod)) {
            $return_request = array_merge($request, $request_mod);
        } else {
            // If nothing was modified and $request_mod is not an array
            $return_request = $request;
        }
    } else {
        // If this is not a Page url or any other front end request ( e.g. and admin page ) don't modify anything
        $return_request = $request;
    }

    return $return_request;
}

add_filter('request', 'brhg2016_alter_the_query');



/**
 * Isolate the slug, which might be a Special URL from the requested URL
 * Used by brhg2016_special_urls when processing bookshop
 * The 1st part is from WP core parse_resuest() in wp-includes/class-wp.php
 *
 * @return string $special_slug The slug retrieved from the requested URL
 */
function brhg2016_get_slug() {
    $pathinfo         = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    list($pathinfo) = explode('?', $pathinfo);
    $pathinfo         = str_replace('%', '%25', $pathinfo);

    list($req_uri) = explode('?', $_SERVER['REQUEST_URI']);
    $self            = $_SERVER['PHP_SELF'];

    $home_path       = parse_url(home_url(), PHP_URL_PATH);
    $home_path_regex = '';
    if (is_string($home_path) && '' !== $home_path) {
        $home_path       = trim($home_path, '/');
        $home_path_regex = sprintf('|^%s|i', preg_quote($home_path, '|'));
    }

    /*
			 * Trim path info from the end and the leading home path from the front.
			 * For path info requests, this leaves us with the requesting filename, if any.
			 * For 404 requests, this leaves us with the requested permalink.
			 */
    $req_uri  = str_replace($pathinfo, '', $req_uri);
    $req_uri  = trim($req_uri, '/');
    $pathinfo = trim($pathinfo, '/');
    $self     = trim($self, '/');

    if (! empty($home_path_regex)) {
        $req_uri  = preg_replace($home_path_regex, '', $req_uri);
        $req_uri  = trim($req_uri, '/');
    }

    /** 
     * $req_uri might be in a single part e.g. news-feed
     * or multiple parts e.g. news-feed/page/2?
     * Put the 1st part and the whole $req_uri in an array
     */

    $slugs = array();
    // All the slugs e.g. news-feed/page/1
    $slugs[] = $req_uri;
    // The 1st part only e.g. news-feed
    $slugs[] = explode('/', $req_uri)[0];

    return $slugs;
}

/**
 * Get the IDs of publications in collections.
 * 
 * Called from brhg2016_special_urls().
 * 
 * Publication Collections are created on the Publication Collections ACF options page.
 * The $brhg_publication_collections global is also set up, it is used in the template file.
 * 
 * @return array The IDs of publications contained within collections. If there are none such, an empty array.
 */
function brhg2024_get_publication_collections() {
    // field_65e1beca34d66 is the outer ACF repeater, that contains the collections
    $collections_raw = get_field('field_65e1beca34d66', 'options');

    // Collect the IDs of all the items in every collection
    $in_ids = array();

    // Protect foreach
    $collections = is_array($collections_raw) ? $collections_raw : array();

    // Loop outer ACF repeater field: the collections
    foreach ($collections as $collection) {

        // Loop inner ACF repeater: the items within a collection
        $ids = array();

        // Protect foreach
        $collection_items = is_array($collection['pamphlet_collection_items'])
            ? $collection['pamphlet_collection_items']
            : array();

        foreach ($collection_items as $item) {
            $ids[] = $item['publication_collection_item_id'];
        }

        // Add the IDs for this collection into the main $in_ids
        $in_ids = array_merge($in_ids, $ids);

        // Cache an array of the collections to use in the template
        $GLOBALS['brhg_publication_collections'][] = array(
            'publication_collection_title' => $collection['publication_collection_title'],
            'publication_collection_description' => $collection['publication_collection_description'],
            'items' => $ids
        );
    };

    if (empty($GLOBALS['brhg_publication_collections'])) {
        $GLOBALS['brhg_publication_collections'] = new WP_Error('pub-col-page', "No collections");
    }

    return $in_ids;
}


/**
 * Dummy Page urls with 'special' content. Note that these Wordpress 'Pages' do not exist and are used for their url only.
 * What is put on these pages is decided here.
 *
 * These query vars completely overwrite the ones that are passed.
 *
 * Current Dummy Pages with 'special urls' are:
 *    /blog/
 *    /event-diary/
 *    /news-feed/
 *    /subject-index/
 *    /radical-history-listings/
 *    /tag-index/
 *    /bookshop/
 *   /publication-collections/
 *
 * Some 'special urls' need template redirects because they are not Pages, although Wordpress would interpret the url as a page without intervention.
 *
 * 'Special urls' also need the <title> tag contents changing because these are also dependent on the way Wordpress interprets the url.
 *
 * Also, some booleans need to be changed in the request if Wordpress needs to treat the 'special url' page as an Archive Page.
 *
 * @param array $request The array of query vars passed from the url
 *
 * @return array $brhg2016_request The modified vars
 */
function brhg2016_special_urls($request, $slugs) {
    switch ($slugs[1]) {

        case 'blog':
            $brhg2016_request['special_url'] = 'blog';
            $brhg2016_request['page_title'] = __('Blog', 'brhg2016');
            $brhg2016_request['paged'] = $request['paged'] ?? 1;
            /*
            *  No template redirect needed. Without query vars Wordpress assumes this is the blog list page and looks for home.php in the theme.
            *  Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
        */
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            break;

        case 'event-diary':

            $todays_date_stamp = strtotime(date("d F Y"));

            $brhg2016_request['special_url'] = 'event-diary';
            $brhg2016_request['post_type'] = 'events';
            $brhg2016_request['meta_key'] = 'start_time_stamp';
            $brhg2016_request['orderby'] = 'meta_value_num';
            $brhg2016_request['order'] = 'ASC';
            $brhg2016_request['posts_per_archive_page'] = -1;
            $brhg2016_request['meta_query'] = array(
                array(
                    'key' => 'end_time_stamp',
                    'value' => $todays_date_stamp,
                    'compare' => '>=',
                )
            );
            $brhg2016_request['intro_text'] = 1160;
            $brhg2016_request['page_title'] = __('Events Diary', 'brhg2016');
            /*
        *  No template redirect needed. ['post_type'] = 'events' means archive.php is called from the theme.
        *  Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
        */
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            break;

        case 'news-feed':

            $brhg2016_request['paged'] = $request['paged'] ?? 1;
            $brhg2016_request['special_url'] = 'news-feed';
            $brhg2016_request['orderby'] = 'date';
            $brhg2016_request['order'] = 'DESC';
            $brhg2016_request['post_type'] = array('articles', 'books', 'rad_his_listings', 'pamphlets', 'post', 'events', 'event_series');
            $brhg2016_request['posts_per_archive_page'] = 10;
            $brhg2016_request['intro_text'] = 1290;
            $brhg2016_request['page_title'] = __('News Feed', 'brhg2016');
            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2020_reset_conditionals. Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');
            break;

        case 'subject-index':
            $copy_data = brhg2016_request_data_list($section = '', $tax = 'category_name');
            // See brhg2016_template_redirects() below
            $brhg2016_request['special_url'] = 'subject-index';
            $brhg2016_request['type_tax'] = $copy_data['type_tax'];
            $brhg2016_request['intro_text'] = $copy_data['intro_text'];
            $brhg2016_request['page_title'] = $copy_data['page_title'];
            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2020_reset_conditionals. Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');
            break;

        case 'radical-history-listings':
            $copy_data = brhg2016_request_data_list($section = 'rad_his_listings');
            // See brhg2016_template_redirects() below
            //$brhg2016_request['page'] = '';
            //$brhg2016_request['pagename'] = 'radical-history-listings';
            $brhg2016_request['post__in'] = array(0);
            $brhg2016_request['special_url'] = 'radical-history-listings';
            $brhg2016_request['type_tax'] = $copy_data['type_tax'];
            $brhg2016_request['intro_text'] = $copy_data['intro_text'];
            $brhg2016_request['page_title'] = $copy_data['page_title'];
            //$brhg2016_request['posts_per_archive_page'] = -1;        
            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2020_reset_conditionals(). Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');
            break;

        case 'tag-index':
            $brhg2016_request['special_url'] = 'tag-index';
            $brhg2016_request['intro_text'] = 4174;
            $brhg2016_request['page_title'] = 'Tag Index';
            $brhg2016_request['post__in'] = array(0);
            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2020_reset_conditionals(). Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');
            break;

        case 'bookshop':

            // If this is just bookshop not bookshop/transaction_id
            if ($slugs[0] === $slugs[1]) return $request;

            $slug_array = explode('/', $slugs[0]);
            $transaction = end($slug_array);

            $brhg2016_request['special_url'] = 'bookshop';
            $brhg2016_request['page_title'] = 'Your Order';
            $brhg2016_request['meta_key'] = 'wpsc_txn_id';
            $brhg2016_request['meta_value'] = $transaction;
            $brhg2016_request['post_type'] = 'wpsc_cart_orders';
            $brhg2016_request['posts_per_page'] = 1;
            $brhg2016_request['paged'] = 1;

            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2016_special_urls_archive_true(). Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');

            break;

        case 'publication-collections':
            $brhg2016_request['special_url'] = 'publication-collections';
            $brhg2016_request['orderby'] = 'title';
            $brhg2016_request['order'] = 'ASC';
            $brhg2016_request['post_type'] = 'pamphlets';
            $brhg2016_request['posts_per_archive_page'] = -1;
            $brhg2016_request['intro_text'] = 21206;
            $brhg2016_request['page_title'] = __('Publication Collections', 'brhg2016');

            $in_ids = brhg2024_get_publication_collections();

            // Query only the IDs of publications that appear in one or more collections
            // If  post__in is array(), WP assumes this is the home page and returns last 10, so use array(0).
            $brhg2016_request['post__in'] = empty($in_ids) ? array(0) : $in_ids;

            // Temple redirect, archive.php instead of page, using brhg2016_template_redirects(). Runs immediately before WordPress includes the predetermined template file.
            add_filter('template_include', 'brhg2016_template_redirects', 99);
            // Changing the contents of the <title> tag is needed using brhg2016_special_urls_title_tag(). Runs just before the tag is created.
            add_filter('pre_get_document_title', 'brhg2016_special_urls_title_tag', 99);
            // Change boolean values in the $wp_query object to show true for is_archive() using brhg2016_special_urls_archive_true(). Runs after WP object is set up and before get_header() is called.
            add_action('wp', 'brhg2020_reset_conditionals');

            break;

        default:
            return $brhg2016_request = $request;
            break;
    }

    return $brhg2016_request;
}


/**
 *
 * Sorts out the $section and $tax used by brhg2016_request_data_list( $section, $tax )
 * to get the data which is used to modify the main query request.
 *
 * Called by brhg2016_alter_the_query(), above.
 *
 * @param WP_Query object $query The dummy query generated by brhg2016_alter_the_query() above
 *
 * @return array Containing the data, generated by brhg2016_request_data_list(), used to modify the main page request
 */
function brhg2016_request_data($query) {

    /*
    *  Set up $section and $tax for conditionals in brhg2016_request_data_list().
    *  $query will be a WP_Query object for the main query and a WP_Post for a function, so we can check this is the main query.
    */
    if (isset($query) && is_a($query, 'WP_Query')) {

        // Search is first so that the search filter does not mess things up
        if ($query->is_search()) {
            $section = 'search';
            $tax = 'none';
        } elseif (is_home()) {
            $section = 'post';
            $tax = 'none';
        } elseif ($query->is_post_type_archive()) {
            // This is for post types
            $section = $query->query['post_type'];
            $tax = 'none';
        } elseif ($query->is_tax() || $query->is_tag() || $query->is_category()) {
            // This is for taxonomies: including tags and categories.
            $section = 'none';
            $tax = $query->query;
        } else {
            $section = 'fail';
            $tax =  'fail';
        }
    }

    return brhg2016_request_data_list($section, $tax, $query);
}


/*
* Sorts out the $section and $tax used by brhg2016_request_data_list( $section, $tax )
* to get the data which is used to build meta data for single items.
*
* Called from \functions\utility_functions.php
*
* Single items which need to have their meta data displayed will be single posts from various post types and so need $section.
* Single taxonomy terms do not need meta data displaying and so do not need $tax.
*
* @param WP_Post object $post
*
* @return array Containing the data, generated by brhg2016_request_data_list(), used to make meta data for an item
*
*/
function brhg2016_item_data($post) {
    if (isset($post) && is_a($post, 'WP_Post')) {
        $section = $post->post_type;
        $tax = 'none';
    }

    return brhg2016_request_data_list($section, $tax);
}

/*
* Generates data used for modifying main page requests or making item meta data
*
* Called by brhg2016_request_data() for the main query on a page, see above, and from utility_functions.php to display single item meta.
*
* @param string $section The post type
* @param string/array $tax The taxonomy
*
* @return array The requested data
*
*/
function brhg2016_request_data_list($section = 'none', $tax = 'none', $query = null) {

    // $tax_test needs to be an array
    $tax_test = array();
    if (is_string($tax)) {
        $tax_test[$tax] = $tax;
    } elseif (is_array($tax)) {
        $tax_test = $tax;
    }

    // Now get the request data
    $brhg2016_request = array();

    # Search
    if ($section == 'search') :
        $brhg2016_request['intro_text'] = 7538;
        $brhg2016_request['page_title'] = __('Search Results', 'brhg2016');

    # List Event Series by start date
    elseif ($section == 'event_series') :
        $brhg2016_request['meta_key'] = 'series_start_date_stamp';
        $brhg2016_request['orderby'] = 'meta_value_num';
        $brhg2016_request['order'] = 'DESC';
        $brhg2016_request['posts_per_archive_page'] = -1;
        $brhg2016_request['intro_text'] = 1138;
        $brhg2016_request['page_title'] = __('Event Series', 'brhg2016');

    # List Event by start date for chronological archive page. Other people's events are filtered out.
    elseif ($section == 'events') :
        $brhg2016_request['meta_key'] = 'start_time_stamp';
        $brhg2016_request['orderby'] = 'meta_value_num';
        $brhg2016_request['order'] = 'DESC';
        $brhg2016_request['intro_text'] = 7478;
        $brhg2016_request['page_title'] = __('Events', 'brhg2016');
        $brhg2016_request['meta_query'] = array(
            array(
                'key' => 'brhg_event_filter',
                'value' => 'brhg',
                'compare' => 'LIKE',
            )
        );

    # List Pamphlets by number
    elseif ($section == 'pamphlets') :
        $brhg2016_request['meta_key'] = 'pamphlet_number';
        $brhg2016_request['orderby'] = 'meta_value_num';
        $brhg2016_request['order'] = 'DESC';
        $brhg2016_request['posts_per_archive_page'] = -1;
        $brhg2016_request['post_status'] = 'publish';
        $brhg2016_request['intro_text'] = 1106;
        $brhg2016_request['page_title'] = __('BRHG Publications', 'brhg2016');

    # Projects
    elseif ($section == 'project') :
        $brhg2016_request['intro_text'] = 6422;
        $brhg2016_request['page_title'] = __('BRHG Projects', 'brhg2016');
        $brhg2016_request['posts_per_archive_page'] = -1;

    # Venues
    // Note that there is no Venues archive page at present
    elseif ($section == 'venues') :
        $brhg2016_request['page_title'] = __('Venues', 'brhg2016');

    # Posts/News/Blog - At present called Blog but used to be called News and is in fact the native Wordpress Post
    /*
    * Note that this is for chunk-item-details.php for single items e.g. on search result pages.
    * The /blog/ archive page is actually handled as a special url in brhg2016_special_urls(), see above, and not this.
    */
    elseif ($section == 'post') :
        $brhg2016_request['page_title'] = __('Blog', 'brhg2016');



        /**
         * Taxonomy archive pages for sub-sections
         * These are important for setting the drop-down filter on archive pages
         *
         *
         */


        # Articles - article type
        /**
         * Different intro for School Course Material
         */
    elseif ($section == 'articles' || array_key_exists('article_type', $tax_test)):
        if (
            $query !== null
            && array_key_exists('article_type', $query->query)
            && $query->query['article_type'] == 'material-for-schools'
        ) {
            $intro = 11249;
        } else {
            $intro = 1250;
        }
        $brhg2016_request['orderby'] = 'date';
        $brhg2016_request['order'] = 'DESC';
        $brhg2016_request['intro_text'] = $intro;
        $brhg2016_request['page_title'] = __('Articles', 'brhg2016');
        $brhg2016_request['type_tax'] = 'article_type';

    # Pamphlets - publication range
    elseif ($section == 'pamphlets' || array_key_exists('pub_range', $tax_test)) :
        $brhg2016_request['meta_key'] = 'pamphlet_number';
        $brhg2016_request['orderby'] = 'meta_value_num';
        $brhg2016_request['order'] = 'DESC';
        $brhg2016_request['intro_text'] = 1106;
        $brhg2016_request['page_title'] = __('Publications', 'brhg2016');
        $brhg2016_request['type_tax'] = 'pub_range';

    # Books - book type
    elseif ($section == 'books' || array_key_exists('book_type', $tax_test)) :
        $brhg2016_request['intro_text'] = 1232;
        $brhg2016_request['page_title'] = __('Book Reviews', 'brhg2016');
        $brhg2016_request['type_tax'] = 'book_type';

    # Radical History Listings - listing type
    elseif ($section == 'rad_his_listings' || array_key_exists('listing_type', $tax_test)) :
        $brhg2016_request['intro_text'] = 1274;
        $brhg2016_request['page_title'] = __('Radical History Listings', 'brhg2016');
        $brhg2016_request['type_tax'] = 'listing_type';

    # Contributors
    /*
    * The Contributors post type archive page is redirected to the contrib_alpha taxonomy archive in brhg2016_alter_the_query(), see above,
    * and the modification of this query is dealt with immediately below.
    *
    * This is used for the meta data for single Contributors.
    */
    elseif ($section == 'contributors') :
        unset($brhg2016_request['post_type']);
        $brhg2016_request['contrib_alpha'] = 'a';
        $brhg2016_request['orderby'] = 'name';
        $brhg2016_request['order'] = 'ASC';
        $brhg2016_request['posts_per_archive_page'] = -1;
        $brhg2016_request['intro_text'] = 1142;
        $brhg2016_request['page_title'] = __('Contributors', 'brhg2016');

    // This is for the contrib_alpha taxonomy archive
    elseif (array_key_exists('contrib_alpha', $tax_test)) :
        $brhg2016_request['orderby'] = 'name';
        $brhg2016_request['order'] = 'ASC';
        $brhg2016_request['posts_per_archive_page'] = -1;
        $brhg2016_request['intro_text'] = 1142;
        $brhg2016_request['page_title'] = __('Contributors', 'brhg2016');

    # Subject Category
    elseif (array_key_exists('category_name', $tax_test)) :
        $brhg2016_request['post_type'] = 'any';
        $brhg2016_request['intro_text'] = 1038;
        $brhg2016_request['page_title'] = __('Subject Index', 'brhg2016');
        $brhg2016_request['type_tax'] = 'category';

    # Tags
    elseif (array_key_exists('tag', $tax_test)) :
        $brhg2016_request['post_type'] = 'any';
        $brhg2016_request['intro_text'] = 4174;
        $brhg2016_request['page_title'] = __('Tag Index', 'brhg2016');

    endif;


    return $brhg2016_request;
}



/**
 * Template redirects for 'special urls'
 *
 * The template_redirect filter is added in brhg2016_special_urls() above
 *
 * @param string $template
 *
 * @return sting $special_template or template the new template file or original template file
 */
function brhg2016_template_redirects($template) {

    $special_url = get_query_var('special_url');

    switch ($special_url) {

        case 'blog':
            $special_template = locate_template('archive.php');
            break;

        case 'news-feed':
            $special_template = locate_template('archive.php');
            break;

        case 'subject-index':
            //$special_template = locate_template('tax-index.php');
            $special_template = locate_template('archive.php');
            break;

        case 'radical-history-listings':
            // $special_template = locate_template('tax-index.php');
            $special_template = locate_template('archive.php');
            break;

        case 'tag-index':
            $special_template = locate_template('archive.php');
            break;

        case 'bookshop':
            $special_template = locate_template('single.php');
            break;

        case 'publication-collections':
            $special_template = locate_template('archive-pamphlets.php');
            break;

        default:
            $special_template = '';
    }

    return (empty($special_template)) ? $template : $special_template;
}


/**
 * <title> tags for special urls
 *
 * The template_include filter is added in brhg2016_special_urls() above.
 *
 * @return The <title> tag contents
 */
function brhg2016_special_urls_title_tag() {
    $title = get_query_var('page_title');
    return $title . ' - Bristol Radical History Group';
}

/**
 * Reset the conditionals for special urls.
 *
 * The pre_get_posts filter is added in brhg2016_special_urls() above.
 *
 */

/* function brhg2016_special_urls_archive_true( $query ){
    
    $query_var = get_query_var( 'special_url' );

    if ( $query->is_main_query() && in_array( $query_var, array("news-feed", "subject-index", "radical-history-listings", "tag-index" )) ) {

        $query->is_page = 0;
        $query->is_singular = 0;
        $query->is_home = 0;       
        $query->is_post_type_archive = 1;
        $query->is_archive = 1;

    } elseif ( $query->is_main_query() &&  $query_var === 'bookshop' ) {
        $query->is_archive = 0;
        $query->is_single = 1;        
    }
} */

/**
 * Reset the conditionals for special urls.
 *
 * Attached to the wp action hook.
 * This changes $wp_the_query, which when done off the wp action hook,
 * also alters $wp_query.
 *
 * This was changed from the pre_get_posts hooks July 2020
 * Because of the conditionals being reset, with is_error() === true, after the query is made.
 *
 */

function brhg2020_reset_conditionals() {
    global $wp_query;
    global $wp_the_query;

    $query_var = get_query_var('special_url');
    $post_type_archive = array("news-feed");
    $url_taxonomy_archive = array("subject-index", "radical-history-listings", "tag-index",);

    if (is_main_query() && in_array($query_var, $post_type_archive)) {
        $wp_the_query->is_404 = false;
        $wp_the_query->is_page = false;
        $wp_the_query->is_singular = false;
        $wp_the_query->is_home = false;
        $wp_the_query->is_post_type_archive = in_array($query_var, $post_type_archive) ? true : false;
        $wp_the_query->is_archive = true;
        $wp_the_query->is_category = $query_var === 'subject-index' ? true : false;;
        $wp_the_query->is_tag = $query_var === 'tag-index' ? true : false;
        $wp_the_query->is_tax = in_array($query_var, $url_taxonomy_archive) ? true : false;;
    } elseif (is_main_query() &&  $query_var === 'bookshop') {
        $wp_the_query->is_archive = false;
        $wp_the_query->is_single = true;
    }
}

/* Admin queries
**************************************************************************************************************************************/
function brhg2016_admin_queries($query) {
    global $pagenow;

    // If not an admin page, or the query is not the main admin page query, or post_type is posts (i.e. $_GET['post_type'] is not set) bail.
    if (! is_admin() || ! $query->is_main_query() || ! array_key_exists('post_type', $_GET)) {
        return;
    }

    if (!isset($query->query['connected_type']) && 'edit.php' == $pagenow && !isset($_GET['orderby'])) {
        // This checks to see if the admin page list has been reordered, in which case $_GET['orderby'] will be set.
        switch ($_GET['post_type']) {
            case 'events':
                $query->set('meta_key', 'start_time_stamp');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'DESC');
                break;
            case 'event_series':
                $query->set('meta_key', 'series_start_date_stamp');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'DESC');
                break;
            case 'pamphlets':
                $query->set('meta_key', 'pamphlet_number');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'DESC');
                break;
            case 'articles':
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
                break;
        }
    }
}
add_action('pre_get_posts', 'brhg2016_admin_queries');


/* Secondary and extra queries
**************************************************************************************************************************************/

/*
*
* Front page queries
*
*/

/**
 * The query for the Event Series slider
 *
 * @return WP_Query object   $event_series the series to be used in the slider
 */
function brhg_event_series_slider() {

    // Filter South Bristol poster by IP
    $user_ip = brhg2024_get_the_visitor_ip();

    $filter_ip_array = ['92.234.24.123', '159.242.227.102'];
    $filter_posters_array = [20491, 17647, 22631];

    $event_series_args = array(
        'post_type'         => 'event_series',
        'orderby'           => 'meta_value_num',
        'order'             => 'DESC',
        'meta_key'          => 'series_start_date_stamp',
        'posts_per_page'    => -1,
        'meta_query' => array(
            array(
                'key'     => 'event_series_in_slider',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
        'post__not_in' => in_array($user_ip, $filter_ip_array) ? [] : $filter_posters_array,
    );

    $event_series = new WP_Query($event_series_args);

    return $event_series;
}

function brhg2024_get_the_visitor_ip() {

    if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

add_shortcode('display_ip', 'get_the_user_ip');

/**
 * Makes a WP_Query object for the front page news feed
 *
 *
 * @return WP_Query object  $news_feed the items for the news feed frame
 */
function brhg2016_front_news_feed_query() {
    $news_feed_args = array(
        'post_type' => array('post', 'events', 'books', 'articles', 'pamphlets', 'rad_his_listings',),
        'meta_key' => 'news_filter',
        'meta_value' => 'yes',
        'order' => 'DESC',
        'orderby' => 'date',
        'posts_per_page' => 10,
    );

    $news_feed = new WP_Query($news_feed_args);

    return $news_feed;
}

/**
 * Makes a WP_Query object for the front page diary
 *
 *
 * @return WP_Query object  $events the items for the event diary frame
 */
function brhg2016_front_diary_query() {
    $todays_date_stamp = strtotime(date("d F Y"));

    $diary_args = array(
        'post_type' => 'events',
        'meta_key' => 'start_time_stamp',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'posts_per_page' => 10,
        'meta_query' => array(
            array(
                'key' => 'end_time_stamp',
                'value' => $todays_date_stamp,
                'compare' => '>=',
            )
        )
    );

    $events = new WP_Query($diary_args);

    return $events;
}

/**
 * Makes a WP_Query object for the front recent stuff
 *
 *
 * @return WP_Query object  $recent_suff the items for the recent stuff sections
 */
function brhg2016_front_recent_query($type = "", $per_page = 10) {

    // Events are listed by start date/time, the rest are listed by posting date
    if ('events' === $type) {
        $todays_date_stamp = strtotime(date("d F Y"));
        $recent_stuff_args = array(
            'post_type' => $type,
            'order' => 'DESC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'start_time_stamp',
            'posts_per_page' => $per_page,
            'meta_query' => array(
                array(
                    'key' => 'end_time_stamp',
                    'value' => $todays_date_stamp,
                    'compare' => '<=',
                )
            )
        );
    } else {
        // Query to non-event items ordered by date posted
        $recent_stuff_args = array(
            'post_type' => $type,
            'order' => 'DESC',
            'orderby' => 'date',
            'posts_per_page' => $per_page,
        );
    }

    $recent_stuff = new WP_Query($recent_stuff_args);

    return $recent_stuff;
}

/*
*
* Project pages queries.
*
*/

/**
 * This is the query to find all the items connected to a project.
 *
 *
 * @return WP_Query object  $return the items connected to a project.
 */
function brhg2016_project_query() {
    global $post;

    $args = array(
        'post_type'         => 'any',
        'posts_per_page'    =>  -1,
        'meta_key'          => 'brhg2016_project',
        'meta_value'        => strval($post->ID),
    );

    $project_items = new WP_Query($args);

    # Make an array of the post-types with items linked to this project
    # Note that the post-type is both the key and value in the array
    if ($project_items) {
        $linked_post_types = array();
        foreach ($project_items->posts as $project) {
            if (!in_array($project->post_type, $linked_post_types)) {
                $linked_post_types[$project->post_type] = $project->post_type;
            }
        }

        # Set the preferred order for the post types to appear on the project page
        $order = array('articles', 'pamphlets', 'event_series', 'events', 'books', 'post');

        # Flip the $order array: $order values become the new keys, and the $order keys become the new value, which are numbers
        # Merge $order with $linked_post_types so that if the key exists in both arrays the value from $linked_post_types is used
        # The keys and values in $linked_post_types are both the post-type, not intigers
        # Post types that were in $order but not $linked_post_types, i.e. there were no posts of that type linked to this project, are still integers values
        $linked_post_types = array_merge(array_flip($order),  $linked_post_types);

        $return['project_items'] = $project_items;
        $return['linked_post_types'] = $linked_post_types;

        return $return;
    } else {
        return false;
    }
}

/*
*
* Other queries.
*
*/

/**
 * The query to find the current & forthcoming event series
 *
 * @return WP_Query object  $return the event series.
 */
function brhg2016_current_series() {
    $todays_date_stamp = strtotime(date("d F Y"));

    $args = array(
        'post_type'                 => 'event_series',
        'meta_key'                  => 'series_start_date_stamp',
        'orderby'                   => 'meta_value_num',
        'order'                     => 'ASC',
        'posts_per_archive_page'    => -1,
        'meta_query' => array(
            array(
                'key'        => 'series_end_date_stamp',
                'value'      => $todays_date_stamp,
                'compare'    => '>=',
            )
        )
    );

    $current_series = new WP_Query($args);

    return $current_series;
}
