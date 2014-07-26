<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL ^ E_NOTICE);

/**
  * Success - 1
  * Login info incorrect - 2
  * Email is too long - 3
  * Password is too long - 4
  * Database connection failed - 5
**/

/*
 * 1) check http_referer to find the site
 * 	a) GETS ARE CRITICAL HERE
 * 		i) network
 * 		ii) search results
 * 	b) NOT SO MUCH
 * 		i) index
 */

include_once('http_redirect.php');

// possible pages that we could be logging in from
$pages = array('index', 'network', 'search_results', 
	'careers', 'about', 'press');

$prev_url = $_SERVER['HTTP_REFERER'];

$redirect = new HTTPRedirect($prev_url, $pages);
$redirect->removeQueryParameters(array('lerror', 'rerror', 'jeerror', 'eid', 'ueerror'));

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
			$redirect->addQueryParameter('lerror', 'Email too long, must be 50 characters or less');
			$redirect->execute();
			//header("Location: ".$redirect);
			/*
			$json_response['error'] = 3;
			echo json_encode($json_response);
			 */
		}
		else if (strlen($pass) > 18)
		{
			mysqli_close($con);
			$redirect->addQueryParameter('lerror', 'Password too long, must be 18 characters or less');
			$redirect->execute();
//			header("Location: ".$redirect);
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

				// if not coming from network, redirect to profile_edit
				if (!$redirect->pathContains('network')) {
					
					$redirect->setPath('profile_edit.php');
					// will sooon have to set get parameter here
				}

				// return successful
				$redirect->addQueryParameter('lerror', 'success');
				$redirect->execute();

			}
			else
			{
			   mysqli_close($con);

			   // Server error
			   $redirect->addQueryParameter('lerror', 'Username and password are incorrect');
			   $redirect->execute();
			   //header("Location: ".$redirect);
			   /*
			    $json_response['error'] = 2;
			    echo json_encode($json_response);
			    */
			}
		}
}
// no data provided
else {
	$redirect->addQueryParameter('lerror', 'No data');
	$redirect->execute();
    //header("Location: index.php");
    /*
    $json_response = array(
    "error" => NULL,
    "network" => NULL,
    "member" => NULL,
    "title" => NULL);

    echo json_encode($json_response);
     */
}

				/*
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

					//echo json_encode($json_response);
				}
				else // came from somewhere else, may be expanded later
				{
					mysqli_close($con);
					header("Location: ".$redirect);
					//echo json_encode($json_response);
				}
				 */
?>
