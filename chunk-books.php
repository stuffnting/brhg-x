<?php

/**
 * Displays the book details
 * brhg2016_get_item_meta_singles() is in functions/utility_functions.php
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php extract(brhg2025_get_details_block_classes()); ?>

<?php
if (brhg2016_get_item_meta_singles('author', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Author: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('author') ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('publisher', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Publisher: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('publisher') ?>
        </span>
    </p>
<?php
}

if (brhg2016_get_item_meta_singles('edition', false)) { ?>
    <p class="details-block__p">
        <span class="<?php echo $key_class; ?>">Edition: </span>
        <span class="<?php echo $value_class; ?>">
            <?php brhg2016_get_item_meta_singles('edition') ?>
        </span>
    </p>
<?php
}
