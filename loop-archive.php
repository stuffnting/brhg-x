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
$archive_item_type = "";

if ( has_term( 'gallery', 'article_type' ) ) {

    $archive_item_type = " archive-item-gallery";

} elseif ( has_term( 'video-2', 'article_type' ) && brhg2016_get_vids( 1, false ) ) {

    $archive_item_type = " archive-item-video";

} 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'archive-item' ); ?>>
    <header class="archive-item-header">
        <h2 class='archive-item-title'><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php get_template_part( 'chunk', 'post-title-meta' ); ?>
    </header>

    <div class="archive-entry-content<?php echo $archive_item_type; ?>">
        
        <?php
        /*  
        *   brhg2016_get_vids() finds videos from embed urls. 
        *   If the post has a video included in another way it won't be found.
        *   $vids is used to indicate if an embed video was found in a article-type = video post
        */
        $vids = 0;
        
        if ( has_term( 'video-2', 'article_type' ) ) { ?>

                <div class="archive-video">
                    <?php 
                    // If a video is found output video and set $vids to true, no videos and $vids set to 0
                    $vids = brhg2016_get_vids( 1 ); 
                    ?>
                </div>
                <?php
        } 

        // Only show title for these post types, don't show thumb and excerpt
        $no_excerpt_or_thumb = array( 'contributors', 'venues' );

        if ( !in_array( get_post_type(), $no_excerpt_or_thumb ) && !$vids && !has_term( 'gallery', 'article_type' ) ) {

            // Find out if there is a thumbnail to be used
            $class = ( brhg2016_archive_thumb( '', false ) ) ? '' : 'archive-item-missing-thumb';
            ?>
   
            <?php 
                $excerpt_no_thumb = array('rad_his_listings'); 
                $no_thumb_class = " archive-item-excerpt-no-thumb";
                
                if ( !in_array( get_post_type(),  $excerpt_no_thumb ) ) { 
                    $no_thumb_class = "";
            ?>

            <div class='archive-item-thumb-wrap <?php echo $class ?>'>      
                <?php brhg2016_archive_thumb(); ?>
            </div>

            <?php 
               }
            ?>

            <div class="archive-item-excerpt<?php echo $no_thumb_class; ?>">
                <?php echo brhg2016_custom_excerpt( 500 ); ?>
            </div>
                
            
        <?php
        } elseif ( has_term( 'gallery', 'article_type' ) ) {
            /** 
             * Show the featured image and 3 other images for a gallery
             * 
             * Get all images attached to the gallery post
             * get_attached_media() returns an array who's keys are attachment IDs
             * Then use array_rand() to choose 4 of these keys at random, and place them in an array
             * 
             */
            $post_thumb = get_post_thumbnail_id();
            $images = get_attached_media( 'image' );
            $chosen_images = array_rand( $images, 4 );

            // If the featured image is not in the chosen images, add it and take one of the others out.
            if ( $post_thumb !== false && !in_array( $post_thumb, $chosen_images ) ) {

                array_unshift( $chosen_images, $post_thumb );
                array_pop( $chosen_images );

            }

            foreach ( $chosen_images as $image ) { ?>

                <figure class="listing-thumb-only archive-gallery-pics">
                    <a href="<?php the_permalink(); ?>" class="listing-thumb-only-img-link">
                        <?php echo wp_get_attachment_image( $image, 'big_thumb' ); ?>
                    </a>
                </figure>
                
                <?php
            }

         } ?>
            
            <div class="archive-read-more-button-wrap">
                <a href="<?php the_permalink() ;?>" class="read-more-button"><?php _e( 'Read More', 'brhg2016' );?> &raquo;</a>
            </div>
    </div>

    <footer class="archive-item-footer">
        <div class="archive-item-meta-wrap">
                <?php 
                    $class = '';
                    if ( get_query_var( 'special_url', 'none' ) != 'event-diary' && get_post_type() == 'events' ) {
                        $class  = 'archive-event-meta';
                        ?>
                            <div class="archive-item-meta archive-item-event-details-wrap">
                                <span class="item-meta-title">Event Details </span><br>
                                 <?php get_template_part( 'chunk', 'events' ); ?>
                            </div>
                        <?php
                    } 
                ?>
            <div class="<?php echo $class ?> archive-item-meta archive-item-details-wrap">
                <?php get_template_part( 'chunk', 'item-details' ); ?>
            </div>
        </div>
    </footer> 
</article>