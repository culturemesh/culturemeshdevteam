<?php
include_once "data/dal_post.php";
include_once "data/dal_network_registration.php";
include_once "environment.php";

session_name("myDiaspora");
session_start();

$json_response = array(
	'error' => NULL,
	'status' => NULL,
	'html' => NULL
);

if (!isset($_SESSION['uid'])) {

	$json_response['error'] = 1;
	$json_response['status'] = 'Not logged in';

	echo json_encode($json_response);
	exit();
}

$cm = new Environment();

$dal = new \dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new \dal\Do2Db();

// check registration
$user = \dobj\User::createFromId((int) $_SESSION['uid'], $dal, $do2db);
$valid = $user->checkNetworkRegistration((int) $_SESSION['cur_network']);

if ($valid)
{
	$post = new \dobj\Post();
	$post->id_user = $_SESSION['uid'];
	$post->id_network = $_SESSION['cur_network'];
	$post->post_text = strip_tags($_POST['post_text']);
	$post->post_class = $_POST['post_class'];

	$network = new \dobj\Network();
	$network->id = (int) $_SESSION['cur_network'];

	if (strlen($post->post_text) <= 0) {
		$json_response['error'] = 'No text in post';
		echo json_encode($json_response);
		exit();
		//$redirect->addQueryParameter('perror', 'No text in post');
		//$redirect->execute();
	}
	else {


	//	Post::createPost($post);
		// create post
		$post->insert($dal, $do2db);
		$post->id = (int) $dal->lastInsertId(); 

		if (count($_FILES) == 0) {

			$cm->closeConnection();

			$json_response['error'] = 0;
			$json_response['status'] = 'noimage';
			$json_response['html'] = $post->getHTML('network', array(
					'network' => $network,
					'site_user' => $user,
					'mustache' => new \misc\MustacheComponent(),
					'cm' => $cm
				)
			);

			echo json_encode($json_response);

			exit();
		}

		$iu = new \misc\ImageUpload($cm, array(
				'dir' => $cm->img_repo_dir,
				'postname' => 'fileupload',
				'validation_type' => array('image/png', 'image/gif', 'image/jpeg'),
				'validation_size' => '2M',
				'thumbnail' => array(
					'thumbnail' => true,
					'class' => 'post')
				)
			);

		$result = $iu->upload();

		// if upload was unsuccessful
		if (!isset($result['files'])) {
			$json_response['error'] = $result['error'];
			echo json_encode($json_response);
			exit();
		}

		$files = $result['files'];

		$ids = array();

		foreach ($files as $file) {

			$id = $file->insert($dal, $do2db);

			if ($id) {
				array_push($ids, $file->id);
			}
			// image insertion has failed for some reason
			else {
			  	$json_response['error'] = 'Could not add image to database';
			  	echo json_encode($json_response);
				exit();
			}
		}

		$post->image_ids = $ids;
		$post->registerImages($dal, $do2db);

		$cm->closeConnection();

		// push hashes to post
		$post->images = $files;

		// get post html
		$json_response['error']  = 0;
		$json_response['status'] = 'image';
		$json_response['html'] = $post->getHTML('network', array(
					'network' => $network,
					'site_user' => $user,
					'mustache' => new \misc\MustacheComponent(),
					'cm' => $cm
				)
			);

		echo json_encode($json_response);
		exit();
	}
}
else
{
	if (isset($_SESSION['cur_network'])) {
		header("Location: network.php?id={$_SESSION['cur_network']}&success=false");
	}
	else {
		header("Location: index.php?signout=true");
	}
}
?>
