<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);


// check for posted variables
if (isset($_POST['uid']) && isset($_POST['nid']))
{
	// include network registration
	include_once("data/dal_network_registration.php");

	// delete registration
	if(NetworkRegistration::deleteNetRegistration($_POST['uid'], $_POST['nid']))
	{
		// success path!
		header("Location: network.php?id={$_POST['nid']}&lnerror=Sorry to see you go.");
	}
	else
	{
		// fail path
		header("Location: network.php?id={$_POST['nid']}&lnerror=Server error. Try later.");
	}
}
else
{
	// return to homepage, we don't know
	// where they should be
	header("Location: index.php");
}
?>
