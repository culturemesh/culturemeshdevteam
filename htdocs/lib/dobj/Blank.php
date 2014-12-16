<?php
namespace dobj;

class Blank extends DObj {

	public static function createFromId($id, $dal, $do2db) {
		$b = new Blank();
		$b->id = $id;

		return $b;
	}
}
