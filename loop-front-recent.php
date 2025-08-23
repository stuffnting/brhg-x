<?php

/**
 * The loop used for the front recent owl sliders
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since 1.0
 */
?>

<?php
// The loop for each recent stuff section
while ($recent_stuff->have_posts()) : $recent_stuff->the_post(); ?>
    <article class="fp-recent__item">
        <div class="fp-recent__item-thumb">
            <?php // Sort out the thumbnail 
            $thumb_attr = array(
                'class' => 'fp-recent__item-img',
                'loading' => 'lazy'
            );

            if (has_post_thumbnail()) {
                the_post_thumbnail('big_thumb', $thumb_attr);
            } else {
                echo '<div class="fp-recent_item-missing-thumb"><!--missing image--></div>';
            }
            ?>
        </div>
        <h3 class="fp-recent__item-title">
            <a class="fp-recent__item-link" title="More about <?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" rel="bookmark">
                <?php
                if ('pamphlets' === $type) {
                    echo get_the_terms($post->ID, 'pub_range')[0]->name;
                    echo " #";
                    the_field('pamphlet_number');
                    echo " — ";
                }

                the_title();

                if (get_field('sub_title')) {
                    echo ": ";
                    the_field('sub_title');
                }

                if ('books' === $type) {
                    echo " — ";
                    the_field('author');
                }
                ?>
            </a>
        </h3>
    </article>
<?php
endwhile; // end of the slider loop.