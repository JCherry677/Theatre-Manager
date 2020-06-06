<?php
//------------------------------------------------------------------------------------------
/**
 * Options Page
 * @since 0.7
 */
add_action( 'admin_menu', 'tm_add_admin_menu' );
add_action( 'admin_init', 'tm_settings_init' );

//create menu
function tm_add_admin_menu(  ) { 
	add_submenu_page( 'options-general.php', 'Theatre Manager', 'Theatre Manager', 'manage_options', 'theatre_manager', 'tm_options_page' );
}

//create options
function tm_settings_init(  ) { 
	register_setting( 'pluginPage', 'tm_settings' );
	add_settings_section(
		'tm_pluginPage_section', 
		__( 'Manager Features', 'theatre-manager' ), 
		'tm_settings_section_callback', 
		'pluginPage'
	);
	add_settings_field( 
		'tm_members', 
		__( 'Use Members in shows', 'theatre-manager' ), 
		'tm_people_render', 
		'pluginPage', 
		'tm_pluginPage_section' 
	);
	add_settings_field( 
		'tm_show_warnings', 
		__( 'Use Show Content Warnings', 'theatre-manager' ), 
		'tm_show_warnings_render', 
		'pluginPage', 
		'tm_pluginPage_section' 
	);
	add_settings_field( 
		'tm_committee_option', 
		__( 'Use Committees', 'theatre-manager' ), 
		'tm_committee_render', 
		'pluginPage', 
		'tm_pluginPage_section' 
	);
	add_settings_field( 
		'tm_email_option', 
		__( 'Use Email for Members (For Committee member emails)', 'theatre-manager' ), 
		'tm_email_render', 
		'pluginPage', 
		'tm_pluginPage_section' 
	);
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
function tm_people_render(  ) { 
	$options = get_option( 'tm_settings' );
	?>
	<input type='checkbox' name='tm_settings[tm_people]' <?php if (isset($options['tm_people'])) checked( $options['tm_people'], 1 ); ?> value='1'>
	<?php
}
function tm_show_warnings_render(  ) { 
	$options = get_option( 'tm_settings' );
	?>
	<input type='checkbox' name='tm_settings[tm_show_warnings]' <?php if (isset($options['tm_show_warnings'])) checked( $options['tm_show_warnings'], 1 ); ?> value='1'>
	<?php
}
function tm_committee_render(  ) { 
	$options = get_option( 'tm_settings' );
	?>
	<input type='checkbox' name='tm_settings[tm_committees]' <?php if (isset($options['tm_committees'])) checked( $options['tm_committees'], 1 ); ?> value='1'>
	<?php
}
function tm_email_render(  ) { 
	$options = get_option( 'tm_settings' );
	?>
	<input type='checkbox' name='tm_settings[tm_person_email]' <?php if (isset($options['tm_person_email'])) checked( $options['tm_person_email'], 1 ); ?> value='1'>
	<?php
}
function tm_settings_section_callback(  ) { 
	echo __( '<p>Enable Features of the plugin</p>', 'theatre-manager' );
	echo __( '<p>If Members are enabled, members must be registered before they can be added to shows</p>', 'theatre-manager');
}
function tm_archive_section_callback(  ) { 
	echo __( '<p>In a multisite setup, this allows you to export shows to a second "archive" site</p>', 'theatre-manager' );
	echo __( '<p>This should be enabled on the main site and NOT the archive site</p>', 'theatre-manager' );
}
if (is_multisite()){
	function tm_archive_render(  ) { 
		$options = get_option( 'tm_settings' );
		?>
		<input type='checkbox' name='tm_settings[tm_archive]' <?php if (isset($options['tm_archive'])) checked( $options['tm_archive'], 1 ); ?> value='1'>
		<?php
	}
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