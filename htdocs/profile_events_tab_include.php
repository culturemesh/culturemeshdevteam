<?php
	include_once 'data/dal_event_registration.php';
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
	<h4>EVENTS YOU'RE ATTENDING</h4>
	<?php
	$events = EventRegistration::getEventRegistrationsByUserId($_SESSION['uid']);
	foreach($events as $event)
		HTMLBuilder::displayEvent($event);
	?>
</div>
