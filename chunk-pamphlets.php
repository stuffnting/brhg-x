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
<p class="details-block__p">
    <span class="<?php echo $key_class; ?>">Range: </span>
    <span class="<?php echo $value_class; ?>">
        <?php echo get_the_term_list($post->ID, 'pub_range', '', ', ', ''); ?>
    </span>
</p>

<?php
if (brhg2016_get_item_meta_singles('pamphlet_number', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Number: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('pamphlet_number') ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_connected('author', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">By: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_connected('author'); ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('edition', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Edition: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('edition'); ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('isbn', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">ISBN: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('isbn'); ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('number_of_pages', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Number of pages: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('number_of_pages'); ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('number_of_images', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Number of images: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('number_of_images'); ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('format', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Format: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('format'); ?>
        </span>
    </p>
<?php
}

if (have_rows('pamphlet_reviews', $post->ID)) { ?>
    <p class="details-block__p">
        <span class="details-block__reviews-link <?php echo $value_class; ?>"><a href="#reviews">Reviews</a></span>
    </p>
<?php
}
