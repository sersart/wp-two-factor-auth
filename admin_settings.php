<?php
defined('ABSPATH') || die();

if(!is_admin())
	exit;

$tfa->setUserHMACTypes();
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Two Factor Auth <?php _e('Settings', TFA_TEXT_DOMAIN); ?></h2>
	<div>
		<img style="margin-top: 10px" src="<?php print plugin_dir_url(__FILE__); ?>img/tfa_header.png">
	</div>
	<form method="post" action="options.php" style="margin-top: 40px">
	<?php
		settings_fields('tfa_user_roles_group');
	?>
		<h2><?php _e('User Roles', TFA_TEXT_DOMAIN); ?></h2>
		<?php _e('Choose which User Roles that will have', TFA_TEXT_DOMAIN); ?> <em>Two Factor Auth</em> <?php _e('activated', TFA_TEXT_DOMAIN); ?>.
		<br>
		<?php _e('The users will default to have their One Time Passwords delivered by email since they need to add their private key to third party apps themselves.', TFA_TEXT_DOMAIN); ?>
		<p>
	<?php
		tfaListUserRolesCheckboxes();
	?></p>
	<?php submit_button(); ?>
	</form>
	
	<hr>
	<h2><?php _e('Email Settings', TFA_TEXT_DOMAIN); ?></h2>
	<form method="post" action="options.php" style="margin-top: 40px">
		<?php
			settings_fields('tfa_email_group');
		?>
		<p>
		<?php
			tfaListEmailSettings()
		?></p>
		<?php submit_button(); ?>
	</form>
	
	<hr>
	<form method="post" action="options.php" style="margin-top: 40px">
	<?php
		settings_fields('tfa_xmlrpc_status_group');
	?>
		<h2><?php _e('XMLRPC Status', TFA_TEXT_DOMAIN); ?></h2>
		<?php _e('Two Factor Auth for XMLRPC users is turned off by default since there exists no clients that supports it. Leave this to off if you don\'t have a custom XMLRPC client that supports it or you won\'t be able to publish posts via Wordpress XMLRPC API.', TFA_TEXT_DOMAIN); ?>
		<p>
		<?php
			tfaListXMLRPCStatusRadios();
		?></p>
		<?php submit_button(); ?>
	</form>
	
	<hr>
	<form method="post" action="options.php" style="margin-top: 40px">
	<?php
		settings_fields('tfa_default_hmac_group');
	?>
		<h2><?php _e('Default Algorithm', TFA_TEXT_DOMAIN); ?></h2>
		<?php _e('Choose which algorithm to be used as default. Your users can change this in their own settings if they want.', TFA_TEXT_DOMAIN); ?>
		<p>
		<?php
			tfaListDefaultHMACRadios();
		?></p>
		<?php submit_button(); ?>
	</form>
	<hr>
	<br><br>
	<h2><?php _e('Change User Settings', TFA_TEXT_DOMAIN); ?></h2>
	<p>
		<?php _e("If some of your users lose their phone and don't have access to their panic codes, you can reset their delivery type here and change it to email so they can login again and add a new phone.", TFA_TEXT_DOMAIN); ?>
		<br>
		<?php _e('Click on the "Change to email" button to change the delivery settings for that user.', TFA_TEXT_DOMAIN); ?>
		<br>
		<?php _e("Users can change their own settings via the menu <strong>Two Factor Auth</strong> when they're logged in.", TFA_TEXT_DOMAIN); ?>
	<p>
		<?php
		
		//List users and type of tfa
		foreach($wp_roles->role_names as $id => $name)
		{	
			$setting = get_option('tfa_'.$id);
			$setting = $setting === false || $setting ? 1 : 0;
			if(!$setting)
				continue;
			
			$users_q = new WP_User_Query( array(
			  'role' => $name
			));
			$users = $users_q->get_results();
			
			if(!$users)
				continue;
			
			print '<h3>'.$name.'s</h3>';
			
			foreach( $users as $user )
			{
				$userdata = get_userdata( $user->ID );
				$tfa_type = get_user_meta($user->ID, 'tfa_delivery_type', true);
				print '<span style="font-size: 1.2em">'.esc_attr( $userdata->user_nicename ).'</span>';
				if(!$tfa_type || $tfa_type == 'email')
					print ' - '.__('Email', TFA_TEXT_DOMAIN);
				else
					print ' - <a class="button" href="'.add_query_arg(array('tfa_change_to_email' => 1, 'tfa_user_id' => $user->ID)).'">'.__('Change to email', TFA_TEXT_DOMAIN).'</a>';
				print '<br>';
			}
		}
		
		?>
	</p>
</div>