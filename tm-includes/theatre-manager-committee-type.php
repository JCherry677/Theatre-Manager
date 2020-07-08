<?php
// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;

//load file if committee enabled
$options = get_option( 'tm_settings' );
if (isset($options['tm_committees']) && $options['tm_committees'] == 1){

    /**
     * Custom Type - committee member
     * @since 0.4
     */
    function tm_committee_type(){
	    $options = get_option( 'tm_settings' );
	    $committee_block = false;
	    if (isset($options['tm_block_committee']) && $options['tm_block_committee'] == 1) {
		    $committee_block = true;
	    }
        $labels = array(
            'name'               => __( 'Past Committees', 'post type general name' ),
            'singular_name'      => __( 'Committee', 'post type singular name' ),
            'add_new'            => __( 'Add New', 'Committee' ),
            'add_new_item'       => __( 'Add New Committee' ),
            'edit_item'          => __( 'Edit Committee' ),
            'new_item'           => __( 'New Committee' ),
            'all_items'          => __( 'All committees' ),
            'view_item'          => __( 'View Committee' ),
            'search_items'       => __( 'Search committees' ),
            'not_found'          => __( 'No committees  found' ),
            'not_found_in_trash' => __( 'No committees  found in the Trash' ), 
            'parent_item_colon'  => '’',
            'menu_name'          => 'Committee',
        );

        $args = array(
            'labels' => $labels,
            'description'   => 'Contains information about our past committee shows',
            'public'        => true,
            'menu_position' => 40,
            'supports'      => array( 'title', 'editor',),
            'rewrite'       => array('slug' => 'committee'),
            'show_in_rest'  => $committee_block, //true => Block editor, false => old editor
            'has_archive'   => true,
            'menu_icon'     => 'dashicons-groups',
        );

        register_post_type('theatre_committee', $args);
    }
    add_action('init', 'tm_committee_type');

    //------------------------------------------------------------------------------------------
    /**
     * committee update messages.
     * @since 0.4
     * 
     * @param array $messages Existing post update messages.
     * @return array Amended post update messages with new CPT update messages.
     */
    function tm_committee_messages( $messages ) {
        global $post, $post_ID;
        $messages['theatre_committee'] = array(
        0 => '’', 
        1 => sprintf( __('Committee updated. <a href="%s">View committee</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Committee updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Committee restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Committee published. <a href="%s">View committee</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Committee saved.'),
        8 => sprintf( __('Committee submitted. <a target="_blank" href="%s">Preview committee</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Committee scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview committee</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Committee draft updated. <a target="_blank" href="%s">Preview committee</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        );
        return $messages;
    }
    add_filter( 'post_updated_messages', 'tm_committee_messages' );

    //------------------------------------------------------------------------------------------
    /**
     * post columns
     * 
     * show date
     * @since 0.5
     */
    function tm_editor_committee_columns($columns){
        unset( $columns['date'] );
        return $columns;
    }
    add_filter( 'manage_theatre_committee_posts_columns', 'tm_editor_committee_columns' );

    //------------------------------------------------------------------------------------------
    /**
     * Change title text
     * @since 0.4
     */
    function tm_committee_enter_title( $input ) {
        if ( 'theatre_committee' === get_post_type() ) {
            return __( 'Committee Season', 'your_textdomain' );
        }

        return $input;
    }
    add_filter( 'enter_title_here', 'tm_committee_enter_title' );

    //------------------------------------------------------------------------------------------
    /**
     * Create Meta boxes
     * 
     * meta - committee Info
     * @since 0.4
     */
    function tm_committee_meta_boxes_setup(){
        //boxes
        add_action('add_meta_boxes', 'tm_committee_member_meta');
        //saves
        add_action('save_post', 'tm_committee_member_save', 10, 2);
    }

    function tm_committee_member_meta(){
        add_meta_box(
            'theatre-manager-committee-member', //ID
            'Committee Members', //Title TODO: Internationalisation
            'tm_committee_member_box', //callback function
            'theatre_committee', //post type
            'normal', //on-page location
            'core' //priority
        );
    }

    function tm_committee_member_box($post, $args){
        wp_nonce_field( plugin_basename( __FILE__ ), 'tm_committee_member_nonce' );
        include plugin_dir_path( __FILE__ ) . 'forms/committee-member-form.php';
    }

    function tm_committee_member_save($post_id, $post){
        // Don't wanna save this now, right?
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;
        if ( !isset( $_POST['tm_committee_member_nonce'] ) )
            return;
        if ( !wp_verify_nonce( $_POST['tm_committee_member_nonce'], plugin_basename( __FILE__ ) ) )
            return;

        //save data to committee
        $old = get_post_meta($post_id, 'th_committee_member_data', true);
        $new = array();
        $known_mem = array();
        $position = array();

        $members = $_POST['member'];
        $positions = $_POST['position'];

        $count = count( $members );

        for ( $i = 0; $i < $count - 1; $i++ ) { //$count - 1 because the hidden box will always count as not empty
            if ( $positions[$i] != '' ) {
                if ( $members[$i] != '' ){
                    if (array_key_exists($members[$i], $new)){
                        array_push($new[$members[$i]], $positions[$i]);
                    } else {
                        $new[$members[$i]] = array( stripslashes( strip_tags( $positions[$i] )));

                    }
	                array_push($known_mem, $members[$i]);
	                if (array_key_exists($positions[$i], $position)){
		                array_push($position[$positions[$i]], $members[$i]);
	                } else {
		                $position[$positions[$i]] = array( stripslashes( strip_tags( $members[$i] )));
	                }
                }
            }
        }
        update_post_meta( $post_id, 'th_committee_member_data', $new );

        //save committee details in person metadata
        foreach ($new as $person => $role){
	        //get old data
	        $committee_roles = get_post_meta( $person, 'th_committee_roles', true );
	        //add new data
	        if ( empty( $committee_roles ) ) {
		        $committee_roles = array();
	        }
	        $committee_roles[ $post_id ] = $role;
	        //save
	        update_post_meta( $person, 'th_committee_roles', $committee_roles );
        }
	    //update the role's data
	    foreach ($position as $pos => $per){
	    	error_log($pos);
	    	error_log( print_r($per, true));
		    $old_roles = get_post_meta($pos, 'th_committee_role_data', true);
		    if (empty($old_roles)){
			    $old_roles = array();
		    }
		    $old_roles[$post_id] = $per;
		    update_post_meta($pos, 'th_committee_role_data', $old_roles);
	    }
        
        //remove records that no longer appear in data
        foreach ($old as $key => $value){
            if (!(in_array($key, $known_mem))){
                //update person's data
                $member_new = array();
                $committee_roles = get_post_meta($key, 'th_committee_roles', true);
                foreach ($committee_roles as $committee => $role) {
	                if ((int)$committee != (int)$post_id){
                        $member_new[$committee] = $role;
                    }
                }
                update_post_meta($key, 'th_committee_roles', $member_new);

                //update role's data
				/**
	            foreach ($value as $item) {
		            $role_new = array();
		            $old_roles = get_post_meta( $item, 'th_committee_role_data', true );
		            foreach ( $old_roles as $committee => $person ) {
		            	if ((int)$committee != (int)$post_id){
				            $role_new[ $committee ] = $person;
			            }
		            }
		            update_post_meta( $item, 'th_committee_role_data', $role_new );
	            }*/
            }
        }
    }
    add_action( 'load-post.php', 'tm_committee_meta_boxes_setup' );
    add_action( 'load-post-new.php', 'tm_committee_meta_boxes_setup' );

    //-----------------------------------------------------------------------------------------
    /**
     * Committee Shortcode
     * Returns show data
     * @since 0.5
     */
    function tm_committee_shortcode() {
        $people = get_post_meta(get_the_ID(), 'th_committee_member_data', true);

        //basic data
        $committeestext = "<h3>Members</h3>";
        $committeestext = $committeestext . "<table><thead><td><h6>Role</h6></td><td><h6>Member</h6></td></thead><tbody>";
        foreach ( $people as $person => $role ) {
            $committeestext .= "<tr><td><table><tbody>";
            foreach ($role as $item){
                $committeestext .= "<tr><td><a href=\"" . get_post_permalink($item)."\">" . get_the_title($item, 'theatre_role') . "</a></td></tr>";
            }
            $committeestext .= "</tbody></table></td><td><a href=\"" . get_post_permalink( $person ) . "\">" . get_the_title( $person ) . "</a></td></tr>";
        }
        $committeestext = $committeestext . "</tbody></table>";
        //return all
        return $committeestext;
    }
    add_shortcode( 'committee_data', 'tm_committee_shortcode' );

	/**
	 * This code is horrible, and should definitely not exist.
	 * If you have found it in the future, I'm sorry.
	 * If you want to tackle it and actually make a nice shortcode, please let me know how you get on!
	 * @param $atts
	 */
    function tm_committee_nice_shortcode($atts) {
        $vars = shortcode_atts( array(
            'committee_id' => '0'
        ), $atts);
        $people = get_post_meta($vars['committee_id'], 'th_committee_member_data', true);

        $count = 0;
        /* The ugly bit.
        This creates two tables, one of width 5 people one width 2 people, which is then shown depending on page size
        eww
        */
        $committeestext = "<style> .is-mobile {display: none;}@media (max-width: 480px) {.is-default {display: none;}.is-mobile {display: block;}}</style><div class='is-default'><table><tbody><tr>";
        foreach ( $people as $person => $role ) {
            if ($count >= 5){
                $committeestext .= "</tr><tr>";
                $count = 0;
            }
            $count += 1;
            $committeestext .= "<td><table><tbody>";
            $committeestext .= "<tr><td>" . get_the_post_thumbnail($person) . "</td></tr>";
            $committeestext .= "<tr><td><a href=\"" . get_post_permalink($person)."\">" . get_the_title($person) . "</a></td></tr>";
            foreach ($role as $item){
                $committeestext .= "<tr><td><a href=\"" . get_post_permalink($item)."\">" . get_the_title($item) . "</a></td></tr>";
            }
            $email = get_post_meta($person, 'tm_person_email', true);
            if ($email == ""){
                $committeestext .= "<tr><td>Email Unknown</td></tr>";
            } else {
                $committeestext .= "<tr><td><a href=\"mailto:" . $email . "\">" . $email . "</a></td></tr>";
            }
            $committeestext .= "</tbody></table></td>";
        }
        $committeestext .= "</tbody></table></div><div class='is-mobile'><table><tbody><tr>";

        //now make the table for mobile size
	    $count = 0;
	    foreach ( $people as $person => $role ) {
		    if ($count >= 2){
			    $committeestext .= "</tr><tr>";
			    $count = 0;
		    }
		    $count += 1;
		    $committeestext .= "<td><table><tbody>";
		    $committeestext .= "<tr><td>" . get_the_post_thumbnail($person) . "</td></tr>";
		    $committeestext .= "<tr><td><a href=\"" . get_post_permalink($person)."\">" . get_the_title($person) . "</a></td></tr>";
		    foreach ($role as $item){
			    $committeestext .= "<tr><td><a href=\"" . get_post_permalink($item)."\">" . get_the_title($item) . "</a></td></tr>";
		    }
		    $email = get_post_meta($person, 'tm_person_email', true);
		    if ($email == ""){
			    $committeestext .= "<tr><td>Email Unknown</td></tr>";
		    } else {
			    $committeestext .= "<tr><td><a href=\"mailto:" . $email . "\">" . $email . "</a></td></tr>";
		    }
		    $committeestext .= "</tbody></table></td>";
	    }
	    $committeestext .= "</tbody></table></div>";
        //return all
        return $committeestext;
    }
    add_shortcode( 'committee_layout', 'tm_committee_nice_shortcode' );
}
