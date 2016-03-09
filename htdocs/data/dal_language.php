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
		$query = <<<SQL
			SELECT id, name 
			FROM languages 
SQL;

		$result = QueryHandler::executeQuery($query, $con);
		
		$languages = array();

		while($row = mysqli_fetch_array($result)) {
			$language = array(
				'id' => $row['id'],
			       	'name' => $row['name']);	

			array_push($languages, $language);
		}

		return $languages;
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
			WHERE name LIKE '{$name}'");
			
		if ($must_close)
			mysqli_close($con);

		if (!$result)
			echo $con->error;

		return $result;
	}
	public static function getLanguageByName($name, $con=null)
	{
		var_dump($name);

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

	public static function getLanguageByNameF($name, $con=null)
	{

		$query = <<<SQL
			SELECT * 
			FROM languages 
			WHERE name='$name'
SQL;

		$result = QueryHandler::executeQuery($query, $con);
		
		while($row = mysqli_fetch_array($result))
			$language = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'tweet_terms' => $row['tweet_terms']);	

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

	////////////////////////////////////////////////////////
	//		UPDATE STATEMENTS
	//	/////////////////////////////
	public static function updateNetworkNames($id, $name, $con=NULL) { 

		// important names
		$norg = language.'_origin'; // eg city_origin
		$iorg = 'id_'.language.'_origin'; // eg id_city_origin

		$query = <<<SQL
			UPDATE networks
				SET $norg = CASE
				WHEN $iorg = $id THEN '$name'
				ELSE $norg
				END
SQL;


		return QueryHandler::executeQuery($query, $con);
	}

	/*
	public static function addSearchKeys($con=NULL) {

		$query = <<<SQL
			INSERT INTO search_keys VALUES ('language')
			 ()
SQL;

		return QueryHandler::executeQuery($query, $con);
	}
	 */


	public static function deleteSearchKeys($id, $con=NULL) {

		$query = <<<SQL
			DELETE FROM search_keys WHERE language_id=$id AND class_searchable='language'
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function deleteSearchKeysByName($original_value, $con=NULL) {

		$identifier_column = $table . '_id';
		$identifier_value = $value;

		$query = <<<SQL
			DELETE FROM search_keys WHERE language_id=$original_value
SQL;

		var_dump($query);
		//return QueryHandler::executeQuery($query, $con);
	}
}
?>
