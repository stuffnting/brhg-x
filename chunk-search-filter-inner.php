 <?php
    /**
     * The inner bit of the search filter form.
     * Used by chunk-search-filter.php and content-search-page.php
     * 
     *
     *
     * @package WordPress
     * @subpackage BRHG2016
     * @since  BRHG2016 1.0
     */
    ?>

 <fieldset class="search-filter__fieldset" name="post_type_slector">
     <label for="post-types" class="search-filter__label">
         <span class="search-filter__field-name">Section filter:</span>
         <span class="search-filter__field-description">
             Choose as many as you like using ctr+click/apple+click. You can reset the filter by selecting 'Any'.
         </span>
     </label>
     <select multiple id="post-types" class="search-filter__select" name="post_type[]">
         <option value='' class="search-filter__option">-- Any -- </option>
         <?php
            $args = array(
                'public' => true,
                'publicly_queryable' => true
            );

            $post_types = get_post_types($args, 'objects');

            // Remove unwanted public and publicly queryable post types
            $remove = array('attachment', 'venues');

            foreach ($remove as $ditch) {
                unset($post_types[$ditch]);
            }

            // Change the name of Posts to Blog
            // Change the array key name from posts to blog, doesn't matter about the order changing
            $post_types['blog'] = $post_types['post'];
            unset($post_types['post']);
            // Sort the array alphabetically by key 
            ksort($post_types);

            // Change the ->labels->name to Blog
            $post_types['blog']->labels->name = 'Blog';

            $options = array();
            foreach ($post_types as $key => $value) {
                $options[] = array('name' => $value->name, 'label' => $value->labels->name);
            }

            if (isset($post_types)) {
                foreach ($options as $option) {
                    $selected = (in_array($option['name'], (array) get_query_var('post_type'))) ? 'selected' : '';
                    echo "<option $selected value='{$option['name']}' class='search-filter__option'>{$option['label']}</option>\n";
                }
            } ?>
     </select>
 </fieldset>
 <fieldset class="search-filter__fieldset" name="category_name_slector">
     <label for="category_name" class="search-filter__label">
         <span class="search-filter__field-name">Subject filter:</span>
         <span class="search-filter__field-description">
             Choose as many as you like using ctr+click/apple+click. You can reset the filter by selecting 'Any'.
         </span>
     </label>
     <select multiple id="category_name" class="search-filter__select" name="category_name[]">
         <option value='' class="search-filter__option">-- Any -- </option>
         <?php
            $args = array(
                'orderby'   => 'name',
                'order'     =>  'ASC'
            );

            $cats = get_terms('category', $args);

            if (isset($cats)) {
                if (array_key_exists('category_name', $wp_query->query)) {
                    $cat_vars = explode(',', $wp_query->query['category_name']);
                }

                foreach ($cats as $option) {
                    if (isset($wp_query->query['category_name'])) {
                        $selected = (in_array($option->slug, $cat_vars)) ? 'selected' : '';
                    }
                    echo "<option $selected value='{$option->slug}' class='search-filter__option'>{$option->name}</option>\n";
                }
            }
            ?>
     </select>

 </fieldset>