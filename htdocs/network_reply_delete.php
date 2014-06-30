<?php
// handle the nojs way
if (isset($_POST['NOJS']))
{
	// if pid is set, we can continue
	if (isset($_POST['rid']) && isset($_POST['nid'])) {
		include("data/dal_post.php");
		if(Post::deleteReply($_POST['rid'])) {
			header("Location: network.php?id={$_POST['nid']}&dr=true");
		}
		else {
			header("Location: network.php?id={$_POST['nid']}&dr=false");
		}
	}
	// else, nothing to do
	else {
		header("Location: index.php");
	}
}
?>
