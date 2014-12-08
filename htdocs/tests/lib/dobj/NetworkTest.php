<?php

class NetworkTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

	}

	public function tearDown() {

	}

	public function testConstruct() {

		$network = new dobj\Network();
		$this->assertInstanceOf('dobj\Network', $network);
	}
}

?>
