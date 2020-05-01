<?php
/**
 * Custom Type - person
 * @since 0.2
 */

//create person type
function theatre_manager_person_type(){
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
add_action('init', 'theatre_manager_person_type');

//------------------------------------------------------------------------------------------
/**
 * person update messages.
 * @since 0.2
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_manager_person_messages( $messages ) {
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
add_filter( 'post_updated_messages', 'theatre_manager_person_messages' );

//------------------------------------------------------------------------------------------
/**
 * post columns
 * 
 * show date
 * @since 0.5
 */

function theatre_manager_editor_person_columns($columns){
    unset( $columns['date'] );
    $columns['grad_year'] = __( 'Graduation Year', 'theatre-manager');
    return $columns;
}
add_filter( 'manage_theatre_person_posts_columns', 'theatre_manager_editor_person_columns' );

//get data
function theatre_manager_person_columns( $column, $post_id ){
    switch ($column){
        case 'grad_year':
            $info = get_post_meta(get_the_ID(), 'th_person_info_data');
            $text = "";
            if ($info == ""){
                $text = "0";
            } else {
                foreach ( $info as $field ) {
                    foreach ($field as $item){
                        $text = $item['grad'];
                    }
                }
            }
            echo $text;
            break;

    }
}
add_action( 'manage_theatre_person_posts_custom_column' , 'theatre_manager_person_columns', 10, 2 );

//make sortable
function theatre_manager_person_columns_sortable( $columns ) {
    $columns['grad_year'] = 'grad_year';
    return $columns;
}
add_filter( 'manage_edit-theatre_person_sortable_columns', 'theatre_manager_person_columns_sortable' );

//potentially doesn't work
function theatre_manager_person_orderby( $query ) {
    if( ! is_admin() || ! $query->is_main_query() ) {
      return;
    }
  
    if ( 'grad_year' === $query->get( 'orderby') ) {
      $query->set( 'orderby', 'meta_value' );
      $query->set( 'meta_key', 'th_person_info_data' );
    }
}
add_action( 'pre_get_posts', 'theatre_manager_person_orderby' );

//------------------------------------------------------------------------------------------
/**
 * Create Meta boxes
 * 
 * meta - Person Info
 * @since 0.2
 */

function theatre_manager_person_meta_boxes_setup(){

    //person info
    add_action('add_meta_boxes', 'theatre_manager_person_info_meta');

    //save data
    add_action('save_post', 'theatre_manager_person_info_save', 10, 2);
}

//info meta box controller
function theatre_manager_person_info_meta(){
    add_meta_box(
        'theatre-manager-person-info', //ID
        'Your Information', //Title TODO: Internationalisation
        'theatre_manager_person_info_box', //callback function
        'theatre_person', //post type
        'normal', //on-page location
        'core' //priority
    );
}
add_action( 'load-post.php', 'theatre_manager_person_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_manager_person_meta_boxes_setup' );

//HTML representation of the box
function theatre_manager_person_info_box($post){
    wp_nonce_field( basename( __FILE__ ), 'theatre_manager_person_info_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/person-info-form.php';
}

//saving metadata 
function theatre_manager_person_info_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_manager_person_info_nonce'] ) || !wp_verify_nonce( $_POST['theatre_manager_person_info_nonce'], basename( __FILE__ ) ) )
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

//------------------------------------------------------------------------------------------
/**
 * Show Shortcode
 * Returns show data
 * @since 0.5
 */
function theatre_manager_person_shortcode() {
    $info = get_post_meta(get_the_ID(), 'th_person_info_data');

    //basic data
    $data = "<h3>York Uni Courses</h3>";
    $casttext = "";
    if ($info == ""){
        $casttext = "<p> No Known Courses </p>";
    } else {
        foreach ( $info as $field ) {
            foreach ($field as $item){
                $casttext = $casttext . "<p>" . $item['course'] . ", Graduating in " . $item['grad'] . "</p>";
            }
        }
    }
    $data = $data . $casttext;
    //return all
    return $data;
}
add_shortcode( 'person_data', 'theatre_manager_person_shortcode' );

/**
 * Utility Functions
 * theatre_manager_show_name_lookup - get name from id
 * @since 0.5
 */
function theatre_manager_show_name_lookup($name_id){
    $query = new WP_Query( 'post_type=theatre_show' );
    while ( $query->have_posts() ) {
        $query->the_post();
        $id = get_the_ID();
        if($id == $name_id){
            return get_the_title();
        }
    }
}