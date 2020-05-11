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
            'menu_position' => "58",
            'supports'      => array( 'title',),
            'rewrite'       => array('slug' => 'committee'),
            'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
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
        $known_pos = array();

        $members = $_POST['member'];
        $positions = $_POST['postition'];

        $count = count( $members );

        for ( $i = 0; $i < $count; $i++ ) {
            if ( $positions[$i] != '' ) {
                preg_match('#\((.*?)\)#', $positions[$i], $position_id);
                if ( $members[$i] != '' ){
                    preg_match('#\((.*?)\)#', $members[$i], $member_id);
                    if (array_key_exists($member_id[1], $new)){
                        array_push($new[$member_id[1]], $position_id[1]);
                    } else {
                        $new[$member_id[1]] = array( stripslashes( strip_tags( $position_id[1] )));
                        array_push($known_mem, $member_id[1]);
                        array_push($known_pos, $position_id[1]);
                    }
                }
            }
        }
        if ( !empty( $new ) && $new != $old ) {
            update_post_meta( $post_id, 'th_committee_member_data', $new );
        } elseif ( empty($new) && $old ) {
            delete_post_meta( $post_id, 'th_committee_member_data', $old );
        }

        //save committee details in person metadata
        foreach ($known_mem as $person){
            $member_position = $new[$person];
            //create new array to store new data in
            $member_new = array();
            //get old data
            $committee_roles = get_person_data($person, "committee", true);
            //add new data
            $member_new[$post_id] = $member_position;
            if (empty($committee_roles)){            
                $member_new[$post_id] = $member_position;
            } else {
                //go through all current stored data
                foreach ($committee_roles as $committee => $role) {
                    if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                        //update if same
                        $member_new[$committee] = $member_position;
                    } else {
                        //copy if not relevant
                        $member_new[$committee] = $role;
                    }
                }
            }
            //save
            set_person_data($person, "committee", $member_new, true);

            //now check the role's data and update it
            $role_new = array();
            $old_roles = get_post_meta($member_position, 'th_committee_role_data', true);
            //add new data
            $role_new[$post_id] = $person;
            if (empty($old_roles)){            
                $role_new[$post_id] = $person;
            } else {
                //go through all current stored data
                foreach ($old_roles as $committee => $person) {
                    if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                        //update if same
                        $role_new[$committee] = $person;
                    } else {
                        //copy if not relevant
                        $role_new[$committee] = $person;
                    }
                }
            }
            //save
            update_post_meta($member_position[0], 'th_committee_role_data', $role_new);
        }
        
        //remove records that no longer appear in data
        foreach ($old as $key => $value){
            if (!(in_array($key, $known_mem))){
                //update person's data
                $member_new = array();
                $committee_roles = get_person_data($key, "committee", true);
                foreach ($committee_roles as $committee => $role) {
                    if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                        //remove by not adding it
                    } else {
                        $member_new[$committee] = $role;
                    }
                }
                set_person_data($key, "committee", $member_new, true);

                //update role's data
                $role_new = array();
                $old_roles = get_post_meta($value, 'th_committee_role_data', true);
                foreach ($old_roles as $committee => $person) {
                    if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                        //remove by not adding it
                    } else {
                        $member_new[$committee] = $person;
                    }
                }
                update_post_meta($value, 'th_committee_role_data', $role_new);
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
            $committeestext = $committeestext . "<tr><td><table><tbody>";
            foreach ($role as $item){
                $committeestext = $committeestext . "<tr><td><a href=\"" . get_post_permalink($item)."\">" . get_the_title($item, 'theatre_role') . "</a></td></tr>";
            }
            $committeestext = $committeestext . "</tbody></table></td><td><!--<a href=\"" . get_post_permalink($person)."\">-->" . get_person_name($person) . "</a></td></tr>";
        }
        $committeestext = $committeestext . "</tbody></table>";
        //return all
        return $committeestext;
    }
    add_shortcode( 'committee_data', 'tm_committee_shortcode' );

    function tm_committee_nice_shortcode($atts) {
        $vars = shortcode_atts( array(
            'committee_id' => '0'
        ), $atts);
        $people = get_post_meta($vars['committee_id'], 'th_committee_member_data', true);

        $count = 0;
        $committeestext = $committeestext . "<table><tbody><tr>";
        foreach ( $people as $person => $role ) {
            if ($count >= 5){
                $committeestext = $committeestext . "</tr><tr>";
                $count = 0;
            }
            $count += 1;
            $committeestext = $committeestext . "<td><table><tbody>";
            $committeestext = $committeestext . "<tr><td>" . get_the_post_thumbnail($person) . "</td></tr>";
            $committeestext = $committeestext . "<tr><td><!--<a href=\"" . get_post_permalink($person)."\">-->" . get_person_name($person) . "</a></td></tr>";
            foreach ($role as $item){
                $committeestext = $committeestext . "<tr><td><a href=\"" . get_post_permalink($item)."\">" . get_the_title($item) . "</a></td></tr>";
            }
            $email = get_post_meta($person, 'tm_person_email', true);
            if ($email == ""){
                $committeestext = $committeestext . "<tr><td>Email Unknown</td></tr>";
            } else {
                $committeestext = $committeestext . "<tr><td><a href=\"mailto:" . $email . "\">" . $email . "</a></td></tr>";
            }
            $committeestext = $committeestext . "</tbody></table></td>";
        }
        $committeestext = $committeestext . "</tbody></table>";
        //return all
        return $committeestext;
    }
    add_shortcode( 'committee_layout', 'tm_committee_nice_shortcode' );
}
