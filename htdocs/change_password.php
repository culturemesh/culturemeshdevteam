<?php
ini_set("display_errors", 1);
include "data/dal_user.php";

// if variables aren't posted, probably don't need to be here
if (!isset($_POST['password']) || !isset($_POST['password_conf']) || !isset($_POST['email'])) 
	exit("You have no access here");

// if passwords do not match, redirect to edit
if ($_POST['password'] != $_POST['password_conf'])
	header("Location: profile_edit.php?cp_error=different+passwords");

// start the process
session_name("myDiaspora");
session_start();

$c_email = mysql_escape_string($_POST['email']);
$q_password = md5($_POST['cur_password']);

$data = User::userLoginQuery($c_email, $q_password);
$result = $data->fetch_assoc();

// if user exists, and has entered correct login information
if($result['email'] != NULL)
{
	if (User::changeUserPassword($_SESSION['uid'], md5($_POST['password'])))
		header("Location: profile_edit.php?cp_error=success");
	else
		header("Location: profile_edit.php?cp_error=failure");
}
else
{
	header("Location: profile_edit.php?cp_error=no+match");
}
?>
