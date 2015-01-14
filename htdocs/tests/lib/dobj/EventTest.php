<?php

class EventTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$event = new dobj\Event();
		$this->assertInstanceOf('dobj\Event', $event);
	}
}

?>
