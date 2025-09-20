<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage BRHG2016
 * @since  BRHG2016 1.0
 */
?>


</main><!-- #page opened in header.php-->

<div id="search-form-modal" class="search-modal" hidden aria-label="Search form">
    <?php $ph = "Search This Site"; ?>
    <form action="<?php echo home_url(); ?>" method="get" class="search-modal__form" role="search">
        <input type="text" class="search-modal__input" id="menu-search" name="s" autocomplete="off"
            value="<?php echo $ph; ?>"
            onfocus="if(this.value=='<?php echo $ph; ?>')this.value='';"
            onblur="if(this.value=='')this.value='<?php echo $ph; ?>'"
            placeholder="<?php echo $ph; ?>" />
        <button class="search-modal__button" type="submit" aria-label="Submit search">Go</button>
        <div id="searchCloseBtn" class="search-modal__button" data-modal-close aria-label="Close search modal">X</div>
    </form>
</div>

<hr class="footer-oxo oxo" />

<footer id="site-footer" class="site-footer">

    <?php // The footer menu  
    ?>
    <div class="footer-menu__wrapper">
        <nav id="footer-menu-bar" class="footer-menu__nav" aria-label="Footer menu">
            <?php
            wp_nav_menu(
                array(
                    'theme_location'    => 'footer_menu',
                    'menu'              => 'Footer Menu',
                    'menu_id'           => 'footer-menu',
                    'depth'             => 1,
                    'container'         => false,
                    'menu_class'        => 'footer-menu__row1',
                )
            );
            ?>
            <div class="footer-menu__row2">
                <a href="mailto:<?php echo brhg2024_get_main_email(true) ?>">
                    <?php echo brhg2024_get_main_email(true) ?>
                </a>
                <span class="cherub-menu-dash">&nbsp;&nbsp;|&nbsp;&nbsp; </span><br class="cherub-menu-break">
                <a href="<?php echo esc_url(get_site_url()); ?>">brh.org.uk</a>
            </div>
        </nav>
    </div>
    <div class="footer-copyright">
        The copyright of material on this website is retained by the originators of the material. <br>
        Bristol Radical History Group <?php echo date("Y"); ?>.
    </div>
</footer>

<?php
/*
* Inline scripts added in function/utilities/inline_scripts.php
*/
wp_footer();

/*
* Add the Schema.
* brhg201_get_schema() is in function/schema.php
*/
if (is_singular()) {
    echo brhg201_get_schema();
}

?>
</body>

</html>