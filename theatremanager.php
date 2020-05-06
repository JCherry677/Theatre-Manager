<?php
/**
 * @package theatrehistory
 * 
 * @wordpress-plugin
 * Plugin Name: Theatre Manager
 * Description: A plugin to manage theatrical productions, storing infomation about who is involved. Can also be used as an archive
 * Version: 0.7
 * Requires at least: 5.4
 * Requires PHP: 7.4
 * Author: John
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * text domain: theatre-manager
 */

// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;


//fetch relevant pages
require_once(dirname(__FILE__) . '/includes/theatre-manager-person-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-committee-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-committee-role-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-show-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-warning-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-options.php');

//------------------------------------------------------------------------------------------
/**
 * Main Plugin functions
 * @since 0.1
 */

//activate plugin
function tm_activate(){
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'tm_activate');

//deactivate plugin
function tm_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'theatre_show' );
    unregister_post_type( 'theatre_member' );
    unregister_post_type( 'theatre_committee' );
    unregister_post_type( 'theatre_committee_role' );
    unregister_post_type( 'theatre_warning' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'tm_deactivate' );

//------------------------------------------------------------------------------------------
/**
 * Add shortcodes to shows, people and committees 
 * Not the best way to do this but guarantees content with every theme
 * @since 0.7
 */
function tm_shortcodes_on_posts( $content ) {
    global $post;
    if( ! $post instanceof WP_Post ) return $content;
  
    switch( $post->post_type ) {
        case 'theatre_show':
            return $content . '[show_data]';
        case 'theatre_person':
            return $content . '[person_data]';
        case 'theatre_committee':
            return $content . '[committee_data]';
        case 'theatre_role':
            return $content . '[role_data]';
        default:
              return $content;
    }
}
add_filter( 'the_content', 'tm_shortcodes_on_posts' );


//------------------------------------------------------------------------------------------
/**
 * Utility Functions
 */
/** 
 * tm_id_lookup - get id from name
 * @since 0.6
 */
function tm_id_lookup($name, $type){
    $query = new WP_Query( 'post_type=' . $type );
    while ( $query->have_posts() ) {
        $query->the_post();
        $title = get_the_title();
        if($title == $name_id){
            return get_the_ID();
        }
    }
    return false;
}

/**
 * Utility Functions for autocomplete text
 * @since 0.7
 */
add_action('wp_enqueue_scripts', 'se_wp_enqueue_scripts');
function se_wp_enqueue_scripts() {
    wp_enqueue_script('suggest');
}

add_action('wp_ajax_th_person_lookup', 'th_person_lookup');
add_action('wp_ajax_nopriv_th_person_lookup', 'th_person_lookup');

function th_person_lookup() {
    global $wpdb;

    $search = $wpdb->esc_like($_REQUEST['q']);

    $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
        WHERE post_title LIKE \'' . $search . '%\'
        AND post_type = \'theatre_person\'
        AND post_status = \'publish\'
        ORDER BY post_title ASC';
    $rows = $wpdb->get_results($query);
    foreach ($rows as $row) {
        $post_title = $row->post_title;
        $id = $row->ID;
        $text = $post_title . " (" . $id . ")\n";
        echo $text;
    }
    wp_die();
}

//autocomplete role
add_action('wp_ajax_th_role_lookup', 'th_role_lookup');
add_action('wp_ajax_nopriv_th_role_lookup', 'th_role_lookup');

function th_role_lookup() {
    global $wpdb;

    $search = $wpdb->esc_like($_REQUEST['q']);

    $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
        WHERE post_title LIKE \'' . $search . '%\'
        AND post_type = \'theatre_role\'
        AND post_status = \'publish\'
        ORDER BY post_title ASC';
    $rows = $wpdb->get_results($query);
    foreach ($rows as $row) {
        $post_title = $row->post_title;
        $id = $row->ID;
        $text = $post_title . " (" . $id . ")\n";
        echo $text;
    }
    wp_die();
}