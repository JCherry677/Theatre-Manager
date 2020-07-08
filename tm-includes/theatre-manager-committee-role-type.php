<?php
// If called directly, abort
if ( ! defined( 'ABSPATH' )) die;

//load file if committee enabled
$options = get_option( 'tm_settings' );
if (isset($options['tm_committees']) && $options['tm_committees'] == 1){
    /**
     * Custom Type - Committee Role
     * @since 0.7
     */

    function tm_role_type(){
        $labels = array(
            'name'               => __( 'Committee roles', 'post type general name' ),
            'singular_name'      => __( 'Committee role', 'post type singular name' ),
            'add_new'            => __( 'Add New', 'Committee role' ),
            'add_new_item'       => __( 'Add New Committee role' ),
            'edit_item'          => __( 'Edit Committee role' ),
            'new_item'           => __( 'New Committee role' ),
            'all_items'          => __( 'Committee roles' ),
            'view_item'          => __( 'View Committee role' ),
            'search_items'       => __( 'Search Committee roles' ),
            'not_found'          => __( 'No Committee roles found' ),
            'not_found_in_trash' => __( 'No Committee roles found in the Trash' ), 
            'parent_item_colon'  => '’',
            'menu_name'          => 'Committee role',
        );

        $args = array(
            'labels' => $labels,
            'description'   => 'Committee roles',
            'public'        => true,
            'supports'      => array( 'title', 'editor'),
            'rewrite'       => array('slug' => 'role'),
            'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
            'has_archive'   => true,
            'show_in_menu'  => 'edit.php?post_type=theatre_committee'
        );

        register_post_type('theatre_role', $args);
    }

    //------------------------------------------------------------------------------------------
    /**
     * warning update messages.
     * @since 0.7
     * 
     * @param array $messages Existing post update messages.
     * @return array Amended post update messages with new CPT update messages.
     */
    function tm_role_messages( $messages ) {
        global $post, $post_ID;
        $messages['theatre_role'] = array(
        0 => '’', 
        1 => sprintf( __('Committee role updated. <a href="%s">View committee role</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Committee role updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Committee role restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Committee role published. <a href="%s">View committee role</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Committee role saved.'),
        8 => sprintf( __('Committee role submitted. <a target="_blank" href="%s">Preview committee role</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Committee role scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview committee role</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Committee role draft updated. <a target="_blank" href="%s">Preview committee role</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        );
        return $messages;
    }

    //------------------------------------------------------------------------------------------
    /**
     * post columns
     * @since 0.7
     */

    function tm_editor_role_columns($columns){
        unset( $columns['date'] );
        return $columns;
    }

    //------------------------------------------------------------------------------------------
    /**
     * Change title text
     * @since 0.7
     */

    function tm_role_enter_title( $input ) {
        if ( 'theatre_role' === get_post_type() ) {
            return __( 'Committee Role Name', 'your_textdomain' );
        }

        return $input;
    }

    //-----------------------------------------------------------------------------------------
        /**
         * Committee Role Shortcode
         * Returns show data
         * @since 0.5
         */
        function tm_committee_role_shortcode() {
	        $options = get_option( 'tm_settings' );
            $people = get_post_meta(get_the_ID(), 'th_committee_role_data', true);
            //basic data
            $committeestext = "<h3>Role History</h3>";
            $committeestext .= "<table><thead><td><h6>Committee Year</h6></td><td><h6>Member</h6></td></thead><tbody>";
            foreach ( $people as $year => $person ) {
                $committeestext .= "<tr><td><a href=\"" . get_post_permalink($year)."\">" . get_the_title($year) . "</a></td>";
                $committeestext .= "<td><table>";
                foreach ( $person as $item ) {
	                $committeestext .= "<tr></tr><td><a href=\"" . get_post_permalink( $item ) . "\">" . get_the_title( $item ) . "</a></td></tr>";
                }
                $committeestext .= "</table></td></tr>";
            }
            $committeestext .= "</tbody></table>";
            //return all
            return $committeestext;
        }
        

    //all calls based on whether option enabled
    $options = get_option( 'tm_settings' );
    if (isset($options['tm_committees']) && $options['tm_committees'] == 1){
        add_action('init', 'tm_role_type');
        add_filter( 'post_updated_messages', 'tm_role_messages' );
        add_filter( 'manage_theatre_role_posts_columns', 'tm_editor_role_columns' );
        add_filter( 'enter_title_here', 'tm_role_enter_title' );
        add_shortcode( 'role_data', 'tm_committee_role_shortcode' );
    }
} 