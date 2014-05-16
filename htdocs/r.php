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

// make sure that the user has written
// 	in all the required fields
if(isset($_POST['email']) && isset($_POST['password'])
	&& isset($_POST['password_conf'])){
	
	// Check if password is long enough to be 
		// worthy of entry into my database
	if( strlen($_POST['password']) < 6) {
		$json_response["message"] = "Password must be longer than 6 characters.";
		$json_response["error"] = 2;
		echo json_encode($json_response);
	}

	// Check if password matches password confirmation
	else if($_POST['password'] != $_POST['password_conf']){
		$json_response["message"] = "Password confirmation does not match.";
		$json_response["error"] = 3;
		echo json_encode($json_response);
	}
	else if(strlen($_POST['email']) > 30)
	{
		$json_response["message"] = "Email too long. Must be less than 30 characters";
		$json_response["error"] = 6;
		echo json_encode($json_response);
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
			if(!CMEmail::sendConfirmationEmail($email, $_SESSION['uid'], $new_user->act_code))
				$json_response["other"] = "Email couldn't be sent";

			// close up
			mysqli_close($conn);
			$json_response["error"] = 5;
			$json_response["message"] = "Account created successfully!";
			echo json_encode($json_response);
		}
		else
		{
		    $json_response["error"] = 4;
		    $json_response["message"] = "Username already exists.";
		    echo json_encode($json_response);
		}  
    }
}

else {
	$json_response["error"] = 1;
	$json_response["message"] = "Please fill out all of the fields.";
	echo json_encode($json_response);
}
?>
