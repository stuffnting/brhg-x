<?php

/**
 * The search form displayed by get_search_form()
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since  1.0
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>" aria-label="Search form">
    <label for="search-input" class="sr-only">
        <?php echo _x('Search for:', 'label') ?>
    </label>
    <input
        type="search"
        class="search-form__input"
        placeholder="<?php echo esc_attr_x('Search …', 'placeholder') ?>"
        value="<?php echo get_search_query() ?>"
        id="search-input"
        name="s"
        title="<?php echo esc_attr_x('Search for:', 'label') ?>" />

    <input type="submit" class="search-form__btn" value="<?php echo esc_attr_x('Search', 'submit button') ?>" />
</form>