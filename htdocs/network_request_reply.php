<?php
$nid = $_POST['nid'];
$pid = $_POST['pid'];

if (isset($_POST['NOJS'])) {
	header("Location: network.php?id={$nid}&pid={$pid}#post-{$pid}");
}
else {
	ini_set('display_errors', false);
	include_once('html_builder.php');
	
	include_once 'Environment.php';
	// get user and post
	$uid = $_POST['uid'];
	$pid = $_POST['pid'];
	$nid = $_POST['nid'];

	$response = array(
		'error' => NULL,
		'html' => NULL);

	$response['error'] = 0;
	$response['html'] = HTMLBuilder::displayReplyPrompt($pid, $uid, $nid, \Environment::host_root());

	echo json_encode($response);
}
?>
