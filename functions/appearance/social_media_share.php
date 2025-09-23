<?php

/**
 * Returns an array of post types, who's single posts should show social media share buttons
 * 
 * @return array Post types that show social media buttons on single posts
 */
function brhg2025_show_social_share_on() {
  return array(
    'articles',
    'pamphlets',
    'event_series',
    'event',
    'rad_his_listings',
    'post',
    'books'
  );
}


/**
 * Generates the social media share buttons
 * 
 * @return string Social media share button HTML
 */
function brhg2025_social_media_share() {
  global $post;

  $url = urlencode(get_permalink($post));
  $title = urlencode(get_the_title($post));

  // Twitter
  $twitter_base_url = 'https://x.com/intent/post';
  $twitter_args = array(
    'url'      => $url,
    'text'     => $title,
    'via'      => 'BrisRadHis'
  );

  $twitter_url = add_query_arg($twitter_args, $twitter_base_url);
  $twitter_icon = get_theme_file_uri('images/social-twitter.svg');

  // Facebook
  $fb_base_url = 'https://www.facebook.com/sharer.php';
  $fb_args = array(
    'u' => $url,
    't' => $title
  );

  $fb_url = add_query_arg($fb_args, $fb_base_url);
  $fb_icon = get_theme_file_uri('images/social-facebook.svg');

  // Reddit
  $reddit_base_url = 'https://www.reddit.com/submit';

  $reddit_args = array(
    'url' => $url,
    'title' => $title
  );

  $reddit_url = add_query_arg($reddit_args, $reddit_base_url);
  $reddit_icon = get_theme_file_uri('images/social-reddit.svg');

  $html_out = sprintf(
    "<div class='social-share'>
      <a href='%2\$s' class='social-share__link' title='Share on Twitter' target='_blank' %1\$s><img src='%3\$s' class='social-share__icon no-border'></a>
      <a href='%4\$s' class='social-share__link' title='Share on Facebook' target='_blank' %1\$s><img src='%5\$s' class='social-share__icon no-border'></a>
      <a href='%6\$s' class='social-share__link' title='Share on Reddit' target='_blank' %1\$s><img src='%7\$s' class='social-share__icon no-border'></a>
    </div>",
    'rel="noopener noreferrer"',
    esc_url($twitter_url),
    esc_url($twitter_icon),
    esc_url($fb_url),
    esc_url($fb_icon),
    esc_url($reddit_url),
    esc_url($reddit_icon)
  );

  return $html_out;
}

/**
 * Social Share Shortcode
 */
add_shortcode('brhg_social_share', 'brhg2025_social_share_shortcode');

function brhg2025_social_share_shortcode() {
  return brhg2025_social_media_share();
}
