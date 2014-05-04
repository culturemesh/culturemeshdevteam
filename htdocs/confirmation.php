<?php
//	ini_set('display_errors', true);
	session_name("myDiaspora");
	session_start();
	include "zz341/fxn.php";
	include "data/dal_user.php";

	if (isset($_GET['uid']) && isset($_GET['act_code']))
	{
		$con = getDBConnection();
		$act_success = User::activateUser($_GET['uid'], $_GET['act_code'], $con);
		mysqli_close($con);

		if ($act_success)
		  { $_SESSION['uid'] = $_GET['uid']; }
	}


?>
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
			<a href="profile_edit.php?confirm=true">Go to profile</a>
			<?php else : ?>
			<h3>Your confirmation was unsuccessful.</h3>
			<a onclick="resendEmail(<?php echo $_GET['uid']; ?>)">Click here to send another confirmation</a>
			<p id="confirm_txt" style="display:none;">Confirmation sent</p>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
