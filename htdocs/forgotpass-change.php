<?php

if (!isset($_POST['email']) || !isset($_POST['password']) ||
	!isset($_POST['password_conf']))
  { header("Location: forgotpass.php?fperror=post"); }

else {
	include_once 'data/dal_query_handler.php';
	include_once 'data/dal_user.php';

	$con = QueryHandler::getDBConnection();
	$email = $_POST['email'];
	$fp_code = $_POST['fp_code'];
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$password_conf = mysqli_real_escape_string($con, $_POST['password_conf']);

	if ($password != $password_conf)
	  { header("Location: forgotpass.php?email={$email}&code={$fp_code}&fperror=mismatch"); }
	else if (strlen($password) < 8)
	  { header("Location: forgotpass.php?email={$email}&code={$fp_code}&fperror=short"); }
	else if (strlen($password) > 30)
	  { header("Location: forgotpass.php?email={$email}&code={$fp_code}&fperror=long"); }
	else {
		$password = md5($password);
		if (User::changeUserPasswordByEmail($email, $password, $con))
		{
			User::updateFPCode($email, null);
			header("Location: forgotpass.php?fperror=success");
		}
		else 
		  { header("Location: forgotpass.php?fperror=failure"); }
	}
}
?>
