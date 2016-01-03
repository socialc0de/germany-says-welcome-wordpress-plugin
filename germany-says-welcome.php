<?php
/*
Plugin Name: Germany Says Welcome Backend Plugin
Plugin URI: http://germany-says-welcome.de/
Description: Backend
Author: Germany Says Welcome
Author URI:http://germany-says-welcome.de/
Version: 0.1
License: AGPLv3
*/

// Make sure that no info is exposed if file is called directly -- Idea taken from Akismet plugin
if ( !function_exists( 'add_action' ) ) {
	echo "This page cannot be called directly.";
	exit;
}

// Define some useful constants that can be used by functions
if ( ! defined( 'WP_CONTENT_URL' ) ) {	
	if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
	define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
}
if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( basename(dirname(__FILE__)) == 'plugins' )
	define("GSW_DIR",'');
else define("GSW_DIR" , basename(dirname(__FILE__)) . '/');
define("GSW_PATH", WP_PLUGIN_URL . "/" . GSW_DIR);

/* Add new menu */
add_action('admin_menu', 'gsw_add_pages');
// http://codex.wordpress.org/Function_Reference/add_action



// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_html5()
{
    register_taxonomy_for_object_type('faq_cat', 'html5-blank');
    register_post_type('faq', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('FAQs', 'html5blank'), // Rename these to suit
            'singular_name' => __('FAQ', 'html5blank'),
            'add_new' => __('Add New', 'html5blank'),
            'add_new_item' => __('Add New', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('Edit Question', 'html5blank'),
            'new_item' => __('New Question', 'html5blank'),
            'view' => __('Display Question', 'html5blank'),
            'view_item' => __('View Question', 'html5blank'),
            'search_items' => __('Search Questions', 'html5blank'),
            'not_found' => __('No question found', 'html5blank'),
            'not_found_in_trash' => __('No question found in Trash', 'html5blank')
        ),
        'description'        => __( 'FAQs', 'html5blank' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'faq' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'show_in_rest'       => true,
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'supports' => array(
            'title',
            'custom-fields',
            'editor'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true // Allows export in Tools > Export
         // Add Category and Post Tags support
    ));
}




function create_faq_cat() {
    register_taxonomy(
        'faq_cat',
        'faq',
        array(
            'label' => __( 'FAQ Category' ),
            'rewrite' => array( 'slug' => 'faq_cat' ),
            'hierarchical' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'genre' ),
            'show_in_rest'       => true,
            'rest_base'          => 'faq_cat',
            'rest_controller_class' => 'WP_REST_Terms_Controller'
        )
    );
}

function create_steps() {
    register_taxonomy(
        'faq_step',
        'faq',
        array(
            'label' => __( 'FAQ Steps' ),
            'rewrite' => array( 'slug' => 'faq_step' ),
            'hierarchical' => true,
            'revisions'

        )
    );
}

function create_county() {
    register_taxonomy(
        'faq_county',
        'faq',
        array(
            'label' => __( 'FAQ Länderschlüssel' ),
            'rewrite' => array( 'slug' => 'faq_county' )
        )
    );
}

function change_default_title( $title ){
     $screen = get_current_screen();
 
     if  ( $screen->post_type == 'faq' ) {
          return 'Enter Question here';
     }
}
 

add_action('init', 'create_post_type_html5'); // Add our HTML5 Blank Custom Post Type
add_action( 'init', 'create_faq_cat' );
add_action( 'init', 'create_steps' );
add_action( 'init', 'create_county' );
 
add_filter( 'enter_title_here', 'change_default_title' );
add_filter( 'rest_api_allowed_post_types', 'allow_my_post_types');





















/*

******** BEGIN PLUGIN FUNCTIONS ********

*/


// function for: 
function gsw_add_pages() {

  add_menu_page('Germany Says Welcome Overview','Germany Says Welcome', 'read', 'gsw_overview', 'gsw_overview', GSW_PATH.'images/logo_small.png');
  // http://codex.wordpress.org/Function_Reference/add_menu_page

  add_submenu_page('gsw_overview', 'Overview for the GSW Plugin', 'Overview', 'read', 'gsw_overview', 'gsw_intro');
  // http://codex.wordpress.org/Function_Reference/add_submenu_page

}

function gsw_overview() {
?>
<div class="wrap"><h2>Germany Says Welcome Plugin Overview</h2>
<p>Nothing here yet</p>
</div>
<?php
exit;
}

?>