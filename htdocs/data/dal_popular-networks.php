<?php

include_once("zz341/fxn.php");

include_once("");
class PopularNetwork
{
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
			"SELECT n.id, n.city, n.region, n.country, n.language, 
				COUNT(nr.id_network) AS member_count, 
				COUNT(p.id_network) AS post_count 
			FROM networks n 
			INNER JOIN network_registration nr 
			ON n.id = nr.id_network 
			LEFT JOIN posts p 
			ON n.id = p.id_network AND nr.id_network = p.id_network 
			GROUP BY nr.id_network 
			ORDER BY member_count 
			LIMIT 0,4");
		
		$networks = array();
			
		// populate array with network objects
		while ($row = mysqli_fetch_array($result))
		{
			$network_dt = new NetworkDT();
			
			$network_dt->city = $row['member_count'];
			$network_dt->region = $row['member_count'];
			$network_dt->country = $row['member_count'];
			$network_dt->language = $row['member_count'];
			$network_dt->member_count = $row['member_count'];
			$network_dt->post_count = $row['post_count'];
			
			array_push($networks, $network_dt);
		}
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
	}
}
?>
