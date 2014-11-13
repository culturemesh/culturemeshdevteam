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
		$do2db = new $do2db();
	}

	public function testConstruct() {

		$convo = new dobj\Conversation();
		$this->assertInstanceOf('dobj\Conversation', $convo);
	}

	public function testDisplayNetwork() {

		$convo = dobj\Conversation::loadById($id);
		$convo->fillConversation();
		$convo->display('network');
	}

	public function testDisplayProfile() {

		$convo = dobj\Conversation::loadById($id);
		$convo->fillConversation();
		$convo->display('profile');
	}
}
