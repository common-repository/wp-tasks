<?php
function edit_about() {    
    class edit_about extends WPTTask {
	var $id		= "edit_about";
	
	function init() {
	    $this->title		= __('Edit the About Page', 'wptasks');
	    $this->completed	= __('Edited the About page', 'wptasks');
	}
	
	// the task object calls this after hooking and receiving the callback we set
	function post_edited($postID) {
	    $post	= get_post($postID);
	    if ( $post->post_type == "page" && strtolower($post->post_title) == "about" ) {
		$this->complete();
	    }
	}
	
	function requires() {
	    return array(
		"edit_pages"
	    );
	}
	
	function links() {
	    $posts	= get_posts(array(
		"post_type"=>"page",
		"orderby"=>"date",
		"order"=>"ASC"
	    ));
	    $post	= NULL;
	    foreach($posts as $post )
		if ( strtolower($post->post_title) == "about")
		    break;
		
	    return array(
		array("url"=>"edit.php?post_type=page", "highlight"=>"#page-$post->ID *:not(input,.comment-count)"),
		array("url"=>"post.php")
	    );
	}
	
	// function to establish WP hooks
	function hook() {
	    $this->add_hook("edit_post", "post_edited");
	}
	
	function condition() {
	    return true;
	}
	
	function describe() {
		_e('Your About page is provided by default.  It is a great place for you to describe your blog.  This task will walk you through the page menu and editing a page.', 'wptasks');
	}
	
	function followup() {
    $posts	= get_posts(array(
	"post_type"=>"page",
	"orderby"=>"date",
	"order"=>"ASC"
    ));
    
    
	    foreach($posts as $post )
		if ( strtolower($post->post_title) == "about"):
		    $link1	= '<a href="'.get_permalink($posts[0]->post_ID).'">here</a>';
		    $link2	= "<a href=\"http://codex.wordpress.org/Pages\">http://codex.wordpress.org/Pages</a>";
		    $link3	= "<a href=\"http://lorelle.wordpress.com/2005/09/28/who-the-hell-are-you/\">http://lorelle.wordpress.com/2005/09/28/who-the-hell-are-you/</a>";
		    printf(__('Great!  Now see your About page %s... You can read more by clicking the links below: <br>
		    Pages - %s <br>
		    Advice from Lorelle - %s', 'wptasks'), $link1, $link2, $link3);
		endif;
	}
	
	function instruct($switch = -1) {
	    if ( $switch == 0 ):
		_e('Here you will see all of the pages currently on your blog.  Click the edit button on the About page (highlighted) to edit that page.', 'wptasks');
	    elseif ( $switch == 1 ):
		_e('Here you can edit your About page.  Fill in a description and hit the update button to the right.  Don\'t worry if you\'re unsure what to write for now, you can always change this page at a later time, too!', 'wptasks' );
	    endif;
	}
	
	function script(){
?>
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
})(jQuery);
</script>
<?php
	}
    }
    
    wpt_register(__FUNCTION__, true);
}

add_action( "wpt_init", "edit_about" );


?>