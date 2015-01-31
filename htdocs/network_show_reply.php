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

	header("Location: network/{$nid}?reply={$rids}#post-{$pid}");
}
else {
	// begin session
	session_name("myDiaspora");
	session_start();

//	include_once('html_builder.php');
//	include_once('data/dal_post.php');

	$response = array(
		'error' => NULL,
		'html' => NULL);

	if (!isset($_POST['pid'])) {
		$response['error'] = 'Not enough information.';	
		echo json_encode($response);
	}

	else {

		include ('environment.php');
		$cm = new \Environment();

		// get connection
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		// create network
		$network = new \dobj\Network();
		$network->id = (int) $_POST['nid'];

		// get reply by stuff
		//$replies = Post::getRepliesByParentId($id_parent, $con);
		$post = new \dobj\Post();
		$post->id = (int) $_POST['pid'];
		$post->getReplies($dal, $do2db);

		// close connection
		$cm->closeConnection();
		//mysqli_close($con);

		$html = $post->getHTML('replies', array(
				'cm' => $cm,
				'mustache' => new \misc\MustacheComponent(),
				'network' => $network
			)
		);

		$response['error'] = 0;
		$response['html'] = $html;
		echo json_encode($response);

		/*
		$replies = Post::getRepliesByParentId($_POST['pid']);
		$response['error'] = 0;
		$response['html'] = HTMLBuilder::displayReplies($replies);
		echo json_encode($response);
		 */
	}
}
?>
