<?php
namespace dobj;

class Location extends Searchable {

	protected $name_values;
	protected $latitude;
	protected $longitude;
	protected $population;
	protected $feature_code;

	public function toString() {

		$str = '';

		for($i = 0; $i < count($this->name_values); $i++) {

			// get property name
			$val = $this->name_values[$i];

			// ignore empty stuff
			if ($this->$val != NULL) {
				$str .= $this->$val;

				// add comma if not last
				if (count($this->name_values) - $i > 1)
					$str .= ', ';
			}
		}

		return $str;
	}
}

?>
