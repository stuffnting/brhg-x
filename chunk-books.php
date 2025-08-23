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

<?php
    if ( brhg2016_get_item_meta_singles( 'author', false ) ) { ?>
        <span class="item-meta-title item-meta-title-author">Author: </span>
        <span class="item-meta-item item-meta-author">
            <?php brhg2016_get_item_meta_singles( 'author' ) ?>
        </span>
        <?php 
    }

    if ( brhg2016_get_item_meta_singles( 'publisher', false ) ) { ?>
        <br>
        <span class="item-meta-title item-meta-title-publisher">Publisher: </span>
        <span class="item-meta-item item-meta-publisher">
            <?php brhg2016_get_item_meta_singles( 'publisher' ) ?>
        </span>
        <?php 
    }

    if ( brhg2016_get_item_meta_singles( 'edition', false ) ) { ?>
        <br>
        <span class="item-meta-title item-meta-title-edition">Edition: </span>
        <span class="item-meta-item item-meta-edition">
            <?php brhg2016_get_item_meta_singles( 'edition' ) ?>
        </span>
        <?php 
    }