<?php
ini_set("display_errors", 1);
include_once("zz341/fxn.php");
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

			SELECT * FROM nearby_cities 
			WHERE city_name='$name'
			ORDER BY distance
SQL;

//////////////////////////////////////////////////////////////////
		$result = QueryHandler::executeQuery($query, $con);
		return $result;
	}

	public static function getNearbyRegions($name, $con=NULL)
	{
		// query
		$query = <<<SQL

			SELECT * FROM nearby_regions
			WHERE region_name='$name'
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
}
?>
