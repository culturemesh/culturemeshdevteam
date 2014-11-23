<?php
namespace dobj;

abstract class DObj {

	protected $id;

	public function __construct() {

	}

	public static function createFromId($id, $dal, $do2db) {

	}

	public static function createFromDataRow($row) {

		$keys = array_keys($row);
		$class = get_called_class();
		$dobj = new $class();

		foreach ($keys as $key) {
			$dobj->$key = $row[$key];
		}

		return $dobj;
	}

	/*
	 * JS style accessors and mutators
	 * 	- just cause I like 'em
	 */
	public function id($value=NULL) {
		
		if ($value == NULL)
			return $this->id;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->id = $value;

		return true;
	}

	public function fill($result) {

	}


	public function __get($name) {

		return $this->$name;
	}

	public function &getReference($name) {
		return $this->$name;
	}

	public function __set($name, $arg) {

		if (method_exists($this, $name)) {
			$this->$name($arg);	
		}
		else{
			$this->$name = $arg;
		}
	}
}

?>
