<?php
// Get the newest comments on the first comments page
add_filter('comments_template_query_args', 'brhg2025_reverse_comments_order');

function brhg2025_reverse_comments_order($comment_args) {
  $comment_args['order'] = 'DESC';
  return $comment_args;
}

/**
 * Generate the comments list
 */
function brhg2025_comments_list() {
  $list_args = array(
    'callback'          => 'brhg2016_comment',
    'avatar_size'       => 64,
    'type'              => 'comment',
    'max_depth'         => 3,
    'style'             => 'li',
    //'per_page'          => 10,
    'reverse_top_level' => false,
  );

  wp_list_comments($list_args);
}

/**
 * Generate the comments form
 *
 */
function brhg2025_comments_form() {

  /**
   * Alter the WP default fields. This section altered from /wp-includes/comment-template.php Line 2537
   */
  global $post;

  $post_id = $post->ID;

  $commenter = wp_get_current_commenter();
  $user          = wp_get_current_user();
  $user_identity = $user->exists() ? $user->display_name : '';
  $required_text      = ' ' . wp_required_field_message();

  $author = sprintf(
    '<div class="comments__input comments__input--author">%s %s</div>',
    sprintf(
      '<label for="author">%s%s</label>',
      __('Name'),
      ' *'
    ),
    sprintf(
      '<input id="author" name="author" type="text" value="%s" size="30" maxlength="245" autocomplete="name"%s />',
      esc_attr($commenter['comment_author']),
      ' required' // Must be HTML5 to use `required`
    )
  );

  $email  = sprintf(
    '<div class="comments__input comments__input--email">%s %s</div>',
    sprintf(
      '<label for="email">%s%s</label>',
      __('Email'),
      ' *'
    ),
    sprintf(
      '<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes" autocomplete="email" %s />',
      'type="email"',  // Must be HTML5 to use `type='email'`
      esc_attr($commenter['comment_author_email']),
      'required' // Must be HTML5 to use `required`
    )
  );

  if (has_action('set_comment_cookies', 'wp_set_comment_cookies') && get_option('show_comments_cookies_opt_in')) {
    $consent = empty($commenter['comment_author_email']) ? '' : ' checked'; // Must be HTML5 to use `checked`

    $cookies = sprintf(
      '<div class="comments__input comments__input--cookies-consent">%s %s</div>',
      sprintf(
        '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s />',
        $consent
      ),
      sprintf(
        '<label for="wp-comment-cookies-consent">%s</label>',
        __('Save my name and email in this browser for the next time I comment.')
      )
    );
  }

  $comment_field = sprintf(
    '<div class="comments__comment-textarea">%s %s</div>',
    sprintf(
      '<label for="comment">%s%s</label>',
      _x('Comment', 'noun'),
      ' *'
    ),
    '<textarea id="comment" class="comments__textarea" name="comment" cols="45" rows="8" maxlength="65525" required></textarea>'
  );

  $logged_in_as = sprintf(
    '<p class="comments__note-before logged-in-as">%s%s</p>',
    sprintf(
      /* translators: 1: User name, 2: Edit user link, 3: Logout URL. */
      __('Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a>'),
      $user_identity,
      get_edit_user_link(),
      /** This filter is documented in wp-includes/link-template.php */
      wp_logout_url(apply_filters('the_permalink', get_permalink($post_id), $post_id))
    ),
    $required_text
  );


  /**
   * Make the form
   */
  $Comment_args = array(
    'class_form'              => 'comments__form',
    'class_submit'            => 'comments__submit-btn',
    'class_container'         => 'comments__form-wrap',
    'class_submit'            => 'comments__submit-btn',
    'logged_in_as'            => $logged_in_as,
    'title_reply'             => 'Leave a comment',
    'title_reply_before'      => '<p id="reply-title" class="comments__reply-title">',
    'title_reply_after'       => '</p>',
    'comment_notes_before'    => '<p class="comments__note-before">Your email address will not be published. Required fields are marked *</p>',
    'comment_notes_after'     => '<p class="comments__note-after">You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:  <code>&lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;s&gt; &lt;strike&gt; &lt;strong&gt; </code></p>',
    'comment_field'           => $comment_field,
    'fields'                  => array(
      'author' => $author,
      'email' => $email,
      'cookies' => $cookies
    ),
    'cancel_reply_link'       => ' (cancel reply)',

  );

  comment_form($Comment_args);
}


/**
 * Callback to format the list of comments
 * 
 */
function brhg2016_comment($comment, $args, $depth) {
  //$GLOBALS['comment'] = $comment;

  if ('div' == $args['style']) {
    $tag = 'div';
    $add_below = 'comment';
  } else {
    $tag = 'li';
    $add_below = 'article-comment';
  }

  //$extra_class = empty($args['has_children']) ? '' : ' comments__parent';
  $class = "comments__comment";

?>

  <<?php echo $tag ?> <?php comment_class($class); ?> id="comment-<?php comment_ID(); ?>">

    <?php if ('div' != $args['style']) : ?>
      <article id="article-comment-<?php comment_ID(); ?>" class="comments__body">
      <?php endif; ?>

      <footer class="comments__meta">
        <?php
        if ($args['avatar_size'] != 0) {
          echo get_avatar($comment, $args['avatar_size']);
        }
        ?>

        <div class="comments__info">
          <div class="comments__author">
            <?php echo get_comment_author(); ?>
            <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>" class="comments__comment-link">
              (link to here)
            </a>
          </div>

          <div class="comments__date">
            <?php
            printf(
              '<time datetime="%1$s">' . __('%2$s at %3$s') . '</time>',
              get_comment_time('c'),
              get_comment_date(),
              get_comment_time()
            );
            ?>
            <?php edit_comment_link(__('(Edit)'), '  ', ''); ?>
          </div>
        </div>
      </footer>

      <?php if ($comment->comment_approved == '0') : ?>
        <em class="comments__awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></em>
        <br />
      <?php endif; ?>

      <div class="comments__text">
        <?php comment_text(); ?>
      </div>

      <?php

      $reply_arg = array(
        'add_below' =>  $add_below,
        'depth'     =>  $depth,
        'max_depth' =>  $args['max_depth'],
        'before'    =>  '<div class="comments__reply-btn">',
        'after'     =>  '</div>'
      );

      comment_reply_link($reply_arg);
      ?>

      <?php if ('div' != $args['style']) : ?>
      </article>
    <?php endif; ?>
  <?php
}
