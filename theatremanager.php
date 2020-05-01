<?php
/**
 * @package theatrehistory
 * 
 * @wordpress-plugin
 * Plugin Name: Theatre Manager
 * Description: A plugin to manage theatrical productions, storing infomation about who is involved. Can also be used as an archive
 * Version: 0.6
 * Requires at least: 5.4
 * Requires PHP: 7.4
 * Author: John
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * text domain: theatre-manager
 * Domain Path: /lang
 */

 // If called directly, abort
if ( ! defined( 'ABSPATH' )) die;


//fetch relevant pages
//show custom type
require_once(dirname(__FILE__) . '/includes/theatre-manager-show-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-person-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-manager-committee-type.php');


/**
 * Main Plugin functions
 * @since 0.1
 */

//activate plugin
function theatre_manager_activate(){
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'theatre_manager_activate');

//deactivate plugin
function theatre_manager_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'theatre_show' );
    unregister_post_type( 'theatre_member' );
    unregister_post_type( 'theatre_committee' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'theatre_manager_deactivate' );

/**
 * Utility Functions
 * theatre_manager_name_lookup - get name from id
 * @since 0.6
 */
function theatre_manager_name_lookup($name_id, $type){
    $query = new WP_Query( 'post_type=' . $type );
    while ( $query->have_posts() ) {
        $query->the_post();
        $id = get_the_ID();
        if($id == $name_id){
            return get_the_title();
        }
    }
}