<?php
//ini_set('display_errors',1);
/**
  * @operations - 
  * 	CREATE
  *         createNetwork
  *	READ
  *	    getAllNetworks
  *    	    getTopFourNetworks
  *	    getNetworksByCountry
  *	    getNetworksByLanguage
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
			(city_cur, country_cur, city_origin, region_origin, country_origin, language_origin, network_class, date_added)
			VALUES ('". $network_dt->city_cur. "', '". $network_dt->country_cur. "', '". $network_dt->city_origin. "', '". $network_dt->region_origin .
			"', '". $network_dt->city_origin. "', '". $network_dt->country_origin. "', '". $network_dt->language_origin . "', NOW())"))
		{
			echo "Error message: " . $con->error;
		}
		
		 
		if (func_num_args() < 2)
		{ mysqli_close($con); }
		echo "finished";
	}

	/*
	 * Creates a network and returns
	 * THE ID
	 */	
	public static function launchNetwork($network, $con = null)
	{
		$statement = "INSERT INTO networks ";

		switch ($network->network_class){
		case "co":
			echo $network->country_origin;
			$statement = $statement."(id_city_cur, city_cur, id_country_cur, country_cur,
				id_country_origin, country_origin, network_class) 
				VALUES ({$network->id_city_cur}, '{$network->city_cur}', 
				{$network->id_country_cur}, '{$network->country_cur}', 
				{$network->id_country_origin}, '{$network->country_origin}', 
				'{$network->network_class}')";
			break;
		case "cc":
			$statement = $statement."(id_city_cur, city_cur, id_country_cur, country_cur,
				id_city_origin, city_origin,
				id_country_origin, country_origin, network_class) 
				VALUES ({$network->id_city_cur}, '{$network->city_cur}',
				{$network->id_country_cur}, '{$network->country_cur}',
				{$network->id_city_origin}, '{$network->city_origin}',
				{$network->id_country_origin}, '{$network->country_origin}', 
				'{$network->network_class}')";
			break;
		case "rc":
			$statement = $statement."(id_city_cur, city_cur, id_country_cur, country_cur,
				id_region_origin, region_origin,
				id_country_origin, country_origin, network_class) 
				VALUES ({$network->id_city_cur}, '{$network->city_cur}',
				{$network->id_country_cur}, '{$network->country_cur}',
				{$network->id_region_origin}, '{$network->region_origin}',
				{$network->id_country_origin}, '{$network->country_origin}', 
				'{$network->network_class}')";
			break;
		case "_l":
			$statement = $statement."(id_city_cur, city_cur, id_country_cur, country_cur,
				id_language_origin, language_origin, network_class) 
				VALUES ({$network->id_city_cur}, '{$network->city_cur}', 
				{$network->id_country_cur}, '{$network->country_cur}', 
				{$network->id_language_origin}, '{$network->language_origin}',
				'{$network->network_class}')";
			break;
		}

		$must_close = false;
		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }

		$result = mysqli_query($con, $statement); 

		echo $con->error;
		if ($result)
		{
			$l_network = null;
			$id = null;
			switch ($network->network_class)
			{
			case "co":
				$query = array(0, $network->city_cur, $network->country_cur, $network->country_origin);
				$l_network = Network::getNetworksByCO($query, $con);
				break;
			case "cc":
				$query = array(0, $network->city_cur, $network->country_cur, $network->city_origin, $network->country_origin);
				$l_network = Network::getNetworksByCC($query, $con);
				break;
			case "rc":
				$query = array(0, $network->city_cur, $network->country_cur, $network->region_origin, $network->country_origin);
				$l_network = Network::getNetworksByRC($query, $con);
				break;
			case "_l":
				$query = array(0, $network->city_cur, $network->country_cur, $network->language_origin);
				var_dump( $query);
				$l_network = Network::getNetworksByL($query, $con);
				break;
			}

			// Close network
			if ($must_close)
			{
				mysqli_close($con);
			}

			// fetch id
			while ($row = mysqli_fetch_array($l_network))
				$id = $row['id'];

			return $id;
		}
		else 
			return false;
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
	
	/***
	   * RETURNS an array with the top four networks' info, post and member count
	***/
	public static function getTopFourNetworks()
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
		
		// GET TABLE WITH  TOP FOUR NETWORKS +
		// 	MEMBER COUNT
		$result = mysqli_query($con,
			"SELECT n.id, n.city_cur, n.country_cur, n.city_origin, n.region_origin, n.country_origin, n.language_origin, n.network_class, nr.member_count, p.post_count
			FROM networks n 
			JOIN (SELECT id_network, COUNT(id_network) AS member_count
                                    FROM network_registration
                                    GROUP BY id_network
                                    ORDER BY member_count DESC) nr  
			ON n.id = nr.id_network
                        LEFT JOIN (SELECT id_network, COUNT(id_network) as post_count
                                   FROM posts 
                                   GROUP BY id_network) p
			ON n.id = p.id_network AND nr.id_network = p.id_network
                        GROUP BY n.id
			ORDER BY nr.member_count DESC
                        LIMIT 0,4");
		
		$networks = array();
		
		
		// populate array with network objects
		while ($row = mysqli_fetch_array($result))
		{
			$network_dt = new NetworkDT();
			
			$network_dt->id = $row['id'];
			$network_dt->city_cur = $row['city_cur'];
			$network_dt->country_cur = $row['country_cur'];
			$network_dt->city_origin = $row['city_origin'];
			$network_dt->region_origin = $row['region_origin'];
			$network_dt->country_origin = $row['country_origin'];
			$network_dt->language_origin = $row['language_origin'];
			$network_dt->network_class = $row['network_class'];
			$network_dt->member_count = $row['member_count'];
			$network_dt->post_count = $row['post_count'];
			
			array_push($networks, $network_dt);
		}
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $networks;
	}
	
	public static function getNetworkById($id)
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
		
		  $result = mysqli_query($con,"SELECT * FROM networks WHERE id={$id}");
		
		  $network_dt = Network::fillNetworkDT($result);

		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $network_dt;
	}
	
	public static function getNetworksByOrigin($origin)
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
		
		  $result = mysqli_query($con,"SELECT * FROM networks WHERE city_origin='{$origin}' OR region_origin='{$origin}' OR country_origin='{$origin}'");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $result;
	}
	
	public static function getNetworksByCity($city)
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
		
		  $result = mysqli_query($con,"SELECT * FROM networks WHERE city_cur={$city}");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $result;
	}
	
	public static function getNetworksByLanguage($language)
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
		
		  $result = mysqli_query($con,"SELECT * FROM networks WHERE language_origin='{$language}'");
		

		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $result;
	}
	
	public static function getNetworksByCountry($country)
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
		
		  $result = mysqli_query($con,"SELECT * FROM networks WHERE country_origin='{$country}'");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $result;
	}

	public static function getNetworksByCO($query, $con=null) {
		$must_close = false;
		if ($con == null)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND country_origin='{$query[3]}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result)
			echo $con->error;
		else
			return $result;
	}

	public static function getNetworksByL($query, $con=null) {
		$must_close = false;
		if ($con == null)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		echo "SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND language_origin='{$query[3]}'";

		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND language_origin='{$query[3]}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result){
			echo $con->error;
			return false;
		}
		else
			return $result;
	}

	public static function getNetworksByRC($query, $con=null) {
		$must_close = false;
		if ($con == null)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND region_origin='{$query[3]}' AND country_origin='{$query[4]}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result) {
			echo $con->error;
			return false;
		}
		else
			return $result;
	}
	
	public static function getNetworksByCC($query, $con=null) {
		$must_close = false;
		if ($con == null)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND city_origin='{$query[3]}' AND country_origin='{$query[4]}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result) {
			echo $con->error;
			return false;
		}
		else
			return $result;
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
			SET city_cur='". $network_dt->city_cur ."', country_cur='". $network_dt->country_cur .
			"', city_origin='". $network_dt->city_origin ."', region_origin='". $network_dt->region_origin .
			"', country_origin='". $network_dt->country_origin ."', language_origin='". $network_dt->language_origin . 
			"', network_class='".$network_dt->network_class .
			"' WHERE id=". $network_dt->id);
		
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
	
	private static function fillNetworkDT($results)
	{
		$network_dt = new NetworkDT();
		
		while ($row = mysqli_fetch_array($results))
		{	
			$network_dt->id = $row['id'];
			$network_dt->city_cur = $row['city_cur'];
			$network_dt->country_cur = $row['country_cur'];
			$network_dt->city_origin = $row['city_origin'];
			$network_dt->region_origin = $row['region_origin'];
			$network_dt->country_origin = $row['country_origin'];
			$network_dt->language_origin = $row['language_origin'];
			$network_dt->network_class = $row['network_class'];
			$network_dt->member_count = $row['member_count'];
			$network_dt->post_count = $row['post_count'];
		}
		
		return $network_dt;
	}
}
?>
