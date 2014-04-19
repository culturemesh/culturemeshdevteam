<?php

ini_set('display_errors', true);
include("zz341/fxn.php");
include("data/dal_user.php");
include("data/dal_user_info.php");
include("data/dal_user_notification.php");

session_name("myDiaspora");
session_start();

if( isset($_SESSION['uid']))
{
	/**
	 * BASIC INFO UPDATE
	 */
	if(isset($_POST['bi_update']) )
	{
		$con = getDBConnection();
		$info = new UserInfoDT();
		$info->uid = $_SESSION['uid'];
		$info->first_name = mysql_escape_string( $_POST['first_name']);
		$info->last_name = mysql_escape_string( $_POST['last_name']);
		$info->gender = mysql_escape_string( $_POST['gender'][0] );
		$info->about_me = mysql_escape_string( $_POST['about_me'] );
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
	 
	/**
	 * ACCOUNT INFO UPDATE
	 */
	if (isset($_POST['ai_update']))
	{
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
		$con = getDBConnection();
		$results = array(
			"error" => null,
			"msg" => null);

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
}
else
{
	exit("Nobody's logged in. Access denied.");
}
?>
