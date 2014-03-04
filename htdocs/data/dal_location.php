<?php
ini_set("display_errors", 1);
include_once("zz341/fxn.php");

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
}
?>
