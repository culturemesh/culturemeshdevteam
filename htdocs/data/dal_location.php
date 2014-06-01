<?php
ini_set("display_errors", 1);

if (file_exists('zz341/fxn.php'))
	include_once("zz341/fxn.php");
if (file_exists('dal_query_handler.php'))
	include_once("dal_query_handler.php");

class Location
{

	public static function getAllCities($con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT * FROM cities");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}
	
	public static function getAllRegions($con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT * FROM regions");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getAllCountries($con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT * FROM countries");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getCity($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM cities
				WHERE name='{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getRegion($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM regions
				WHERE name='{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getCountry($name, $con = null)
	{
		$must_close = false;

		if ($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$result = mysqli_query($con, "SELECT id, name FROM countries 
				WHERE name='{$name}'");

		if ($must_close)
			mysqli_close($con);

		if (!$result)
			return $con->error;

		else
			return $result;
	}

	public static function getNearbyCities($name, $con=NULL) 
	{
//////////////////////////////////////////////////////////////////
		
		$query = <<<SQL

			SELECT * FROM nearby_cities nc, cities c
			WHERE nc.neighbor_name=c.name
			AND nc.neighbor_id=c.id
			AND nc.city_name='$name'
			ORDER BY nc.distance
SQL;

//////////////////////////////////////////////////////////////////
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getNearbyRegions($name, $con=NULL)
	{
		// query
		$query = <<<SQL

			SELECT * FROM nearby_regions nr, regions r
			WHERE nr.neighbor_name=r.name
			AND nr.neighbor_id=r.id
			AND nr.region_name='$name'
			ORDER BY distance

SQL;
		$result = QueryHandler::executeQuery($query, $con);
		return $result;	
	}

	public static function getRegionsByCountry($name, $con=NULL)
	{
		// query
		$query = <<<SQL

			SELECT *
			FROM regions
			WHERE country_name = '$name'
			LIMIT 0,5
SQL;
	//-----------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;	
	}

	public static function getCitiesByCountry($name, $con=NULL)
	{
		$query = <<<SQL

			SELECT *
			FROM cities
			WHERE country_name = '$name'
			ORDER BY population DESC
			LIMIT 0,5
SQL;
	//------------------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getCitiesByRegion($name, $con=NULL)
	{
		$query = <<<SQL

			SELECT *
			FROM cities
			WHERE region_name = '$name'
			LIMIT 0,5
SQL;
	//-------------------------------------------------
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getCCByName($name, $region_name, $country_name, $con=NULL)
	{

		$query = <<<SQL
			SELECT id, name, region_id, region_name, country_id, country_name
			FROM cities
			WHERE name='$name'
			AND region_name='$region_name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$city = array($row['id'], $row['name'], 
			$row['region_id'], $row['region_name'],
			$row['country_id'], $row['country_name']);

		return $city;
	}

	public static function getRCByName($name, $country_name, $con=NULL)
	{
		$query = <<<SQL
			SELECT id, name, country_id, country_name
			FROM regions 
			WHERE name='$name'
			AND country_name='$country_name'
SQL;
///////////////
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$region = array(NULL, NULL, $row['id'], $row['name'], 
			$row['country_id'], $row['country_name']);

		return $region;
	}

	public static function getCOByName($name, $con=NULL)
	{
		$query = <<<SQL
			SELECT id, name
			FROM countries 
			WHERE name='$name'
SQL;
//------------>
		$result = QueryHandler::executeQuery($query, $con);

		// fill in results
		//  this should be done in a function, but laziness
		$row = mysqli_fetch_array($result);
		// return first city
		$country = array(NULL, NULL, NULL, NULL, $row['id'], $row['name']);

		return $country;
	}

	///////////////////////////////////////////////////////
	// 		INSERTS
	// 	//////////////////////

	public static function insertCity($city, $con=NULL) {
		$query = <<<SQL
			INSERT INTO cities
			(name, latitude, longitude,
			region_id, region_name, country_id,
			country_name, population, added)
			VALUES
			('$city->city_name',
				$city->latitude, $city->longitude,
				$city->region_id, '$city->region_name',
				$city->country_id, '$city->country_name',
				$city->population, 1)
SQL;
//////////////	
		return QueryHandler::executeQuery($query, $con);
	}

}
?>
