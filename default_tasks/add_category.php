<?php
function add_category() {    
    class add_category extends WPTTask {
	var $id		= "add_category";
	var $title	= "Add a Category";
	var $completed	= "Added a category";
	
	function init() {
	    $this->title		= __('Add a Category', 'wptasks');
	    $this->completed	= __('Added a Category', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"edit-tags.php?taxonomy=category", "highlight"=>".form-field")
	    );
	}
	
	function requires() {
	    return array(
		"manage_categories"
	    );
	}
	
	function describe() {
		_e('Categories not only help you organize posts, but it allows users to better understand the content on your website.  This task will introduce you to the categories/taxonomies of WordPress.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Glossary#Category\">http://codex.wordpress.org/Glossary#Category</a>";
	    $link2	= "<a href=\"http://codex.wordpress.org/Posts_Categories_SubPanel\">http://codex.wordpress.org/Posts_Categories_SubPanel</a>";
		printf(__("Great!  You just created a category named: %s.  You can now file posts under this category and are one step closer to a neatly organized blog!
Remember to check out the dashboard for more fun things to learn.<br><br>
    Glossary(Catgegories) - %s<br>
    Manage Categories SubPanel - %s", 'wptasks'), stripslashes($_GET['wpt_newcat']), $link1, $link2);
	}
	
	function instruct() {
	    _e('Create a category by filling out the highlighted fields.  When you are done, click "Add new category" to add the category!', 'wptasks');
	}
	
	function script() {
?>	    
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).ready( function() {
	$("#submit").click(function(){
	    if ( $("input[name=tag-name]").val() != "" )
		wpTasks.ajax("complete","<?php echo get_class($this); ?>", "&wpt_msg=1&wpt_insertid=<?php echo urlencode( htmlspecialchars(".tablenav:eq(0)")); ?>&wpt_newcat="+$("input[name=tag-name]").val() );
	});
    });
})(jQuery);
</script>
<?php
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "add_category" );

?>