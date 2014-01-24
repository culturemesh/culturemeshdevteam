<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

if(strlen($_POST['email'])>1 && strlen($_POST['password']) >=6 && strlen($_POST['password_conf'])>=6){
    if($_POST['password'] != $_POST['password_conf']){
        header("Location: registration_error.php?error=password_mismatch");
    }//mismatch password
    else{
    	session_name("myDiaspora");
    	session_start();
    	
        include 'zz341/fxn.php';
        include 'data/dal_user.php';
        
        $conn = getDBConnection();
        
        $email = $conn->real_escape_string($_POST['email']);
        
        // check to see if email is already taken
        $email_in_use = User::checkEmailMatch($email);

        if(!$email_in_use){
        	$new_user = new UserDt();
        	$new_user->username = null;
        	$new_user->email = $email;
        	$new_user->password = md5($_POST['password']);
        	$new_user->role = 0;
        	
        	User::createUser($new_user, $conn);
        	
        	//if ($conn->error)
        	//{
        	//	header("Location: registration_error.php?error=registration_failed");
        	//}
        	//else
        	//{
        		$_SESSION['uid'] = User::getUserId($email);
        		mysqli_close($conn);
        		header("Location: profile_edit.php");
        	//}
        	
        }//valid new user
        else{
            header("Location: registration_error.php?error=user_exists");
        }//username already exists        
    }
}
else{
    header("Location: index.php");
}
?>
