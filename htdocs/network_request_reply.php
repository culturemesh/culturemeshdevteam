<?php
$nid = $_POST['nid'];
$pid = $_POST['pid'];

if (isset($_POST['NOJS'])) {
	header("Location: network.php?id={$nid}&pid={$pid}#post-{$pid}");
}
else {
	include_once('html_builder.php');
	
	// get user and post
	$uid = $_POST['uid'];
	$pid = $_POST['pid'];
	$nid = $_POST['nid'];

	$response = array(
		'error' => NULL,
		'html' => NULL);

	$response['error'] = 0;
	$response['html'] = HTMLBuilder::displayReplyPrompt($pid, $uid, $nid);

	echo json_encode($response);
}
?>
