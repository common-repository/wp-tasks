<?php
function add_page() {    
    class add_page extends WPTTask {
	var $id		= "add_page";
	var $title	= "Add a Page";
	var $completed	= "Added a Page";
	
	function init() {
	    $this->title		= __('Add a Page', 'wptasks');
	    $this->completed	= __('Added a Page', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"post-new.php?post_type=page", "highlight"=>"#col-left .form-wrap")
	    );
	}
	
	function requires() {
	    return array(
		"publish_pages"
	    );
	}
	
	function describe() {
		_e('In WordPress, pages can be looked at as more "static" than your main blog page.  That is, you update these pages less frequently and may dedicate them to more specialized purposes.
This could include but is certainly not limited to an about page, pictures page or a contact form page.  This task will walk you through making a page.', 'wptasks');
	}
	
	function followup() {
	    $posts	= get_posts(array(
		"post_type"=>"page",
		"orderby"=>"date",
		"order"=>"DSC"
	    ));
	    $post	= NULL;
	    foreach($posts as $post )
		if ( strtolower($post->post_title) == "about")
		    break;
	    $link1	= '<a href="'.get_permalink($posts[0]->post_ID).'">'.$posts[0]->post_title.'</a>';
	    $link2	= "<a href=\"http://codex.wordpress.org/Pages\">http://codex.wordpress.org/Pages</a>";
	    printf(__('Congratulations!  You are the proud owner of a new page!  See your page here: %s <br><br>
		      Pages - %s', 'wptasks'), $link1, $link2 );
	}
	
	function instruct() {
	    _e('Go ahead and fill out a page title and put some content in.  Don\'t worry if you aren\'t sure what to put, you can always update or remove this page later.
When you are finished, hit the blue publish button over to the right :)', 'wptasks');
	}
	
	function hook() {
	    $this->add_hook("publish_page", "complete");
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "add_page" );

?>