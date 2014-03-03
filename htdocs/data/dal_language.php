<?php
ini_set('display_errors',1);
/**
  * @operations - 
  *	CREATE
  *	READ
  *	    getAllLanguages
  *	UPDATE
  *	DELETE
**/ 

include_once("zz341/fxn.php");

class Language
{
	public static function getAllLanguages($con=NULL)
	{
		$must_close = false;

		if ($con == NULL)
		{ $con = getDBConnection();
		  $must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }

		$result = mysqli_query($con,"SELECT * FROM languages");
			
		if ($must_close)
			mysqli_close($con);
		
		return $result;
	}
}
?>
