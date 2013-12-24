<?php

/**
  * @operations - 
  * 	CREATE
  *         insertUser
  *	READ
  *	    getAllUsers
  	    getUserById(id)
  *	UPDATE
  *	    updateNetwork
  *	DELETE
  *         deleteNetwork
**/ 
include_once("dal_user-dt.php");

class User
{
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllUsers()
	{
		//$con = getDBConnection();
		
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM users");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id'] . " " . $row['username'] . " " . $row['email'];
		  	  echo "<br>";
		}
		
		//mysqli_close($con);
	}
	
	public static function getUserById($id)
	{
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con, "SELECT * FROM users WHERE id = " . $id );
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id'] . " " . $row['username'] . " " . $row['email'];
		}
		
		mysqli_close($con);
	}
	
	////////////////////// CREATE OPERATIONS //////////////////////////////////////////////
	
	public static function insertUser($user_dt)
	{
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		mysqli_query($con, "INSERT INTO users (password, email, date_joined, last_login) 
			VALUES ('". $user_dt::password."', '". $user_dt::email ."', NOW(), NOW() )");
		
		mysqli_close($con);
	}
	
	////////////////////// UPDATE OPERATIONS //////////////////////////////////////////////
	
	public static function updateUser($user_dt)
	{
		$con = getDBConnection();
		
		// Check connection
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		mysqli_close($con);
	}
	////////////////////// DELETE OPERATIONS //////////////////////////////////////////////
}
?>
