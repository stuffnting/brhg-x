<?php
/*
*
* code for the posts-2-posts plugin.
*
* The [event-list] shortcode is in shortcodes.php
*/


//define the connections
function brhg2016_p2p_go() {
	// Make sure the Posts 2 Posts plugin is active.
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;

	p2p_register_connection_type( array(
		'name' => 'speaker_to_event',
		'from' => 'contributors',
		'to' => 'events',
		'sortable' => 'any',
	) );

	p2p_register_connection_type( array(
		'name' => 'events_to_series',
		'from' => 'events',
		'to' => 'event_series',
		'sortable' => 'any',
	) );

	p2p_register_connection_type( array(
		'name' => 'venue_to_events',
		'from' => 'venues',
		'to' => 'events',
		'sortable' => 'any',
	) );

	p2p_register_connection_type( array(
		'name' => 'speakers_to_pamphlets',
		'from' => 'contributors',
		'to' => 'pamphlets',
		'sortable' => 'any',
	) );

	p2p_register_connection_type( array(
		'name' => 'author_to_articles',
		'from' => 'contributors',
		'to' => 'articles',
		'sortable' => 'any',
	) );	
    
    p2p_register_connection_type( array(
		'name' => 'author_to_post',
		'from' => 'contributors',
		'to' => 'post',
		'sortable' => 'any',
	) );

}

add_action( 'p2p_init', 'brhg2016_p2p_go' );

//add the connected pages to $wp_query for each item already in $wp_query, they can then be listed by p2p_list_posts() in the loop
function brhg2016_add_connected_items(){
global $wp_query;
if(function_exists('p2p_type')):
	p2p_type( 'speakers_to_pamphlets' )->each_connected( $wp_query, array(), 'author' );
	p2p_type( 'speaker_to_event' )->each_connected( $wp_query, array(), 'speakers' );
	p2p_type( 'venue_to_events' )->each_connected( $wp_query, array(), 'venues' );
	p2p_type( 'events_to_series' )->each_connected( $wp_query, array(), 'series' );
	p2p_type( 'author_to_articles' )->each_connected( $wp_query, array(), 'article_author' );
    p2p_type( 'author_to_post' )->each_connected( $wp_query, array(), 'post_contri_author' );
	endif;
}

add_action('wp_head','brhg2016_add_connected_items' );

//add a template hook to use in second loops
function connection_hook(){
	do_action('connection_hook');
}

add_action('connection_hook','add_connected_items' );
