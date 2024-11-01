<?php

class WPTTask {
    // wptTask interfaces with the database
    // and communicates with the task system through the provided inteface (see init)
    
    var	$id		= "";
    var $name		= "";
    var $completed	= "";
    var $_links		= NULL;
    var $_init		= false;
    var $_global	= false;
    var $_skipped	= false;
    
    var $users;
    var $hooks;
    
    function WPTTask() { // TODO: should we even care about chaining/PHP4 if we just use an 'init' function?
	$this->__construct();
    }
    
    function __construct( $global = false, $skipped = 0 ) {
	// proper translation of strings
	$this->_global		= $global;
	$this->_skipped		= $skipped;
	$this->_links		= $this->links();
	
	$this->hooks		= array();
	$this->users		= $this->get_users(); // TODO: do we need to call this after task initialization?
	
	//$this->_init		= true;
	$this->init();
	//if ( !$this->_init )
	//	self::init();
    }
    
    function init() {
	//$this->_init		= true;
    }
    
    //function init(){} // TODO: need this?
    function hook() {}
    function links() {	return array();}
    
    /* add a hook to later be hooked */
    function add_hook($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	array_push( $this->hooks, array("tag"=>$tag,"function"=>$function_to_add,"priority"=>$priority,"accepted_args"=>$accepted_args) );
    }
    
    /* to be overrided - should return array of user_id's */
    function get_users() {	return array(); }
    
    /* hooks task-defined actions/filters */
    function load() {
	foreach ( $this->hooks as $h ) {
	    $default_hook	= array(
		"priority"=>10,
		"accepted_args"=>1,
		"args"=>array()
	    );
	    
	    $h	= array_merge(array_diff_key($h,$default_hook), $h);
	    if ( !is_array($h['args']) )
		$h['args']	= $h['args'];
	    
	    new WPTCallback($this, $h['tag'], $h['function'], $h['priority'], $h['accepted_args'], $h['args']);
	}
    }
    
    /* prints any task-related javascript into the document */
    function script() {}
    
    function complete() {
	wpt_complete(get_class($this));
	ob_start();
	    $this->followup();
	$output	= ob_get_clean();
	wpt_message(get_class($this), $output);
    }
    
    function describe() {} // displayed on dashboard
    function followup() {} // displayed after completion
    function instruct($switch=-1) {} // displayed after clicking to task page
    
    function href() {
	$options	= $this->_links[0];
	
	if ( !isset($options['url']) )
	    return "";	
    
	$default_options	= array(
	    "highlight"=>"",
	    "other"=>""
	);
	
	$options	= array_merge(array_diff_key($options,$default_options), $options);
	
	if ( preg_match("/\?/", $options['url']) == 0 )
	    $options['url']	.= "?";
	else if ( preg_match("/\?.*=.*&$/", $options['url']) == 0 )
	    $options['url']	.= "&";
	$options['url']	.= "wpt_highlight=".urlencode($options['highlight'])."&wpt_tid=".get_class($this)."&".$options['other'];

	return (htmlspecialchars($options['url']));
    }
    
	
    function parse_url($url) {
	preg_match("/.+\/([^\/]+\.[a-z]+)\?(.+)/", $url, $file );
	return array("file"=>$file[1], "query"=>$file[2]);
    }
    
    /* Produces an anchor tag with the href using make_url */
    function link($options) {
	    if ( !isset($options['url']) || !isset($options['text']) )
		return "";
	    
	    return "<a href=\"".$this->make_url($options)."\">".$options['text']."</a>";
    }
    
    function requires() {
	return array(
	);
    }
    
    function user_capable() {
	if ( !is_array($this->requires()) || sizeof($this->requires() ) < 1 )
	    return true;
	
	foreach( $this->requires() as $r )
	    if ( (is_bool($r)&&$r===true) || !current_user_can($r))
		return false;
	
	return true;
    }
}

?>