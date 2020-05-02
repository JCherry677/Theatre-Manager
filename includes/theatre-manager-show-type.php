<?php
/**
 * Custom Type - Show
 * @since 0.1
 */

//create show type
function theatre_manager_show_type(){
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
        'supports'      => array( 'title', 'editor', 'comments', 'revisions', 'thumbnail'),
        'rewrite'       => array('slug' => 'shows'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_show', $args);
}
add_action( 'init', 'theatre_manager_show_type' );

//------------------------------------------------------------------------------------------
/**
 * Show update messages.
 * @since 0.1
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function theatre_manager_show_messages( $messages ) {
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
add_filter( 'post_updated_messages', 'theatre_manager_show_messages' );

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
    register_taxonomy( 'season', array( 'theatre_show', 'theatre_committee' ), $args );

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

function theatre_manager_editor_show_columns($columns){
    unset( $columns['date'] );
    $columns['show_start'] = __( 'Start Date', 'theatre-manager');
    $columns['show_end'] = __( 'End Date', 'theatre-manager');
    return $columns;
}

//get data
function theatre_manager_show_columns( $column, $post_id ){
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
function theatre_manager_show_columns_sortable( $columns ) {
    $columns['show_start'] = 'show_start';
    $columns['show_end'] = 'show_end';
    return $columns;
}

function theatre_manager_show_orderby( $query ) {
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
add_action( 'manage_theatre_show_posts_custom_column' , 'theatre_manager_show_columns', 10, 2 );
add_action( 'pre_get_posts', 'theatre_manager_show_orderby' );
add_filter( 'manage_theatre_show_posts_columns', 'theatre_manager_editor_show_columns' );
add_filter( 'manage_edit-theatre_show_sortable_columns', 'theatre_manager_show_columns_sortable' );

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

function theatre_manager_show_meta_boxes_setup(){

    //show info
    add_action('add_meta_boxes', 'theatre_manager_show_info_meta');
    add_action('add_meta_boxes', 'theatre_manager_show_person_meta');
    add_action('add_meta_boxes', 'theatre_manager_show_crew_meta');
    add_action('add_meta_boxes', 'theatre_manager_show_review_meta');

    //save data
    add_action('save_post', 'theatre_manager_show_info_save', 10, 2);
    add_action('save_post', 'theatre_manager_show_person_save', 10, 2);
    add_action('save_post', 'theatre_manager_show_crew_save', 10, 2);
    add_action('save_post', 'theatre_manager_show_review_save', 10, 2);
}
add_action( 'load-post.php', 'theatre_manager_show_meta_boxes_setup' );
add_action( 'load-post-new.php', 'theatre_manager_show_meta_boxes_setup' );

//info meta box controller
function theatre_manager_show_info_meta(){
    add_meta_box(
        'theatre-manager-show-info', //ID
        'Show Information', //Title TODO: Internationalisation
        'theatre_manager_show_info_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

//HTML representation of the box
function theatre_manager_show_info_box($post){
    $value = get_post_meta($post->ID, 'theatre_manager_show_info', true );
    wp_nonce_field( basename( __FILE__ ), 'theatre_manager_show_info_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-info-form.php';
}

//saving metadata 
function theatre_manager_show_info_save( $post_id, $post ) {
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_manager_show_info_nonce'] ) || !wp_verify_nonce( $_POST['theatre_manager_show_info_nonce'], basename( __FILE__ ) ) )
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
function theatre_manager_show_person_meta(){
    add_meta_box(
        'theatre-manager-show-person', //ID
        'Cast', //Title TODO: Internationalisation
        'theatre_manager_show_person_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

function theatre_manager_show_person_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'theatre_manager_show_person_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-person-form.php';
}

function theatre_manager_show_person_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['theatre_manager_show_person_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['theatre_manager_show_person_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_person_info_data', true);
    $new = array();
    $known = array();

    $members = $_POST['actor'];
    $roles = $_POST['role'];

    $count = count( $members );

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
    
    //save committee details in person metadata 
    foreach ($known as $person){
        $member_new = array(); //create new array to store new data in
        $show_roles = get_post_meta($person, 'th_show_roles', true);
        $member_new[$post_id] = $new[$person];
        if (empty($show_roles)){            
            $member_new[$post_id] = $new[$person];
        } else {
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
}

//------------------------------------------------------------------------------------------
/**
 * crew meta box controller
 * @since 0.2
 */
function theatre_manager_show_crew_meta(){
    add_meta_box(
        'theatre-manager-show-crew', //ID
        'Production Team', //Title TODO: Internationalisation
        'theatre_manager_show_crew_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

function theatre_manager_show_crew_box($post, $args){
    wp_nonce_field( plugin_basename( __FILE__ ), 'theatre_manager_show_crew_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-crew-form.php';
}

function theatre_manager_show_crew_save($post_id, $post){
    // Don't wanna save this now, right?
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !isset( $_POST['theatre_manager_show_crew_nonce'] ) )
        return;
    if ( !wp_verify_nonce( $_POST['theatre_manager_show_crew_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // We do want to save? Ok!
    $old = get_post_meta($post_id, 'th_show_crew_info_data');
    $new = array();
    $known = array();

    $members = $_POST['crew-person'];
    $jobs = $_POST['crew-job'];

    $count = count( $members );

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

    //save committee details in person metadata 
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
}

//------------------------------------------------------------------------------------------
/**
 * Reviews meta box 
 * @since 0.3
 */
function theatre_manager_show_review_meta(){
    add_meta_box(
        'theatre-manager-show-review', //ID
        'Reviews', //Title TODO: Internationalisation
        'theatre_manager_show_review_box', //callback function
        'theatre_show', //post type
        'normal', //on-page location
        'core' //priority
    );
}

//HTML representation of the box
function theatre_manager_show_review_box($post){
    wp_nonce_field( basename( __FILE__ ), 'theatre_manager_show_review_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-review-form.php';
}

//saving metadata 
function theatre_manager_show_review_save( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['theatre_manager_show_review_nonce'] ) || !wp_verify_nonce( $_POST['theatre_manager_show_review_nonce'], basename( __FILE__ ) ) )
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
add_action( 'admin_init', 'add_admin_menu_separator' );

//------------------------------------------------------------------------------------------
/**
 * Show Shortcode
 * Returns show data
 * @since 0.5
 */
function theatre_manager_show_shortcode() {
    $author = get_post_meta(get_the_ID(), 'th_show_info_author', true);
    if ($author == "") $author =  "unknown";
    $start = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_start_date'));
    $end = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_end_date'));
    $cast = get_post_meta(get_the_ID(), 'th_show_person_info_data', true);
    $crew = get_post_meta(get_the_ID(), 'th_show_crew_info_data', true);
    $reviews = get_post_meta(get_the_ID(), 'th_show_review_data');
    //basic data
    $data = "<table><tbody>
            <tr><td>Playwrite</td><td>" . $author . "</td></tr>
            <tr><td>Date</td><td>" . $start . " - " . $end . "</td></tr>";
    //cast data
    $data = $data . "<tr><td>Cast</td><td><table><tbody>";
    $casttext = "";
    foreach ( $cast as $actor => $role ) {
        foreach ($role as $item){
            $casttext = $casttext . "<tr><td><a href=\"" . get_post_permalink($actor)."\">" . theatre_manager_name_lookup($actor, 'theatre_person') . "</a> as " . $item . "</td></tr>";
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //crew data
    $data = $data . "<tr><td>Crew</td><td><table><tbody>";
    $casttext = "";
    foreach ( $crew as $pos => $job ) {
        foreach ($job as $item){
            $casttext = $casttext . "<tr><td>" . $item . ": <a href=\"" . get_post_permalink($pos)."\">" . theatre_manager_name_lookup($pos, 'theatre_person') . "</td></tr>";
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //Reviews
    $data = $data . "<tr><td>Reviews</td><td><table><tbody>";
    $casttext = "";
    foreach ( $reviews as $field ) {
        foreach ($field as $item){
            $casttext = $casttext . "<tr><td><a href=\"" . $item['link'] . "\"> ". $item['reviewer'] . " reviewed this!</a></td></tr>";
        }
    }
    $data = $data . $casttext . "</tbody></table></td></tr>";
    //return all
    return $data . "</tbody></table>";
}
add_shortcode( 'show_data', 'theatre_manager_show_shortcode' );

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
