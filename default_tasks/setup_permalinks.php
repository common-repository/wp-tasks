<?php

add_action( "wp_ajax_wpt_permalinks_complete", "wpt_permalinks_complete" );
add_action( "wp_ajax_wpt_permalinks_incomplete", "wpt_permalinks_incomplete" );
function wpt_permalinks_complete() {
    set_transient("wpt_permalinks_complete",1);
}
function wpt_permalinks_incomplete() {
    delete_transient("wpt_permalinks_complete");
}
function setup_permalinks() {    
    class setup_permalinks extends WPTTask {
	var $id		= "setup_permalinks";
	
	function init() {
	    $this->title		= __('Set Up Permalink Structure', 'wptasks');
	    $this->completed	= __('Set Up Permalink Structure', 'wptasks');
	}
	
	// function to establish WP hooks
	function hook() {
	}
	
	function requires() {
	    return array(
		"manage_options",
		(strlen(get_option( 'permalink_structure' ))==0)
	    );
	}
	
	function script() {
?>	    
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).ready( function() {
<?php if (get_transient("wpt_permalinks_complete")!=false): ?>
<?php delete_transient("wpt_permalinks_complete"); ?>
setTimeout(function() {wpTasks.ajax("complete", "<?php echo esc_js(get_class($this)); ?>", "&wpt_msg=1" ); }, 600);
<?php endif; ?>
	$("input:radio[name=selection]").change(function() {
	    if ( $("input:radio[name=selection]:checked").val() != "" )
		wpTasks.ajax("permalinks_complete","<?php echo get_class($this); ?>" );
	    else
		wpTasks.ajax("permalinks_incomplete","<?php echo get_class($this); ?>" );
	    
	});
    });
})(jQuery);
</script>
<?php
	}
	
	function links() {
	    return array(
		array("url"=>"options-permalink.php")
	    );
	}
	
	function describe() {
		_e('Permalinks are helpful for making your content links more readable by search engines.  They also allow you to customize how you would like links structured.', 'wptasks');
	}
	
	function followup() {
	    $link1	= '<a href="http://codex.wordpress.org/">http://codex.wordpress.org/</a>';
	    $link2	= '<a href="http://codex.wordpress.org/Using_Permalinks">http://codex.wordpress.org/Using_Permalinks</a>';
	    printf(__('Now your links are pretty!  Permalinks can become a pretty advanced topic if you choose.  Remember to visit WordPress.org for info on permalinks and anything else Wordpress related!<br><br>
		      WordPress Codex - %s <br>
		      Using Permalinks - %s', 'wptasks'), $link1, $link2);
	}
	
	function instruct() {
	    $link	= '<a href="http://codex.wordpress.org/Using_Permalinks">http://codex.wordpress.org/Using_Permalinks</a>';
	    printf(__('Below you may choose from a few predefined permalink structures.  Alternatively, you may define your own.  A great source for permalinks worth reading can be found on the WordPress.org website:<br>%s', 'wptasks' ), $link );
	}
    }
    
    wpt_register(__FUNCTION__,true);
}


add_action( "wpt_init", "setup_permalinks" );

?>