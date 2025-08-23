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
    if (!empty($_SERVER[ 'SCRIPT_FILENAME' ]) && 'comments.php' == basename($_SERVER[ 'SCRIPT_FILENAME' ]))
        die ( ' Please do not load this page directly. Thanks ! ' );

    if ( post_password_required() ) { ?>
        <p class="nocomments">This post is password protected. Enter the password to view comments.</p>
        <?php
        return;
    }

    if ( !post_type_supports( get_post_type(), 'comments' ) ) {
        return;
    }
?>


<section id="comments" class="comment-section" >
    <h3><?php comments_number ( 'No Comments' , '1 Comment' , '% Comments ' );?></h3>

    <?php if ( have_comments () ) : ?>
        <ol class="commentlist">
            <?php 
            $list_args = array(
                'callback'      => 'brhg2016_comment',
                'avatar_size'   => 64,
                'type'          => 'comment',
                'max_depth'     => 3,
                'style' => 'li'
            );
            wp_list_comments( $list_args ); ?>
        </ol>

        <?php if( get_option( 'page_comments' ) ){ ?>
            <div class="pagination">
                <?php paginate_comments_links(); ?> 
            </div>
        <?php } ?>

    <?php endif ; ?>

    <?php
    // Check to see if comments are open for this post
    if ( comments_open() ) :
        // comment_form() adds the <div id="respond"> used by comment-reply script
        //see comment_form() codex
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' );

        $Comment_args = array(
            'cancel_reply_link' =>  __( '(Cancel Reply)', 'brhg2016' ),
            'class_form'        =>  'comment-form form-horizontal',
            'class_submit'      => 'submit btn btn-primary',

            'comment_field'     =>  '<p class="comment-form-comment">
                                        <label class="comment-form-label" for="comment">' . _x( 'Comment', 'noun', 'brhg2016' ) . '</label>
                                    </p>
                                    <div class="error-container">
                                            <textarea id="comment" class="required form-control" name="comment" placeholder="Comment here ..." cols="45" rows="8" aria-required="true" maxlength="2000">' .
                                            '</textarea>
                                    </div>',

            'comment_notes_after'=> '<p class="form-allowed-tags">' .
                                        sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ),
                                            ' <code>' . allowed_tags() . '</code>'
                                        ) . 
                                    '</p>',

            'fields'            =>  apply_filters( 'comment_form_default_fields', array(

                'author'            =>  '<div class="comment-form-author form-group">' .
                                            '<label class="col-sm-2 control-label comment-form-label" for="author">' . __( 'Name', 'brhg2016' ) .( $req ? '<span class="required-mark">*</span>' : '' ) . '</label> ' .
                                            '<div class="col-sm-10">
                                                <input id="author" class="required form-control" name="author" type="text" placeholder="Name"
                                                    value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' />
                                                </div>
                                        </div>',

                'email'             =>  '<div class="comment-form-email form-group">
                                            <label class="col-sm-2 control-label comment-form-label" for="email">' . __( 'Email', 'brhg2016' ) . ( $req ? '<span class="required-mark">*</span>' : '' ) .'</label> ' .
                                            '<div class="col-sm-10">
                                                <input id="email" class="required email form-control" name="email" type="text"  placeholder="email"
                                                    value="' . esc_attr( $commenter['comment_author_email'] ) . '" ' . $aria_req . ' />
                                            </div>
                                        </div>',

                'url'               =>  '<div class="comment-form-url form-group">
                                            <label class="col-sm-2 control-label comment-form-label" for="url">' . __( 'Website', 'brhg2016' ) . '</label>' . 
                                            '<div class="col-sm-10">
                                                <input id="url" class="url form-control" name="url" type="text" placeholder="Website" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />
                                            </div>
                                        </div>'
                ))
        ); ?>

    <?php comment_form( $Comment_args ); ?>

    <?php else : // if comments are closed?>
        <h3>Comments are now closed .</h3>
    <?php endif ; ?>
</section>

 <script type="text/javascript">
    jQuery(document).ready(function($) {
        // validate the comment form when it is submitted
        jQuery("#commentform").validate();
    });
 </script>
 
<?php     
    function brhg2016_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;

        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'article-comment';
        } ?>

        <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">

        <?php if ( 'div' != $args['style'] ) : ?>
            <article id="article-comment-<?php comment_ID(); ?>" class="comment-body">
        <?php endif; ?>
        
        <header class="comment-author vcard">
            <?php 
                if ( $args['avatar_size'] != 0 ) {
                    echo get_avatar( $comment, $args['avatar_size'] ); 
                } 
            ?>

            <div class="comment-info">
                <?php
                    printf( __( '<h4 class="comment-author-title"><cite class="fn">%s</cite></h4>' ), 
                        get_comment_author_link()
                    ); 
                ?>

                <div class="comment-meta commentmetadata">
                    <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
                    <?php
                        printf( '<time datetime="%1$s">' . __('%2$s at %3$s') . '</time>', 
                            get_comment_time('c'),
                            get_comment_date(),  
                            get_comment_time() 
                        ); 
                    ?>
                    </a>
                    <?php edit_comment_link( __( '(Edit)' ), '  ', '' ); ?>
                </div>
            </div>
        </header>

        <?php if ( $comment->comment_approved == '0' ) : ?>
            <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
            <br />
        <?php endif; ?>

        <?php 
            comment_text();

            $reply_arg = array( 
                'add_below' =>  $add_below,
                'depth'     =>  $depth,
                'max_depth' =>  $args['max_depth'],
                'before'    =>  '<div class="reply btn btn-primary btn-xs">',
                'after'     =>  '</div>'
            );

            comment_reply_link( $reply_arg ); 
        ?>

        <?php if ( 'div' != $args['style'] ) : ?>
            </article>
        <?php endif; ?>
        <?php
    }