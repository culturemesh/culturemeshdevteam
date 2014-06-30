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
include_once("dal_query_handler.php");

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
		$col_names = "INSERT INTO networks (";
		$values = "VALUES (";

		// check network for null values
		// get array with all the things we'll use
		$network_keys = get_object_vars($network);
		$statement_values = array();
		/*
		foreach ($network_keys as $key)
		{
			if ($network->$key == null)
				continue;
			array_push($statement_values, array($key, $network[$key]));
		}
		 */
		foreach ($network as $key => $value)
		{
			if ($value == null)
				continue;
			array_push($statement_values, array($key, $value));
		}
		$sv_length = count($statement_values);

		// now generate sql statement with all our stuff
		for ($i = 0; $i < $sv_length; $i++)
		{
			// add key to column names statement
			$col_names .= $statement_values[$i][0];
			if ($sv_length - $i == 1)
			  { $col_names .= ") "; }
			else
			  { $col_names .= ", "; }

			// add value to VALUES values
			$sv_next = $statement_values[$i][1];
			if (gettype($sv_next) == "string")
			  { $values .= "'".$sv_next."'"; }
			else
			  { $values .= $sv_next; }

			// add a comma ( or parenthesis if
			// we've reached the end
			if ($sv_length - $i == 1)
			  { $values .= ") "; }
			else
			  { $values .= ", "; }
		}

		$statement = $col_names . $values;

		/*
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
		 */

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

		//echo $statement;
		$result = mysqli_query($con, $statement); 

		echo $con->error;
		if ($result)
		{
			$l_network = null;
			$id = null;
			switch ($network->network_class)
			{
			case "co":
				$query = array(0, $network->city_cur, $network->region_cur, $network->country_cur, null, null, $network->country_origin);
				$l_network = Network::getNetworksByCO($query, $con);
				break;
			case "cc":
				$query = array(0, $network->city_cur, $network->region_cur, $network->country_cur, $network->city_origin, $network->region_origin, $network->country_origin);
				$l_network = Network::getNetworksByCC($query, $con);
				break;
			case "rc":
				$query = array(0, $network->city_cur, $network->region_cur, $network->country_cur, null, $network->region_origin, $network->country_origin);
				$l_network = Network::getNetworksByRC($query, $con);
				break;
			case "_l":
				$query = array(0, $network->city_cur, $network->region_cur, $network->country_cur, $network->language_origin);
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

		if(!$result = mysqli_query($con,
			"SELECT n.id, n.city_cur, n.region_cur, n.country_cur, n.city_origin, n.region_origin, n.country_origin, n.language_origin, n.network_class, nr.member_count, p.post_count
			FROM networks n 
			JOIN (SELECT id_network, COUNT(id_network) AS member_count
                                    FROM network_registration
                                    GROUP BY id_network
                                    ORDER BY member_count DESC) nr  
			ON n.id = nr.id_network
                        LEFT JOIN (SELECT p.id_network, COUNT(p.id_network) + IFNULL(pr.reply_count,0) AS post_count
				FROM posts p
				LEFT JOIN (SELECT id_network, COUNT(id_network) AS reply_count
					FROM post_replies
					GROUP BY id_network) pr
				ON p.id_network=pr.id_network
                                GROUP BY id_network) p
			ON n.id = p.id_network AND nr.id_network = p.id_network
                        GROUP BY n.id
			ORDER BY nr.member_count DESC, p.post_count DESC
                        LIMIT 0,4"))
                {
                	echo "Error message: " . $con->error;
                }
		
		$networks = array();
		
		// populate array with network objects
		while ($row = mysqli_fetch_array($result))
		{
			$network_dt = new NetworkDT();
			
			$network_dt->id = $row['id'];
			$network_dt->city_cur = $row['city_cur'];
			$network_dt->region_cur = $row['region_cur'];
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
		
		$cols = array("city_cur", "region_cur", "country_cur");	

		$statement = "SELECT * FROM networks WHERE ";

		// Get parameters from query, put them into query
		for ($i = 0; $i < count($cols); $i++)
		{
			if ($query[$i+1] == null)
			{
				$statement .= $cols[$i]. " IS NULL ";
			}
			else
			{
				$statement .= $cols[$i]."=";
				if (gettype($query[$i+1]) == "string")
				  { $statement .= "'".$query[$i+1]."'"; }
				else
				  { $statement .= $query[$i+1]; }
			}

			// add an AND if we're not at the end
			$statement .= " AND city_origin IS NULL AND region_origin IS NULL AND "; 
			// at the end, add country_origin
			if (count($cols) - $i == 1)
			  { $statement .= "country_origin='".$query[$i+4]."'"; }
		}
		//echo $statement;
		/*
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND country_origin='{$query[3]}'");
		 */
		$result = mysqli_query($con,$statement);
		
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

		$cols = array("city_cur", "region_cur", "country_cur");	

		$statement = "SELECT * FROM networks WHERE ";

		// Get parameters from query, put them into query
		for ($i = 0; $i < count($cols); $i++)
		{
			if ($query[$i+1] == null)
			{
				$statement .= $cols[$i]. " IS NULL ";
			}
			else
			{
				$statement .= $cols[$i]."=";
				if (gettype($query[$i+1]) == "string")
				  { $statement .= "'".$query[$i+1]."'"; }
				else
				  { $statement .= $query[$i+1]; }
			}

			// add an AND if we're not at the end
			$statement .= " AND "; 
			// at the end, add country_origin
			if (count($cols) - $i == 1)
			  { $statement .= "language_origin='{$query[$i+2]}'"; }
		}
		//echo $statement;

		$result = mysqli_query($con, $statement);
		/*
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND language_origin='{$query[3]}'");
		 */
		
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

		$cols = array("city_cur", "region_cur", "country_cur");	

		$statement = "SELECT * FROM networks WHERE ";

		// Get parameters from query, put them into query
		for ($i = 0; $i < count($cols); $i++)
		{
			if ($query[$i+1] == null)
			{
				$statement .= $cols[$i]. " IS NULL ";
			}
			else
			{
				$statement .= $cols[$i]."=";
				if (gettype($query[$i+1]) == "string")
				  { $statement .= "'".$query[$i+1]."'"; }
				else
				  { $statement .= $query[$i+1]; }
			}

			// add an AND if we're not at the end
			$statement .= " AND city_origin IS NULL AND "; 
			// at the end, add country_origin
			if (count($cols) - $i == 1)
			  { $statement .= "region_origin='{$query[$i+3]}' AND country_origin='".$query[$i+4]."'"; }
		}
		//echo $statement;
		//var_dump($query);

		$result = mysqli_query($con,$statement);
		/*
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND region_origin='{$query[3]}' AND country_origin='{$query[4]}'");
		 */
		
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

		$cols = array("city_cur", "region_cur", "country_cur");	

		$statement = "SELECT * FROM networks WHERE ";

		// Get parameters from query, put them into query
		for ($i = 0; $i < count($cols); $i++)
		{
			// if something is null, make it so
			if ($query[$i+1] == null)
			{
				$statement .= $cols[$i]. " IS NULL ";
			}
			else
			{
				$statement .= $cols[$i]."=";
				if (gettype($query[$i+1]) == "string")
				  { $statement .= "'".$query[$i+1]."'"; }
				else
				  { $statement .= $query[$i+1]; }
			}

			// add an AND if we're not at the end
			$statement .= " AND "; 
			// at the end, add origins
			if (count($cols) - $i == 1)
			{ 
				$statement .= "city_origin='{$query[$i+2]}' AND ";
				if ($query[$i+3] == null)
				  { $statement .= "region_origin IS NULL AND "; }
				else
				  { $statement .= "region_origin='".$query[$i+3]."' AND "; }
				$statement .= "country_origin='".$query[$i+4]."'";
			}
		}	
		//echo $statement;
		$result = mysqli_query($con,$statement);
		/*
		$result = mysqli_query($con,"SELECT * FROM networks WHERE 
			city_cur='{$query[1]}' AND country_cur='{$query[2]}' 
			AND city_origin='{$query[3]}' AND country_origin='{$query[4]}'");
		 */
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result) {
			echo $con->error;
			return false;
		}
		else
			return $result;
	}

	public static function getNetworksByRegions($regions, $con=NULL)
	{
		// craft statement with all the regions 
		// inside
		$stmt_regions='';
		for ($i = 0; $i < count($regions); $i++) {

			// add region
			$stmt_regions .= '\''.$regions[$i].'\'';

			// add comma if not the last
			// thing in the list
			if (count($regions) - $i > 1)
			  { $stmt_regions .= ','; }
			  
		}

		$query = <<<SQL

			SELECT *
			FROM networks
			WHERE region_cur IN ($stmt_regions)	
			LIMIT 0,5

SQL;
		// -----------------------------------------

		$result = QueryHandler::executeQuery($query, $con);
		return $result;

	}

	public static function getNetworksByCities($cities, $con=NULL)
	{
		// craft statement with all the cities
		// inside
		$stmt_cities='';
		for ($i = 0; $i < count($cities); $i++) {

			// add city
			$stmt_cities .= '\''.$cities[$i].'\'';

			// add comma if not the last
			// thing in the list
			if (count($cities) - $i > 1)
			  { $stmt_cities .= ','; }
			  
		}

		$query = <<<SQL

			SELECT *
			FROM networks
			WHERE city_cur IN ($stmt_cities)	
			LIMIT 0,5

SQL;
		echo $query;
		// -----------------------------------------

		$result = QueryHandler::executeQuery($query, $con);
		return $result;

	}

	public static function getNetworksAllClasses($data, $con=NULL)
	{
		switch($data[0])
		{
		case '_l':
			return static::getNetworksByL($data, $con);
		case 'co':
			return static::getNetworksByCO($data, $con);
		case 'rc':
			return static::getNetworksByRC($data, $con);
		case 'cc':
			return static::getNetworksByCC($data, $con);
		}
	}

	public static function networkToQuery($network, $con=NULL)
	{
		$query = array(NULL, NULL, NULL, NULL,
				NULL, NULL, NULL);

		$query[0] = $network->network_class;
		$query[1] = $network->city_cur;
		$query[2] = $network->region_cur;
		$query[3] = $network->country_cur;

		switch($query[0])
		{
		case '_l':
			$query[4] = $network->language_origin;
			break;
		case 'co':
			$query[6] = $network->country_origin;
			break;
		case 'rc':
			$query[5] = $network->region_origin;
			$query[6] = $network->country_origin;
			break;
		case 'cc':
			$query[4] = $network->city_origin;
			$query[5] = $network->region_origin;
			$query[6] = $network->country_origin;
			break;
		}

		return $query;
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
	
	public static function fillNetworkDT($result, $con=NULL)
	{
		//var_dump($result);
		$row = mysqli_fetch_array($result);
		$network_dt = new NetworkDT();

		$network_dt->id = $row['id'];
		$network_dt->city_cur = $row['city_cur'];
		$network_dt->region_cur = $row['region_cur'];
		$network_dt->country_cur = $row['country_cur'];
		$network_dt->city_origin = $row['city_origin'];
		$network_dt->region_origin = $row['region_origin'];
		$network_dt->country_origin = $row['country_origin'];
		$network_dt->language_origin = $row['language_origin'];
		$network_dt->network_class = $row['network_class'];
		$network_dt->member_count = NetworkRegistration::getMemberCount($network_dt->id, $con);
		$network_dt->post_count = Post::getPostCount($network_dt->id, $con);
	//	$network_dt->member_count = $row['member_count'];
	//	$network_dt->post_count = $row['post_count'];
		$network_dt->existing = true;
		
		return $network_dt;
	}

	public static function fillDTWithQuery($query)
	{
		$network_dt = new NetworkDT();
		
		// some particulars
		$network_dt->network_class = $query[0];
		$network_dt->existing = false;

		// location
		$network_dt->city_cur = $query[1]; 
		$network_dt->region_cur = $query[2];
		$network_dt->country_cur = $query[3];

		// origin stuff
		switch ($query[0])
		{
		case "_l":
			$network_dt->language_origin = $query[4];
			break;
		case "co":
			$network_dt->country_origin = $query[6];
			break;
		case "rc":
			$network_dt->region_origin = $query[5];
			$network_dt->country_origin = $query[6];
			break;
		case "cc":
			$network_dt->city_origin = $query[4];
			$network_dt->region_origin = $query[5];
			$network_dt->country_origin = $query[6];
			break;
		}
		//var_dump($network_dt);

		return $network_dt;
	}

	public static function fillNetworkDTs($results)
	{
		$networks = array();
		
		while ($row = mysqli_fetch_array($results))
		{	
			$network_dt = new NetworkDT();

			$network_dt->id = $row['id'];
			$network_dt->city_cur = $row['city_cur'];
			$network_dt->region_cur = $row['region_cur'];
			$network_dt->country_cur = $row['country_cur'];
			$network_dt->city_origin = $row['city_origin'];
			$network_dt->region_origin = $row['region_origin'];
			$network_dt->country_origin = $row['country_origin'];
			$network_dt->language_origin = $row['language_origin'];
			$network_dt->network_class = $row['network_class'];
			$network_dt->member_count = $row['member_count'];
			$network_dt->post_count = $row['post_count'];
			$network_dt->existing = true;

			array_push($networks, $network_dt);
		}
		
		return $networks;
	}
}
?>
