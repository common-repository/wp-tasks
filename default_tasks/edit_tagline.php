<?php
function edit_tagline() {    
    class edit_tagline extends WPTTask {
	var $id		= "edit_tagline";
	
	function init() {
	    $this->title		= __('Edit the Tagline', 'wptasks');
	    $this->completed	= __('Edited the Tagline', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"options-general.php", "highlight"=>".form-table tr:eq(1)")
	    );
	}
	
	function requires() {
	    return array(
		"manage_options"
	    );
	}
	
	function describe() {
		_e('The tagline can be thought of as a subtitle, catch-phrase, or slogan of your blog.  It is supported by most themes, but may be removed by making the tagline field blank.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://www.wordpress.org/\">WordPress.org</a>";
	    $link2	= "<a href=\"http://codex.wordpress.org/Glossary#Tagline\">http://codex.wordpress.org/Glossary#Tagline</a>";
		printf(__('Now goto your blog to see your changes!  Make sure to check back at the dashboard for the next task!  Remember there is tons of awesome content to read and learn about WordPress on %s: <br><br>
			  Glossary(Tagline) - %s', 'wptasks' ), $link1, $link2 );
	}
	
	function instruct() {
	    _e('Below you will see the tagline input field highlighted.  Change the value to what you would like your tagline to be and hit save changes at the bottom of this page.', 'wptasks');
	}
	
	// function to establish WP hooks
	function hook() {
	    $this->add_hook( "update_option_blogdescription", "complete" );
	}
    }
    
    wpt_register(__FUNCTION__, true);
}

add_action( "wpt_init", "edit_tagline" );

?>