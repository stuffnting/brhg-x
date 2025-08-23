<?php
/**
 * The template part for displaying a message that posts cannot be found
 *
 *
 * @package WordPress
 * @subpackage BRHG2016
 * @since 1.0
 */
?>

<section class="no-results not-found">
	<header class="page-header">

	<?php	if ( get_query_var( 'special_url' ) === 'event-diary' ) {
		$title = "No events in the diary" ;
	} else{
		$title =  __( 'Nothing found', 'brhg2016' );
	}
	?>

		<h1 class="page-title"><?php echo $title; ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'brhg2016' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'brhg2016' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'brhg2016' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	</div><!-- .page-content -->
</section><!-- .no-results -->