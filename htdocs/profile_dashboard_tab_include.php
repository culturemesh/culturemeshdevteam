<?php
	include_once 'data/dal_event.php';
	include_once 'data/dal_post.php';
	include_once 'html_builder.php';
?>
<div>
	<h4>EVENTS YOU'RE HOSTING</h4>
	<?php
	$events = Event::getEventsByUserId($_SESSION['uid']);
	foreach($events as $event)
		HTMLBuilder::displayEvent($event);
	?>
</div>
<div>
	<h3>EVENTS IN YOUR NETWORKS</h3>
	<?php
	$events = Event::getEventsByNetworkId($_SESSION['uid']);
	foreach($events as $event)
		HTMLBuilder::displayEvent($event);
	?>
</div>
<div>
	<h3>POSTS TO INTEREST YOU</h3>
</div>
<div>
	<h3>YOUR POSTS</h3>
	<?php
	$posts = Post::getPostsByUserId($_SESSION['uid']);
	foreach($posts as $post)
		HTMLBuilder::displayPost($post);
	?>
</div>
