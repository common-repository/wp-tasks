<?php
#ajax.php - stores all callbacks to XHR requests

add_action( "wp_ajax_wpt_complete", "wpt_ajax_complete" );
add_action( "wp_ajax_wpt_message", "wpt_ajax_message" );
add_action( "wp_ajax_wpt_skip", "wpt_ajax_skip" );

function wpt_ajax_complete() {
    global $wptasks;
    if ( !wp_verify_nonce($_GET['_nonce'], 'wptasks'))
	return;
    
    if ( !isset($_GET['wpt_tid']) )
	return;
    if ( !$_GET['wpt_insertid'] )
	$_GET['wpt_insertid']	= "";
    
    $task	= $wptasks->get($_GET['wpt_tid']);
    
    if ( isset($_GET['wpt_msg'])) {
	ob_start();
	$task->followup();
	echo json_encode(array("msg"=>(ob_get_clean()),"highlight"=>"", "id"=>$_GET['wpt_insertid']));
	wpt_complete(get_class($task));
    }
    else {
	$task->complete();
	echo json_encode(array());
    }
	
    die();
}

function wpt_ajax_message() {
    global $wptasks;
    if ( !wp_verify_nonce($_GET['_nonce'], 'wptasks'))
	return;
    
    if ( !isset($_GET['wpt_tid']) || !isset($_GET['wpt_step']) )
	return;
    
    if ( !isset($_GET['wpt_insertid'] ))
	$_GET['wpt_insertid']	= "";
	
    if ( !isset($_GET['wpt_other']))
	$_GET['wpt_other']	= array();
    
    $task	= $wptasks->get($_GET['wpt_tid']);
    ob_start();
    $task->instruct($_GET['wpt_step']);
    $output	= ob_get_clean();
    
    $arr	= array("msg"=>($output),"id"=>$_GET['wpt_insertid'], "other"=>$_GET['wpt_other']);
    
    if ( isset($_GET['wpt_highlight']))
	$arr['highlight']	= $_GET['wpt_highlight'];
	
    print json_encode($arr);
    die();
}

function wpt_ajax_skip() {
    global $wptasks, $wpt_widget;
    if ( !wp_verify_nonce($_GET['_nonce'], 'wptasks'))
	return;

    if ( !isset($_GET['qid']))
	return;
    
    preg_match("/wpt_skip_(.+)$/",$_GET['qid'], $matches);
    $wptasks->skipped($matches[1]);
    
    $task	= $wptasks->next_task();
    $wpt_widget->task($task);
    
    die();
}
?>