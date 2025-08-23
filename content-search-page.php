 <?php
    /**
     * The form for the Search Page (not the search results pages, look in content-page-header.php for that).
     * 
     *
     *
     * @package WordPress
     * @subpackage BRHG2016
     * @since  BRHG2016 1.0
     */
    ?>

 <form role="search" method="post" class="search-page-search-form" action="<?php echo home_url('/'); ?>">
     <fieldset class="form-group" name="search-box">
         <label for="search-term" class="filter-form-label">Search Term:</label>
         <span class="screen-reader-text"><?php echo _x('Search for:', 'label') ?></span>
         <input
             id="search-term"
             type="search"
             class="form-control search-field"
             placeholder="<?php echo esc_attr_x('Search …', 'placeholder') ?>"
             value="<?php echo get_search_query() ?>"
             name="s"
             title="<?php echo esc_attr_x('Search for:', 'label') ?>" />
         <input type="hidden" name="sent_from" value="search-filter">
     </fieldset>

     <div class="search-filters-wrapper">
         <?php get_template_part('chunk', 'search-filter-inner'); ?>
     </div>

     <div class="filter-button-wrap">
         <input type="submit" class="btn btn-primary" value="Go Search!">
     </div>
 </form>