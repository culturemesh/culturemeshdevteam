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
		$cur_network = findNetwork($yh_events[0]->id_network, $ye_networks);
		// get network title
		echo HTMLBuilder::displayDashNetworkTitle($cur_network);

		// for each event
		foreach($yh_events as $event) {
			if ($cur_network->id !== $event->id_network) {
				$cur_network = findNetwork($event->id_network, $ye_networks);
				// change network title
				// print
				echo HTMLBuilder::displayDashNetworkTitle($cur_network);
			}

			// display event
			echo HTMLBuilder::displayDashEvent($event, true);
		}

		?>
	</ul>
</div>
<div>
	<h5><b>EVENTS IN YOUR NETWORKS</b></h5>
	<ul class="dashboard item">
		<?php
		// first network
		$cur_network = findNetwork($yn_events[0]->id_network, $ye_networks);
		// get network title
		echo HTMLBuilder::displayDashNetworkTitle($cur_network);

		foreach($yn_events as $event) {
			if ($cur_network->id !== $event->id_network) {
				$cur_network = findNetwork($event->id_network, $ye_networks);
				// change network title
				// print
				echo HTMLBuilder::displayDashNetworkTitle($cur_network);
			}

			// display Event
			echo HTMLBuilder::displayDashEvent($event);
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
	<ul id='your-posts' class="network">
		<?php
		// first network
		$cur_network = findNetwork($yp_posts[0]->id_network, $yn_networks);
		// get network title
		echo HTMLBuilder::displayDashNetworkTitle($cur_network);

		$count = 0;
		foreach($yp_posts as $post) {
			if ($count >= 10)
				break;

			// check network
			if ($cur_network->id !== $post->id_network) {
				// change network
				$cur_network = findNetwork($post->id_network, $yp_networks);

				// display network
				echo HTMLBuilder::displayDashNetworkTitle($cur_network);
			}
				
			// display post
			echo HTMLBuilder::displayDashPost($post, true);

			$count++;
		}
		?>
	</ul>

	<?php if (count($yp_posts) >= $test_bounds[1]) : ?>
	<form id='more-posts' action='profile_operations.php' method='POST' />
		<input type='hidden' name='more_posts' value='true' />
		<input type='hidden' name='uid' value='<?php echo $_SESSION['uid']; ?>' />
		<input type='hidden' id='lb' name='lb' value='10' />
		<input type='hidden' id='nid' name='nid' value='<?php echo $yp_posts[10]->id_network; ?>' />
		<button id="mp_button" class="post show">Load More Posts</button>
	</form>
	<?php endif; ?>
	<script>
		// necessary for some reason
		$('#lb').val(10);

		// more posts form submit event
		$('#more-posts').on('submit', function(e) {
			// prevent form submission
			e.preventDefault();
		
			var postForm = $( e.target ).serialize();

			var ajaxRequest = new Ajax({
					requestType: 'POST',
					requestUrl: 'profile_operations.php',
					requestHeaders: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
				}, function(data) {
					var response = JSON.parse(data);

					if (response['error'] == 'success') {
						// stuff
						$('#your-posts').append(response['html']);

						if (response['continue'] == 'y') {
							$('#lb').val(response['lb']);
						}
						else {
							$('#mp_button').hide();
						}
					}
				}, function() {
				});
		});

	</script>
</div>
