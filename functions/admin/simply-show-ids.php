<?php
/*
    Show IDs in admin list screens.
    
    This is an old plugin that is no longer maintained.
    
	Copyright (c) 2009-2010 Matt Martz (http://sivel.net)
	Simply Show IDs is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Prepend the new column to the columns array
function brhg_column($cols) {
  $cols['ssid'] = 'ID';
  return $cols;
}

// Echo the ID for the new column
function brhg_value($column_name, $id) {
  if ($column_name == 'ssid')
    echo $id;
}

function brhg_return_value($value, $column_name, $id) {
  if ($column_name == 'ssid')
    $value = $id;
  return $value;
}

// Output CSS for width of new column
function brhg_css() {
?>
	<style type="text/css">
		#ssid {
			width: 50px;
		}

		/* Simply Show IDs */
	</style>
<?php
}

// Actions/Filters for various tables and the css output
function brhg_add() {
  add_action('admin_head', 'brhg_css');

  add_filter('manage_posts_columns', 'brhg_column');
  add_action('manage_posts_custom_column', 'brhg_value', 10, 2);

  add_filter('manage_pages_columns', 'brhg_column');
  add_action('manage_pages_custom_column', 'brhg_value', 10, 2);

  add_filter('manage_media_columns', 'brhg_column');
  add_action('manage_media_custom_column', 'brhg_value', 10, 2);

  add_filter('manage_link-manager_columns', 'brhg_column');
  add_action('manage_link_custom_column', 'brhg_value', 10, 2);

  add_action('manage_edit-link-categories_columns', 'brhg_column');
  add_filter('manage_link_categories_custom_column', 'brhg_return_value', 10, 3);

  foreach (get_taxonomies() as $taxonomy) {
    add_action("manage_edit-{$taxonomy}_columns", 'brhg_column');
    add_filter("manage_{$taxonomy}_custom_column", 'brhg_return_value', 10, 3);
  }

  add_action('manage_users_columns', 'brhg_column');
  add_filter('manage_users_custom_column', 'brhg_return_value', 10, 3);

  add_action('manage_edit-comments_columns', 'brhg_column');
  add_action('manage_comments_custom_column', 'brhg_value', 10, 2);
}

add_action('admin_init', 'brhg_add');
