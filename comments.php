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
            <?php brhg2025_comments_list(); ?>
        </ol>

        <?php if (get_option('page_comments')) { ?>
            <div class="archive-p8n__wrap" aria-label="Comments">
                <?php echo brhg2016_archive_pagination(); ?>
            </div>
        <?php } ?>

    <?php endif; ?>

    <?php
    // Check to see if comments are open for this post
    if (comments_open()) :

        brhg2025_comments_form();

    else : // if comments are closed
    ?>
        <div class="comments__title">Comments are now closed.</div>
    <?php endif; ?>
</section>