<?php

class Languages
{
	public static function getLanguageByName($name, $con=null)
	{
		$must_close = false;
		if($con == null)
		{
			$con = getDBConnection();
			$must_close = true;
		}

		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		
		$result = mysqli_query($con,"SELECT id, name FROM languages WHERE 
			name='{$name}'");
		
		if ($must_close)
			mysqli_close($con);
		
		if (!$result)
			echo $con->error;

		else
		{
			$language = null;

			while($row = mysqli_fetch_array($result))
				$language = array($row['id'], $row['name']);	

			return $language;
		}
	}
}
?>
