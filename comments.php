<?php

/**
 * The comments template
 *
 * Includes the callback function brhg2016_comment()
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */
?>

<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die(' Please do not load this page directly. Thanks ! ');

if (post_password_required()) { ?>
    <p class="nocomments">This post is password protected. Enter the password to view comments.</p>
<?php
    return;
}

if (!post_type_supports(get_post_type(), 'comments')) {
    return;
}
?>


<section id="comments" class="comments__section">
    <div class="comments__title"><?php comments_number('No Comments Yet', '1 Comment', '% Comments '); ?></div>

    <?php if (have_comments()) : ?>
        <ol class="comments__list">
            <?php
            $list_args = array(
                'callback'          => 'brhg2016_comment',
                'avatar_size'       => 64,
                'type'              => 'comment',
                'max_depth'         => 3,
                'style'             => 'li',
                //'per_page'          => 10,
                'reverse_top_level' => true,
            );
            wp_list_comments($list_args); ?>
        </ol>



        <?php if (get_option('page_comments')) { ?>
            <div class="archive-p8n__wrap" aria-label="Comments">
                <?php paginate_comments_links(); ?>
            </div>
        <?php } ?>

    <?php endif; ?>

    <?php
    // Check to see if comments are open for this post
    if (comments_open()) :

        $Comment_args = array(
            'class_form'              => 'comments__form',
            'class_submit'            => 'comments__submit-btn',
            'class_container'         => 'comments__form-wrap',
            'class_form'              => 'comments__form',
            'class_submit'            => 'comments__submit-btn',
            'title_reply'             => 'Leave a comment',
            'title_reply_before'      => '<p id="reply-title" class="comments__reply-title">',
            'title_reply_after'       => '</p>',
            'comment_notes_before'    => '<p class="comments__note-before">Your email address will not be published. Required fields are marked *</p>',
            'comment_notes_after'     => '<p class="comments__note-after">You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:  <code>&lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;s&gt; &lt;strike&gt; &lt;strong&gt; </code></p>',
            'cancel_reply_link'       => ' (cancel reply)',

        );

        comment_form($Comment_args);

    else : // if comments are closed
    ?>
        <div class="comments__title">Comments are now closed.</div>
    <?php endif; ?>
</section>

<?php
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
