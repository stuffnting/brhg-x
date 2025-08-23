<?php

/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php wp_head(); ?>
</head>

<body id="top" <?php body_class("brhg-body"); ?>>
    <header class="site-header">
        <div class="banner">
            <div class="banner__title-wrap">
                <?php
                if (is_front_page()) {
                    $title_tag = 'h1';
                } else {
                    $title_tag = 'div';
                }
                ?>

                <img src="<?php echo get_theme_file_uri('/images/angel.svg');  ?>" class="banner__logo-angel" alt="Logo angel">

                <<?php echo $title_tag; ?> class="banner__site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="banner__title-link">
                        Bristol Radical <br class="banner__title-break">History Group
                    </a>
                </<?php echo $title_tag; ?>>

                <img src="<?php echo get_theme_file_uri('/images/skeleton.svg');  ?>" class="banner__logo-skeleton" alt="Logo skeleton">

            </div>

            <nav class="top-info-menu" aria-label="Site info menu">

                <?php
                /* Site info menu */
                wp_nav_menu(
                    array(
                        'theme_location'    => 'scroll_info_menu',
                        'menu'              => 'Scroll Info Menu',
                        'depth'             => 1,
                        'container'         => false,
                        'menu_class'        => 'top-info-menu__wrap',
                    )
                );
                ?>

            </nav>

        </div>
        <hr class="header-oxo oxo" />

        <nav id="main-menu-bar" class="main-menu-bar" aria-label="Main contents menu">
            <?php
            /* Main contents menu */
            wp_nav_menu(
                array(
                    'theme_location'    => 'main_content_menu',
                    'menu'              => 'Main contents Menu',
                    'depth'             => 2,
                    'container'         => false,
                    'menu_class'        => 'main-menu-bar__menu',
                )
            ); ?>
            <button id="mobileMenuTrigger" class="main-menu-bar__mobile-menu-btn">
                <span class="sr-only">Toggle navigation</span>
                <span class="main-menu-bar__hamburger-icon"></span>
                <span class="main-menu-bar__hamburger-text">CONTENT MENU</span>
            </button>
            <?php
            /* mobile contents menu */
            wp_nav_menu(
                array(
                    'theme_location'    => 'mobile_menu',
                    'menu'              => 'Mobile Menu',
                    'menu_id'           => 'mobileMenu',
                    'depth'             => 1,
                    'container'         => false,
                    'menu_class'        => 'main-menu-bar__mobile-menu',
                )
            );
            ?>

            <button id="searchTriggerBtn" class="main-menu-bar__search-btn" title="Search" data-modal-target="#search-form-modal"><!--Search--></button>

        </nav>

        <?php echo brhg2025_number_items_trolley_link(); ?>

    </header>

    <main id="page" class="torso">