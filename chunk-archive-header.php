 <?php
   /**
    * The template for displaying the title and intro text and archive filter (type-tax not search) in the archive page header.
    *
    * brhg2016_get_intro() and brhg2016_archive_filter() are in functions/utility_functions.php.
    *
    * @package WordPress
    * @subpackage BRHG2016
    * @since  BRHG2016 1.0
    */
   ?>

 <?php
   // Add the search terms
   if (is_search()) { ?>
    <h2 class='page-header__search-title'><span class="page-header__searched-for">You searched for: </span>
       <?php echo get_query_var('s', ''); ?><br>
       <?php echo $wp_query->found_posts; ?> results
    </h2>

 <?php
   }
   ?>

 <?php brhg2016_get_intro(); ?>

 <?php
   if (current_user_can('editor') || current_user_can('administrator')) {  ?>
    <div class="edit-front-info-widget"><a href="<?php brhg2016_intro_test_edit_link(); ?>">Edit Intro</a></div>
 <?php
   } ?>

 <?php
   if (get_query_var('type_tax') !== 'category') brhg2016_archive_filter(); ?>