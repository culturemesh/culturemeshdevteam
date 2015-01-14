<?php

class StubConnectionTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$sc = new dal\StubConnection();

		$this->assertInstanceOf('dal\StubConnection', $sc);
	}

	public function testPrepare() {

		$sc = new dal\StubConnection();
		$result = $sc->prepare('nothing query');

		$this->assertInstanceOf('dal\StubStatement', $result);
	}
}
