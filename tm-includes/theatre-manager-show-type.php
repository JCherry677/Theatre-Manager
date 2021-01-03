<?php
/**
 * Custom Type - Show
 * @since 0.1
 */

// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;
//create show type
function tm_show_type(){
    $show_block_option = get_option('tm_block_show');
	$show_block = false;
	if (isset($show_block_option) && $show_block_option == 1) {
		$show_block = true;
	}
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
        'description'   => 'Our Shows',
        'public'        => true,
        'menu_position' => 30,
        'supports'      => array( 'title', 'editor', 'comments', 'thumbnail'),
        'rewrite'       => array('slug' => 'shows'),
        'show_in_rest'  => $show_block, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-tickets-alt',
    );

    register_post_type('theatre_show', $args);
    flush_rewrite_rules();
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
        'name'                  => _x( 'Season', 'taxonomy general name' ),
        'singular_name'         => _x( 'Season', 'taxonomy singular name' ),
        'search_items'          => __( 'Search Seasons' ),
        'all_items'             => __( 'All Seasons' ),
        'parent_item'           => __( 'Parent Season' ),
        'parent_item_colon'     => __( 'Parent Season:' ),
        'edit_item'             => __( 'Edit Season' ),
        'view_item'             => __( 'View Season' ),
        'update_item'           => __( 'Update Season' ),
        'add_new_item'          => __( 'Add New Season' ),
        'new_item_name'         => __( 'New Season Name' ),
        'not_found'             => __( 'No Seasons '),
        'items_list_navigation' => __( 'Season List Navigation' ),
        'items_list'            => __( 'Season List' ),
        'back_to_items'         => __( '&larr; Go to Seasons' ),
        'menu_name'             => __( 'Seasons' ),

    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'season' ),
    );
    register_taxonomy( 'season', array( 'theatre_show' ), $args );

    //venue taxonomy - hierarchical
    $labels = array(
        'name'                  => _x( 'Venues', 'taxonomy general name' ),
        'singular_name'         => _x( 'Venue', 'taxonomy singular name' ),
        'search_items'          => __( 'Search Venues' ),
        'all_items'             => __( 'All Venues' ),
        'parent_item'           => __( 'Parent Venue' ),
        'parent_item_colon'     => __( 'Parent Venue:' ),
        'edit_item'             => __( 'Edit Venue' ),
        'view_item'             => __( 'View Venue' ),
        'update_item'           => __( 'Update Venue' ),
        'add_new_item'          => __( 'Add New Venue' ),
        'new_item_name'         => __( 'New Venue Name' ),
        'not_found'             => __( 'No Venues '),
        'items_list_navigation' => __( 'Venue List Navigation' ),
        'items_list'            => __( 'Venue List' ),
        'back_to_items'         => __( '&larr; Go to Venues' ),
        'menu_name'             => __( 'Venues' ),
    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'venue' ),
    );
    register_taxonomy( 'venue', array( 'theatre_show' ), $args );

    //show_type taxonomy - hierarchical
    $labels = array(
        'name'                  => _x( 'Show Type', 'taxonomy general name' ),
        'singular_name'         => _x( 'Show Type', 'taxonomy singular name' ),
        'search_items'          => __( 'Search Show Types' ),
        'all_items'             => __( 'All Show Types' ),
        'parent_item'           => __( 'Parent Show Type' ),
        'parent_item_colon'     => __( 'Parent Show Type:' ),
        'edit_item'             => __( 'Edit Show Type' ),
        'view_item'             => __( 'View Show Type' ),
        'update_item'           => __( 'Update Show Type' ),
        'add_new_item'          => __( 'Add New Show Type' ),
        'new_item_name'         => __( 'New Show Type Name' ),
        'not_found'             => __( 'No Show Types '),
        'items_list_navigation' => __( 'Show Type List Navigation' ),
        'items_list'            => __( 'Show Type List' ),
        'back_to_items'         => __( '&larr; Go to Show Types' ),
        'menu_name'             => __( 'Show Types' ),
    );
    $args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true, //true => Gutenberg editor, false => old editor
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'type' ),
    );
    register_taxonomy( 'show_type', array( 'theatre_show' ), $args );

    /**
     * content_warning taxonomy
     * @since 1.0
     * Replaces Custom post type
     */
    $warnings = get_option('tm_show_warnings');
    if (isset($warnings) && $warnings == 1) {
        $labels = array(
            'name'                          => _x( 'Content Warnings', 'taxonomy general name' ),
            'singular_name'                 => _x( 'Content Warning', 'taxonomy singular name' ),
            'search_items'                  => __( 'Search Content Warnings' ),
            'all_items'                     => __( 'All Content Warnings' ),
            'edit_item'                     => __( 'Edit Content Warning' ),
            'view_item'                     => __( 'View Content Warning' ),
            'update_item'                   => __( 'Update Content Warning' ),
            'add_new_item'                  => __( 'Add New Content Warning' ),
            'new_item_name'                 => __( 'New Content Warning Name' ),
            'not_found'                     => __( 'No Content Warnings '),
            'items_list_navigation'         => __( 'Content Warning List Navigation' ),
            'items_list'                    => __( 'Content Warning List' ),
            'back_to_items'                 => __( '&larr; Go to Content Warnings' ),
            'menu_name'                     => __( 'Content Warnings' ),
            'popular_items'                 => __( 'Popular Content Warnings' ),
            'separate_items_with_commas'    => __( 'Separate Content Warnings with commas' ),
            'add_or_remove_items'           => __( 'Add or remove Content Warnings' ),
            'choose_from_most_used'         => __( 'Choose from the most used Content Warnings' ),
        );
        $args = array(
            'labels'        => $labels,
            'description'   => __( 'Content Warnings for shows' ),
            'public'        => true,
            'hierarchical'  => false,
            'show_in_rest'  => true,
            'query_var'     => true,
            'rewrite'       => array( 'slug' => 'content_warning' ),
        );
        register_taxonomy( 'content_warning', array( 'theatre_show' ), $args );
    }
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

add_action('add_meta_boxes', 'tm_show_meta', 1, 1);
add_action('save_post', 'tm_meta_save', 10, 2);

//Meta Box Controllers
function tm_show_meta($post_id){
    if (get_post_type() == "theatre_show") {
        //show info
        add_meta_box(
            'theatre-manager-show-info', //ID
            __('Show Information'), //Title
            'tm_show_info_box', //callback function
            'theatre_show', //post type
            'normal', //on-page location
            'high' //priority
        );

        //Actors
        add_meta_box(
            'theatre-manager-show-person', //ID
            __('Cast'), //Title
            'tm_show_person_box', //callback function
            'theatre_show', //post type
            'normal', //on-page location
            'high' //priority
        );

        //Crew
        add_meta_box(
            'theatre-manager-show-crew', //ID
            __('Production Team'), //Title
            'tm_show_crew_box', //callback function
            'theatre_show', //post type
            'normal', //on-page location
            'high' //priority
        );

        //Reviews
        $reviews = get_option('tm_show_reviews');
        if (isset($reviews) && $reviews == 1) {
            add_meta_box(
                'theatre-manager-show-review', //ID
                __('Reviews'), //Title
                'tm_show_review_box', //callback function
                'theatre_show', //post type
                'normal', //on-page location
                'high' //priority
            );
        }
    }
}

//Rendering of Boxes
/**
 * Show Data meta box
 * @since 0.1
 */
function tm_show_info_box($post){
    wp_nonce_field( basename( __FILE__ ), 'tm_show_meta_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-info-form.php';
}
/**
 * Actor meta box controller
 * @since 0.2
 */
function tm_show_person_box($post, $args){
    wp_nonce_field( basename( __FILE__ ), 'tm_show_meta_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-person-form.php';
}
/**
 * crew meta box controller
 * @since 0.2
 */
function tm_show_crew_box($post, $args){
    wp_nonce_field( basename( __FILE__ ), 'tm_show_meta_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-crew-form.php';
}
/**
 * Reviews meta box
 * @since 0.3
 */
function tm_show_review_box($post){
    wp_nonce_field( basename( __FILE__ ), 'tm_show_meta_nonce' );
    include plugin_dir_path( __FILE__ ) . 'forms/show-review-form.php';
}

//Saving Meta Boxes
//Utility Functions
/**
 * Formats people data in specific way for saving
 * @param $itemCount int number of people
 * @param $people array people
 * @param $roles array roles
 * @return array formatted data
 */
function createArrays(int $itemCount, array $people, array $roles): array
{
    $new = array();
    for ( $i = 0; $i < $itemCount; $i++ ) {
        if ( $roles[$i] != '' ) {
            if ( $people[$i] != '' ){
                if (array_key_exists($people[$i], $new)){
                    array_push($new[$people[$i]], $roles[$i]);
                } else {
                    $new[$people[$i]] = array( stripslashes( strip_tags( $roles[$i] )));
                }
            }
        }
    }
    return $new;
}

/**
 * Updates all role ids in $roleData with their new roles
 * @param $post_id int id of calling show
 * @param $metaKey string calling show's meta key ('th_show_person_info_data' or 'th_show_crew_info_data')
 * @param $roleKey string meta key to update each role's meta ('th_show_roles' or 'th_crew_roles')
 * @param $roleData array $person=>$role pairs of updated roles
 */
function updateOtherMeta(int $post_id, string $metaKey, string $roleKey, array $roleData){
    //save details in person metadata
    foreach ($roleData as $person => $role){
        //get person's old roles
        $roles = get_post_meta($person, $roleKey, true);
        if (empty($old_roles)){
            $roles = array();
        }
        $roles[$post_id] = $role;
        update_post_meta($person, $roleKey, $roles);
    }

    //remove any old people
    $current = get_post_meta($post_id, $metaKey, true);
    foreach ($current as $person => $role){
        if (!(in_array($person, $roleData))){
            $person_new = array();
            $person_old = get_post_meta($person, $roleKey, true);
            foreach ($person_old as $show => $showRole){
                if((int)$show != (int)$post_id){
                    $person_new[$show] = $showRole;
                }
            }
            update_post_meta($person, $roleKey, $person_new);
        }
    }
}

/**
 * Submit the array into the given meta key
 * @param $post_id int id of calling show
 * @param $metaKey string meta key to save to
 * @param $data array new data to submit
 */
function submitMeta(int $post_id, string $metaKey, array $data){
    $current = get_post_meta($post_id, $metaKey, true);

    if ( empty($data) && $current ) {
        delete_post_meta( $post_id, $metaKey, $current );
    } else {
        update_post_meta( $post_id, $metaKey, $data );
    }
}

//Saving Function
function tm_meta_save($post_id, $post){
    if (get_post_type() == "theatre_show") {
        $review = get_option('tm_show_reviews');
        $people = get_option('tm_people');
        // Don't wanna save this now, right?
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            error_log('[TM] Save Failed - Autosaving');
            return;
        }

        //This should really have Nonces but i cba to get that to work

        // We do want to save? Ok!

        //start with general info
        $fields = [
            'th_show_info_author',
            'th_show_info_start_date',
            'th_show_info_end_date',
        ];
        foreach ($fields as $field) {
            if (array_key_exists($field, $_POST)) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            } else {
                add_post_meta($post_id, $field, sanitize_text_field($_POST[$field]), true);
            }
        }
        //Actors
        if (isset($_POST['actor']) && isset($_POST['role'])) {
            //Get info
            $members = $_POST['actor'];
            $roles = $_POST['role'];
            $count = count($members);

            //update this post
            $new = createArrays($count, $members, $roles);
            submitMeta($post_id, 'th_show_person_info_data', $new);

            //if enabled, update other people
            if (isset($people) && $people == 1) {
                updateOtherMeta($post_id, 'th_show_person_info_data', 'th_show_roles', $new);
            }
        }

        //Crew
        if (isset($_POST['crew-person']) && isset($_POST['crew-job'])) {
            //Get info
            $members = $_POST['crew-person'];
            $roles = $_POST['crew-job'];
            $count = count($members);

            //update this post
            $new = createArrays($count, $members, $roles);
            submitMeta($post_id, 'th_show_crew_info_data', $new);

            //if enabled, update other people
            if (isset($people) && $people == 1) {
                updateOtherMeta($post_id, 'th_show_crew_info_data', 'th_crew_roles', $new);
            }
        }

        if (isset($review) && $review == 1) {
            if (isset($_POST['reviewer']) && isset($_POST['link'])) {
                //get info
                $reviewers = $_POST['reviewer'];
                $links = $_POST['link'];
                $count = count($reviewers);

                //organise data
                $new = array();
                for ($i = 0; $i < $count; $i++) {
                    if ($reviewers[$i] != '') :
                        $new[$i]['reviewer'] = stripslashes(strip_tags($reviewers[$i]));

                        if ($links[$i] == '')
                            $new[$i]['link'] = '';
                        else
                            $new[$i]['link'] = stripslashes($links[$i]); // and however you want to sanitize
                    endif;
                }
                //submit data
                submitMeta($post_id, 'th_show_review_data', $new);
            }
        }
    }
}

//------------------------------------------------------------------------------------------
/**
 * Show Shortcode
 * Returns show data
 * @since 0.5
 */
function tm_show_shortcode() {
    $useCast = get_option( 'tm_people' ) == 1;
    $author = get_post_meta(get_the_ID(), 'th_show_info_author', true);
    if ($author == "") $author =  "unknown";
    $start = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_start_date'));
    $end = implode(" ", get_post_meta(get_the_ID(), 'th_show_info_end_date'));
    if ($start == $end){
        // Remove end for one day performances
        $end = null;
        // Change Date format
        $start = date("d/m/Y", strtotime($start));
    } else{
        // Change Date format
        $start = date("d/m/Y", strtotime($start));
        $end = date("d/m/Y", strtotime($end));
        $end = " - " . $end;
    }
    $cast = get_post_meta(get_the_ID(), 'th_show_person_info_data', true);
    $crew = get_post_meta(get_the_ID(), 'th_show_crew_info_data', true);
    $reviews = get_post_meta(get_the_ID(), 'th_show_review_data');
    $data = "";

    //basic data
    $warnings = get_option( 'tm_show_warnings' );
    if (isset($warnings) && $warnings == 1){
        $content = get_the_terms(get_the_ID(), 'content_warning');
        if (is_null( $content ) || empty($content)){
            $data .= "<hr><p> This show has no content warnings.</p><hr>";
        } else {
            $data .= '<hr><h5> This show\'s Content Warnings include:</h5><p>';
            foreach ( $content as $item) {
                $data .= $item->name . ", " ;
            }
            $data = substr($data, 0, -2);
            $data .= "</p><p>Please get in touch if you have any questions about our Content Warnings</p><hr>";
        }
    }

    //General Info
    $data =  $data ."<table><tbody>
            <tr><td>Playwright</td><td>" . $author . "</td></tr>
            <tr><td>Date</td><td>" . $start . $end . "</td></tr>";

    //cast data
    $data .= "<tr><td>Cast</td><td><table><tbody>";
    $casttext = "";
    if (is_null( $cast ) || empty($cast)){
        $casttext = "This show has no known cast";
    } else {
        if($useCast){
            foreach ( $cast as $actor => $role ) {
                foreach ($role as $item){
                    $casttext .= "<tr><td><a href=\"" . get_post_permalink($actor)."\">" . get_the_title($actor) . "</a> as " . $item . "</td></tr>";
                }
            }
        } else {
            foreach ( $cast as $actor => $role ) {
                foreach ($role as $item){
                    $casttext .= "<tr><td>" . $actor . " as " . $item . "</td></tr>";
                }
            }
        }
    }
    $data .= $casttext . "</tbody></table></td></tr>";

    //crew data
    $data .= "<tr><td>Production Team</td><td><table><tbody>";
    $casttext = "";
    if (is_null( $crew ) || empty($crew)){
        $casttext = "This show has no known crew";
    } else {
        if ($useCast){
            foreach ( $crew as $pos => $job ) {
                foreach ($job as $item){
                    $casttext .= "<tr><td>" . $item . ": <a href=\"" . get_post_permalink($pos)."\">" . get_the_title($pos) . "</td></tr>";
                }
            }
        } else {
            foreach ( $crew as $pos => $job ) {
                foreach ($job as $item){
                    $casttext .= "<tr><td>" . $item . ": " . $pos . "</td></tr>";
                }
            }
        }
    }
    $data .= $casttext . "</tbody></table></td></tr>";

    //Reviews
    $reviews_option = get_option( 'tm_show_reviews' );
    if (isset($reviews_option) && $reviews_option == 1) {
        $data .= "<tr><td>Reviews</td><td><table><tbody>";
        $casttext = "";
        if (is_null($reviews) || empty($reviews)) {
            $casttext = "This show has no reviews yet";
        } else {
            foreach ($reviews as $field) {
                foreach ($field as $item) {
                    $casttext .= "<tr><td><a href=\"" . ((strpos($item['link'], 'http') === 0) ? "" : "http://") . $item['link'] . "\"> " . $item['reviewer'] . " reviewed this!</a></td></tr>";
                }
            }
        }
        $data .= $casttext . "</tbody></table></td></tr>";
    }

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

$archive = get_option( 'tm_archive' );
if (isset($archive) && $archive == 1 && is_multisite()){
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
                $bulk_array['move_to_'.$site->blog_id] = 'Move to Site: ' . $site->blogname;
            }
        }
    
        return $bulk_array;
    }
    add_filter('bulk_actions-edit-theatre_show', 'tm_move_show_action');

    function tm_create_data_table($post_id): string
    {
        $useCast = get_option( 'tm_people' ) == 1;
        $cast = get_post_meta($post_id, 'th_show_person_info_data', true);
        $crew = get_post_meta($post_id, 'th_show_crew_info_data', true);
        $reviews = get_post_meta($post_id, 'th_show_review_data');
        $data = "<table><tbody>";
        //cast data
        $data .= "<tr><td>Cast</td><td><table><tbody>";
        $casttext = "";
        if (is_null( $cast ) || empty($cast)){
            $casttext = "This show has no known cast";
        } else {
            if($useCast){
                foreach ( $cast as $actor => $role ) {
                    foreach ($role as $item){
                        $casttext .= "<tr><td><a href=\"" . get_post_permalink($actor)."\">" . get_the_title($actor) . "</a> as " . $item . "</td></tr>";
                    }
                }
            } else {
                foreach ( $cast as $actor => $role ) {
                    foreach ($role as $item){
                        $casttext .= "<tr><td>" . $actor . " as " . $item . "</td></tr>";
                    }
                }
            }
        }
        $data .= $casttext . "</tbody></table></td></tr>";

        //crew data
        $data .= "<tr><td>Production Team</td><td><table><tbody>";
        $casttext = "";
        if (is_null( $crew ) || empty($crew)){
            $casttext = "This show has no known crew";
        } else {
            if ($useCast){
                foreach ( $crew as $pos => $job ) {
                    foreach ($job as $item){
                        $casttext .= "<tr><td>" . $item . ": <a href=\"" . get_post_permalink($pos)."\">" . get_the_title($pos) . "</td></tr>";
                    }
                }
            } else {
                foreach ( $crew as $pos => $job ) {
                    foreach ($job as $item){
                        $casttext .= "<tr><td>" . $item . ": " . $pos . "</td></tr>";
                    }
                }
            }
        }
        $data .= $casttext . "</tbody></table></td></tr>";

        //return all
        return $data . "</tbody></table>";
    }

    function tm_bulk_move_show_handler($redirect, $doaction, $object_ids){
        // we need query args to display correct admin notices
        $redirect = remove_query_arg( array( 'tm_posts_moved', 'tm_blogid' ), $redirect );
    
        // our actions begin with "move_to_", so let's check if it is a target action
        if( strpos( $doaction, "move_to_" ) === 0 ) {
            $blog_id = str_replace( "move_to_", "", $doaction );

            foreach ( $object_ids as $post_id ) {
                $taxonomies_terms = array();
                // get the original post object as an array
                $post = get_post( $post_id, ARRAY_A );

                //TODO complete todo below and remove this line!
                $post['post_content'] .= "<p>The data below has been moved to the archive site recently and will be removed soon.</p>". tm_create_data_table($post_id);

                $taxonomies = get_object_taxonomies('theatre_show');

                foreach ( $taxonomies as $taxonomy ) {
                    $taxonomies_terms[$taxonomy] = wp_get_object_terms( $post_id, $taxonomy, array('fields' => 'slugs') );
                }

                // get all the post meta
                $data = get_post_custom($post_id);

                //Get thumbnail image
                $post_thumbnail_id = get_post_thumbnail_id($post_id);
                $image_url = wp_get_attachment_image_src($post_thumbnail_id, 'full');
                $image_url = $image_url[0];

                // empty ID field, to tell WordPress to create a new post, not update an existing one
                $post['ID'] = '';

                //Move to the other blog
               switch_to_blog( $blog_id );

                // insert the post
                $inserted_post_id = wp_insert_post($post); // insert the post

                // Add Featured Image to Post
                $upload_dir = wp_upload_dir(); // Set upload folder
                $image_data = file_get_contents($image_url); // Get image data
                $filename   = basename($image_url); // Create image file name
                // Check folder permission and define file location
                if( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }
                // Create the image  file on the server
                file_put_contents( $file, $image_data );
                // Check image file type
                $wp_filetype = wp_check_filetype( $filename, null );
                // Set attachment data
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name( $filename ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
                // Create the attachment
                $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                // Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                // Define attachment metadata
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                // Assign metadata to attachment
                wp_update_attachment_metadata( $attach_id, $attach_data );
                // And finally assign featured image to post
                set_post_thumbnail( $inserted_post_id, $attach_id );


                // update post terms
                foreach ( $taxonomies as $taxonomy ) {
                    wp_set_object_terms( $inserted_post_id, $taxonomies_terms[$taxonomy], $taxonomy, false );
                }

                // add post meta
                foreach ( $data as $key => $values) {
                    error_log('[TM] ' . $key);
                    // These keys are blog relevant and shouldn't be copied
                    if( $key == '_wp_old_slug' || $key == '_edit_lock' || $key == '_edit_last') {
                        continue;
                    }
                    //These plugin specific keys have to be handled differently
                    if ($key == 'th_show_crew_info_data' || $key == 'th_show_person_info_data') {
                        //TODO make this post meta import properly
                        continue; //ATM they are ignored and added as a table (see above)
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

            // a bit extended for clarity
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
$people = get_option( 'tm_people' );
if (isset($people) && $people == 1) {
	// Filters to setup the automatic change of the publish date
	add_filter( 'wp_insert_post_data', 'update_post_date', 99, 2 );
	add_filter( 'wp_update_post_data', 'update_post_date', 99, 2 );
}