<?php

include 'environment.php';
$cm = new Environment();

// turned off error display
// start session for reply thing
session_name($cm->session_name);
session_start();

$response = array(
	'error' => NULL,
	'status' => NULL,
	'html' => NULL);

// handle the nojs way
// if pid is set, we can continue
if (isset($_POST['pid']) && isset($_POST['nid']) && isset($_POST['replies'])) {

	// important includes
	include_once("data/dal_post.php");
	include_once("data/dal_query_handler.php");
	include_once("html_builder.php");

	// stuff
	$pid = $_POST['pid'];
	$nid = $_POST['nid'];
	$nrs = $_POST['replies'];

	// if there are no replies, check db to see if we
	// can delete the entire post 
	$con = QueryHandler::getDBConnection();

	// check count in database
	$replies = Post::getRepliesByParentId($pid, $con);
	$nrs = count($replies);

	// free we are to destroy the post
	if ( $nrs == 0 ) {
		if(Post::deletePost($_POST['pid'], $con)) {
			// close connection
			mysqli_close($con);

			if (isset($_POST['NOJS'])) {
				header("Location: network/{$_POST['nid']}/?dp=true");
			}
			else {
				$response['error'] = 0;
				$response['status'] = 'destroyed';
				echo json_encode($response);
			}
		}
		else {
			if (isset($_POST['NOJS'])) {
				header("Location: network.php?id={$_POST['nid']}&dp=false");
			}
			else {
				echo json_encode($response);
			}
		}
	}
	else {
		// just wipe the post
		if (Post::wipePost($pid, $con)) {
			if (isset($_POST['NOJS'])) {
				mysqli_close($con);
				header("Location: network.php?id={$_POST['nid']}&dp=true");
			}
			else {
				// now get post and get replies for replacing with ajax
				$replies = NULL;
				$post = Post::getPostById($pid, $con);

				$response['error'] = 0;
				$response['status'] = 'wiped';
				$response['html'] = HTMLBuilder::displayPost($post, $replies);

				mysqli_close($con);
				echo json_encode($response);
			}
		}
		else {

				mysqli_close($con);
			if (isset($_POST['NOJS'])) {
				header("Location: network.php?id={$_POST['nid']}&dp=false");
			}
			else {
				$response['error'] = 'Could not wipe post';
				echo json_encode($response);
			}
		}
	}
}
// else, nothing to do
else {
	if (isset($_POST['NOJS'])) {
		header("Location: index.php");
	}
	else {
		$response['error'] = 'Nothing has been posted';
		echo json_encode($response);
	}
}
?>
