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
}
