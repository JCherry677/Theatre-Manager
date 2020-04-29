<?php
/**
 * Custom Type - person
 * @since 0.2
 */

//create person type
function theatre_history_person_type(){
    $labels = array(
        'name'               => __( 'Members', 'post type general name' ),
        'singular_name'      => __( 'Member', 'post type singular name' ),
        'add_new'            => __( 'Add New', 'Member' ),
        'add_new_item'       => __( 'Add New Member' ),
        'edit_item'          => __( 'Edit Member' ),
        'new_item'           => __( 'New Member' ),
        'all_items'          => __( 'All Members' ),
        'view_item'          => __( 'View Member' ),
        'search_items'       => __( 'Search Members' ),
        'not_found'          => __( 'No members found' ),
        'not_found_in_trash' => __( 'No members found in the Trash' ), 
        'parent_item_colon'  => '’',
        'menu_name'          => 'Members',
    );

    $args = array(
        'labels' => $labels,
        'description'   => 'Contains information about members - past and present',
        'public'        => true,
        'menu_position' => 30,
        'supports'      => array( 'title', 'editor', 'revisions'),
        'rewrite'       => array('slug' => 'members'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_person', $args);
}

//------------------------------------------------------------------------------------------
/**
 * person update messages.
 * @since 0.2
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_history_person_messages( $messages ) {
    global $post, $post_ID;
    $messages['theatre_person'] = array(
      0 => '’', 
      1 => sprintf( __('Member updated. <a href="%s">View Member</a>'), esc_url( get_permalink($post_ID) ) ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Member updated.'),
      5 => isset($_GET['revision']) ? sprintf( __('Member restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Member published. <a href="%s">View member</a>'), esc_url( get_permalink($post_ID) ) ),
      7 => __('Member saved.'),
      8 => sprintf( __('Member submitted. <a target="_blank" href="%s">Preview member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Member scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview member</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
      10 => sprintf( __('Member draft updated. <a target="_blank" href="%s">Preview member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}

//------------------------------------------------------------------------------------------
/**
 * display contextual help for People
 * @since 0.3
 */
function theatre_history_person_contextual_help( $contextual_help, $screen_id, $screen ) { 
    if ( 'show' == $screen->id ) {
  
      $contextual_help = '<h2>Members</h2>
      <p>Members list all members that we know about! You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
      <p>You can view the details of each member by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';
  
    } elseif ( 'edit-show' == $screen->id ) {
  
      $contextual_help = '<h2>Editing Members</h2>
      <p>This page allows you to view/modify Member details. Please make sure to fill out the available boxes with the appropriate details (Title, Author, Cast) and <strong>not</strong> add these details to the show description.</p>';
  
    }
    return $contextual_help;
}

//------------------------------------------------------------------------------------------
/**
 * Create Meta boxes
 * 
 * meta - Person Info
 * @since 0.2
 */

function theatre_history_person_meta_boxes_setup(){

    //person info
    add_action('add_meta_boxes', 'theatre_history_person_info_meta');

    //save data
    add_action('save_post', 'theatre_history_person_info_save', 10, 2);
}

//info meta box controller
function theatre_history_person_info_meta(){
    add_meta_box(
        'theatre-history-person-info', //ID
        'Your Information', //Title TODO: Internationalisation
        'theatre_history_person_info_box', //callback function
        'theatre_person', //post type
        'normal', //on-page location
        'core' //priority
    );
}

//HTML representation of the box
function theatre_history_person_info_box($post){
    wp_nonce_field( basename( __FILE__ ), 'theatre_history_person_info_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/person-info-form.php';
}

//saving metadata 
function theatre_history_person_info_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_history_person_info_nonce'] ) || !wp_verify_nonce( $_POST['theatre_history_person_info_nonce'], basename( __FILE__ ) ) )
        return;
  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    //if (!current_user_can('edit_post', $post_id))
    //    return;

    $old = get_post_meta($post_id, 'th_person_info_data', true);
    $new = array();

    $courses = $_POST['course'];
    $grads = $_POST['grad'];

    $count = count( $courses );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $courses[$i] != '' ) :
            $new[$i]['course'] = stripslashes( strip_tags( $courses[$i] ) );

            if ( $grads[$i] == '' )
                $new[$i]['grad'] = '';
            else
                $new[$i]['grad'] = stripslashes( $grads[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'th_person_info_data', $new );
    elseif ( empty($new) && $old )
      delete_post_meta( $post_id, 'th_person_info_data', $old );
}

add_action('init', 'theatre_history_person_type');
add_filter( 'post_updated_messages', 'theatre_history_person_messages' );
add_action( 'contextual_help', 'theatre_history_person_contextual_help', 10, 3 );
add_action( 'load-post.php', 'theatre_history_person_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_history_person_meta_boxes_setup' );