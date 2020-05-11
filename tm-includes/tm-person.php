<?php
/**
 * Person
 * @since 1.0
 * Uses database created in theatremanager.php
 */

// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;

//get WP_List_Table class
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * person_list class
 * creates and formats a list for all people types
 * @since 1.0
 */
class person_list extends WP_List_Table {

    /** Constructor */
    public function __constructor(){
        parent::__construct( array(
			'singular' => __( 'Member', 'theatre-manager' ), //singular name of the listed records
			'plural'   => __( 'Members', 'theatre-manager' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

         ) );
    }

    //utility functions
    /**
     * Retrieve person's data from the database
     * @since 1.0
     * @param int $per_page
     * @param int $page_number
     * @return mixed
     */
    public static function get_people( $per_page = 5, $page_number = 1, $search = '') {

        global $wpdb;
    
        $sql = "SELECT * FROM {$wpdb->base_prefix}tm_people";
        if (!empty($search)){
            $sql .= ' WHERE (name LIKE \'' . $search . '%\' OR email LIKE \'' . $search . '%\')';
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        
    
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }

    /**
     * Delete a person record.
     * @since 1.0
     *
     * @param int $id person ID
     */
    public static function delete_person( $id ) {
        global $wpdb;
    
        $wpdb->delete(
        "{$wpdb->base_prefix}tm_people",
        [ 'id' => $id ],
        [ '%d' ]
        );
    }

    /**
     * Returns the count of records in the database.
     * @since 1.0
     * 
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
    
        $sql = "SELECT COUNT(*) FROM {$wpdb->base_prefix}tm_people";
    
        return $wpdb->get_var( $sql );
    }

    //override functions
    /** Text displayed when no person data is available */
    public function no_items() {
        _e( 'No Members found.', 'theatre-manager' );
    } 

    /**
     * Method for name column
     * @since 1.0
     * @param array $item an array of DB data
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'tm_delete_person' );
    
        $title = sprintf('<strong><a href="?page=tm_members_edit&action=%s&person=%s">%s</a></strong>','edit', absint( $item['id'] ), esc_attr($item['name']));
    
        $actions = [
            'edit'   => sprintf('<a href="?page=tm_members_edit&action=%s&person=%s">Edit</a>','edit', absint( $item['id'] )),
            'delete' => sprintf('<a href="?page=%s&action=%s&person=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];
    
        return $title . $this->row_actions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
     * @since 1.0
     * @param array $item
     * @param string $column_name
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
        case 'email':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     * @since 1.0
     * @param array $item
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }
    /**
     * Associative array of columns
     * @since 1.0
     * @return array
     */
    function get_columns() {
        $columns = array(
        'cb'            => '<input type="checkbox" />',
        'name'   => __( 'Name', 'theatre-manager' ),
        'email'  => __( 'Email', 'theatre-manager' ),
        );
    
        return $columns;
    }
    /**
     * Columns to make sortable.
     * @since 1.0
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
        'name' => array( 'name', true ),
        );
    
        return $sortable_columns;
    }
    
    /**
     * Returns an associative array containing the bulk action
     * @since 1.0
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
        'bulk-delete' => 'Delete'
        ];
    
        return $actions;
    }

    //important methods
    /**
     * Handles data query and filter, sorting, and pagination.
     * @since 1.0
     */
    public function prepare_items($search='') {

        $this->_column_headers = $this->get_column_info();
        $search = sanitize_text_field($search);
    
        /** Process bulk action */
        $this->process_bulk_action();
    
        $per_page     = $this->get_items_per_page( 'people_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
    
        $this->set_pagination_args( [
        'total_items' => $total_items, //WE have to calculate the total number of items
        'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );
    
    
        $this->items = self::get_people( $per_page, $current_page, $search);
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
      
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
        
            if ( ! wp_verify_nonce( $nonce, 'tm_delete_person' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_person( absint( $_GET['person'] ) );
        
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }
      
        }
      
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
      
            $delete_ids = esc_sql( $_POST['bulk-delete'] );
            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_person( $id );
      
            }
      
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }
}

/**
 * Class TM_Person
 * gets data for person class
 * adds an edit page
 */
class TM_Person {
    // class instance
	static $instance;

	// person WP_List_Table object
	public $person_obj;

    /** 
     * class constructor
     * @since 1.0
     */
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    }
    
    public static function set_screen( $status, $option, $value ) {
        return $value;
    }
    
    /**
     * Create menu items
     * @since 1.0
     */
    public function plugin_menu() {
    
        $hook = add_menu_page(
            'Theatre Members',                  //title
            'Members',                          //menu name
            'manage_options',                   //capability
            'tm_members',                       //menu slug
            [ $this, 'member_info_page' ],  //function
            'dashicons-admin-users',            //icon
            58,                                 //position
        );
    
        add_action( "load-$hook", [ $this, 'screen_option' ] );

        add_submenu_page(
            'tm_members',                       //parent slug
            __('Add New', 'theatre-manager'),   //title
            __('Add New', 'theatre-manager'),   //menu name
            'manage_options',                   //capability
            'tm_members_edit',                  //menu slug
            [ $this, 'member_edit_page' ],  //function
        );
    
    }

    /** 
     * Singleton instance 
     * @since 1.0
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
    * Screen options
    * @since 1.0
    */
    public function screen_option() {

        $option = 'per_page';
        $args   = [
            'label'   => 'Members',
            'default' => 5,
            'option'  => 'people_per_page'
        ];

        add_screen_option( $option, $args );

        $this->person_obj = new person_list();
    }

    /**
    * Member Info page
    * @since 1.0
    */
    public function member_info_page() {
        //get search box data if present
        if(isset($_POST['s'])){
            $this->person_obj->prepare_items($_POST['s']);
        } else {
            $this->person_obj->prepare_items();
        }
        $message = '';
        if ('delete' === $this->person_obj->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'theatre-manager'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
        <div class="wrap">
            <h2>Members <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=tm_members_edit');?>"><?php _e('Add New', 'theatre-manager')?></a>
            </h2>
            <?php echo $message; ?>
            <div id="poststuff">
                <!--<div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">-->
                            <form method="post">
                                <?php
                                
                                //add search box
                                $this->person_obj->search_box("Search", 'search');
                                //display the table
                                $this->person_obj->display(); ?>
                            </form>
                <br class="clear">
            </div>
        </div>
    <?php
    }

    /** 
     * Edit member page
     * @since 1.0
     */
    public function member_edit_page(){
        //setup
        global $wpdb;
        $table_name = $wpdb->base_prefix . 'tm_people';

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id'    => 0,
            'name'  => '',
            'bio'   => '',
            'email' => '',
            'course'=> '',
        );

        function tm_person_validate($item){
            $messages = array();
            if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
            if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
            
            if (empty($messages)) return true;
            return implode('<br />', $messages);
        }

        //----------------------------------------------------------------------------------------------------------------
        //  Saving
        function tm_save_person_info() {
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
            //return data
            return $new;
        }
        // Verify Noncee
        if (isset( $_POST['tm_person_nonce'] ) && wp_verify_nonce( $_POST['tm_person_nonce'], basename( __FILE__ ) ) ){
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            $item['course'] = serialize(tm_save_person_info());
            error_log(print_r($item));
            // validate data, and if all ok save item to database
            $item_valid = tm_person_validate($item);
            if ($item_valid === true) {
                // if id is zero insert otherwise update
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Member saved', 'theatre-manager');
                    } else {
                        $notice = __('There was an error while saving item', 'theatre-manager');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Member updated', 'theatre-manager');
                    } else {
                        $notice = __('There was an error while updating item', 'theatre-manager');
                    }
                }
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
        } else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['person'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['person']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'theatre-manager');
                }
            }
        }

        //----------------------------------------------------------------------------------------------------------------
        //  Meta Boxes
        add_meta_box(
            'theatre-manager-person-info', //ID
            'Your Information', //Title TODO: Internationalisation
            'tm_person_info_handler', //callback function
            'tm_person', //post type
            'normal', //on-page location
            'default' //priority
        );

        //HTML representation of the box
        function tm_person_info_handler($post){
            include plugin_dir_path( __FILE__ ) . 'forms/person-info-form.php';
        }
        //save metabox
        
        //----------------------------------------------------------------------------------------------------------------
        //  Main form 
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function( $ ){
                $( '#name' ).on('input', function() {
                    "" !== this.value ? $( '#title-prompt-text' ).addClass("screen-reader-text") : $( '#title-prompt-text' ).removeClass("screen-reader-text")
                });
                var text = $( '#name' ).val();
                if (text!=""){
                    $( '#title-prompt-text' ).addClass("screen-reader-text")
                }
            });
        </script>
        <div class="wrap">
            <?php 
            if (isset($_GET['person'])) { ?>
                <h2><?php _e('Edit Member', 'custom_table_example')?></h2>
            <?php } else { ?>
                <h2><?php _e('Add new Member', 'custom_table_example')?></h2>
            <?php } ?>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form id="form" method="POST">
                            <?php wp_nonce_field( basename( __FILE__ ), 'tm_person_nonce' );?>
                            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
                            <div id="titlediv">
                                <div id="titlewrap">
                                    <label id="title-prompt-text" class="" for="name">Member Name</label>
                                    <input type="text" name="name" id="name" size="30" spellcheck="true" autocomplete="off" value="<?php echo esc_attr($item['name']);?>" style="width: 100%; height: 40px; font-size: 1.7em;"/>
                                </div>
                            </div>
                            <br>
                            <input type="submit" value="<?php _e('Save', 'theatre-manager')?>" id="submit" class="button-primary" name="submit">
                            <?php
                                $bio_content = wpautop($item['bio']);
                                wp_editor($bio_content, 'bio', array(
                                    'wpautop'           => true,
                                    'media_buttons'     => false,
                                    'textarea_name'     => 'bio',
                                    'textarea_rows'     => 10,
                                    'teeny'             => false
                                ));
                            ?>	
                            <div class="metabox-holder" id="poststuff">
                                <div id="post-body">
                                    <div id="post-body-content">
                                        <?php do_meta_boxes('tm_person', 'normal', $item); ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="<?php _e('Save', 'theatre-manager')?>" id="submit" class="button-primary" name="submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

add_action( 'wp_before_admin_bar_render', 'tm_add_person_item' );

function tm_add_person_item(){
    global $wp_admin_bar;
    //Get a reference to the new-content node to modify.
    $new_content_node = $wp_admin_bar->get_node('new-content');
    //Change href
    $new_content_node->href = '#';
    //Update Node.
    $wp_admin_bar->add_node($new_content_node);

    //Remove add-user and add again in a bit.
    $wp_admin_bar->remove_menu('new-user');

    // Adding a new custom menu item that did not previously exist.
    $wp_admin_bar->add_menu( array(
        'id'    => 'new-member',
        'title' => 'Member',
        'parent'=> 'new-content',
        'href'  => get_site_url() . '/wp-admin/admin.php?page=tm_members_edit',)
    );

    $wp_admin_bar->add_menu( array(
        'id'    => 'new-user',
        'title' => 'User',
        'parent'=> 'new-content',
        'href'  => get_site_url() . '/wp-admin/user-new.php',)
    );

}

//create instance of TM_Person
add_action( 'plugins_loaded', function () {
    TM_Person::get_instance();
} );

//----------------------------------------------------------------------------------------------------------
//Global utility functions
function get_person_info($person_id){
    global $wpdb;
    $returns = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}tm_people WHERE id=$person_id", ARRAY_A);
    return $returns;
}

function get_person_ids(){
    global $wpdb;
    $column = $wpdb->get_col("SELECT id FROM {$wpdb->base_prefix}tm_people");
    return $column;
}

function get_person_name($person_id){
    global $wpdb;
    $name = $wpdb->get_col("SELECT name FROM {$wpdb->base_prefix}tm_people WHERE id = $person_id");
    return $name[0];
}

/**
 * Returns $column of person's data
 * @param $person_id
 * @param $column - column to fetch
 * @param $serialized - whether the column should be returned as an unserialised array
 * @since 1.1
 * @see get_person_info() to get all data
 * @see get_person_name() to get name
 */
function get_person_data($person_id, $column, $serialized = false){
    if (!is_numeric($person_id)){
        die ("person_id should be numeric, '" . $person_id . "' given");
    }
    global $wpdb;
    $data = $wpdb->get_col("SELECT $column FROM {$wpdb->base_prefix}tm_people WHERE id = $person_id");
    if ($serialized){
        return unserialize($data[0]);
    } else {
        return $data[0];
    }
}

/**
 * Sets data for given person id and column
 * @param $person_id
 * @param $column
 * @param $data - unserialized
 * @param $serialized - whether to serialize the data
 * @since 1.1
 */
function set_person_data($person_id, $column, $data, $serialized = false){
    error_log($person_id);
    if (!is_numeric($person_id)){
        wp_die ("person_id should be numeric, '" . $person_id . "' given");
    }
    global $wpdb;
    if ($serialized){
        $data = serialize($data);
    }
    $value = array($column => $data);
    $returns = $wpdb->update(
        "{$wpdb->base_prefix}tm_people",    //table
        $value,                             //data
        array (                             //where
            'id' => $person_id
        ),
    );
    error_log($returns);
}

//add custom archive
add_action('init', 'tm_rewrite_init');
function tm_rewrite_init(){
    add_rewrite_rule(
        'properties/([0-9]+)/?$',
        //'index.php?pagename=properties&property_id=$matches[1]',
        'wp-admin/admin.php?page=tm_members',
        'top' );
}
add_filter( 'query_vars', 'tm_query_vars' );
function tm_query_vars( $query_vars ){
    $query_vars[] = 'property_id';
    return $query_vars;
}