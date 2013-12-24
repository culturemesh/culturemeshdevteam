<?php
include("zz341/fxn.php");

/**
  * @operations - 
  * 	CREATE
  *         createNetwork
  *	READ
  *	    getAllNetworks
  *	UPDATE
  *	    updateNetwork
  *	DELETE
  *         deleteNetwork
**/ 
//define("DB_SERVER", "69.195.79.120");	// for local development
//define("DB_USER", "culturp7");
//define("DB_PASS", "GoRoop2013!");
//define("DB_NAME", "culturp7_ktc");
 
class Network
{
	public static function getAllNetworks()
	{
		//$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
		//$con = getDBConnection();
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT * FROM networks");
		
		while($row = mysqli_fetch_array($result))
		  {
		  echo $row['city'] . " " . $row['date_added'];
		  echo "<br>";
		  }
		
		//mysqli_close($con);
	}
}
?>
