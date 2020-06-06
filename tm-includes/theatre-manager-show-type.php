<?php
/**
 * Custom Type - Show
 * @since 0.1
 */

// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;
//create show type
function tm_show_type(){
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
        'supports'      => array( 'title', 'editor', 'comments', 'thumbnail'),
        'rewrite'       => array('slug' => 'shows'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-tickets-alt',
    );

    register_post_type('theatre_show', $args);
}
add_action( 'init', 'tm_show_type' );

//------------------------------------------------------------------------------------------
/**
 * Show update messages.
 * @since 0.1
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function tm_show_messages( $messages ) {
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
add_filter( 'post_updated_messages', 'tm_show_messages' );

//------------------------------------------------------------------------------------------
/**
 * Add custom taxonomies
 * 
 * taxonomy - show season
 * @since 0.1
 * taxonomy - show venue
 * @since 0.1
 * taxonomy - show type
 * @since 0.5
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
    register_taxonomy( 'season', array( 'theatre_show' ), $args );

    //venue taxonomy - hierarchical
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

    //show_type taxonomy - hierarchical
    $labels = array(
        'name'              => _x( 'Show Type', 'taxonomy general name' ),
        'singular_name'     => _x( 'Show Type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Types' ),
        'all_items'         => __( 'All Types' ),
        'edit_item'         => __( 'Edit Type' ),
        'update_item'       => __( 'Update Type' ),
        'add_new_item'      => __( 'Add New Type' ),
        'new_item_name'     => __( 'New Type Name' ),
        'menu_name'         => __( 'Show Types' ),
    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'type' ),
    );
    register_taxonomy( 'show_type', array( 'theatre_show' ), $args );
}
add_action( 'init', 'create_show_taxonomies', 0 );

//------------------------------------------------------------------------------------------
/**
 * post columns
 * 
 * show date
 * @since 0.5
 */

function tm_editor_show_columns($columns){
    unset( $columns['date'] );
    $columns['show_start'] = __( 'Start Date', 'theatre-manager');
    $columns['show_end'] = __( 'End Date', 'theatre-manager');
    return $columns;
}

//get data
function tm_show_columns( $column, $post_id ){
    switch ($column){
        case 'show_start':
            $start = get_post_meta($post_id, 'th_show_info_start_date', true);
            echo $start;
            break;
        case 'show_end':
            $end = get_post_meta($post_id, 'th_show_info_end_date', true);
            echo $end;
            break;
    }
}

//make sortable
function tm_show_columns_sortable( $columns ) {
    $columns['show_start'] = 'show_start';
    $columns['show_end'] = 'show_end';
    return $columns;
}

function tm_show_orderby( $query ) {
    if( ! is_admin() || ! $query->is_main_query() ) {
      return;
    }
  
    if ( 'show_start' === $query->get( 'orderby') ) {
      $query->set( 'orderby', 'meta_value' );
      $query->set( 'meta_key', 'th_show_info_start_date' );
    } elseif ( 'show_end' === $query->get( 'orderby') ) {
        $query->set( 'orderby', 'meta_value' );
        $query->set( 'meta_key', 'th_show_info_start_date' );
    }
}
add_action( 'manage_theatre_show_posts_custom_column' , 'tm_show_columns', 10, 2 );
add_action( 'pre_get_posts', 'tm_show_orderby' );
add_filter( 'manage_theatre_show_posts_columns', 'tm_editor_show_columns' );
add_filter( 'manage_edit-theatre_show_sortable_columns', 'tm_show_columns_sortable' );

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

function tm_show_meta_boxes_setup(){
    $options = get_option( 'tm_settings' );

    //show info
    add_action('add_meta_boxes', 'tm_show_info_meta', 1);
    add_action('save_post', 'tm_show_info_save', 10, 2);
    //content Warnings
    if (isset($options['tm_show_warnings']) && $options['tm_show_warnings'] == 1){
        add_action('add_meta_boxes', 'tm_content_meta', 1);
        add_action('save_post', 'tm_content_save', 10, 2);
    }
    //actors
    add_action('add_meta_boxes', 'tm_show_person_meta', 1);
    add_action('save_post', 'tm_show_person_save', 10, 2);
    //crew
    add_action('add_meta_boxes', 'tm_show_crew_meta', 1);
    add_action('save_post', 'tm_show_crew_save', 10, 2);
    //reviews
    add_action('add_meta_boxes', 'tm_show_review_meta', 1);
    add_action('save_post', 'tm_show_review_save', 10, 2);
}
add_action( 'load-post.php', 'tm_show_meta_boxes_setup', 1);
add_action( 'load-post-new.php', 'tm_show_meta_boxes_setup' );

//info meta box controller
function tm_show_info_meta(){
    add_meta_box(
        'theatre-manager-show-info', //ID
        'Show Information', //Title TODO: Internationalisation
        'tm_show_info_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'high' //priority
    );
}

//HTML representation of the box
function tm_show_info_box($post){
    $value = get_post_meta($post->ID, 'tm_show_info', true );
    wp_nonce_field( basename( __FILE__ ), 'tm_show_info_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-info-form.php';
}

//saving metadata 
function tm_show_info_save( $post_id, $post ) {
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tm_show_info_nonce'] ) || !wp_verify_nonce( $_POST['tm_show_info_nonce'], basename( __FILE__ ) ) )
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

/**
 * Content Warning meta box
 * @since 0.7
 */
function tm_content_meta(){
    add_meta_box(
        'theatre-manager-show-content', //ID
        'Content Warnings', //Title TODO: Internationalisation
        'tm_content_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'high' //priority
    );
}

function tm_content_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'tm_show_content_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-content-form.php';
}

function tm_content_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;
    if ( !isset( $_POST['tm_show_content_nonce'] ) )
    return;
    if ( !wp_verify_nonce( $_POST['tm_show_content_nonce'], plugin_basename( __FILE__ ) ) )
    return;
    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_content_warning_data', true);
    $new = array();

    $content = $_POST['content'];
    $count = count( $content ) - 1;

    for ( $i = 0; $i < $count; $i++ ) {
        if ( $content[$i] != '' ){
            $new[$i]['warning'] = stripslashes( strip_tags( $content[$i] ) );
        }
    }
    if ( !empty( $new ) && $new != $old ){
        update_post_meta( $post_id, 'th_show_content_warning_data', $new );
    } elseif ( empty($new) && $old ){
        delete_post_meta( $post_id, 'th_show_content_warning_data', $old );
    }
}

//------------------------------------------------------------------------------------------
/** 
 * person meta box controller
 * @since 0.2
 */
function tm_show_person_meta(){
    add_meta_box(
        'theatre-manager-show-person', //ID
        'Cast', //Title TODO: Internationalisation
        'tm_show_person_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'high' //priority
    );
}

function tm_show_person_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'tm_show_person_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-person-form.php';
}

function tm_show_person_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['tm_show_person_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['tm_show_person_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_person_info_data', true);
    $new = array();
    $known = array();

    $members = $_POST['actor'];
    $roles = $_POST['role'];

    $count = count( $members );

    $options = get_option( 'tm_settings' );
    if (isset($options['tm_people']) && $options['tm_people'] == 1){
        error_log("People is set");
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $roles[$i] != '' ) {
                if ( $members[$i] != '' ){
                    preg_match('#\((.*?)\)#', $members[$i], $match);
                    if (array_key_exists($match[1], $new)){
                        array_push($new[$match[1]], $roles[$i]);
                    } else {
                        $new[$match[1]] = array( stripslashes( strip_tags( $roles[$i] )));
                        array_push($known, $match[1]);
                    }
                }
            }
        }
        if ( !empty( $new ) && $new != $old ){
            update_post_meta( $post_id, 'th_show_person_info_data', $new );
        } elseif ( empty($new) && $old ) {
            delete_post_meta( $post_id, 'th_show_person_info_data', $old );
        }
        
        //save role details in person metadata 
        foreach ($known as $person){
            $member_new = array(); //create new array to store new data in
            $show_roles = get_post_meta($person, 'th_show_roles', true);
            $member_new[$post_id] = $new[$person];
            if (!empty($show_roles)){            
                //go through all current stored data
                foreach ($show_roles as $show => $role) {
                    if ( (abs($show-$post_id) < PHP_FLOAT_EPSILON)){
                        //update if same
                        $member_new[$show] = $new[$person];
                    } else {
                        //copy if not relevant
                        $member_new[$show] = $role;
                    }
                }
            }
            
            update_post_meta($person, 'th_show_roles', $member_new);
        }

        //remove records that no longer appear in data
        foreach ($old as $key => $value){
            if (!(in_array($key, $known))){
                $member_new = array();
                $show_roles = get_post_meta($key, 'th_show_roles', true);
                foreach ($show_roles as $show => $role) {
                    if ( (abs($show-$post_id) < PHP_FLOAT_EPSILON)){
                        //remove by not adding it
                    } else {
                        $member_new[$show] = $role;
                    }
                }
                update_post_meta($key, 'th_show_roles', $member_new);
            }
        }
    } else {
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $roles[$i] != '' ) {
                if ( $members[$i] != '' ){
                    if (array_key_exists($members[$i], $new)){
                        array_push($new[$members[$i]], $roles[$i]);
                    } else {
                        $new[$members[$i]] = array( stripslashes( strip_tags( $roles[$i] )));
                        array_push($known, $members[$i]);
                    }
                }
            }
        }
        if ( !empty( $new ) && $new != $old ){
            update_post_meta( $post_id, 'th_show_person_info_data', $new );
        } elseif ( empty($new) && $old ) {
            delete_post_meta( $post_id, 'th_show_person_info_data', $old );
        }
    }
}

//------------------------------------------------------------------------------------------
/**
 * crew meta box controller
 * @since 0.2
 */
function tm_show_crew_meta(){
    add_meta_box(
        'theatre-manager-show-crew', //ID
        'Production Team', //Title TODO: Internationalisation
        'tm_show_crew_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'high' //priority
    );
}

function tm_show_crew_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'tm_show_crew_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-crew-form.php';
}

function tm_show_crew_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['tm_show_crew_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['tm_show_crew_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_crew_info_data');
    $new = array();
    $known = array();

    $members = $_POST['crew-person'];
    $jobs = $_POST['crew-job'];

    $count = count( $members );

    $options = get_option( 'tm_settings' );
    if (isset($options['tm_people']) && $options['tm_people'] == 1){
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $jobs[$i] != '' ) {
                if ( $members[$i] != '' ){
                    preg_match('#\((.*?)\)#', $members[$i], $match);
                    if (array_key_exists($match[1], $new)){
                        array_push($new[$match[1]], $jobs[$i]);
                    } else {
                        $new[$match[1]] = array( stripslashes( strip_tags( $jobs[$i] )));
                        array_push($known, $match[1]);
                    }
                }
            }
        }        
        if ( empty($new) && $old ){
            delete_post_meta( $post_id, 'th_show_crew_info_data', $old );
        } else{
            update_post_meta( $post_id, 'th_show_crew_info_data', $new );
        }

        //save crew details in person metadata 
        foreach ($known as $person){
            $member_new = array(); //create new array to store new data in
            $crew_roles = get_post_meta($person, 'th_crew_roles', true);
            $member_new[$post_id] = $new[$person];
            if (empty($crew_roles)){            
                $member_new[$post_id] = $new[$person];
            } else {
                //go through all current stored data
                foreach ($crew_roles as $show => $role) {
                    if ( (abs($show-$post_id) < PHP_FLOAT_EPSILON)){
                        //update if same
                        $member_new[$show] = $new[$person];
                    } else {
                        //copy if not relevant
                        $member_new[$show] = $role;
                    }
                }
            }
            
            update_post_meta($person, 'th_crew_roles', $member_new);
        }

        //remove records that no longer appear in data
        foreach ($old as $key => $value){
            if (!(in_array($key, $known))){
                $member_new = array();
                $crew_roles = get_post_meta($key, 'th_crew_roles', true);
                foreach ($crew_roles as $show => $role) {
                    if ( (abs($show-$post_id) < PHP_FLOAT_EPSILON)){
                        //remove by not adding it
                    } else {
                        $member_new[$show] = $role;
                    }
                }
                update_post_meta($key, 'th_crew_roles', $member_new);
            }
        }
    } else {
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $jobs[$i] != '' ) {
                if ( $members[$i] != '' ){
                    if (array_key_exists($members[$i], $new)){
                        array_push($new[$members[$i]], $jobs[$i]);
                    } else {
                        $new[$members[$i]] = array( stripslashes( strip_tags( $jobs[$i] )));
                        array_push($known, $members[$i]);
                    }
                }
            }
        }        
        if ( empty($new) && $old ){
            delete_post_meta( $post_id, 'th_show_crew_info_data', $old );
        } else{
            update_post_meta( $post_id, 'th_show_crew_info_data', $new );
        }
    }
}

//------------------------------------------------------------------------------------------
/**
 * Reviews meta box 
 * @since 0.3
 */
function tm_show_review_meta(){
    add_meta_box(
        'theatre-manager-show-review', //ID
        'Reviews', //Title TODO: Internationalisation
        'tm_show_review_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'high' //priority
    );
}

//HTML representation of the box
function tm_show_review_box($post){
    wp_nonce_field( basename( __FILE__ ), 'tm_show_review_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-review-form.php';
}

//saving metadata 
function tm_show_review_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tm_show_review_nonce'] ) || !wp_verify_nonce( $_POST['tm_show_review_nonce'], basename( __FILE__ ) ) )
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

//------------------------------------------------------------------------------------------
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

//------------------------------------------------------------------------------------------
/**
 * Show Shortcode
 * Returns show data
 * @since 0.5
 */
function tm_show_shortcode() {
    $author = get_post_meta(get_the_ID(), 'th_show_info_author', true);
    if ($author == "") $author =  "unknown";
    $start = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_start_date'));
    $end = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_end_date'));
    $cast = get_post_meta(get_the_ID(), 'th_show_person_info_data', true);
    $crew = get_post_meta(get_the_ID(), 'th_show_crew_info_data', true);
    $reviews = get_post_meta(get_the_ID(), 'th_show_review_data');
    $data = "";
    //basic data
    $options = get_option( 'tm_settings' );
    if (isset($options['tm_show_warnings']) && $options['tm_show_warnings'] == 1){
        $content = get_post_meta(get_the_ID(), 'th_show_content_warning_data', true);
        if (is_null( $content ) || empty($content)){
            $data = $data . "<p> This show has no content warnings.</p>";
        } else {
            $data = $data . '<p> This show\'s Content Warnings include:</p><p style="text-align: center !important;">';
            foreach ( $content as $item) {
                $data = $data . "<a href=\"" . get_post_permalink($item['warning'])."\">" . get_the_title($item['warning'], 'theatre_warning') . "</a>, ";
            }
            $data = substr($data, 0, -2);
            $data = $data . "</p>";
        }
    }
    $data =  $data ."<table><tbody>
            <tr><td>Playwright</td><td>" . $author . "</td></tr>
            <tr><td>Date</td><td>" . $start . " - " . $end . "</td></tr>";
    //cast data
    $data = $data . "<tr><td>Cast</td><td><table><tbody>";
    $casttext = "";
    if (is_null( $cast ) || empty($cast)){
        $casttext = "This show has no known cast";
    } else {
        if($cast){
            foreach ( $cast as $actor => $role ) {
                foreach ($role as $item){
                    $casttext = $casttext . "<tr><td><a href=\"" . get_post_permalink($actor)."\">" . get_the_title($actor) . "</a> as " . $item . "</td></tr>";
                }
            }
        } else {
            foreach ( $cast as $actor => $role ) {
                foreach ($role as $item){
                    $casttext = $casttext . "<tr><td>" . $actor . " as " . $item . "</td></tr>";
                }
            }
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //crew data
    $data = $data . "<tr><td>Production Team</td><td><table><tbody>";
    $casttext = "";
    if (is_null( $crew ) || empty($crew)){
        $casttext = "This show has no known crew";
    } else {
        foreach ( $crew as $pos => $job ) {
            foreach ($job as $item){
                $casttext = $casttext . "<tr><td>" . $item . ": <a href=\"" . get_post_permalink($pos)."\">" . get_the_title($pos) . "</td></tr>";
            }
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //Reviews
    $data = $data . "<tr><td>Reviews</td><td><table><tbody>";
    $casttext = "";
    $casttext = "";
    if (is_null( $reviews ) || empty($reviews)){
        $casttext = "This show has no reviews yet";
    } else {
        foreach ( $reviews as $field ) {
            foreach ($field as $item){
                $casttext = $casttext . "<tr><td><a href=\"" . $item['link'] . "\"> ". $item['reviewer'] . " reviewed this!</a></td></tr>";
            }
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //return all
    return $data . "</tbody></table>";
}
add_shortcode( 'show_data', 'tm_show_shortcode' );

//------------------------------------------------------------------------------------------
/** 
 * Add Widgets
 * 
 * Season Listing widget
 * @since 0.6
 * Venue Listing widget
 * @since 0.6
 * Show Type Listing widget
 * @since 0.6
 */
class tm_show_season_widget extends WP_Widget {

    function __construct(){
        parent::__construct(
            'tm_show_season_widget', //ID
            __('Show Seasons', 'theatre-manager'),//Widget
            array(
                'description' => __('Lists all seasons that there are shows in', 'theatre-manager'),
            ),
        );
    }

    //Front end
    public function widget($args, $instance){
        $title = apply_filters( 'widget_title', $instance['title'] );

        $count        = ! empty( $instance['count'] ) ? '1' : '0';
		$hierarchical = ! empty( $instance['hierarchical'] ) ? '1' : '0';

        // before and after widget arguments defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
			'orderby'      => 'name',
			'show_count'   => $count,
            'hierarchical' => $hierarchical,
            'taxonomy'     => 'season',
		);?>
        <p>View shows by Season</p>
		<ul>
			<?php
			$cat_args['title_li'] = '';

			wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
			?>
		</ul>
        <?php
        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form( $instance ) {
        // Defaults.
		$instance     = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
			<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy' ); ?></label>
		</p>
		<?php 
    }
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['count']        = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;

		return $instance;
    }
}

class tm_show_venue_widget extends WP_Widget {

    function __construct(){
        parent::__construct(
            'tm_show_venue_widget', //ID
            __('Show Venues', 'theatre-manager'),//Widget
            array(
                'description' => __('Lists all Venues that there are shows in', 'theatre-manager'),
            ),
        );
    }

    //Front end
    public function widget($args, $instance){
        $title = apply_filters( 'widget_title', $instance['title'] );

        $count        = ! empty( $instance['count'] ) ? '1' : '0';
		$hierarchical = ! empty( $instance['hierarchical'] ) ? '1' : '0';

        // before and after widget arguments defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
			'orderby'      => 'name',
			'show_count'   => $count,
            'hierarchical' => $hierarchical,
            'taxonomy'     => 'venue',
        );
        echo "<p>View shows by Venue</p>";
        ?>
		<ul>
			<?php
			$cat_args['title_li'] = '';

			wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
			?>
		</ul>
        <?php
        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form( $instance ) {
        // Defaults.
		$instance     = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
			<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy' ); ?></label>
		</p>
		<?php 
    }
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['count']        = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;

		return $instance;
    }
}

class tm_show_type_widget extends WP_Widget {

    function __construct(){
        parent::__construct(
            'tm_show_type_widget', //ID
            __('Show Types', 'theatre-manager'),//Widget
            array(
                'description' => __('Lists show types', 'theatre-manager'),
            ),
        );
    }

    //Front end
    public function widget($args, $instance){
        $title = apply_filters( 'widget_title', $instance['title'] );

        $count        = ! empty( $instance['count'] ) ? '1' : '0';
		$hierarchical = ! empty( $instance['hierarchical'] ) ? '1' : '0';

        // before and after widget arguments defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
			'orderby'      => 'name',
			'show_count'   => $count,
            'hierarchical' => $hierarchical,
            'taxonomy'     => 'show_type',
        );
        echo "<p>View shows by Type</p>";
        ?>
		<ul>
			<?php
			$cat_args['title_li'] = '';

			wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
			?>
		</ul>
        <?php
        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form( $instance ) {
        // Defaults.
		$instance     = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
			<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy' ); ?></label>
		</p>
		<?php 
    }
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['count']        = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;

		return $instance;
    }
}


// Register and load the widgets
function tm_load_widgets() {
    register_widget( 'tm_show_season_widget' );
    register_widget( 'tm_show_venue_widget' );
    register_widget( 'tm_show_type_widget' );
}
add_action( 'widgets_init', 'tm_load_widgets' );

$options = get_option( 'tm_settings' );
if (isset($options['tm_archive']) && $options['tm_archive'] == 1){
    //------------------------------------------------------------------------------------------
    /** 
     * Add Bulk Action - move to site
     * @since 0.7
     */
    function tm_move_show_action($bulk_array){
        if( $sites = get_sites( array(
            // 'site__in' => array( 1,2,3 )
            'site__not_in'  => get_current_blog_id(), // excluding current blog
            'number'        => 5,
        ))) {
            foreach( $sites as $site ) {
                $bulk_array['move_to_'.$site->blog_id] = 'Move to "' .$site->blogname . '"';
            }
        }
    
        return $bulk_array;
    }
    add_filter('bulk_actions-edit-theatre_show', 'tm_move_show_action');

    function tm_bulk_move_show_handler($redirect, $doaction, $object_ids){
        // we need query args to display correct admin notices
        $redirect = remove_query_arg( array( 'tm_posts_moved', 'tm_blogid' ), $redirect );
    
        // our actions begin with "move_to_", so let's check if it is a target action
    if( strpos( $doaction, "move_to_" ) === 0 ) {
            $blog_id = str_replace( "move_to_", "", $doaction );

            foreach ( $object_ids as $post_id ) {
                // get the original post object as an array
                $post = get_post( $post_id, ARRAY_A );
                $taxonomies = get_object_taxonomies( $post['theatre_show'] );
                foreach ( $taxonomies as $taxonomy ) {
                    $post_terms = wp_get_object_terms( $post_id, $taxonomy, array('fields' => 'slugs') );
                }
                // get all the post meta
                $data = get_post_custom($post_id);
                // empty ID field, to tell WordPress to create a new post, not update an existing one
                $post['ID'] = '';

                switch_to_blog( $blog_id );

                // insert the post
                $inserted_post_id = wp_insert_post($post); // insert the post
                // update post terms
                foreach ( $taxonomies as $taxonomy ) {
                    wp_set_object_terms( $inserted_post_id, $post_terms, $taxonomy, false );
                }
                // add post meta
                foreach ( $data as $key => $values) {
                    // if you do not want weird redirects
                    if( $key == '_wp_old_slug' ) {
                        continue;
                    }
                    foreach ($values as $value) {
                        add_post_meta( $inserted_post_id, $key, $value );
                    }
                }
                restore_current_blog();

                wp_delete_post( $post_id );
            }
            $redirect = add_query_arg( array(
                'tm_posts_moved' => count( $object_ids ),
                'tm_blogid' => $blog_id
            ), $redirect );
        }
        return $redirect;
    }
    add_filter( 'handle_bulk_actions-edit-theatre_show', 'tm_bulk_move_show_handler', 10, 3 );

    function tm_bulk_move_notice() {
    
        if( ! empty( $_REQUEST['tm_posts_moved'] ) ) {

            $blog = get_blog_details( $_REQUEST['tm_blogid'] );//get blog moved to
    
            printf( '<div id="message" class="updated notice is-dismissible"><p>' .
                _n( '%d post has been moved to "%s".', '%d posts have been moved to "%s".', intval( $_REQUEST['tm_posts_moved'] )
            ) . '</p></div>', intval( $_REQUEST['tm_posts_moved'] ), $blog->blogname );
        }
    }
    add_action( 'admin_notices', 'tm_bulk_move_notice' );
}
/**
 * Update show post date when submitted
 * @since 0.8.1
 * Requires using people in shows to be set in setup
 */
function update_post_date( $data , $error ) {

	// first check if the post_type is the right one
	if (array_key_exists('post_type', $_POST) && $_POST['post_type'] === 'theatre_show') {
		// check if the correct metadata POST variable is available
		if (array_key_exists('th_show_info_start_date', $_POST)) {
			$metaData = $_POST['th_show_info_start_date'];
            // format is 'yyyy-mm-dd hh:mm:ss', that is Wordpress itself
            $datumPost = $data['post_date'];

            // a bit extended for clearity
            $dateOne = new DateTime($datumPost);
            $dateTwo = new DateTime($metaData);

            // we use the DateTime->modify function
            $dateOne->modify($dateTwo->format('Y-m-d'));

            // We apply a change to post_date and post_date_gmt to the same value
            $data['post_date'] = $dateOne->format('Y-m-d H:i:s');
            $data['post_date_gmt'] = $dateOne->format('Y-m-d H:i:s');
		}
	}

	// finally we return the $data and Wordpress will take it over from there
	return $data;
}
$options = get_option( 'tm_settings' );
if (isset($options['tm_people']) && $options['tm_people'] == 1) {
	// Filters to setup the automatic change of the publish date
	add_filter( 'wp_insert_post_data', 'update_post_date', 99, 2 );
	add_filter( 'wp_update_post_data', 'update_post_date', 99, 2 );
}