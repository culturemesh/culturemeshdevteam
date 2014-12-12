<?php

class UtilTest extends PHPUnit_Framework_TestCase {

	public function testHasStringKey() {

		// you know
		$empty_array = array();

		// numeric key
		$strnum_key = array(
			'0' => 'value');

		// no key
		$nokeys = array('Pacific Overtures', 'Sunday in the Park with George');

		// str key
		$str_key = array(
			'key' => 'value');

		// str key
		$mixed_key = array(
			'0' => 'value',
			'key' => 'value');

		$this->assertFalse(misc\Util::hasStringKey($empty_array));
		$this->assertFalse(misc\Util::hasStringKey($strnum_key));
		$this->assertFalse(misc\Util::hasStringKey($nokeys));
		$this->assertTrue(misc\Util::hasStringKey($str_key));
		$this->assertTrue(misc\Util::hasStringKey($mixed_key));
	}

	public function testGetController() {

		$string = 'controller#action';
		$split = misc\Util::getController($string);
		$this->assertEquals(array('controller' => 'controller', 
			'action' => 'action'), $split);
	}

	public function testArrayToSearchableCity1() {
		
		$arr = array(
			'id_city_origin' => 1,
			'city_origin' => 'City',
			'id_region_origin' => 1,
			'region_origin' => 'Region',
			'id_country_origin' => 1,
			'country_origin' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\City', $srch);
	}

	public function testArrayToSearchableCity2() {

		$arr = array(
			'id_city_cur' => 1,
			'city_cur' => 'City',
			'id_region_cur' => 1,
			'region_cur' => 'Region',
			'id_country_cur' => 1,
			'country_cur' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\City', $srch);
	}

	public function testArrayToSearchableCity3() {

		$arr = array(
			'id_city_origin' => 1,
			'city_origin' => 'City',
			'id_region_origin' => NULL,
			'region_origin' => NULL,
			'id_country_origin' => 1,
			'country_origin' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\City', $srch);
	}

	public function testArrayToSearchableCity4() {

		$arr = array(
			'id_city_cur' => 1,
			'city_cur' => 'City',
			'id_region_cur' => NULL,
			'region_cur' => NULL,
			'id_country_cur' => 1,
			'country_cur' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\City', $srch);
	}

	public function testArrayToSearchableRegion1() {

		$arr = array(
			'id_city_origin' => NULL,
			'city_origin' => NULL,
			'id_region_origin' => 1,
			'region_origin' => 'Region',
			'id_country_origin' => 1,
			'country_origin' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\Region', $srch);
	}

	public function testArrayToSearchableRegion2() {

		$arr = array(
			'id_city_cur' => NULL,
			'city_cur' => NULL,
			'id_region_cur' => 1,
			'region_cur' => 'Region',
			'id_country_cur' => 1,
			'country_cur' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\Region', $srch);
	}

	public function testArrayToSearchableCountry1() {

		$arr = array(
			'id_city_origin' => NULL,
			'city_origin' => NULL,
			'id_region_origin' => NULL,
			'region_origin' => NULL,
			'id_country_origin' => 1,
			'country_origin' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\Country', $srch);
	}

	public function testArrayToSearchableCountry2() {

		$arr = array(
			'id_city_cur' => NULL,
			'city_cur' => NULL,
			'id_region_cur' => NULL,
			'region_cur' => NULL,
			'id_country_cur' => 1,
			'country_cur' => 'Country');

		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\Country', $srch);
	}

	public function testArrayToSearchableLanguage() {

		$arr = array('id_language_origin' => 1,
			'language_origin' => 'Language');
		$srch = misc\Util::ArrayToSearchable($arr);
		$this->assertInstanceOf('dobj\Language', $srch);
	}
}

?>
