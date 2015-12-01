<?php
namespace dobj;

class NearbyLocation extends DObj {

	protected $neighbor_id;
	protected $neighbor_name;
	protected $dist_level;
	protected $distance;

	public function toSearchable() {

		$class = get_called_class();
		$dobj = new $class();

		$dobj->id = $this->neighbor_id;
		$dobj->name = $this->neighbor_name;

		return $dobj;
	}
}

?>
