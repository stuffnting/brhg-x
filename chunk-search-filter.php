 <?php
    /**
     * The template for displaying the filter form in the archive page header.
     *
     * brhg2016_get_intro() and brhg2016_archive_filter() are in functions/utility_functions.php.
     *
     * The JS for the filter button is in chunk-footer-js.php
     *
     * @package WordPress
     * @subpackage BRHG2016
     * @since  BRHG2016 1.0
     */
    ?>

 <div class="search-filter">
     <form id="search-filter-form" action="<?php echo brhg2016_get_Url(); ?>" method="post" class="search-filter__form">
         <?php $search = get_query_var('s'); ?>
         <input type="hidden" name="s" value="<?php echo $search; ?>">
         <input type="hidden" name="sent_from" value="search-filter">

         <?php get_template_Part('chunk', 'search-filter-inner'); ?>

         <input type="submit" class="search-filter__btn" value="Go Filter!">

     </form>

     <button type="button" id="search-filter-form-btn" class="search-filter__btn">Filter Search</button>
 </div>