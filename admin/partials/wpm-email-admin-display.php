<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       hqcservices.org
 * @since      1.0.0
 *
 * @package    WPM_Email
 * @subpackage WPM_Email/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

	<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
	<h4>
		<?php _e('Tick the box and enter your email address to receive emails when members update their profile data.', $this->plugin_name); ?>
	</h4>
	<form method="post" name="wpm_email_options" action="options.php">

		<?php
			//Grab all options
			$options = get_option($this->plugin_name);
			$email_required = $options['email_required'];
			// if invalid email entered there will be no option set - also on first run there will be no $options
			if ($options && array_key_exists('email_to', $options)){
				$email_to = $options['email_to'];
			}
			if ($options && array_key_exists('email_cc', $options)){
				$email_cc = $options['email_cc'];
			}
			$options_fields = get_option($this->plugin_name . '-fields');
			// field options checked within foreach loop
		?>

		<?php
			settings_fields($this->plugin_name);
			do_settings_sections($this->plugin_name);
		?>
		
		<!-- Email required ? -->
		<fieldset>
			<legend class="screen-reader-text"><span>Email required</span></legend>
			<label for="<?php echo $this->plugin_name; ?>-email_required">
				<input type="checkbox" id="<?php echo $this->plugin_name; ?>-email_required" name="<?php echo $this->plugin_name; ?>[email_required]" value="1" <?php checked($email_required, 1); ?> />
				<span><?php esc_attr_e('Email required', $this->plugin_name); ?></span>
			</label>
		</fieldset>
		
		<!-- Email addresses -->
		<fieldset>
			<p><?php _e('Membership administrator email address</p>', $this->plugin_name); ?>
			<!-- _e echos the returned translated text from translate() -->
			<legend class="screen-reader-text"><span><?php _e('Membership administrator email address', $this->plugin_name); ?></span></legend>
			<input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-email_to" name="<?php echo $this->plugin_name; ?>[email_to]" value="<?php if(!empty($email_to)) echo $email_to; ?>"/>
		</fieldset>
 		<fieldset>
			<p><?php _e('CC email address</p>', $this->plugin_name); ?>
			<!-- _e echos the returned translated text from translate() -->
			<legend class="screen-reader-text"><span><?php _e('CC email address', $this->plugin_name); ?></span></legend>
			<input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-email_cc" name="<?php echo $this->plugin_name; ?>[email_cc]" value="<?php if(!empty($email_cc)) echo $email_cc; ?>"/>
		</fieldset>
		<br>
		<h4>
			<?php _e('Select the fields you would like reported when members update their profile.', $this->plugin_name); ?>
		</h4>
		<p>
			<?php _e('Selected fields will be reported only if they have been changed.', $this->plugin_name); ?>
		</p>
				
		<!-- Fields to notify if changed -->
		<?php
			/** wpmembers_field keys
			*       0 order, 
			*       1 label, 
			*       2 meta key, 
			*       3 type, 
			*       4 display, 
			*       5 required, 
			*       6 native, 
			*/	
			$wpmembers_fields = wpmem_fields();
			foreach ($wpmembers_fields as $key => $value){
			if (1 == $value['register']){
				$html_str = '<fieldset>';
				$html_str .= '  <legend class="screen-reader-text"><span>Meta Key: ' . $key . '  Label: ' . $value['label'] . '</span></legend>';
				$html_str .=  '  <label for="'. $this->plugin_name . '-fields-' . $key . '">';
			
					// if the field has been changed in wp-member to Display, the option will not exist in this plugin - so check it exists before getting its value
					if ($options && array_key_exists($value[2], $options_fields)){
						$html_str .=  '    <input type="checkbox" id="' . $this->plugin_name . '-fields-' . $key . '" name="' . $this->plugin_name . '-fields[' . $key . ']" value="1" ' . checked($options_fields[$key], 1, false) . '/>';
					} else {
						$html_str .=  '    <input type="checkbox" id="' . $this->plugin_name . '-fields-' . $key . '" name="' . $this->plugin_name . '-fields[' . $key . ']" value="1" />';
					}										
					// esc_attr_e  - check out how it works because it returns empty string when applied within span below.
					$html_str .=  '    <span>' . $value['label'] . ' &nbsp;&nbsp;&nbsp;(' . $key . ')</span>';
					$html_str .=  '  </label>';
					$html_str .=  '</fieldset>';
					echo $html_str;
				}
			}
		?>	

		<?php submit_button('Save all changes', 'primary','submit', TRUE); ?>
	</form>
</div>
