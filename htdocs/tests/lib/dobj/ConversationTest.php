<?php

class ConversationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Conversation::__construct
	 *
	 */
	protected $dal;
	protected $do2db;

	public function setUp() {

		$dal = new dal\DAL(new dal\StubConnection());
		$do2db = new dal\Do2Db();
	}

	public function testConstruct() {

		$convo = new dobj\Conversation();
		$this->assertInstanceOf('dobj\Conversation', $convo);
	}

	public function testDisplayNetwork() {

		$this->markTestSkipped('Not displaying network in conversation yet');

		$convo = dobj\Conversation::createFromId($id);
		$convo->fillConversation();
		$convo->display('network');
	}

	public function testDisplayProfile() {

		$this->markTestSkipped('Can\'t display profile yet');

		$convo = dobj\Conversation::createFromId($id);
		$convo->fillConversation();
		$convo->display('profile');
	}
}
