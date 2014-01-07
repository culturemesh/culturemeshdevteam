<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
/**
  * Success - 1
  * Login info incorrect - 2
  * Email is too long - 3
  * Password is too long - 4
  * Database connection failed - 5
**/

if($_POST['email'] && $_POST['password']){
		include 'zz341/fxn.php';
		include_once("data/dal_user.php");
		include_once("data/dal_user-dt.php");
		
		session_name("myDiaspora");
		session_start();
		//$conn = getDBConnection();
		
		//$user = new UserDT();
		$email = mysql_escape_string($_POST['email']);
		$pass = mysql_escape_string($_POST['password']);
		//echo $pass;
		
		if($conn->connect_errno){
		    printf("Connect failed! %s\n", $conn->connect_error);
		    echo "5";
		    exit();
		}//db conect failure
		
		if (strlen($email) > 50)
		{
			echo "3";
		}
		else if (strlen($pass) > 18)
		{
			echo "4";
		}
		else
		{	
			$pass = md5($pass);
			$data = User::userLoginQuery($email, $pass);
			//mysqli_close($conn);
			
			$result = $data->fetch_assoc();
			
			if($result["email"] != NULL){
				//session_name("myDiaspora");
				//session_start();
                		$_SESSION['uid'] = getMemberUID($email);
				echo "1";
			}
			else
			{
			    echo "2";
			}
		}
}
else{
    //header("Location: index.php");
    echo "Invalid Post";
}
?>
