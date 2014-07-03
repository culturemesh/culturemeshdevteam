<?php
	//include_once 'data/dal_event.php';
	//include_once 'data/dal_post.php';
	//include_once 'html_builder.php';
?>
<div>
	<h5><b>EVENTS YOU'RE HOSTING</b></h5>
	<ul class="dashboard item">
		<?php
		// first network
		$cur_network = findNetwork($yh_events[0]->id_network, $yn_networks);
		// get network title
		HTMLBuilder::displayDashNetworkTitle($cur_network);

		foreach($yh_events as $event) {
			if ($cur_network->id !== $event->id_network) {
				$cur_network = findNetwork($event->id_network, $yn_networks);
				// change network title
				// print
				HTMLBuilder::displayDashNetworkTitle($cur_network);
			}

			// display event
			HTMLBuilder::displayDashEvent($event, true);
		}

		?>
	</ul>
</div>
<div>
	<h5><b>EVENTS IN YOUR NETWORKS</b></h5>
	<ul class="dashboard item">
		<?php
		// first network
		$cur_network = findNetwork($yn_events[0]->id_network, $yn_networks);
		// get network title
		HTMLBuilder::displayDashNetworkTitle($cur_network);

		foreach($yn_events as $event) {
			if ($cur_network->id !== $event->id_network) {
				$cur_network = findNetwork($event->id_network, $yn_networks);
				// change network title
				// print
				HTMLBuilder::displayDashNetworkTitle($cur_network);
			}

			// display Event
			HTMLBuilder::displayDashEvent($event);
		}
		?>
	</ul>
</div>
<!--
<div>
	<h5>POSTS TO INTEREST YOU</h5>
	<ul class="dashboard item"></ul>
</div>
-->
<div>
	<h5><b>YOUR POSTS</b></h5>
	<ul class="network">
		<?php
		// first network
		$cur_network = findNetwork($yp_posts[0]->id_network, $yn_networks);
		// get network title
		HTMLBuilder::displayDashNetworkTitle($cur_network);

		foreach($yp_posts as $post) {
			// check network
			if ($cur_network->id !== $post->id_network) {
				// change network
				$cur_network = findNetwork($post->id_network, $yp_networks);

				// display network
				HTMLBuilder::displayDashNetworkTitle($cur_network);
			}
				
			// display post
			HTMLBuilder::displayDashPost($post, true);
		}
		?>
	</ul>
</div>
