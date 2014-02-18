<?php
ini_set('display_errors',1);
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
			(city_cur, region_cur, city_origin, region_origin, country_origin, language_origin, network_class, date_added, img_link)
			VALUES ('". $network_dt->city_cur. "', '". $network_dt->region_cur. "', '". $network_dt->city_origin. "', '". $network_dt->region_origin .
			"', '". $network_dt->city_origin. "', '". $network_dt->country_origin. "', '". $network_dt->language_origin . "', NOW() , '" . $network_dt->img_link  . "')"))
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
			"SELECT n.id, n.city_cur, n.region_cur, n.city_origin, n.region_origin, n.country_origin, n.language_origin, n.network_class, nr.member_count, p.post_count
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
			SET city_cur='". $network_dt->city_cur ."', region_cur='". $network_dt->region_cur .
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
			$network_dt->region_cur = $row['region_cur'];
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
