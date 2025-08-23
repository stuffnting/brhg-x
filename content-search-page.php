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

 <div id="search-filter-form" class="search-form">
     <form role="search" method="post" class="search-form__form" action="<?php echo home_url('/'); ?>">
         <fieldset class="search-form__search-fieldset" name="search-box">
             <label for="search-term" class="search-form__label">
                 <span class="search-form__field-name">Search Term:</span>
             </label>
             <input
                 id="search-form__input"
                 type="search"
                 class="search-form__input"
                 placeholder="<?php echo esc_attr_x('Search …', 'placeholder') ?>"
                 value="<?php echo get_search_query() ?>"
                 name="s"
                 title="<?php echo esc_attr_x('Search for:', 'label') ?>" />
             <input type="hidden" name="sent_from" value="search-filter">
         </fieldset>

         <div class="search-form__filter">
             <?php get_template_part('chunk', 'search-filter-inner'); ?>
         </div>


         <input type="submit" class="search-form__btn" value="Go Search!">

     </form>

 </div>