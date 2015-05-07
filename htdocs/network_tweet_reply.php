<?php

$json_response = array(
	'error' => NULL,
	'error_message' => NULL,
	'html' => NULL
);

//
// Checking for a great deal of variables
//
if (!isset($_POST['nid']) || !isset($_POST['uid']) || !isset($_POST['id_tweet']) || !isset($_POST['tweet_text'])
	|| !isset($_POST['tweet_date']) || !isset($_POST['name']) || !isset($_POST['screen_name'])
	|| !isset($_POST['reply_text']) || !isset($_POST['profile_image'])) {

		$json_response['error'] = 1;
		$json_response['error_message'] = 'Not all values were accounted for.';

		echo json_encode($json_response);
		exit();
}

//
// Now let's get on with it
//
include('environment.php');

$cm = new \Environment();

session_name('myDiaspora');
session_start();


//
// Check to see if user is set
//
if (!isset($_SESSION['uid'])) {

	$json_response['error'] = 2;
	$json_response['error_message'] = 'Nobody is logged in.';

	echo json_encode($json_response);
	exit();
}

//
// DATABASE TIME
//
$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

//
// GETTING THE USER, MY FRIENDS
//
$uid = $_SESSION['uid'];
$site_user = dobj\User::createFromId($uid, $dal, $do2db);

// 
// LOADING UP THE TWEET INFORMATION
//
$origin_tweet = new \dobj\Tweet();
$origin_tweet->id = (int) $_POST['id_tweet'];
$origin_tweet->id_network = (int) $_POST['nid'];
$origin_tweet->text = $_POST['tweet_text'];

// jumping through some datetime hoops
$tweet_date = strtotime($_POST['tweet_date']);
$origin_tweet->created_at = date('Y-m-d H:i:s', $tweet_date);

$origin_tweet->name = $_POST['name'];
$origin_tweet->screen_name = $_POST['screen_name'];
$origin_tweet->profile_image_url = $_POST['profile_image'];
$origin_tweet->saved = $_POST['saved'];

//
// LOADING UP THE REPLY INFORMATION
//
$tweet_reply = new \dobj\TweetReply();
$tweet_reply->id_parent = (int) $origin_tweet->id;
$tweet_reply->id_user = (int) $site_user->id;
$tweet_reply->id_network = (int)  $_POST['nid'];
$tweet_reply->reply_text = strip_tags($_POST['reply_text']);


if ($origin_tweet->saved !== True) {

	// Load tweet into database
	$origin_tweet->insert($dal, $do2db);

	// Overwrite cache without saved tweet
	/*
	$cache = new \misc\Cache();
	$cache->fetch(
	 */
}

// Load reply into database
$reply_id = $tweet_reply->insert($dal, $do2db);

if ($reply_id != False) {

	// create network
	$network = new \dobj\Network();
	$network->id = (int) $nid;

	$origin_tweet->getReplies($dal, $do2db);

	// close connection
	$cm->closeConnection();
	
	$replies_html = $origin_tweet->getHTML('replies', array(
		'cm' => $cm,
		'mustache' => new \misc\MustacheComponent(),
		'network' => $network
	)); 

	$json_response['html'] = $replies_html;
	$json_response['error'] = 0;
	$json_response['error_message'] = 'SUCCESS!!';

	echo json_encode($json_response);
	exit();
}

else {
	$json_response['error'] = 3;
	$json_response['error_message'] = 'Could not insert tweet into database';

	echo json_encode($json_response);
	exit();
}

?>