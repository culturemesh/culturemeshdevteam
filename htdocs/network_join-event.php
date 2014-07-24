<?php
if (!isset($_POST['uid']) || !isset($_POST['event_id'])
	|| !isset($_POST['nid']))
  { header("Location: index.php?jeerror=nopost"); }
 
include_once 'data/dal_event_registration.php';
include_once 'data/dal_query_handler.php';

if(EventRegistration::createEventRegistration($_POST['uid'], $_POST['event_id']))
{
	header("Location: network.php?id={$_POST['nid']}&eid={$_POST['event_id']}&jeerror=You joined the event");
}
else
{
	header("Location: network.php?id={$_POST['nid']}&eid={$_POST['event_id']}&jeerror=Server error. Try again later");
}
?>
