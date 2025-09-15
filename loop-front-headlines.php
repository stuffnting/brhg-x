<?php

/**
 * Loop for the front page headlines
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since 1.0
 */
?>
<ul>
    <?php

    // brhg2016_front_news_feed_query() is in functions/custom_query.php
    $news_feed = brhg2016_front_news_feed_query();
    while ($news_feed->have_posts()) : $news_feed->the_post();
    ?>
        <li class="fp-h-line-item">
            <a href="<?php the_permalink(); ?>" class="fp-h-line-item__link" title="<?php printf(esc_attr__('More about %s', 'brhg2016'), the_title_attribute('echo=0')); ?>" rel="bookmark">
                <?php
                $type = get_post_type_object(get_post_type());
                $section = $type->labels->name === 'Posts' ? 'Blog' : $type->labels->name;
                echo $section . '—' . get_the_title(); ?>
            </a>
        </li>
    <?php
    endwhile; // end of the news feed loop.
    ?>
</ul>