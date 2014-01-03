<?php

/**
  * @operations - 
  * 	CREATE
  *         createSuggestedNetwork
  *	READ
  *	    getAllSuggestedNetworks
  *	UPDATE
  *	    updateSuggestedNetwork
  *	DELETE
  *         deleteSuggestedNetwork
**/ 

include_once("zz341/fxn.php");
include_once("dal_suggested_network-dt.php");

class SuggestedNetwork
{
	////////////////////// CREATE OPERATIONS ////////////////////////////////////////
	public static function createSuggestedNetwork($sugnet_dt)
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
		
		if (!mysqli_query($con, "INSERT INTO suggested_networks
			(city, region, language, date_suggested) 
			VALUES ('". $sugnet_dt->city . "', '". $sugnet_dt->region . "', '". $sugnet_dt->language ."', NOW())"))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllSuggestedNetworks()
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
		
		$result = mysqli_query($con,"SELECT * FROM suggested_networks");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
		
		/*
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['city'] . " " . $row['region'] . " " . $row['language'] . " " . $row['date_suggested'];
		  	  echo "<br>";
		}
		*/
		
		//mysqli_close($con);
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateSuggestedNetwork($sugnet_dt)
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
		
		if (!mysqli_query($con, "UPDATE suggested_networks
			SET city='". $sugnet_dt->city ."', region='". $sugnet_dt->region .
			"', language='". $sugnet_dt->language . 
			"' WHERE id=". $sugnet_dt->id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteSuggestedNetwork($sugnet_dt)
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
		
		if (!mysqli_query($con, "DELETE FROM suggested_networks 
			WHERE id=". $sugnet_dt->id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
}
?>
