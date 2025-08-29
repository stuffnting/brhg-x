<?php

/**
 * Loop for the archive page excerpt Pamphlets and Event Series custom post types which use loop-thumbs-only.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 *
 */

// Archive item type

if (has_term('gallery', 'article_type')) {
    $archive_item_content = "archive-item-gallery";
    $archive_item_more = "archive-item-content__more-wrap--gallery";
    $extra_content_class = '';
} elseif (has_term('video-2', 'article_type') && brhg2016_get_vids(1, false)) {
    $archive_item_content = "archive-item-content";
    $archive_item_more = '';
    $extra_content_class = '';
} else {
    $archive_item_content = "archive-item-content";
    $archive_item_more = "";
    // Find out if there is a thumbnail to be used
    $thumb_test = brhg2016_archive_thumb('', false);
    $extra_content_class = '';

    if ($thumb_test === 'text') {
        $extra_content_class = 'archive-item-content--text-thumb';
    } elseif ($thumb_test === false) {
        $extra_content_class = 'archive-item-content--no-thumb';
    }
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('archive-item'); ?> aria-label="Archive item">
    <header class="archive-item-header">
        <h2 class='archive-item-header__title'>
            <a href="<?php the_permalink(); ?>" class="archive-item-header__title-link">
                <?php the_title(); ?>
            </a>
        </h2>
        <?php get_template_part('chunk', 'post-title-meta'); ?>
    </header>

    <div class="<?php echo $archive_item_content; ?> <?php echo $extra_content_class; ?>">

        <?php
        /*  
        *   brhg2016_get_vids() finds videos from embed urls. 
        *   If the post has a video included in another way it won't be found.
        *   $vids is used to indicate if an embed video was found in a article-type = video post
        */
        $vids = 0;

        if (has_term('video-2', 'article_type')) {
            // If a video is found, output video and set $vids to true, no videos and $vids set to 0
            $vids = brhg2016_get_vids(1);

            if (!empty($vids)) {
        ?>
                <div class="archive-item-content__video">
                    <?php
                    foreach ($vids as $vid) {
                        echo $vid;
                    }
                    ?>
                </div>
            <?php
            }
        }

        // Only show title for these post types, don't show thumb and excerpt
        $title_only = array('contributors', 'venues');

        if (
            !in_array(get_post_type(), $title_only)
            && !$vids
            && !has_term('gallery', 'article_type')
        ) {

            if ($thumb_test !== false): ?>
                <div class='archive-item-content__thumb-wrap'>
                    <?php brhg2016_archive_thumb(); ?>
                </div>
            <?php endif; ?>

            <div class="archive-item-content__excerpt">
                <?php echo brhg2016_custom_excerpt(500); ?>
            </div>


            <?php
        } elseif (has_term('gallery', 'article_type')) {
            /** 
             * Show the featured image and 3 other images for a gallery
             * 
             * Get all images attached to the gallery post
             * get_attached_media() returns an array who's keys are attachment IDs
             * Then use array_rand() to choose 4 of these keys at random, and place them in an array
             * 
             */
            $post_thumb = get_post_thumbnail_id();
            $images = get_attached_media('image');
            $chosen_images = array_rand($images, 4);

            // If the featured image is not in the chosen images, add it and take one of the others out.
            if ($post_thumb !== false && !in_array($post_thumb, $chosen_images)) {

                array_unshift($chosen_images, $post_thumb);
                array_pop($chosen_images);
            }

            foreach ($chosen_images as $image) {
                $atts = array('class' => 'archive-item-gallery__img');
            ?>

                <figure class="archive-item-gallery__img-wrap">
                    <a href="<?php the_permalink(); ?>" class="archive-item-gallery__img-link">
                        <?php echo wp_get_attachment_image($image, 'big_thumb', false, $atts); ?>
                    </a>
                </figure>

        <?php
            }
        } ?>

        <div class="archive-item-content__more-wrap <?php echo $archive_item_more; ?>">
            <a href="<?php the_permalink(); ?>" class="archive-item-content__more-btn">
                <?php _e('Read More', 'brhg2016'); ?> &raquo;
            </a>
        </div>
    </div>

    <footer class="archive-item-footer" aria-label="Archive item details">
        <div class="details-block details-block--archive">
            <?php
            $class = '';
            if (get_query_var('special_url', 'none') != 'event-diary' && get_post_type() == 'events') {
                $class  = 'details-block__details--after-event';
            ?>
                <div class="details-block__details archive-item-footer__details--event">
                    <span class="details-block__title">Event Details </span><br>
                    <?php get_template_part('chunk', 'events'); ?>
                </div>
            <?php
            }
            ?>
            <div class=" details-block__details <?php echo $class ?>">
                <?php get_template_part('chunk', 'item-details'); ?>
            </div>
        </div>
    </footer>
</article>