<?php
ini_set('display_errors', true);

if($_POST['email'] && $_POST['password']){
		include 'zz341/fxn.php';
		
		$conn = getDBConnection();
		$email = escape_string($_POST['email']);
		$pass = escape_string($_POST['password']);
		
		if($conn->connect_errno){
		    printf("Connect failed! %s\n", $conn->connect_error);
		    exit();
		}//db conect failure
		
		if strlen($email) > 18
		{
			echo "3";
		}
		else if strlen($password) > 18
		{
			echo "4";
		}
		else
		{
			$uQuery = $conn->query("SELECT * FROM users WHERE email_address='$email' AND password='".md5($pass)."'");
			$data = $uQuery->fetch_assoc();
		
			$_SESSION['username'] = $data["email_address"]; // for now
			
			if($data["email_address"] != NULL){
				echo "1";
			}//valid new user
			
			else
			{
			    header("Location: login_error.php?error=user_doesn't_exist");
			}//username already exists
		}
}
else{
    header("Location: index.php");
}
?>
