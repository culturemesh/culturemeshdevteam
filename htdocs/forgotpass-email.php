<?php

// if not posted, go back
if (!isset($_POST['email']))
  { header("Location: forgotpass.php?email=false"); }
 
// else, press on
include_once 'data/dal_query_handler.php';
include_once 'data/dal_user.php';
include_once 'cm_email.php';

$con = QueryHandler::getDBConnection();
$email = mysqli_real_escape_string($con, $_POST['email']);
$fp_code = md5(microtime());

$updated = User::updateFPCode($email, $fp_code, $con);
mysqli_close($con);

// send email if successful
if(!$updated)
{
	CMEmail::sendChangePasswordEmail($email, $fp_code);
}
?>
