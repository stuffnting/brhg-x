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

<span class="item-meta-title item-meta-title-pub-range">Range: </span>
<span class="item-meta-item item-meta-pub-range">
    <?php echo get_the_term_list( $post->ID, 'pub_range', '' , ', ', '' ); ?>
</span>

<?php
if ( brhg2016_get_item_meta_singles( 'pamphlet_number', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-number">Number: </span>
    <span class="item-meta-item item-meta-pub-number">
        <?php brhg2016_get_item_meta_singles( 'pamphlet_number' ) ?>
    </span>
    <?php
}

if ( brhg2016_get_item_connected( 'author', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-author">By: </span>
    <span class="item-meta-item item-meta-pub-author">
        <?php brhg2016_get_item_connected( 'author' ); ?>
    </span>
    <?php
}

if ( brhg2016_get_item_meta_singles( 'edition', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-edition">Edition: </span>
    <span class="item-meta-item item-meta-pub-edition">
        <?php brhg2016_get_item_meta_singles( 'edition' ); ?>
    </span>
    <?php
}

if ( brhg2016_get_item_meta_singles( 'isbn', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-isbn">ISBN: </span>
    <span class="item-meta-item item-meta-pub-isbn">
        <?php brhg2016_get_item_meta_singles( 'isbn' ); ?>
    </span>
    <?php
}

if ( brhg2016_get_item_meta_singles( 'number_of_pages', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-pages">Number of pages: </span>
    <span class="item-meta-item item-meta-pub-pages">
        <?php brhg2016_get_item_meta_singles( 'number_of_pages' ); ?>
    </span>
    <?php
}

if ( brhg2016_get_item_meta_singles( 'number_of_images', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-images">Number of images: </span>
    <span class="item-meta-item item-meta-pub-images">
        <?php brhg2016_get_item_meta_singles( 'number_of_images' ); ?>
    </span>
    <?php
}

if ( brhg2016_get_item_meta_singles( 'format', false ) ) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-format">Format: </span>
    <span class="item-meta-item item-meta-pub-format">
        <?php brhg2016_get_item_meta_singles( 'format' ); ?>
    </span>
    <?php
}

if (have_rows('pamphlet_reviews', $post->ID)) { ?>
    <br>
    <span class="item-meta-title item-meta-title-pub-reviews"><a href="#reviews">Reviews</span>
    <?php
}