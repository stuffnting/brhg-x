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

<?php extract(brhg2025_get_details_block_classes()); ?>
<p class="details-block__p">
    <span class="<?php echo $key_class; ?>">Section: </span>
    <span class="<?php echo $value_class; ?>"> <?php brhg2016_get_item_section(); ?></span>

    <?php if (brhg2016_get_item_sub_section(false)) { ?>
        <span class="<?php echo $value_class; ?> <?php echo $value_class; ?>--sub-section">
            <?php brhg2016_get_item_sub_section(); ?>
        </span>
</p>
<?php } ?>

<?php if (brhg2016_get_item_project(false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Projects: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_project(); ?>
        </span>
    </p>
<?php } ?>

<?php if (in_array('category', get_object_taxonomies(get_post_type(), 'names'))) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Subjects: </span>
        <span class="<?php echo $value_class; ?>"><?php the_category(', ') ?></span>
    </p>
<?php } ?>

<?php
the_tags(
    "<p class='details-block__p'>\n<span class='$key_class'>Tags: </span>
        <span class='$value_class'>",
    ', ',
    '</span></p>'
);
?>
<p class="details-block__p">
    <span class="<?php echo $key_class; ?> <?php echo $key_class; ?>--posted">Posted: </span>
    <span class="<?php echo $value_class; ?> <?php echo $value_class; ?>--posted">
        <?php brhgh2016_post_date(); ?>
    </span>
    <span class="<?php echo $key_class; ?> <?php echo $key_class; ?>--mod">Modified: </span>
    <span class="<?php echo $value_class; ?> <?php echo $value_class; ?>--mod">
        <?php brhgh2016_post_modified(); ?>
    </span>
</p>

<?php
if (current_user_can('edit_posts')) { ?>
    <p class="details-block__p">
        <span class="<?php echo $value_class; ?>"><a href=" <?php echo get_edit_post_link($post->ID) ?>">Edit Post</a></span>
    </p>

<?php
}
