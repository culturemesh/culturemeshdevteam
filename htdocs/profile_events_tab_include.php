<?php
	//include_once 'data/dal_event_registration.php';
?>
<div>
	<h5>EVENTS YOU'RE HOSTING</h5>
	<ul class='dashboard item'>
		<?php
		foreach($yh_events as $event)
			HTMLBuilder::displayDashEvent($event, true);
		?>
	</ul>
</div>
<div>
	<h5>EVENTS YOU'RE ATTENDING</h5>
	<ul class='dashboard item'>
		<?php
		foreach($ya_events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
