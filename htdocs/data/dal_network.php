<?php
ini_set('display_errors',1);
/**
  * @operations - 
  * 	CREATE
  *         createNetwork
  *	READ
  *	    getAllNetworks
  *	UPDATE
  *	    updateNetwork
  *	DELETE
  *         deleteNetwork
  *         purgeNetwork
**/ 

include_once("zz341/fxn.php");
include_once("dal_network-dt.php");

include_once("dal_event.php");
include_once("dal_network_registration.php");
include_once("dal_post.php");

class Network
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createNetwork($network_dt)
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
		
		if (!mysqli_query($con, "INSERT INTO networks 
			(city, region, country, date_added, language)
			VALUES ('". $network_dt->city. "', '". $network_dt->region .
			"', '". $network_dt->country . "', NOW() , '" . $network_dt->language  . "')"))
		{
			echo "Error message: " . $con->error;
		}
		
		 
		if (func_num_args() < 2)
		{ mysqli_close($con); }
		echo "finished";
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllNetworks()
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
		
		$result = mysqli_query($con,"SELECT * FROM networks");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
		/*
		while($row = mysqli_fetch_array($data))
		  {
		  echo $row['city'] . " " . $row['date_added'];
		  echo "<br>";
		  }
		  */
	}
	
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateNetwork($network_dt)
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
		
		mysqli_query($con, "UPDATE networks 
			SET city='". $network_dt->city ."', region='". $network_dt->region .
			"', country='". $network_dt->country ."', language='". $network_dt->language .
			" WHERE id=". $network_dt->id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteNetwork($network_dt)
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
		
		purgeNetwork($network_dt->id, $con);
		
		mysqli_query($con, "DELETE FROM networks 
			WHERE id=". $network_dt->id);
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	function purgeNetwork($id, $con)
	{
		/*
			- must delete all references to this network
				inside the tables
				) network_registration, events, posts
				1) Events
				2) Posts
				3) Network_Registration
		*/
		Event::deleteEventsByNetwork($id, $con);
		Post::deletePostsByNetwork($id, $con);
		NetworkRegistration::deleteRegistrationsByNetwork($id, $con);
	}
}
?>
