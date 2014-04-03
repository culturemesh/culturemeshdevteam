<?php

include_once "zz341/fxn.php";
include_once "data/dal_post.php";

// Kick them out if id isn't posted
if (!isset($_POST["id"]))
	exit("Nothing posted");
$con = getDBConnection();
$posts = Post::getPostsByNetworkId($id, $con);
echo json_encode($posts);
?>
