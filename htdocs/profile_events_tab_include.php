<?php
	//include_once 'data/dal_event_registration.php';
?>
<div>
	<h5>EVENTS YOU'RE HOSTING</h5>
	<ul class='dashboard item'>
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
	<h5>EVENTS YOU'RE ATTENDING</h5>
	<ul class='dashboard item'>
		<?php
		// first network
		$cur_network = findNetwork($ya_events[0]->id_network, $yn_networks);
		// get network title
		HTMLBuilder::displayDashNetworkTitle($cur_network);

		foreach($ya_events as $event) {
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
