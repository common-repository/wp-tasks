<?php
/*
Plugin Name: WordPress Tasks
Plugin URI: http://www.thedukebag.com
Description: WordPress Tasks walks you through and helps you learn to use basic WordPress features
Author: dukebag
Version: 0.1
Author URI: http://www.thedukebag.com
*/

/***************************** WP Task Defines ********************************/
//define("WPT_DEBUG", 1);

define("WPT_MESSAGES", "wpt_messages"); // where we store messages
define("WPT_QUEUE", "wptasks"); // all tasks stored in the task queue
define("WPT_ACTIVE", "wptasks_active"); // the active tasks only

define("WPT_NUM_TASKS", 1); // how many displayed on the widget?

/************************* LOAD CORE TASK FILES *******************************/
require_once( "core/WPTManager.php" );
include_once( "frontend/WPTWidget.php" );

/************************ GLOBALS FOR INSTALLATION ****************************/
global $wpt_path;
global $wpt_url;
global $wptasks;
global $wpt_widget;
global $current_user, $wpdb;

/****************************** PATH VARIABLES ********************************/
$wpt_url	= trailingslashit( get_bloginfo('url') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
$wpt_path	= WP_PLUGIN_DIR."/".dirname( plugin_basename(__FILE__) );

/****************************** JS ENQUEUES ***********************************/
wp_enqueue_script("jquery");
wp_enqueue_script("jquery-scrollto", $wpt_url."/js/jquery.scrollTo-min.js", array("jquery"));
include_once( "js/WPTAjax.php");

/************************ INITIALIZE TASK OBJECTS *****************************/
$wptasks	= new WPTManager();
$wpt_widget	= new WPTWidget();

/*************************** WPTASKS HOOKS  ***********************************/
add_action( "admin_init",	array($wptasks, "admin_init") );
add_action( "admin_head",	array($wpt_widget,'css') );
add_action( "wp_dashboard_setup", create_function('','global $wpt_widget; wp_add_dashboard_widget( "dashboard_tasks", "Tasks", array($wpt_widget,"html") );') );
add_action( "init",		array($wptasks, "init") );

/********************* INSTALATION/UNINSTALLATION *****************************/
register_activation_hook( __FILE__, "wpt_install" );
if ( defined("WPT_DEBUG") )
    register_deactivation_hook( __FILE__, "wpt_uninstall" );
else
    register_uninstall_hook( __FILE__, "wpt_uninstall" ); // uninstalls the global+user task queues

/********************* GLOBALLY ACCESSIBLE/API FUNCTIONS **********************/
function wpt_install() {
    global $wpt_path;
    global $wptasks;
    global $wpdb;
    require_once($wpt_path."/core/WPTManager.php");
    $wptasks->install();
}

function wpt_uninstall() {
    global $wpt_path;
    global $wptasks;
    global $wpdb;
    require_once($wpt_path."/core/WPTManager.php");
    $wptasks->uninstall();
}

function	wpt_register($classname, $global=false) {
    global $wptasks;
    $wptasks->register_task($classname, $global);
}

function	wpt_complete($classname) {
    global $wptasks;
    $wptasks->complete_task($classname);
}

function	wpt_message($classname, $msg) {
    global $wptasks;
    $wptasks->message($classname, $msg);
}
?>