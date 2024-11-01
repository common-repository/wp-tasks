<?php
class WPTWidget {
    function WPTWidget() {}
    

    function css() {
    ?>
    <style type="text/css">
    #dashboard_tasks ul {
	list-style: none;
	padding-left: 2em;
    
    }
    #dashboard_tasks h5 {
	padding: 0em; margin: 0em;
	line-height: 1em;
	font-size: 1.2em;
    }
    .dashboard_tasks_meta {
	padding: 0em; margin: 0em;
	padding-left: 1.2em;
	
    }
    </style>
    <?php
    }
    
    function html() {
	global $wptasks;
    ?>
    <h4>What to learn next:</h4>
    <br>
    <br>
    <ul id="wpt_widget_tasks">
<?php if ( defined("WPT_DEBUG") ): ?>
<?php $tasks	= $wptasks->all_tasks();  ?>
<?php else: ?>
<?php $tasks	= $wptasks->next_task(); ?>
<?php endif; ?>
<?php if ( $tasks != NULL ): ?>
<?php if ( !is_array($tasks) ): $tasks = array($tasks); endif; ?>
<?php foreach( $tasks as $i => $task ): ?>
	    <?php $this->task($task); ?>
<?php endforeach; ?>
<?php else: ?>
You have completed every task.  Go get 'em, tiger!
<?php endif; ?>
    </ul>
    <?php
    }
    
    function task($task) {
?>
	<li>
	<h5><a href="<?php echo $task->href(); ?>"><?php echo $task->title; ?></a>
	<?php if ( defined("WPT_DEBUG") && $task->_skipped ): echo "(Skipped $task->_skipped)"; endif; ?></h5>
	<a href="#" id="wpt_skip_<?php echo get_class($task); ?>" class="wpt_skip">Skip for now</a>
	<div class="wpt_dashboard_meta"><p>
<?php
	ob_start();
	$task->describe();
	$output	= ob_get_clean();
	print $output;
?></p>
	</div>
	</li>
<?php
    }
}
?>