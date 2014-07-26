<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);

session_name('myDiaspora');
session_start();

$POST_INCREMENT = 10;

$json_response = array(
	'error' => NULL,
	'html' => NULL,
	'continue' => NULL,
	'lb' => NULL,
	'ub' => NULL
	);

if (isset($_POST['lb']) && isset($_POST['ub'])
	&& isset($_POST['nid'])) {
	
	// let's move on
	include_once('data/dal_post.php');
	include_once('data/dal_query_handler.php');
	include_once('html_builder.php');

	// get db connection
	$con = QueryHandler::getDBConnection();

	// init relevant variables
	$nid = $_POST['nid'];
	$bounds = array($_POST['lb'], $_POST['ub'] + 1);

	$posts = Post::getPostsByNetworkId($nid, $bounds, $con);

	$replies = array();

	// for each post, check if it's in get
	// get replies from database,
	// push into array
	for ($i = 0; $i < count($posts) && $i < $POST_INCREMENT; $i++) {
		// get replies
		$prs = Post::getRepliesByParentId($posts[$i]->id, $con);
		// push into array
		$replies[$posts[$i]->id] = $prs;
	}


	// close connection
	mysqli_close($con);

	$post_html = '';
	for($i = 0; $i < count($posts) && $i < $POST_INCREMENT; $i++) {
		$post_html .= HTMLBuilder::displayPost($posts[$i], $replies[$posts[$i]->id], 4);
	}

	$json_response['html'] = $post_html;
	$json_response['error'] = 'Success';
	$json_response['continue'] = 'n';

	// if there are more posts to be gotten
	if (count($posts) > $POST_INCREMENT) {
		$json_response['continue'] = 'y';
		$json_response['lb'] = $bounds[0] + $POST_INCREMENT;
		$json_response['ub'] = 10;
	}

	// return stuff
	echo json_encode($json_response);
}
else {
	$json_response['error'] = 'Necessary data not included';
	echo json_encode($json_response);
}
?>
