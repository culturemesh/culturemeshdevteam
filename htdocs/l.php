<?php
/**
  * Success - 1
  * Login info incorrect - 2
  * Email is too long - 3
  * Password is too long - 4
  * Database connection failed - 5
**/

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
else
	$redirect = "profile_edit.php";

// start working on stuff
if($_POST['email'] && $_POST['password']){
	
		include 'zz341/fxn.php';
		include_once("data/dal_user.php");
		include_once("data/dal_user-dt.php");
		include_once("data/dal_network_registration.php");
		include_once("data/dal_network_registration-dt.php");
		include_once("data/dal_event.php");
		include_once("data/dal_event_registration.php");
		
		session_name("myDiaspora");
		session_start();
		$con = getDBConnection();
		
		$email = mysql_escape_string($_POST['email']);
		$pass = mysql_escape_string($_POST['password']);

		$json_response = array(
			"error" => NULL,
			"network" => NULL,
			"member" => NULL,
			"title" => NULL,
			"events" => NULL,
			"uid" => NULL
		);
		
		if (strlen($email) > 50)
		{
			mysqli_close($con);
			header("Location: ".$redirect);
			/*
			$json_response['error'] = 3;
			echo json_encode($json_response);
			 */
		}
		else if (strlen($pass) > 18)
		{
			mysqli_close($con);
			header("Location: ".$redirect);
			/*
			$json_response['error'] = 4;
			echo json_encode($json_response);
			 */
		}
		else
		{	
			$pass = md5($pass);
			$data = User::userLoginQuery($email, $pass, $con);
			
			$result = $data->fetch_assoc();
			
			if($result["email"] != NULL){
				// set session variable
                		$_SESSION['uid'] = getMemberUID($email, $con);
				$json_response['uid'] = $_SESSION['uid'];
                		
                		// check to see if we came from network.php, if so,
                			// we must find out if we're a member of the network we logged in from
                		if (isset($_SESSION['cur_network']))
                		{
                			$json_response["network"] = $_SESSION['cur_network'];
                		
					$netreg = new NetworkRegistrationDT();
					$netreg->id_user = $_SESSION['uid'];
					$netreg->id_network = $_SESSION['cur_network'];
					$events = EventRegistration::getEventRegistrationByUid($_SESSION['uid'], $con);
					$events = QueryHandler::getRows($events);
					$json_response['events'] = $events;
					$json_response['member'] = NetworkRegistration::checkRegistration($netreg, $con);

					// close connection
					mysqli_close($con);

					// get title
					if ($result['first_name'] != NULL)
						$json_response['title'] = $result['first_name'];
					else if ($result['username'] != NULL)
						$json_response['title'] = $result['username'];
					else
						$json_response['title'] = $email;

					// return successful
					header("Location: ".$redirect);
					//echo json_encode($json_response);
				}
				else // came from somewhere else, may be expanded later
				{
					mysqli_close($con);
					header("Location: ".$redirect);
					//echo json_encode($json_response);
				}
			}
			else
			{
			   mysqli_close($con);
			   header("Location: ".$redirect);
			   /*
			    $json_response['error'] = 2;
			    echo json_encode($json_response);
			    */
			}
		}
}
else{
    header("Location: index.php");
    /*
    $json_response = array(
    "error" => NULL,
    "network" => NULL,
    "member" => NULL,
    "title" => NULL);

    echo json_encode($json_response);
     */
}
?>
