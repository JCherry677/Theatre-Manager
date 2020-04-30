<?php
/**
 * Custom Type - committee member
 * @since 0.4
 */

function theatre_manager_committee_type(){
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
        'supports'      => array( 'title', 'editor', 'revisions'),
        'rewrite'       => array('slug' => 'committee'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_committee', $args);
}

//------------------------------------------------------------------------------------------
/**
 * committee update messages.
 * @since 0.4
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_manager_committee_messages( $messages ) {
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

//------------------------------------------------------------------------------------------
/**
 * post columns
 * 
 * show date
 * @since 0.5
 */

function theatre_manager_editor_committee_columns($columns){
    unset( $columns['date'] );
    return $columns;
}

//------------------------------------------------------------------------------------------
/**
 * Change title text
 * @since 0.4
 */

function theatre_manager_committee_enter_title( $input ) {
    if ( 'theatre_committee' === get_post_type() ) {
        return __( 'Committee Season', 'your_textdomain' );
    }

    return $input;
}

//------------------------------------------------------------------------------------------
/**
 * Create Meta boxes
 * 
 * meta - committee Info
 * @since 0.4
 */
function theatre_manager_committee_meta_boxes_setup(){
    //boxes
    add_action('add_meta_boxes', 'theatre_manager_committee_member_meta');
    //saves
    add_action('save_post', 'theatre_manager_committee_member_save', 10, 2);
}

function theatre_manager_committee_member_meta(){
    add_meta_box(
        'theatre-manager-committee-member', //ID
        'Committee Members', //Title TODO: Internationalisation
        'theatre_manager_committee_member_box', //callback function
        'theatre_committee', //post type
        'normal', //on-page location
        'core' //priority
    );
}

function theatre_manager_committee_member_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'theatre_manager_committee_member_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/committee-member-form.php';
}

function theatre_manager_committee_member_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['theatre_manager_committee_member_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['theatre_manager_committee_member_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_committee_member_data', true);
    $new = array();

    $members = $_POST['member'];
    $postitions = $_POST['postition'];

    $count = count( $members );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $postitions[$i] != '' ) :
            $new[$i]['postition'] = stripslashes( strip_tags( $postitions[$i] ) );

            if ( $members[$i] == '' )
                $new[$i]['member'] = '';
            else
                $new[$i]['member'] = stripslashes( $members[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'th_committee_member_data', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'th_committee_member_data', $old );
}

//-----------------------------------------------------------------------------------------
/**
 * Show Shortcode
 * Returns show data
 * @since 0.5
 */
function theatre_manager_committee_shortcode() {
    $people = get_post_meta(get_the_ID(), 'th_committee_member_data');

    //basic data
    $data = "<h3>Members</h3><table><tbody>";
    $casttext = "";
    foreach ( $people as $field ) {
        foreach ($field as $item){
            $casttext = $casttext . "<tr><td>" . $item['postition'] . " : " . theatre_manager_show_person_lookup($item['member'])  . "</td></tr>";
        }
    }
    $data = $data . $casttext . "</tbody></table>";
    //return all
    return $data;
}

//-----------------------------------------------------------------------------------------
/** 
 * Add Actions/filters
 * @since 0.4
 */
add_action('init', 'theatre_manager_committee_type');
add_action( 'load-post.php', 'theatre_manager_committee_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_manager_committee_meta_boxes_setup' );

add_filter( 'post_updated_messages', 'theatre_manager_committee_messages' );
add_filter( 'enter_title_here', 'theatre_manager_committee_enter_title' );
add_filter( 'manage_theatre_committee_posts_columns', 'theatre_manager_editor_committee_columns' );

/**
 * Add Shortcodes
 * @since 0.5
 */
add_shortcode( 'committee_data', 'theatre_manager_committee_shortcode' );