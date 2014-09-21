<?php



// fopen, fwrite
// getAll___
// strtolower

class TextData {

	public static $location_class = array('cities', 'regions', 'countries');

	public static $tf_data = array(
		'cities' => array('../data/s_cities.txt', '../data/s_citynames.txt'),
		'regions' => array('../data/s_regions.txt', '../data/s_regionnames.txt'),
		'countries' => array('../data/s_countries.txt', '../data/s_countrynames.txt'),
		'languages' => array('../data/s_languages.txt', '../data/s_langnames.txt')
	);

	public static function rewrite($type) {
		switch ($type) {
		case 'languages':
			self::rewriteLanguages();
			break;
		case 'locations':
			self::rewriteLocations();
			break;
		// have these in case I wanna optimize
		case 'cities': // equal to rewriteLocations
			self::rewriteLocations();
			break;
		case 'regions':
			self::rewriteLocations();
			break;
		case 'countries':
			self::rewriteLocations();
			break;
		}
		
	}

	public static function rewriteLanguages() {
		$languages = Language::getAllLanguages();

		$f0_name = self::$tf_data['languages'][0];
		$f1_name = self::$tf_data['languages'][1];

		$f0 = fopen($f0_name, 'w');
		$f1 = fopen($f1_name, 'w');

		$f0_string = 'name'.PHP_EOL;
		$f1_string = '';

		foreach ($languages as $language) {
			$f0_string .= $language['name'].PHP_EOL;
			$f1_string .= strtolower($language['name']).PHP_EOL;
		}

		fwrite($f0, $f0_string);
		fwrite($f1, $f1_string);

		fclose($f0);
		fclose($f1);
	}

	public static function rewriteLocations() {

		$con = QueryHandler::getDBConnection();
		$cities = Location::getAllCities($con);
		$regions = Location::getAllRegions($con);
		$countries = Location::getAllCountries($con);

		mysqli_close($con);

		foreach (self::$location_class as $l) {

			for ($i = 0; $i < count(self::$tf_data[$l]); $i++) {

				// get file
				$filename = self::$tf_data[$l][$i];
				$file = fopen($filename, 'w');

				$filestring = '';

				switch($i) {
				case 0:		// alldata
					switch ($l) {
					case 'cities':
						if( $i == 0)
						 { $filestring = 'name'."\t".'region_name'."\t".'country_name'.PHP_EOL; }

						foreach ($cities as $city) {
							$filestring .= $city['name']."\t".$city['region_name']."\t".$city['country_name'].PHP_EOL;
						}
						break;
					case 'regions':
						if( $i == 0)
						 { $filestring = 'name'."\t".'country_name'.PHP_EOL; }

						foreach ($regions as $region) {
							$filestring .= $region['name']."\t".$region['country_name'].PHP_EOL;
						}
						break;
					case 'countries':
						if( $i == 0)
						 { $filestring = 'name'.PHP_EOL; }

						foreach($countries as $country) {
							$filestring .= $country['name'].PHP_EOL;
						}
					}

					break;
				case 1:		// name lowercase
					switch ($l) {
					case 'cities':
						foreach ($cities as $city) {
							$filestring .= strtolower($city['name']).PHP_EOL;
						}
						break;
					case 'regions':
						foreach ($regions as $region) {
							$filestring .= strtolower($region['name']).PHP_EOL;
						}
						break;
					case 'countries':
						foreach($countries as $country) {
							$filestring .= strtolower($country['name']).PHP_EOL;
						}
					}
					break;
				}

				// write and close file
				fwrite($file, $filestring);
				fclose($file);
			}
		}
	}
}
?>