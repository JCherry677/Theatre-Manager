<?php
/**
 * Custom Type - person
 * @since 0.2
 */

//create show type
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
        'supports'      => array( 'title', 'editor', 'excerpt', 'comments', 'revisions' ),
        'rewrite'       => array('slug' => 'members'),
        'show_in_rest'  => false, //true => Gutenberg editor, false => old editor
        'has_archive'   => true,
    );

    register_post_type('theatre_member', $args);
}
add_action('init', 'theatre_history_person_type');