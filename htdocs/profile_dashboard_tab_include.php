<?php
	//include_once 'data/dal_event.php';
	//include_once 'data/dal_post.php';
	//include_once 'html_builder.php';
?>
<div>
	<h5>EVENTS YOU'RE HOSTING</h5>
	<ul class="dashboard item">
		<?php
		$events = Event::getEventsByUserId($_SESSION['uid'], $con);
		foreach($events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
<div>
	<h5>EVENTS IN YOUR NETWORKS</h5>
	<ul class="dashboard item">
		<?php
		$events = Event::getEventsByNetworkId($_SESSION['uid'], $con);
		foreach($events as $event)
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
		$posts = Post::getPostsByUserId($_SESSION['uid'], $con);
		foreach($posts as $post)
			HTMLBuilder::displayDashPost($post);
		?>
	</ul>
</div>
