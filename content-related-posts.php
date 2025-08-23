<?php

/**
 * Makes the related posts block on single item pages.
 *
 * Needs the modified Microkid Related Posts plugin
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since  BRHG2016 1.0
 */
?>

<?php
$show_related = array('Publications' => 'pamphlets', 'Articles' => 'articles', 'Book Reviews' => 'books');
// brhg2024_get_related is in functions/content-related-posts.php
$related_items_array = brhg2024_get_related($show_related);

if (! empty($related_items_array) && is_array($related_items_array)) :
?>
    <section id="related-posts-section" aria-label="Related content">
        <?php foreach ($related_items_array as $key => $related) :  ?>
            <aside class="related-list-wrapper related-list-wrapper-<?php echo $key ?>" aria-label="Related <?php echo $key ?>">
                <header class="related-post-type-title">More <?php echo $key; ?></header>
                <div class="thumb-only-listing-wrapper">
                    <?php
                    foreach ($related as $post):
                        setup_postdata($post);
                        get_template_part('loop', 'thumbs-only');
                    endforeach;
                    ?>
                </div>
                <div class="related-archive-link">
                    <?php if ($key === 'pamphlets') echo '<a href="' . site_url('publication-collections') . '">See Publication Collections</a><br>'; ?>
                    <a href="<?php echo get_post_type_archive_link($key); ?>">See all <?php echo array_search($key, $show_related); ?></a>
                </div>
            </aside>
        <?php endforeach; ?>
    </section>
<?php

endif;
