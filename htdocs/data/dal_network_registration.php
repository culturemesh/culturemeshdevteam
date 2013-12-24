<?php
include_once("zz341/fxn.php");

class NetworkRegistration
{
		////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllNetRegistrations()
	{
		//$con = getDBConnection();
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM network_registration");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['id_user'] . " " . $row['id_network'] . " " . $row['join_date'];
		  	  echo "<br>";
		}
		
		//mysqli_close($con);
	}
}
?>
