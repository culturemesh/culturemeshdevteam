<?php
	//include_once 'data/dal_event.php';
	//include_once 'data/dal_post.php';
	//include_once 'html_builder.php';
?>
<div>
	<h5>EVENTS YOU'RE HOSTING</h5>
	<ul class="dashboard item">
		<?php
		foreach($yh_events as $event)
			HTMLBuilder::displayDashEvent($event, true);
		?>
	</ul>
</div>
<div>
	<h5>EVENTS IN YOUR NETWORKS</h5>
	<ul class="dashboard item">
		<?php
		foreach($yn_events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
<div>
	<h5>POSTS TO INTEREST YOU</h5>
	<ul class="dashboard item"></ul>
</div>
<div>
	<h5>YOUR POSTS</h5>
	<ul class="network">
		<?php
		foreach($yp_posts as $post)
			HTMLBuilder::displayDashPost($post, true);
		?>
	</ul>
</div>
