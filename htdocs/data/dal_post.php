<?php
include_once("zz341/fxn.php");

class Post
{
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllPosts()
	{
		
		//$con = getDBConnection();
		$con = func_get_arg(0);
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM posts");
		
		while($row = mysqli_fetch_array($result))
		{
		  	  echo $row['post_date'] . " " . $row['post_text'];
		  	  echo "<br>";
		}
		
		//mysqli_close($con);
	}
}
?>
