<?php
/**
 * Custom Type - show warning
 * @since 0.7
 */

function tm_warning_type(){
    $labels = array(
        'name'               => __( 'Content Warnings', 'post type general name' ),
        'singular_name'      => __( 'Content Warning', 'post type singular name' ),
        'add_new'            => __( 'Add New', 'Warning' ),
        'add_new_item'       => __( 'Add New Content Warning' ),
        'edit_item'          => __( 'Edit Content Warning' ),
        'new_item'           => __( 'New Content Warning' ),
        'all_items'          => __( 'Content Warnings' ),
        'view_item'          => __( 'View Content Warning' ),
        'search_items'       => __( 'Search Content Warnings' ),
        'not_found'          => __( 'No Content Warnings found' ),
        'not_found_in_trash' => __( 'No Content Warnings found in the Trash' ), 
        'parent_item_colon'  => '’',
        'menu_name'          => 'Committee',
    );

    $args = array(
        'labels' => $labels,
        'description'   => 'Content Warnings for shows',
        'public'        => true,
        'supports'      => array( 'title', 'editor'),
        'rewrite'       => array('slug' => 'warning'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
        'show_in_menu'  => 'edit.php?post_type=theatre_show'
    );

    register_post_type('theatre_warning', $args);
}

//------------------------------------------------------------------------------------------
/**
 * warning update messages.
 * @since 0.7
 * 
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function tm_warning_messages( $messages ) {
    global $post, $post_ID;
    $messages['theatre_warning'] = array(
      0 => '’', 
      1 => sprintf( __('Show warning updated. <a href="%s">View show warning</a>'), esc_url( get_permalink($post_ID) ) ),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => __('Show warning updated.'),
      5 => isset($_GET['revision']) ? sprintf( __('Show warning restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
      6 => sprintf( __('Show warning published. <a href="%s">View show warning</a>'), esc_url( get_permalink($post_ID) ) ),
      7 => __('Show warning saved.'),
      8 => sprintf( __('Show warning submitted. <a target="_blank" href="%s">Preview show warning</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
      9 => sprintf( __('Show warning scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview show warning</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
      10 => sprintf( __('Show warning draft updated. <a target="_blank" href="%s">Preview show warning</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}

//------------------------------------------------------------------------------------------
/**
 * post columns
 * @since 0.7
 */

function tm_editor_warning_columns($columns){
    unset( $columns['date'] );
    return $columns;
}

//------------------------------------------------------------------------------------------
/**
 * Change title text
 * @since 0.7
 */

function tm_warning_enter_title( $input ) {
    if ( 'theatre_warning' === get_post_type() ) {
        return __( 'Content Warning Name', 'your_textdomain' );
    }

    return $input;
}

//all calls based on whether option enabled
$options = get_option( 'tm_settings' );
if (isset($options['tm_show_warnings']) && $options['tm_show_warnings'] == 1){
    add_action('init', 'tm_warning_type');
    add_filter( 'post_updated_messages', 'tm_warning_messages' );
    add_filter( 'manage_theatre_warning_posts_columns', 'tm_editor_warning_columns' );
    add_filter( 'enter_title_here', 'tm_warning_enter_title' );
}