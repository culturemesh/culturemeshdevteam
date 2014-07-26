<?php
if (isset($_POST['NOJS'])) {
	$nid = $_POST['nid'];
	$pid = $_POST['pid'];

	// add stuff
	$rids = $_POST['rids'];
	if ($rids == "") {
		$rids.=$pid;
	}
	else {
		// check to see if we're adding something
		// already there
		// ... just in case...
		if (!strpos($rids, $pid))
			$rids .= '+'.$pid;
	}

	header("Location: network.php?id={$nid}&reply={$rids}#post-{$pid}");
}
else {
	// begin session
	session_name("myDiaspora");
	session_start();

	include_once('html_builder.php');
	include_once('data/dal_post.php');

	$response = array(
		'error' => NULL,
		'html' => NULL);

	if (!isset($_POST['pid'])) {
		$response['error'] = 'Not enough information.';	
		echo json_encode($response);
	}

	else {
		$replies = Post::getRepliesByParentId($_POST['pid']);
		$response['error'] = 0;
		$response['html'] = HTMLBuilder::displayReplies($replies);
		echo json_encode($response);
	}
}
?>
