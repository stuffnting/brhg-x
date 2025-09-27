<?php

/**
 * Loop for the front page event diary
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since 1.0
 */
?>

<ul class="fp-h-line-list">
    <?php
    // brhg2016_front_diary_query() is in functions/custom_query.php
    $events = brhg2016_front_diary_query();
    while ($events->have_posts()) : $events->the_post();
        $event_start_time_stamp = get_post_meta($post->ID, 'start_time_stamp', true);
        $event_time = date('d/m/Y', $event_start_time_stamp);
    ?>

        <li class="fp-h-line-list__item">
            <a
                href="<?php the_permalink(); ?>"
                class="fp-h-line-list__item-link"
                title="<?php printf(
                            esc_attr__('M %s', 'toolbox'),
                            the_title_attribute('echo=0')
                        ); ?>"
                rel="bookmark">
                <?php the_title(); ?></a>
            - <?php echo $event_time; ?>
        </li>

    <?php
    endwhile; // end of the loop.
    ?>
</ul>