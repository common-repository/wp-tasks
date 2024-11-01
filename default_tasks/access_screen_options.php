<?php
function access_screen_options() {    
    class access_screen_options extends WPTTask {
	var $id		= "access_screen_options";
	
	function init() {
	    $this->title		= __('Access the screen options on any page', 'wptasks');
	    $this->completed	= __('Accessed the Screen Options', 'wptasks');
	}
	
	// function to establish WP hooks
	function hook() {
	}
	
	function links() {
	    return array(
		array("url"=>"link-manager.php", "highlight"=>"#show-settings-link" )
	    );
	}
	
	function describe() {
		_e('The screen options allow you to easily toggle options on a given page.  This means if you
happen to never use categories, you can disable the display of categories on certain pages decreasing clutter.
It is also a great point of reference for exploring the different aspects of WordPress.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Administration_Panels#Screen_Options\">http://codex.wordpress.org/Administration_Panels#Screen_Options</a>";
	    printf(__('That\'s all there is to it!  Any page that has screen options will display a tab in the upper right corner, same place you clicked this one. <br><br>
		      You can also read more here: %s', 'wptasks'), $link1 );
	}
	
	function instruct($switch=-1) {
	    switch( $switch ):
		case 0:
		    _e('Click the "Screen options" link in the top right to pull down the options for the links page.', 'wptasks');
		    break;
		case 1:
		    if ( isset($_GET['wpt_checked']) && $_GET['wpt_checked']==1 )
			_e('Now click on the "relationship" checkbox and you shall see the "relationship" column disappear.', 'wptasks');
		    else
			_e('Now click on the "relationship" checkbox and you shall see the "relationship" column appear.', 'wptasks');
		    break;
	    endswitch;
	}
	
	function script() {
?>
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).ready( function() {

	if ( $("#show-settings-link").length ) {
	    $("#show-settings-link").click(function(){
		var arg = "&wpt_checked=";
		if ( $("input:checkbox[name=rel-hide]:checked").length )
		    arg = arg+"1";
		else
		    arg = arg+"0";
		
		wpTasks.ajax("message", "<?php echo get_class($this); ?>", arg+"&wpt_highlight=<?php echo urlencode(htmlspecialchars("label[for=rel-hide]")); ?>&wpt_step=1&wpt_insertid=<?php echo urlencode( htmlspecialchars("#adv-settings h5")); ?>" );
	    });
	}
	if ( $("input:checkbox[name=rel-hide]").length ) {
	    $("input:checkbox[name=rel-hide]").click(function(){
		wpTasks.ajax("complete", "<?php echo get_class($this); ?>", "&wpt_msg=1" );	
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

add_action( "wpt_init", "access_screen_options" );

?>