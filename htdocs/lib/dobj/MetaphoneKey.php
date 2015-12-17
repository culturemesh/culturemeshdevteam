<?php
namespace dobj;

class MetaphoneKey extends DObj{

	private $meta_key;
	private $city_id;
	private $city_name;
	private $region_id;
	private $region_name;
	private $country_id;
	private $country_name;
	private $language_id;
	private $language_name;
	private $class_searchable;

	public static function createFromDataRow($row) {

		/*
		$keys = array_keys($row);
		$class = get_called_class();
		$dobj = new $class();

		foreach ($keys as $key) {
			$dobj->$key = $row[$key];
		}
		 */

		$searchable = NULL;

		if ($row['class_searchable'] == 'city') {

			$searchable = new \dobj\City();
			$searchable->id = $row['city_id'];
			$searchable->name = $row['city_name'];
			$searchable->region_id = $row['region_id'];
			$searchable->region_name = $row['region_name'];
			$searchable->country_id = $row['country_id'];
			$searchable->country_name = $row['country_name'];
		}

		if ($row['class_searchable'] == 'region') {

			$searchable = new \dobj\Region();
			$searchable->id = $row['region_id'];
			$searchable->name = $row['region_name'];
			$searchable->country_id = $row['country_id'];
			$searchable->country_name = $row['country_name'];
		}

		if ($row['class_searchable'] == 'country') {

			$searchable = new \dobj\Country();
			$searchable->id = $row['country_id'];
			$searchable->name = $row['country_name'];
		}

		if ($row['class_searchable'] == 'language') {

			$searchable = new \dobj\Language();
			$searchable->id = $row['language_id'];
			$searchable->name = $row['language_name'];
		}

		// do the remora
		if ($remora != NULL) {

			if (is_array($remora)) {

				// execute on a loop
			}

			else {
				$remora->execute($searchable);
			}
		}

		return $searchable;
	}

	public function toSearchable() {

		$searchable = NULL;

		if ($this->class_searchable == 'city') {

			$searchable = new City();
			$searchable->id = $this->city_id;
			$searchable->name = $this->city_name;
			$searchable->region_id = $this->region_id;
			$searchable->region_name = $this->region_name;
			$searchable->country_id = $this->country_id;
			$searchable->country_name = $this->country_name;
		}

		if ($this->class_searchable == 'region') {

			$searchable = new Region();
			$searchable->id = $this->region_id;
			$searchable->name = $this->region_name;
			$searchable->country_id = $this->country_id;
			$searchable->country_name = $this->country_name;
		}

		if ($this->class_searchable == 'country') {

			$searchable = new Country();
			$searchable->id = $this->country_id;
			$searchable->name = $this->country_name;
		}

		if ($this->class_searchable == 'language') {

			$searchable = new Language();
			$searchable->id = $this->language_id;
			$searchable->name = $this->language_name;
		}

		return $searchable;
	}
}

?>
