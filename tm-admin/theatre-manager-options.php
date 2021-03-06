<?php
//------------------------------------------------------------------------------------------
/**
 * Options Page
 * @since 0.7
 */
/*
 * 0.9.1 removed tm_committee_people as code was wrong elsewhere
 */
add_action( 'admin_menu', 'tm_add_admin_menu' );
add_action( 'admin_init', 'tm_settings_init' );

//create menu
function tm_add_admin_menu(  ) { 
	add_submenu_page( 'options-general.php', 'Theatre Manager', 'Theatre Manager', 'manage_options', 'theatre_manager', 'tm_options_page' );
}

//create options
function tm_settings_init(  ) {
    register_setting( 'pluginPage', 'tm_people');
    register_setting( 'pluginPage', 'tm_show_warnings');
    register_setting( 'pluginPage', 'tm_show_reviews');
    register_setting( 'pluginPage', 'tm_committees');
    register_setting( 'pluginPage', 'tm_person_email');
    register_setting( 'pluginPage', 'tm_archive');
    register_setting( 'pluginPage', 'tm_block_person');
    register_setting( 'pluginPage', 'tm_block_show');
    register_setting( 'pluginPage', 'tm_block_committee');
	//Show Section
	add_settings_section(
		'tm_show_section',
		__( 'Show Features', 'theatre-manager' ),
		'tm_show_section_callback',
		'pluginPage'
	);
	add_settings_field( 
		'tm_people', 
		__( 'Use Members in shows', 'theatre-manager' ), 
		'tm_people_render', 
		'pluginPage', 
		'tm_show_section'
	);
	add_settings_field(
		'tm_show_warnings',
		__( 'Use Show Content Warnings', 'theatre-manager' ),
		'tm_show_warnings_render',
		'pluginPage',
		'tm_show_section'
	);
    add_settings_field(
        'tm_show_reviews',
        __( 'Use Show Reviews', 'theatre-manager' ),
        'tm_show_reviews_render',
        'pluginPage',
        'tm_show_section'
    );

	//Committee section
	add_settings_section(
		'tm_committee_section',
		__( 'Committee Features', 'theatre-manager' ),
		'tm_committee_section_callback',
		'pluginPage'
	);
	add_settings_field(
		'tm_committee_option',
		__( 'Use Committees', 'theatre-manager' ),
		'tm_committee_render',
		'pluginPage',
		'tm_committee_section'
	);
	add_settings_field(
		'tm_email_option',
		__( 'Use Email for Members (For Committee member emails)', 'theatre-manager' ),
		'tm_email_render',
		'pluginPage',
		'tm_committee_section'
	);

	//Block editor section
    add_settings_section(
        'tm_block_section',
        __( 'Block Editor', 'theatre-manager'),
        'tm_block_section_callback',
        'pluginPage'
    );
    add_settings_field(
        'tm_block_person',
        __('Block Editor for Members', 'theatre-manager' ),
        'tm_block_member_render',
        'pluginPage',
        'tm_block_section'
    );
	add_settings_field(
		'tm_block_show',
		__('Block Editor for Shows', 'theatre-manager' ),
		'tm_block_show_render',
		'pluginPage',
		'tm_block_section'
	);
	add_settings_field(
		'tm_block_committee',
		__('Block Editor for Committees', 'theatre-manager' ),
		'tm_block_committee_render',
		'pluginPage',
		'tm_block_section'
	);


	//Multisite Archiving section
	add_settings_section(
		'tm_pluginPage_section_archive', 
		__( 'Archive Settings', 'theatre-manager' ), 
		'tm_archive_section_callback', 
		'pluginPage'
	);
	if (is_multisite()){
		add_settings_field( 
			'tm_show_archive', 
			__( 'Use Show Archive', 'theatre-manager' ), 
			'tm_archive_render', 
			'pluginPage', 
			'tm_pluginPage_section_archive' 
		);
	}
	
}

//option render
//shows
function tm_people_render(  ) { 
	$option = get_option( 'tm_people' );
	?>
	<input type='checkbox' name='tm_people' <?php if (isset($option)) checked( $option, 1 ); ?> value='1'>
	<?php
}
function tm_show_warnings_render(  ) { 
	$option = get_option( 'tm_show_warnings' );
	?>
	<input type='checkbox' name='tm_show_warnings' <?php if (isset($option)) checked( $option, 1 ); ?> value='1'>
	<?php
}
function tm_show_reviews_render(  ) {
    $option = get_option( 'tm_show_reviews' );
    ?>
    <input type='checkbox' name='tm_show_reviews' <?php if (isset($option)) checked( $option, 1 ); ?> value='1'>
    <?php
}

//committees
function tm_committee_render(  ) { 
	$option = get_option( 'tm_committees' );
	?>
	<input type='checkbox' name='tm_committees' <?php if (isset($option)) checked( $option, 1 ); ?> value='1'>
	<?php
}
function tm_email_render(  ) { 
	$option = get_option( 'tm_person_email' );
	?>
	<input type='checkbox' name='tm_person_email' <?php if (isset($option)) checked( $option, 1 ); ?> value='1'>
	<?php
}

//multisite render
if (is_multisite()){
	function tm_archive_render(  ) {
		$options = get_option( 'tm_archive' );
		?>
        <input type='checkbox' name='tm_archive' <?php if (isset($options)) checked( $options, 1 ); ?> value='1'>
		<?php
	}
}

//block editor
function tm_block_member_render(  ) {
	$options = get_option( 'tm_block_person' );
	?>
    <input type='checkbox' name='tm_block_person' <?php if (isset($options)) checked( $options, 1 ); ?> value='1'>
	<?php
}
function tm_block_show_render(  ) {
	$options = get_option( 'tm_block_show' );
	?>
    <input type='checkbox' name='tm_block_show' <?php if (isset($options)) checked( $options, 1 ); ?> value='1'>
	<?php
}
function tm_block_committee_render(  ) {
	$options = get_option( 'tm_block_committee' );
	?>
    <input type='checkbox' name='tm_block_committee' <?php if (isset($options)) checked( $options, 1 ); ?> value='1'>
	<?php
}

//sections callbacks
function tm_show_section_callback(  ) {
	echo __( '<p>Enable Show Features of the plugin</p>', 'theatre-manager' );
	echo __( '<p>Shows cannot be disabled</p>', 'theatre-manager' );
	echo __( '<p>If Members are enabled for shows, members must be registered before they can be added to a show</p>', 'theatre-manager');
}
function tm_committee_section_callback(  ) {
	echo __( '<p>Enable Committee Features of the plugin</p>', 'theatre-manager' );
	echo __( '<p>Members must be registered before they can be added to a committee</p>', 'theatre-manager');
}
function tm_archive_section_callback(  ) { 
	echo __( '<p>In a multisite setup, this allows you to copy shows to other sites on the network</p>', 'theatre-manager' );

	if (is_multisite()){
		echo __( '<p>Generally, this should be enabled on the main site and NOT the archive site</p>', 'theatre-manager' );
	} else {
		echo __( '<p>Your wordpress installation is not multisite-ready, and so cannot use this feature.</p>', 'theatre-manager' );
	}
}
function tm_block_section_callback(  ) {
	echo __( '<p>Use the block editor for the plugin\'s post types</p>', 'theatre-manager' );
}

//create page
function tm_options_page(  ) { ?>
    <form action='options.php' method='post'>

        <h1>Theatre Manager</h1>
        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>
    <?php

}