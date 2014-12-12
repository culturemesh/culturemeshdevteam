<?php
namespace dobj;

class Language extends Searchable {

	protected $name;

	public function toString() {
		return $this->name;
	}
}

?>
