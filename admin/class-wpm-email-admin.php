<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       hqcservices.org
 * @since      1.0.0
 *
 * @package    WPM_Email
 * @subpackage WPM_Email/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPM_Email
 * @subpackage WPM_Email/admin
 * @author     Giles Wheatley <giles@hqcservices.org>
 */
class WPM_Email_Admin {

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
	public function wpm_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPM_Email_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPM_Email_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpm-email-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function wpm_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPM_Email_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPM_Email_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpm-email-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function add_plugin_admin_menu() {
		/*
		* Add a settings page for this plugin to the Settings menu.
		* NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		*        Administration Menus: http://codex.wordpress.org/Administration_Menus
		*/
		// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		add_options_page( 'WPM Email Setup', 'WPM Email', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	}

	/**
	* Add settings action link to the plugins page.
	* @since    1.0.0
	*/

	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*  adds links alongside the Deactivate link under the plugin name for activated plugins listed on the installed plugins page.
		*/
		$settings_link = array('<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>');
		return array_merge(  $settings_link, $links );
	}

	/**
	* Render the settings page for this plugin.
	* @since    1.0.0
	*/

	public function display_plugin_setup_page() {
		include_once( 'partials/wpm-email-admin-display.php' );
	}
	
	
	public function options_update() {
		// register_setting( string $option_group, string $option_name, array $args = array() )
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
		register_setting($this->plugin_name, $this->plugin_name . '-fields', array($this, 'validate_fields'));
	}

	public function validate($input) {  
		$valid = array();
		//echo "<pre>"; print_r( empty($input['email_to'] )); echo "</pre>";
		//exit();
		
		// validate email checkbox
		$valid['email_required'] = (isset($input['email_required']) && !empty($input['email_required'])) ? 1 : 0;

		// validate email addresses
		if ( empty($input['email_to']) || ($input['email_to'] !== sanitize_email($input['email_to']))) {
			$type = 'error';
			$message = __( 'You must enter a valid email address for the membership administrator.', $this->plugin_name );
			add_settings_error(
				'email_to',
				esc_attr( 'settings_updated' ),
				$message,
				$type
			);
		} else {
			$valid['email_to'] = $input['email_to'];
		}
		
		if ($input['email_cc'] !== sanitize_email($input['email_cc'])) {
			$type = 'error';
			$message = __( 'CC email address is invalid', $this->plugin_name );
			add_settings_error(
				'email_cc',
				esc_attr( 'settings_updated' ),
				$message,
				$type
			);
		} else {
			$valid['email_cc'] = $input['email_cc'];
		}
    
		return $valid;
	}

	public function validate_fields($input) {  
		$valid = array();
		//checkbox inputs for displayed wp-members fields
		$wpmembers_fields = wpmem_fields();
		foreach ($wpmembers_fields as $key => $value){		
			// Check if displayed
			if (1 == $value['register']){
				$valid[$key] = (isset($input[$key]) && !empty($input[$key])) ? 1 : 0;		
			}		
		}
		return $valid;
	}
}
