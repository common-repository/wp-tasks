<?php
function change_default_post_category() {    
    class change_default_post_category extends WPTTask {
	var $id		= "change_default_post_category";
	
	function init() {
	    $this->title		= __('Change the default post category', 'wptasks');
	    $this->completed	= __('Changed the default post category', 'wptasks');
	}
	
	// the task object calls this after hooking and receiving the callback we set
	function default_post_category_changed($old_value, $new_value) {
	    $this->complete();
	}
	
	function requires() {
	    return array(
		"manage_options"
	    );
	}
	
	// function to establish WP hooks
	function hook() {
	    $this->add_hook("update_option_default_category", "default_post_category_changed", 10, 2);
	}
	
	function links() {
	    return array(
		array("url"=>"options-writing.php", "highlight"=>".form-table tr:eq(2)")
	    );
	}
	
	function describe() {
		_e('By *default*, your default post category is uncategorized.  If you don\'t utilize categories, this may not be of particular concern.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Glossary#Category\">http://codex.wordpress.org/Glossary#Category</a>";
	    $link2	= "<a href=\"http://codex.wordpress.org/Posts_Categories_SubPanel\">http://codex.wordpress.org/Posts_Categories_SubPanel</a>";
		printf(__('Your default category is now %s.  Next time you post without specifying a category, your post will be filed under your default category.<br><br>
    Glossary(Catgegories) - %s<br>
    Manage Categories SubPanel - %s', 'wptasks'), get_cat_name(get_option("default_category")), $link1, $link2);
	}
	
	function instruct() {
	    _e('Change the drop down box to whatever category you\'d like your default post category to be.', 'wptasks');
	}
    }
    
    wpt_register(__FUNCTION__, true);
}

add_action( "wpt_init", "change_default_post_category" );

?>