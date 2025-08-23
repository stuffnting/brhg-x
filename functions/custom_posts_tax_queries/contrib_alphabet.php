<?php 
/**
* Applied automatically to top or bottom of taxonomy archive OR Action Hook for theme is 'brh_alphabet_stuff()'
*
* NOTE at some point re-do this without transient - the theme template does not use it
*
* @package Wordpress
* @subpackage BRHG2016
* @since 1.0
*/

/*
* Setup variables -  change these on installation
*/

//alphabet taxonomy name
global $brh_alpha_tax_name;
$brh_alpha_tax_name = 'contrib_alpha';

//post type
// only works with one post type at the moment
global $brh_alpha_type;
$brh_alpha_type = 'contributors';


//when plugin registers create the taxonomy and an array containing the first letters the title of existing posts
function brh_run_once(){
	global $brh_alpha_tax_name;
	global $brh_alpha_type;
	//call the function below that creates the taxonomy
	brh_create_contrib_alpha_taxonomy();

    if ( false === get_transient( 'brh_run_once' ) ) {
        
        $alphabet = array();
 
        $posts = get_posts(array(
							'numberposts' => -1,
							'post_type'   => $brh_alpha_type,
					) );
 
        foreach( $posts as $p ) :
            //set term as first letter of post title, lower case
            wp_set_object_terms( $p->ID, strtolower(substr($p->post_title, 0, 1)), $brh_alpha_tax_name );
        endforeach;

        set_transient( 'brh_run_once', 'true' );
 
    }
 
}

add_action( 'after_switch_theme', 'brh_run_once' );
//add_action('init','brh_run_once');

//when plugin is deactivated delete the transient data so that if it is re-activated  brh_run_once() will run.
function brh_delete_run_once(){
	delete_transient( 'brh_archive_contrib_alphabet' );
	delete_transient( 'brh_run_once' );
}

add_action( 'switch_theme', 'brh_delete_run_once' );

// if it does not exist Add new taxonomy, NOT hierarchical (like tags)
function brh_create_contrib_alpha_taxonomy(){
	global $brh_alpha_tax_name;
	global $brh_alpha_type;
	
    if(!taxonomy_exists($brh_alpha_tax_name)){
	
		$labels = array(
				'name'              => _x( 'Con Alpha', 'taxonomy general name' ),
				'singular_name'     => _x( 'Con Alpha', 'taxonomy singular name' ),
		);
	
		$con_alpha_args = array(
					'show_ui'  		=> false,
					'hierarchical'  => false,
					'labels'        => $labels,
		);
	
		register_taxonomy($brh_alpha_tax_name, array($brh_alpha_type), $con_alpha_args);
		
     }
}

add_action('init','brh_create_contrib_alpha_taxonomy');

/* When the post is saved, saves our custom data */
function brh_save_first_letter( $post_id ) {
	global $brh_alpha_tax_name;
	global $brh_alpha_type;
	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;
 
	//check location (only run for correct post_type)
	$limitPostTypes = array($brh_alpha_type);
	if (!in_array($_POST['post_type'], $limitPostTypes)) 
	return;
 
	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
	return;
 
 
	//set term as first letter of post title, lower case
	wp_set_post_terms( $post_id, strtolower(substr($_POST['post_title'], 0, 1)), $brh_alpha_tax_name );
 
	//delete the transient that is storing the alphabet letters
	delete_transient( 'brh_archive_contrib_alphabet');
}

if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == $brh_alpha_type ) {
	add_action( 'save_post' , 'brh_save_first_letter' );
}

//When a post is trashed delete the transient data so that a new list will be drawn up
function brh_remove_trashed(){
	//delete the transient that is storing the alphabet letters
	delete_transient( 'brh_archive_contrib_alphabet');
	
}
$brh_trash_what = 'trash_' . $brh_alpha_type;
add_action( $brh_trash_what , 'brh_remove_trashed' );

//When a post is un-trashed delete the transient data so that a new list will be drawn up
function brh_remove_untrashed(){
	//delete the transient that is storing the alphabet letters
	delete_transient( 'brh_archive_contrib_alphabet');
	
}
$brh_untrash_what = 'untrash_' . $brh_alpha_type;
add_action( $brh_untrash_what, 'brh_remove_untrashed' );