<?php

class EventRegistration
{
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllEventRegistrations()
	{
		//$con = getDBConnection();
		
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM event_registration");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_guest'] . " " . $row['id_event'] . " " . $row['job'];
		  	  echo "<br>";
		}
		
		//mysqli_close($con);
	}
}
?>
