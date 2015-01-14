<?php

class ReplyTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$reply = new dobj\Reply();
		$this->assertInstanceOf('dobj\Reply', $reply);
	}
}

?>
