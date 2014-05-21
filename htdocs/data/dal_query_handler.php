<?php

class QueryHandler
{
	// executes prewritten query,
	// returns unmolested results
	public static function executeQuery($query, $con=NULL)
	{
		// Decide if new connection
		// must be created,
		// if so, create it, but remember
		// to close it at the end
		$must_close = false;

		if ($con == NULL) {
			$con = static::getDBConnection();
			$must_close = true;
		}

		// execute query
		$result = mysqli_query($con, $query);

		// if query fails, get reason why
		if (!$result)
		  { $result = $con->error; }

		// close connection
		if ($must_close)
		  { mysqli_close($con); }

		return $result;
	}

	// return a db connection
	private static function getDBConnection() {
		return new mysqli(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	}
}
?>
