<?php

class Do2DbTest extends PHPUnit_Framework_TestCase {

	protected $dal;

	public function setUp() {

		$this->dal = new dal\DAL(new dal\StubConnection());
		$this->dal->loadFiles();
	}

	public function testConstruct() {

		$do2db = new dal\Do2Db();

		$this->assertInstanceOf('dal\Do2Db', $do2db);
	}

	public function testConstructFail() {

		$do2db = new dal\Do2Db();

		$this->assertInstanceOf('dal\Do2Db', $do2db);
	}

	public function testExecute() {

		$do2db = new dal\Do2Db();

		$object = new dobj\Blank();
		$object->id = 1;
//		var_dump($object);

		$result = $do2db->execute($this->dal, $object, 'test');

		$this->assertEquals(1, $result->id);
		$this->assertTrue($do2db->isEmpty());
	}

	public function stubExecute() {

	}
}

?>
