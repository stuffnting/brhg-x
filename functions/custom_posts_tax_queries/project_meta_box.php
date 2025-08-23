<?php

/**
 * Calls the class on the post edit screen.
 */
function brhg2016_project_meta()
{
    new BRHG2016ProjectMetaBox();
}

if ( is_admin() )
{
    add_action( 'load-post.php', 'brhg2016_project_meta' );
    add_action( 'load-post-new.php', 'brhg2016_project_meta' );
}


/*
*
*
*
*/
function brhg2016_project_allowed_post_types(){

    return array( 'articles', 'pamphlets', 'books', 'event_series', 'events', 'post' );

}

/** 
 * The Class.
 */
class BRHG2016ProjectMetaBox{
    // Post Type to link to
    private $post_type_link_to = array('project');
    
    // Post Types which can link
    private $post_types_allowed = array();
   

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct()
    {
    
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
        add_action( 'save_post', array( $this, 'save' ) );

    }
    
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $passed_post_type, $post ) { 

            $this->post_types_allowed = brhg2016_project_allowed_post_types();
            
            if ( in_array( $passed_post_type, $this->post_types_allowed )) {
        
        add_meta_box(
            'product-meta',
            __( 'Linked Projects.', 'brhg2016' ),
            array( $this, 'render_meta_box_content' ),
            $passed_post_type,
            'side',
            'core'
        );
            }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id )
    {
    
        if ( ! isset( $_POST['brhg2016_product_meta_nonce'] ) )
            return $post_id;

        $nonce = $_POST['brhg2016_product_meta_nonce'];

        if ( ! wp_verify_nonce( $nonce, 'brhg2016_save_product_meta' ) )
            return $post_id;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id;

        if ( 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
    
        }else {

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }
                          

        
        //Project Checkbox
        //Delete old values first to prevent them being carried over
        delete_post_meta( $post_id, 'brhg2016_project' );
        if( isset( $_POST['brhg2016_project'] ) ) {
            foreach( $_POST['brhg2016_project'] as $project ) :                
             add_post_meta( $post_id, 'brhg2016_project', absint( $project ) );
            endforeach;
        }
        
    }


    public function render_meta_box_content( $post )
    {
        
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'brhg2016_save_product_meta', 'brhg2016_product_meta_nonce' );

    $values = get_post_custom( $post->ID );
                
    $project = $this->brhg2016_post_type_list( $this->post_type_link_to, 'all' );
    
    ?>
 
                <ul id="project-list" class="linked-projects form-no-clear">
         
                <?php
                $i = 0;
                foreach( $project as $item ):
                    $check = ( isset( $values['brhg2016_project'] )  &&  in_array( $item->ID, $values['brhg2016_project'] ) ) ? 1 : 0;
        ?>
        
                    <li id="project-list-<?php echo $item->ID ?>">
            <input
                type="checkbox"
                id="project-<?php echo $item->ID; ?>"
                name="brhg2016_project[<?php echo $i; ?>]"
                value = "<?php echo esc_attr($item->ID); ?>"
                <?php checked( $check, "1" ); ?>
            />
            <label for="project-<?php echo $item->ID ?>"><?php echo $item->post_title; ?></label>
                    </li>
        <?php $i++;
                endforeach; ?>
                </ul>
                  
    
                                
                <?php
    }
        
        public function brhg2016_post_type_list( $post_type, $fields ){
            $args = array(
                        'posts_per_page'   => -1,
                        'orderby'          => 'title',
                        'order'            => 'ASC',
                        'post_type'        => $post_type,
                        'post_status'      => 'publish',
                        'fields'           => $fields
                );
                
            $output = new WP_Query( $args );
                                                  
            return  $output->posts;
        }
}