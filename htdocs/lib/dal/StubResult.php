<?php
namespace dal;

class StubResult {

	protected $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function fetch_assoc() {
		return array(
			'id' => $this->id
		);
	}

	public function arrayify() {
		return get_object_vars($this);
	}

	public function __get($name) {

		if (!isset($this->$name))
			throw new \InvalidArgumentException("This object doesn\'t have index : {$name}");

		return $this->$name;
	}
}
