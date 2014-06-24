<?php
session_name("myDiaspora");
session_start();
if (!isset($_SESSION['uid']))
	exit("Can't be done");

/*
if (!isset($_POST['title']) ||
	!isset($_POST['date']) ||
	!isset($_POST['address_1']) ||
	!isset($_POST['address_2']) ||
	!isset($_POST['description']) ||
	!isset($_POST['id_event']) ||
	!isset($_POST['city']) ||
	!isset($_POST['region']))
	exit("Not all necessary data is in here");
 */

include_once("data/dal_event-dt.php");
include_once("data/dal_event.php");
include_once("zz341/fxn.php");

$con = getDBConnection();
$event = new EventDT();
$event->id = mysqli_real_escape_string($con, $_POST['id_event']);
$event->title = mysqli_real_escape_string($con, $_POST['title']);
$event->event_date = mysqli_real_escape_string($con, $_POST['datetime']);
$event->address_1 = mysqli_real_escape_string($con, $_POST['address_1']);
$event->address_2 = mysqli_real_escape_string($con, $_POST['address_2']);
$event->description = mysqli_real_escape_string($con, $_POST['description']);
$event->city = mysqli_real_escape_string($con, $_POST['city']);
$event->region = mysqli_real_escape_string($con, $_POST['region']);

Event::updateEvent($event, $con);

mysqli_close($con);
header("Location: network.php?id=".$_SESSION['cur_network']);
?>

