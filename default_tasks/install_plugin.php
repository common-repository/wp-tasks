<?php
function install_plugin() {    
    class install_plugin extends WPTTask {
	var $id		= "install_plugin";
	var $title	= "Activate a Plugin";
	var $completed	= "Activated a Plugin";
	
	function init() {
	    $this->title		= __('Activate a Plugin', 'wptasks');
	    $this->completed	= __('Activated a Plugin', 'wptasks');
	}
	
	// function to establish WP hooks
	function hook() {
	    $referer	= $this->parse_url( wp_get_referer() );
	    $page	= $this->parse_url( $_SERVER['REQUEST_URI']);
	    if ( $referer['file'] == "plugin-install.php" && (preg_match("/action=install-plugin/", $page['query'] ) || preg_match("/action=upgrade-plugin/", $page['query'] ) ) )
		$this->complete();
	}
	
	function requires() {
	    return array(
		"install_plugins"
	    );
	}
	
	function links() {
	    return array(
		array("url"=>"plugin-install.php", "highlight"=>".subsubsub li:gt(1)" ),
		array("url"=>"plugin-install.php?tab=featured", "highlight"=>".install-now" ),
		array("url"=>"plugin-install.php?tab=popular", "highlight"=>".install-now" ),
		array("url"=>"plugin-install.php?tab=new", "highlight"=>".install-now" ),
		array("url"=>"plugin-install.php?tab=updated", "highlight"=>".install-now" ),
		array("url"=>"update.php" ),
		
	    );
	}
	
	function describe() {
		_e('Plugins are just one of the reasons WordPress is awesome.  Because it is open-source, WordPress enjoys thousands of user-contributed modifications that enhance, modify or extend default WordPress functionality.  This task will walk you through activating and installing a plugin.', 'wptasks');
	}
	
	function followup() {
	    $link1	= "<a href=\"http://codex.wordpress.org/Plugins\">http://codex.wordpress.org/Plugins</a>";
	    $link2	= "<a href=\"http://wordpress.org/extend/plugins/\">http://wordpress.org/extend/plugins/</a>";
		printf(__('There are many more plugins to discover on the WordPress.org website.  If you can think of a neat and different thing to do with your blog, there\'s a good chance someone has made a plugin for it already.<br><br>
		    Plugin Directory(Plugins) - %s', 'wptasks'), $link1, $link2 );
	}
	
	function instruct($switch) {
	    switch($switch):
		case 0:
		    _e('Click on any one of the highlighted tabs below.  These will allow you to browse plugins listed on WordPress.org.', 'wptasks');
		    break;
		case 1:
		case 2:
		case 3:
		case 4:
		    _e('Great.  Now feel free to browse through the plugins.  When you find one you like, click the install button.', 'wptasks');
		    break;
	    endswitch;
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "install_plugin" ); 

?>