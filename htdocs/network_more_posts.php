<?php
ini_set('display_errors', false);
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
	

		if ($_POST['direction'] == 'older') {

			$network->getOlderPostsFromId($dal, $do2db, $pid, $_POST['lb'], $_POST['ub']);
		}

		if ($_POST['direction'] == 'newer') {

			$network->getNewerPostsFromId($dal, $do2db, $pid, $_POST['lb'], $_POST['ub']);
		}

		include 'environment.php';

		$cm = new \Environment();

		// add one to the upper bound so that we can check if there
		// are more posts
		$bounds = array($_POST['lb'], $_POST['ub'] + 1);

		$network = new \dobj\Network();
		$network->id = (int) $_POST['nid'];

		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		$site_user = \dobj\User::createFromId((int) $_SESSION['uid'], $dal, $do2db);

		$network->getPosts($dal, $do2db, (int ) $bounds[0], (int) $bounds[1]);
		$cm->closeConnection();

		/////// make components //////////
		$m_comp = new \misc\MustacheComponent();

		// set network stuff
		$network->posts->setMustache($m_comp);

		try
		{
			$p_html = $network->posts->getHTML('network', array(
				'cm' => $cm,
				'network' => $network,
				'site_user' => $site_user,
				'mustache' => $m_comp
				)
			);
		}
		catch (\Exception $e)
		{
			$p_html = NULL;
			$json_response['error'] = 'Failure: ' . $e;
		}

		// successful
		$json_response['error'] = 'Success';
		$json_response['html'] = $p_html;
		$json_response['continue'] = 'n';

		if (count($network->posts) > $POST_INCREMENT) {
			$json_response['continue'] = 'y';
			$json_response['lb'] = $bounds[0] + $POST_INCREMENT;
			$json_response['ub'] = 10;
		}

		// return stuff
		echo json_encode($json_response);

	/*
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
	 */


}
else {
	$json_response['error'] = 'Necessary data not included';
	echo json_encode($json_response);
}
?>
