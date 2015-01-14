<?php

class RegionTest extends PHPUnit_Framework_TestCase {

	protected $region;

	public function setUp() {

		$this->region = new dobj\Region();
	}

	public function testConstruct() {
		$this->assertInstanceOf('dobj\Region', $this->region);
	}

	public function testToString() {

		$this->region->name = 'Region';
		$this->region->country_name = 'Country';
		$this->assertEquals('Region, Country', $this->region->toString());
	}
}

?>
