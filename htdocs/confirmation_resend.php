<?php
	include "zz341/fxn.php";
	include "cm_email.php";
	include "data/dal_user.php";

	$email = User::getMemberEmail($_POST['uid']);
	$act_code = md5(microtime());
	User::updateActCode($_POST['uid'], $act_code, $con);
	if ( CMEmail::sendConfirmationEmail($email, $_POST['uid'], $act_code))
	  { echo 200; }
	else
	  { echo 500; }
?>
