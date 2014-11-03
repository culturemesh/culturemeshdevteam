<?php
namespace dobj;

abstract class DObj {

	protected $id;

	public function __construct() {

	}

	public abstract static function createFromId($id);
	public abstract static function createFromDataRow($row);

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

	public function __set($name, $arg) {

		if (method_exists($this, $name)) {
			$this->$name($arg);	
		}

		$this->$name = $arg;
	}
}

?>
