<?php

class Locations
{
	public static function getCCByName($city, $country, $con=null)
	{
		$must_close = false;
		if($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT id, name, country_id, country_name
			FROM cities
		       	WHERE name='{$city}' AND country_name='{$country}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result)
			echo $con->error;
		else
		{
			$city = null;

			while($row = mysqli_fetch_array($result))
				$city =	array($row['id'], $row['name'], $row['country_id'], $row['country_name']);	

			return $city;
		}

	}

	public static function getRCByName($region, $country, $con=null)
	{
		$must_close = false;
		if($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT id, name, country_id, country_name
			FROM regions
		       	WHERE name='{$region}' AND country_name='{$country}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result)
			echo $con->error;
		else
		{
			$region = null;

			while($row = mysqli_fetch_array($result))
				$region = array($row['id'], $row['name'], $row['country_id'], $row['country_name']);	

			return $region;
		}
	}

	public static function getCOByName($country, $con=null)
	{
		$must_close = false;
		if($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT id, name
			FROM countries
		       	WHERE name='{$country}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result)
			echo $con->error;
		else
		{
			$country = null;

			while($row = mysqli_fetch_array($result))
				$country = array($row['id'], $row['name']);	

			return $country;
		}
	}
}
?>
