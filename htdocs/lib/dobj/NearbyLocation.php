<?php
namespace dobj;

class NearbyLocation extends DObj {

	protected $neighbor_id;
	protected $neighbor_name;
	protected $dist_level;
	protected $distance;

	public function toSearchable($reference=NULL) {

		$class = get_called_class();

		if ($class == 'dobj\NearbyCity')
			$dobj = new City();
		else if ($class == 'dobj\NearbyRegion')
			$dobj = new Region();
		else if ($class == 'dobj\NearbyCountry')
			$dobj = new Country();

		$dobj->id = $this->neighbor_id;
		$dobj->name = $this->neighbor_name;

		if ($reference !== NULL) {

			if (in_array($class, array('dobj\NearbyCity', 'dobj\NearbyRegion'))) {
				$dobj->country_id = $reference->country_id;
				$dobj->country_name = $reference->country_name;
			}

			if ($class == 'dobj\NearbyCity') {
				$dobj->region_id = $reference->region_id;
				$dobj->region_name = $reference->region_name;
			}
		}

		return $dobj;
	}

	public function isNeighborTo($reference) {

		$class = get_called_class();

		if ($class == 'dobj\NearbyCity') {
			return $reference->id == $this->city_id;
		}
		else if ($class == 'dobj\NearbyRegion') {
			return $reference->id == $this->region_id;
		}
		else if ($class == 'dobj\NearbyCountry') {
			return $reference->id == $this->country_id;
		}
	}
}

?>
