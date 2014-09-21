<?php

include_once 'dal_query_handler.php';

class Meta {

	public static function getTables($con=NULL) {

		$query = <<<SQL
			SHOW TABLES in culturp7_ktc
SQL;

		// execute
		$result =  QueryHandler::executeQuery($query, $con);

		return QueryHandler::getColumn($result, 'Tables_in_culturp7_ktc');
	}

	public static function describeTable($table, $con=NULL) {
		
		$query = <<<SQL
			SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE
			  FROM INFORMATION_SCHEMA.COLUMNS
			  WHERE table_name = '$table'
			  AND table_schema = 'culturp7_ktc'
SQL;

		// execute
		$result = QueryHandler::executeQuery($query, $con);

		$fk_tables = array(
			'region_id' => 'regions',
			'country_id' => 'countries'
		);

		$fk_cols = array_keys($fk_tables);


		$desc = array();

		while ($row = $result->fetch_assoc()) {
			$thing = array(
				'COLUMN_NAME' => $row['COLUMN_NAME'],
				'DATA_TYPE' => $row['DATA_TYPE'],
				'IS_NULLABLE' => $row['IS_NULLABLE'],
				'COLUMN_DEFAULT' => $row['COLUMN_DEFAULT'],
				'CHARACTER_MAXIMUM_LENGTH' => $row['CHARACTER_MAXIMUM_LENGTH'],
				'NUMERIC_PRECISION' => $row['NUMERIC_PRECISION'],
				'NUMERIC_SCALE' => $row['NUMERIC_SCALE'],
				'FK' => NULL
			);

			if (in_array($thing['COLUMN_NAME'], $fk_cols)) {
				$thing['FK'] = array(
					$thing['COLUMN_NAME'] => $fk_tables[$thing['COLUMN_NAME']]
				);
			}

			array_push($desc, $thing);
		}
		return $desc;
	}

	public static function createJunkCopy($og_table, $con=NULL) {

		// should probably be three separate functions
		$suffix = rand();
		$jt_table = $og_table.'_'.$suffix;

		$query = <<<SQL
			CREATE TABLE $jt_table LIKE $og_table
SQL;
		QueryHandler::executeQuery($query, $con);


		/// SET AUTO INCREMENT TO MATCH INSERT
		// get auto inc
		$auto_inc = self::getAutoIncrement($og_table, $con);

		$query = <<<SQL
			ALTER TABLE $jt_table AUTO_INCREMENT=$auto_inc
SQL;

		QueryHandler::executeQuery($query, $con);

		return $jt_table;
	}

	public static function getAutoIncrement($table, $con=NULL) {
		$db_name = DB_NAME;

		$query = <<<SQL
		SELECT `AUTO_INCREMENT`
		FROM  INFORMATION_SCHEMA.TABLES
		WHERE TABLE_SCHEMA = '$db_name'
		AND TABLE_NAME   = '$table';
SQL;

		$result = QueryHandler::executeQuery($query, $con);

		$row = mysqli_fetch_assoc($result);

		return $row['AUTO_INCREMENT'];
	}

	public static function deleteJunkCopy($table, $con=NULL) {

		$query = <<<SQL
			DROP TABLE $table
SQL;

		QueryHandler::executeQuery($query, $con);

	}

	public static function getJunkDuplicate($table, $con=NULL) {

		$query = <<<SQL
		SELECT * FROM $table
		LIMIT 0,1
SQL;

		return QueryHandler::executeQuery($query, $con);
	}
}
?>
