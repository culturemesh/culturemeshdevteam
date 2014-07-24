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
include_once("dal_query_handler.php");

class Event
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createEvent($event_dt, $con=NULL)
	{
		
		$query = <<<SQL
			INSERT INTO events 
			(id_network, id_host, date_created, 
			event_date, title, address_1, address_2,
			 city, region, description)
			VALUES ($event_dt->network_id, $event_dt->host_id, NOW(),
				'$event_dt->event_date', '$event_dt->title', 
				'$event_dt->address_1', '$event_dt->address_2', 
				'$event_dt->city', '$event_dt->region', 
				'$event_dt->description')
SQL;

		return QueryHandler::executeQuery($query, $con);
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
	
	// used for getting events hosted
	public static function getEventsByUserId($id, $con=NULL)
	{
		$query = <<<SQL
			SELECT * 
			FROM events e, users u 
			WHERE e.id_host=u.id 
			AND id_host=$id
			ORDER BY e.id_network
SQL;
		//$result = mysqli_query($con,"SELECT * FROM events e, users u WHERE e.id_host=u.id AND id_host={$id}");
		
		// execute
		$result = QueryHandler::executeQuery($query, $con);

		$events = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$event_dt = new EventDT();
			
			$event_dt->id = $row['id'];
			$event_dt->id_network = $row['id_network'];
			$event_dt->id_host = $row['id_host'];
			$event_dt->date_created = $row['date_created'];
			$event_dt->event_date = $row['event_date'];
			$event_dt->title = $row['title'];
			$event_dt->email = $row['email'];
			$event_dt->address_1 = $row['address_1'];
			$event_dt->address_2 = $row['address_2'];
			$event_dt->city = $row['city'];
			$event_dt->region = $row['region'];
			$event_dt->description = $row['description'];
			$event_dt->username = $row['username'];
			$event_dt->first_name = $row['first_name'];
			$event_dt->last_name = $row['last_name'];
			$event_dt->img_link = $row['img_link'];

			array_push($events, $event_dt);
		}
	
		return $events;
	}
	
	public static function getEventsByNetworkId($id, $con=NULL)
	{

		$query = <<<SQL
			SELECT e.id AS event_id, e.*, u.* 
			FROM events e, users u 
			WHERE event_date >= CURDATE() 
			AND e.id_host=u.id 
			AND id_network=$id
			ORDER BY id_network
SQL;
		
		$result = QueryHandler::executeQuery($query, $con);	

		$events = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$event_dt = new EventDT();
			
			$event_dt->id = $row['event_id'];
			$event_dt->id_network = $row['id_network'];
			$event_dt->id_host = $row['id_host'];
			$event_dt->date_created = $row['date_created'];
			$event_dt->event_date = $row['event_date'];
			$event_dt->title = $row['title'];
			$event_dt->email = $row['email'];
			$event_dt->address_1 = $row['address_1'];
			$event_dt->address_2 = $row['address_2'];
			$event_dt->city = $row['city'];
			$event_dt->region = $row['region'];
			$event_dt->description = $row['description'];
			$event_dt->username = $row['username'];
			$event_dt->first_name = $row['first_name'];
			$event_dt->last_name = $row['last_name'];
			$event_dt->img_link = $row['img_link'];
			
			array_push($events, $event_dt);
		}
		
		return $events;
	}

	public static function getEventsByNetworkId_D($id, $con=NULL)
	{

		$query = <<<SQL
			SELECT e.id AS event_id, e.*, u.* 
			FROM events e, users u 
			WHERE event_date >= NOW() - INTERVAL 1 MONTH
			AND e.id_host=u.id 
			AND id_network=$id
			ORDER BY event_date 
SQL;
		
		$result = QueryHandler::executeQuery($query, $con);	

		$events = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$event_dt = new EventDT();
			
			$event_dt->id = $row['event_id'];
			$event_dt->id_network = $row['id_network'];
			$event_dt->id_host = $row['id_host'];
			$event_dt->date_created = $row['date_created'];
			$event_dt->event_date = $row['event_date'];
			$event_dt->title = $row['title'];
			$event_dt->email = $row['email'];
			$event_dt->address_1 = $row['address_1'];
			$event_dt->address_2 = $row['address_2'];
			$event_dt->city = $row['city'];
			$event_dt->region = $row['region'];
			$event_dt->description = $row['description'];
			$event_dt->username = $row['username'];
			$event_dt->first_name = $row['first_name'];
			$event_dt->last_name = $row['last_name'];
			$event_dt->img_link = $row['img_link'];
			
			array_push($events, $event_dt);
		}
		
		return $events;
	}

	public static function getEventsYourNetworks($id, $con=NULL)
	{
		$query = <<<SQL
			SELECT *, u.img_link AS usr_image
			FROM events e, users u, networks n
			WHERE id_network IN (
				SELECT id_network
				FROM network_registration
				WHERE id_user=$id)
			AND e.id_host = u.id
			AND n.id = e.id_network
			ORDER BY e.id_network
SQL;

		$result = QueryHandler::executeQuery($query, $con);

		$events = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$event_dt = new EventDT();
			
			$event_dt->id = $row['id'];
			$event_dt->id_network = $row['id_network'];
			$event_dt->id_host = $row['id_host'];
			$event_dt->date_created = $row['date_created'];
			$event_dt->event_date = $row['event_date'];
			$event_dt->title = $row['title'];
			$event_dt->email = $row['email'];
			$event_dt->address_1 = $row['address_1'];
			$event_dt->address_2 = $row['address_2'];
			$event_dt->city = $row['city'];
			$event_dt->region = $row['region'];
			$event_dt->description = $row['description'];
			$event_dt->username = $row['username'];
			$event_dt->first_name = $row['first_name'];
			$event_dt->last_name = $row['last_name'];
			$event_dt->img_link = $row['usr_image'];

			// add network data
			$event_dt->network_class = $row['network_class'];
			$event_dt->city_cur = $row['city_cur'];
			$event_dt->region_cur = $row['region_cur'];
			$event_dt->country_cur = $row['country_cur'];
			$event_dt->city_origin = $row['city_origin'];
			$event_dt->region_origin = $row['region_origin'];
			$event_dt->country_origin = $row['country_origin'];
			$event_dt->language_origin = $row['language_origin'];
			
			array_push($events, $event_dt);
		}

		return $events;
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateEvent($event_dt, $con=NULL)
	{
		$query = <<<SQL
			UPDATE events 
			SET event_date='$event_dt->event_date', address_1='$event_dt->address_1',
			address_2='$event_dt->address_2', city='$event_dt->city', region='$event_dt->region',
			description='$event_dt->description'
			WHERE id=$event_dt->id
SQL;
		
		return QueryHandler::executeQuery($query, $con);
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
