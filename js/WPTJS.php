<?php if (!defined("WPT_SCRIPTS_LOADED")): ?>
<?php define(WPT_HIGHLIGHT_COLOR, "#f5f0b2"); ?>

<?php if (isset($_GET['wpt_highlight'])): $_GET['wpt_highlight'] = urldecode(htmlspecialchars_decode($_GET['wpt_highlight'])); endif; ?>
<?php if (isset($_GET['wpt_insertid'])): $_GET['wpt_insertid'] = urldecode(htmlspecialchars_decode($_GET['wpt_insertid'])); endif; ?>

<script type="text/javascript">
(function($){
wpTasks	= {};
wpTasks.ajax	= function(action, id, options ) {
    if ( typeof(options) == "string" ) {
	var string	= options;
	options		= {
	    args: string
	};
    }
    
    var defaultOptions	= {
	callback: null,
	callbackModifies: false,
	callbackReturns: false,
	args: "",
    };
    
    options = $.extend(defaultOptions,options);
	
    $.ajax( {
	    url:  "<?php echo esc_js(admin_url('admin-ajax.php')); ?>",
	    data: "_nonce=<?php echo esc_js(wp_create_nonce('wptasks')); ?>&action=wpt_"+action+"&wpt_tid="+id+options.args,
	    dataType: "json",
	    success: function(dat){
		if ( options.callback != null && typeof(options.callback) == "function" ) {
		    var modifiedDat	= options.callback(dat);
		    if ( options.callbackModifies )
			dat = modifiedDat;
		    if ( options.callbackReturns )
			return;
		}
		
		if ( typeof(dat) != "object" || dat == null )
		    return;
		    
		if ( dat.msg != undefined )
		    wpTasks.displayMsg( dat.msg, dat.id );
		if ( dat.highlight != undefined )
		    wpTasks.addHighlight(dat.highlight);
		else
		    wpTasks.addHighlight();
	    }
    });
}

wpTasks.addHighlight	= function(highlight) {
    $(".wpt_highlight").css("background-color","");
    if ( highlight != undefined ) {
	if ($(highlight).length > 0 ) {
	    $(highlight).css("background-color", "<?php echo esc_js(WPT_HIGHLIGHT_COLOR); ?>");
	    $(highlight).addClass("wpt_highlight");
	}
    }
    else if ( ("<?php if (isset($_GET['wpt_highlight'])): echo esc_js($_GET['wpt_highlight']); endif; ?>").length > 0 ) {
	$("<?php echo esc_js($_GET['wpt_highlight']); ?>").css("background-color", "<?php echo esc_js(WPT_HIGHLIGHT_COLOR); ?>");
	$("<?php echo esc_js($_GET['wpt_highlight']); ?>").addClass("wpt_highlight");
    }
    if( $("#wpt_instruction").length > 0 ) {
	var coords	= $("#wpt_instruction").offset();
	$.scrollTo({top:coords.top-32, left:'+=0'}, 400 );
    }
}

wpTasks.hookSkips	= function() {
    $(".wpt_skip").click(function() {
	$.ajax( {
	    url:  "<?php echo esc_js(admin_url('admin-ajax.php')); ?>",
	    data: "_nonce=<?php echo esc_js(wp_create_nonce('wptasks')); ?>&action=wpt_skip&qid="+$(this).attr("id"),
	    success: function(dat){
		$("#wpt_widget_tasks").fadeOut(400,function(){
		    $(this).html(dat);
		    $(this).slideUp(0);
		    $(this).fadeIn(0, function(){
			$(this).slideDown(800, function() {
			    wpTasks.hookSkips();
			});
		    });
		});
	    }
	});
	return false;
    });
}

wpTasks.displayMsg	= function(msg, id, func) {
    if ( func == undefined )
	func = "after";
    
    var string	= "<div class=\"wpt_message\"><div class=\"updated fade\"  id=\"wpt_instruction\"><p><h5 style=\"padding:0px; margin:0px;\">WordPress Tasks</h5>"+msg+"</p></div></div>";
    $(".updated.fade").remove();
    $("#wpt_instruction").remove();
    if ( id != undefined && id != null && id.length > 0 ) {
	    $(id)[func](string);
    }
    else {
	$(".wrap>h2:eq(0)")[func](string);
    }
    if( $("#wpt_instruction").length > 0 ) {
	var coords	= $("#wpt_instruction").offset();
	$.scrollTo({top:coords.top-60, left:'+=0'}, 400 );
    }
}
wpTasks.ready	= function() {
    wpTasks.addHighlight();
    wpTasks.hookSkips();
    $(document).trigger("wptLoaded");
}
})(jQuery);
</script>

<?php else: // if scripts ARE loaded :) ?>
<script type="text/javascript">
(function($){
    $(document).ready(wpTasks.ready);
})(jQuery);
</script>

<?php endif; ?>