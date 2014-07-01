<?php
//	ini_set('display_errors', true);
//	error_reporting(E_ALL ^ E_NOTICE);
include_once("data/dal_post.php");
include_once("data/dal_query_handler.php");
include_once('html_builder.php');
session_name('myDiaspora');
session_start();

$response = array(
	'error' => NULL,
	'html' => NULL);

// get post values
$con = QueryHandler::getDBConnection();
$text = mysqli_real_escape_string($con, $_POST['reply_text']);
$nid = mysqli_real_escape_string($con, $_POST['nid']);
$uid = mysqli_real_escape_string($con, $_POST['uid']);
$id_parent = mysqli_real_escape_string($con, $_POST['id_parent']);

// can't figure out network
if ($nid == "") {
	mysqli_close($con);
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
	mysqli_close($con);
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
	// set up post
	$success = Post::createReply($text, $nid, $uid, $id_parent, $con);
	
	// redirect to main page
	if($success)
	{
		if( isset($_POST['NOJS']) ) {
			// close connection
			mysqli_close($con);

			header("Location: network.php?id={$nid}");
		}
		else {
			// get reply by stuff
			$replies = Post::getRepliesByParentId($id_parent, $con);
			
			// close connection
			mysqli_close($con);

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
}
?>
