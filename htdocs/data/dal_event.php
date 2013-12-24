<?php
include_once("zz341/fxn.php");

class Event
{
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllEvents()
	{
		// if db connection was passed in to function, get it
		// if not, get dbconnection yourself
		if (func_num_args() == 1)
		{ $con = func_get_arg(0); }
		else
		{ $con = getDBConnection();}
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM events");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_network'] . " " . $row['id_host'] . " " . $row['description'];
		  	  echo "<br>";
		}
		
		// close the connection if opened within function
		if (func_num_args() < 1)
		{ mysqli_close($con); }
	}
}
?>
