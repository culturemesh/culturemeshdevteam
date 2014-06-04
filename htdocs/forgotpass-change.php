<?php

if (!isset($_POST['email'] || !isset($_POST['password']) ||
	!isset($_POST['password_conf']))
  { header("Location: forgotpass.php?fperror=post"); }

include_once 'data/dal_query_handler.php';
include_once 'data/dal_user.php';

$con = QueryHandler::getDBConnection();
$email = $_POST['email'];
$password = mysqli_real_escape_string($con, $_POST['password']);
$password_conf = mysqli_real_escape_string($con, $_POST['password_conf']);

if ($password != $password)
  { header("Location: forgotpass.php?fperror=mismatch"); }

$password = md5($password);
if (User::changeUserPasswordByEmail($email, $password, $con))
{
	User::updateFPCode($email, null);
	header("Location: forgotpass.php?fperror=success");
}
else 
  { header("Location: forgotpass.php?fperror=failure"); }
?>
