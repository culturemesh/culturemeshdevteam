<?php

class LocationTest extends PHPUnit_Framework_TestCase {

	protected $loc;

	public function setUp() {

		$this->loc = new dobj\Location();
	}

	public function testConstruct() {
		
		$this->assertInstanceOf('dobj\Location', $this->loc);
	}

	public function testToString() {

		$this->assertEquals(null, $this->loc->toString());
	}
}

?>
