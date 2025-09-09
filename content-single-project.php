<?php

/**
 * Single Project page, stuff related to project
 * The JS is in chunk-footer-js.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php
//Number to show before hiding the rest
$number_to_show = 3;
$content_area_title = "Stuff linked to this project...";

// Get the content that relates to this project
// brhg2016_project_query() in functions/custom_query.php
$project = brhg2016_project_query();
?>

<section class="project">
    <h2 class="project__linked-stuff-title"><?php echo $content_area_title; ?></h2>
    <?php
    if (!empty($project)) :
        // Loop through each the array of post types
        foreach ($project as $type => $items) {

            $i = 0;
            // Change change some of the section titles from the post-type names
            switch ($type) {
                case 'post':
                    $title = 'Blog';
                    break;
                case 'pamphlets':
                    $title = 'Publications';
                    break;
                case 'books':
                    $title = 'Book Reviews';
                    break;
                default:
                    $title = get_post_type_object($type)->label;
            }

            if (!empty($items)) :
    ?>
                <section class="project__type-wrap">
                    <h3 class="project__type-title"><?php echo $title; ?> (<?php echo count($items) ?>)</h3>
                    <?php
                    foreach ($items as $post) :
                        setup_postdata($post);

                        $i++;
                        $counter_class = ($i > $number_to_show) ? ' project-item--hide' : '';

                        $thumb_status = brhg2016_archive_thumb('', false);

                        if ($thumb_status == 'text' || $thumb_status == false) {
                            $has_thumb = false;
                            $missing_thumb_class = " project-item--no-thumb";
                        } else {
                            $has_thumb = true;
                            $missing_thumb_class = "";
                        } ?>

                        <div class="project-item <?php echo $missing_thumb_class; ?><?php echo $counter_class ?>">
                            <?php if ($has_thumb) { ?>
                                <div class="project-item__thumb">
                                    <?php brhg2016_archive_thumb(); ?>
                                </div>
                            <?php } ?>
                            <div class="project-item__content">
                                <h4 class="project-item__title">
                                    <a class="project-item__title-link" href="<?php the_permalink(); ?>" rel="bookmark">
                                        <?php echo brhg2016_custom_title(0); ?>
                                    </a>
                                </h4>
                                <div class="project-item__excerpt">
                                    <?php echo brhg2016_custom_excerpt(250); ?>
                                    <a href="<?php the_permalink(); ?>" class="project-item__more-link">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
                    endforeach;

                    wp_reset_postdata();

                endif;

                if ($i > $number_to_show) { ?>
                    <div $id="button-<?php echo esc_attr($type); ?>" class="project__show-btn"></div>
                <?php
                }
                ?>
                </section>
        <?php
        }
    endif; //  $project
        ?>

</section>