<?php
function moderate_comment() {    
    class moderate_comment extends WPTTask {
	var $id		= "moderate_comment";
	
	function init() {
	    $this->title		= __('Moderate a Comment', 'wptasks');
	    $this->completed	= __('Moderated a Comment', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"edit-comments.php", "highlight"=>".approve")
	    );
	}
	
	function requires() {
	    return array(
		"moderate_comments"
	    );
	}
	
	function describe() {
		_e('Your users will often comment on your posts.  Sometimes, bots may try to post spam or your users even could post things inappropriate for your blog or post.  This task will walk you through moderating a comment.', 'wptasks');
	}
	
	function followup() {
	    $comments	= get_comments(array(
		'author_email' => "admin@wordpresstasks.com"
	    ));
	    foreach ( $comments as $c )
		wp_delete_comment($c->comment_ID);
		
	}
	
	function instruct($switch=0) {
	    switch ( $switch ) {
		case 0:
		    _e('At the top of this list, you should see a comment beginning with "This is a test post for you to moderate".  Go ahead and highlight over the comment row, then click approve.', 'wptasks');
		    $comments	= get_comments(array(
			'author_email' => "admin@wordpresstasks.com",
		    ));
	    
		    if ( sizeof($comments) == 0 ) {
			$this->add_comment();
		    }
		    break;
		case 1:
		    $link1	= "<a href=\"http://codex.wordpress.org/Administration_Panels#Comments\">http://codex.wordpress.org/Administration_Panels#Comments</a>";
		    $link2	= "<a href=\"http://codex.wordpress.org/Comments_Comments_SubPanel\">http://codex.wordpress.org/Comments_Comments_SubPanel</a>";
		    $link3	= "<a href=\"http://codex.wordpress.org/Comment_Spam\">http://codex.wordpress.org/Comment_Spam</a>";
		    printf(__('This comment is now approved (it has also been deleted since it was an example).  Normally after approving comments, they will display on the pages or posts they were originally posted on.  There are many options you can choose to alter the way comments are approved, displayed and even filtered.  Read more on the following pages listed below: <br><br>
			      Comments - %s <br>
			      Comments SubPanel - %s <br>
			      Spam - %s', 'wptasks'), $link1, $link2, $link3);
		    
		    set_transient("wpt_moderate_comment_finished", 1);
		    break;
	    }
		
	    if ( get_transient("wpt_moderate_comment_finished") != false ){
		$comments	= get_comments(array(
		    'author_email' => "admin@wordpresstasks.com"
		));
		
		foreach ( $comments as $c )
		    wp_delete_comment($c->comment_ID);
		    
		delete_transient("wpt_moderate_comment_finished");
	    }
	}
	
	function add_comment() {
	    global $wptasks;
	    
	    $posts	= get_posts(array(
		"post_type"=>"page",
		"orderby"=>"date",
		"order"=>"ASC"
	    ));
	    $post	= NULL;
	    
	    foreach($posts as $post )
		if ( strtolower($post->post_title) == "about")
		    break;
		
	    $comments	= get_comments(array(
		'author_email' => "admin@wordpresstasks.com"
	    ));
	    
	    foreach ( $comments as $c )
		wp_delete_comment($c->comment_ID);
		
	    $time = current_time('mysql');
	    
	    $data = array(
		'comment_post_ID' => $post->ID,
		'comment_author' => 'admin',
		'comment_author_email' => 'admin@wordpresstasks.com',
		'comment_content' => 'This is a test comment for you to moderate.  Often, it is important to filter out some of the comments people make, be they spam or flames.',
		'comment_parent' => 0,
		'user_id' => 1,
		'comment_author_IP' => '127.0.0.1',
		'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
		'comment_date' => $time,
		'comment_approved' => 0,
	    );
	    
	    wp_insert_comment($data);
	}
	
	function script() {
?>
<script type="text/javascript"> <!-- DO WE WANT THIS HERE OR SHOULD WE ADD IT AUTOMATICALLY??? -->
(function($){
    $(document).bind('wptLoaded', function(){
	if ( $(".wpt_highlight").length ) {
	    $(".wpt_highlight a").click (function(){
		wpTasks.ajax("message", "<?php echo get_class($this); ?>", "&wpt_step=1&wpt_insertid=<?php echo urlencode(htmlspecialchars("#submitted-on")); ?>" );
		wpTasks.ajax("complete", "<?php echo get_class($this); ?>" );
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

add_action( "wpt_init", "moderate_comment" );

?>