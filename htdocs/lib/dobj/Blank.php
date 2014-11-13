<?php
namespace dobj;

class Blank extends DObj {

	public static function createFromId($id) {
		$b = new Blank();
		$b->id = $id;

		return $b;
	}
}
