<?php
	include_once 'zz341/fxn.php';

	// we're here, real deal to change password
$valid = false;
if (isset($_GET['code']) && isset($_GET['email']))
{
	include_once 'data/dal_query_handler.php';
	include_once 'data/dal_user.php';

	$email = $_GET['email'];
	$code = $_GET['code'];
	$con = QueryHandler::getDBConnection();

	$result = User::checkFPCode($email, $code, $con);

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
<?php
/////////////////////////////////////////////////////////////////////////////
//			ENTER EMAIL
/////////////////////////////////////////////////////////////////////////////
?>
		<?php if(!isset($_GET['code']) && !isset($_GET['email']) && !isset($_GET['fperror']) ) : ?>
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
//			ENTER NEW PASSWORD
/////////////////////////////////////////////////////////////////////////////////////
?>
		<?php if($valid) : ?>
		<div id="message">
			<h2>Forgotten Password?</h2>
			<h5>Never fear!</h5>
			<p>Enter your new password below.</p>
		</div>
		<div id="submission">
			<?php if (isset($_GET['fperror'])) : ?>
				<?php if($_GET['fperror'] === 'mismatch') : ?>
					<p>Your password and confirmation must match.</p>
				<?php endif; ?>
				<?php if($_GET['fperror'] === 'short') :?>
					<p>Your password must be 8 characters or longer.</p>
				<?php endif; ?>
				<?php if($_GET['fperror'] === 'long') :?>
					<p>Your password must be shorter than 30 characters.</p>
				<?php endif; ?>
			<?php endif; ?>
			<form id="forgot-pass-form" method="POST" action="forgotpass-change.php">
				<input type="hidden" name="email" value='<?php echo $_GET['email']; ?>'/>
				<input type="hidden" name="fp_code" value='<?php echo $_GET['code']; ?>'/>
				<input type="password" name="password" placeholder="New Password"/>
				<input type="password" name="password_conf" placeholder="Confirm Password"/>
				<input type="submit" name="submit" value="Ok!"/>
			</form>
		</div>
		<?php endif; ?>
<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////
//			EMAIL SENT
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
		<?php if(!$valid && isset($_GET['email'])) : ?>
		<div id="message">
			<h2>Forgotten Password</h2>
			<?php if($_GET['email'] === 'nopost') : ?>
				<p>You've come to this page by mistake</p>
			<?php endif; ?>
			<?php if ($_GET['email'] === 'fail') : ?>
				<p>There was an error sending your email on our end.</p>
				<a href="forgotpass.php">Click here to try again.</a>
			<?php endif; ?>
			<?php if ($_GET['email'] === 'success') : ?>
				<p>An email has been sent to you. Answer it so that you may reset your password.</p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
<?php
////////////////////////////////////////////////////////////////////////////////
//			CHANGED
///////////////////////////////////////////////////////////////////////////////

?>
		<?php if (!$valid && isset($_GET['fperror'])) : ?>
			<div id="message">
				<?php if($_GET['fperror'] == 'post') : ?>
					<h2>Forgotten Password</h2>
					<p>You've come to this page by mistake</p>
				<?php endif; ?>
				<?php if($_GET['fperror'] === 'success') : ?>
					<h2>Success</h2>
					<p>Your password has been successfully changed!</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php include 'footer.php'; ?>
	</div>
</body>
</html>
