<?php

/**
 * Controls the extra admin columns and Quick Edit options
 *
 * If there are problems populating the columns check custom_query.php brhg2016_admin_queries( $query )
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since 1.0
 */

/*********************************************************************************
 * 
 * Used by manage columns and quick edit
 * 
 ********************************************************************************/

/**
 * Allow Quick Edit Render and Save
 *
 * @return array $allow_on the post types that are allowed Quick Edit modifications
 */
function brhg2016_allow_quick_edit() {

    $allow_on = array('articles', 'pamphlets', 'books', 'event_series', 'events', 'post');

    return $allow_on;
}

/**
 * Get the current admin page type.
 * 
 * @see {@link https://wp-mix.com/get-current-post-type-wordpress/}
 * 
 * @return string The current admin post type.
 */
function brhg2024_get_current_post_type() {

    global $post, $typenow, $current_screen;

    if ($post && $post->post_type) {

        return $post->post_type;
    } elseif ($typenow) {

        return $typenow;
    } elseif ($current_screen && $current_screen->post_type) {

        return $current_screen->post_type;
    } elseif (isset($_REQUEST['post_type'])) {

        return sanitize_key($_REQUEST['post_type']);
    }

    return null;
}


/*********************************************************************************
 * 
 * Manage columns
 * 
 ********************************************************************************/

// Add the style to the header
add_action('admin_head', 'brhg2016_admin_column_css');
// Add the new columns to the product list page
add_filter('manage_posts_columns', 'brhg2016_columns_head', 999, 2);
//add_filter('manage_pages_columns', 'brhg2016_columns_head', 99, 2);
// Fill the new columns
add_action('manage_posts_custom_column', 'brhg2016_columns_content', 10, 2);
add_action('manage_pages_custom_column', 'brhg2016_columns_content', 10, 2);

/**
 * Add new column headers to the admin pages.
 * Runs off the manage_posts_columns and manage_pages_columns filters
 * which are set above.
 *
 * @param array      $defaults the default column headers passed by the filter
 *
 * @return array     $defaults the modified list of columns headers
 */
function brhg2016_columns_head($defaults, $post_type) {

    if ($post_type == 'pamphlets') {

        $defaults['pamphlet_number'] = 'Pam No.';
    }

    // Events in Event Series
    if ($post_type == 'events') {

        $defaults['in_event_Series'] = 'Series';
    }

    // In front page slider for event series
    if ($post_type == 'event_series') {

        $defaults['in_slider'] = 'Slider?';
    }

    // Project
    $show_project_on = brhg2016_project_allowed_post_types();

    if (in_array($post_type, $show_project_on)) {

        $defaults['project'] = 'Project';
    }

    // Features images
    $show_featured_on = array('post', 'event_series', 'articles', 'pamphlets', 'books');

    if (in_array($post_type, $show_featured_on)) {

        $defaults['featured_image'] = 'Image';
    }

    return $defaults;
}


/**
 * Fills the new column in the admin pages.
 * Runs off the manage_posts_custom_column and manage_pages_custom_column actions
 * which are set above.
 *
 * @param string     $column_name the column name passed by the action.
 * @param string     $post_ID the ID of the post in the table row.
 *
 */
function brhg2016_columns_content($column_name, $post_ID) {

    switch ($column_name) {

        case 'featured_image';
            $featured_image = brhg2016_featured_image($post_ID);
            if ($featured_image) {

                echo "<img src='$featured_image' width='50' height='80' />";
            }
            break;

        case 'in_slider';
            echo brhg2016_slider($post_ID);
            break;

        case 'pamphlet_number';
            echo brhg2016_pam_no($post_ID);
            break;

        case 'project':
            echo brhg2016_project_col($post_ID);
            break;

        case 'in_event_Series':
            echo brhg2016_series_col($post_ID);

            break;
    }
}

/**
 * Gets the featured images for columns that need them.
 * Called by brhg2016_columns_content().
 *
 * @return string    The featured image src.
 */
function brhg2016_featured_image($post_ID) {

    $post_thumbnail_id = get_post_thumbnail_id($post_ID);

    if ($post_thumbnail_id) {

        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'tiny_thumbs');
        return $post_thumbnail_img[0];
    } else {

        // No featured image, show default
        echo '<div class="missing-featured" style="width:50px; height: 80px; border: 1px solid #cfcfcf">No Image</div>';
    }
}

/**
 * Checks if the current Event Series is in the Front Page Slider
 * Called by brhg2016_columns_content().
 *
 * @return string    'Slider' if the Event Series is in the slider
 */
function  brhg2016_slider($post_ID) {
    $in_slider = get_post_meta($post_ID, 'event_series_in_slider', true);
    if ($in_slider) {
        return ($in_slider === '1') ? 'Slider' : '';
    }
}

/**
 * Get the pamphlet number
 * Called by brhg2016_columns_content().
 *
 * @return string    The pamphlet number
 */
function brhg2016_pam_no($post_ID) {
    $pam_no = get_post_meta($post_ID, 'pamphlet_number', true);
    return $pam_no;
}

/**
 * Checks if the current item is in a project.
 * Called by brhg2016_columns_content().
 *
 * @return string    The comma separated list list of projects that the item is in.
 */
function brhg2016_project_col($post_ID) {
    $projects = get_post_meta($post_ID, 'brhg2016_project', false);

    if (empty($projects)) {
        return '—';
    }

    if (isset($projects)) {

        $project_list = '';

        foreach ($projects as $key => $project) {
            // Protect against 0 being set as the project in save
            if ($project == 0) {
                continue;
            }
            $separator = (count($projects) > $key + 1) ? ', ' : '';
            $project_list .= sprintf(
                "<a href='%s' class='project-item' data-project='project-%s'>%s</a>%s",
                get_permalink($project),
                $project,
                get_the_title($project),
                $separator
            );
        }
    }

    return $project_list;
}

/**
 * Checks if the current Event is in a Event Series.
 * Called by brhg2016_columns_content().
 *
 * @return string    The comma separated list of the Event Series.
 */
function brhg2016_series_col($post_ID) {

    $series = get_posts(array(
        'connected_type' => 'events_to_series',
        'connected_items' => $post_ID,
        'nopaging' => true,
        'suppress_filters' => false
    ));

    if (empty($series)) {
        return false;
    }

    $series_list = '';

    foreach ($series  as $key => $event_series) {
        $series_list .= sprintf(
            "<a href='%s'>%s</a>",
            get_edit_post_link($event_series->ID, 'url'),
            $event_series->post_title
        );
        $series_list .= ($key + 1 < count($series)) ? ', ' : '';
    }

    return $series_list;
}

/**
 * Final adjustments
 */

$post_types_list = array('post', 'events', 'event_series', 'articles', 'pamphlets', 'books', 'rad_his_listings', 'projects', 'contributors', 'venues');

foreach ($post_types_list as $type) {
    add_filter("manage_edit-{$type}_columns", 'brhg2024_pamphlet_admin_cols', 99);
}

function brhg2024_pamphlet_admin_cols($columns) {

    $column_order = array(
        'cb',
        'title',
        'taxonomy-pub_range',
        'in_slider',
        'in_event_Series',
        'pamphlet_number',
        'categories',
        'tags',
        'project',
        'comments',
        'date',
        'featured_image',
        'ssid'
    );

    // Author is left out of $column_order because on most post types it is not wanted. However, on these two, it is.
    $add_author_back = array('post', 'venues');

    if (in_array(brhg2024_get_current_post_type(), $add_author_back)) {
        array_splice($column_order, 2, 0, 'author');
    }

    $new_column_order = array();

    foreach ($column_order as $col) {
        if (array_key_exists($col, $columns)) {
            $new_column_order[$col] =  $columns[$col];
        }
    }

    return  $new_column_order;
}

/*********************************************************************************
 * 
 * Manage sortable columns
 * 
 ********************************************************************************/

/**
 * Make publication number column sortable.
 */
add_filter('manage_edit-pamphlets_sortable_columns', 'brhg2024_pamphlet_add_sortable_cols');

function brhg2024_pamphlet_add_sortable_cols($columns) {
    $columns['pamphlet_number'] = 'pamphlet_number';
    return $columns;
}

/**
 * Publication number column query
 */
add_action('pre_get_posts', 'brhg2024_pamphlet_sortable_cols_query');

function brhg2024_pamphlet_sortable_cols_query($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby == 'pamphlet_number') {
        $query->set('meta_key', 'pamphlet_number');
        $query->set('orderby', 'meta_value_num');
    }
}

/*********************************************************************************
 * 
 * Column width CSS 
 * 
 ********************************************************************************/

/**
 * Adds CSS to set the column widths
 */
function brhg2016_admin_column_css() {
    $post_type = brhg2024_get_current_post_type();

    $style_out = "";

    switch ($post_type) {

        case 'event_series':
            $style_out .= '.column-title { width:25% !important}';
            $style_out .= '.column-project { width:30% !important}';
            $style_out .= '.column-in_slider { width:75px !important}';
            $style_out .= '.column-featured_image { width: 75px; !important }';
            break;

        case 'events':
            $style_out .= '.column-title { width:20% !important}';
            $style_out .= '.column-date { width: 200px !important }';
            break;

        case 'pamphlets':
            $style_out .= '.column-title { width:18% !important }';
            $style_out .= '.column-pamphlet_number { width:55px !important}';
            $style_out .= '.column-categories { width:12% !important }';
            $style_out .= '.column-tags { width:12% !important }';
            $style_out .= '.column-date { width: 170px !important }';
            $style_out .= '.column-featured_image { width: 50px !important }';
            $style_out .= '.column-ssid { width: 50px !important }';
            break;

        case 'rad_his_listings':
            break;

        case 'venues':
            break;

        case 'contributors':
            break;

        case 'page':
            break;

        default:
            $style_out .= '.column-cb, .check-column {width: 25px !important }';
            $style_out .= '.column-project { width:150px !important}';
            $style_out .= '.column-title { width:20% !important }';
            $style_out .= '.column-date { width: 170px !important }';
            $style_out .= '.column-featured_image { width: 50px !important }';
            $style_out .= '.column-ssid { width: 50px !important }';
            break;
    }

    echo "<style type='text/css'>$style_out</style>";
}


/********************************************************************************************************
 *
 * Quick Edit
 *
 * NOTE: Because $post can not be accessed here, the selected and checked items in quick edit
 * need to be set by the JS in /js/admin-extra.js
 * 
 ******************************************************************************************************/

// Add to our admin_init function
add_action('quick_edit_custom_box',  'brhg2016_add_quick_edit', 10, 3);
// Save from Quick Edit
add_action('save_post', 'brhg2016_save_quick_edit_data');

// The JS file admin-extras.js is enqueued in functions.php

/**
 * Add the required form elements to the quick edit form.
 * Called from the quick_edit_custom_box action, set above.
 */
function brhg2016_add_quick_edit($column_name, $post_type, $taxonomy) {

    $allowed_post_types_for_cols = brhg2016_allow_quick_edit() ?? array();

    if (! in_array($post_type, $allowed_post_types_for_cols)) {
        return;
    }
?>

    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">

            <?php
            if ($column_name == 'in_slider') {
                # Checkbox for the slider
            ?>
                <input type="checkbox" class='in-slider' value='1' name="event_series_in_slider_QE" />
                <span class="title">In Front Page Slider</span>
            <?php
            } elseif ($column_name == 'taxonomy-pub_range') {
                # Dropdown for the publication range
            ?>
                <div class="inline-edit-group">
                    <label class="inline-edit-status alignleft">
                        <?php
                        $drop_args = array(
                            'taxonomy'          => 'pub_range',
                            'hide_empty'        => 0,
                            'name'              => "pub_range",
                            'id'                => 'pub_range_drop',
                            'orderby'           => 'ID',
                            'order'             => 'ASC',
                            'hierarchical'      => 0,
                            'show_option_none'  => 'None'
                        );
                        wp_dropdown_categories($drop_args);
                        ?>
                        <span class="title">Publication Range</span>
                        <label>
                </div>
            <?php
            } elseif ($column_name == 'project') {
                # Group of checkboxes for the link projects
                $projects = brh2016_project_list();
            ?>
                <span class="title inline-edit-categories-label">Projects</span>
                <input type="hidden" value="0" name="brhg2016_project[]">
                <ul id="project-list" class="linked-projects cat-checklist">
                    <?php
                    foreach ($projects as $item):
                    ?>
                        <li id="project-list-<?php echo $item->ID ?>">
                            <label class="selectit project-check-label" for="project-<?php echo $item->ID ?>">
                                <input
                                    type="checkbox"
                                    id="project-<?php echo $item->ID; ?>"
                                    class="project-checkbox"
                                    name="brhg2016_project[]"
                                    value="<?php echo esc_attr($item->ID); ?>" />
                                <?php echo $item->post_title; ?></label>
                        </li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            <?php
            } else {
                // return;
            }
            ?>
        </div> <!-- inline-edit-col -->
    </fieldset> <!-- inline-edit-col-right -->
<?php
}

/**
 * A query to find what projects there are.
 *
 * @return array     The post objects from the WP_Query objects
 */
function brh2016_project_list() {
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_type'        => 'project',
        'post_status'      => 'publish',
    );

    $output = new WP_Query($args);

    return  $output->posts;
}

/**
 * Quick Edit save
 */
function brhg2016_save_quick_edit_data($post_id = 0) {
    if (empty($_POST['action'])) {
        return;
    }

    // Don't let this save routine run on anything other than a quick edit save for a certain post type
    $allowed_post_types_for_cols = brhg2016_allow_quick_edit();

    if ($_POST['action'] !== 'inline-save' || !in_array($_POST['post_type'], $allowed_post_types_for_cols)) {
        return;
    }

    // Verify if this is an auto save routine. If it is our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }

    if ($_POST['post_type'] == 'event_series') {

        // Find and save the data
        $post = get_post($post_id);
        if (isset($_POST['event_series_in_slider_QE'])) {
            $value =  '1';
            delete_post_meta($post_id, 'event_series_in_slider');
            update_post_meta($post_id, 'event_series_in_slider', $value);
        } else {
            delete_post_meta($post_id, 'event_series_in_slider');
        }
    }

    if (isset($_POST['pub_range']) && $_POST['post_type'] == 'pamphlets') {
        wp_set_object_terms($post_id, intval($_POST['pub_range']), 'pub_range', false);
    }

    if (isset($_POST['brhg2016_project'])) {

        delete_post_meta($post_id, 'brhg2016_project');
        foreach ($_POST['brhg2016_project'] as $value) {
            if ($value != 0) {
                // Don't use update_post_meta, it updates the same array item on each iteration
                add_post_meta($post_id, 'brhg2016_project', $value);
            }
        }
    }
}
