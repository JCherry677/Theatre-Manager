<?php
/**
 * Custom Type - Show
 * @since 0.1
 */

//create show type
function theatre_history_show_type(){
    $labels = array(
        'name'               => __( 'Shows', 'post type general name' ),
        'singular_name'      => __( 'Show', 'post type singular name' ),
        'add_new'            => __( 'Add New', 'show' ),
        'add_new_item'       => __( 'Add New Show' ),
        'edit_item'          => __( 'Edit Show' ),
        'new_item'           => __( 'New Show' ),
        'all_items'          => __( 'All Shows' ),
        'view_item'          => __( 'View Show' ),
        'search_items'       => __( 'Search Shows' ),
        'not_found'          => __( 'No shows found' ),
        'not_found_in_trash' => __( 'No shows found in the Trash' ), 
        'parent_item_colon'  => '’',
        'menu_name'          => 'Shows',
    );

    $args = array(
        'labels' => $labels,
        'description'   => 'Contains information about past shows',
        'public'        => true,
        'menu_position' => 30,
        'supports'      => array( 'title', 'editor', 'comments', 'revisions' ),
        'rewrite'       => array('slug' => 'shows'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_show', $args);
}

//------------------------------------------------------------------------------------------
/**
 * Show update messages.
 * @since 0.1
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_history_show_messages( $messages ) {
    global $post, $post_ID;
    $messages['theatre_show'] = array(
      0 => '’', 
      1 => sprintf( __('Show updated. <a href="%s">View Show</a>'), esc_url( get_permalink($post_ID) ) ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Show updated.'),
      5 => isset($_GET['revision']) ? sprintf( __('Show restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Show published. <a href="%s">View show</a>'), esc_url( get_permalink($post_ID) ) ),
      7 => __('Show saved.'),
      8 => sprintf( __('Show submitted. <a target="_blank" href="%s">Preview show</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Show scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview show</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
      10 => sprintf( __('Show draft updated. <a target="_blank" href="%s">Preview show</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}

//------------------------------------------------------------------------------------------
/**
 * display contextual help for Shows
 * @since 0.1
 */
function theatre_history_contextual_help( $contextual_help, $screen_id, $screen ) { 
    if ( 'show' == $screen->id ) {
  
      $contextual_help = '<h2>Shows</h2>
      <p>Shows list all previous shows that we know about! You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
      <p>You can view/edit the details of each show by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';
  
    } elseif ( 'edit-show' == $screen->id ) {
  
      $contextual_help = '<h2>Editing Shows</h2>
      <p>This page allows you to view/modify show details. Please make sure to fill out the available boxes with the appropriate details (Title, Author, Cast) and <strong>not</strong> add these details to the show description.</p>';
  
    }
    return $contextual_help;
}

//------------------------------------------------------------------------------------------
/**
 * Add custom taxonomies
 * 
 * taxonomy - show season
 * @since 0.1
 */
function create_show_taxonomies(){
    //season taxonomy - hierarchical
    $labels = array(
        'name'              => _x( 'Season', 'taxonomy general name' ),
        'singular_name'     => _x( 'Season', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Seasons' ),
        'all_items'         => __( 'All Seasons' ),
        'parent_item'       => __( 'Parent Season' ),
        'parent_item_colon' => __( 'Parent Season:' ),
        'edit_item'         => __( 'Edit Season' ),
        'update_item'       => __( 'Update Season' ),
        'add_new_item'      => __( 'Add New Season' ),
        'new_item_name'     => __( 'New Season Name' ),
        'menu_name'         => __( 'Seasons' ),
    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'season' ),
    );
    register_taxonomy( 'season', array( 'theatre_show', 'theatre_committee' ), $args );

    //Venue taxonomy - hierarchical
    $labels = array(
        'name'              => _x( 'Venue', 'taxonomy general name' ),
        'singular_name'     => _x( 'Venue', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Venues' ),
        'all_items'         => __( 'All Venues' ),
        'edit_item'         => __( 'Edit Venue' ),
        'update_item'       => __( 'Update Venue' ),
        'add_new_item'      => __( 'Add New Venue' ),
        'new_item_name'     => __( 'New Venue Name' ),
        'menu_name'         => __( 'Venues' ),
    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'venue' ),
    );
    register_taxonomy( 'venue', array( 'theatre_show' ), $args );
}

//------------------------------------------------------------------------------------------
/**
 * Create Meta boxes
 * 
 * meta - Show Info
 * @since 0.1
 * meta - Show person(cast)
 * @since 0.2
 * meta - Show crew
 * @since 0.2
 * meta - Show Reviews
 * @since 0.3
 */

function theatre_history_show_meta_boxes_setup(){

    //show info
    add_action('add_meta_boxes', 'theatre_history_show_info_meta');
    add_action('add_meta_boxes', 'theatre_history_show_person_meta');
    add_action('add_meta_boxes', 'theatre_history_show_crew_meta');
    add_action('add_meta_boxes', 'theatre_history_show_review_meta');

    //save data
    add_action('save_post', 'theatre_history_show_info_save', 10, 2);
    add_action('save_post', 'theatre_history_show_person_save', 10, 2);
    add_action('save_post', 'theatre_history_show_crew_save', 10, 2);
    add_action('save_post', 'theatre_history_show_review_save', 10, 2);
}

//info meta box controller
function theatre_history_show_info_meta(){
    add_meta_box(
        'theatre-history-show-info', //ID
        'Show Information', //Title TODO: Internationalisation
        'theatre_history_show_info_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

//HTML representation of the box
function theatre_history_show_info_box($post){
    $value = get_post_meta($post->ID, 'theatre_history_show_info', true );
    wp_nonce_field( basename( __FILE__ ), 'theatre_history_show_info_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-info-form.php';
}

//saving metadata 
function theatre_history_show_info_save( $post_id, $post ) {
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_history_show_info_nonce'] ) || !wp_verify_nonce( $_POST['theatre_history_show_info_nonce'], basename( __FILE__ ) ) )
        return $post_id;
    
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
  
    $fields = [
        'th_show_info_author',
        'th_show_info_start_date',
        'th_show_info_end_date',
    ];

    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
        else {
            add_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ), true);
        }
    }
}

//------------------------------------------------------------------------------------------
/** 
 * person meta box controller
 * @since 0.2
 */
function theatre_history_show_person_meta(){
    add_meta_box(
        'theatre-history-show-person', //ID
        'Cast', //Title TODO: Internationalisation
        'theatre_history_show_person_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

function theatre_history_show_person_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'theatre_history_show_person_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-person-form.php';
}

function theatre_history_show_person_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['theatre_history_show_person_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['theatre_history_show_person_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_person_info_data', true);
    $new = array();

    $actors = $_POST['actor'];
    $roles = $_POST['role'];

    $count = count( $actors );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $roles[$i] != '' ) :
            $new[$i]['role'] = stripslashes( strip_tags( $roles[$i] ) );

            if ( $actors[$i] == '' )
                $new[$i]['actor'] = '';
            else
                $new[$i]['actor'] = stripslashes( $actors[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'th_show_person_info_data', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'th_show_person_info_data', $old );
}

//------------------------------------------------------------------------------------------
/**
 * crew meta box controller
 * @since 0.2
 */
function theatre_history_show_crew_meta(){
    add_meta_box(
        'theatre-history-show-crew', //ID
        'Production Team', //Title TODO: Internationalisation
        'theatre_history_show_crew_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

function theatre_history_show_crew_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'theatre_history_show_crew_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-crew-form.php';
}

function theatre_history_show_crew_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['theatre_history_show_crew_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['theatre_history_show_crew_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_crew_info_data', true);
    $new = array();

    $persons = $_POST['pos'];
    $jobs = $_POST['job'];

    $count = count( $persons );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $persons[$i] != '' ) :
            $new[$i]['pos'] = stripslashes( strip_tags( $persons[$i] ) );

            if ( $jobs[$i] == '' )
                $new[$i]['job'] = '';
            else
                $new[$i]['job'] = stripslashes( $jobs[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'th_show_crew_info_data', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'th_show_crew_info_data', $old );
}

//------------------------------------------------------------------------------------------
/**
 * Reviews meta box 
 * @since 0.3
 */
function theatre_history_show_review_meta(){
    add_meta_box(
        'theatre-history-show-review', //ID
        'Reviews', //Title TODO: Internationalisation
        'theatre_history_show_review_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

//HTML representation of the box
function theatre_history_show_review_box($post){
    wp_nonce_field( basename( __FILE__ ), 'theatre_history_show_review_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-review-form.php';
}

//saving metadata 
function theatre_history_show_review_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_history_show_review_nonce'] ) || !wp_verify_nonce( $_POST['theatre_history_show_review_nonce'], basename( __FILE__ ) ) )
        return;
  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    //if (!current_user_can('edit_post', $post_id))
    //    return;

    $old = get_post_meta($post_id, 'th_show_review_data', true);
    $new = array();

    $reviewers = $_POST['reviewer'];
    $links = $_POST['link'];

    $count = count( $reviewers );

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $reviewers[$i] != '' ) :
            $new[$i]['reviewer'] = stripslashes( strip_tags( $reviewers[$i] ) );

            if ( $links[$i] == '' )
                $new[$i]['link'] = '';
            else
                $new[$i]['link'] = stripslashes( $links[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'th_show_review_data', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'th_show_review_data', $old );
}

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

//------------------------------------------------------------------------------------------
/** 
 * Add Actions/filters
 * @since 0.1
 */
add_action( 'init', 'theatre_history_show_type' );
add_action( 'init', 'create_show_taxonomies', 0 );
add_action( 'admin_init', 'add_admin_menu_separator' );
add_filter( 'post_updated_messages', 'theatre_history_show_messages' );
add_action( 'contextual_help', 'theatre_history_contextual_help', 10, 3 );
add_action( 'load-post.php', 'theatre_history_show_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_history_show_meta_boxes_setup' );
