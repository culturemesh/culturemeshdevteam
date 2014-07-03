<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);

$json_response = array(
	"message" => NULL,
	"error" => NULL,
	"network" => NULL,
	"member" => NULL,
	"other" => NULL
);

// check if we came from a network page
$netpos = strpos($_SERVER['HTTP_REFERER'], "network");

// set the redirect page
// it may be a network page,
if ($netpos > -1) {
	$netend = strpos($_SERVER['HTTP_REFERER'], "&");

	// if there's a &, ignore it
	if ($netend > -1)
	{
		$length = $netend - $netpos;
		$redirect = substr($_SERVER['HTTP_REFERER'], $netpos, $length);
	}
	else {
		$redirect = substr($_SERVER['HTTP_REFERER'], $netpos);
	}
}
else {
	$redirect = "profile_edit.php";
}


/*
// Figure out error redirect
$files = array('index', 'search_results');

foreach($files as $file) {
	// check for string
	$refstart = strpos($_SERVER['HTTP_REFERER'], $file);

	// if the string is in the referer, we got a hit
	if ($refstart > -1) {
		// get string position
		$refend = strpos($_SERVER['HTTP_REFERER'], '&');

		// check for query string, cut it out
		if ($refend > -1) {
			$length = $refend - $refstart;
			$err_redirect = substr($_SERVER['HTTP_REFERER'], $refstart, $length);
		}
		else {
			$err_redirect = substr($_SERVER['HTTP_REFERER'], $refstart);
		}

		// end
		break;
	}
}

if ($err_redirect == NULL)
	$redirect = 'index.php';

 */
$err_redirect = 'index.php';

// make sure that the user has written
// 	in all the required fields
if(isset($_POST['email']) && isset($_POST['password'])
	&& isset($_POST['password_conf'])){
	
	// Check if password is long enough to be 
		// worthy of entry into my database
	if( strlen($_POST['password']) < 6) {
		/*
		$json_response["message"] = "Password must be longer than 6 characters.";
		$json_response["error"] = 2;
		echo json_encode($json_response);
		 */
		$msg = urlencode("Password must be longer than 6 characters.");
		header("Location: ".$redirect."?regerror=true&msg={$msg}");
	}

	// Check if password matches password confirmation
	else if($_POST['password'] != $_POST['password_conf']){
		/*
		$json_response["message"] = "Password confirmation does not match.";
		$json_response["error"] = 3;
		echo json_encode($json_response);
		 */
		$msg = urlencode("Password confirmation does not match.");
		header("Location: ".$redirect."?regerror=true&msg={$msg}");
	}
	else if(strlen($_POST['email']) > 30)
	{
		/*
		$json_response["message"] = "Email too long. Must be less than 30 characters";
		$json_response["error"] = 6;
		echo json_encode($json_response);
		 */
		$msg = urlencode("Email too long. Must be less than 30 characters");
		header("Location: ".$redirect."?regerror=true&msg={$msg}");
	}
	else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	{
		/*
		$json_response["message"] = 'Not a valid email. Must be like example@example.com';
		$json_response['error'] = 7;
		echo json_encode($json_response);
		 */
		$msg = urlencode('Not a valid email. Must be like example@example.com');
		header("Location: ".$redirect."?regerror=true&msg={$msg}");
	}
	else
	{
		session_name("myDiaspora");
		session_start();
		
		include 'zz341/fxn.php';
		include 'data/dal_user.php';
		include 'cm_email.php';
		
		$conn = getDBConnection();

		
		$email = $conn->real_escape_string($_POST['email']);
		
		// check to see if email is already taken
		$email_in_use = User::checkEmailMatch($email);

		if(!$email_in_use){
			$new_user = new UserDt();
			$new_user->username = null;
			$new_user->email = $email;
			$new_user->password = md5($_POST['password']);
			$new_user->role = 0;
			$new_user->act_code = md5(microtime());
			
			// create user
			User::createUser($new_user, $conn);
			
			// get user id back
			$_SESSION['uid'] = User::getUserId($email, $conn);

			// send confirmation email
			if(!CMEmail::sendConfirmationEmail($email, $_SESSION['uid'], $new_user->act_code)) {
				$json_response["other"] = "Email couldn't be sent";
				$msg = urlencode("Email couldn't be sent");
			}

			/*
			// close up
			mysqli_close($conn);
			$json_response["error"] = 5;
			$json_response["message"] = "Account created successfully!";
			echo json_encode($json_response);
			 */
			header("Location: ".$redirect."?msg={$msg}");
		}
		else
		{
			/*
		    $json_response["error"] = 4;
		    $json_response["message"] = "Username already exists.";
		    echo json_encode($json_response);
			 */
			$msg = urlencode();
			header("Location: ".$redirect."?regerror=true&msg={$msg}");
		}  
    }
}

else {
	/*
	$json_response["error"] = 1;
	$json_response["message"] = "Please fill out all of the fields.";
	echo json_encode($json_response);
	 */
	$msg = urlencode("Please fill out all of the fields.");
	header("Location: ".$err_redirect."?regerror=true&msg={$msg}");
}
?>
