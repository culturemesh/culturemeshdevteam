<?php

include '/var/www/culturemesh/culturemeshdevteam/htdocs/autoload.php';

class HelloDBClassTest extends PHPUnit_Extensions_Database_TestCase {

	protected static $con;
	
	/**
	* @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	*/
	public function getConnection()
	{
		if (file_exists('../localdbconn.php')) {

			// get things
			include_once ('../localdbconn.php');

			// get connection from yuknowher
			return $this->createDefaultDBConnection(new 
				PDO($pdo_db_string, DB_USER, DB_PASS));
		}

		$con = dal\QueryHandler::getDBConnection();
		mysqli_close($con);

	}

	public function getDataSet()
	{
	//	echo 'Bear';
		return 1;
	}

	public function testSelect() {

	//	$dataSet = $this->getConnection()->createDataSet();
		echo 'testSelect';
	}
}

?>
