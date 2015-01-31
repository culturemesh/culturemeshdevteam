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

	public static function getDBConnection() {


		if ($_SERVER['HTTP_HOST'] == 'www.culturemesh.com') {
			if (strpos($_SERVER['REQUEST_URI'], 'culturemeshdevteam') !== false) 
			  return new mysqli('localhost', 'culturp7', 'IanTheMan2014!', 'culturp7_rehearsal');
			else
			  return new mysqli('localhost', 'culturp7', 'IanTheMan2014!', 'culturp7_ktc');
		}
		else {
			return new mysqli('www.culturemesh.com', 'culturp7', 'IanTheMan2014!', 'culturp7_rehearsal');
		}
	}

	// return an array of assoc_arrays
	// , the data table, basically
	public static function getRows($result)
	{	
		if (!is_string($result)) {
			$data = array();
			while($row = $result->fetch_assoc())
			   { $data[] = $row; }
			return $data;
		}
		else
		   { return -1; }
	}
	//
	// return an array of assoc_arrays
	// , the data table, basically
	public static function getRows2($result)
	{	
		if (!is_string($result)) {
			$data = array();
			while($row = $result->fetch_assoc())
			   { $data = array_push($data, $row); }
			return $data;
		}
		else
		   { return -1; }
	}
	public static function getColumn($result, $column)
	{
		if (!is_string($result)) {
			$data = array();
			while($row = $result->fetch_assoc())
			   { array_push($data, $row[$column]); }
			return $data;
		}
		else
		   { return -1; }
	}

	public static function getColumnFromDS($ds, $col_name)
	{
		$col = array();
		foreach ($ds as $row)
		{
			array_push($col, $row[$col_name]);
		}
		return $col;
	}
}
?>
