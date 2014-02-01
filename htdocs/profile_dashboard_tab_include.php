<?php
	//include_once 'data/dal_event.php';
	//include_once 'data/dal_post.php';
	//include_once 'html_builder.php';
?>
<div>
	<h4>EVENTS YOU'RE HOSTING</h4>
	<ul class="dashboard item">
		<?php
		$events = Event::getEventsByUserId($_SESSION['uid'], $con);
		foreach($events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
<div>
	<h3>EVENTS IN YOUR NETWORKS</h3>
	<ul class="dashboard item">
		<?php
		$events = Event::getEventsByNetworkId($_SESSION['uid'], $con);
		foreach($events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
<div>
	<h3>POSTS TO INTEREST YOU</h3>
</div>
<div>
	<h3>YOUR POSTS</h3>
	<ul class="network">
		<?php
		$posts = Post::getPostsByUserId($_SESSION['uid'], $con);
		foreach($posts as $post)
			HTMLBuilder::displayDashPost($post);
		?>
	</ul>
</div>
