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
    <span class="<?php echo $key_class; ?>">Author: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('author') ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('publisher', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Publisher: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('publisher') ?>
    </span>
<?php
}

if (brhg2016_get_item_meta_singles('edition', false)) { ?>
    <br>
    <span class="<?php echo $key_class; ?>">Edition: </span>
    <span class="<?php echo $value_class; ?>">
        <?php brhg2016_get_item_meta_singles('edition') ?>
    </span>
<?php
}
