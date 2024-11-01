<?php

add_action("wp_ajax_wpt_check_sidebar_complete", "wpt_check_sidebar_complete" );

function wpt_check_sidebar_complete() {
    global $wptasks;
    global $current_user;
    $tasks	= get_user_meta( $current_user, WPT_QUEUE, true );
    
    if ( $tasks['customize_sidebar']['ready'] ) {
	$tasks['customize_sidebar']['ready']	= false;	
	$tasks['customize_sidebar']['step']++;
	$step	= $tasks['customize_sidebar']['step'];
	$data	= $tasks['customize_sidebar']['data'];
	update_user_meta($current_user, WPT_QUEUE, $tasks );

	$task	= $wptasks->get($_GET['wpt_tid']);
	ob_start();
	$task->instruct($step);
	$output	= ob_get_clean();
	
	$arr	= array("msg"=>($output),"id"=>$data, "step"=>$step);
	echo json_encode( $arr );
    }
    die();
}
function customize_sidebar() {    
    class customize_sidebar extends WPTTask {
	var $id		= "customize_sidebar";
	var $title	= "Customize a Sidebar";
	var $completed	= "Customized a Sidebar";
	
	function init() {
	    $this->title		= __('Customize a Sidebar', 'wptasks');
	    $this->completed	= __('Customized a Sidebar', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"widgets.php" )
	    );
	}
	
	function requires() {
	    return array(
		"edit_theme_options"
	    );
	}
	
	function describe() {
	    _e('Widgets are yet another feature of WordPress that are just plain fun.  You may have seen tag clouds while wandering the web, and aside from their iconicism, they are but one example of cool things widgets can do.  There are also widgets created by the WordPress community that will stream your tweets.  This task will walk you through adding widgets to WordPress.', 'wptasks');
	}
	
	function followup() {
	    //delete_transient("wpt_customize_sidebar_done");
	    //_e('Fantastic, you now have a tag cloud on your blog!', 'wptasks');
	}
	
	function instruct($switch=-1) {
	    global $current_user;
	    switch($switch):
		case 0:
		    _e('Below are a list of available widgets.  You may note on the right side of the screen there are one or many sidebars.  Go ahead and drag
the tag cloud widget onto one of the sidebars.', 'wptasks' );
		    break;
		case 1:
		    _e('Good.  Now give the Tag Cloud a title and hit save', 'wptasks');
		    break;
		case 2:
		    $link1	= "<a href=\"http://codex.wordpress.org/WordPress_Widgets\">http://codex.wordpress.org/WordPress_Widgets</a>";
		    $link2	= "<a href=\"http://codex.wordpress.org/Appearance_Widgets_SubPanel\">http://codex.wordpress.org/Appearance_Widgets_SubPanel</a>";
		    $link3	= "<a href=\"http://wordpress.org/extend/plugins/tags/widget\">http://wordpress.org/extend/plugins/tags/widget</a>";
		    printf(__('Nice job.  You can now view your homepage to see the tag cloud you just added.  See below for many more resources on WordPress widgets:<br><br>
		    WordPress Widgets - %s <br>
		    Appearance Widgets SubPanel - %s <br>
		    Plugin Directory(Widgets) - %s', 'wptasks'), $link1, $link2, $link3 );
		    $tasks	= get_user_meta( $current_user, WPT_QUEUE, true );
		    $tasks[__CLASS__]['step'] = 0;
		    update_user_meta( $current_user, WPT_QUEUE, $tasks );
		    wpt_complete(get_class($this));
		    break;
	    endswitch;
	}
	
	// function to establish WP hooks
	function hook() {
	    global $current_user;
	    
	    $tasks	= get_user_meta( $current_user, WPT_QUEUE, true );
	    if ( !isset($tasks[__CLASS__]['step']) || $tasks[__CLASS__]['step'] <= 0 )
		$tasks[__CLASS__]['step']	= 0;
	    update_user_meta( $current_user, WPT_QUEUE, $tasks );
	    
	    add_action("widgets.php", array($this,"widget_callback"), 10);
	}
	
	function widget_callback() {
	    global $current_user;
	    
	    $tasks	= get_user_meta( $current_user, WPT_QUEUE, true );
	    
	    switch( $tasks[__CLASS__]['step'] ) {
		case 0:
		    if ( $_POST['id_base'] == "tag_cloud" ) {
			$tasks[__CLASS__]['data']	= $_POST['widget-id'];
			$tasks[__CLASS__]['ready']	= true;
		    }
		    break;
		case 1:
		    if ( isset($_POST['widget-tag_cloud'] ) ) {
			foreach( $_POST['widget-tag_cloud'] as $k=>$v) {
			    
			    if( is_array($v) && strlen($v['title'])
				|| $k == "title" && strlen($v) )
			    {
				$tasks[__CLASS__]['ready']	= true;
				$tasks[__CLASS__]['data']	= "";
				brek;
			    }
			}
		    }
	    
		    break;
	    }
	    update_user_meta( $current_user, WPT_QUEUE, $tasks );
	}
	
	function script(){
?>
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    var customizeSidebar = {};
	
    customizeSidebar.doMessage	= function(dat) {
	if ( dat.id == undefined || dat.id.length == 0 ) {
	    wpTasks.displayMsg( dat.msg );
	    return;
	}
	$("input[name=widget-id][value="+dat.id+"]").siblings(".widget-content").attr("id", "wpt_"+dat.id);
	wpTasks.displayMsg(dat.msg, "#wpt_"+dat.id, "prepend");
    }
    customizeSidebar.checkForTransient = function() {
	wpTasks.ajax("check_sidebar_complete","<?php echo get_class($this); ?>", {args:"&wpt_msg=1", callbackReturns: true, callback: customizeSidebar.doMessage});
    }
    $(document).ready( function() {
	setInterval(customizeSidebar.checkForTransient, 1000);
    });
})(jQuery);
</script>
<?php
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "customize_sidebar" );
?>