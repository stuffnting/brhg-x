<?php

/**
 * Filters a single post content. Called from the single.php template file.
 * 
 * Pamphlets: adds cover images, where to buy link,reviews, and buy-book box.
 * Book Reviews: adds covers.
 * Contributors: add the list of involvement.
 * 
 * Adds img-responsive class to img tags.
 * Adds footnotes.
 * 
 * @return string The filtered post content
 */
function brhg2016_content_filter() {
    global $post;

    if (is_admin()) {
        return;
    }

    $content_out = '';

    $content = str_replace('&nbsp;', '', get_the_content());

    // Add a Where To Buy link and covers to pamphlets
    if (get_post_type() == 'pamphlets') :

        // brhg2024_add_pamphlet_content() is in functions/publications/publication_the_content_filter.php
        $content_out = brhg2024_add_pamphlet_content($content);

    // Add book covers
    elseif (get_post_type() == 'books' && has_post_thumbnail($post->ID)) :

        $book_cover_html = wp_get_attachment_image(get_post_thumbnail_id($post->ID), 'big_thumb');
        $book_html_out = "<div id='pub-covers' class='books__single-covers'>$book_cover_html</div>\n";
        $content_out = $content . $book_html_out;

    // Add list of involvement to contributors
    elseif (get_post_type() == 'contributors') :

        $contributor_list =  brhg2016_add_contributor_list();
        $contributor_list_html = "<div class='single-contributor-list'>$contributor_list</div>\n";
        $content_out = $content . $contributor_list_html;

    else :

        $content_out = $content;

    endif;

    // Add responsive image class to attached images (galleries not processed yet)
    $content_out = str_replace('<img class="', '<img class="img-responsive ', $content_out);

    // Deal with the Programme List for Event Series
    // This is no longer dealt with here 

    // Add Footnotes for Wordpress footnotes list, now done in functions/footnotes_shortcode.php

    // Apply the normal filters to the content, this includes the [event_list] shortcode
    // The [event_list] shortcode is in functions/post-2-posts.php 
    echo apply_filters('the_content', $content_out);
}



/**
 * Add the contributor's list to after the content of a single Contributor item.
 * Runs off the the_content filter added by brhg2016_add_single_after_contents() above.
 * p2p_list_posts() if from the post-2-post plugin. 
 *
 * @param string     $contents the contents passed by the the_content filter.
 *
 * @return string    The contributor's list appended to $content.
 */
function brhg2016_add_contributor_list() {
    if (function_exists('p2p_list_posts')):
        global $post;
        ob_start();

        //events
        p2p_list_posts($post->speakers, array(
            'before_list' => '<p class="single-contributor-list-title">Appeared at: </p><ul class="single-contributor-list q-list">',
            'after_list'  => '</ul>',
            'before_item' => '<li>',
            'after_item'  => '</li>',
        ));
        //pamphlets
        p2p_list_posts($post->author, array(
            'before_list' => '<p class="single-contributor-list-title">BRHG Publications: </p><ul class="single-contributor-list q-list">',
            'after_list'  => '</ul>',
            'before_item' => '<li>',
            'after_item'  => '</li>',
        ));
        //articles
        p2p_list_posts($post->article_author, array(
            'before_list' => '<p class="single-contributor-list-title">Articles: </p><ul class="single-contributor-list q-list">',
            'after_list'  => '</ul>',
            'before_item' => '<li>',
            'after_item'  => '</li>',
        ));
        //Blog Posts
        p2p_list_posts($post->post_contri_author, array(
            'before_list' => '<p class="single-contributor-list-title">Blog Posts: </p><ul class="single-contributor-list q-list">',
            'after_list'  => '</ul>',
            'before_item' => '<li>',
            'after_item'  => '</li>',
        ));

        $contributor_list = ob_get_contents();
        ob_end_clean();

        return $contributor_list;

    endif; // p2p_list_posts exists
}
