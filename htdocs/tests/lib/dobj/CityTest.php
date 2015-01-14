<?php

class CityTest extends PHPUnit_Framework_TestCase {

	protected $city;

	public function setUp() {

		$this->city = new dobj\City();
	}

	public function testConstruct() {
		$this->assertInstanceOf('dobj\City', $this->city);
	}

	public function testToString1() {

		$this->city->name = 'City';
		$this->city->region_name = 'Region';
		$this->city->country_name = 'Country';

		$this->assertEquals('City, Region, Country', $this->city->toString());
	}

	public function testToString2() {

		$this->city->name = 'City';
		$this->city->country_name = 'Country';

		$this->assertEquals('City, Country', $this->city->toString());
	}
}

?>
