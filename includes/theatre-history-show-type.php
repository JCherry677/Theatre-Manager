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
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'excerpt', 'comments', 'revisions' ),
        'rewrite'       => array('slug' => 'shows'),
        'show_in_rest'  => true, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_show', $args);
}
add_action('init', 'theatre_history_show_type');

/**
 * Show update messages.
 * @since 0.1
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_history_messages( $messages ) {
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
add_filter( 'post_updated_messages', 'theatre_history_messages' );

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
add_action( 'contextual_help', 'theatre_history_contextual_help', 10, 3 );

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
        'show_in_rest'      => true, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'season' ),
    );
    register_taxonomy( 'season', array( 'theatre_show' ), $args );
}
add_action( 'init', 'create_show_taxonomies', 0 );


/**
 * Create Meta boxes
 * 
 * meta - Show Info
 * @since 0.1
 */

function theatre_history_meta_boxes_setup(){

    //show info
    add_action('add_meta_boxes', 'theatre_history_show_info_meta');

    //save data
    add_action('save_post', 'theatre_history_show_info_save', 10, 2);
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
function theatre_history_show_info_box(){?>

<?php wp_nonce_field( basename( __FILE__ ), 'theatre_history_show_info_nonce' ); ?>
<p>
    <label for="theatre-history-show-info"><?php _e("First Performance Date")?></label>
    <br>
    <input class="" type="date" name="theatre-history-show-info" id="theatre-history-show-info" value="<?php echo esc_attr(get_post_meta($post->ID, 'theatre_history_show_info', true ));?>" size="30"/>
</p>
<?php }

//saving metadata 
function theatre_history_show_info_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_history_show_info_nonce'] ) || !wp_verify_nonce( $_POST['theatre_history_show_info_nonce'], basename( __FILE__ ) ) )
      return $post_id;
  
    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );
  
    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
      return $post_id;
  
    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = ( isset( $_POST['theatre-history-show-info'] ) ? sanitize_html_class( $_POST['theatre-history-show-info'] ) : ’ );
  
    /* Get the meta key. */
    $meta_key = 'theatre_history_show_info';
  
    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post_id, $meta_key, true );
  
    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && ’ == $meta_value )
      add_post_meta( $post_id, $meta_key, $new_meta_value, true );
  
    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
      update_post_meta( $post_id, $meta_key, $new_meta_value );
  
    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( ’ == $new_meta_value && $meta_value )
      delete_post_meta( $post_id, $meta_key, $meta_value );
}


add_action( 'load-post.php', 'theatre_history_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_history_meta_boxes_setup' );
