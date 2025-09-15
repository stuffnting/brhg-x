<?php

/**
 * Front page template
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since 1.0
 */


// brhg2025_get_font_page_about_featured() is in function/appearance/font_page_about_featured.php
extract(brhg2025_get_font_page_about_featured());

?>

<?php get_header(); ?>

<!-- Into section containing about BRHG and 4 featured items -->
<section id="front-page-intro" class="fp-intro">
    <section id="front-page-about" class="fp-about" aria-label="About BRHG">
        <img
            src="<?php echo $logo_url; ?>"
            alt="Bristol Radical History Group Logo"
            id="front-page-about-logo"
            class="fp-about__logo"
            alt="BRHG Logo" />
        <div id="front-page-about-text-wrapper" class="fp-about__text-wrapper">
            <div id="front-page-about-text" class="fp-about__text">
                <?php echo $about_section['fp_about_text']; ?>
            </div>
            <a
                href="<?php echo $about_section['fp_about_button_link']; ?>"
                class="fp-intro__btn fp-about__btn">
                <?php echo $about_section['fp_about_button_text']; ?>
            </a>
        </div>
    </section>
    <span role="region" id="front-page-featured" class="fp-featured" aria-label="Featured items">
        <?php
        foreach ($featured_items as $item) {
            $item_number = $item['id'];
        ?>
            <article id="featured-item-<?php echo $item_number; ?>" class="fp-featured-item fp-featured-item--<?php echo $item_number; ?>">
                <h2 class="fp-featured-item__title">
                    <a href="<?php echo esc_url($item['link']); ?>" class="fp-featured-item__title-link">
                        <?php echo $item['title']; ?>
                    </a>
                </h2>
                <a
                    href="<?php echo esc_url($item['link']); ?>"
                    class="fp-featured-item__image-link"
                    aria-label="Read more about <?php echo $item['title']; ?>">
                    <?php echo $item['image_tag']; ?>
                </a>
                <div class="fp-featured-item__text">
                    <?php echo $item['text']; ?>
                </div>
                <div class="fp-featured-item__btn-wrapper">
                    <a href="<?php echo esc_url($item['link']); ?>" class="fp-featured-item__btn">
                        <?php echo $item['button']; ?>
                    </a>
                </div>
                <?php
                if ($item['id'] === 1) { ?>
                    <hr id="featured-item-1-hr" class="fp-featured-item__item-1-hr">
                <?php
                } ?>
            </article>
        <?php
        }
        ?>
    </span>
</section><!-- fp-intro -->

<!-- Friends Section -->
<?php
$friends_fields = get_field('fp_friends', 'options');

if ($friends_fields['fp_show_friends_section']) :
?>

    <section id="front-page-friends" class="fp-friends" aria-label="Friends">
        <div class="fp-friends__header">
            <h2 class="fp-friends__title"><?php echo $friends_fields['fp_friends_title'] ?></h2>
            <?php
            if (!empty($friends_fields['fp_friends_page_link'])) {
                printf(
                    "<div class='fp-friends__page-link-wrap'><a href='%s' class='fp-friends__page-link'>%s</a></div>",
                    esc_url($friends_fields['fp_friends_page_link']),
                    $friends_fields['fp_friends_page_link_text']
                );
            }
            ?>
        </div>
        <div class='fp-friends__items-wrap'>
            <?php
            // Defined in function/appearance/front_page_controls.php
            echo brhg2024_front_page_friends_section_repeater_loop($friends_fields['fp_friends_repeater']);
            ?>
        </div>
    </section><!-- Friends Section -->

<?php endif; // $friends_field 
?>

<!-- Recent headline frames -->
<section id="fp-h-line" class="fp-h-line" aria-label="News feed">
    <!-- News Headline Frame -->
    <div id="headline-frame-1" class="fp-h-line__frame-outer">
        <div class="fp-h-line__frame-inner">
            <h2 class="fp-h-line__title">News Feed</h2>
            <?php include(locate_template('loop-front-headlines.php')); ?>
            <div class="fp-h-line__more"><a href="<?php echo esc_url(site_url('/news-feed/')); ?>" class="fp-h-line__more-link">Go to the news page.</a></div>
            <div class="fp-h-line__more"><a href="<?php echo esc_url(get_bloginfo_rss('rss2_url')); ?>" class="fp-h-line__more-link">News Feed RSS.</a></div>
        </div>
    </div>
    <!-- Events Headline Frame -->
    <div id="headline-frame-2" class="fp-h-line__frame-outer">
        <div class="fp-h-line__frame-inner">
            <h2 class="fp-h-line__title">Pending Events</h2>
            <?php get_template_part('loop', 'front-diary'); ?>
            <div class="fp-h-line__more">
                <a href="<?php echo esc_url(site_url('/event-diary/')); ?>">See all pending events on the diary page.</a>
            </div>
        </div>
    </div>
</section>

<!-- Recent event series sliders see chunk-footer-js.php for the JS controls -->
<?php
// The query for which event series' posters are in the slider is in functions/custom_query.php
// Posters are filtered by IP address.
$event_series = brhg_event_series_slider();
?>
<section id="fp-slider" class="fp-slider" aria-label="Selected events series">
    <div class="fp-recent__title-wrap--slider">
        <h2 class="fp-recent__title">Past Event Series</h2>
        <a href="<?php echo esc_url(get_post_type_archive_link('event_series')); ?>" class="fp-recent__more">
            See all event series
        </a>
    </div>
    <div class="fp-slider__outer">
        <!--Tiny Slider wraps the following-->
        <div class="fp-slider__inner">
            <?php
            // The slider loop 
            if ($event_series->have_posts()) :
                while ($event_series->have_posts()) : $event_series->the_post();
                    if (has_post_thumbnail()) {
                        $slide_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'big_thumb');
                    } ?>
                    <div class="fp-slider__item">
                        <div class="thumb-only-listing">
                            <img
                                src="<?php echo  $slide_img[0]; ?>"
                                data-src="<?php echo  $slide_img[0]; ?>"
                                width="<?php echo  $slide_img[1]; ?>"
                                height="<?php echo
                                        $slide_img[2]; ?>"
                                class="tns-lazy-img thumb-only-listing__item-img"
                                alt="Poster for <?php the_title(); ?>"
                                loading="lazy">
                            <a href="<?php the_permalink(); ?>" class="thumb-only-listing__item-link">
                                <h2 class="thumb-only-listing__item-title"><?php the_title(); ?></h2>
                            </a>
                        </div>
                    </div>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<!-- Recent stuff -->
<?php
// Which sections are there?
$post_types = array('post', 'books', 'articles', 'pamphlets'); ?>

<section id="front-page-recent-stuff" class="fp-recent" aria-label="Recent stuff">

    <?php
    foreach ($post_types as $type) {
        // brhg2016_front_recent_query() is in functions/custom_query.php
        $recent_stuff = brhg2016_front_recent_query($type, 4);

        switch ($type) {
            case 'post':
                $title = "Blog Posts";
                break;
            case 'books':
                $title = "Book Reviews";
                break;
            case 'pamphlets':
                $title = "Publications";
                break;
            default:
                $title = get_post_type_object($type)->labels->name;
        }

        $link = ($type !== 'post') ? get_post_type_archive_link($type) : "https://www.brh.org.uk/site/blog/";
    ?>
        <section id="recent-stuff-<?php echo $type ?>-section" class="fp-recent__section" aria-label="Recent <?php echo $title ?>">
            <div class="fp-recent__title-wrap">
                <h2 class="fp-recent__title">Recent <?php echo $title ?></h2>
                <a href="<?php echo $link; ?>" class="fp-recent__more">See all <?php echo $title; ?></a>
            </div>
            <div class="fp-recent__items-wrap">
                <?php include(locate_template('loop-front-recent.php')); ?>
            </div>
        </section>
    <?php
    } //end foreach - post types for recent sliders 
    ?>

</section> <!-- End #front-page-recent-stuff  -->

<?php
if (!brhg2024_check_phone()) {
?>
    <!-- Tag Cloud -->
    <section id="front-page-tag-cloud" class="fp-tag-cloud" aria-label="Tag cloud">
        <div class="fp-tag-cloud__wrap">
            <?php brhg2016_tag_cloud(100); ?>
        </div>
    </section>
<?php
}
?>

<?php get_footer();
