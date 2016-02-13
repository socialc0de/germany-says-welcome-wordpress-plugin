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



/**********************************************************************************************************************************************
 *	Plugin stuff
 **********************************************************************************************************************************************/

/** Add plugin menu ***/
function gsw_manage_menu_pages() {
    if(current_user_can( 'manage_options' )) {
        add_menu_page('Germany Says Welcome Overview','Germany Says Welcome', 'read', 'gsw_overview', 'gsw_overview', GSW_PATH.'images/logo_small.png');
        add_submenu_page('gsw_overview', 'Overview for the GSW Plugin', 'Overview', 'read', 'gsw_overview', 'gsw_intro');
    } else {
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('upload.php'); // Media
        remove_menu_page('link-manager.php'); // Links
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('edit.php?post_type=page'); // Pages
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('users.php'); // Users
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
        remove_submenu_page( 'index.php', 'update-core.php' );
    }
}
add_action('admin_menu', 'gsw_manage_menu_pages');

/** Plugin Main Page **/
function gsw_overview() {
    echo <<<HTML
<div class="wrap"><h2>Germany Says Welcome Plugin Overview</h2>
	<p>Nothing here yet</p>
</div>
HTML;
    exit;
}



/**********************************************************************************************************************************************
 *	FAQ post type and taxonomy
 **********************************************************************************************************************************************/

/**
 * create custom post type "faq"
 */
function create_post_type_faq() {
    // register taxonomy
    register_taxonomy_for_object_type('faq_cat', 'html5-blank');
    // register post type
    register_post_type('faq', array(
        'labels' => array(
            'name' => __('FAQs', 'html5blank'),
            'singular_name' => __('FAQ', 'html5blank'),
            'add_new' => __('FAQ Eintrag erstellen', 'html5blank'),
            'add_new_item' => __('Neuer FAQ Eintrag', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('FAQ Eintrag bearbeiten', 'html5blank'),
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
        ),
        'can_export' => true
    ));
}

/**
 * create "faq category" taxonomy
 */
function create_faq_cat() {
    register_taxonomy('faq_cat', 'faq', array(
        'label' => __( 'FAQ Kategorien' ),
        'rewrite' => array( 'slug' => 'faq_cat' ),
        'hierarchical' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'faq_cat' ),
        'show_in_rest'       => true,
        'rest_base'          => 'faq_cat',
        'rest_controller_class' => 'WP_REST_Terms_Controller'
    ));
}

/**
 * create "faq steps" taxonomy
 */
function create_steps() {
    register_taxonomy('faq_step', 'faq', array(
        'label' => __( 'FAQ Steps' ),
        'rewrite' => array( 'slug' => 'faq_step' ),
        'hierarchical' => true,
        'query_var'         => true,
        'show_in_rest'       => true,
        'rest_base'          => 'faq_cat',
        'rest_controller_class' => 'WP_REST_Terms_Controller'
    ));
}
function gsw_labels() {
    global $wp_taxonomies;
    //echo '<pre>';print_r($wp_taxonomies);echo '</pre>';die();
}

/**
 * create "faq country" taxonomy
 */
function create_county() {
    register_taxonomy('faq_county', 'faq', array(
        'label' => __( 'FAQ Länderschlüssel' ),
        'rewrite' => array( 'slug' => 'faq_county' ),
        'show_in_rest'       => true,
        'rest_base'          => 'faq_cat',
        'rest_controller_class' => 'WP_REST_Terms_Controller'
    ));
}



/**********************************************************************************************************************************************
 *	Emergency post type and taxonomy
 **********************************************************************************************************************************************/

// Create "emergeny" custom post type
function create_post_type_emergency() {
    // register taxonomy
    register_taxonomy_for_object_type('emergency_county', 'html5-blank');
    // register post type
    register_post_type('emergency', array(
        'labels' => array(
            'name' => __('Notruf-Nummern', 'html5blank'),
            'singular_name' => __('Notruf-Nummer', 'html5blank'),
            'add_new' => __('Notruf-Nummer hinzufügen', 'html5blank'),
            'add_new_item' => __('Notruf-Nummer hinzufügen', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('Notruf-Nummer bearbeiten', 'html5blank'),
            'new_item' => __('New Number', 'html5blank'),
            'view' => __('Display Numbers', 'html5blank'),
            'view_item' => __('View Number', 'html5blank'),
            'search_items' => __('Search emergency numbers', 'html5blank'),
            'not_found' => __('No question found', 'html5blank'),
            'not_found_in_trash' => __('No emergency number found in Trash', 'html5blank')
        ),
        'description'        => __( 'Emergency Numbers', 'html5blank' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'emergency' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'show_in_rest'       => true,
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'register_meta_box_cb' => 'add_number_metabox',
        'supports' => array(
            'title',
            'custom-fields',
            'editor'
        ),
        'can_export' => true
    ));
}

// add "phonenumber" metabox
function emergency_number_meta() {
    global $post;
    echo '<input type="hidden" name="emergencymeta_nonce" id="emergencymeta_nonce" value="' .
        wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    $number = get_post_meta($post->ID, '_number', true);
    echo '<input type="text" name="_number" value="' . $number  . '" class="widefat" />';
}
function add_number_metabox() {
    add_meta_box('emergency_numbers', 'Telefonnummer', 'emergency_number_meta', 'emergency', 'side', 'high');
}
// Save the Metabox Data
function save_emergency_meta($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['emergencymeta_nonce'], plugin_basename(__FILE__) )) {
        return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $emergency_meta['_number'] = $_POST['_number'];
    // Add values of $events_meta as custom fields
    foreach ($emergency_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}

// Display Emergency Numbers in Post listing in Backend
function display_emergency_numbers_head( $columns ) {
    $screen = get_current_screen();
    if  ( $screen->post_type == 'emergency' ) {
        $columns['emergency_number']  = 'Number';
    }
    return $columns;
}
function display_emergency_numbers_body( $column_name, $post_id ) {
    if( $column_name == 'emergency_number' ) {
        $number = get_post_meta( $post_id, '_number', true );
        echo $number;
    }
}

/**
 * register "emergency country" taxonomiy
 */
function create_emergency_county() {
    register_taxonomy('emergency_county', 'emergency', array(
        'label' => __( 'Notruf Länderschlüssel' ),
        'rewrite' => array( 'slug' => 'emergency_county' ),
        'show_in_rest'       => true,
        'rest_base'          => 'faq_cat',
        'rest_controller_class' => 'WP_REST_Terms_Controller'
    ));
}



/**********************************************************************************************************************************************
 *	misc
 **********************************************************************************************************************************************/
function change_default_title( $title ){
    $screen = get_current_screen();
    if  ( $screen->post_type == 'faq' ) {
        return 'Frage eingeben ...';
    }
    if  ( $screen->post_type == 'emergency' ) {
        return 'Name eingeben ...';
    }
}

/**
 * Hook default_content
 * Set default value for content field cause wpml doesnt copy empty fields
 * Needed for custom content types "faq"
 */
add_filter( 'default_content', 'gsw_set_default_content', 10, 2 );
function gsw_set_default_content( $content, $post ) {
    if( $post->post_type == 'faq' ) {
        $content = "Antwort eingeben ...";
    }
    return $content;
}


/**********************************************************************************************************************************************
 *	Create new Endpoints to fetch "faq" and "emergency" data as JSON
 **********************************************************************************************************************************************/

/**
 * add additional query vars
 * @param $vars
 * @return array
 */
function gsw_add_query_vars($vars){
    $vars[] = '__api';
    $vars[] = 'type';
    return $vars;
}
add_filter('query_vars', 'gsw_add_query_vars', 0);

/**
 * sniff request if api is called. get data and send json output
 */
function gsw_sniff_requests(){
    global $wp;
    if(isset($wp->query_vars['__api']) && isset($wp->query_vars['type'])){
        switch($wp->query_vars['type']) {
            case 'faq':
                $posts = gsw_get_faq();
                break;
            case 'faq_categories':
                $posts = gsw_get_faq_categories();
                break;
            case 'emergency':
                $posts = gsw_get_emergency();
                break;
        }
        // send json
        wp_send_json($posts);
        exit;
    }
}

/**
 * get faq data
 * @return array
 */
function gsw_get_faq() {
    global $post, $sitepress;

    $posts  = array();
    $args   = array(
        'post_type'         => 'faq',
        'post_status'       => 'publish',
        'suppress_filters'  => 0,
        'posts_per_page'    => 9999
    );
    foreach ( get_posts($args) as $post) : setup_postdata($post);
        // get parent id
        $parent_id      = icl_object_id( $post->ID, 'page', true, $sitepress->get_default_language() );
        // get catgeries
        $terms          = get_the_terms($post->ID, "faq_cat");
        $categories     = array();
        foreach($terms as $term) {
            $categories[] = array(
                'id'            => $term->term_id,
                'original_id'   => icl_object_id( $term->term_id, 'faq_cat', true, $sitepress->get_default_language() )
            );
        }
        // get steps
        $terms          = get_the_terms($post->ID, "faq_step");
        $steps           = array();
        foreach($terms as $term) {
            $steps[] = $term->slug;
        }
        // get countrycodes
        $terms          = get_the_terms($post->ID, "faq_county");
        $codes           = array();
        foreach($terms as $term) {
            $codes[] = $term->name;
        }
        $posts[] = array(
            'id'            => $post->ID,
            'original_id'   => $parent_id ? $parent_id : $post->ID,
            'title'         => array('rendered' => $post->post_title),
            'content'       => array('rendered' => $post->post_content),
            'categories'    => $categories,
            'steps'         => $steps,
            'countries'     => $codes
        );
    endforeach;

    return $posts;
}

/**
 * get faq categories
 * @return array
 */
function gsw_get_faq_categories() {
    global $sitepress;

    $terms = get_terms( 'faq_cat', array(
        'orderby'    => 'count',
        'hide_empty' => 0,
    ) );
    $categories = array();
    foreach($terms as $term) {
        $categories[] = array(
            'id'            => $term->term_id,
            'original_id'   => icl_object_id( $term->term_id, 'faq_cat', true, $sitepress->get_default_language() ),
            'title'         => array('rendered' => $term->name),
            'image'         => z_taxonomy_image_url(icl_object_id( $term->term_id, 'faq_cat', true, $sitepress->get_default_language() ))
        );
    }

    return $categories;
}

/**
 * get emergency data
 * @return array
 */
function gsw_get_emergency() {
    global $post, $sitepress;

    $posts  = array();
    $args   = array(
        'post_type'         => 'emergency',
        'post_status'       => 'publish',
        'suppress_filters'  => 0,
        'posts_per_page'    => 9999
    );
    foreach ( get_posts($args) as $post) : setup_postdata($post);
        // get parent id
        $parent_id      = icl_object_id( $post->ID, 'page', true, $sitepress->get_default_language() );
        // get countrycodes
        $terms          = get_the_terms($post->ID, "emergency_county");
        $codes           = array();
        foreach($terms as $term) {
            $codes[] = $term->name;
        }
        // custom fields
        $custom_fields = get_post_custom($parent_id);
        $posts[] = array(
            'id'            => $post->ID,
            'original_id'   => $parent_id,
            'title'         => array('rendered' => $post->post_title),
            'content'       => array('rendered' => $post->post_content),
            'number'        => $custom_fields['_number'][0],
            'countries'     => $codes
        );
    endforeach;

    return $posts;
}



/**********************************************************************************************************************************************
 *	Startup
 **********************************************************************************************************************************************/

add_action( 'init', 'create_post_type_faq'); //Create Post Type
add_action( 'init', 'create_faq_cat' ); //Create Post Categories
add_action( 'init', 'create_steps' ); //Create Asylum Steps
add_action( 'init', 'create_county' ); //Create City-Code-Field

add_action( 'init', 'create_post_type_emergency'); //load custom fields
add_action( 'save_post', 'save_emergency_meta', 1, 2); // save the custom fields
add_action( 'init', 'create_emergency_county' ); //Create City-Code-Field

add_filter( 'enter_title_here', 'change_default_title' ); //change title-field name of the editor in FAQs

// For registering the column
add_filter( 'manage_posts_columns', 'display_emergency_numbers_head' );

// For rendering the column
add_action( 'manage_posts_custom_column', 'display_emergency_numbers_body', 10, 2 );

add_action('parse_request', 'gsw_sniff_requests', 0);

add_action( 'init', 'gsw_labels');