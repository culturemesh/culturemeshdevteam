<?php
namespace dobj;

class Blank extends DObj {

	public static function createFromId($id) {
		$b = new Blank();
		$b->id = $id;

		return $b;
	}

	public static function createFromDataRow($row) {

		$b = new Blank();
		$attrs = array_keys($row);

		foreach ($attrs as $attr) {
			$b->$attr = $row[$attr];
		}

		return $b;
	}
}
