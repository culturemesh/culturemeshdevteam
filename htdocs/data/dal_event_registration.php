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
include_once('dal_query_handler.php');

class EventRegistration
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createEventRegistration($uid, $eid, $con=NULL)
	{
		$query = <<<SQL
			INSERT INTO event_registration
			(id_guest, id_event)
			VALUES
			($uid, $eid)
SQL;

		echo $query;
		return QueryHandler::executeQuery($query, $con);
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
	
	public static function getEventRegistrationsByUserId($id)
	{
		//$con = getDBConnection();
		
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM event_registration er, events e WHERE er.id_event = e.id AND er.id_guest={$id}");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		$events = array();
		
		while($row = mysqli_fetch_array($result))
		{
			$event_dt = new EventDT();
		  	$event_dt->id = $row['id'];
		  	$event_dt->network_id = $row['network_id'];
		  	$event_dt->host_id = $row['host_id'];
		  	$event_dt->date_created = $row['date_created'];
		  	$event_dt->event_date = $row['event_date'];
		  	$event_dt->title = $row['title'];
		  	$event_dt->email = $row['email'];
		  	$event_dt->address_1 = $row['address_1'];
		  	$event_dt->address_2 = $row['address_2'];
		  	$event_dt->city = $row['city'];
		  	$event_dt->region = $row['region'];
		  	$event_dt->description = $row['description'];
		  	
		  	array_push($events, $event_dt);
		}
		
		return $events;
		
		/*
		
		*/
		
		//mysqli_close($con);
	}

	public static function getEventRegistrationByUid($uid, $con=NULL)
	{
		$query = <<<SQL
			SELECT *
			FROM event_registration
			WHERE id_guest=$uid
SQL;
		return QueryHandler::executeQuery($query, $con);
	}

	public static function checkAttendance($uid, $eid, $con=NULL)
	{
		$query = <<<SQL
			SELECT *
			FROM event_registration
			WHERE id_guest=$uid
			AND id_event=$eid
SQL;
		return QueryHandler::executeQuery($query, $con);
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
