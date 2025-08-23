<?php
/**
* The template file that gathers together the details for an individual item.
* Used on single posts and for separate items on archive pages.
*
* @package WordPress
* @subpackage BRHG2016
* @since  BRHG2016 1.0
*
*/

?>

<span class="item-meta-title item-meta-title-section">Section: </span>
<span class="item-meta-item item-meta-section"> <?php brhg2016_get_item_section(); ?></span>

<?php if ( brhg2016_get_item_sub_section( false ) ) { ?>
    <span class="item-sub-section-seperator">=></span>
    <span class="item-meta-item item-meta-sub-section">
         <?php brhg2016_get_item_sub_section(); ?>
    </span>
<?php } ?>

<?php if ( brhg2016_get_item_project( false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-projects">Projects: </span>
    <span class="item-meta-item item-meta-sub-project">
         <?php brhg2016_get_item_project(); ?>
    </span>
<?php } ?>

<?php if ( in_array( 'category', get_object_taxonomies( get_post_type(), 'names' ) ) ) { ?>
<br>
<span class="item-meta-title item-meta-title-subject">Subjects: </span>
<span class="item-meta-item item-meta-subject"><?php the_category( ', ' ) ?></span>
<?php } ?>

<?php 
    the_tags( 
        "<br>\n<span class='item-meta-title item-meta-title-tags'>Tags: </span>
        <span class='item-meta-item item-meta-tags'>", 
        ', ', 
        '</span>'
    );
?>
<br>
<span class="item-meta-title item-meta-title-published">Posted: </span>
<span class="item-meta-item item-meta-sub-published">
    <?php brhgh2016_post_date(); ?>
</span>
<span class="item-meta-title item-meta-title-modified">Modified: </span>
<span class="item-meta-item item-meta-sub-modified">
    <?php brhgh2016_post_modified(); ?>
</span>