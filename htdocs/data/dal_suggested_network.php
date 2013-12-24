<?php
include_once("zz341/fxn.php");

class SuggestedNetwork
{
	public static function getAllSuggestedNetworks()
	{
		//$con = getDBConnection();
		
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM suggested_networks");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['city'] . " " . $row['region'] . " " . $row['language'] . " " . $row['date_suggested'];
		  	  echo "<br>";
		}
		
		//mysqli_close($con);	
	}
}
?>
