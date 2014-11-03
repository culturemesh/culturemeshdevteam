<?php

class ConversationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Conversation::__construct
	 *
	 */
	public function testConstruct() {

		$convo = new dobj\Conversation();
		$this->assertInstanceOf('dobj\Conversation', $convo);
	}
}
