<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       hqcservices.org
 * @since      1.0.0
 *
 * @package    WPM_Email
 * @subpackage WPM_Email/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WPM_Email
 * @subpackage WPM_Email/public
 * @author     Giles Wheatley <giles@hqcservices.org>
 */
class WPM_Email_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpm-email-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpm-email-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function wpm_profile_update($new_user_data) {
		// get current settings for this plugin
		$options = get_option( $this->plugin_name );
		$options_fields = get_option( $this->plugin_name . '-fields' );
		
		// Check if email required and we have an email address
		if ($options['email_required'] && !empty($options['email_to'])){
			
			// Create array of fields to be reported on
			$fields = [];
			foreach ($options_fields as $key => $value) {
				if ($value){
					$fields[] .= $key;
				}
			}
			
			// Check if any fields to be reported on - if not, do not send an email
			if (!empty($fields)) {

				// Get current user data for comparison		
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				$user_login = $current_user->user_login;
				$user_email = $current_user->user_email;

				// Build message string
				$message_content = "";
				
				// go through the updated profile fields
				foreach( $new_user_data as $key=>$val ) {
					
					// check field is to be reported on
					if (in_array($key, $fields, true)) {

						$old_val = get_user_meta( $user_id, $key, true );
						
						// Check field value has changed
						if($val != $old_val && $key != 'user_email') {
							$message_content .= "<tr><td>" . $key . "</td><td>" . $old_val . "</td><td>" . $val . "</td></tr>\r\n";
						}
						// user_email is not in meta data so we need to check it here
						if ($key == 'user_email' && $val != $user_email) {
							$message_content .= "<tr><td>" . $key . "</td><td>" . $user_email . "</td><td>" . $val . "</td></tr>\r\n";
						}
					}
				}
						
				// Check if that there are fields that have changed
				if (strlen($message_content) > 0) {
					
					//build email headers and message
					$to = $options['email_to'];
					$cc = $options['email_cc'];
					$subject = get_bloginfo('name') . ' ' . __( 'Member Update:', $this->plugin_name ) . ' ' .$user_login;		

					$css = "<style>
						table, th, td {
							border: 1px solid black;
							border-collapse: collapse;
							text-align: left;
							padding: .2em;
						}
						tr {
							vertical-align:top;
						}
					</style>\r\n";
					$message = "<html>\r\n<head>\r\n" . $css . "<title>HTML email</title>\r\n</head>\r\n<body>\r\n";
					$message .= "<p>" . __( 'This email was generated by', $this->plugin_name ) . " " . get_bloginfo('wpurl') . " " . __( 'and sent to you for Membership Administration', $this->plugin_name ) . ".</p>";					
					$message .=  "<p>" . $new_user_data['first_name'] . " " . $new_user_data['last_name'] . " " . __( 'has updated profile data.', $this->plugin_name ) . "</p>\r\n";
					$message .= "<table>\r\n<tr><th>".  __( 'Field', $this->plugin_name ) . "</th><th style='text-align:left'>" . __( 'Old value', $this->plugin_name ) . "</th><th>" . __( 'New value', $this->plugin_name ) . "</th></tr>\r\n";
					$message .= $message_content . "</table>";
					


					$message .= "<p>" . __( 'Please contact the website administrator', $this->plugin_name ) . " (email:<a href='mailto:" . get_bloginfo('admin_email') . "'>" . get_bloginfo('admin_email') . "</a>)" . __( 'if you no longer wish to receive these emails.', $this->plugin_name ) . "</p>";
					$message .= "</body>\r\n</html>";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "<br>\r\n";
					$headers .= "Cc: " . $cc . "\r\n";

					//send email
					wp_mail ( $to, $subject, $message, $headers );
				}
			}  // end check if any fields to report on
		} // end check email required
	} // end wpm_profile_update function
} // end WPM_Email_Public class

