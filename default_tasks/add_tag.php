<?php
function add_tag() {    
    class add_tag extends WPTTask {
	var $id		= "add_tag";
	var $title	= "Add a Post Tag";
	var $completed	= "Added a Tag";
	
	function init() {
	    $this->title		= __('Add a Post Tag', 'wptasks');
	    $this->completed	= __('Added a Post Tag', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"edit-tags.php?taxonomy=post_tag", "highlight"=>".form-field")
	    );
	}
	
	function requires() {
	    return array(
		"manage_categories"
	    );
	}
	
	function describe() {
		_e('Tags not only help you organize posts, but it allows users to better understand the content on your website.  This task will introduce you to the tags/taxonomies of WordPress.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Glossary#Tag\">http://codex.wordpress.org/Glossary#Tag</a>";
	    $link2	= "<a href=\"http://codex.wordpress.org/Posts_Post_Tags_SubPanel\">http://codex.wordpress.org/Posts_Post_Tags_SubPanel</a>";
		printf(__("Great!  You just created a tag named: %s.  You can now file posts under this category and are one step closer to a neatly organized blog!
Remember to check out the dashboard for more fun things to learn.<br><br>
    Glossary(Tag) - %s <br>
    Post tags SubPanel - %s", 'wptasks'), stripslashes($_GET['wpt_newtag']), $link1, $link2);
	}
	
	function instruct() {
	    _e('Create a tag by filling out the highlighted fields.  When you are done, click "Add New Tag" to add the tag!', 'wptasks');
	}
	
	function script() {
?>	    
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).ready( function() {
	$("#submit").click(function(){
	    if ( $("input[name=tag-name]").val() != "" )
		wpTasks.ajax("complete","<?php echo get_class($this); ?>", "&wpt_msg=1&wpt_insertid=<?php echo urlencode( htmlspecialchars(".tablenav:eq(0)")); ?>&wpt_newtag="+$("input[name=tag-name]").val() );
	});
    });
})(jQuery);
</script>
<?php
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "add_tag" );

?>