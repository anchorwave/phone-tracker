<?php
// configuration settings
ini_set("max_execution_time", "3600");
ini_set('memory_limit', '100M');

add_action('init', 'myTwilloSession', 1);
function myTwilloSession()
{
    if(!session_id())
	{
        session_start();
    }
}

if(!class_exists('WP_List_Table'))
{
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

global $wpdb;
define('MY_BASE_URL', plugins_url('twillo') . '/');
define('PHONES_TABLE', $wpdb->prefix . "tw_phones");
define('CALLS_TABLE', $wpdb->prefix . "tw_calls");

require_once(ABSPATH . 'wp-includes/wp-db.php');
require_once(MY_BASE . 'setup/install.php');

$tw_sid = get_option('tw_sid');
$tw_atoken = get_option('tw_atoken');

define('TW_SID', $tw_sid);
define('TW_ATOKEN', $tw_atoken);

require_once(MY_BASE . 'controller.php');
$GLOBALS['tw_cont_obj'] = new tw_controller();