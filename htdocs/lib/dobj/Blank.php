<?php
namespace dobj;

class Blank extends DObj {

	public static function createFromId($id, $dal, $do2db) {
		$b = new Blank();
		$b->id = $id;

		return $b;
	}

	public function standOut() {
		$ME = '';
		$arr = get_object_vars($this);
		$keys = array_keys($arr);

		for($i = 0; $i < count($keys); $i++) {

			// put value in the thing
			$ME .= $this->$keys[$i];

			// add a space to front and middle items 
			if (count($keys) - $i > 1) {
				$ME .= ' ';
			}
		}

		return $ME;
	}

	public function id($value=NULL) {

		$this->id = $value;
		return true;
	}
}
