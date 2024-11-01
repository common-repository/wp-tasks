<?php

add_action( "wp_ajax_wpt_password_good", "wpt_password_good" );
add_action( "wp_ajax_wpt_password_bad", "wpt_password_bad" );
function wpt_password_good() {
    set_transient("wpt_password_good",1);
}
function wpt_password_bad() {
    delete_transient("wpt_password_good");
}


function set_strong_password() {    
    class set_strong_password extends WPTTask {
	var $id		= "set_strong_password";
	var $title	= "Set Strong Password";
	var $completed	= "Set Strong Password";
	
	function init() {
	    $this->title		= __('Set Strong Password', 'wptasks');
	    $this->completed	= __('Set Strong password', 'wptasks');
	}
	
	// function to establish WP hooks
	function hook() {
	    wp_enqueue_script('password-strength-meter');
	}
	
	function links() {
	    return array(
		array("url"=>"profile.php", "highlight"=>"table #password *:not(input,#pass-strength-result)")
	    );
	}
	
	function describe() {
	    _e('Having a strong password is the easiset way to secure your blog.  WordPress measures your password strength as you type it.  This task will also link you to other ways to protect your blog.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Hardening_WordPress\">http://codex.wordpress.org/Hardening_WordPress</a>";
	    $link2	= "<a href=\"http://akismet.com/\">http://akismet.com/</a>";
	    printf(__('Keep this password in a safe place.  Securing your blog takes little time and saves a lot of headache later down the road.<br><br>
You can always read more about securing your blog on WordPress.org: %s <br>
Akismet is also a great way to keep your blog spam-free: %s', 'wptasks'), $link1, $link2);;
	}
	
	function instruct() {
	    _e('Navigate to the password fields below and choose a password so that the strength indicator indicates a "strong" password.', 'wptasks');
	}
	
	function script() {
?>
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).ready( function() {
<?php if (get_transient("wpt_password_good")!=false): ?>
<?php delete_transient("wpt_password_good"); ?>
wpTasks.ajax("complete", "<?php echo esc_js(get_class($this)); ?>", "&wpt_msg=1" );
<?php endif; ?>
	if ( $("#pass1").length ) {
	    $("#pass2").change(function(){
		$("#pass2").trigger("blur");
	    });
	    $("#pass2").blur(function(){
		var passStrength = passwordStrength($("#pass1").attr("value"), $("#user_login").attr("value"), $("#pass2").attr("value"));
		if ( passStrength == 4 )
		    wpTasks.ajax("password_good", "<?php echo get_class($this); ?>" );
		else
		    wpTasks.ajax("password_bad", "<?php echo get_class($this); ?>" );
	    });
	}
    });
})(jQuery);
</script>
<?php
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "set_strong_password" );

?>