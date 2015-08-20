<?php

ini_set("display_errors", false);

include("zz341/fxn.php");
include("data/dal_user.php");
include("data/dal_user_info.php");
include("data/dal_user_notification.php");
include_once('data/dal_post.php');
include_once('data/dal_query_handler.php');
include_once('data/dal_network.php');
include_once('html_builder.php');

include 'environment.php';
$cm = new Environment();

session_name($cm->session_name);
session_start();

$results = array(
	'error' => NULL,
	'msg' => NULL);

/**
 * BASIC INFO UPDATE
 */
if(isset($_POST['bi_update']) )
{
	if (!isset($_SESSION['uid'])) {
		$results['error'] = 6;
		$results['msg'] = 'Not logged in. Access denied';
		echo json_encode($results);
	}

	$con = getDBConnection();
	$info = new UserInfoDT();
	$info->uid = $_SESSION['uid'];
	$info->first_name = mysql_escape_string( $_POST['first_name']);
	$info->last_name = mysql_escape_string( $_POST['last_name']);
	$info->gender = mysql_escape_string( $_POST['gender'][0] );
	$info->about_me = mysql_escape_string( $_POST['about_me'] );

	// end conditions
	if (strlen($info->first_name) > 30)
	{
		echo json_encode(array("error" => "First name too long. Please keep it 30 characters or less."));
	}
	else if (strlen($info->last_name) > 30)
	{
		echo json_encode(array("error" => "Last name too long. Please keep it 30 characters or less."));
	}
	else if (strlen($info->about_me) > 500)
	{
		echo json_encode(array("error" => "Please limit about me to 500 characters or less."));

	}
	else {
		$success = UserInfo::updateInfo($info);
		if($success == 1)
		{
			mysqli_close($con);
			// put valid info in array
			$data = array(
				"error" => 0,
				"uid" => $info->uid,
				"first_name" => $_POST['first_name'],
				"last_name" => $_POST['last_name'],
				"gender" => $_POST['gender'],
				"about_me" => $_POST['about_me']);

			echo json_encode($data);
		}
		else
		{
			mysqli_close($con);
			$data = array(
				"error" => $con->error);
			echo json_encode($data);
		}
	}
}

/**
 * ACCOUNT INFO UPDATE
 */
if (isset($_POST['ai_update']))
{
	if (!isset($_SESSION['uid'])) {
		$results['error'] = 6;
		$results['msg'] = 'Not logged in. Access denied';
		echo false;
	}

	$note = new UserNotificationDT();
	$note->uid = $_SESSION['uid'];
	$note->events_upcoming = getCheckboxBool($_POST['notify_interesting_events']);
	$note->events_interested_in = getCheckboxBool($_POST['notify_company_news']);
	$note->company_news = getCheckboxBool($_POST['notify_events_upcoming']);
	$note->network_activity = getCheckboxBool($_POST['notify_events_upcoming']);
	echo UserNotification::updateNotification($note);
}

if (isset($_POST['pi_update']))
{
	if (!isset($_SESSION['uid'])) {
		$results['error'] = 6;
		$results['msg'] = 'Not logged in. Access denied';
		echo json_encode($results);
	}

	$con = getDBConnection();

	// get post variables
	$email = mysql_escape_string($_POST['email']);
	$cur_password = mysql_escape_string($_POST['cur_password']);
	$new_password = mysql_escape_string($_POST['password']);
	$conf_password = mysql_escape_string($_POST['password_conf']);

	if ($new_password != $conf_password)
	{
		$results['error'] = 1;
		$results['msg'] = "Passwords must match";	
		echo json_encode($results);
	}

	else if (strlen($new_password) < 6)
	{
		$results['error'] = 2;
		$results['msg'] = "Password must be at least 6 characters.";
		echo json_encode($results);
	}

	else if (strlen($new_password) > 32)
	{
		$results['error'] = 3;
		$results['msg'] = "Password cannot be longer than 32 characters.";
		echo json_encode($results);
	}
	else
	{
	// check if user is valid
	$check = User::userLoginQuery($email, md5($cur_password), $con);

		if ($check->num_rows > 0)
		{
			if (User::changeUserPassword($_SESSION['uid'], md5($new_password)))
			{
				$results['error'] = 0;
				$results['msg'] = "Password changed successfully.";
				echo json_encode($results);
			}
			else
			{
				$results['error'] = 4;
				$results['msg'] = "We experienced a database error, try later";
				echo json_encode($results);
			}
		}
		else
		{
			$results['error'] = 3;
			$results['msg'] = "Not a valid username/password combination";
			echo json_encode($results);
		}
	}
}
/**
 * MORE POSTS
 */
if (isset($_POST['more_posts']))
{
	ini_set('display_errors', false);

	$json_response = array(
		'error' => NULL,
		'html' => NULL,
		'continue' => NULL,
		'lb' => NULL);

//		$con = getDBConnection();
	$uid = $_POST['uid'];
	$lb = $_POST['lb'];
	$nid = $_POST['nid'];
	$test_ub = 11;
	$ub = 11;
	$bounds = array($lb, $test_ub);

	/*
	$posts = Post::getPostsByUserId($uid, $bounds, $con);

	$html = '';

	// get post html
	for ($i = 0; $i < $ub && $i < count($posts); $i++){
		// check to see if we're in the same network
		if ($posts[$i]->id_network != $nid) {
			// swap ids
			$nid = $posts[$i]->id_network;
			// get new network
			$network = Network::getNetworkById($id, $con);	
			// display new network
			$html .= HTMLBuilder::displayDashNetworkTitle($network);
		}

		// get post html
		$html .= HTMLBuilder::displayDashPost($posts[$i], true);
	}

	mysqli_close($con);
	 */

	include('environment.php');
	$cm = new \Environment();

	$user = new \dobj\User();
	$user->id = (int) $uid;

	// db stuff
	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();

	$user->getPosts($dal, $do2db, (int) $lb, (int) $ub);

	$cm->closeConnection();

	// get thing
	/////// make components //////////
	$m_comp = new \misc\MustacheComponent();

	// set network stuff

	if ($user->yp_posts) {
		$tmp = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-postul.html');
		$p_html = $user->yp_posts->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp,
			'list_template' => $tmp,
			'max' => 10
			)
		);
	}
	else {
		$p_html = NULL;
		$json_response['error'] = 'Failure: ' . $e;
	}

	$json_response['html'] = $p_html;
	$json_response['error'] = 'success';
	$json_response['continue'] = 'n';

	// if there are 11+ posts, more can be loaded
	if ($user->yp_posts->countAll() >= $test_ub) {
		$json_response['continue'] = 'y';
		$json_response['lb'] = $lb + 10;
	}

	// return the thing
	echo json_encode($json_response);
}

?>
