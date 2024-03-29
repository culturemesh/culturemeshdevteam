<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include 'environment.php';
$cm = new Environment();

include_once "data/dal_network_registration.php";

session_name($cm->session_name);
session_start();

$netreg = new NetworkRegistrationDT();

$netreg->id_user = $_SESSION['uid'];
$netreg->id_network = $_SESSION['cur_network'];

NetworkRegistration::createNetRegistration($netreg);

header("Location: network/{$_SESSION['cur_network']}/?jnerror=Welcome to the network!");

?>
