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

        <h2 class="project-linked-top"><?php echo $content_area_title; ?></h2>


<?php
    if ( $project ) :
        // This is the content retrieved for the project and returned by brhg2016_project_query()
        $project_items = $project['project_items'];
        // This is an array of post types that have items link to this project. Returned by brhg2016_project_query()
        // The order of post types is also set by brhg2016_project_query()
        $linked_post_types = $project['linked_post_types'];

        // Loop through each the array of post types
        foreach( $linked_post_types as $item ){
            // Check if this $item value is an integer, this means there were no items from the post-type linked to this project
            // See brhg2016_project_query() in functions/custom_query.php
            if ( is_int( $item ) ) {
                continue;
            }

            $i = 0;
            // Change change some of the section titles from the post-type names
            switch ( $item ) {
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
                    $title = get_post_type_object( $item )->label;
            }
            ?>
            <section class="projected-post-type">
                <h3 class="project-linked-header"><?php echo $title; ?></h3>
                <?php
                    if ( $project_items->have_posts() ) : 
                        while ( $project_items->have_posts() ) : $project_items->the_post();

                            if ( $post->post_type === $item ) {
                                $i++;
                                $couter_class = ( $i > $number_to_show ) ? 'project-show-counter' : '' ;

                                $missing_thumb_class = brhg2016_archive_thumb('big_thumb', false) 
                                    ? "" 
                                    : "archive-item-missing-thumb";
                                ?>
                                <div class="project-item <?php echo $couter_class ?>">
                                    <div class="project-item-thumb <?php echo $missing_thumb_class; ?>">
                                        <?php brhg2016_archive_thumb(); ?>
                                    </div>
                                    <div class="project-item-content">
                                        <h4 class="project-item-title">
                                            <a class="project-title-link" title="More about <?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" rel="bookmark">
                                                <?php echo brhg2016_custom_title(0) ; ?>
                                            </a>
                                        </h4>
                                        <div class="project-item-excerpt">
                                            <?php echo brhg2016_custom_excerpt( 250 ); ?> <span class="project-item-read-more"><a href="<?php the_permalink(); ?>">Read More =></a></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }

                        endwhile;
                    endif;

                    rewind_posts();

                    if ( $i > $number_to_show ){ ?>
                    
                        <div $id="button-<?php echo esc_attr( $item ); ?>" class="project-show-button"></div>
                        <?php
                    }
                ?>
            </section>
            <?php
        }
    endif; //  $project
?>