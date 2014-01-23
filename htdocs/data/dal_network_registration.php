<?php

/**
  * @operations - 
  * 	CREATE
  *         createNetRegistration
  *	READ
  *	    getAllNetRegistrations
  *	UPDATE
  *	    updateNetRegistration
  *	DELETE
  *         deleteNetRegistration
  *         deleteRegistrationsByUser
  *         deleteRegistrationsByNetwork
**/ 

include_once("zz341/fxn.php");
include_once("dal_network_registration-dt.php");

class NetworkRegistration
{
	////////////////////// CREATE OPERATIONS ////////////////////////////////////////
	public static function createNetRegistration($netreg_dt)
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
		
		if (!mysqli_query($con, "INSERT INTO network_registration
			(id_user, id_network, join_date) 
			VALUES (". $netreg_dt->id_user . ", ". $netreg_dt->id_network . ", NOW())"))
		{
			echo "Error message: " . $con->error;	
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllNetRegistrations()
	{
		if (func_num_args() == 1)
		{ $con = func_get_arg(0); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM network_registration");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
		
		/*
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_user'] . " " . $row['id_network'] . " " . $row['join_date'];
		  	  echo "<br>";
		}
		*/
		
		//mysqli_close($con);
	}
	
	public static function getMemberCount($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT COUNT(id_network) as member_count FROM network_registration WHERE id_network={$id}");
		
		while ($row = mysqli_fetch_array($result))
			$count = $row['member_count'];
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $count;
	}
	
	public static function checkRegistration($netreg_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT COUNT(id_user) AS user_count FROM network_registration WHERE id_user={$netreg_dt->id_user} AND id_network={$netreg_dt->id_network}");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		$row = mysqli_fetch_array($result);
		
		if ($row['user_count'] == 0) 
		{return false;}
		else
		{return true;}
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateNetRegistration($netreg_dt)
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
		
		mysqli_query($con, "UPDATE network_registration
			SET id_user=". $netreg_dt->id_user .", id_network=". $netreg_dt->id_network .
			", join_date=". $netreg_dt->join_date . 
			" WHERE id_user=". $netreg_dt->id_user ." AND id_network=". $netreg_dt->id_network);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteNetRegistration($netreg_dt)
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
		
		mysqli_query($con, "DELETE FROM network_registration 
			WHERE id_user=". $netreg_dt->id_user ." AND id_network=". $netreg_dt->id_network);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deleteRegistrationByUser($id)
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
		
		mysqli_query($con, "DELETE FROM network_registration 
			WHERE id_user=". $id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deleteRegistrationsByNetwork($id)
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
		
		mysqli_query($con, "DELETE FROM network_registration 
			WHERE id_network=". $id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
}
?>
