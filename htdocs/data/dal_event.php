<?php
/**
  * @operations - 
  * 	CREATE
  *         createEvent
  *	READ
  *	    getAllEvents
  *	    getEventsByUserId
  *	    getEventsByNetworkId
  *	UPDATE
  *	    updateEvent
  *	DELETE
  *         deleteEventbyId
  *         deleteEventsbyUser
  *         deleteEventsbyNetwork
  *	    purgeEvent
**/ 

include_once("zz341/fxn.php");
include_once("dal_event-dt.php");

class Event
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createEvent($event_dt)
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
		
		if (!mysqli_query($con, "INSERT INTO events 
			(id_network, id_host, date_created, event_date, title, address_1, address_2, city, region, description)
			VALUES (". $event_dt->network_id . ", ". $event_dt->host_id . ", NOW(), '" . $event_dt->event_date . "', '" . $event_dt->title . "', '"
			. $event_dt->address_1 . "', '" . $event_dt->address_2 . "', '" . $event_dt->city . "', '" 
			. $event_dt->region . "', '" . $event_dt->description . "')"))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllEvents()
	{
		// if db connection was passed in to function, get it
		// if not, get dbconnection yourself
		if (func_num_args() == 1)
		{ $con = func_get_arg(0); }
		else
		{ $con = getDBConnection();}
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM events");
		
		if (func_num_args() < 1)
		{ mysqli_close($con); }
	
		return $result;
		/*
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_network'] . " " . $row['id_host'] . " " . $row['description'];
		  	  echo "<br>";
		}
		*/
	}
	
	public static function getEventsByUserId($id)
	{
		// if db connection was passed in to function, get it
		// if not, get dbconnection yourself
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM events WHERE id_host={$id}");
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	
		$events = array();
		
		while ($row = mysqli_fetch_array($result))
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
	}
	
	public static function getEventsByNetworkId($id)
	{
		// if db connection was passed in to function, get it
		// if not, get dbconnection yourself
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM events e, users u WHERE e.id_host=u.id AND id_network=" . $id);
		
		$events = array();
		
		while ($row = mysqli_fetch_array($result))
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
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	
		return $events;
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateEvent($event_dt)
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
		
		if (!mysqli_query($con, "UPDATE events 
			SET id_network=". $event_dt->network_id .", id_host=". $event_dt->host_id .
			", event_date='". $event_dt->event_date ."', address_1='". $event_dt->address_1 .
			"', address_2='". $event_dt->address_2 ."', city='". $event_dt->city .
			"', region='". $event_dt->region ."', description='". $event_dt->description . 
			"' WHERE id=". $event_dt->id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteEvent($event_dt)
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
		
		/*
			- must delete all references to this network
				inside the tables
				) event_registration
			
		*/
		if (!mysqli_query($con, "DELETE FROM events 
			 WHERE id=". $event_dt->id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deleteEventsByUser($id)
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
		
		/*
			- must delete all references to this network
				inside the tables
				) event_registration
			
		*/
		
		$user_events = Event::getEventsByUserId($id, $con);
		
		while($row = mysqli_fetch_array($user_events))
		{
			purgeEvent($row['id_host']);
		}
		
		
		if (!mysqli_query($con, "DELETE FROM events 
			 WHERE id_host=". $id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deleteEventsByNetwork($id)
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
		
		/*
			- must delete all references to this network
				inside the tables
				) event_registration
			
		*/
		
		$network_events = Event::getEventsByNetworkId($id, $con);
		
		while($row = mysqli_fetch_array($network_events))
		{
			purgeEvent($row['id_network']);
		}
		
		
		if (!mysqli_query($con, "DELETE FROM events 
			 WHERE id_network=". $id ))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	function purgeEvent($id, $con)
	{
		EventRegistration::deleteRegistrationsByEvent($id, $con);
	}
}
?>
