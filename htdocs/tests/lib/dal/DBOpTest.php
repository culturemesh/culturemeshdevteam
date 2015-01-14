<?php

class DBOpTest extends PHPUnit_Framework_TestCase {

	protected $dal;

	public function setUp() {

		$this->dal = new dal\DAL(new dal\StubConnection());
		$this->dal->loadFiles();
	}

	public function testConstructOp() {
		
		$op = $this->dal->test;

		$this->assertInstanceOf('\dal\DBQuery', $op);
		
	}

	public function testGetScheme() {

		$op = $this->dal->getCQuery('test');

		$scheme = $op->getScheme();

		$this->assertTrue(isset($scheme['params']));
		$this->assertTrue(isset($scheme['returning']));
		$this->assertTrue(isset($scheme['returning_list']));
		$this->assertTrue(isset($scheme['returning_class']));
		$this->assertEquals(array('id'), $scheme['params']);
		$this->assertEquals(True, $scheme['returning']);
		$this->assertEquals(False, $scheme['returning_list']);
		$this->assertEquals('dobj\Blank', $scheme['returning_class']);
	}

	public function testGetConnection() {

		/*
		$op = $this->dal->getCQuery('test');
		$connection = $op->connection;
		 */
	}
}

?>
