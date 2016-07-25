<?php
// install table and other required settings for plugin
class tw_install
{
	function install()
	{
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$table = PHONES_TABLE;
		$sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
				`p_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`phn_no` VARCHAR( 255 ) NOT NULL ,
				`name` VARCHAR( 255 ) NOT NULL ,
				`dest_no` VARCHAR( 255 ) NOT NULL ,
				`rec_status` TINYINT NOT NULL ,
				`rel_url` VARCHAR( 255 ) NOT NULL ,
				`l_fetch_time` DATETIME NOT NULL ,
				`status` TINYINT NOT NULL,
				`created` DATETIME NOT NULL
			)ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta($sql);
		
		$table = CALLS_TABLE;
		$sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
			`c_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`p_id` INT NOT NULL ,
			`c_from` VARCHAR( 255 ) NOT NULL ,
			`c_to` VARCHAR( 255 ) NOT NULL ,
			`s_time` DATETIME NOT NULL ,
			`e_time` DATETIME NOT NULL ,
			`duration` INT NOT NULL ,
			`rec` INT NOT NULL ,
			`rec_file` VARCHAR( 255 ) NOT NULL,
			`call_sid` VARCHAR( 255 ) NOT NULL
		)ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		dbDelta($sql);
		
		add_option('tw_sid', '');
		add_option('tw_atoken', '');
	}
	
	static function uninstall()
	{
		delete_option('tw_sid');
		delete_option('tw_atoken');
	}
	
	static function admin_menu()
	{
		add_menu_page('WP Phone Tracker', 'WP Phone Tracker', 'manage_options','tw-phone-tracker', 'tw_phone_numbers', MY_BASE_URL . 'images/phone_grey.png', 36);
		
		
		add_submenu_page('tw-phone-tracker', 'Phone Numbers', 'Phone Numbers', 'manage_options', 'tw-phone-tracker', 'tw_phone_numbers');
		add_submenu_page('tw-phone-tracker', 'Add New', 'Add New', 'manage_options', 'add-phone-number', 'tw_add_phone');
		add_submenu_page('tw-phone-tracker', 'Settings', 'Settings', 'manage_options','tw-settings', 'tw_settings');
		add_submenu_page(NULL, 'Calls', 'Calls', 'manage_options','tw-calls', 'tw_calls');
	}
}
add_action('admin_menu', array('tw_install', 'admin_menu'));
