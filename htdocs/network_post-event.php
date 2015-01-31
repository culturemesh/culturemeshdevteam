<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL ^ E_NOTICE);

include_once("data/dal_query_handler.php");
include_once "data/dal_event.php";
include_once "data/dal_network_registration.php";

session_name("myDiaspora");
session_start();

$test = new NetworkRegistrationDT();
$test->id_user = $_SESSION['uid'];
$test->id_network = $_SESSION['cur_network'];
$valid = NetworkRegistration::checkRegistration($test);

// create redirect


if ($valid)
{
	$event = new EventDT();
	$con = QueryHandler::getDBConnection();
	
	$event->host_id = $_SESSION['uid'];
	$event->network_id = $_SESSION['cur_network'];
	$event->event_date = mysqli_real_escape_string($con, $_POST['datetime']);
	$event->title = mysqli_real_escape_string($con, $_POST['title']);
	$event->address_1 = mysqli_real_escape_string($con, $_POST['address_1']);
	$event->address_2 = mysqli_real_escape_string($con, $_POST['address_2']);
	$event->city = mysqli_real_escape_string($con, $_POST['city']);
	//$event->country = mysqli_real_escape_string($con, $_POST['country']);
	$event->region = mysqli_real_escape_string($con, $_POST['region']);
	$event->description = mysqli_real_escape_string($con, $_POST['description']);
	
	if (strlen($event->title) > 50) {
		mysqli_close($con);
		$msg = "Event title too long. Must be 50 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->title) == 0) {
		mysqli_close($con);
		$msg = "You must include a title.";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->address_1) > 40) {
		mysqli_close($con);
		$msg = "Address 1 too long. Must be 40 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->address_1) == 0) {
		mysqli_close($con);
		$msg = "You must include the first line of an address.";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->address_2) > 30) {
		mysqli_close($con);
		$msg = "Address 2 too long. Must be 30 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}

	else if (strlen($event->city) > 50) {
		mysqli_close($con);
		$msg = "City too long. Must be 50 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->city) == 0) {
		mysqli_close($con);
		$msg = "You must include a city.";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->region) > 50) {
		mysqli_close($con);
		$msg = "Region too long. Must be 50 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->region) == 0) {
		mysqli_close($con);
		$msg = "You must include a region.";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}
	else if (strlen($event->description) > 500) {
		mysqli_close($con);
		$msg = "Description too long. Must be 500 characters or less";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}

	else if (strlen($event->description) == 0) {
		mysqli_close($con);
		$msg = "You must include a description.";
		header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
	}

	else {
		// create event
		if(Event::createEvent($event, $con)) {
			mysqli_close($con);
			header("Location: network/{$_SESSION['cur_network']}/?eperror=Success");
		}
		else
		{
			mysqli_close($con);
			$msg = "Unable to save your event. Try again later.";
			header("Location: network/{$_SESSION['cur_network']}/?eperror={$msg}");
		}
	}

}
else
{
	header("Location: network.php/{$_SESSION['cur_network']}/?eperror=Not a member");
}
?>

