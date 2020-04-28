<?php
/**
 * @package theatrehistory
 * 
 * @wordpress-plugin
 * Plugin Name: Theatre History
 * Description: A plugin to archive old theatrical productions, storing infomation about who was involved.
 * Version: 0.3
 * Requires at least: 5.4
 * Requires PHP: 7.4
 * Author: John
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * text domain: theatre-history
 * Domain Path: /lang
 */

 // If called directly, abort
if ( ! defined( 'ABSPATH' )) die;


//fetch relevant pages
//show custom type
require_once(dirname(__FILE__) . '/includes/theatre-history-show-type.php');
require_once(dirname(__FILE__) . '/includes/theatre-history-person-type.php');


/**
 * Add separator in admin menu
 * @since 0.2
 */
function add_admin_menu_separator() {
    global $menu;
    $position = 25;
    $menu[ $position ] = array(
    0 => '',
    1 => 'read',
    2 => 'separator' . $position,
    3 => '',
    4 => 'wp-menu-separator'
    );
}
add_action( 'admin_init', 'add_admin_menu_separator' );

/**
 * Main Plugin functions
 * @since 0.1
 */

//activate plugin
function theatre_history_activate(){
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'theatre_history_activate');

//deactivate plugin
function theatre_history_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'theatre_show' );
    unregister_post_type( 'theatre_member' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'theatre_history_deactivate' );