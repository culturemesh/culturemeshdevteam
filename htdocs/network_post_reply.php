<?php

include('environment.php');
$cm = new \Environment();

session_name('myDiaspora');
session_start();

$response = array(
	'error' => NULL,
	'html' => NULL);

// get post values
//$con = QueryHandler::getDBConnection();
$text = strip_tags($_POST['reply_text']);
$nid = (int) $_POST['nid'];
$uid = (int) $_POST['uid'];
$id_parent = (int) $_POST['id_parent'];

// can't figure out network
if ($nid == "") {
	//mysqli_close($con);
	if( isset($_POST['NOJS']) ) {
		header("Location: index.php");
	}
	else {
		$response['error'] = 'We don\'t know the network';
		echo json_encode($response);
	}
}

// can't figure out user or parent post
else if ($uid == "" || $id_parent == "") {
	//mysqli_close($con);
	$msg = 'Not enough information to send reply';
	if( isset($_POST['NOJS']) ) {
		header("Location: network.php?id={$nid}&rperror={$msg}");
	}
	else {
		$response['error'] = 'Not enough information to send reply';
		echo json_encode($response);
	}
}
else {
	$reply = new \dobj\Reply();
	$reply->id_parent = $id_parent;
	$reply->reply_text = $text;
	$reply->id_network = $nid;
	$reply->id_user = $uid;

	// set up post
	//$success = Post::createReply($text, $nid, $uid, $id_parent, $con);
	
	// get connection
	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();
	$id = $reply->insert($dal, $do2db);

	if ($id) {

		if( isset($_POST['NOJS']) ) {
			header("Location: network/{$nid}");
		}
		else {

			// create network
			$network = new \dobj\Network();
			$network->id = $nid;

			// get reply by stuff
			//$replies = Post::getRepliesByParentId($id_parent, $con);
			$post = new \dobj\Post();
			$post->id = $id_parent;
			$post->getReplies($dal, $do2db);

			// emails
			//$em = new cm\EmailManager();
			
			// close connection
			$cm->closeConnection();
			//mysqli_close($con);

			$html = $post->getHTML('replies', array(
					'cm' => $cm,
					'mustache' => new \misc\MustacheComponent(),
					'network' => $network
				)
			);

			if ($html != NULL) {
				$response['html'] = $html;
				$response['error'] = 0;
				echo json_encode($response);
			}
			else {
				$response['error'] = 1;
				echo json_encode($response);
			}
		}
	}
	else {
		$msg = "Could not post reply. Try again later.";
		if( isset($_POST['NOJS']) ) {
			header("Location: network/{$nid}/?rperror={$msg}");
		}
		else {
			$response['error'] = $msg;
			echo json_encode($response);
		}
	}
	/*
	// redirect to main page
	if($success)
	{
		if( isset($_POST['NOJS']) ) {
			// close connection
			//mysqli_close($con);

			header("Location: network.php?id={$nid}");
		}
		else {
			// get reply by stuff
			$replies = Post::getRepliesByParentId($id_parent, $con);
			
			// close connection
			//mysqli_close($con);

			if ($replies != NULL) {
				$response['html'] = HTMLBuilder::displayReplies($replies);
				$response['error'] = 0;
				echo json_encode($response);
			}
			else {
				$response['error'] = 1;
				echo json_encode($response);
			}
		}
	}
	else {
		$msg = "Could not post reply. Try again later.";
		if( isset($_POST['NOJS']) ) {
			header("Location: network.php?id={$nid}&rperror={$msg}");
		}
		else {
			$response['error'] = $msg;
			echo json_encode($response);
		}
	}
	 */
}
?>
