<?php
	//include_once 'data/dal_event_registration.php';
?>
<div>
	<h4>EVENTS YOU'RE HOSTING</h4>
	<ul class='dashboard item'>
		<?php
		$events = Event::getEventsByUserId($_SESSION['uid'], $con);
		foreach($events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
<div>
	<h4>EVENTS YOU'RE ATTENDING</h4>
	<ul class='dashboard item'>
		<?php
		$events = EventRegistration::getEventRegistrationsByUserId($_SESSION['uid'], $con);
		foreach($events as $event)
			HTMLBuilder::displayDashEvent($event);
		?>
	</ul>
</div>
