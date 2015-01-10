<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

include_once "data/dal_post.php";
include_once "data/dal_network_registration.php";
include_once "http_redirect.php";
include_once "Environment.php";

session_name("myDiaspora");
session_start();

// create redirect
/*
$prev_url = $_SERVER['HTTP_REFERER'];
$pages = array('network');
$redirect = new \nav\HTTPRedirect($prev_url, $pages);
 */

$test = new NetworkRegistrationDT();
$test->id_user = $_SESSION['uid'];
$test->id_network = $_SESSION['cur_network'];
$valid = NetworkRegistration::checkRegistration($test);

$cm = new Environment();

// create redirect
$prev_url = $_SERVER['HTTP_REFERER'];
$pages = array('network');
$redirect = new \nav\HTTPRedirect($cm, $prev_url, $pages);

if ($valid)
{
	$post = new \dobj\Post();
	$post->id_user = $_SESSION['uid'];
	$post->id_network = $_SESSION['cur_network'];
	$post->post_text = strip_tags($_POST['post_text']);
	$post->post_class = $_POST['post_class'];

	/*
	$post = new PostDT();
	
	$post->id_user = $_SESSION['uid'];
	$post->id_network = $_SESSION['cur_network'];
	$post->post_text = mysql_escape_string($_POST['post_text']);
	$post->post_class = mysql_escape_string($_POST['post_class']);
	$post->post_original = mysql_escape_string($_POST['post_original']);
	 */
	
	if (strlen($post->post_text) <= 0) {
		$redirect->addQueryParameter('perror', 'No text in post');
		//$redirect->execute();
	}
	else {
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

	//	Post::createPost($post);
		$post->insert($dal, $do2db);
		$post->id = (int) $dal->lastInsertId(); 

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

		if (!isset($result['files'])) {
			echo $result['error'];
		}

		$files = $result['files'];

		$ids = array();

		foreach ($files as $file) {

			$file->insert($dal, $do2db);
			array_push($ids, $file->id);
		}

		$post->image_ids = $ids;
		$post->registerImages($dal, $do2db);

		$cm->closeConnection();

		$redirect->setControl('network', $_SESSION['cur_network']);
		//$redirect->execute();
		//header("Location: network.php?id={$_SESSION['cur_network']}");
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
