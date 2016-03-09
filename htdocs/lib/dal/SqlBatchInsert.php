<?php
namespace dal;

class SqlBatchInsert {

	private $members;

	public function __construct($table_name, $column_list) {

		if (!is_array($column_list)) {
			throw new \Exception('SqlBatchInsert: Column list must be an array');
		}

		$this->members = array();
		$this->members['table_name'] = $table_name;
		$this->members['column_list'] = $column_list;
	}

	public function __set($key, $value) {
		$this->members[$key] = $value;
	}

	public function __get($key) {
		return $this->members[$key];
	}

	public function getQuery($data) {

		// EXCEPTIONS
		if (!is_array($data)) {
			throw new \Exception('SqlBatchInsert->getQuery: Data must be in associative array form');
		}

		// Create the opening
		$opening = "INSERT INTO `" . $this->table_name . "` (`" . implode('`, `', $this->column_list) . "`) VALUES \n";

		// Write all the query lines
		//
		$query_lines = array();

		for($i=0; $i<count($data); $i++) {
			
			$datum = $data[$i];

			$line_data = array();

			// Add data to the line
			for($j=0; $j<count($this->column_list); $j++) {

				if (isset($datum[ $this->column_list[$j] ])) {
				  $item = $datum[ $this->column_list[$j] ];
				}
				else {
				  $item = 'NULL';
				}

				array_push($line_data, $item);
			}

			$line_string = '(' . implode(', ', $line_data) . ')';
			array_push($query_lines, $line_string);
		}

		return $opening . implode(",\n", $query_lines);
	}
}

?>
