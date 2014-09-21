<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);
include_once "zz341/fxn.php";
include_once "data/dal_post.php";

// Kick them out if id isn't posted
/*
if (!isset($_POST["id"]))
	exit("Nothing posted");
 */
if (!isset($_POST['id']))
{
$con = getDBConnection();
$posts = Post::getPostsByNetworkId(9, $con);
var_dump($posts);
}

// else do what we're here for
$con = getDBConnection();
$posts = Post::getPostsByNetworkId($_POST["id"], $con);
echo json_encode($posts);
?>
