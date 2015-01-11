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

	// parse name
	public function getName() {

		if (!property_exists($this, 'first_name'))
			throw new \Exception('This class does not have a first_name property');
		if (!property_exists($this, 'last_name'))
			throw new \Exception('This class does not have a first_name property');

		$name = NULL;
		if ($this->first_name == '')
			$name = "UNNAMED USER";
		else {
			$name = $this->first_name;
			if (isset($this->last_name))
				$name .= " ".$this->last_name;
		}

		return $name;
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
