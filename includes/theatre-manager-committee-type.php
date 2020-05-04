<?php
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
        'menu_position' => 40,
        'supports'      => array( 'title',),
        'rewrite'       => array('slug' => 'committee'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
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

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_committee_member_data', true);
    $new = array();
    $known = array();

    $members = $_POST['member'];
    $postitions = $_POST['postition'];

    $count = count( $members );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $postitions[$i] != '' ) {
            if ( $members[$i] != '' ){
                preg_match('#\((.*?)\)#', $members[$i], $match);
                if (array_key_exists($match[1], $new)){
                    array_push($new[$match[1]], $postitions[$i]);
                } else {
                    $new[$match[1]] = array( stripslashes( strip_tags( $postitions[$i] )));
                    array_push($known, $match[1]);
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
    foreach ($known as $person){
        //create new array to store new data in
        $member_new = array();
        $committee_roles = get_post_meta($person, 'th_committee_roles', true);
        $member_new[$post_id] = $new[$person];
        if (empty($committee_roles)){            
            $member_new[$post_id] = $new[$person];
        } else {
            //go through all current stored data
            foreach ($committee_roles as $committee => $role) {
                if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                    //update if same
                    $member_new[$committee] = $new[$person];
                } else {
                    //copy if not relevant
                    $member_new[$committee] = $role;
                }
            }
        }
        //save
        update_post_meta($person, 'th_committee_roles', $member_new);
    }
    
    //remove records that no longer appear in data
    foreach ($old as $key => $value){
        if (!(in_array($key, $known))){
            $member_new = array();
            $committee_roles = get_post_meta($key, 'th_committee_roles', true);
            foreach ($committee_roles as $committee => $role) {
                if ( (abs($committee-$post_id) < PHP_FLOAT_EPSILON)){
                    //remove by not adding it
                } else {
                    $member_new[$committee] = $role;
                }
            }
            update_post_meta($key, 'th_committee_roles', $member_new);
        }
    }
}
add_action( 'load-post.php', 'tm_committee_meta_boxes_setup' );
add_action( 'load-post-new.php', 'tm_committee_meta_boxes_setup' );

//-----------------------------------------------------------------------------------------
/**
 * Show Shortcode
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
            $committeestext = $committeestext . "<tr><td>" . $item . "</td></tr>";
        }
        $committeestext = $committeestext . "</tbody></table></td><td><a href=\"" . get_post_permalink($person)."\">" . tm_name_lookup($person, 'theatre_person') . "</td></tr>";
    }
    $committeestext = $committeestext . "</tbody></table>";
    //return all
    return $committeestext;
}
add_shortcode( 'committee_data', 'tm_committee_shortcode' );
