<?php

/*

We need to be grabbing the state of the task upon loading it into the "alltasks"
array.  This means that when a task registers, we are also determining at that point
if it has been skipped or not.  All tasks should initialize in their constructor
knowing whether or not they have been skipped.

Further, this skipped state now plays a role when we are calling the 'next_task'
function.  The 'next_task' function searches in this order:
    * non-skipped, default tasks (ordered)
    * non-skipped, 3rd party tasks
    * skipped, default tasks (ordered)
    * skipped, 3rd party tasks

This is the only place where the order of a task comes into play.  The order of the
task, if we disregard our inorganic placement of default tasks, is determined by
'add_action' hook priority.  This is the same as WordPress handles callbacks already.

*/

/*
 
There are many areas where the WPTmanager::get function is used.  this is a lookup
function that is slow or can be for obvious reasons.  The use of this function should
definitely be avoided.  Also, there are a number of times get_user_meta is being used
unnecessarily.  It is noted that all of the uses of "active_tasks" in the old code were
storing an array of classnames rather than objects themselves.  It sounds like it will
be advantageous to only once iterate through the WPT_ACTIVE array of classes, then
grab all of the objects via WPTManager::get.  Any time we wish to add to the array of
active_tasks, we simply store the object rather than the classname.  After initialization,
we update_user_meta, grabbing and storingall of the classnames from WPTManager::active_tasks
 
*/


/***************************** WP Task Defines ********************************/
define("WPT_ST_READY", "wpt_ready"); // ready state
define("WPT_ST_COMPLETE", "wpt_complete"); // complete state

/************************** WP Task Include Files *****************************/
require( "WPTTask.php" );
require( "WPTCallback.php" );

class WPTManager {
    /****************** WPTManager members ********************/
    var	$messages;
    var	$tasks; // objects
    var $all_tasks; // stores classnames only
    var $default_tasks; // objects
    var $all_default_tasks;
    var $active_tasks;
    var $all_active_tasks;
    var $callbacks;
    
    /*************** WPTManager construction ******************/
    function WPTManager() {
    }
    
    function __construct() {
	global $wpt_path;
	global $current_user;
	
	//PHP 5
	$this->callbacks	= array();
	$this->tasks		= array();
	$this->all_tasks	= array(); // these include completed
	
	$this->all_default_tasks= array(
	    'install_theme',
	    'set_strong_password',
	    'edit_about',
	    'edit_tagline',
	    'access_screen_options',
	    'customize_sidebar',
	    'moderate_comment'
	    'add_page',
	    'setup_permalinks',
	    'add_category',
	    'add_tag',
	    'install_plugin',
	    'change_default_post_category',
	);
	
	foreach ( $this->all_default_tasks as $dt )
	    include_once( $wpt_path."/default_tasks/$dt.php" );
    }
    
    /******************** QUEUE FUNCTIONS **********************/
    function next_task() {
	foreach( $this->default_tasks as $dt )
	    if ( $dt->_skipped == 0 )
		return $dt;
	
	foreach( $this->tasks as $t )
	    if ( $t->_skipped == 0 )
		return $t;
	
	$best_task	= NULL;
	foreach( $this->default_tasks as $dt ) {
	    if ( ($best_task == NULL || (int)$dt->_skipped < (int)$best_task->_skipped) ) {
		$best_task	= $dt;
	    }
	}
	    
	if ( $best_task != NULL )
	    return $best_task;
	
	$best_task	= NULL;
	foreach( $this->tasks as $t )
	    if ( ($best_task == NULL || $t->_skipped < $best_task->_skipped) ) {
		$best_task	= $t;
	    }
	
	if ( $best_task != NULL )
	    return $best_task;
	
	return NULL;
    }
    
    function all_tasks() {
	return $this->tasks;
    }
    function skipped($class) {
	global $current_user;
	
	$i=0;
	foreach ( $this->all_tasks as $i=>$t )
	    if ( $t['class'] == $class ) {
		if ( $t['global'] ) {
		    $tobj	= get_option(WPT_QUEUE);
		    if ( key_exists($class, $tobj) ) {
			if ( !isset($tobj[$class]["skipped"]))
			    $tobj[$class]["skipped"]	= 0;
			    
			$tobj[$class]["skipped"] += 1;
			update_option(WPT_QUEUE, $tobj);
		    }
		}
		else {
		    $tobj	= get_user_meta($current_user->ID, WPT_QUEUE, true);
		    if ( key_exists($class, $tobj) ) {
			if ( !isset($tobj[$class]["skipped"]))
			    $tobj[$class]["skipped"]	= 0;
			    
			$tobj[$class]["skipped"] += 1;
			update_user_meta($current_user->ID, WPT_QUEUE, $tobj);
		    }
		}
		break;
	    }
	
	$task	= $this->get($class);
	$task->_skipped++;
    }
    
    /************** Initialization & Callbacks ****************/
    function admin_init() {
	$this->check_messages(); // messages only display on the backend
    }
    
    function init() {
	do_action( "wpt_init", $this ); // calling all registrations
	$this->load_ready_tasks(); // only "ready", user-capable tasks
	$this->load_active_tasks(); // further filter to active tasks, load messages and highlights
	$this->get_instructions();

	add_action( "admin_footer", array($this, "scripts") );
	add_action( "wp_footer", array($this, "scripts") );
	
    }
    
    /************ Task registration/object creation ***********/
    /* hook from global to task manager */
    function register_task($classname, $global) {
	array_push( $this->all_tasks, array("class"=>$classname, "global"=>$global));
    }
    
    function load_ready_tasks() {
	$rdy_tasks	= $this->get_ready_tasks();
	$this->init_ready_tasks($rdy_tasks);
    }
    
    function get_ready_tasks() {
	global $current_user;
	$tasks	= array();
	
	foreach( $this->all_tasks as $i=>$t ){
	    $tobj	= NULL;
	    if ( $t['global'] ) {
		$tobj	= get_option(WPT_QUEUE);
		if ( $tobj === false ) {
		    $tobj	= array();
		    update_option(WPT_QUEUE,$tobj);
		}
		if ( !key_exists($t['class'], $tobj) ) {
		    $tobj[$t['class']]	= array("class"=>$t['class'], "status"=>WPT_ST_READY);
		    update_option(WPT_QUEUE, $tobj);
		}
	    }
	    else {
		$tobj	= get_user_meta($current_user->ID, WPT_QUEUE, true);
		if ( $tobj == false ) {
		    $tobj	= array();
		    update_user_meta($current_user->ID, WPT_QUEUE, $tobj);
		}
		if ( !key_exists($t['class'], $tobj) ) {
		    $tobj[$t['class']]	= array("class"=>$t['class'], "status"=>WPT_ST_READY);
		    update_user_meta($current_user->ID, WPT_QUEUE, $tobj);
		}
	    }
	    
	    $tobj	= $tobj[$t['class']];
		
	    if ( !isset($tobj['skipped']) )
		$this->all_tasks[$i]['skipped']	= 0;
	    else
		$this->all_tasks[$i]['skipped']	= $tobj['skipped'];
		
	    if ( $tobj['status'] == WPT_ST_READY )
		array_push( $tasks, $this->all_tasks[$i] );
	}
	
	return $tasks;
    }
    
    /* initializes added tasks as objects */
    function init_ready_tasks($rdy_tasks) {
	$tasks		= array();
	$default_tasks	= array();
	
	foreach( $rdy_tasks as $t ) {
	    $class	= $t['class'];
	    $task	= new $class($t['global'], $t['skipped']);
	    if ( $task->user_capable() ) {
		array_push($tasks, $task );
		if ( in_array($class, $this->all_default_tasks) )
		    array_push($default_tasks, $task);
	    }
	}
	
	$this->tasks		= $tasks;
	$this->default_tasks	= $default_tasks;
    }
    
    /********* Link/Active task detection & Load/hook *********/
    function load_active_tasks() {
	global $current_user;
	$this->active_tasks	= array();
	
	// now make sure this user has a WPT_ACTIVE entry
	$this->all_active_tasks	= get_user_meta($current_user->ID, WPT_ACTIVE, true );

	if ( $this->all_active_tasks == false || !is_array($this->all_active_tasks) ) {
	    //$this->all_active_tasks	= array("wpt_last_page"=>get_bloginfo('wpurl').$_SERVER['REQUEST_URI']);
	    $this->all_active_tasks	= array();
	    update_user_meta($current_user->ID, WPT_ACTIVE, $this->all_active_tasks );
	}
	
	foreach ( $this->all_active_tasks as $at )
	    if ( ($t=$this->get($at)) != NULL )
		array_push($this->active_tasks, $t);
		
	$this->add_active();
	$this->load_active();
	$this->check_active();
	
	$this->all_active_tasks	= array();
	foreach ( $this->active_tasks as $at )
	    array_push($this->all_active_tasks, get_class($at));

	if ( sizeof( $this->all_active_tasks) > 0 )
	    update_user_meta($current_user->ID, WPT_ACTIVE, $this->all_active_tasks );
    }
    function add_active() {
	if ( !isset($_GET['wpt_tid']) || ($t=$this->get($_GET['wpt_tid'])) == NULL ) {
	    if ( isset( $_GET['wpt_highlight']))
		unset($_GET['wpt_highlight']);
	    return;
	}
	
	if ( !in_array($_GET['wpt_tid'], $this->all_active_tasks) )
	    array_push($this->active_tasks,$t);
	//else if ( isset( $_GET['wpt_highlight']))
	//    unset($_GET['wpt_highlight']);
    }
    
    // searches for tasks within their own link domains
    function check_active() {
	$active_tasks	= array();
	$result1	= preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", $_SERVER['REQUEST_URI']);
	$result2	= preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", wp_get_referer());
	
	// from our current active tasks, filter ones that no longer fit domain
	foreach( $this->active_tasks as $i=>$t ) {
	    $result1	= preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", $_SERVER['REQUEST_URI'], $subs );
	    $file	= $subs[1];
	    $query	= $subs[2];
	    
	    //.../filename.php?
	    $result2	= preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", wp_get_referer(), $subs );
	    $file2	= $subs[1];
	    $query2	= $subs[2];
	    
	    $hasLink	= false;
	    foreach( $t->links() as $i=>$l )
		if (( strtolower(substr($file."?".$query, 0, strlen($l['url']))) == strtolower($l['url']))
		||  ( strtolower(substr($file2."?".$query2, 0, strlen($l['url']))) == strtolower($l['url']))){
		    $hasLink	= true;
		    break;
		}
		
	    if ( $hasLink )
		array_push( $active_tasks, $t);
	}

	$this->active_tasks	= $active_tasks;
    }
    
    function load_active() {
	global $current_user;
	foreach ( $this->active_tasks as $t ) {
	    $t->hook();
	    $t->load();
	}
    }
    
    /***************** INSTRUCTION MESSAGING ******************/
    // whether or not to establish hooks for instructions
    function get_instructions() {
	foreach( $this->active_tasks as $t ) {
	    preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", $_SERVER['REQUEST_URI'], $subs );
	    $file	= $subs[1];
	    $query	= $subs[2];
	    
	    $best	= -1;
	    $links	= $t->links();
	    foreach( $links as $i=>$l ) {
		if (strtolower(substr($file."?".$query, 0, strlen($l['url']))) == strtolower($l['url'])) {
		    if ( $best >= 0 && strlen($links[$best]['url']) < strlen($l['url']))
			$best	= $i;
		    else if ( $best < 0 )
			$best	= $i;
		}
	    }
	    
	    if ( $best >= 0 ) {
		if ( isset($links[$best]['highlight']))
		    $_GET['wpt_highlight']	= urlencode(htmlspecialchars($links[$best]['highlight']));
		array_push($this->callbacks,new WPTCallback($this, 'admin_notices', 'dispatch_instruction', 10, 1, array($t,$best) ));
	    }
	}
    }
    
    function dispatch_instruction($t, $i) {
?>
<div class="wpt_message">	
    <div class="updated fade" id="wpt_instruction">
	<p>
	<h5 style="padding:0px; margin:0px;">WordPress Tasks</h5>
<?php
	ob_start();
	$t->instruct($i);
	$output	= ob_get_clean();
	echo $output;
?>
	</p>
    </div>
</div>
<?php
    }
    
    /******************* GENERIC MESSAGING ********************/
    function message($classname, $msg) {
	$wpt_msgs	= get_transient( WPT_MESSAGES );
	
	if ( $wpt_msgs == null || !is_array($wpt_msgs) )
	    $wpt_msgs	= array();
	
	array_push($wpt_msgs, $msg );
	set_transient( WPT_MESSAGES, $wpt_msgs );
    }
    
    /* check for messages previously stored for output */
    function check_messages() {
	$this->messages	= get_transient(WPT_MESSAGES);
	
	if ( $this->messages == null || !is_array($this->messages) )
	    return;
	
	foreach ( $this->messages as $i=>$msg ) {
		add_action( "admin_notices", array($this, 'dispatch_message'), $i );
	}
	
	delete_transient( WPT_MESSAGES ); // TODO: remove me!!! this should be done via JS & AJAX
    }
    
    /* dispatch messages to the admin notification API */
    function dispatch_message() {
	if (strlen($this->messages[0]) > 0):
?>
<div class="wpt_message">	
    <div class="updated fade">
	<p>
	<h5 style="padding:0px; margin:0px;">WordPress Tasks</h5>
<?php	print  array_shift( $this->messages ); ?>

	</p>
    </div>
</div>
<?php	if (!sizeof($this->messages)): ?>
    <script type="text/javascript">
    </script>
<?php
	endif;
	endif;
    }
    
    function parse_url($url) {
	return array("file"=>$file[1], "query"=>$file[2]);
    }
    
    function complete_task($classname) {
	global $current_user;
	$tobj	= $this->get($classname);
	if ( $tobj->_global ) {
	    $task	= get_option( WPT_QUEUE );
	    $task[$classname]['status']	= WPT_ST_COMPLETE;
	    update_option( WPT_QUEUE, $task );
	}
	else {
	    $task	= get_user_meta( $current_user->ID, WPT_QUEUE, true );
	    $task[$classname]['status']	= WPT_ST_COMPLETE;
	    update_user_meta( $current_user->ID, WPT_QUEUE, $task );
	}
    }
    
    function install() {
	
    }
    
    function uninstall() {
	global $current_user, $wpdb;
	
	$users	= $wpdb->get_results("SELECT * FROM $wpdb->users");
	foreach ( $users as $u ) {
	    delete_user_meta($u->ID, WPT_ACTIVE );
	    delete_user_meta($u->ID, WPT_QUEUE );
	}
	
	delete_option(WPT_QUEUE );
    }
    
    function get($id) { //gets a task by classname
	foreach ( $this->tasks as $task )
	    if ( is_object($task) && get_class($task) == $id )
		return $task;
	    
	return NULL;
    }
    
    function scripts() {
	global $wpt_path;
	
	include( $wpt_path."/js/WPTJS.php" );
	foreach ( $this->active_tasks as $t ) {
	    $t->script();
	}
	define("WPT_SCRIPTS_LOADED", 1);
	include( $wpt_path."/js/WPTJS.php" );
    }
}

?>