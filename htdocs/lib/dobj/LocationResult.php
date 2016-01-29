<?php
namespace dobj;

/*
 * The class is designed to be returned from the database
 * and be funneled into the proper location variable
 */
class LocationResult extends Location {

	public static function createFromDataRow($row, $remora=NULL) {

		$location = NULL;

		if ($row['class_searchable'] == 'city') {

			$location = new \dobj\City();
			$location->region_id = $row['region_id'];
			$location->region_name = $row['region_name'];
			$location->country_id = $row['country_id'];
			$location->country_name = $row['country_name'];
		}
		if ($row['class_searchable'] == 'region') {

			$location = new \dobj\Region();
			$location->country_id = $row['country_id'];
			$location->country_name = $row['country_name'];
		}
		if ($row['class_searchable'] == 'country') {

			$location = new \dobj\Country();
		}

		$location->id = $row['id'];
		$location->name = $row['name'];
		$location->population = $row['population'];
		$location->feature_code = $row['feature_code'];

		// do the remora
		if ($remora != NULL) {

			if (is_array($remora)) {

				// execute on a loop
			}

			else {
				$remora->execute($location);
			}
		}

		return $location;
	}
}
