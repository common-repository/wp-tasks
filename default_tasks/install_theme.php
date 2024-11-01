<?php
function install_theme() {    
    class install_theme extends WPTTask {
	var $id		= "install_theme";
	var $title	= "Install/Activate a Theme";
	var $completed	= "Installed/Activated a Theme";
	
	function init() {
	    $this->title		= __('Install a Theme', 'wptasks');
	    $this->completed	= __('Installed a Theme', 'wptasks');
	}
	
	function links() {
	    return array(
		array("url"=>"theme-install.php", "highlight" => ".subsubsub li:gt(1)" ),
		array("url"=>"theme-install.php?tab=featured", "highlight"=>".action-links a:first-child"),
		array("url"=>"theme-install.php?tab=search", "highlight"=>".action-links a:first-child"),
		array("url"=>"theme-install.php?tab=new", "highlight"=>".action-links a:first-child"),
		array("url"=>"theme-install.php?tab=updated", "highlight"=>".action-links a:first-child"),
		array("url"=>"update.php?action=install-theme", "highlight"=>"p .activatelink"),
		array("url"=>"update.php?action=upgrade-theme", "highlight"=>"p .activatelink")
		
	    );
	}
	
	function requires() {
	    return array(
		"install_themes"
	    );
	}
	
	function describe() {
		_e('Your blogs \'theme\' is how your blog appears visually.  Think of your comment as a painting, and the theme is what frames your painting.  Themes are of course, very important and often something we choose to personally associate with, be it a sports theme, minimal theme or nature theme.', 'wptasks');
	}
	
	function followup() {
		$link1	= "<a href=\"http://codex.wordpress.org/Using_Themes/Theme_List\">http://codex.wordpress.org/Using_Themes/Theme_List</a>";
		$link2	= "<a href=\"http://codex.wordpress.org/Appearance_Themes_SubPanel\">http://codex.wordpress.org/Appearance_Themes_SubPanel</a>";
		$link3	= "<a href=\"http://wordpress.org/extend/themes/\">http://wordpress.org/extend/themes/</a>";
		printf(__('Great!  You now have this theme installed.  Make sure to hit the "activate" link below if you would like it as your new theme.  You can read more about themes below: <br>
			  Using Themes - %s <br>
			  Appearance/Themes SubPanel - %s <br>
			  Themes Directory - %s', 'wptasks'), $link1, $link2, $link3);
	}
	
	function instruct($switch=-1) {
	    switch ( $switch ):
		default:
		break;
		case 0:
		    _e('Click on any one of the highlighted tabs below to see some of the themes listed on WordPress.org', 'wptasks' );
		break;
		case 1:
		case 2:
		case 3:
		    _e('Click on any of the "Install" links below to install the theme to your blog.  You may also hit "Preview" to see a full glimpse of the theme.', 'wptasks');
	    endswitch;
	}
	
	// function to establish WP hooks
	function hook() {
	    $referer	= $this->parse_url( wp_get_referer() );
	    $page	= $this->parse_url( $_SERVER['REQUEST_URI']);
	    if ( $referer['file'] == "theme-install.php" && (preg_match("/action=install-theme/", $page['query'] ) || preg_match("/action=upgrade-theme/", $page['query'] ) ) )
		$this->complete();
	}
    }
    
    wpt_register(__FUNCTION__);
}

add_action( "wpt_init", "install_theme" );

?>