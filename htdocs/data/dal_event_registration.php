<?php
/**
  * @operations - 
  * 	CREATE
  *         createEventRegistration
  *	READ
  *	    getAllEventRegistrations
  *	UPDATE
  *	    updateEventRegistration
  *	DELETE
  *         deleteEventRegistration
**/ 

include_once("zz341/fxn.php");
include_once("dal_event_registration-dt.php");

class EventRegistration
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createEventRegistration($eventreg_dt)
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
		
		if(!mysqli_query($con, "INSERT INTO event_registration 
			(id_guest, id_event, date_registered, job)
			VALUES (". $eventreg_dt->id_guest . ", ". $eventreg_dt->id_event .
			", NOW() , '" . $eventreg_dt->job  . "')"))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllEventRegistrations()
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
		
		$result = mysqli_query($con,"SELECT * FROM event_registration");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
		
		/*
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_guest'] . " " . $row['id_event'] . " " . $row['job'];
		  	  echo "<br>";
		}
		*/
		
		//mysqli_close($con);
	}
	
	
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateEventRegistration($eventreg_dt)
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
		
		if (!mysqli_query($con, "UPDATE event_registration 
			SET job='". $eventreg_dt->job . "'
			 WHERE id_guest=". $eventreg_dt->id_guest." AND id_event= ". $eventreg_dt->id_event ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteEventRegistration($eventreg_dt)
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
		//echo "</br>DELETE FROM event_registration 
		//	WHERE id_guest=". $eventreg_dt->id_guest." AND id_event=". $eventreg_dt->id_event;
		if (!mysqli_query($con, "DELETE FROM event_registration 
			WHERE id_guest=". $eventreg_dt->id_guest." AND id_event=". $eventreg_dt->id_event))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	function deleteRegistrationsByEvent($id)
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
		
		if (!mysqli_query($con, "DELETE FROM events 
			 WHERE id_event=". $id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	function deleteRegistrationsByUser($id)
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
		
		if (!mysqli_query($con, "DELETE FROM events 
			 WHERE id_guest=". $id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
}
?>
