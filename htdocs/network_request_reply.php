<?php

if (isset($_POST['NOJS'])) {

	if (isset($_POST['pid']))
		$pid = $_POST['pid'];

	if (isset($_POST['tid']))
		$tid = $_POST['tid'];

	$nid = $_POST['nid'];

	header("Location: network.php?id={$nid}&pid={$pid}#post-{$pid}");
}
else {
	ini_set('display_errors', false);
	include_once('html_builder.php');
	
	include_once 'environment.php';

	$cm = new \Environment();
	$m_comp = new \misc\MustacheComponent();
	$template = file_get_contents($cm->template_dir . $cm->ds . 'network-reply-prompt.html');

	// get user and post
	$uid = $_POST['uid'];
	$email = $_POST['email'];
	$pid = $_POST['pid'];
	$nid = $_POST['nid'];
	$tid = $_POST['tid'];

	$response = array(
		'error' => NULL,
		'html' => NULL);

	if (isset($tid)) {
		$post = NULL;
		$tweet = array(
			'text' => $_POST['tweet_text'],
			'date' => $_POST['tweet_date'],
			'name' => $_POST['name'],
			'screen_name' => $_POST['screen_name'],
		        'profile_image' => $_POST['profile_image']
		);

		$action = 'network_tweet_reply.php';
	}

	if (isset($pid)) {
		$post = True;
		$tweet = NULL;
		$action = 'network_post_reply.php';
	}

	$response['html'] = $m_comp->render($template, array(
		'cm' => $cm,
		'post' => $post,
		'tweet' => $tweet,
		'email' => $email,
		'uid' => $uid,
		'pid' => $pid,
		'nid' => $nid,
		'tid' => $tid,
		'action' => $action
	));

	$response['error'] = 0;

	echo json_encode($response);
}
?>
