<?php

/**
 * Fired during plugin deactivation
 *
 * @link       johncherry.me
 * @since      1.0.0
 *
 * @package    Theatre_Manager
 * @subpackage Theatre_Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Theatre_Manager
 * @subpackage Theatre_Manager/includes
 * @author     John Cherry <wordpress@johncherry.me>
 */
class Theatre_Manager_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Unregister the post type, so the rules are no longer in memory.
		unregister_post_type( 'theatre_show' );
		unregister_post_type( 'theatre_member' );
		unregister_post_type( 'theatre_committee' );
		unregister_post_type( 'theatre_committee_role' );
		unregister_post_type( 'theatre_warning' );
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
	}

}
