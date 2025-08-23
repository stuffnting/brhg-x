<?php
/**
 * Pamphlets' Archive Page and Collections
 *
 * @package Wordpress
 * @subpackage BRHG2016
 * @since BRHG2016 1.0
 */

get_header(); 

  // set = a publication range or collection

	$special_url = get_query_var( 'special_url', false );

    // /publication-collections/ is a special URL, but the /pamphleteer/ is not.
	if ( get_query_var( 'special_url', false ) ) {

		// $GLOBALS['brhg_publication_collections'] set in functions/custom_query.php via brhg2016_special_urls() 
		// $brhg_publication_collections will be a WP_Error object if there are no collections
		$pub_sets = $brhg_publication_collections;

	} elseif ( get_query_var('post_type') === 'pamphlets' ) {

		// Get a list of terms from pub_range Bristol Radical Pamphleteer, Book, Reprints etc.
		$pub_range_args = array( 
			'orderby'			=> 'id',
			'order'				=> 'DESC',
			'hide_empty'	=> true,
		);
		
		//  Returns WP_Error object if not pub_range terms
		$pub_sets = get_terms( 'pub_range', $pub_range_args );
	}
?>

<?php 
	if ( is_wp_error( $pub_sets ) ) {
		echo "<h2>No publications to see here!</h2>";
	}  else {
		// Use include and locate_template so that variable can be passed and child themes can override.
		include( locate_template('content-thumbs-only-list.php') ); 
	}
?>

<?php get_footer();


?>