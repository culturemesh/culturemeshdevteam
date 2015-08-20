<?php
if(!isset($_POST['notification']))
	exit("You don't belong here.");

include 'environment.php';
$cm = new Environment();

session_name($cm->session_name);
session_start();

include_once("data/dal_user_notification-dt.php");
include_once("data/dal_user_notification.php");

$not_dt = new UserNotificationDT();

$not_dt->uid = $_SESSION['uid'];

if (isset($_POST['notify_events_upcoming']))
	$not_dt->events_upcoming = 1;
else
	$not_dt->events_upcoming = 0;

if (isset($_POST['notify_interesting_events']))
	$not_dt->events_interested_in = 1;
else
	$not_dt->events_interested_in = 0;

if (isset($_POST['notify_company_news']))
	$not_dt->company_news = 1;
else
	$not_dt->company_news = 0;

if (isset($_POST['notify_network_activity'])) 
	$not_dt->network_activity = 1;
else
	$not_dt->network_activity = 0;
	
if (UserNotification::updateNotification($not_dt))
	header("Location: profile/$not_dt->uid/?un=success");
else
	header("Location: profile/$not_dt->uid/?un=failure")
?>
