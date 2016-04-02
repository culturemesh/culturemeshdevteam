<?php
	include 'environment.php';
	$cm = new Environment();
	
	session_name($cm->session_name);
	session_start();

	// user id
	$uid = $_GET['uid'];
	$act_code = $_GET['act_code'];
	$act_success = False;

	$cm->enableDatabase($dal, $do2db);

	if ($uid !== NULL) {
	  $user = \dobj\User::createFromId($uid, $dal, $do2db);
	}

	if (($user !== NULL) && ($user->act_code === $_GET['act_code'])) {

		$act_success = $user->activate($dal, $do2db, $act_code);
		$_SESSION['uid'] = $user->id; 
	}

	$cm->closeConnection();

	/*
	$user = User::getUserById($id, $con);
	
	if ($user->act_code === $_GET['act_code']) {
		if (User::activateUser($_GET['uid'], $_GET['act_code'], $con)) {
			$act_success = true;
			$_SESSION['uid'] = $_GET['uid']; 
		}
	}
	 */


	$page_loader = new \misc\PageLoader($cm);
	echo $page_loader->generate('templates' . $cm->ds .'confirmation.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'success' => $act_success,
		'get' => $_GET
	));
	/*
	include "zz341/fxn.php";
	include "data/dal_user.php";

	$act_success = false;
	if (isset($_GET['uid']) && isset($_GET['act_code']))
	{
		$con = getDBConnection();
		$id = mysqli_real_escape_string($con, $_GET['uid']);

		$user = User::getUserById($id, $con);
		
		if ($user->act_code === $_GET['act_code']) {
			if (User::activateUser($_GET['uid'], $_GET['act_code'], $con)) {
				$act_success = true;
				$_SESSION['uid'] = $_GET['uid']; 
			}
		}

		mysqli_close($con);
	}


<html>
	<head>
		<?php
		//	include "headinclude.php";
		?>
		<title>CultureMesh - Confirmation</title>
		<meta name="keywords" content="" />
		<meta name="description" content="Welcome to CultureMesh - Connecting the world's diasporas!" />

	<script>
		function resendEmail(uid) {
			var confirmTxt = document.getElementById("confirm_txt");
			var query = "uid="+uid;

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200)	{
					confirmTxt.style.display = "block";
				}
			}
			xmlhttp.open("POST", "confirmation_resend.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send(query);
		}
	</script>
	</head>
<body>
	<div class="wrapper">
		<?php
		//	include "header.php";
		?>
		<div>
			<?php if($act_success) : ?>
			<h3>Your confirmation was successful!</h3>
			<p>You now have full access to CultureMesh!</p>
			<a href="profile/<?php echo $_GET['uid']; ?>/?confirm=true">Go to profile</a>
			<?php else : ?>
			<h3>Your confirmation was unsuccessful.</h3>
			<a href="#" onclick="resendEmail(<?php echo $_GET['uid']; ?>)">Click here to send another confirmation</a>
			<p id="confirm_txt" style="display:none;">Confirmation sent</p>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
	 */
?>
