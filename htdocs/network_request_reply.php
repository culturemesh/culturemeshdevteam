<?php
$nid = $_POST['nid'];
$pid = $_POST['pid'];

if (isset($_POST['NOJS'])) {
	header("Location: network.php?id={$nid}&pid={$pid}#post-{$pid}");
}
else {
	ini_set('display_errors', false);
	include_once('html_builder.php');
	
	include_once 'environment.php';

	$cm = new \Environment();
	$m_comp = new \misc\MustacheComponent();
	$template = $cm->template_dir . $cm->ds . 'network-reply-prompt.html';

	// get user and post
	$uid = $_POST['uid'];
	$pid = $_POST['pid'];
	$nid = $_POST['nid'];


	if (isset($tid)) {

	}

	if (isset($pid)) {
		$response['html'] = HTMLBuilder::displayReplyPrompt($pid, $uid, $nid, \Environment::host_root());
	}

	$response = array(
		'error' => NULL,
		'html' => NULL);

	$response['error'] = 0;

	echo json_encode($response);
}
?>
