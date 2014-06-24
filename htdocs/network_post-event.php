<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include_once "data/dal_event.php";
include_once "data/dal_network_registration.php";

session_name("myDiaspora");
session_start();

$test = new NetworkRegistrationDT();
$test->id_user = $_SESSION['uid'];
$test->id_network = $_SESSION['cur_network'];
$valid = NetworkRegistration::checkRegistration($test);

if ($valid)
{
	$event = new EventDT();
	
	$event->host_id = $_SESSION['uid'];
	$event->network_id = $_SESSION['cur_network'];
	$event->event_date = mysql_escape_string($_POST['datetime']);
	$event->title = mysql_escape_string($_POST['title']);
	$event->address_1 = mysql_escape_string($_POST['address_1']);
	$event->address_2 = mysql_escape_string($_POST['address_2']);
	$event->city = mysql_escape_string($_POST['city']);
	//$event->country = mysql_escape_string($_POST['country']);
	$event->region = mysql_escape_string($_POST['region']);
	$event->description = mysql_escape_string($_POST['description']);
	
	Event::createEvent($event);
	
	header("Location: network.php?id={$_SESSION['cur_network']}");
}
else
{
	header("Location: network.php?id={$_SESSION['cur_network']}&error=false");
}
?>
