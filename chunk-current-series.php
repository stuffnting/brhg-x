<?php

/**
 * Displays the current and forthcoming series event series
 *
 *
 * @package WordPress
 * @subpackage BRHG2016
 * @since  BRHG2016 1.0
 */
?>

<?php
$current_series = brhg2016_current_series();

if ($current_series->have_posts()) : ?>

    <h2 class="current-series__title">Current &amp; forthcoming Event Series:</h2>
    <ul class="current-series__list">
        <?php while ($current_series->have_posts()) : $current_series->the_post(); ?>
            <li class="current-series__item">
                <a href="<?php the_permalink(); ?>" class="current-series__link">
                    <?php the_title(); ?>
                </a>
                :
                <?php brhg2016_event_series_dates(); ?>
            </li>
        <?php
        endwhile; ?>
    </ul>

<?php endif;
?>