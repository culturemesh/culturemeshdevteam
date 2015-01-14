<?php

class CountryTest extends PHPUnit_Framework_TestCase {

	protected $country;

	public function setUp() {

		$this->country = new dobj\Country();
	}

	public function testConstruct() {
		$this->assertInstanceOf('dobj\Country', $this->country);
	}

	public function testToString() {

		$this->country->name = 'Country';
		$this->assertEquals('Country', $this->country->toString());
	}
}

?>
