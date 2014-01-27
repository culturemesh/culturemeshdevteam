<?php

/**
  * @operations - 
  * 	CREATE
  *         createUser
  *	READ
  *	    getAllUsers
  	    getUserById(id)
  	    getMemberEmail(id)
  *	UPDATE
  *	    updateUser
  *	DELETE
  *         deleteUser
**/ 

// if you pass in a connection, it will keep it open
// if you don't, the function opens and closes a connection
// 	by itself

include_once("zz341/fxn.php");
include_once("dal_user-dt.php");

include_once("dal_event_registration.php");
include_once("dal_event.php");
include_once("dal_network_registration.php");
include_once("dal_post.php");

class User
{
	////////////////////// CREATE OPERATIONS //////////////////////////////////////////////
	
	public static function createUser($user_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		if (!mysqli_query($con, "INSERT INTO users (username, password, role, email, register_date, last_login) 
			VALUES (NULL, '". $user_dt->password."', {$user_dt->role}, '". $user_dt->email ."', NOW(), NOW() )"))
		{
			echo "Error message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllUsers()
	{
		//$con = getDBConnection();
		if (func_num_args() == 1)
		{ $con = func_get_arg(0); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM users");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
		
		//mysqli_close($con);
	}
	
	public static function getMemberEmail($id){
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		$result = mysqli_query($con, "SELECT email FROM users WHERE id={$id}");
		$row = mysqli_fetch_array($result);
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $row['email'];
    	}
	
    	public static function getUserId($email){
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		$result = mysqli_query($con, "SELECT id FROM users WHERE email='{$email}'");
		$row = mysqli_fetch_array($result);
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $row['id'];
    	}
    	
	public static function getUserById($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con, "SELECT * FROM users WHERE id=" . $id );
		
		$user = new UserDT();
		
		while ($row = mysqli_fetch_array($result))
		{
			$user->id = $row['id'];
			$user->username = $row['username'];
			$user->first_name = $row['first_name'];
			$user->last_name = $row['last_name'];
			$user->email = $row['email'];
			$user->password = $row['password'];
			$user->role = $row['role'];
			$user->register_date = $row['register_date'];
			$user->last_login = $row['last_login'];
			$user->gender = $row['gender'];
		}
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	
		return $user;
		/*
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id'] . " " . $row['username'] . " " . $row['email'];
		}
		*/
	}
	
	public static function userLoginQuery($email, $password)
	{
		if (func_num_args() == 3)
		{ $con = func_get_arg(2); }
		else
		{ $con = getDBConnection();}
		
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con, "SELECT * FROM users WHERE email='" . $email . "' AND password='" . $password . "'");
		
		if (func_num_args() < 3)
		{ mysqli_close($con); }
	
		return $result;
	}
	
	public static function checkEmailMatch($email)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con, "SELECT email FROM users WHERE email='{$email}'");
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	
		$row = mysqli_fetch_array($result);
		
		if ($row['email'] == NULL) { return false; }  // email not in use
		else { return true; } // email free
	}
	
	////////////////////// UPDATE OPERATIONS //////////////////////////////////////////////
	
	public static function updateUser($user_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		mysqli_query($con, "UPDATE users
			SET username='". $user_dt->username ."', first_name='". $user_dt->id_network ."', last_name='". $user_dt->last_name .
			"', email='". $user_dt->email ."', password='". $user_dt->password ."', role=". $user_dt->role .", gender='". $user_dt->gender .
			" WHERE id=". $user_dt->id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS //////////////////////////////////////////////
	public static function deleteUser($user_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		// delete user everywhere else for safety
		purgeUser($user_dt->id, $con);
		
		mysqli_query($con, "DELETE FROM users 
			WHERE id=". $user_dt->id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	function purgeUser($id, $con)
	{
		/*
		1. Event Registration
		2. Event
		3. Network Registration 
		4. Posts
		*/
		EventRegistration::deleteRegistrationsByUser($id, $con);
		Event::deleteEventsByUser($id, $con);
		//Post::deletePostsByUser($id, $con);	// may not be necessary
		NetworkRegistration::deleteRegistrationByUser($id, $con);		
	}
}
?>
