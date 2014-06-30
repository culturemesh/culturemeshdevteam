<?php
// handle the nojs way
if (isset($_POST['NOJS']))
{
	// if pid is set, we can continue
	if (isset($_POST['pid']) && isset($_POST['nid'])) {
		include("data/dal_post.php");
		if(Post::deletePost($_POST['pid'])) {
			header("Location: network.php?id={$_POST['nid']}&dp=true");
		}
		else {
			header("Location: network.php?id={$_POST['nid']}&dp=false");
		}
	}
	// else, nothing to do
	else {
		header("Location: index.php");
	}
}
?>
