<?php
/*
Plugin Name: WP Phone Tracker
Plugin URI: https://www.anchorwave.com
Description: This plugin is used to track phone numbers on twillo.com
Version: 1.0.6
Author: Anchor Wave
Author URI: https://www.anchorwave.com
License: GPL2
*/


define('MY_BASE', plugin_dir_path( __FILE__ ));
require_once MY_BASE . 'update.php';
add_action('admin_init', 'phone_tracker_updates');
function phone_tracker_updates(){
	if ( is_admin() && class_exists('WP_GitHub_Updater') && WP_GitHub_Updater::VERSION == '1.6' ) {
		if ( !defined('WP_GITHUB_FORCE_UPDATE') && isset( $_GET['force-check'] ) && $_GET['force-check'] == '1' && current_user_can('update_plugins') ){	
			define('WP_GITHUB_FORCE_UPDATE', true);
		}
	    $config = array(
	        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
	        'proper_folder_name' => 'phone-tracker', // this is the name of the folder your plugin lives in
	        'api_url' => 'https://api.github.com/repos/anchorwave/phone-tracker', // the GitHub API url of your GitHub repo
	        'raw_url' => 'https://raw.github.com/anchorwave/phone-tracker/master', // the GitHub raw url of your GitHub repo
	        'github_url' => 'https://github.com/anchorwave/phone-tracker', // the GitHub url of your GitHub repo
	        'zip_url' => 'https://github.com/anchorwave/phone-tracker/zipball/master', // the zip url of the GitHub repo
	        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
	        'requires' => '4.0', // which version of WordPress does your plugin require?
	        'tested' => '4.2', // which version of WordPress is your plugin tested up to?
	        'readme' => 'README.md',
	        'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
	    );
	    $githubupdater = new WP_GitHub_Updater($config);
	}
}

require_once(MY_BASE . 'config/config.php');
register_activation_hook(__FILE__, array('tw_install', 'install'));
register_deactivation_hook( __FILE__, array('tw_install', 'uninstall') );

function tw_phone_numbers()
{
	global $tw_cont_obj;
	/*global $wpdb;
	$data = $wpdb->get_results('SELECT * FROM ' . CALLS_TABLE, ARRAY_A);
	echo '<pre>';
	print_r($data);
	exit;*/
	//mysql_query('update ' . CALLS_TABLE . ' set rec=1');
	$tw_cont_obj->phone_numbers();
}

function tw_add_phone()
{
	global $tw_cont_obj;
	$tw_cont_obj->add_phone();
}

function tw_settings()
{
	global $tw_cont_obj;
	$tw_cont_obj->settings();
}

function tw_calls()
{
	global $tw_cont_obj;
	$tw_cont_obj->calls();
}

if(is_numeric($_GET['tw_phone_call']) && $_GET['tw_phone_call'] > 0)
{
	$p_id =(int) $_GET['tw_phone_call'];
	$tw_cont_obj->called($p_id);
	exit;
}

if(is_numeric($_GET['tw_phone_record']) && $_GET['tw_phone_record'] > 0)
{
	$callId =(int) $_GET['tw_phone_record'];
	$tw_cont_obj->recording($callId);
	exit;
}