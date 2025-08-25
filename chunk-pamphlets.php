<?php

/**
 * Displays the pamphlet details
 * brhg2016_get_item_meta_singles() is in functions/utility_functions.php
 *
 * brhg2016_get_item_meta_singles() and brhg2016_get_item_connected() are in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php extract(brhg2025_get_details_block_classes()); ?>

<span class="<?php echo $key_class; ?>">Range: </span>
<span class="<?php echo $value_class; ?>">
    <?php echo get_the_term_list($post->ID, 'pub_range', '', ', ', ''); ?>
</span>

<?php
if (brhg2016_get_item_meta_singles('pamphlet_number', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Number: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('pamphlet_number') ?>
    </span>
<?php
}

if (brhg2016_get_item_connected('author', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">By: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_connected('author'); ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('edition', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Edition: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('edition'); ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('isbn', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">ISBN: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('isbn'); ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('number_of_pages', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Number of pages: </span>
    <span class="value">
        <?php brhg2016_get_item_meta_singles('number_of_pages'); ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('number_of_images', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Number of images: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('number_of_images'); ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('format', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Format: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('format'); ?>
    </span>
<?php
}

if (have_rows('pamphlet_reviews', $post->ID)) { ?>
    <br>
    <span class="details-block__reviews-link"><a href="#reviews">Reviews</span>
<?php
}
