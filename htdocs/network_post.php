<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include_once "data/dal_post.php";
include_once "data/dal_network_registration.php";

session_name("myDiaspora");
session_start();


$test = new NetworkRegistrationDT();
$test->id_user = $_SESSION['uid'];
$test->id_network = $_SESSION['cur_network'];
$valid = NetworkRegistration::checkRegistration($test);


if ($valid)
{

	$post = new PostDT();
	
	$post->id_user = $_SESSION['uid'];
	$post->id_network = $_SESSION['cur_network'];
	$post->post_text = mysql_escape_string($_POST['post_text']);
	$post->post_class = mysql_escape_string($_POST['post_class']);
	$post->post_original = mysql_escape_string($_POST['post_original']);
	
	Post::createPost($post);
	
//	header("Location: network.php?id={$_SESSION['cur_network']}");

}
else
{
	header("Location: network.php?id={$_SESSION['cur_network']}&success=false");
}
?>
