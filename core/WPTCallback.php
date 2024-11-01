<?php

class WPTCallback {
	// wptCallback provides callback objects for WP hooks
	// this serves as a wrapper to interface with a wptTask object
    var	$caller;
    var	$tag;
    var	$function;
    var	$priority;
    var	$accepted_args;
    var $args;
    
    function WPTCallback($caller, $tag, $function, $priority, $accepted_args, $args) {
	$this->__construct( $caller, $tag, $function, $priority, $accepted_args, $args );
    }
    
    function __construct($caller, $tag, $function, $priority, $accepted_args, $args) {
	    $this->caller	= $caller;
	    $this->tag		= $tag;
	    $this->function	= $function;
	    $this->priority	= $priority;
	    $this->accepted_args= $accepted_args;
	    $this->args		= $args;
	    add_action( $this->tag, array($this, "callback"), $priority, $accepted_args );
    }
    
    function callback() {
	    $args	=  func_get_args();
	    if (!is_array($this->args) && $this->args != NULL)
		array_unshift($args,$this->args);
	    else if (sizeof($this->args))
		$args	= array_merge($this->args, $args);
	    call_user_func_array(array($this->caller, $this->function), $args);
    }
}

?>