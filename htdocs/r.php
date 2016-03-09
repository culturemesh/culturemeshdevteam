<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);
//include("http_redirect.php");
include('environment.php');

include_once('http_redirect.php');
include_once('lib/nav/HTTPRedirect.php');

$cm = new Environment();

$json_response = array(
	"message" => NULL,
	"error" => NULL,
	"network" => NULL,
	"member" => NULL,
	"other" => NULL
);

// pages you can register from
$pages = array('index', 'network', 'search_results', 
	'careers', 'about', 'press');

// get prev url
$prev_url = $_SERVER['HTTP_REFERER'];

// create redirect object
$redirect = new \nav\HTTPRedirect($cm, $prev_url, $pages);
$redirect->removeQueryParameters(array('lerror', 'rerror'));

// make sure that the user has written
// 	in all the required fields
if(isset($_POST['email']) && isset($_POST['password'])
	&& isset($_POST['password_conf']) && isset($_POST['fname'])
	&& isset($_POST['lname'])){
	
	// Check if password is long enough to be 
		// worthy of entry into my database
	if( strlen($_POST['password']) < 6) {
		/*
		$json_response["message"] = "Password must be longer than 6 characters.";
		$json_response["error"] = 2;
		echo json_encode($json_response);
		 */
		//$msg = urlencode("Password must be longer than 6 characters.");
		//header("Location: ".$redirect."?regerror=true&msg={$msg}");
		$redirect->addQueryParameter('rerror', 'Password must be longer than 6 characters.');
		$redirect->execute();
	}
	// Check if password matches password confirmation
	else if(strlen($_POST['password']) > 25){

		$redirect->addQueryParameter('rerror', 'Password too long, must be 25 characters or less');
		$redirect->execute();
	}
	// Check if password matches password confirmation
	else if($_POST['password'] != $_POST['password_conf']){
		/*
		$json_response["message"] = "Password confirmation does not match.";
		$json_response["error"] = 3;
		echo json_encode($json_response);
		 */
		//$msg = urlencode("Password confirmation does not match.");
		//header("Location: ".$redirect."?regerror=true&msg={$msg}");
		$redirect->addQueryParameter('rerror', 'Password confirmation does not match');
		$redirect->execute();
	}
	else if(strlen($_POST['email']) > 50)
	{
		/*
		$json_response["message"] = "Email too long. Must be less than 30 characters";
		$json_response["error"] = 6;
		echo json_encode($json_response);
		 */
		//$msg = urlencode("Email too long. Must be less than 30 characters");
		//header("Location: ".$redirect."?regerror=true&msg={$msg}");
		$redirect->addQueryParameter('rerror', 'Email too long. Must be less than 50 characters');
		$redirect->execute();
	}
	else if(strlen($_POST['fname']) > 30) {
		$redirect->addQueryParameter('rerror', 'First Name too long. Must be less than 30 characters');
		$redirect->execute();
	}
	else if(strlen($_POST['lname']) > 30) {
		$redirect->addQueryParameter('rerror', 'Last Name too long. Must be less than 30 characters');
		$redirect->execute();
	}
	else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	{
		/*
		$json_response["message"] = 'Not a valid email. Must be like example@example.com';
		$json_response['error'] = 7;
		echo json_encode($json_response);
		 */
		//$msg = urlencode('Not a valid email. Must be like example@example.com');
		//header("Location: ".$redirect."?regerror=true&msg={$msg}");
		$redirect->addQueryParameter('rerror', 'Not a valid email. Must follow form: example@example.com');
		$redirect->execute();
	}
	else
	{
		session_name($cm->session_name);
		session_start();
		
		include 'zz341/fxn.php';
		include 'data/dal_user.php';
		include_once "data/dal_network_registration.php";
		include 'cm_email.php';
		
		$conn = getDBConnection();

		
		$email = $conn->real_escape_string($_POST['email']);
		$fname = $conn->real_escape_string($_POST['fname']);
		$lname = $conn->real_escape_string($_POST['lname']);

		$is_joining = $conn->real_escape_string($_POST['reg_joining']) == "1";
		$joining_network = (int) $conn->real_escape_string($_POST['reg_joining_network']);

		// check to see if email is already taken
		$email_in_use = User::checkEmailMatch($email);

		if(!$email_in_use){
			$new_user = new UserDt();
			$new_user->username = null;
			$new_user->email = $email;
			$new_user->first_name = $fname;
			$new_user->last_name = $lname;
			$new_user->password = md5($_POST['password']);
			$new_user->role = 0;
			$new_user->act_code = md5(microtime());

			// create user
			User::createUser($new_user, $conn);
			
			// get user id back
			$_SESSION['uid'] = User::getUserId($email, $conn);

			if ($is_joining) {

				$netreg = new NetworkRegistrationDT();

				$netreg->id_user = $_SESSION['uid'];
				$netreg->id_network = $_SESSION['cur_network'];

				NetworkRegistration::createNetRegistration($netreg);

				$redirect->addQueryParameter('jnerror', 'Welcome to the network!');
			}

			// send confirmation email
			if(!CMEmail::sendConfirmationEmail($email, $_SESSION['uid'], $new_user->act_code)) {
				//$json_response["other"] = "Email couldn't be sent";
				//$msg = urlencode("Email couldn't be sent");
				$redirect->addQueryParameter('other', 'Email couldn\'t be sent');
			}

			//header("Location: ".$redirect."?msg={$msg}");

			// if not coming from network, redirect to profile_edit
			if (!$redirect->getControl()['control'] == 'network') {
				$redirect->setControl('profile', $_SESSION['uid']);
				// will sooon have to set get parameter here
			}

			$redirect->addQueryParameter('rerror', 'success');
			$redirect->execute();
		}
		else
		{
			//$msg = urlencode();
			//header("Location: ".$redirect."?regerror=true&msg={$msg}");
			$redirect->addQueryParameter('rerror', 'Username already exists');
			$redirect->execute();
		}  
    }
}

else {
	/*
	$json_response["error"] = 1;
	$json_response["message"] = "Please fill out all of the fields.";
	echo json_encode($json_response);
	 */
//	$msg = urlencode("Please fill out all of the fields.");
	//
	//header("Location: ".$err_redirect."?regerror=true&msg={$msg}");
	$redirect->addQueryParameter('rerror', 'Please fill out all of the field');
	$redirect->execute();
}
?>
