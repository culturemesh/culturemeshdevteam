<?php
    ini_set("display_errors", true);
    session_name("myDiaspora");
    session_start();
    include_once 'data/dal_user.php';
    include_once 'data/dal_query_handler.php';
    include_once 'zz341/fxn.php';
    $con = null;

    if(!isset($_POST['username']) && !isset($_POST['password'])){

	include 'admin_login.php';
    }
    else{
	$con = QueryHandler::getDBConnection();
	$user = mysqli_real_escape_string($con, $_POST['username']);
	$pass = md5($_POST['password']);
	$result = User::loginAdmin($user, $pass, $con);
	if(mysqli_num_rows($result) > 0) {
		$ppre = "admin_pg";
		include 'page_tpl.php';
	}
	else {
		$login = false;
		include 'admin_login.php';
	}
    }
?>
