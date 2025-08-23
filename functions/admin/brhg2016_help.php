<?php 
/**
*
* Help page
*
* @package Wordpress
* @subpackage BRHG2016
* @since 1.0
*/

class BRHG2016Help{
        
        public $options;
        
        public function __construct()
        {
                //clear out
                //delete_option('brhg_2016_help');
                
                //use $name from register_option()
                //this option will be an array who's keys are the $ids from add_settings_field() and <input> name attributes
                $this->register_settings_and_fields();
                
                if(get_option('brhg_2016_help')){
                        $this->options = get_option('brhg_2016_help');
                }else{
                        /*
                         *this line stops media_handle_upload() throwing an error the first time the save button is pressed
                         *params
                         *$option_name (use the same name that register_setting() will use, see below)
                         *$value (leave as a blank array)
                         *$depreciated (not used anymore)
                         *$autoload (see codex)
                         *
                         */
                        add_option('brhg_2016_help', array(), '', 'no');
                }


                $this->add_brhg_help_menu_page();
        }

        //register the settings page so that it appears in admin menu
        public function add_brhg_help_menu_page()
        {
                /*
                *add_options_page() is a wrapper for add_submenu_page and only adds a page to the Settings Menu
                *add_theme_page() adds to the appearance menu
                *add_submenu_page() used any add a page to any menu
                *
                *Params:
                *[*** add_submenu_page() only $parent_slug () see codex]
                *$page_title (Title at top of settings page),
                *$menu_title (Title in admin settings menu),
                *$capability (user permissions),
                *$menu_slug (can use __FILE__ to ensure it is unique),
                *$cb_function
                *
                *Note: you can't use $this in the call-back function array because we call the add_menu_page() method statically
                */
                
               //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
                //add_theme_page('BRHG2016 Help', 'BRHG2016 Help', 'administrator', __FILE__, array( 'BRHG2016Help', 'display_options_page' ));
                add_menu_page('BRHG2016 Help', 'BRHG2016 Help', 'edit_posts', 'brhg-help', array( $this, 'display_brhg_help_page' ), 'dashicons-editor-help' );
        } 
        
        //layout the settings page
        public function display_brhg_help_page()
        {
                ?>
                <div class = "wrap">
                        <h2><?php _e('BRHG 2016 Help', 'brhg2016') ?></h2>
                        <?php
                        //make sure enctype attribute is set to "multipart/form-data" if files are being uploaded
                        ?>
                        <form method = "post" action = "options.php" enctype = "multipart/form-data">

                                <?php
                                //add nonce etc.
                                //param is $option_group from register_setting();
                                settings_fields('brhg_2016_help_group');
                                //output the settings fields for this page
                                //param is $page
                                //i.e. add all fields with same $page value
                                do_settings_sections( __FILE__ );
                                ?>
                                
                        </form>
                </div>
                <?php
               
        }
        
        /*
         *Settings
         *
         */
        public function register_settings_and_fields()
        {
                /*
                 *GROUP
                 *
                 *params:
                 *$option_group (unique name, used for nonce etc. see settings_fields()),
                 *$option_name(unique name, see get_option() e.g. $option = get_option('$option_name'), appears in options.php),
                 *$sanitize_callback (optional, processes the options before processing)
                 *get_option() is used to retrieve the options when they are needed
                 *
                 *Note: the option ($option_name) will appear on wp_admin/options.php after the save button has bee pressed for the first time.
                 *If media_handle_upload() or wp_handle_upload are being used make sure that add_option() has already created the option of the same name
                 *before these functions are run when the save button is pressed for the first time. see http://wpquestions.com/question/show/id/1339
                 */
                
                register_setting('brhg_2016_help_group' ,'brhg_2016_help');
                
                /*
                 *SECTION
                 *
                 *It is possible to add more than one section of settings to the settings page
                 *params: $id (see add_settings_section()),
                 *$title_of_section (as seen on the settings page),
                 *$callback, for extra content in the section
                 *$page (see add_options_page() 4th param and do_settings_sections() param, the page to which these setting belong)
                 */
                add_settings_section( 'brhg2016_other', __('Other Help', 'brhg2016'), array( $this, 'brhg2016_other_cb' ), __FILE__ );
                add_settings_section( 'brhg2016_post_types', __('Post Types', 'brhg2016'), array( $this, 'brhg2016_post_types_cb' ), __FILE__ );
                add_settings_section( 'brhg2016_taxonomies', __('Taxonomies', 'brhg2016'), array( $this, 'brhg2016_taxomonies_cb' ), __FILE__ );
                
                /*
                 *FIELD
                 *
                 *params: $id (for <input> name attribute and option array key e.g. $option = get_option('$option_name'), $option['$field_id']),
                 *$title (of field),
                 *$callback,
                 *$page (see add_options_page() 4th param and do, the page on which to display this setting),
                 *$section (See add_settings_section() 1st, which setting section does this field belong to),
                 *$args to be passed to the callback function
                 *
                 */
                

        }
        
        public function brhg2016_post_types_cb()
        {
                $post_types = get_post_types( '', 'objects' );

                if ( isset( $post_types ) ) {
                    foreach ( $post_types as $key => $value ) {
                        echo "<p>{$value->labels->name} {$value->name}</p>";
                    }
                }
        }

        public function brhg2016_taxomonies_cb()
        {
                $taxonomies = get_taxonomies( '', 'objects' );

                if ( isset( $taxonomies ) ) {
                    foreach ( $taxonomies as $key => $value ) {
                        echo "<p>{$value->labels->name} {$value->name}</p>";
                    }
                }
        }

        public function brhg2016_other_cb()
        {

            $help_text = get_post( 7534 );
           
            if ( empty($help_text) ) {
                return;
            }
            
            // Don't apply filter so that shortcodes remain as text
            //$help_text = apply_filters( 'the_content', $help_text->post_content );
            $help_text = wpautop( $help_text->post_content );
            echo $help_text;
        }
        
       
}

/* add_action('admin_menu', function(){
        //call static method, adds the page to the admin menu
        BRHG2016Help::add_brhg_help_menu_page();
}); */

add_action('admin_menu', function(){
        // this will run constructor
        new BRHG2016Help();
        }, 1);