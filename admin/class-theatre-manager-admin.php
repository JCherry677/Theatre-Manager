<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       johncherry.me
 * @since      1.0.0
 *
 * @package    Theatre_Manager
 * @subpackage Theatre_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Theatre_Manager
 * @subpackage Theatre_Manager/admin
 * @author     John Cherry <wordpress@johncherry.me>
 */
class Theatre_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Theatre_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Theatre_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/theatre-manager-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Theatre_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Theatre_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/theatre-manager-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_admin_menu(){
		add_submenu_page( 'options-general.php', 'Theatre Manager', 'Theatre Manager', 'manage_options', 'theatre_manager', 'tm_options_page' );
	}

	public function setting_init(){
		register_setting( 'pluginPage', 'tm_settings' );
		//Main Section
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

	//committees
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

		function tm_archive_render(  ) {
			//multisite render
			if (is_multisite()){
				$options = get_option( 'tm_settings' );
				?>
				<input type='checkbox' name='tm_settings[tm_archive]' <?php if (isset($options['tm_archive'])) checked( $options['tm_archive'], 1 ); ?> value='1'>
				<?php
			}
		}

	//block editor
	function tm_block_member_render(  ) {
		$options = get_option( 'tm_settings' );
		?>
		<input type='checkbox' name='tm_settings[tm_block_person]' <?php if (isset($options['tm_block_person'])) checked( $options['tm_block_person'], 1 ); ?> value='1'>
		<?php
	}
	function tm_block_show_render(  ) {
		$options = get_option( 'tm_settings' );
		?>
		<input type='checkbox' name='tm_settings[tm_block_show]' <?php if (isset($options['tm_block_show'])) checked( $options['tm_block_show'], 1 ); ?> value='1'>
		<?php
	}
	function tm_block_committee_render(  ) {
		$options = get_option( 'tm_settings' );
		?>
		<input type='checkbox' name='tm_settings[tm_block_committee]' <?php if (isset($options['tm_block_committee'])) checked( $options['tm_block_committee'], 1 ); ?> value='1'>
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
		echo __( '<p>If Members are enabled for committees, members must be registered before they can be added to a committee</p>', 'theatre-manager');
	}
	function tm_archive_section_callback(  ) {
		echo __( '<p>In a multisite setup, this allows you to export shows to a second "archive" site</p>', 'theatre-manager' );

		if (is_multisite()){
			echo __( '<p>This should be enabled on the main site and NOT the archive site</p>', 'theatre-manager' );
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
}
