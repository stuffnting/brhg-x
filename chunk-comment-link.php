<?php
/**
* Adds the comment link to the details block on single posts
*
* @package WordPress
* @subpackage BRHG2016
* @since  BRHG2016 1.0
*/

comments_popup_link( '<span class="leave-reply">' . __( 'Leave a comment', 'brhg2016' ) . '</span>', 
    '<span class="item-meta-title">'. __( '1 Comment', 'brhg2016' ) . '</span>', 
    '<span class="item-meta-title">'. __( '% Comments', 'brhg2016' ) . '</span>'
);