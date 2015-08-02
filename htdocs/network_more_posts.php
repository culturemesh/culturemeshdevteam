<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

session_name('myDiaspora');
session_start();

$POST_INCREMENT = 10;

$json_response = array(
	'error' => NULL,
	'html' => NULL,
	//'continue' => 'n',
	'nmp_more_posts' => 0,
	'lb' => NULL,
	'ub' => NULL,
	'until_date' => NULL
	);


if (isset($_POST['lb']) && isset($_POST['ub'])
	&& isset($_POST['nid'])) {
	

		/*
		if ($_POST['direction'] == 'older') {

			$network->getOlderPostsFromId($dal, $do2db, $pid, $_POST['lb'], $_POST['ub']);
		}

		if ($_POST['direction'] == 'newer') {

			$network->getNewerPostsFromId($dal, $do2db, $pid, $_POST['lb'], $_POST['ub']);
		}
		 */


		include 'environment.php';

		$cm = new \Environment();

		// add one to the upper bound so that we can check if there
		// are more posts
		$bounds = array((int) $_POST['lb'], (int) $_POST['ub']);
		$bounds[1] += 1;

		$network = new \dobj\Network();
		$network->id = (int) $_POST['nid'];

		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();
		$site_user = \dobj\User::createFromId((int) $_SESSION['uid'], $dal, $do2db);

		$network->getPosts($dal, $do2db, $bounds[0], $bounds[1]);

		$cm->closeConnection();

		/////// make components //////////
		$m_comp = new \misc\MustacheComponent();

		// set network stuff
		if (count($network->posts) > $POST_INCREMENT) {
			//$json_response['continue'] = 'y';
			$json_response['nmp_more_posts'] = 1;
			$json_response['lb'] = $bounds[0] + $POST_INCREMENT;
			$json_response['ub'] = 10;

			$network->posts->slice(0, 10);
		}

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
		
		// return stuff
		echo json_encode($json_response);
}
else {
	$json_response['error'] = 'Necessary data not included';
	echo json_encode($json_response);
}
?>
