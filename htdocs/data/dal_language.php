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

if (file_exists('zz341/fxn.php'))
	include_once("zz341/fxn.php");
if (file_exists('dal_language-dt.php'))
	include_once("dal_language-dt.php");

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

		if (!$result)
			echo $con->error;

		return $result;
	}

	public static function getLanguage($name, $con=NULL)
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

		$result = mysqli_query($con,"SELECT name FROM languages 
			WHERE name='{$name}'");
			
		if ($must_close)
			mysqli_close($con);

		if (!$result)
			echo $con->error;

		return $result;
	}
	public static function getLanguageByName($name, $con=null)
	{

		$query = <<<SQL
			SELECT id, name 
			FROM languages 
			WHERE name='$name'
SQL;

		$result = QueryHandler::executeQuery($query, $con);
		
		while($row = mysqli_fetch_array($result))
			$language = array($row['id'],
			       		$row['name']);	

		return $language;
	}

	////////////////////////////////////////////////////////
	//		INSERT STATEMENTS
	//	/////////////////////////////

	public static function insertLanguage($language, $con=NULL)
	{
		$query = <<<SQL
			INSERT INTO languages
			(name, num_speakers, added)
			VALUES
			('$language->name', $language->num_speakers, 1)
SQL;
///// ------->
		return QueryHandler::executeQuery($query, $con);
	}
}
?>
