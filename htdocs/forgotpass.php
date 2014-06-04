<?php
	include_once 'zz341/fxn.php';

	// we're here, real deal to change password
if (isset($_GET['code']) && isset($_GET['email']))
{
	include_once 'data/dal_query_handler.php';
	include_once 'data/dal_user.php';

	$email = $_GET['email'];
	$code = $_GET['code'];
	$con = QueryHandler::getDBConnection();
	$result = User::checkFPCode($email, $code);
	$valid = false;

	if (mysqli_num_rows($result) > 0)
	  { $valid = true; }

	// close connection
	mysqli_close($con);
}
?>

<html>
<head>
	<?php include 'headinclude.php'; ?>
</head>
<body>
	<div id="wrapper">
	<?php include 'header.php'; ?>
	<div id="content">
		<?php if(!isset($_GET['code'])) : ?>
		<div id="message">
			<h2>Forgotten Password?</h2>
			<h5>Never fear!</h5>
			<p>If you've forgotten your password, please
				enter it below. We'll send an email so
				that you can reset it!
			</p>
		</div>
		<div id="submission">
			<form id="forgot-pass-form" method="POST" action="forgotpass-email.php">
				<input type="text" name="email" placeholder="Email"/>
				<input type="submit" name="submit" value="Ok!"/>
			</form>
		</div>
		<?php endif; ?>
<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////
?>
		<?php if($valid) : ?>
		<div id="message">
			<h2>Forgotten Password?</h2>
			<h5>Never fear!</h5>
			<p>Enter your new password below.</p>
		</div>
		<div id="submission">
			<form id="forgot-pass-form" method="POST" action="forgotpass-change.php">
				<input type="hidden" name="email" value='<?php echo $_GET['email']; ?>'/>
				<input type="password" name="password" placeholder="New Password"/>
				<input type="password" name="password_conf" placeholder="Confirm Password"/>
				<input type="submit" name="submit" value="Ok!"/>
			</form>
		</div>
		<?php endif; ?>
	</div>
	<?php include 'footer.php'; ?>
	</div>
</body>
</html>
