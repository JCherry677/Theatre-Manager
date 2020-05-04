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
		__( 'Show Settings', 'theatre-manager' ), 
		'tm_settings_section_callback', 
		'pluginPage'
    );
	add_settings_field( 
		'tm_show_warnings', 
		__( 'Use Show Content Warnings', 'theatre-manager' ), 
		'tm_show_warnings_render', 
		'pluginPage', 
		'tm_pluginPage_section' 
    );
}

//option render
function tm_show_warnings_render(  ) { 
	$options = get_option( 'tm_settings' );
	?>
	<input type='checkbox' name='tm_settings[tm_show_warnings]' <?php if (isset($options['tm_show_warnings'])) checked( $options['tm_show_warnings'], 1 ); ?> value='1'>
	<?php
}
function tm_settings_section_callback(  ) { 
	echo __( 'Theatre Show Settings', 'theatre-manager' );
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